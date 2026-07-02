<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Models\SaasPlan;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;
use Stripe\Subscription;

class StripeBillingService
{
    public function __construct(
        private readonly StripeSettingsService $settingsService,
        private readonly SubscriptionBillingReminderService $reminderService,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->settingsService->isEnabled();
    }

    public function syncPlan(SaasPlan $plan): SaasPlan
    {
        if ($plan->contact_only) {
            return $plan;
        }

        if ($plan->price_cents === null || $plan->price_cents <= 0) {
            throw new InvalidArgumentException('Informe um valor valido para sincronizar o plano com o Stripe.');
        }

        $client = $this->client();
        $product = $this->upsertProduct($client, $plan);
        $signature = $plan->pricingSignature();

        $updates = [
            'stripe_product_id' => $product->id,
        ];

        if (! $plan->stripe_price_id || $plan->stripe_price_signature !== $signature) {
            if ($plan->stripe_price_id) {
                $this->archivePrice($client, $plan->stripe_price_id);
            }

            $price = $client->prices->create([
                'unit_amount' => $plan->price_cents,
                'currency' => strtolower($plan->currency),
                'product' => $product->id,
                'nickname' => $plan->name,
                'recurring' => [
                    'interval' => $plan->billing_interval,
                    'interval_count' => $plan->interval_count,
                ],
                'metadata' => [
                    'plan_id' => (string) $plan->id,
                    'plan_slug' => $plan->slug,
                ],
            ]);

            $updates['stripe_price_id'] = $price->id;
            $updates['stripe_price_signature'] = $signature;
        }

        $plan->fill($updates);
        $plan->save();

        return $plan->refresh();
    }

    public function createCheckoutSession(
        Enterprise $enterprise,
        User $user,
        SaasPlan $plan,
        string $successUrl,
        string $cancelUrl,
    ): Session {
        if (! $plan->is_active) {
            throw new InvalidArgumentException('O plano selecionado nao esta disponivel para assinatura.');
        }

        if (! $plan->isCheckoutEnabled()) {
            throw new InvalidArgumentException('O plano selecionado ainda nao possui um preco sincronizado no Stripe.');
        }

        $client = $this->client();
        $customerId = $enterprise->stripe_customer_id ?: $this->createCustomer($client, $enterprise, $user);

        if ($enterprise->stripe_customer_id !== $customerId) {
            $enterprise->forceFill([
                'stripe_customer_id' => $customerId,
            ])->save();
        }

        $subscriptionData = [
            'metadata' => [
                'enterprise_id' => (string) $enterprise->id,
                'plan_id' => (string) $plan->id,
                'user_id' => (string) $user->id,
            ],
        ];

        if ($plan->trial_days) {
            $subscriptionData['trial_period_days'] = $plan->trial_days;
        }

        return $client->checkout->sessions->create([
            'mode' => 'subscription',
            'customer' => $customerId,
            'client_reference_id' => (string) $enterprise->id,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'allow_promotion_codes' => true,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
            'metadata' => [
                'enterprise_id' => (string) $enterprise->id,
                'plan_id' => (string) $plan->id,
                'user_id' => (string) $user->id,
            ],
            'subscription_data' => $subscriptionData,
        ]);
    }

    public function retrieveCheckoutSession(string $sessionId): Session
    {
        return $this->client()->checkout->sessions->retrieve($sessionId, []);
    }

    public function createBillingPortalSession(Enterprise $enterprise, string $returnUrl): BillingPortalSession
    {
        if (! $enterprise->stripe_customer_id) {
            throw new InvalidArgumentException('A empresa ainda nao possui cliente Stripe vinculado.');
        }

        return $this->client()->billingPortal->sessions->create([
            'customer' => $enterprise->stripe_customer_id,
            'return_url' => $returnUrl,
        ]);
    }

    public function syncEnterpriseFromSubscriptionId(string $subscriptionId): ?Enterprise
    {
        $subscription = $this->client()->subscriptions->retrieve($subscriptionId, []);

        return $this->syncEnterpriseFromSubscription($subscription);
    }

    public function handleCheckoutCompleted(Session $session): ?Enterprise
    {
        $enterpriseId = (int) ($session->metadata->enterprise_id ?? 0);

        if (! $session->subscription) {
            return $enterpriseId ? Enterprise::query()->find($enterpriseId) : null;
        }

        return $this->syncEnterpriseFromSubscriptionId((string) $session->subscription);
    }

    public function handleSubscriptionUpdated(Subscription $subscription): ?Enterprise
    {
        return $this->syncEnterpriseFromSubscription($subscription);
    }

    public function handleInvoicePaymentFailed(Invoice $invoice): ?Enterprise
    {
        $enterprise = $this->findEnterpriseForStripeReferences(
            $this->stripeString($invoice->customer),
            $this->stripeString($invoice->subscription),
            isset($invoice->metadata->enterprise_id) ? (int) $invoice->metadata->enterprise_id : null,
        );

        if (! $enterprise) {
            Log::warning('Falha de pagamento Stripe recebida sem empresa correspondente.', [
                'stripe_invoice_id' => $invoice->id,
                'stripe_subscription_id' => $invoice->subscription,
                'stripe_customer_id' => $invoice->customer,
            ]);

            return null;
        }

        $overdueSince = $this->timestampToCarbon($invoice->due_date)
            ?: $this->timestampToCarbon($invoice->created)
            ?: now()->toImmutable();

        return $this->reminderService->markAsOverdue($enterprise, $overdueSince);
    }

    public function handleInvoicePaid(Invoice $invoice): ?Enterprise
    {
        $enterprise = $this->findEnterpriseForStripeReferences(
            $this->stripeString($invoice->customer),
            $this->stripeString($invoice->subscription),
            isset($invoice->metadata->enterprise_id) ? (int) $invoice->metadata->enterprise_id : null,
        );

        if (! $enterprise) {
            return null;
        }

        return $this->reminderService->clearOverdueState($enterprise);
    }

    public function client(): StripeClient
    {
        $settings = $this->settingsService->requireEnabled();

        return new StripeClient($settings->stripe_secret_key);
    }

    private function upsertProduct(StripeClient $client, SaasPlan $plan): Product
    {
        $payload = [
            'name' => $plan->name,
            'description' => $plan->description,
            'active' => $plan->is_active,
            'metadata' => [
                'plan_id' => (string) $plan->id,
                'plan_slug' => $plan->slug,
            ],
        ];

        if ($plan->stripe_product_id) {
            try {
                return $client->products->update($plan->stripe_product_id, $payload);
            } catch (ApiErrorException $exception) {
                Log::warning('Falha ao atualizar produto Stripe existente. Um novo produto sera criado.', [
                    'plan_id' => $plan->id,
                    'stripe_product_id' => $plan->stripe_product_id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $client->products->create($payload);
    }

    private function createCustomer(StripeClient $client, Enterprise $enterprise, User $user): string
    {
        $customer = $client->customers->create([
            'name' => $enterprise->name,
            'email' => $enterprise->email ?: $user->email,
            'metadata' => [
                'enterprise_id' => (string) $enterprise->id,
                'user_id' => (string) $user->id,
            ],
        ]);

        return $customer->id;
    }

    private function archivePrice(StripeClient $client, string $priceId): void
    {
        try {
            $client->prices->update($priceId, ['active' => false]);
        } catch (ApiErrorException $exception) {
            Log::warning('Nao foi possivel inativar o preco antigo no Stripe.', [
                'stripe_price_id' => $priceId,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function syncEnterpriseFromSubscription(Subscription $subscription): ?Enterprise
    {
        $enterpriseId = isset($subscription->metadata->enterprise_id)
            ? (int) $subscription->metadata->enterprise_id
            : null;

        $enterprise = $this->findEnterpriseForStripeReferences(
            $this->stripeString($subscription->customer),
            $subscription->id,
            $enterpriseId,
        );

        if (! $enterprise) {
            Log::warning('Webhook Stripe recebido sem empresa correspondente.', [
                'stripe_subscription_id' => $subscription->id,
                'stripe_customer_id' => $subscription->customer,
            ]);

            return null;
        }

        $priceId = $this->extractPriceIdFromSubscription($subscription);
        $plan = $priceId
            ? SaasPlan::query()->where('stripe_price_id', $priceId)->first()
            : null;
        $status = (string) $subscription->status;
        $subscriptionEndsAt = $this->timestampToCarbon($subscription->current_period_end);

        $updates = [
            'subscription_plan_id' => $plan?->id,
            'stripe_customer_id' => (string) $subscription->customer,
            'stripe_subscription_id' => $subscription->id,
            'stripe_price_id' => $priceId,
            'subscription_status' => $status,
            'subscription_started_at' => $this->timestampToCarbon($subscription->start_date),
            'subscription_ends_at' => $subscriptionEndsAt,
            'trial_ends_at' => $this->timestampToCarbon($subscription->trial_end),
            'subscription_canceled_at' => $this->timestampToCarbon($subscription->cancel_at),
        ];

        if (in_array($status, $this->reminderService->overdueStatuses(), true)) {
            $updates['payment_overdue_since'] = $enterprise->payment_overdue_since
                ?: $subscriptionEndsAt
                ?: now()->toImmutable();
        } else {
            $updates['payment_overdue_since'] = null;
            $updates['last_payment_overdue_reminder_at'] = null;
            $updates['last_payment_overdue_reminder_stage'] = null;
        }

        $enterprise->forceFill($updates)->save();

        return $enterprise->refresh();
    }

    private function findEnterpriseForStripeReferences(
        ?string $customerId,
        ?string $subscriptionId = null,
        ?int $enterpriseId = null,
    ): ?Enterprise {
        if (! $enterpriseId && ! $customerId && ! $subscriptionId) {
            return null;
        }

        return Enterprise::query()
            ->where(function ($query) use ($customerId, $subscriptionId, $enterpriseId): void {
                if ($enterpriseId) {
                    $query->orWhereKey($enterpriseId);
                }

                if ($customerId) {
                    $query->orWhere('stripe_customer_id', $customerId);
                }

                if ($subscriptionId) {
                    $query->orWhere('stripe_subscription_id', $subscriptionId);
                }
            })
            ->first();
    }

    private function extractPriceIdFromSubscription(Subscription $subscription): ?string
    {
        $item = $subscription->items?->data[0] ?? null;

        if (! $item) {
            return null;
        }

        $price = $item->price ?? null;

        return $price instanceof Price ? $price->id : ($price->id ?? null);
    }

    private function timestampToCarbon(null|int|string $timestamp): ?CarbonImmutable
    {
        if (! $timestamp) {
            return null;
        }

        return CarbonImmutable::createFromTimestamp((int) $timestamp);
    }

    private function stripeString(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        return $value->id ?? null;
    }
}
