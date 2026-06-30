<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Services\EvolutionWhatsAppService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class WhatsAppConnectionController extends Controller
{
    public function __construct(private readonly EvolutionWhatsAppService $whatsAppService) {}

    public function show(Request $request)
    {
        $actor = $request->user();
        $enterprise = $this->resolveEnterprise($request);
        $enterprises = $actor->isAdmin()
            ? Enterprise::query()->orderBy('name')->get(['id', 'name'])
            : collect();
        $connection = null;
        $statusError = null;

        if ($enterprise?->evolution_instance && $this->whatsAppService->hasBaseConfiguration()) {
            try {
                $connection = $this->whatsAppService->connectionState($enterprise->evolution_instance);
                $this->syncEnterpriseConnection($enterprise, $connection);
                $enterprise->refresh();
            } catch (Throwable $exception) {
                report($exception);
                $statusError = 'Nao foi possivel consultar o status na Evolution API.';
            }
        }

        return view('whatsapp.connection', [
            'enterprise' => $enterprise,
            'enterprises' => $enterprises,
            'selectedEnterpriseId' => $enterprise?->id,
            'isEvolutionConfigured' => $this->whatsAppService->hasBaseConfiguration(),
            'isWebhookConfigured' => $this->whatsAppService->hasWebhookConfiguration(),
            'connection' => $connection,
            'statusError' => $statusError,
            'statusLabels' => $this->statusLabels(),
        ]);
    }

    public function connect(Request $request)
    {
        $enterprise = $this->resolveEnterprise($request);

        if (! $enterprise) {
            throw ValidationException::withMessages([
                'enterprise_id' => 'Selecione um escritorio para conectar o WhatsApp.',
            ]);
        }

        if (! $this->whatsAppService->hasBaseConfiguration()) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Configure EVOLUTION_API_BASE_URL para conectar o WhatsApp.',
            ]);
        }

        $isNewInstance = ! $enterprise->evolution_instance;
        $instance = $enterprise->evolution_instance ?: $this->makeInstanceName($enterprise);

        try {
            $connection = $isNewInstance
                ? $this->whatsAppService->createInstance($instance)
                : $this->whatsAppService->connect($instance);

            if ($isNewInstance && ! ($connection['qr_code'] ?? null)) {
                $connection = $this->whatsAppService->connect($instance);
            }

            $enterprise->forceFill([
                'evolution_instance' => $instance,
            ])->save();

            $this->syncEnterpriseConnection($enterprise, $connection);
            $webhookConfigured = $this->whatsAppService->configureWebhookSafely($instance, [
                'enterprise_id' => $enterprise->id,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'whatsapp' => $this->evolutionErrorMessage($exception),
            ]);
        }

        $message = $webhookConfigured
            ? 'Conexao iniciada. Escaneie o QR Code com o WhatsApp do escritorio. O webhook do chatbot foi cadastrado na Evolution.'
            : 'Conexao iniciada. Escaneie o QR Code com o WhatsApp do escritorio. Confira a configuracao do webhook do chatbot.';

        return redirect()
            ->route('whatsapp.connection.show', $this->routeParameters($request, $enterprise))
            ->with('success', $message);
    }

    public function refresh(Request $request)
    {
        $enterprise = $this->resolveEnterprise($request);

        if (! $enterprise?->evolution_instance) {
            return redirect()
                ->route('whatsapp.connection.show', $this->routeParameters($request, $enterprise))
                ->withErrors(['whatsapp' => 'Inicie a conexao antes de atualizar o status.']);
        }

        try {
            $connection = $this->whatsAppService->connectionState($enterprise->evolution_instance);
            $this->syncEnterpriseConnection($enterprise, $connection);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'whatsapp' => 'Nao foi possivel atualizar o status na Evolution API.',
            ]);
        }

        return redirect()
            ->route('whatsapp.connection.show', $this->routeParameters($request, $enterprise))
            ->with('success', 'Status do WhatsApp atualizado.');
    }

    public function disconnect(Request $request)
    {
        $enterprise = $this->resolveEnterprise($request);

        if (! $enterprise?->evolution_instance) {
            return redirect()
                ->route('whatsapp.connection.show', $this->routeParameters($request, $enterprise))
                ->withErrors(['whatsapp' => 'Nenhuma instancia de WhatsApp foi criada para este escritorio.']);
        }

        try {
            $this->whatsAppService->logout($enterprise->evolution_instance);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'whatsapp' => 'Nao foi possivel desconectar o WhatsApp na Evolution API.',
            ]);
        }

        $enterprise->forceFill([
            'whatsapp_connection_status' => 'disconnected',
            'whatsapp_qr_code' => null,
            'whatsapp_disconnected_at' => now(),
        ])->save();

        return redirect()
            ->route('whatsapp.connection.show', $this->routeParameters($request, $enterprise))
            ->with('success', 'WhatsApp desconectado do escritorio.');
    }

    private function resolveEnterprise(Request $request): ?Enterprise
    {
        $actor = $request->user();

        if ($actor->isAdmin()) {
            $enterpriseId = $request->integer('enterprise_id') ?: $actor->enterprise_id;

            return $enterpriseId
                ? Enterprise::query()->find($enterpriseId)
                : null;
        }

        if (! $actor->enterprise_id) {
            return null;
        }

        return Enterprise::query()->find($actor->enterprise_id);
    }

    private function makeInstanceName(Enterprise $enterprise): string
    {
        $base = Str::slug($enterprise->slug ?: $enterprise->name);
        $base = $base !== '' ? $base : 'escritorio';

        return 'juristack-'.$base.'-'.$enterprise->id;
    }

    private function syncEnterpriseConnection(Enterprise $enterprise, array $connection): void
    {
        $state = $this->normalizeStatus($connection['state'] ?? null)
            ?: (($connection['qr_code'] ?? null) ? 'qrcode' : null);
        $isConnected = in_array($state, ['open', 'connected'], true);
        $isDisconnected = $state === 'disconnected';

        $enterprise->forceFill([
            'whatsapp_connection_status' => $state ?: $enterprise->whatsapp_connection_status,
            'whatsapp_qr_code' => ($isConnected || $isDisconnected)
                ? null
                : (($connection['qr_code'] ?? null) ?: $enterprise->whatsapp_qr_code),
            'whatsapp_connected_at' => $isConnected
                ? ($enterprise->whatsapp_connected_at ?: now())
                : $enterprise->whatsapp_connected_at,
            'whatsapp_disconnected_at' => $isConnected
                ? null
                : ($isDisconnected && $enterprise->whatsapp_connection_status !== 'disconnected'
                    ? now()
                    : $enterprise->whatsapp_disconnected_at),
        ])->save();
    }

    private function normalizeStatus(?string $status): ?string
    {
        if (! $status) {
            return null;
        }

        return match (strtolower($status)) {
            'open', 'connected' => 'connected',
            'connecting' => 'connecting',
            'close', 'closed', 'disconnected', 'notconnected' => 'disconnected',
            default => strtolower($status),
        };
    }

    private function statusLabels(): array
    {
        return [
            'connected' => 'Conectado',
            'open' => 'Conectado',
            'connecting' => 'Conectando',
            'qrcode' => 'Aguardando QR Code',
            'disconnected' => 'Desconectado',
        ];
    }

    private function evolutionErrorMessage(Throwable $exception): string
    {
        if ($exception instanceof RequestException && $exception->response) {
            $status = $exception->response->status();
            $body = Str::limit(trim($exception->response->body()), 300);

            if ($status === 404 && $this->looksLikeWrongEvolutionBaseUrl($body)) {
                return 'Nao foi possivel iniciar a conexao com a Evolution API. '
                    .'O EVOLUTION_API_BASE_URL parece apontar para outro sistema, e nao para a API da Evolution. '
                    .'Use a URL base publica da Evolution, sem sufixos como /manager ou /manager/login, e depois limpe o cache de configuracao.'
                    .($body !== '' ? " Resposta HTTP 404: {$body}" : '');
            }

            return 'Nao foi possivel iniciar a conexao com a Evolution API. '
                ."A Evolution respondeu HTTP {$status}"
                .($body !== '' ? ": {$body}" : '.');
        }

        if ($exception instanceof ConnectionException) {
            return 'Nao foi possivel conectar na Evolution API. Verifique se o container esta rodando e se EVOLUTION_API_BASE_URL aponta para uma URL publica acessivel.';
        }

        return 'Nao foi possivel iniciar a conexao com a Evolution API: '.$exception->getMessage();
    }

    private function routeParameters(Request $request, ?Enterprise $enterprise): array
    {
        if (! $request->user()?->isAdmin() || ! $enterprise) {
            return [];
        }

        return ['enterprise_id' => $enterprise->id];
    }

    private function looksLikeWrongEvolutionBaseUrl(string $body): bool
    {
        $normalized = strtolower($body);

        return str_contains($normalized, 'the route instance/create could not be found')
            || str_contains($normalized, 'abstractroutecollection.php')
            || str_contains($normalized, 'laravel/framework')
            || str_contains($normalized, 'nfoundhttpexception');
    }
}
