<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Customer
Route::middleware(['auth', 'role:Customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin
Route::middleware(['auth', 'role:Admin,Super Admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Halaman Admin - Works!';
    });
});

// Design
Route::middleware(['auth', 'role:Design,Super Admin'])->group(function () {
    Route::get('/design/dashboard', function () {
        return 'Halaman Design - Works!';
    });
});

// Produksi
Route::middleware(['auth', 'role:Produksi,Super Admin'])->group(function () {
    Route::get('/produksi/dashboard', function () {
        return 'Halaman Produksi - Works!';
    });
});

// Super Admin
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        return 'Halaman Super Admin - Works!';
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
