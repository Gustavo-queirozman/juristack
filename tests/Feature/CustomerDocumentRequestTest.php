<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerDocumentRequest;
use App\Models\DatajudProcesso;
use App\Models\Enterprise;
use App\Models\User;
use App\Notifications\CustomerDocumentRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerDocumentRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_user_can_request_customer_document_and_notify_client(): void
    {
        Notification::fake();

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);
        $clientUser = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $customer = Customer::create([
            'user_id' => $clientUser->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria da Silva',
            'email' => $clientUser->email,
        ]);

        $response = $this
            ->actingAs($lawyer)
            ->post(route('customers.document-requests.store', $customer), [
                'document_type' => 'cpf',
                'description' => 'Envie um PDF legivel do CPF.',
            ]);

        $response->assertRedirect(route('customers.show', $customer));

        $this->assertDatabaseHas('customer_document_requests', [
            'customer_id' => $customer->id,
            'requested_by_user_id' => $lawyer->id,
            'document_type' => 'cpf',
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        Notification::assertSentTo($clientUser, CustomerDocumentRequestNotification::class);
    }

    public function test_client_dashboard_displays_pending_document_requests(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);
        $clientUser = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $customer = Customer::create([
            'user_id' => $clientUser->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria da Silva',
            'email' => $clientUser->email,
        ]);
        $processo = DatajudProcesso::create([
            'user_id' => $lawyer->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        CustomerDocumentRequest::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'datajud_processo_id' => $processo->id,
            'requested_by_user_id' => $lawyer->id,
            'document_type' => 'medical_report',
            'description' => 'Anexe o laudo assinado mais recente.',
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($clientUser)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Documentos solicitados')
            ->assertSee('Laudo ou documento complementar')
            ->assertSee('Anexe o laudo assinado mais recente.')
            ->assertSee('0001234-56.2023.8.13.0001');
    }

    public function test_client_upload_fulfills_matching_document_request(): void
    {
        Storage::fake('public');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);
        $clientUser = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $customer = Customer::create([
            'user_id' => $clientUser->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria da Silva',
            'email' => $clientUser->email,
        ]);
        $processo = DatajudProcesso::create([
            'user_id' => $lawyer->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);
        $documentRequest = CustomerDocumentRequest::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'datajud_processo_id' => $processo->id,
            'requested_by_user_id' => $lawyer->id,
            'document_type' => 'medical_report',
            'description' => 'Envie o laudo atualizado.',
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        $response = $this
            ->actingAs($clientUser)
            ->from(route('dashboard'))
            ->post(route('customers.upload'), [
                'datajud_processo_id' => $processo->id,
                'document_type' => 'medical_report',
                'file' => UploadedFile::fake()->create('laudo.pdf', 100, 'application/pdf'),
            ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('customer_document_requests', [
            'id' => $documentRequest->id,
            'status' => CustomerDocumentRequest::STATUS_FULFILLED,
        ]);

        $this->assertNotNull($documentRequest->fresh()->fulfilled_at);
    }
}
