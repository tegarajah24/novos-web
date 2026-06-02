<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Admin,Manager,Super Admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Halaman Admin - Works!';
    });
});

Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        return 'Halaman Super Admin - Works!';
    });
});

Route::middleware(['auth', 'role:Manager,Super Admin'])->group(function () {
    Route::get('/manager/dashboard', function () {
        return 'Halaman Manager - Works!';
    });
});
