<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StripeSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BillingSettingController extends Controller
{
    public function __construct(
        private readonly StripeSettingsService $stripeSettings,
    ) {
    }

    public function edit(): View
    {
        return view('admin.billing.settings', [
            'settings' => $this->stripeSettings->current(),
            'webhookUrl' => route('api.stripe.webhook'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = $this->stripeSettings->current();

        $validated = $request->validate([
            'stripe_publishable_key' => ['nullable', 'string', 'max:255'],
            'stripe_secret_key' => ['nullable', 'string', 'max:500'],
            'stripe_webhook_secret' => ['nullable', 'string', 'max:500'],
            'default_currency' => ['required', 'string', 'size:3'],
            'is_stripe_enabled' => ['nullable', 'boolean'],
        ]);

        $publishableKey = trim((string) ($validated['stripe_publishable_key'] ?? ''));
        $secretKey = $request->filled('stripe_secret_key')
            ? trim((string) $validated['stripe_secret_key'])
            : $settings->stripe_secret_key;
        $webhookSecret = $request->filled('stripe_webhook_secret')
            ? trim((string) $validated['stripe_webhook_secret'])
            : $settings->stripe_webhook_secret;
        $isEnabled = $request->boolean('is_stripe_enabled');

        $errors = [];

        if ($isEnabled && $publishableKey === '') {
            $errors['stripe_publishable_key'] = 'Informe a chave publica para ativar o Stripe.';
        }

        if ($isEnabled && blank($secretKey)) {
            $errors['stripe_secret_key'] = 'Informe a chave secreta para ativar o Stripe.';
        }

        if ($errors !== []) {
            return back()
                ->withErrors($errors)
                ->withInput();
        }

        $payload = [
            'stripe_publishable_key' => $publishableKey !== '' ? $publishableKey : null,
            'default_currency' => strtolower((string) $validated['default_currency']),
            'is_stripe_enabled' => $isEnabled,
        ];

        if ($request->filled('stripe_secret_key')) {
            $payload['stripe_secret_key'] = $secretKey;
        }

        if ($request->filled('stripe_webhook_secret')) {
            $payload['stripe_webhook_secret'] = $webhookSecret !== '' ? $webhookSecret : null;
        }

        $settings->fill($payload)->save();

        return back()->with('success', 'Credenciais do Stripe atualizadas com sucesso.');
    }
}
