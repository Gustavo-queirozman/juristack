<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\DatajudProcesso;
use App\Models\Document;
use App\Models\ProcessoMonitor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isClient = $user->isClient();
        $inviteEnterprise = ! $isClient && $user->enterprise_id
            ? $user->enterprise()->first(['id', 'name', 'slug'])
            : null;

        $clientFiles = collect();
        $clientDocuments = collect();
        $clientProcesses = collect();
        $clientProcessOptions = collect();
        $fileChecklist = collect();

        if ($isClient) {
            $customer = $user->customerProfile?->load(['files.processo', 'files.uploader']);
            $totalClientes = $customer ? 1 : 0;
            $totalProcessos = $customer ? DatajudProcesso::where('customer_id', $customer->id)->count() : 0;
            $totalArquivos = $customer ? $customer->files->count() : 0;
            $totalDocumentos = $customer ? Document::where('customer_id', $customer->id)->count() : 0;
            $processosRecentes = collect();
            $ultimosClientes = $customer ? collect([$customer]) : collect();

            if ($customer) {
                $clientFiles = $customer->files->sortByDesc('created_at')->values();
                $clientDocuments = Document::query()
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->limit(6)
                    ->get();
                $clientProcessOptions = DatajudProcesso::query()
                    ->where('customer_id', $customer->id)
                    ->latest('updated_at')
                    ->get()
                    ->unique(fn (DatajudProcesso $processo) => implode('|', [
                        $processo->tribunal,
                        $processo->numero_processo,
                        $processo->grau,
                    ]))
                    ->values();
                $clientProcesses = DatajudProcesso::query()
                    ->with('latestMovement')
                    ->where('customer_id', $customer->id)
                    ->latest('updated_at')
                    ->limit(8)
                    ->get();
                $fileChecklist = collect(CustomerFile::DOCUMENT_TYPES)
                    ->map(function (string $label, string $key) use ($clientFiles) {
                        $uploads = $clientFiles->where('document_type', $key)->values();

                        return [
                            'key' => $key,
                            'label' => $label,
                            'count' => $uploads->count(),
                            'status' => $uploads->isNotEmpty() ? 'Enviado' : 'Pendente',
                        ];
                    })
                    ->values();
            }
        } elseif ($user->isAdmin()) {
            $totalClientes = Customer::count();
            $totalProcessos = ProcessoMonitor::count();
            $totalArquivos = CustomerFile::count();
            $totalDocumentos = Document::count();
            $processosRecentes = ProcessoMonitor::orderByDesc('updated_at')->limit(5)->get();
            $ultimosClientes = Customer::latest()->limit(4)->get(['id', 'name', 'email', 'created_at']);
        } else {
            $totalClientes = Customer::where('enterprise_id', $user->enterprise_id)->count();
            $totalProcessos = ProcessoMonitor::whereHas('usuario', function ($query) use ($user) {
                $query->where('enterprise_id', $user->enterprise_id);
            })->count();
            $totalArquivos = CustomerFile::whereHas('customer', function ($query) use ($user) {
                $query->where('enterprise_id', $user->enterprise_id);
            })->count();
            $totalDocumentos = Document::where('enterprise_id', $user->enterprise_id)->count();
            $processosRecentes = ProcessoMonitor::whereHas('usuario', function ($query) use ($user) {
                $query->where('enterprise_id', $user->enterprise_id);
            })->orderByDesc('updated_at')->limit(5)->get();
            $ultimosClientes = Customer::where('enterprise_id', $user->enterprise_id)
                ->latest()
                ->limit(4)
                ->get(['id', 'name', 'email', 'created_at']);
        }

        return view('dashboard', compact(
            'clientDocuments',
            'clientFiles',
            'clientProcessOptions',
            'clientProcesses',
            'fileChecklist',
            'inviteEnterprise',
            'isClient',
            'processosRecentes',
            'totalArquivos',
            'totalClientes',
            'totalDocumentos',
            'totalProcessos',
            'ultimosClientes'
        ) + ['userName' => $user->name ?? null]);
    }
}
