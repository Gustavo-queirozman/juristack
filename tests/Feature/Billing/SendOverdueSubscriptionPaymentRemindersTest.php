<?php

namespace Tests\Feature\Billing;

use App\Models\Enterprise;
use App\Models\SaasPlan;
use App\Models\User;
use App\Notifications\SubscriptionPaymentOverdueNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendOverdueSubscriptionPaymentRemindersTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_email_for_overdue_subscription_at_expected_stage(): void
    {
        Notification::fake();

        $plan = SaasPlan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'price_cents' => 59700,
            'currency' => 'brl',
            'billing_interval' => 'month',
            'interval_count' => 1,
            'is_active' => true,
            'is_public' => true,
        ]);

        $enterprise = Enterprise::create([
            'name' => 'Atlas Juridico',
            'email' => 'financeiro@atlas.test',
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'past_due',
            'subscription_ends_at' => now()->subDays(7),
            'payment_overdue_since' => now()->subDays(7),
        ]);

        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'email' => 'admin@atlas.test',
        ]);

        $this->artisan('billing:send-overdue-payment-reminders')
            ->assertSuccessful();

        Notification::assertSentTo($admin, SubscriptionPaymentOverdueNotification::class);

        $enterprise->refresh();

        $this->assertSame(7, $enterprise->last_payment_overdue_reminder_stage);
        $this->assertNotNull($enterprise->last_payment_overdue_reminder_at);
    }

    public function test_command_does_not_resend_same_stage_twice(): void
    {
        Notification::fake();

        $plan = SaasPlan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'price_cents' => 29700,
            'currency' => 'brl',
            'billing_interval' => 'month',
            'interval_count' => 1,
            'is_active' => true,
            'is_public' => true,
        ]);

        $enterprise = Enterprise::create([
            'name' => 'Silva & Rocha',
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'past_due',
            'subscription_ends_at' => now()->subDays(3),
            'payment_overdue_since' => now()->subDays(3),
        ]);

        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $this->artisan('billing:send-overdue-payment-reminders')
            ->assertSuccessful();
        $this->artisan('billing:send-overdue-payment-reminders')
            ->assertSuccessful();

        Notification::assertSentToTimes($admin, SubscriptionPaymentOverdueNotification::class, 1);
    }

    public function test_command_escalates_to_next_stage_after_more_days(): void
    {
        Notification::fake();

        $plan = SaasPlan::create([
            'name' => 'Scale',
            'slug' => 'scale',
            'price_cents' => 99700,
            'currency' => 'brl',
            'billing_interval' => 'month',
            'interval_count' => 1,
            'is_active' => true,
            'is_public' => true,
        ]);

        $enterprise = Enterprise::create([
            'name' => 'Costa Advogados',
            'subscription_plan_id' => $plan->id,
            'subscription_status' => 'past_due',
            'subscription_ends_at' => now()->subDays(14),
            'payment_overdue_since' => now()->subDays(14),
            'last_payment_overdue_reminder_stage' => 7,
            'last_payment_overdue_reminder_at' => now()->subDays(7),
        ]);

        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $this->artisan('billing:send-overdue-payment-reminders')
            ->assertSuccessful();

        Notification::assertSentToTimes($admin, SubscriptionPaymentOverdueNotification::class, 1);

        $enterprise->refresh();

        $this->assertSame(14, $enterprise->last_payment_overdue_reminder_stage);
    }
}
