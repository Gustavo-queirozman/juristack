<?php

namespace App\Http\Controllers;

use App\Models\SaasPlan;
use App\Services\StripeSettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly StripeSettingsService $stripeSettings,
    ) {
    }

    public function __invoke(): View
    {
        return view('welcome', [
            'publicPlans' => SaasPlan::query()->publicActive()->get(),
            'stripeEnabled' => $this->stripeSettings->isEnabled(),
        ]);
    }
}
