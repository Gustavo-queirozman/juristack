<?php

namespace Tests\Feature;

use App\Mail\ServiceContractSignatureMail;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerServiceContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_creation_generates_and_sends_service_contract_signed_by_enterprise(): void
    {
        Storage::fake('public');
        Mail::fake();

        $enterprise = Enterprise::create([
            'name' => 'Atenas Advocacia',
            'cnp' => '12345678000199',
            'address' => 'Rua Central, 100, Belo Horizonte/MG',
            'email' => 'contato@atenas.test',
        ]);

        $actor = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'email' => 'admin@atenas.test',
        ]);

        $response = $this->actingAs($actor)->post(route('customers.store'), [
            'name' => 'Maria da Silva',
            'cnp' => '123.456.789-09',
            'email' => 'maria@example.com',
            'mobile_phone' => '(31) 99999-0000',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'send_service_contract' => '1',
            'service_contract_signer_type' => 'enterprise',
            'service_contract_subject' => 'acao previdenciaria',
            'service_contract_city' => 'Belo Horizonte',
        ]);

        $response->assertRedirect(route('customers.index'));
        $response->assertSessionHas('success', 'Cliente cadastrado com sucesso e contrato enviado para assinatura por e-mail.');

        $customer = Customer::where('email', 'maria@example.com')->firstOrFail();
        $document = Document::where('customer_id', $customer->id)->firstOrFail();
        $relativePath = ltrim((string) parse_url($document->document_link, PHP_URL_PATH), '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        $this->assertSame('contract', $document->type);
        Storage::disk('public')->assertExists($relativePath);

        Mail::assertSent(ServiceContractSignatureMail::class, function (ServiceContractSignatureMail $mail) use ($document): bool {
            return $mail->hasTo('maria@example.com')
                && $mail->document->is($document)
                && ($mail->signer['type'] ?? null) === 'enterprise';
        });
    }

    public function test_customer_creation_generates_and_sends_service_contract_signed_by_lawyer(): void
    {
        Storage::fake('public');
        Mail::fake();

        $enterprise = Enterprise::create([
            'name' => 'Atenas Advocacia',
            'address' => 'Rua Central, 100, Belo Horizonte/MG',
        ]);

        $actor = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'email' => 'admin@atenas.test',
        ]);

        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
            'name' => 'Joao Pereira',
            'email' => 'joao@atenas.test',
            'oab_state' => 'MG',
            'oab_number' => '12345',
        ]);

        $response = $this->actingAs($actor)->post(route('customers.store'), [
            'name' => 'Carlos Souza',
            'cnp' => '987.654.321-00',
            'email' => 'carlos@example.com',
            'mobile_phone' => '(31) 98888-0000',
            'city' => 'Contagem',
            'state' => 'MG',
            'send_service_contract' => '1',
            'service_contract_signer_type' => 'lawyer',
            'service_contract_signer_user_id' => (string) $lawyer->id,
            'service_contract_subject' => 'defesa em processo civel',
            'service_contract_city' => 'Contagem',
        ]);

        $response->assertRedirect(route('customers.index'));

        Mail::assertSent(ServiceContractSignatureMail::class, function (ServiceContractSignatureMail $mail) use ($lawyer): bool {
            return $mail->hasTo('carlos@example.com')
                && ($mail->signer['type'] ?? null) === 'lawyer'
                && ($mail->signer['name'] ?? null) === $lawyer->name;
        });
    }
}
