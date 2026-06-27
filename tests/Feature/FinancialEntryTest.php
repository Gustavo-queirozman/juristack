<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\FinancialEntry;
use App\Models\FinancialEntryPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FinancialEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_user_can_create_financial_entry_in_own_enterprise(): void
    {
        [$enterprise, $user] = $this->createInternalUser();
        $otherEnterprise = Enterprise::create(['name' => 'Outra Empresa']);
        $customer = $this->createCustomer($enterprise, 'Maria da Silva', '31999999999', '12345678901');
        $this->createCustomer($otherEnterprise, 'Cliente Externo', '31988888888', '10987654321');

        $response = $this
            ->actingAs($user)
            ->post(route('financial-entries.store'), [
                'enterprise_id' => $otherEnterprise->id,
                'customer_id' => $customer->id,
                'title' => 'Honorarios de junho',
                'amount' => '1500.50',
                'entry_date' => '2026-06-20',
                'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
                'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
                'notes' => 'Contrato mensal',
                'whatsapp_reminder_enabled' => '1',
            ]);

        $response->assertRedirect(route('financial-entries.index'));

        $entry = FinancialEntry::query()->first();

        $this->assertNotNull($entry);
        $this->assertSame($enterprise->id, $entry->enterprise_id);
        $this->assertSame($customer->id, $entry->customer_id);
        $this->assertSame('Honorarios de junho', $entry->title);
        $this->assertSame('1500.50', $entry->amount);
        $this->assertSame('2026-06-20', $entry->entry_date?->format('Y-m-d'));
        $this->assertSame(FinancialEntry::TYPE_RECEIVABLE, $entry->entry_type);
        $this->assertSame(FinancialEntry::PAYMENT_METHOD_PIX, $entry->payment_method);
        $this->assertSame('Contrato mensal', $entry->notes);
        $this->assertTrue((bool) $entry->whatsapp_reminder_enabled);
    }

    public function test_internal_user_only_sees_financial_entries_from_same_enterprise(): void
    {
        [$enterprise, $user] = $this->createInternalUser();
        $otherEnterprise = Enterprise::create(['name' => 'Outra Empresa']);

        FinancialEntry::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $this->createCustomer($enterprise, 'Cliente 1')->id,
            'title' => 'Recebimento visivel',
            'amount' => '100.00',
            'entry_date' => '2026-06-10',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
            'whatsapp_reminder_enabled' => true,
        ]);

        FinancialEntry::create([
            'enterprise_id' => $otherEnterprise->id,
            'customer_id' => $this->createCustomer($otherEnterprise, 'Cliente 2')->id,
            'title' => 'Recebimento oculto',
            'amount' => '200.00',
            'entry_date' => '2026-06-11',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_CARD,
            'whatsapp_reminder_enabled' => true,
        ]);

        $response = $this->actingAs($user)->get(route('financial-entries.index'));

        $response->assertOk();
        $response->assertSee('Recebimento visivel');
        $response->assertDontSee('Recebimento oculto');
    }

    public function test_internal_user_can_register_partial_payment(): void
    {
        [$enterprise, $user] = $this->createInternalUser();
        $customer = $this->createCustomer($enterprise, 'Maria da Silva');
        $entry = FinancialEntry::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'title' => 'Mensalidade',
            'amount' => '1000.00',
            'entry_date' => '2026-06-25',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
            'whatsapp_reminder_enabled' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('financial-entries.payments.store', $entry->id), [
                'payment_amount' => '400.00',
                'payment_date' => '2026-06-26',
                'payment_reference' => 'PIX-123',
                'payment_notes' => 'Entrada inicial',
            ]);

        $response->assertRedirect(route('financial-entries.edit', $entry->id));

        $this->assertDatabaseHas('financial_entry_payments', [
            'financial_entry_id' => $entry->id,
            'amount' => '400.00',
            'source' => FinancialEntryPayment::SOURCE_MANUAL,
            'reference' => 'PIX-123',
        ]);
    }

    public function test_internal_user_can_open_whatsapp_charge_for_pending_receivable(): void
    {
        [$enterprise, $user] = $this->createInternalUser();
        $customer = $this->createCustomer($enterprise, 'Maria da Silva', '31999999999');
        $entry = FinancialEntry::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'title' => 'Mensalidade',
            'amount' => '500.00',
            'entry_date' => '2026-06-26',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
            'whatsapp_reminder_enabled' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('financial-entries.whatsapp-reminder', $entry->id));

        $response->assertRedirect();
        $this->assertStringContainsString('https://wa.me/5531999999999', $response->headers->get('Location'));
        $this->assertNotNull($entry->fresh()->last_whatsapp_reminder_at);
    }

    public function test_internal_user_can_import_bank_file_and_reconcile_payment(): void
    {
        [$enterprise, $user] = $this->createInternalUser();
        $customer = $this->createCustomer($enterprise, 'Maria da Silva', '31999999999', '12345678901');
        $entry = FinancialEntry::create([
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'title' => 'Honorarios INSS',
            'amount' => '500.00',
            'entry_date' => '2026-06-25',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
            'whatsapp_reminder_enabled' => true,
        ]);

        $statement = UploadedFile::fake()->createWithContent(
            'extrato.csv',
            "data;descricao;valor;documento\n2026-06-26;Pagamento Maria da Silva 12345678901;500,00;ABC123\n"
        );

        $response = $this
            ->actingAs($user)
            ->post(route('financial-entries.import-bank-file'), [
                'statement_file' => $statement,
            ]);

        $response->assertRedirect(route('financial-entries.index'));

        $this->assertDatabaseHas('financial_entry_payments', [
            'financial_entry_id' => $entry->id,
            'amount' => '500.00',
            'source' => FinancialEntryPayment::SOURCE_BANK_IMPORT,
            'reference' => 'ABC123',
        ]);
    }

    private function createInternalUser(): array
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        return [$enterprise, $user];
    }

    private function createCustomer(
        Enterprise $enterprise,
        string $name,
        ?string $mobilePhone = '31999999999',
        ?string $cnp = null
    ): Customer {
        return Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@teste.local',
            'mobile_phone' => $mobilePhone,
            'cnp' => $cnp,
        ]);
    }
}
