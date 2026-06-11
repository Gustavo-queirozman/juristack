<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function listDocuments(Request $request)
    {
        $actor = $request->user();
        $query = $this->scopedDocumentsQuery($actor)->with(['template', 'customer']);

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

        $templates = DocumentTemplate::orderBy('type')->orderBy('title')->take(8)->get();
        $allTemplates = DocumentTemplate::orderBy('type')->orderBy('title')->get();
        $customers = $this->scopedCustomersQuery($actor)
            ->orderBy('name')
            ->get(['id', 'name', 'cnp', 'email', 'street', 'number', 'neighborhood', 'city', 'state', 'zip_code', 'profession', 'marital_status', 'rg']);

        return view('documents.index', [
            'templates' => $templates,
            'allTemplates' => $allTemplates,
            'documents' => $documents,
            'customers' => $customers,
        ]);
    }

    public function showDocument(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id)->load(['template', 'customer']);

        if ($request->expectsJson()) {
            return response()->json($document);
        }

        return view('documents.show', ['document' => $document]);
    }

    public function download(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id);

        if (empty($document->document_link)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $path = preg_replace('#^/storage/#', '', parse_url($document->document_link, PHP_URL_PATH));
        if (! Storage::disk('public')->exists($path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document->title) . '.pdf';
        return Storage::disk('public')->download($path, $safeName, ['Content-Type' => 'application/pdf']);
    }

    public function downloadOwn(Request $request, int $id)
    {
        $user = $request->user();
        abort_unless($user && $user->isClient(), 403);

        return $this->download($request, $id);
    }

    public function destroyDocument(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id);
        $document->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Document deleted successfully.']);
        }

        return redirect()->route('documents.index')->with('success', 'Documento excluído.');
    }

    public function createFromTemplate(Request $request)
    {
        if (! $request->filled('template_id')) {
            return redirect()->route('documents.index')->with('error', 'Selecione um modelo.');
        }

        $template = DocumentTemplate::findOrFail($request->integer('template_id'));
        $customers = $this->scopedCustomersQuery($request->user())->orderBy('name')->get(['id', 'name']);

        return view('documents.create-from-template', [
            'template' => $template,
            'customers' => $customers,
        ]);
    }

    public function createDocument(Request $request)
    {
        $actor = $request->user();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(Document::TYPES))],
            'document_template_id' => ['required', 'exists:document_templates,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'document_link' => ['nullable', 'url', 'max:2048'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'form_link' => ['nullable', 'url', 'max:2048'],
        ]);

        $customer = $this->resolveCustomer($actor, $validated['customer_id'] ?? null);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('documents', 'public');
            $validated['document_link'] = Storage::disk('public')->url($path);
        }

        $document = Document::create([
            'enterprise_id' => $customer?->enterprise_id ?? $actor->enterprise_id,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'document_link' => $validated['document_link'] ?? null,
            'form_link' => $validated['form_link'] ?? null,
            'document_template_id' => $validated['document_template_id'],
            'customer_id' => $customer?->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Document created successfully.',
                'data' => $document->load('template'),
            ], 201);
        }

        return redirect()->route('documents.index')->with('success', 'Documento criado com sucesso.');
    }

    public function updateDocument(Request $request, int $id)
    {
        $actor = $request->user();
        $document = $this->scopedDocument($actor, $id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type' => ['sometimes', 'required', Rule::in(array_keys(Document::TYPES))],
            'document_template_id' => ['sometimes', 'required', 'exists:document_templates,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'document_link' => ['nullable', 'url', 'max:2048'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'form_link' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('documents', 'public');
            $validated['document_link'] = Storage::disk('public')->url($path);
        }

        if (array_key_exists('customer_id', $validated)) {
            $customer = $this->resolveCustomer($actor, $validated['customer_id']);
            $validated['customer_id'] = $customer?->id;
            $validated['enterprise_id'] = $customer?->enterprise_id ?? $actor->enterprise_id;
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

    public function generateDocument(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id)->load(['template', 'customer']);

        $validated = $request->validate([
            'data' => ['nullable', 'array'],
        ]);

        return response()->json([
            'message' => 'Document generation not implemented yet.',
            'document' => $document,
            'merge_data' => $validated['data'] ?? [],
        ], 501);
    }

    public function createForm(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id);

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

    public function showForm(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id);

        return response()->json([
            'document_id' => $document->id,
            'form_link' => $document->form_link,
        ]);
    }

    public function updateForm(Request $request, int $id)
    {
        $document = $this->scopedDocument($request->user(), $id);

        $validated = $request->validate([
            'form_link' => ['nullable', 'url', 'max:2048'],
            'form_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        if ($request->hasFile('form_file')) {
            $path = $request->file('form_file')->store('forms', 'public');
            $validated['form_link'] = Storage::disk('public')->url($path);
        }

        if (! array_key_exists('form_link', $validated)) {
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

    private function scopedDocumentsQuery(User $user): Builder
    {
        $query = Document::query();

        if ($user->isClient()) {
            return $query->where('customer_id', $user->customerProfile?->id ?? 0);
        }

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function scopedDocument(User $user, int $id): Document
    {
        return $this->scopedDocumentsQuery($user)->findOrFail($id);
    }

    private function scopedCustomersQuery(User $user): Builder
    {
        $query = Customer::query();

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function resolveCustomer(User $user, ?int $customerId): ?Customer
    {
        if (! $customerId) {
            return null;
        }

        return $this->scopedCustomersQuery($user)->findOrFail($customerId);
    }
}
