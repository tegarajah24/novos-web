<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('internal.dashboard');
    });

    Route::get('/superadmin/dashboard', function () {
        return view('internal.dashboard');
    });

    Route::get('/manager/dashboard', function () {
        return view('internal.dashboard');
    });

    Route::get('/admin/kelola-pengguna', function () {
        return view('internal.kelola-pengguna');
    });
});
