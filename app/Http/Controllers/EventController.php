<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Event::query()
            ->where('user_id', $userId)
            ->orderBy('starts_at');

        // Para a agenda, buscamos por intervalo (start/end) e retornamos lista simples.
        if ($request->filled('start') && $request->filled('end')) {
            $start = $request->date('start')->startOfDay();
            $end = $request->date('end')->endOfDay();

            $events = $query
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('starts_at', [$start, $end])
                      ->orWhere(function ($qq) use ($start, $end) {
                          $qq->whereNotNull('ends_at')
                             ->where('ends_at', '>=', $start)
                             ->where('starts_at', '<=', $end);
                      });
                })
                ->get();

            return response()->json($events);
        }

        // Lista paginada, mais recentes primeiro (fallback)
        return $query->orderByDesc('starts_at')->paginate(15);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();

        $event = Event::create($data);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        $this->authorizeOwner($event);
        return $event;
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeOwner($event);
        $data = $this->validated($request, $event->id);

        $event->update($data);

        return $event;
    }

    public function destroy(Event $event)
    {
        $this->authorizeOwner($event);
        $event->delete();

        return response()->noContent();
    }

    private function validated(Request $request, ?int $eventId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_public' => ['sometimes', 'boolean'],
        ]);
    }

    private function authorizeOwner(Event $event): void
    {
        if ((int) $event->user_id !== (int) Auth::id()) {
            abort(403);
        }
    }
}
