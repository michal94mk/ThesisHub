<?php

use App\Http\Controllers\ProfileController;
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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route'y z sprawdzaniem ról (przykłady)
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Panel admina - dostępny tylko dla admin
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Panel Administratora - dostępny tylko dla admina'
        ]);
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:supervisor'])->group(function () {
    // Panel promotora - dostępny tylko dla supervisor
    Route::get('/supervisor/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Panel Promotora - dostępny tylko dla promotorów'
        ]);
    })->name('supervisor.dashboard');
});

Route::middleware(['auth', 'role:student'])->group(function () {
    // Panel studenta - dostępny tylko dla student
    Route::get('/student/dashboard', function () {
        return Inertia::render('Dashboard', [
            'message' => 'Panel Studenta - dostępny tylko dla studentów'
        ]);
    })->name('student.dashboard');
});

require __DIR__.'/auth.php';
