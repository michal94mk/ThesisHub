<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Thesis;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Upload a new document for a thesis.
     */
    public function store(Request $request, Thesis $thesis)
    {
        // Check if user can upload documents for this thesis
        $this->authorize('update', $thesis);

        $request->validate([
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,txt,zip,rar', // 50MB max
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        
        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store file in thesis-specific directory
        $path = $file->storeAs(
            'theses/' . $thesis->id . '/documents',
            $filename,
            'local'
        );

        // Create document record
        $document = Document::create([
            'thesis_id' => $thesis->id,
            'uploaded_by' => auth()->id(),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'description' => $request->description,
            'version' => $thesis->documents()->count() + 1,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        // Check if user can view this thesis
        $this->authorize('view', $document->thesis);

        if (!$document->fileExists()) {
            return back()->withErrors(['file' => 'File not found.']);
        }

        return Storage::disk('local')->download(
            $document->path,
            $document->original_name
        );
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        // Check if user can update this thesis
        $this->authorize('update', $document->thesis);

        // Delete file from storage
        $document->deleteFile();

        // Delete database record
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
