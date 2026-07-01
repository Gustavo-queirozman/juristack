<?php

namespace App\Services;

use App\Models\BillingSetting;
use RuntimeException;

class StripeSettingsService
{
    public function current(): BillingSetting
    {
        $settings = BillingSetting::query()->first();

        if ($settings) {
            return $settings;
        }

        return BillingSetting::query()->create([
            'default_currency' => strtolower((string) config('services.stripe.currency', 'brl')),
            'stripe_publishable_key' => config('services.stripe.publishable_key'),
            'stripe_secret_key' => config('services.stripe.secret_key'),
            'stripe_webhook_secret' => config('services.stripe.webhook_secret'),
            'is_stripe_enabled' => filled(config('services.stripe.secret_key')),
        ]);
    }

    public function isEnabled(): bool
    {
        return $this->current()->isConfigured();
    }

    public function requireEnabled(): BillingSetting
    {
        $settings = $this->current();

        if (! $settings->isConfigured()) {
            throw new RuntimeException('As credenciais do Stripe ainda nao foram configuradas no painel administrativo.');
        }

        return $settings;
    }
}
