<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DatajudProcesso;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataJudProcessosTest extends TestCase
{
    use RefreshDatabase;

    public function test_salvar_processo_vincula_cliente_pelo_cpf(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'user_id' => $user->id,
            'name' => 'Maria da Silva',
            'cnp' => '12345678909',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('datajud.salvar'), [
                'tribunal' => 'TJMG',
                'cpf_cliente' => '123.456.789-09',
                'source' => [
                    'id' => 'proc-1',
                    'numeroProcesso' => '0001234-56.2023.8.13.0001',
                    'grau' => 'G1',
                    'assuntos' => [],
                    'movimentos' => [],
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('customer.id', $customer->id);

        $this->assertDatabaseHas('datajud_processos', [
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
        ]);

        $this->assertDatabaseHas('processo_monitors', [
            'user_id' => $user->id,
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'ativo' => true,
        ]);
    }

    public function test_lista_de_processos_salvos_filtra_por_nome_ou_cpf_do_cliente(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $maria = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria da Silva',
            'cnp' => '12345678909',
        ]);

        $joao = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Joao Pereira',
            'cnp' => '98765432100',
        ]);

        DatajudProcesso::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $maria->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        DatajudProcesso::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $joao->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0009999-10.2023.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        $responseByName = $this
            ->actingAs($user)
            ->get(route('datajud.salvos', ['busca' => 'Maria da Silva']));

        $responseByName->assertOk()
            ->assertSee('0001234-56.2023.8.13.0001')
            ->assertDontSee('0009999-10.2023.8.13.0001');

        $responseByCpf = $this
            ->actingAs($user)
            ->get(route('datajud.salvos', ['busca' => '123.456.789-09']));

        $responseByCpf->assertOk()
            ->assertSee('0001234-56.2023.8.13.0001')
            ->assertDontSee('0009999-10.2023.8.13.0001');
    }
}
