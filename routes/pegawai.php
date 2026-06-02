<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Design,Super Admin'])->group(function () {
    Route::get('/design/dashboard', function () {
        return 'Halaman Design - Works!';
    });
});

Route::middleware(['auth', 'role:Produksi,Super Admin'])->group(function () {
    Route::get('/produksi/dashboard', function () {
        return 'Halaman Produksi - Works!';
    });
});
