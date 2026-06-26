<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\DatajudMovimento;
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
                'responsible_lawyer_user_id' => $user->id,
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
            'responsible_lawyer_user_id' => $user->id,
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

    public function test_advogado_responsavel_consegue_ver_processo_salvo_por_outro_usuario_do_escritorio(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);

        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
            'name' => 'Advogado Responsavel',
        ]);

        $otherLawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
            'name' => 'Outro Advogado',
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Cliente Teste',
            'cnp' => '12345678909',
        ]);

        DatajudProcesso::create([
            'user_id' => $admin->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'responsible_lawyer_user_id' => $lawyer->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        $this->actingAs($lawyer)
            ->get(route('datajud.salvos'))
            ->assertOk()
            ->assertSee('0001234-56.2023.8.13.0001')
            ->assertSee('Advogado Responsavel');

        $this->actingAs($otherLawyer)
            ->get(route('datajud.salvos'))
            ->assertOk()
            ->assertDontSee('0001234-56.2023.8.13.0001');
    }

    public function test_dashboard_do_cliente_exibe_ultimo_status_do_processo(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $clientUser = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);

        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'user_id' => $clientUser->id,
            'name' => 'Maria da Silva',
            'email' => $clientUser->email,
        ]);

        $processo = DatajudProcesso::create([
            'user_id' => $clientUser->id,
            'enterprise_id' => $enterprise->id,
            'customer_id' => $customer->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2023.8.13.0001',
            'grau' => 'G1',
            'datahora_ultima_atualizacao' => now()->subHour(),
            'payload' => [],
        ]);

        DatajudMovimento::create([
            'processo_id' => $processo->id,
            'nome' => 'Concluso para julgamento',
            'data_hora' => now()->subHour(),
        ]);

        $response = $this
            ->actingAs($clientUser)
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Ultimo status')
            ->assertSee('Concluso para julgamento')
            ->assertSee('0001234-56.2023.8.13.0001');
    }

    public function test_tela_do_cliente_interno_mostra_pasta_geral_e_pasta_por_processo(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);
        $customer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Maria da Silva',
            'email' => 'maria@example.com',
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

        CustomerFile::create([
            'customer_id' => $customer->id,
            'document_type' => 'other',
            'uploaded_by_user_id' => $lawyer->id,
            'path' => 'customers/' . $customer->id . '/geral/cadastro.pdf',
            'original_name' => 'cadastro.pdf',
            'mime' => 'application/pdf',
            'size' => 1234,
        ]);

        CustomerFile::create([
            'customer_id' => $customer->id,
            'datajud_processo_id' => $processo->id,
            'document_type' => 'other',
            'uploaded_by_user_id' => $lawyer->id,
            'path' => 'customers/' . $customer->id . '/processos/' . $processo->id . '/peticao.pdf',
            'original_name' => 'peticao.pdf',
            'mime' => 'application/pdf',
            'size' => 2345,
        ]);

        $response = $this
            ->actingAs($lawyer)
            ->get(route('customers.show', $customer));

        $response->assertOk()
            ->assertSee('Pasta geral do cliente')
            ->assertSee('Pastas por processo')
            ->assertSee('cadastro.pdf')
            ->assertSee('peticao.pdf')
            ->assertSee('0001234-56.2023.8.13.0001');
    }
}
