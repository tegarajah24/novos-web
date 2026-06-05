<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', fn() => view('customer.beranda'));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;
        return match($role) {
            'Super Admin' => redirect('/superadmin/dashboard'),
            'Manager'     => redirect('/manager/dashboard'),
            'Admin'       => redirect('/admin/dashboard'),
            'Design'      => redirect('/design/dashboard'),
            'Produksi'    => redirect('/produksi/dashboard'),
            default       => redirect('/customer/dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/pegawai.php';
require __DIR__.'/customer.php';
