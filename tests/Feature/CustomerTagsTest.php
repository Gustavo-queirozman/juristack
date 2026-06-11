<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_user_can_store_customer_with_tags(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('customers.store'), [
                'name' => 'Cliente com Etiquetas',
                'tags' => ['Processo trabalhista', 'Processo criminal'],
            ]);

        $response->assertRedirect(route('customers.index'));

        $customer = Customer::first();

        $this->assertNotNull($customer);
        $this->assertSame(
            ['Processo trabalhista', 'Processo criminal'],
            $customer->tags
        );
    }

    public function test_customer_tags_are_trimmed_and_deduplicated_on_update(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Cliente Existente',
            'tags' => ['Contrato'],
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('customers.update', $customer), [
                'name' => 'Cliente Existente',
                'tags' => ['  Processo criminal  ', 'processo criminal', '', 'Audiência'],
            ]);

        $response->assertRedirect(route('customers.show', $customer));

        $this->assertSame(
            ['Processo criminal', 'Audiência'],
            $customer->fresh()->tags
        );
    }
}
