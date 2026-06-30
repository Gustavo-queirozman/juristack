<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_enterprise_admin_can_view_whatsapp_connection_screen(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('whatsapp.connection.show'));

        $response->assertOk();
        $response->assertSee('Conectar WhatsApp');
        $response->assertSee('Empresa Teste');
    }

    public function test_client_cannot_view_whatsapp_connection_screen(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $client = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);

        $response = $this->actingAs($client)->get(route('whatsapp.connection.show'));

        $response->assertForbidden();
    }

    public function test_enterprise_admin_can_start_whatsapp_connection_with_evolution(): void
    {
        Http::fake([
            'https://evolution.test/instance/create' => Http::response([
                'instance' => ['state' => 'connecting'],
                'base64' => str_repeat('a', 240),
            ], 200),
            'https://evolution.test/instance/connectionState/*' => Http::response([
                'instance' => ['state' => 'connecting'],
            ], 200),
            'https://evolution.test/webhook/set/*' => Http::response([], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.api_key', 'secret');
        config()->set('services.whatsapp.token', 'webhook-secret');
        config()->set('services.whatsapp.webhook_url', 'https://app.test/api/whatsapp/webhook');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('whatsapp.connection.connect'));

        $response->assertRedirect(route('whatsapp.connection.show'));
        $response->assertSessionHas('success');

        $enterprise->refresh();
        $this->assertSame('juristack-empresa-teste-'.$enterprise->id, $enterprise->evolution_instance);
        $this->assertSame('connecting', $enterprise->whatsapp_connection_status);
        $this->assertStringStartsWith('data:image/png;base64,', $enterprise->whatsapp_qr_code);

        Http::assertSent(function ($request) use ($enterprise): bool {
            return $request->url() === 'https://evolution.test/instance/create'
                && $request->hasHeader('apikey', 'secret')
                && $request['instanceName'] === 'juristack-empresa-teste-'.$enterprise->id
                && $request['qrcode'] === true;
        });

        Http::assertSent(function ($request) use ($enterprise): bool {
            return $request->url() === 'https://evolution.test/webhook/set/juristack-empresa-teste-'.$enterprise->id
                && $request['enabled'] === true
                && $request['url'] === 'https://app.test/api/whatsapp/webhook?token=webhook-secret'
                && $request['events'] === ['MESSAGES_UPSERT'];
        });
    }

    public function test_connect_strips_manager_suffix_from_evolution_base_url(): void
    {
        Http::fake([
            'https://evolution.test/instance/create' => Http::response([
                'instance' => ['state' => 'connecting'],
                'base64' => str_repeat('a', 240),
            ], 200),
            'https://evolution.test/instance/connectionState/*' => Http::response([
                'instance' => ['state' => 'connecting'],
            ], 200),
            'https://evolution.test/webhook/set/*' => Http::response([], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test/manager');
        config()->set('services.evolution.api_key', 'secret');
        config()->set('services.whatsapp.token', 'webhook-secret');
        config()->set('services.whatsapp.webhook_url', 'https://app.test/api/whatsapp/webhook');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('whatsapp.connection.connect'));

        $response->assertRedirect(route('whatsapp.connection.show'));
        $response->assertSessionHas('success');

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://evolution.test/instance/create';
        });
    }

    public function test_connect_returns_specific_message_when_base_url_points_to_laravel_app(): void
    {
        Http::fake([
            '*' => Http::response([
                'message' => 'The route instance/create could not be found.',
                'exception' => 'Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException',
                'file' => '/var/www/streaming/backend/vendor/laravel/framework/src/Illuminate/Routing/AbstractRouteCollection.php',
            ], 404),
        ]);

        config()->set('services.evolution.base_url', 'https://streaming.example.com');
        config()->set('services.evolution.api_key', 'secret');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('whatsapp.connection.connect'));

        $response->assertSessionHasErrors('whatsapp');
        $this->assertStringContainsString(
            'EVOLUTION_API_BASE_URL parece apontar para outro sistema',
            session('errors')->first('whatsapp')
        );
        $this->assertStringContainsString(
            'sem sufixos como /manager ou /manager/login',
            session('errors')->first('whatsapp')
        );
    }
}
