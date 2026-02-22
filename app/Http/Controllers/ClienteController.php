<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query()
            ->where('user_id', $request->user()->id)
            ->withCount('enderecos');

        $busca = $request->get('busca');
        if ($busca !== null && $busca !== '') {
            $term = '%' . trim($busca) . '%';
            $digits = preg_replace('/\D/', '', $busca);
            $query->where(function ($q) use ($term, $digits) {
                $q->where('nome', 'like', $term)
                    ->orWhere('cpf', 'like', $digits . '%')
                    ->orWhere('cnpj', 'like', $digits . '%');
            });
        }

        $clientes = $query->latest()->paginate(15)->withQueryString();

        return view('clientes.index', compact('clientes', 'busca'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $cliente = $request->user()->clientes()->create($request->only([
            'type', 'nome', 'cpf', 'cnpj', 'email', 'telefone',
        ]));

        $this->syncEndereco($cliente, $request);

        return redirect()->route('users.show', $cliente)
            ->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function show(Cliente $cliente)
    {
        $this->authorizeUser($cliente);
        $cliente->load('enderecos');

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $this->authorizeUser($cliente);
        $cliente->load('enderecos');

        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        $this->authorizeUser($cliente);

        $cliente->update($request->only([
            'type', 'nome', 'cpf', 'cnpj', 'email', 'telefone',
        ]));

        $this->syncEndereco($cliente, $request);

        return redirect()->route('users.show', $cliente)
            ->with('status', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Request $request, Cliente $cliente)
    {
        $this->authorizeUser($cliente);
        $cliente->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('users.index')
            ->with('status', 'Cliente excluído com sucesso.');
    }

    protected function authorizeUser(Cliente $cliente): void
    {
        if ($cliente->user_id !== request()->user()->id) {
            abort(403);
        }
    }

    protected function syncEndereco(Cliente $cliente, Request $request): void
    {
        $logradouro = $request->input('logradouro');
        $cidade = $request->input('cidade');
        $estado = $request->input('estado');
        $cep = $request->input('cep');

        $cliente->enderecos()->delete();
        if ($logradouro && $cidade && $estado && $cep) {
            $cliente->enderecos()->create([
                'logradouro' => $logradouro,
                'numero' => $request->input('numero'),
                'complemento' => $request->input('complemento'),
                'bairro' => $request->input('bairro'),
                'cidade' => $cidade,
                'estado' => strtoupper($estado),
                'cep' => preg_replace('/\D/', '', $cep),
            ]);
        }
    }
}
