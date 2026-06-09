<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    // Design routes
    Route::get('/design/dashboard', function () {
        return view('internal.dashboard');
    })->name('design.dashboard');

    Route::get('/design/daftar-pesanan', function () {
        return view('internal.daftar-pesanan');
    })->name('design.daftar-pesanan');

    Route::get('/design/design', function () {
        return view('internal.design');
    })->name('design.design');

    // Produksi routes
    Route::get('/produksi/dashboard', function () {
        return view('internal.dashboard');
    })->name('produksi.dashboard');

    Route::get('/produksi/daftar-pesanan', function () {
        return view('internal.daftar-pesanan');
    })->name('produksi.daftar-pesanan');

    Route::get('/produksi/produksi', function () {
        return view('internal.produksi');
    })->name('produksi.produksi');
});
