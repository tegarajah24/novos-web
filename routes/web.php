<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('beranda');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/internal.php';
require __DIR__.'/customer.php';
