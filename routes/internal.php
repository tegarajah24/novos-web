<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\LaporanController;
use App\Http\Controllers\Internal\DashboardController;
use App\Http\Controllers\Internal\OrderController;
use App\Http\Controllers\Internal\DesignController;
use App\Http\Controllers\Internal\ProductionController;
use App\Http\Controllers\Internal\ProductController;
use App\Http\Controllers\Internal\UserController;
use App\Http\Controllers\Internal\ChatController;
use App\Http\Controllers\Internal\DailyMentalCheckController;

Route::prefix('staf')
    ->middleware(['auth', 'role:Super Admin,Manager,Admin,Design,Produksi'])
    ->name('staf.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary', [DashboardController::class, 'summary'])->name('summary');

        Route::get('/daftar-pesanan', [OrderController::class, 'index'])->name('daftar-pesanan');
        Route::get('/detail-pesanan/{order:order_number}', [OrderController::class, 'show'])->name('detail-pesanan');
        Route::post('/validasi-pesanan/{order:order_number}', [OrderController::class, 'validateOrder'])->name('validasi-pesanan');

        Route::get('/design', [DesignController::class, 'index'])->name('design');
        Route::get('/produksi', [ProductionController::class, 'index'])->name('produksi');

        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('kelola-produk');
        Route::get('/kelola-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');
        Route::post('/kelola-pengguna', [UserController::class, 'store'])->name('kelola-pengguna.store');
        Route::put('/kelola-pengguna/{user}', [UserController::class, 'update'])->name('kelola-pengguna.update');
        Route::delete('/kelola-pengguna/{user}', [UserController::class, 'destroy'])->name('kelola-pengguna.destroy');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        Route::get('/daily-mental-check', [DailyMentalCheckController::class, 'index'])->name('daily-mental-check');
        Route::get('/daily-mental-check/today', [DailyMentalCheckController::class, 'getToday'])->name('daily-mental-check.today');
        Route::post('/daily-mental-check/daily', [DailyMentalCheckController::class, 'storeDailyCheck'])->name('daily-mental-check.store-daily');
        Route::post('/daily-mental-check/micro', [DailyMentalCheckController::class, 'storeMicroBreak'])->name('daily-mental-check.store-micro');
        Route::get('/daily-mental-check/history', [DailyMentalCheckController::class, 'getHistory'])->name('daily-mental-check.history');
        Route::get('/notifikasi', fn() => view('internal.notifikasi'))->name('notifikasi');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])->name('laporan.csv');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    });
