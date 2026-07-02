<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppAuthNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_notification_is_also_sent_via_whatsapp(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.instance', 'juristack');
        config()->set('services.evolution.api_key', 'secret');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
            'name' => 'Maria da Silva',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile_phone' => '31999995555',
        ]);

        $user->sendPasswordResetNotification('token-123');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://evolution.test/message/sendText/juristack'
                && $request['number'] === '5531999995555'
                && str_contains($request['text'], 'redefinir sua senha');
        });
    }

    public function test_verify_email_notification_is_also_sent_via_whatsapp(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.instance', 'juristack');
        config()->set('services.evolution.api_key', 'secret');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->unverified()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
            'name' => 'Maria da Silva',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile_phone' => '31999994444',
        ]);

        $user->sendEmailVerificationNotification();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://evolution.test/message/sendText/juristack'
                && $request['number'] === '5531999994444'
                && str_contains($request['text'], 'confirme seu e-mail');
        });
    }

    public function test_client_notification_does_not_fall_back_to_enterprise_phone_when_customer_has_no_whatsapp(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.instance', 'juristack');
        config()->set('services.evolution.api_key', 'secret');

        $enterprise = Enterprise::create([
            'name' => 'Empresa Teste',
            'phone' => '31977776666',
        ]);
        $user = User::factory()->unverified()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
            'name' => 'Maria da Silva',
        ]);

        Customer::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $user->sendEmailVerificationNotification();

        Http::assertNothingSent();
    }
}
