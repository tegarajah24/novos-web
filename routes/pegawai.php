<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/design/dashboard', function () {
        return view('internal.dashboard');
    });

    Route::get('/produksi/dashboard', function () {
        return view('internal.dashboard');
    });
});
