<?php

namespace Tests\Feature;

use App\Models\DatajudProcesso;
use App\Models\Enterprise;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProcessLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task_linked_to_saved_process_with_deadline_priority_and_assignee(): void
    {
        $enterprise = Enterprise::create(['name' => 'Escritorio Atlas']);

        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $assignee = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $processo = DatajudProcesso::create([
            'user_id' => $user->id,
            'enterprise_id' => $enterprise->id,
            'tribunal' => 'TJMG',
            'numero_processo' => '0001234-56.2024.8.13.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Protocolar manifestacao',
                'description' => 'Revisar a minuta e protocolar no prazo.',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => '2026-06-20',
                'datajud_processo_id' => $processo->id,
                'users' => [$assignee->id],
            ]);

        $response->assertRedirect(route('tasks.index'));

        $task = Task::firstOrFail();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'enterprise_id' => $enterprise->id,
            'datajud_processo_id' => $processo->id,
            'title' => 'Protocolar manifestacao',
            'status' => 'pending',
            'priority' => 'high',
        ]);

        $this->assertSame('2026-06-20', $task->due_date?->format('Y-m-d'));

        $this->assertDatabaseHas('task_user', [
            'task_id' => $task->id,
            'user_id' => $assignee->id,
        ]);
    }

    public function test_user_cannot_link_task_to_process_from_another_enterprise(): void
    {
        $enterprise = Enterprise::create(['name' => 'Escritorio Atlas']);
        $otherEnterprise = Enterprise::create(['name' => 'Escritorio Prisma']);

        $user = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $foreignProcess = DatajudProcesso::create([
            'user_id' => User::factory()->create([
                'enterprise_id' => $otherEnterprise->id,
                'role' => User::ROLE_LAWYER,
            ])->id,
            'enterprise_id' => $otherEnterprise->id,
            'tribunal' => 'TJSP',
            'numero_processo' => '0009999-10.2024.8.26.0001',
            'grau' => 'G1',
            'payload' => [],
        ]);

        $response = $this
            ->from(route('tasks.index'))
            ->actingAs($user)
            ->post(route('tasks.store'), [
                'title' => 'Contato com cliente',
                'status' => 'pending',
                'priority' => 'medium',
                'datajud_processo_id' => $foreignProcess->id,
            ]);

        $response
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors('datajud_processo_id');

        $this->assertDatabaseCount('tasks', 0);
    }
}
