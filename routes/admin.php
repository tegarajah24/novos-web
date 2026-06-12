<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Internal\LaporanController;

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::get('/admin/dashboard', function () {
        return view('internal.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/summary', function () {
        return view('internal.summary');
    })->name('admin.summary');

    Route::get('/admin/daftar-pesanan', function () {
        return view('internal.daftar-pesanan');
    })->name('admin.daftar-pesanan');

    Route::get('/admin/design', function () {
        return view('internal.design');
    })->name('admin.design');

    Route::get('/admin/produksi', function () {
        return view('internal.produksi');
    })->name('admin.produksi');

    Route::get('/admin/stress-test', function () {
        return view('internal.stress-test');
    })->name('admin.stress-test');

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');

    Route::get('/admin/kelola-produk', function () {
        return view('internal.kelola-produk');
    })->name('admin.kelola-produk');

    Route::get('/admin/kelola-pengguna', function () {
        return view('internal.kelola-pengguna');
    })->name('admin.kelola-pengguna');

    // Super Admin routes
    Route::get('/superadmin/dashboard', function () {
        return view('internal.dashboard');
    })->name('superadmin.dashboard');

    // Manager routes
    Route::get('/manager/dashboard', function () {
        return view('internal.dashboard');
    })->name('manager.dashboard');
});
