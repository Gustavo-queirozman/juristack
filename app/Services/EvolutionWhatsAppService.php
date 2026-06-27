<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppService
{
    public function isConfigured(): bool
    {
        return $this->baseUrl() !== '' && $this->instance() !== '';
    }

    public function canSendTo(?string $phone): bool
    {
        return $this->normalizePhone($phone) !== null;
    }

    public function sendText(?string $phone, string $message): bool
    {
        $normalizedPhone = $this->normalizePhone($phone);

        if ($normalizedPhone === null || ! $this->isConfigured()) {
            return false;
        }

        $request = Http::timeout(15)->acceptJson();
        $apiKey = (string) config('services.evolution.api_key', '');

        if ($apiKey !== '') {
            $request = $request->withHeaders([
                'apikey' => $apiKey,
            ]);
        }

        $request->post($this->messageEndpoint(), [
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

    private function baseUrl(): string
    {
        return rtrim((string) config('services.evolution.base_url', ''), '/');
    }

    private function instance(): string
    {
        return trim((string) config('services.evolution.instance', ''));
    }

    private function messageEndpoint(): string
    {
        return $this->baseUrl().'/message/sendText/'.$this->instance();
    }
}
