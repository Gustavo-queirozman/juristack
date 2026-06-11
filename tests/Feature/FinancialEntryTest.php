<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\FinancialEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_user_can_create_financial_entry_in_own_enterprise(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $otherEnterprise = Enterprise::create(['name' => 'Outra Empresa']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('financial-entries.store'), [
                'enterprise_id' => $otherEnterprise->id,
                'title' => 'Honorarios de junho',
                'amount' => '1500.50',
                'entry_date' => '2026-06-20',
                'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
                'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
            ]);

        $response->assertRedirect(route('financial-entries.index'));

        $entry = FinancialEntry::query()->first();

        $this->assertNotNull($entry);
        $this->assertSame($enterprise->id, $entry->enterprise_id);
        $this->assertSame('Honorarios de junho', $entry->title);
        $this->assertSame('1500.50', $entry->amount);
        $this->assertSame('2026-06-20', $entry->entry_date?->format('Y-m-d'));
        $this->assertSame(FinancialEntry::TYPE_RECEIVABLE, $entry->entry_type);
        $this->assertSame(FinancialEntry::PAYMENT_METHOD_PIX, $entry->payment_method);
    }

    public function test_internal_user_only_sees_financial_entries_from_same_enterprise(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $otherEnterprise = Enterprise::create(['name' => 'Outra Empresa']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        FinancialEntry::create([
            'enterprise_id' => $enterprise->id,
            'title' => 'Recebimento visivel',
            'amount' => '100.00',
            'entry_date' => '2026-06-10',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_PIX,
        ]);

        FinancialEntry::create([
            'enterprise_id' => $otherEnterprise->id,
            'title' => 'Recebimento oculto',
            'amount' => '200.00',
            'entry_date' => '2026-06-11',
            'entry_type' => FinancialEntry::TYPE_RECEIVABLE,
            'payment_method' => FinancialEntry::PAYMENT_METHOD_CARD,
        ]);

        $response = $this->actingAs($user)->get(route('financial-entries.index'));

        $response->assertOk();
        $response->assertSee('Recebimento visivel');
        $response->assertDontSee('Recebimento oculto');
    }
}
