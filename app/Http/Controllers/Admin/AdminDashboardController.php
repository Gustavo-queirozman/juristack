<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Enterprise;
use App\Models\FinancialEntry;
use App\Models\ProcessoMonitor;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'enterprises' => Enterprise::count(),
            'global_admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'enterprise_admins' => User::query()->where('role', User::ROLE_ENTERPRISE_ADMIN)->count(),
            'lawyers' => User::query()->where('role', User::ROLE_LAWYER)->count(),
            'clients' => User::query()->where('role', User::ROLE_CLIENT)->count(),
            'customers' => Customer::count(),
            'documents' => Document::count(),
            'processes' => ProcessoMonitor::count(),
            'financial_entries' => FinancialEntry::count(),
        ];

        $latestEnterprises = Enterprise::query()
            ->withCount([
                'users as enterprise_admins_count' => fn ($query) => $query->where('role', User::ROLE_ENTERPRISE_ADMIN),
                'users as internal_users_count' => fn ($query) => $query->whereIn('role', User::INTERNAL_ROLES),
                'customers',
            ])
            ->latest()
            ->limit(6)
            ->get();

        $recentOfficeAdmins = User::query()
            ->with('enterprise:id,name,slug')
            ->where('role', User::ROLE_ENTERPRISE_ADMIN)
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'latestEnterprises' => $latestEnterprises,
            'recentOfficeAdmins' => $recentOfficeAdmins,
            'stats' => $stats,
        ]);
    }
}
