<?php

use Illuminate\Support\Facades\Route;

Route::get('/beranda', function () {
    return view('customer.beranda');
})->name('beranda');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});