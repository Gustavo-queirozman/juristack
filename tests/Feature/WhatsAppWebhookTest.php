<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerDocumentRequest;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_webhook_answers_customer_with_chatbot(): void
    {
        Http::fake([
            'https://evolution.test/message/sendText/juristack-test' => Http::response([], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.api_key', 'secret');
        config()->set('services.whatsapp.token', 'webhook-secret');

        $enterprise = Enterprise::create([
            'name' => 'Escritorio Teste',
            'evolution_instance' => 'juristack-test',
            'whatsapp_connection_status' => 'connected',
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria Cliente',
            'mobile_phone' => '(11) 99999-0000',
            'email' => 'maria@example.com',
        ]);

        CustomerDocumentRequest::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'document_type' => 'identification',
            'description' => 'RG ou CNH',
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        $response = $this->postJson('/api/whatsapp/webhook?token=webhook-secret', [
            'instance' => 'juristack-test',
            'data' => [
                'key' => [
                    'remoteJid' => '5511999990000@s.whatsapp.net',
                    'fromMe' => false,
                ],
                'message' => [
                    'conversation' => 'documentos',
                ],
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'handled' => true,
                'reason' => 'sent',
                'enterprise_id' => $enterprise->id,
                'customer_id' => $customer->id,
            ]);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://evolution.test/message/sendText/juristack-test'
                && $request['number'] === '5511999990000'
                && str_contains($request['text'], 'Maria Cliente')
                && str_contains($request['text'], 'RG ou CNH');
        });
    }

    public function test_whatsapp_webhook_rejects_invalid_token(): void
    {
        config()->set('services.whatsapp.token', 'webhook-secret');

        $response = $this->postJson('/api/whatsapp/webhook?token=wrong', []);

        $response->assertForbidden();
    }

    public function test_whatsapp_webhook_rejects_missing_configured_token(): void
    {
        config()->set('services.whatsapp.token', '');

        $response = $this->postJson('/api/whatsapp/webhook', []);

        $response->assertForbidden();
    }

    public function test_whatsapp_webhook_ignores_own_messages(): void
    {
        Http::fake();

        config()->set('services.whatsapp.token', 'webhook-secret');

        $enterprise = Enterprise::create([
            'name' => 'Escritorio Teste',
            'evolution_instance' => 'juristack-test',
        ]);

        $response = $this->postJson('/api/whatsapp/webhook', [
            'instance' => $enterprise->evolution_instance,
            'data' => [
                'key' => [
                    'remoteJid' => '5511999990000@s.whatsapp.net',
                    'fromMe' => true,
                ],
                'message' => [
                    'conversation' => 'oi',
                ],
            ],
        ], [
            'X-Webhook-Token' => 'webhook-secret',
        ]);

        $response->assertOk()
            ->assertJson([
                'handled' => false,
                'reason' => 'own_message',
            ]);

        Http::assertNothingSent();
    }
}
