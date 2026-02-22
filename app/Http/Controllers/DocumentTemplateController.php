<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        return response()->json(
            DocumentTemplate::latest()->paginate(15)
        );
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
            'type'  => ['required', Rule::in(DocumentTemplate::TYPES)],
            'date'  => ['required', 'date'],
        ]);

        $template = DocumentTemplate::create($validated);

        return response()->json([
            'message' => 'Template created successfully.',
            'data' => $template,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'type'  => ['sometimes', 'required', Rule::in(DocumentTemplate::TYPES)],
            'date'  => ['sometimes', 'required', 'date'],
        ]);

        $template->update($validated);

        return response()->json([
            'message' => 'Template updated successfully.',
            'data' => $template->fresh(),
        ]);
    }

    public function destroy(int $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }
}
