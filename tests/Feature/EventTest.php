<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_event_with_status_and_category(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LAWYER,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('events.store'), [
                'title' => 'Audiencia de instrucao',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Audiencia',
                'description' => 'Levar documentos do cliente.',
                'starts_at' => '2026-06-15 09:00:00',
                'ends_at' => '2026-06-15 10:00:00',
                'location' => 'Forum central',
                'is_public' => true,
            ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'Audiencia de instrucao',
                'status' => Event::STATUS_CONFIRMED,
                'category' => 'Audiencia',
            ]);

        $event = Event::query()->first();

        $this->assertNotNull($event);
        $this->assertSame($user->id, $event->user_id);
        $this->assertSame(Event::STATUS_CONFIRMED, $event->status);
        $this->assertSame('Audiencia', $event->category);
    }

    public function test_user_can_update_event_status_and_category(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_LAWYER,
        ]);

        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Reuniao inicial',
            'status' => Event::STATUS_PENDING,
            'category' => 'Reuniao',
            'starts_at' => '2026-06-16 14:00:00',
            'ends_at' => '2026-06-16 15:00:00',
            'is_public' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->putJson(route('events.update', $event), [
                'title' => 'Reuniao inicial',
                'status' => Event::STATUS_COMPLETED,
                'category' => 'Atendimento',
                'description' => 'Cliente atendido.',
                'starts_at' => '2026-06-16 14:00:00',
                'ends_at' => '2026-06-16 15:00:00',
                'location' => 'Escritorio',
                'is_public' => false,
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'status' => Event::STATUS_COMPLETED,
                'category' => 'Atendimento',
            ]);

        $event->refresh();

        $this->assertSame(Event::STATUS_COMPLETED, $event->status);
        $this->assertSame('Atendimento', $event->category);
    }
}
