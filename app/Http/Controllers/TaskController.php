<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // LISTAR TODAS
    public function index()
    {
        $tasks = Task::with('users')->latest()->get();
        return view('tasks.index', compact('tasks'));
    }

    // FORMULÁRIO DE CRIAÇÃO
    public function create()
    {
        $users = User::all();
        return view('tasks.create', compact('users'));
    }

    // SALVAR
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'users' => 'nullable|array'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // associar usuários (many-to-many)
        if ($request->users) {
            $task->users()->sync($request->users);
        }

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa criada com sucesso!');
    }

    // MOSTRAR UMA
    public function show(Task $task)
    {
        $task->load('users');
        return view('tasks.show', compact('task'));
    }

    // FORMULÁRIO DE EDIÇÃO
    public function edit(Task $task)
    {
        $users = User::all();
        $task->load('users');
        return view('tasks.edit', compact('task', 'users'));
    }

    // ATUALIZAR
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'users' => 'nullable|array'
        ]);

        $task->update($request->only(['title', 'description', 'status']));

        if ($request->users) {
            $task->users()->sync($request->users);
        }

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa atualizada com sucesso!');
    }

    // DELETAR
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
                         ->with('success', 'Tarefa removida com sucesso!');
    }

    // LISTAR USUÁRIOS DA TAREFA
    public function users(Task $task)
    {
        $users = $task->users;
        return view('tasks.users', compact('task', 'users'));
    }
}