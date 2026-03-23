<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\ProcessoMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalClientes = Customer::count();
        $totalProcessos = ProcessoMonitor::where('user_id', $user->id)->count();
        $totalArquivos = CustomerFile::count();

        $processosRecentes = ProcessoMonitor::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $ultimosClientes = Customer::latest()
            ->limit(4)
            ->get(['id', 'name', 'email', 'created_at']);

        return view('dashboard', compact(
            'totalClientes',
            'totalProcessos',
            'totalArquivos',
            'processosRecentes',
            'ultimosClientes'
        ) + ['userName' => $user->name ?? null]);
    }
}
