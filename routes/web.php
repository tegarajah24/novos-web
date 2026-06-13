<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('beranda');

require __DIR__.'/auth.php';
require __DIR__.'/internal.php';
require __DIR__.'/customer.php';
