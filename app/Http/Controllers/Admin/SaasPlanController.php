<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Services\StripeBillingService;
use App\Services\StripeSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaasPlanController extends Controller
{
    public function __construct(
        private readonly StripeBillingService $stripeBilling,
        private readonly StripeSettingsService $stripeSettings,
    ) {
    }

    public function index(): View
    {
        return view('admin.billing.plans.index', [
            'plans' => SaasPlan::query()
                ->orderBy('sort_order')
                ->orderBy('price_cents')
                ->paginate(12),
            'stripeEnabled' => $this->stripeSettings->isEnabled(),
        ]);
    }

    public function create(): View
    {
        return view('admin.billing.plans.create', [
            'plan' => new SaasPlan([
                'currency' => $this->stripeSettings->current()->default_currency,
                'billing_interval' => 'month',
                'interval_count' => 1,
                'button_label' => 'Assinar plano',
                'is_active' => true,
                'is_public' => true,
                'is_featured' => false,
                'contact_only' => false,
            ]),
            'featuresText' => '',
            'stripeEnabled' => $this->stripeSettings->isEnabled(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $plan = new SaasPlan();

        return $this->persist($request, $plan, 'Plano cadastrado com sucesso.');
    }

    public function edit(SaasPlan $plan): View
    {
        return view('admin.billing.plans.edit', [
            'plan' => $plan,
            'featuresText' => implode(PHP_EOL, $plan->features ?? []),
            'stripeEnabled' => $this->stripeSettings->isEnabled(),
        ]);
    }

    public function update(Request $request, SaasPlan $plan): RedirectResponse
    {
        return $this->persist($request, $plan, 'Plano atualizado com sucesso.');
    }

    private function persist(Request $request, SaasPlan $plan, string $successMessage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('saas_plans', 'slug')->ignore($plan->id),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'string', 'max:30'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_interval' => ['required', Rule::in(['month', 'year'])],
            'interval_count' => ['required', 'integer', 'min:1', 'max:12'],
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'button_label' => ['nullable', 'string', 'max:100'],
            'features_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_public' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'contact_only' => ['nullable', 'boolean'],
        ]);

        $contactOnly = $request->boolean('contact_only');
        $priceCents = null;

        if (! $contactOnly) {
            $priceCents = $this->parsePriceToCents((string) $request->input('price'));
        }

        $plan->fill([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'price_cents' => $priceCents,
            'currency' => strtolower((string) $validated['currency']),
            'billing_interval' => $validated['billing_interval'],
            'interval_count' => (int) $validated['interval_count'],
            'trial_days' => filled($validated['trial_days'] ?? null) ? (int) $validated['trial_days'] : null,
            'button_label' => $validated['button_label'] ?? null,
            'features' => collect(preg_split('/\r\n|\r|\n/', (string) ($validated['features_text'] ?? '')))
                ->map(fn (string $item) => trim($item))
                ->filter()
                ->values()
                ->all(),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active'),
            'is_public' => $request->boolean('is_public'),
            'is_featured' => $request->boolean('is_featured'),
            'contact_only' => $contactOnly,
        ]);

        $plan->save();

        $warning = null;

        if (! $contactOnly) {
            if ($this->stripeSettings->isEnabled()) {
                try {
                    $this->stripeBilling->syncPlan($plan);
                } catch (\Throwable $exception) {
                    report($exception);
                    $warning = 'Plano salvo localmente, mas a sincronizacao com o Stripe falhou: '.$exception->getMessage();
                }
            } else {
                $warning = 'Plano salvo localmente. Configure e ative o Stripe para sincronizar o preco automaticamente.';
            }
        }

        $response = redirect()
            ->route('admin.billing.plans.index')
            ->with('success', $successMessage);

        if ($warning) {
            $response->with('warning', $warning);
        }

        return $response;
    }

    private function parsePriceToCents(string $value): int
    {
        $normalized = preg_replace('/[^\d,\.]/', '', $value);

        if ($normalized === '') {
            throw ValidationException::withMessages([
                'price' => 'Informe o valor do plano.',
            ]);
        }

        $lastComma = strrpos($normalized, ',');
        $lastDot = strrpos($normalized, '.');

        if ($lastComma !== false && $lastDot !== false) {
            $normalized = $lastComma > $lastDot
                ? str_replace('.', '', $normalized)
                : str_replace(',', '', $normalized);
        }

        if (str_contains($normalized, ',')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        }

        $amount = (float) $normalized;

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'price' => 'Informe um valor maior que zero.',
            ]);
        }

        return (int) round($amount * 100);
    }
}
