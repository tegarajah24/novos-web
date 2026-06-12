<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProfileController;

Route::get('/', fn() => view('customer.beranda'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;
        return match($role) {
            'Super Admin', 'Manager', 'Admin', 'Design', 'Produksi' => redirect('/internal/dashboard'),
            default => redirect('/customer/dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', function () {
        $role = auth()->user()->role?->name;
        if (in_array($role, ['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi'])) {
            return redirect()->route('internal.chat');
        }
        return redirect()->route('customer.chat');
    });
});

require __DIR__.'/auth.php';
require __DIR__.'/internal.php';
require __DIR__.'/customer.php';
