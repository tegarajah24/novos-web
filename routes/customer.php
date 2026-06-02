<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Customer'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
