<?php

namespace Tests\Feature;

use App\Mail\ServiceContractSignatureMail;
use App\Models\Customer;
use App\Models\CustomerDocumentRequest;
use App\Models\Document;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
        $response->assertSessionHas('success', 'Cliente cadastrado com sucesso e contrato enviado para assinatura por e-mail e, quando disponivel, por WhatsApp.');

        $customer = Customer::where('email', 'maria@example.com')->firstOrFail();
        $document = Document::where('customer_id', $customer->id)->firstOrFail();
        $relativePath = ltrim((string) parse_url($document->document_link, PHP_URL_PATH), '/');
        $relativePath = preg_replace('#^storage/#', '', $relativePath);

        $this->assertSame('contract', $document->type);
        $this->assertSame('enterprise', $document->service_contract_signer_type);
        $this->assertNull($document->service_contract_signer_user_id);
        Storage::disk('public')->assertExists($relativePath);

        Mail::assertSent(ServiceContractSignatureMail::class, function (ServiceContractSignatureMail $mail) use ($document): bool {
            $html = $mail->render();

            return $mail->hasTo('maria@example.com')
                && $mail->document->is($document)
                && ($mail->signer['type'] ?? null) === 'enterprise'
                && $mail->envelope()->subject === 'Assinatura pendente: contrato de prestacao de servicos'
                && str_contains($html, 'assine o contrato');
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

        $customer = Customer::where('email', 'carlos@example.com')->firstOrFail();
        $document = Document::where('customer_id', $customer->id)->firstOrFail();

        $this->assertSame('lawyer', $document->service_contract_signer_type);
        $this->assertSame($lawyer->id, $document->service_contract_signer_user_id);

        Mail::assertSent(ServiceContractSignatureMail::class, function (ServiceContractSignatureMail $mail) use ($lawyer): bool {
            return $mail->hasTo('carlos@example.com')
                && ($mail->signer['type'] ?? null) === 'lawyer'
                && ($mail->signer['name'] ?? null) === $lawyer->name;
        });
    }

    public function test_internal_user_can_send_service_contract_from_customer_page_when_no_pending_documents(): void
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

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Mariana Dias',
            'email' => 'mariana@example.com',
            'city' => 'Belo Horizonte',
        ]);

        $response = $this->actingAs($actor)->post(route('customers.service-contract.send', $customer), [
            'service_contract_signer_type' => 'enterprise',
            'service_contract_subject' => 'consulta e acompanhamento juridico',
            'service_contract_city' => 'Belo Horizonte',
        ]);

        $response->assertRedirect(route('customers.show', $customer));
        $response->assertSessionHas('success', 'Contrato de prestacao de servicos enviado para assinatura por e-mail e, quando disponivel, por WhatsApp.');

        $document = Document::where('customer_id', $customer->id)->firstOrFail();
        $this->assertSame('enterprise', $document->service_contract_signer_type);
        $this->assertNull($document->service_contract_signer_user_id);

        Mail::assertSent(ServiceContractSignatureMail::class, function (ServiceContractSignatureMail $mail) use ($document): bool {
            return $mail->hasTo('mariana@example.com')
                && $mail->document->is($document)
                && ($mail->signer['type'] ?? null) === 'enterprise';
        });
    }

    public function test_internal_user_cannot_send_service_contract_when_customer_has_pending_document_requests(): void
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

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Paula Mendes',
            'email' => 'paula@example.com',
        ]);

        CustomerDocumentRequest::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'requested_by_user_id' => $actor->id,
            'document_type' => 'cpf',
            'description' => 'Envie o CPF atualizado.',
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($actor)
            ->from(route('customers.show', $customer))
            ->post(route('customers.service-contract.send', $customer), [
                'service_contract_signer_type' => 'enterprise',
                'service_contract_subject' => 'atendimento previdenciario',
                'service_contract_city' => 'Belo Horizonte',
            ]);

        $response->assertRedirect(route('customers.show', $customer));
        $response->assertSessionHasErrors([
            'send_service_contract' => 'Nao e possivel solicitar a assinatura enquanto houver documentos pendentes de envio pelo cliente.',
        ]);

        $this->assertDatabaseMissing('documents', [
            'customer_id' => $customer->id,
            'type' => 'contract',
        ]);

        Mail::assertNothingSent();
    }

    public function test_service_contract_email_is_also_sent_via_whatsapp_when_evolution_is_configured(): void
    {
        Storage::fake('public');
        Mail::fake();
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        config()->set('services.evolution.base_url', 'https://evolution.test');
        config()->set('services.evolution.instance', 'juristack');
        config()->set('services.evolution.api_key', 'secret');

        $enterprise = Enterprise::create([
            'name' => 'Atenas Advocacia',
            'address' => 'Rua Central, 100, Belo Horizonte/MG',
        ]);

        $actor = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'email' => 'admin@atenas.test',
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Mariana Dias',
            'email' => 'mariana@example.com',
            'mobile_phone' => '31988887777',
            'city' => 'Belo Horizonte',
        ]);

        $response = $this->actingAs($actor)->post(route('customers.service-contract.send', $customer), [
            'service_contract_signer_type' => 'enterprise',
            'service_contract_subject' => 'consulta e acompanhamento juridico',
            'service_contract_city' => 'Belo Horizonte',
        ]);

        $response->assertRedirect(route('customers.show', $customer));

        Http::assertSent(function ($request) {
            return $request->url() === 'https://evolution.test/message/sendText/juristack'
                && $request['number'] === '5531988887777'
                && str_contains($request['text'], 'contrato de prestacao de servicos');
        });
    }
}
