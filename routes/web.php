<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\SitemapController;

Route::get('/', [HomeController::class, 'index'])->name('beranda');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

require __DIR__.'/auth.php';
require __DIR__.'/internal.php';
require __DIR__.'/customer.php';
