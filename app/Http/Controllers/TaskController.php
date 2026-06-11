<?php

namespace App\Http\Controllers;

use App\Models\DatajudProcesso;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        $tasks = $this->scopedTasksQuery($actor)
            ->with(['users', 'processo.customer'])
            ->latest()
            ->get();
        $users = $this->scopedUsersQuery($actor)->orderBy('name')->get();
        $processes = $this->scopedProcessesQuery($actor)
            ->with('customer')
            ->orderByDesc('updated_at')
            ->get();
        $priorityOptions = Task::priorityLabels();

        return view('tasks.index', compact('tasks', 'users', 'processes', 'priorityOptions'));
    }

    public function create(Request $request)
    {
        $users = $this->scopedUsersQuery($request->user())->get();
        $processes = $this->scopedProcessesQuery($request->user())->with('customer')->get();
        $priorityOptions = Task::priorityLabels();

        return view('tasks.create', compact('users', 'processes', 'priorityOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'datajud_processo_id' => 'nullable|integer|exists:datajud_processos,id',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $actor = $request->user();
        $userIds = $this->validatedUserIds($actor, $request->input('users', []));
        $processId = $this->validatedProcessId($actor, $request->integer('datajud_processo_id'));

        $task = Task::create([
            'enterprise_id' => $actor->enterprise_id,
            'datajud_processo_id' => $processId,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->input('due_date'),
            'priority' => $request->input('priority'),
        ]);

        if (! empty($userIds)) {
            $task->users()->sync($userIds);
        }

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa criada com sucesso!');
    }

    public function show(Request $request, int $task)
    {
        $task = $this->scopedTask($request->user(), $task)->load(['users', 'processo.customer']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Request $request, int $task)
    {
        $task = $this->scopedTask($request->user(), $task)->load(['users', 'processo.customer']);
        $users = $this->scopedUsersQuery($request->user())->get();
        $processes = $this->scopedProcessesQuery($request->user())->with('customer')->get();
        $priorityOptions = Task::priorityLabels();

        return view('tasks.edit', compact('task', 'users', 'processes', 'priorityOptions'));
    }

    public function update(Request $request, int $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'datajud_processo_id' => 'nullable|integer|exists:datajud_processos,id',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        $actor = $request->user();
        $taskModel = $this->scopedTask($actor, $task);
        $userIds = $this->validatedUserIds($actor, $request->input('users', []));
        $processId = $this->validatedProcessId($actor, $request->integer('datajud_processo_id'));

        $taskModel->update([
            'datajud_processo_id' => $processId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'due_date' => $request->input('due_date'),
            'priority' => $request->input('priority'),
        ]);

        if (! empty($userIds)) {
            $taskModel->users()->sync($userIds);
        } else {
            $taskModel->users()->detach();
        }

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy(Request $request, int $task)
    {
        $taskModel = $this->scopedTask($request->user(), $task);
        $taskModel->delete();

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa removida com sucesso!');
    }

    public function updateStatus(Request $request, int $task)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $taskModel = $this->scopedTask($request->user(), $task);
        $taskModel->update([
            'status' => $request->status,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'task' => $taskModel->fresh(['users', 'processo.customer']),
            ]);
        }

        return back()->with('success', 'Status da tarefa atualizado com sucesso!');
    }

    public function updateAssignee(Request $request, int $task)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
        ]);

        $taskModel = $this->scopedTask($request->user(), $task);
        $userId = $data['user_id'] ?? null;
        $validIds = $this->validatedUserIds($request->user(), $userId ? [$userId] : []);

        $taskModel->users()->sync($validIds);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'task' => $taskModel->fresh(['users', 'processo.customer']),
            ]);
        }

        return back()->with('success', 'Responsável da tarefa atualizado com sucesso!');
    }

    public function users(Request $request, int $task)
    {
        $taskModel = $this->scopedTask($request->user(), $task)->load(['users', 'processo.customer']);
        $users = $taskModel->users;
        return view('tasks.users', ['task' => $taskModel, 'users' => $users]);
    }

    private function scopedTasksQuery(User $user): Builder
    {
        $query = Task::query();

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function scopedTask(User $user, int $taskId): Task
    {
        return $this->scopedTasksQuery($user)->findOrFail($taskId);
    }

    private function scopedUsersQuery(User $user): Builder
    {
        $query = User::query()->whereIn('role', [
            User::ROLE_ADMIN,
            User::ROLE_ENTERPRISE_ADMIN,
            User::ROLE_LAWYER,
        ]);

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function scopedProcessesQuery(User $user): Builder
    {
        $query = DatajudProcesso::query();

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function validatedUserIds(User $actor, array $userIds): array
    {
        $ids = collect($userIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (empty($ids)) {
            return [];
        }

        $validIds = $this->scopedUsersQuery($actor)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        sort($ids);
        $sortedValidIds = $validIds;
        sort($sortedValidIds);

        if ($ids !== $sortedValidIds) {
            throw ValidationException::withMessages([
                'users' => 'Selecione apenas usuários válidos da mesma empresa.',
            ]);
        }

        return $validIds;
    }

    private function validatedProcessId(User $actor, ?int $processId): ?int
    {
        if (! $processId) {
            return null;
        }

        $validProcessId = $this->scopedProcessesQuery($actor)
            ->whereKey($processId)
            ->value('id');

        if (! $validProcessId) {
            throw ValidationException::withMessages([
                'datajud_processo_id' => 'Selecione apenas processos salvos validos da mesma empresa.',
            ]);
        }

        return (int) $validProcessId;
    }
}
