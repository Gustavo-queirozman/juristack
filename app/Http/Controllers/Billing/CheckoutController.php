<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\SaasPlan;
use App\Services\StripeBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly StripeBillingService $stripeBilling,
    ) {
    }

    public function create(Request $request, SaasPlan $plan): RedirectResponse
    {
        $user = $request->user();
        $enterprise = $user?->enterprise;

        abort_if(! $user || ! $enterprise, 403);

        $session = $this->stripeBilling->createCheckoutSession(
            $enterprise,
            $user,
            $plan,
            route('billing.subscription.success', absolute: true).'?session_id={CHECKOUT_SESSION_ID}',
            route('billing.subscription.cancel', absolute: true),
        );

        return redirect()->away($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (is_string($sessionId) && $sessionId !== '' && $this->stripeBilling->isEnabled()) {
            $session = $this->stripeBilling->retrieveCheckoutSession($sessionId);
            $this->stripeBilling->handleCheckoutCompleted($session);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Assinatura concluida. O status da sua assinatura foi atualizado.');
    }

    public function cancel(): RedirectResponse
    {
        return redirect()
            ->route('dashboard')
            ->with('warning', 'Checkout cancelado. Voce pode tentar novamente a qualquer momento.');
    }

    public function portal(Request $request): RedirectResponse
    {
        $user = $request->user();
        $enterprise = $user?->enterprise;

        abort_if(! $user || ! $enterprise, 403);

        if (! $this->stripeBilling->isEnabled() || ! $enterprise->stripe_customer_id) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'A gestao da assinatura ainda nao esta disponivel para este escritorio.');
        }

        try {
            $session = $this->stripeBilling->createBillingPortalSession(
                $enterprise,
                route('dashboard', absolute: true),
            );
        } catch (InvalidArgumentException) {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'Nao foi possivel abrir o portal de cobranca desta assinatura.');
        }

        return redirect()->away($session->url);
    }
}
