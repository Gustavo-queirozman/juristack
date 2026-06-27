<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppService
{
    public function hasBaseConfiguration(): bool
    {
        return $this->baseUrl() !== '';
    }

    public function isConfigured(?string $instance = null): bool
    {
        return $this->hasBaseConfiguration() && $this->instance($instance) !== '';
    }

    public function canSendTo(?string $phone): bool
    {
        return $this->normalizePhone($phone) !== null;
    }

    public function sendText(?string $phone, string $message, ?string $instance = null): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null || ! $this->isConfigured($instance)) {
            return false;
        }

        $this->request()->post($this->messageEndpoint($instance), [
            'number' => $normalizedPhone,
            'text' => $message,
        ])->throw();

        return true;
    }

    public function sendTextSafely(?string $phone, string $message, array $context = []): bool
    {
        try {
            return $this->sendText($phone, $message);
        } catch (\Throwable $exception) {
            Log::warning('Falha ao enviar notificacao via WhatsApp.', [
                'phone' => $this->normalizePhone($phone),
                'error' => $exception->getMessage(),
                'context' => $context,
            ]);

            return false;
        }
    }

    public function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (! str_starts_with($digits, '55')) {
            $digits = '55'.$digits;
        }

        return $digits;
    }

    public function createInstance(string $instance): array
    {
        if (! $this->hasBaseConfiguration() || trim($instance) === '') {
            return [];
        }

        $payload = $this->request()->post($this->endpoint('/instance/create'), [
            'instanceName' => $instance,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => true,
        ])->throw()->json() ?: [];

        return $this->normalizeConnectionPayload($payload);
    }

    public function connect(?string $instance = null): array
    {
        if (! $this->isConfigured($instance)) {
            return [];
        }

        $payload = $this->request()->get($this->endpoint('/instance/connect/'.$this->instance($instance)))
            ->throw()
            ->json() ?: [];

        return $this->normalizeConnectionPayload($payload);
    }

    public function connectionState(?string $instance = null): array
    {
        if (! $this->isConfigured($instance)) {
            return [];
        }

        $payload = $this->request()->get($this->endpoint('/instance/connectionState/'.$this->instance($instance)))
            ->throw()
            ->json() ?: [];

        return $this->normalizeConnectionPayload($payload);
    }

    public function logout(?string $instance = null): array
    {
        if (! $this->isConfigured($instance)) {
            return [];
        }

        $payload = $this->request()->delete($this->endpoint('/instance/logout/'.$this->instance($instance)))
            ->throw()
            ->json() ?: [];

        return $this->normalizeConnectionPayload($payload);
    }

    public function normalizeConnectionPayload(array $payload): array
    {
        $state = data_get($payload, 'instance.state')
            ?? data_get($payload, 'state')
            ?? data_get($payload, 'status')
            ?? data_get($payload, 'instance.status');

        return [
            'state' => is_string($state) ? $state : null,
            'qr_code' => $this->extractQrCode($payload),
            'pairing_code' => $this->extractPairingCode($payload),
            'payload' => $payload,
        ];
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.evolution.base_url', ''), '/');
    }

    private function instance(?string $instance = null): string
    {
        return trim((string) ($instance ?: config('services.evolution.instance', '')));
    }

    private function messageEndpoint(?string $instance = null): string
    {
        return $this->endpoint('/message/sendText/'.$this->instance($instance));
    }

    private function endpoint(string $path): string
    {
        return $this->baseUrl().'/'.ltrim($path, '/');
    }

    private function request()
    {
        $request = Http::timeout(20)->acceptJson();
        $apiKey = (string) config('services.evolution.api_key', '');

        if ($apiKey !== '') {
            $request = $request->withHeaders([
                'apikey' => $apiKey,
            ]);
        }

        return $request;
    }

    private function extractQrCode(array $payload): ?string
    {
        foreach ([
            'base64',
            'qrcode.base64',
            'qrcode.code',
            'qrcode',
            'qr',
            'code',
        ] as $key) {
            $value = data_get($payload, $key);

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $value = trim($value);

            if (str_starts_with($value, 'data:image')) {
                return $value;
            }

            if (strlen($value) > 200) {
                return 'data:image/png;base64,'.$value;
            }
        }

        return null;
    }

    private function extractPairingCode(array $payload): ?string
    {
        foreach ([
            'pairingCode',
            'pairing_code',
            'qrcode.pairingCode',
            'qrcode.pairing_code',
        ] as $key) {
            $value = data_get($payload, $key);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }
}
