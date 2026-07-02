<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\StripeBillingService;
use App\Services\StripeSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Invoice;
use Stripe\StripeObject;
use Stripe\Subscription;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private readonly StripeBillingService $stripeBilling,
        private readonly StripeSettingsService $stripeSettings,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $settings = $this->stripeSettings->current();

        abort_unless(filled($settings->stripe_webhook_secret), 503, 'Webhook Stripe nao configurado.');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) $settings->stripe_webhook_secret,
            );
        } catch (UnexpectedValueException|SignatureVerificationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 400);
        }

        $this->handleEvent($event);

        return response()->json(['received' => true]);
    }

    private function handleEvent(Event $event): void
    {
        $object = $event->data->object;

        match ($event->type) {
            'checkout.session.completed' => $this->stripeBilling->handleCheckoutCompleted($this->toCheckoutSession($object)),
            'customer.subscription.created',
            'customer.subscription.updated',
            'customer.subscription.deleted' => $this->stripeBilling->handleSubscriptionUpdated($this->toSubscription($object)),
            'invoice.payment_failed' => $this->stripeBilling->handleInvoicePaymentFailed($this->toInvoice($object)),
            'invoice.paid' => $this->stripeBilling->handleInvoicePaid($this->toInvoice($object)),
            default => null,
        };
    }

    private function toCheckoutSession(mixed $object): Session
    {
        if ($object instanceof Session) {
            return $object;
        }

        return Session::constructFrom($this->toArray($object));
    }

    private function toSubscription(mixed $object): Subscription
    {
        if ($object instanceof Subscription) {
            return $object;
        }

        return Subscription::constructFrom($this->toArray($object));
    }

    private function toInvoice(mixed $object): Invoice
    {
        if ($object instanceof Invoice) {
            return $object;
        }

        return Invoice::constructFrom($this->toArray($object));
    }

    private function toArray(mixed $object): array
    {
        if ($object instanceof StripeObject) {
            return $object->toArray();
        }

        return (array) $object;
    }
}
