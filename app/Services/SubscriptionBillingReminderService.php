<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Notifications\SubscriptionPaymentOverdueNotification;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Notification;

class SubscriptionBillingReminderService
{
    private const REMINDER_STAGES = [0, 3, 7, 14];

    private const OVERDUE_STATUSES = [
        'past_due',
        'unpaid',
        'incomplete',
        'incomplete_expired',
    ];

    public function __construct(
        private readonly StripeSettingsService $stripeSettings,
    ) {
    }

    public function overdueStatuses(): array
    {
        return self::OVERDUE_STATUSES;
    }

    public function markAsOverdue(Enterprise $enterprise, ?CarbonInterface $since = null): Enterprise
    {
        $resolvedSince = $enterprise->payment_overdue_since;

        if (! $resolvedSince) {
            $resolvedSince = $since ?: now();
        } elseif ($since && $since->lt($resolvedSince)) {
            $resolvedSince = $since;
        }

        $enterprise->forceFill([
            'payment_overdue_since' => $resolvedSince,
        ])->save();

        return $enterprise->refresh();
    }

    public function clearOverdueState(Enterprise $enterprise): Enterprise
    {
        $enterprise->forceFill([
            'payment_overdue_since' => null,
            'last_payment_overdue_reminder_at' => null,
            'last_payment_overdue_reminder_stage' => null,
        ])->save();

        return $enterprise->refresh();
    }

    public function resolveStage(Enterprise $enterprise, ?CarbonInterface $reference = null): ?int
    {
        if (! $this->isOverdue($enterprise)) {
            return null;
        }

        $daysOverdue = $this->daysOverdue($enterprise, $reference);

        if ($daysOverdue === null) {
            return null;
        }

        $stage = null;

        foreach (self::REMINDER_STAGES as $threshold) {
            if ($daysOverdue >= $threshold) {
                $stage = $threshold;
            }
        }

        return $stage;
    }

    public function shouldSendReminder(Enterprise $enterprise, ?int $stage = null, ?CarbonInterface $reference = null): bool
    {
        $stage ??= $this->resolveStage($enterprise, $reference);

        if ($stage === null) {
            return false;
        }

        return $enterprise->last_payment_overdue_reminder_stage === null
            || $stage > $enterprise->last_payment_overdue_reminder_stage;
    }

    public function daysOverdue(Enterprise $enterprise, ?CarbonInterface $reference = null): ?int
    {
        $overdueSince = $enterprise->payment_overdue_since;

        if (! $overdueSince && $enterprise->subscription_ends_at?->isPast()) {
            $overdueSince = $enterprise->subscription_ends_at;
        }

        if (! $overdueSince) {
            return null;
        }

        $reference ??= now();

        return max(0, $overdueSince->copy()->startOfDay()->diffInDays($reference->copy()->startOfDay(), false));
    }

    public function sendReminder(Enterprise $enterprise, ?CarbonInterface $reference = null): bool
    {
        $stage = $this->resolveStage($enterprise, $reference);

        if (! $this->shouldSendReminder($enterprise, $stage, $reference)) {
            return false;
        }

        $recipients = $enterprise->billingRecipientUsers();
        $daysOverdue = $this->daysOverdue($enterprise, $reference) ?? 0;
        $hasEnterpriseEmail = filled($enterprise->email);

        if ($recipients->isEmpty() && ! $hasEnterpriseEmail) {
            return false;
        }

        $actionUrl = $enterprise->stripe_customer_id && $this->stripeSettings->isEnabled()
            ? route('billing.portal.start', absolute: true)
            : route('dashboard', absolute: true);
        $notification = new SubscriptionPaymentOverdueNotification(
            $enterprise->fresh(['subscriptionPlan']),
            $daysOverdue,
            $stage,
            $actionUrl,
        );

        foreach ($recipients as $recipient) {
            $recipient->notify($notification);
        }

        if ($hasEnterpriseEmail) {
            $knownEmails = $recipients
                ->pluck('email')
                ->filter()
                ->map(static fn (string $email) => mb_strtolower($email))
                ->all();

            if (! in_array(mb_strtolower($enterprise->email), $knownEmails, true)) {
                Notification::route('mail', $enterprise->email)->notify($notification);
            }
        }

        $enterprise->forceFill([
            'last_payment_overdue_reminder_at' => $reference ?: now(),
            'last_payment_overdue_reminder_stage' => $stage,
        ])->save();

        return true;
    }

    public function isOverdue(Enterprise $enterprise): bool
    {
        if (in_array((string) $enterprise->subscription_status, self::OVERDUE_STATUSES, true)) {
            return true;
        }

        if (! $enterprise->subscription_plan_id) {
            return false;
        }

        return $enterprise->subscription_ends_at !== null
            && $enterprise->subscription_ends_at->isPast()
            && ! in_array((string) $enterprise->subscription_status, ['active', 'trialing', 'canceled'], true);
    }
}
