<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThesisController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    $stats = [];
    $recentTheses = [];

    if ($user->isStudent()) {
        $stats = [
            'total_theses' => $user->thesesAsStudent()->count(),
            'draft' => $user->thesesAsStudent()->where('status', 'draft')->count(),
            'pending' => $user->thesesAsStudent()->where('status', 'pending_approval')->count(),
            'approved' => $user->thesesAsStudent()->where('status', 'approved')->count(),
        ];
        $recentTheses = $user->thesesAsStudent()->with(['supervisor'])->latest()->take(5)->get();
    } elseif ($user->isSupervisor()) {
        $stats = [
            'total_students' => $user->thesesAsSupervisor()->distinct('student_id')->count('student_id'),
            'pending_approval' => $user->thesesAsSupervisor()->where('status', 'pending_approval')->count(),
            'in_progress' => $user->thesesAsSupervisor()->where('status', 'approved')->count(),
            'completed' => $user->thesesAsSupervisor()->whereIn('status', ['accepted', 'defended'])->count(),
        ];
        $recentTheses = $user->thesesAsSupervisor()->with(['student'])->latest()->take(5)->get();
    } elseif ($user->isAdmin()) {
        $stats = [
            'total_theses' => \App\Models\Thesis::count(),
            'total_users' => \App\Models\User::count(),
            'pending_approval' => \App\Models\Thesis::where('status', 'pending_approval')->count(),
            'this_month' => \App\Models\Thesis::whereMonth('created_at', now()->month)->count(),
        ];
        $recentTheses = \App\Models\Thesis::with(['student', 'supervisor'])->latest()->take(5)->get();
    }

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'recentTheses' => $recentTheses,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Thesis routes
    Route::resource('theses', ThesisController::class);
    
    // Thesis action routes
    Route::post('theses/{thesis}/submit', [ThesisController::class, 'submit'])->name('theses.submit');
    Route::post('theses/{thesis}/approve', [ThesisController::class, 'approve'])->name('theses.approve');
    Route::post('theses/{thesis}/reject', [ThesisController::class, 'reject'])->name('theses.reject');
    Route::post('theses/{thesis}/return-for-corrections', [ThesisController::class, 'returnForCorrections'])->name('theses.returnForCorrections');
});

// Routes with role checking (examples)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Admin panel - accessible only to admins
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Admin Panel - accessible only to admins'
        ]);
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:supervisor'])->group(function () {
    // Supervisor panel - accessible only to supervisors
    Route::get('/supervisor/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Supervisor Panel - accessible only to supervisors'
        ]);
    })->name('supervisor.dashboard');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    // Student panel - accessible only to students
    Route::get('/student/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Student Panel - accessible only to students'
        ]);
    })->name('student.dashboard');
});

require __DIR__.'/auth.php';
