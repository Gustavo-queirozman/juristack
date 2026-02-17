<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    public function show($id)
    {
        // find() já retorna 1 registro ou null (não existe -> null)
        // melhor usar findOrFail pra dar 404 automático
        $customer = Customer::findOrFail($id);

        // o correto é passar o array/compact como 2º parâmetro do view()
        return view('customer.show', compact('customer'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        // Corrigido: Cutomer -> Customer
        Customer::create($request->all());

        // normalmente redireciona depois de salvar
        return redirect()->route('customer.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customer.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customer.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

public function uploadFiles(Request $request)
{
    $request->validate([
        'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $customer = Auth::guard('customer')->user(); // cliente que está logado

    $storedPath = $request->file('file')->store('customers', 'public');

    CustomerFile::create([
        'customer_id'   => $customer->id,
        'path'          => $storedPath,
        'original_name' => $request->file('file')->getClientOriginalName(),
        'mime'          => $request->file('file')->getMimeType(),
        'size'          => $request->file('file')->getSize(),
    ]);

    return back()->with('success', 'Arquivo enviado e vinculado ao seu usuário!');
}
}
