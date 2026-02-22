<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(
                DocumentTemplate::latest()->paginate(15)
            );
        }
        $templates = DocumentTemplate::orderBy('type')->orderBy('title')->paginate(12);
        return view('document-templates.index', [
            'templates' => $templates,
        ]);
    }

    public function create()
    {
        return view('document-templates.create', [
            'types' => DocumentTemplate::TYPES,
        ]);
    }

    public function show(int $id)
    {
        return response()->json(
            DocumentTemplate::with('documents')->findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type'  => ['required', Rule::in(array_keys(DocumentTemplate::TYPES))],
            'description' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'date'  => ['required', 'date'],
        ]);

        $template = DocumentTemplate::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Template created successfully.',
                'data' => $template,
            ], 201);
        }
        return redirect()->route('documents.index')->with('success', 'Modelo criado com sucesso.');
    }

    public function edit(int $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('document-templates.edit', [
            'template' => $template,
            'types' => DocumentTemplate::TYPES,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type'  => ['sometimes', 'required', Rule::in(array_keys(DocumentTemplate::TYPES))],
            'description' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'date'  => ['sometimes', 'required', 'date'],
        ]);

        $template->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Template updated successfully.',
                'data' => $template->fresh(),
            ]);
        }
        return redirect()->route('documents.index')->with('success', 'Modelo atualizado com sucesso.');
    }

    public function destroy(Request $request, int $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Template deleted successfully.']);
        }
        return redirect()->route('documents.index')->with('success', 'Modelo excluído.');
    }

    /**
     * Exibe o formulário para preencher os dados do modelo e gerar o documento.
     * Se customer_id for informado, preenche os campos com os dados do cliente.
     */
    public function showFillForm(Request $request, int $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $placeholders = $template->getPlaceholders();
        if (empty($placeholders)) {
            return redirect()->route('documents.index')
                ->with('error', 'Este modelo não possui campos para preenchimento. Edite o modelo e use placeholders como {{nome}} no conteúdo.');
        }
        $defaultData = [];
        $customer = null;
        if ($request->filled('customer_id')) {
            $customer = \App\Models\Customer::find($request->integer('customer_id'));
            if ($customer) {
                $defaultData = self::customerToPlaceholders($customer);
            }
        }
        return view('document-templates.fill', [
            'template' => $template,
            'placeholders' => $placeholders,
            'defaultData' => $defaultData,
            'customer' => $customer,
        ]);
    }

    /**
     * Mapeia dados do Customer para valores padrão dos placeholders.
     */
    public static function customerToPlaceholders(\App\Models\Customer $customer): array
    {
        $parts = array_filter([
            $customer->street,
            $customer->number,
            $customer->neighborhood,
            $customer->city ? ($customer->city . ($customer->state ? '/' . $customer->state : '')) : null,
            $customer->zip_code,
        ]);
        $endereco = implode(', ', $parts);
        if (empty($endereco)) {
            $endereco = $customer->street ?? '';
        }
        return [
            'nome_outorgante' => $customer->name ?? '',
            'nome_cliente' => $customer->name ?? '',
            'nome_declarante' => $customer->name ?? '',
            'nome_autor' => $customer->name ?? '',
            'cpf' => $customer->cnp ?? '',
            'rg' => $customer->rg ?? '',
            'endereco' => $endereco,
            'endereco_outorgante' => $endereco,
            'endereco_cliente' => $endereco,
            'endereco_completo' => $endereco,
            'profissao' => $customer->profession ?? '',
            'estado_civil' => $customer->marital_status ?? '',
        ];
    }

    /**
     * Gera o documento a partir do modelo preenchido: mescla dados, gera PDF, grava e redireciona para download.
     */
    public function generateDocument(Request $request, int $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $placeholders = $template->getPlaceholders();
        if (empty($placeholders)) {
            return redirect()->route('documents.index')->with('error', 'Modelo sem placeholders.');
        }

        $rules = [];
        foreach ($placeholders as $key) {
            $rules['data.' . $key] = ['required', 'string', 'max:2000'];
        }
        $validated = $request->validate($rules);
        $data = $validated['data'] ?? [];

        if (!empty($data['data']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['data'])) {
            $data['data'] = \Carbon\Carbon::createFromFormat('Y-m-d', $data['data'])->format('d/m/Y');
        }
        if (!empty($data['hora']) && preg_match('/^\d{2}:\d{2}$/', $data['hora'])) {
            $data['hora'] = substr($data['hora'], 0, 5);
        }

        $content = $template->content;
        foreach ($data as $key => $value) {
            $safeValue = e($value);
            $content = str_replace(['{{' . $key . '}}', '{{ ' . $key . ' }}'], $safeValue, $content);
        }

        $isHtml = preg_match('/<[a-z][a-z0-9]*\b/i', $content);
        if ($isHtml) {
            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12pt;line-height:1.5;margin:2cm;color:#000;} p{margin:0.4em 0;} ul,ol{margin:0.4em 0;padding-left:1.5em;} strong{font-weight:bold;} em{font-style:italic;} blockquote{margin:0.5em 0;padding-left:1em;border-left:3px solid #ccc;}</style></head><body>' . $content . '</body></html>';
        } else {
            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12pt;line-height:1.5;margin:2cm;color:#000;} pre{white-space:pre-wrap;font-family:inherit;margin:0;}</style></head><body><pre>' . e($content) . '</pre></body></html>';
        }

        $filename = 'documento-' . $template->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        $path = 'documents/' . $filename;
        Storage::disk('public')->makeDirectory('documents');
        $fullPath = Storage::disk('public')->path('documents/' . $filename);

        Pdf::loadHTML($html)->save($fullPath);

        $documentLink = Storage::disk('public')->url('documents/' . $filename);
        $documentTitle = $template->title . ' - ' . now()->format('d/m/Y');
        if (!empty($data['nome_outorgante'])) {
            $documentTitle = $template->title . ' - ' . $data['nome_outorgante'];
        } elseif (!empty($data['nome_cliente'])) {
            $documentTitle = $template->title . ' - ' . $data['nome_cliente'];
        } elseif (!empty($data['nome_declarante'])) {
            $documentTitle = $template->title . ' - ' . $data['nome_declarante'];
        }

        $customerId = $request->filled('customer_id') && $request->integer('customer_id') > 0
            ? $request->integer('customer_id')
            : null;
        if ($customerId && !\App\Models\Customer::where('id', $customerId)->exists()) {
            $customerId = null;
        }
        $document = Document::create([
            'title' => $documentTitle,
            'type' => $template->type,
            'document_template_id' => $template->id,
            'document_link' => $documentLink,
            'customer_id' => $customerId,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Documento gerado.',
                'data' => $document->load('template'),
                'download_url' => route('documents.download', $document->id),
            ], 201);
        }
        return redirect()->route('documents.show', $document->id)
            ->with('success', 'Documento gerado com sucesso. Use o botão abaixo para baixar.');
    }
}
