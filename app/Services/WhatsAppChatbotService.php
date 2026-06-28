<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerDocumentRequest;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppChatbotService
{
    public function __construct(private readonly EvolutionWhatsAppService $whatsAppService) {}

    public function handle(array $payload): array
    {
        if ($this->isOwnMessage($payload)) {
            return ['handled' => false, 'reason' => 'own_message'];
        }

        $remoteJid = $this->extractRemoteJid($payload);

        if ($remoteJid && str_contains($remoteJid, '@g.us')) {
            return ['handled' => false, 'reason' => 'group_message'];
        }

        $phone = $this->extractPhone($payload, $remoteJid);
        $text = $this->extractText($payload);
        $enterprise = $this->resolveEnterprise($payload);

        if (! $enterprise?->evolution_instance || ! $phone || ! $text) {
            Log::info('Webhook WhatsApp ignorado pelo chatbot.', [
                'instance' => $this->extractInstance($payload),
                'has_phone' => (bool) $phone,
                'has_text' => (bool) $text,
            ]);

            return ['handled' => false, 'reason' => 'missing_context'];
        }

        $customer = $this->findCustomer($enterprise, $phone);
        $reply = $this->buildReply($enterprise, $customer, $text);

        $sent = $this->whatsAppService->sendTextSafely($phone, $reply, [
            'source' => 'whatsapp_chatbot',
            'enterprise_id' => $enterprise->id,
        ], $enterprise->evolution_instance);

        return [
            'handled' => $sent,
            'reason' => $sent ? 'sent' : 'send_failed',
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer?->id,
        ];
    }

    private function resolveEnterprise(array $payload): ?Enterprise
    {
        $instance = $this->extractInstance($payload);

        if (! $instance) {
            return null;
        }

        return Enterprise::query()
            ->where('evolution_instance', $instance)
            ->first();
    }

    private function findCustomer(Enterprise $enterprise, string $phone): ?Customer
    {
        $normalized = $this->digits($phone);
        $withoutCountry = str_starts_with($normalized, '55')
            ? substr($normalized, 2)
            : $normalized;

        return $enterprise->customers()
            ->where(function ($query) use ($normalized, $withoutCountry) {
                foreach (['mobile_phone', 'phone', 'phone_2'] as $column) {
                    $query->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE($column, '+', ''), ' ', ''), '-', ''), '(', ''), ')', '') in (?, ?)",
                        [$normalized, $withoutCountry]
                    );
                }
            })
            ->first();
    }

    private function buildReply(Enterprise $enterprise, ?Customer $customer, string $text): string
    {
        $normalizedText = Str::of($text)
            ->lower()
            ->ascii()
            ->squish()
            ->toString();

        if (str_contains($normalizedText, 'document') || str_contains($normalizedText, 'arquivo')) {
            return $this->documentsReply($enterprise, $customer);
        }

        if (str_contains($normalizedText, 'portal') || str_contains($normalizedText, 'login') || str_contains($normalizedText, 'acesso')) {
            return $this->portalReply($enterprise, $customer);
        }

        if (str_contains($normalizedText, 'atendente') || str_contains($normalizedText, 'advogado') || str_contains($normalizedText, 'humano')) {
            return $this->humanReply($enterprise, $customer);
        }

        if (in_array($normalizedText, ['1', '2', '3'], true)) {
            return match ($normalizedText) {
                '1' => $this->documentsReply($enterprise, $customer),
                '2' => $this->portalReply($enterprise, $customer),
                default => $this->humanReply($enterprise, $customer),
            };
        }

        $name = $customer?->name ? " {$customer->name}" : '';

        return "Ola{$name}! Sou o assistente virtual do {$enterprise->name}.\n\n"
            ."Como posso ajudar?\n"
            ."1. Documentos pendentes\n"
            ."2. Acesso ao portal\n"
            ."3. Falar com atendimento";
    }

    private function documentsReply(Enterprise $enterprise, ?Customer $customer): string
    {
        if (! $customer) {
            return "Ainda nao encontrei seu cadastro pelo telefone. Um atendente do {$enterprise->name} pode conferir seus documentos para voce.";
        }

        $pending = CustomerDocumentRequest::query()
            ->where('enterprise_id', $enterprise->id)
            ->where('customer_id', $customer->id)
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        if ($pending->isEmpty()) {
            return "{$customer->name}, nao encontrei documentos pendentes no momento. Se voce acabou de enviar arquivos, nossa equipe vai conferir em breve.";
        }

        $lines = $pending->map(function (CustomerDocumentRequest $request): string {
            $description = trim((string) $request->description);

            return '- '.$request->document_type_label.($description !== '' ? ': '.$description : '');
        })->implode("\n");

        return "{$customer->name}, encontrei estes documentos pendentes:\n{$lines}\n\nVoce pode envia-los pelo portal do cliente ou responder aqui para falar com atendimento.";
    }

    private function portalReply(Enterprise $enterprise, ?Customer $customer): string
    {
        $url = url('/dashboard');
        $name = $customer?->name ? "{$customer->name}, " : '';

        return "{$name}acesse seu portal por aqui: {$url}\n\nUse o mesmo e-mail cadastrado no escritorio. Se precisar recuperar a senha, escolha \"esqueci minha senha\" na tela de login.";
    }

    private function humanReply(Enterprise $enterprise, ?Customer $customer): string
    {
        $name = $customer?->name ? " {$customer->name}" : '';

        return "Certo{$name}. Ja deixei sua solicitacao encaminhada para o atendimento do {$enterprise->name}. Em breve alguem da equipe responde por aqui.";
    }

    private function extractInstance(array $payload): ?string
    {
        foreach (['instance', 'instanceName', 'data.instance', 'data.instanceName'] as $key) {
            $value = data_get($payload, $key);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function extractRemoteJid(array $payload): ?string
    {
        foreach ([
            'data.key.remoteJid',
            'data.remoteJid',
            'data.from',
            'sender',
            'from',
            'remoteJid',
        ] as $key) {
            $value = data_get($payload, $key);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function extractPhone(array $payload, ?string $remoteJid): ?string
    {
        foreach ([
            $remoteJid,
            data_get($payload, 'data.sender'),
            data_get($payload, 'data.key.participant'),
            data_get($payload, 'sender'),
            data_get($payload, 'from'),
        ] as $value) {
            if (! is_string($value)) {
                continue;
            }

            $digits = $this->digits(strtok($value, '@') ?: $value);

            if ($digits !== '') {
                return $digits;
            }
        }

        return null;
    }

    private function extractText(array $payload): ?string
    {
        foreach ([
            'data.message.conversation',
            'data.message.extendedTextMessage.text',
            'data.message.text',
            'data.text',
            'text.message',
            'message.text',
            'message',
        ] as $key) {
            $value = data_get($payload, $key);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function isOwnMessage(array $payload): bool
    {
        return (bool) (
            data_get($payload, 'data.key.fromMe')
            ?? data_get($payload, 'key.fromMe')
            ?? data_get($payload, 'fromMe')
            ?? false
        );
    }

    private function digits(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?: '';
    }
}
