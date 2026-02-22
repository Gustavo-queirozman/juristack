<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    /**
     * List documents (optionally filter by type/template).
     * Returns view for web, JSON for API.
     */
    public function listDocuments(Request $request)
    {
        $query = Document::query()->with(['template', 'customer']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('template_id')) {
            $query->where('document_template_id', $request->integer('template_id'));
        }

        $documents = $query->latest()->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($documents);
        }

        $templates = \App\Models\DocumentTemplate::orderBy('type')->orderBy('title')->take(8)->get();
        $allTemplates = \App\Models\DocumentTemplate::orderBy('type')->orderBy('title')->get();
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name', 'cnp', 'email', 'street', 'number', 'neighborhood', 'city', 'state', 'zip_code', 'profession', 'marital_status', 'rg']);
        return view('documents.index', [
            'templates' => $templates,
            'allTemplates' => $allTemplates,
            'documents' => $documents,
            'customers' => $customers,
        ]);
    }

    /**
     * showDocument
     * Show a single document (JSON for API).
     */
    public function showDocument(Request $request, int $id)
    {
        $document = Document::with(['template', 'customer'])->findOrFail($id);
        if ($request->expectsJson()) {
            return response()->json($document);
        }
        return view('documents.show', ['document' => $document]);
    }

    /**
     * Download the generated document file (PDF).
     */
    public function download(int $id)
    {
        $document = Document::findOrFail($id);
        if (empty($document->document_link)) {
            abort(404, 'Arquivo não encontrado.');
        }
        $path = preg_replace('#^/storage/#', '', parse_url($document->document_link, PHP_URL_PATH));
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document->title) . '.pdf';
        return Storage::disk('public')->download($path, $safeName, ['Content-Type' => 'application/pdf']);
    }

    /**
     * destroyDocument
     * Delete a document.
     */
    public function destroyDocument(Request $request, int $id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Document deleted successfully.']);
        }
        return redirect()->route('documents.index')->with('success', 'Documento excluído.');
    }

    /**
     * Show form to create a document from a template (web).
     */
    public function createFromTemplate(Request $request)
    {
        if (!$request->filled('template_id')) {
            return redirect()->route('documents.index')->with('error', 'Selecione um modelo.');
        }
        $templateId = $request->integer('template_id');
        $template = DocumentTemplate::findOrFail($templateId);
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name']);
        return view('documents.create-from-template', [
            'template' => $template,
            'customers' => $customers,
        ]);
    }

    /**
     * createDocument
     * Create a document record (with link or uploaded file)
     */
    public function createDocument(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type'  => ['required', Rule::in(array_keys(Document::TYPES))],
            'document_template_id' => ['required', 'exists:document_templates,id'],

            'document_link' => ['nullable', 'url', 'max:2048'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],

            'form_link' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('documents', 'public');
            $validated['document_link'] = Storage::disk('public')->url($path);
        }

        $document = Document::create([
            'title' => $validated['title'],
            'type'  => $validated['type'],
            'document_link' => $validated['document_link'] ?? null,
            'form_link' => $validated['form_link'] ?? null,
            'document_template_id' => $validated['document_template_id'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Document created successfully.',
                'data' => $document->load('template'),
            ], 201);
        }
        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    /**
     * updateDocument
     * Update a document record
     */
    public function updateDocument(Request $request, int $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type'  => ['sometimes', 'required', Rule::in(array_keys(Document::TYPES))],
            'document_template_id' => ['sometimes', 'required', 'exists:document_templates,id'],

            'document_link' => ['nullable', 'url', 'max:2048'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],

            'form_link' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('documents', 'public');
            $validated['document_link'] = Storage::disk('public')->url($path);
        }

        $document->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Document updated successfully.',
                'data' => $document->fresh()->load('template'),
            ]);
        }
        return redirect()->route('documents.index')->with('success', 'Documento atualizado.');
    }

    /**
     * generateDocument
     * Generate a document based on its template (stub/placeholder)
     *
     * Here you typically:
     * - fetch template
     * - merge variables
     * - render PDF/DOCX
     * - store result and update document_link
     */
    public function generateDocument(Request $request, int $id)
    {
        $document = Document::with(['template', 'customer'])->findOrFail($id);

        // Example payload validation (variables to merge)
        $validated = $request->validate([
            'data' => ['nullable', 'array'],
        ]);

        // TODO: implement generation (PDF/DOCX) using your preferred library.
        // For now, we just return a message.
        return response()->json([
            'message' => 'Document generation not implemented yet.',
            'document' => $document,
            'merge_data' => $validated['data'] ?? [],
        ], 501);
    }

    /**
     * createForm
     * Attach/create a form link for this document (or upload a form file)
     */
    public function createForm(Request $request, int $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'form_link' => ['nullable', 'url', 'max:2048'],
            'form_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('form_file')) {
            $path = $request->file('form_file')->store('forms', 'public');
            $validated['form_link'] = Storage::disk('public')->url($path);
        }

        if (empty($validated['form_link'])) {
            return response()->json([
                'message' => 'Provide a form_link or upload a form_file.',
            ], 422);
        }

        $document->update(['form_link' => $validated['form_link']]);

        return response()->json([
            'message' => 'Form created/attached successfully.',
            'data' => $document->fresh()->load('template'),
        ], 201);
    }

    /**
     * showForm
     * View the form link for a document
     */
    public function showForm(int $id)
    {
        $document = Document::findOrFail($id);

        return response()->json([
            'document_id' => $document->id,
            'form_link' => $document->form_link,
        ]);
    }

    /**
     * updateForm
     * Update the form link (or replace uploaded form)
     */
    public function updateForm(Request $request, int $id)
    {
        $document = Document::findOrFail($id);

        $validated = $request->validate([
            'form_link' => ['nullable', 'url', 'max:2048'],
            'form_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('form_file')) {
            $path = $request->file('form_file')->store('forms', 'public');
            $validated['form_link'] = Storage::disk('public')->url($path);
        }

        if (!array_key_exists('form_link', $validated)) {
            return response()->json([
                'message' => 'Nothing to update.',
            ], 422);
        }

        $document->update(['form_link' => $validated['form_link']]);

        return response()->json([
            'message' => 'Form updated successfully.',
            'data' => $document->fresh()->load('template'),
        ]);
    }
}
