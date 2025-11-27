<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ThesisController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of theses based on user role.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Thesis::with(['student', 'supervisor']);
        
        // Filter based on user role
        if ($user->isStudent()) {
            $query->forStudent($user->id);
        } elseif ($user->isSupervisor()) {
            $query->forSupervisor($user->id);
        }
        // Admin sees all theses
        
        $theses = $query->latest()->paginate(15);
        
        return Inertia::render('Theses/Index', [
            'theses' => $theses,
        ]);
    }

    /**
     * Show the form for creating a new thesis.
     */
    public function create()
    {
        // Get all supervisors for selection
        $supervisors = User::where('role', 'supervisor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        
        return Inertia::render('Theses/Create', [
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Store a newly created thesis in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:bachelor,master',
            'supervisor_id' => 'required|exists:users,id',
            'field_of_study' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
            'outline' => 'nullable|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string',
            'academic_year' => 'nullable|string|max:9',
        ]);
        
        // Verify supervisor has correct role
        $supervisor = User::findOrFail($validated['supervisor_id']);
        if (!$supervisor->isSupervisor()) {
            return back()->withErrors(['supervisor_id' => 'Selected user is not a supervisor.']);
        }
        
        $thesis = Thesis::create([
            ...$validated,
            'student_id' => $request->user()->id,
            'status' => Thesis::STATUS_DRAFT,
        ]);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis created successfully.');
    }

    /**
     * Display the specified thesis.
     */
    public function show(Thesis $thesis)
    {
        // Load relationships
        $thesis->load(['student', 'supervisor', 'documents.uploader']);
        
        // Check authorization
        $this->authorize('view', $thesis);
        
        return Inertia::render('Theses/Show', [
            'thesis' => $thesis,
        ]);
    }

    /**
     * Show the form for editing the specified thesis.
     */
    public function edit(Thesis $thesis)
    {
        $this->authorize('update', $thesis);
        
        $supervisors = User::where('role', 'supervisor')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
        
        return Inertia::render('Theses/Edit', [
            'thesis' => $thesis,
            'supervisors' => $supervisors,
        ]);
    }

    /**
     * Update the specified thesis in storage.
     */
    public function update(Request $request, Thesis $thesis)
    {
        $this->authorize('update', $thesis);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:bachelor,master',
            'supervisor_id' => 'required|exists:users,id',
            'field_of_study' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'abstract' => 'nullable|string',
            'outline' => 'nullable|string',
            'keywords' => 'nullable|array',
            'keywords.*' => 'string',
            'academic_year' => 'nullable|string|max:9',
        ]);
        
        $thesis->update($validated);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis updated successfully.');
    }

    /**
     * Remove the specified thesis from storage.
     */
    public function destroy(Thesis $thesis)
    {
        $this->authorize('delete', $thesis);
        
        $thesis->delete();
        
        return redirect()->route('theses.index')
            ->with('success', 'Thesis deleted successfully.');
    }

    /**
     * Submit thesis for approval.
     */
    public function submit(Thesis $thesis)
    {
        $this->authorize('update', $thesis);
        
        if (!$thesis->isDraft()) {
            return back()->withErrors(['status' => 'Only draft theses can be submitted.']);
        }
        
        $thesis->submit();
        
        // Notify supervisor
        Notification::create([
            'user_id' => $thesis->supervisor_id,
            'type' => 'thesis_submitted',
            'title' => 'New Thesis Submitted',
            'message' => $thesis->student->name . ' submitted "' . $thesis->title . '" for approval',
            'icon' => '📝',
            'color' => 'yellow',
            'action_url' => route('theses.show', $thesis->id),
            'related_thesis_id' => $thesis->id,
        ]);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis submitted for approval.');
    }

    /**
     * Approve thesis (supervisor only).
     */
    public function approve(Request $request, Thesis $thesis)
    {
        if (!$request->user()->isSupervisor() || $thesis->supervisor_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!$thesis->isPendingApproval()) {
            return back()->withErrors(['status' => 'Only pending theses can be approved.']);
        }
        
        $thesis->approve();
        
        // Notify student
        Notification::create([
            'user_id' => $thesis->student_id,
            'type' => 'thesis_approved',
            'title' => 'Thesis Approved',
            'message' => 'Your thesis "' . $thesis->title . '" has been approved!',
            'icon' => '✅',
            'color' => 'green',
            'action_url' => route('theses.show', $thesis->id),
            'related_thesis_id' => $thesis->id,
        ]);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis approved successfully.');
    }

    /**
     * Reject thesis (supervisor only).
     */
    public function reject(Request $request, Thesis $thesis)
    {
        if (!$request->user()->isSupervisor() || $thesis->supervisor_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);
        
        $thesis->reject($validated['notes']);
        
        // Notify student
        Notification::create([
            'user_id' => $thesis->student_id,
            'type' => 'thesis_rejected',
            'title' => 'Thesis Rejected',
            'message' => 'Your thesis "' . $thesis->title . '" has been rejected. Check supervisor notes.',
            'icon' => '❌',
            'color' => 'red',
            'action_url' => route('theses.show', $thesis->id),
            'related_thesis_id' => $thesis->id,
        ]);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis rejected.');
    }

    /**
     * Return thesis for corrections (supervisor only).
     */
    public function returnForCorrections(Request $request, Thesis $thesis)
    {
        if (!$request->user()->isSupervisor() || $thesis->supervisor_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $validated = $request->validate([
            'notes' => 'required|string',
        ]);
        
        $thesis->returnForCorrections($validated['notes']);
        
        // Notify student
        Notification::create([
            'user_id' => $thesis->student_id,
            'type' => 'thesis_returned',
            'title' => 'Thesis Returned for Corrections',
            'message' => 'Your thesis "' . $thesis->title . '" has been returned. Please review supervisor notes.',
            'icon' => '🔄',
            'color' => 'orange',
            'action_url' => route('theses.show', $thesis->id),
            'related_thesis_id' => $thesis->id,
        ]);
        
        return redirect()->route('theses.show', $thesis)
            ->with('success', 'Thesis returned for corrections.');
    }
}
