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
use App\Http\Controllers\Internal\NotificationController;
use App\Http\Controllers\Internal\CategoryController;
use App\Http\Controllers\Internal\SettingController;

Route::prefix('staf')
    ->middleware(['auth', 'role:Super Admin,Manager,Admin,Design,Produksi'])
    ->name('staf.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary', [DashboardController::class, 'summary'])->name('summary');

        Route::get('/daftar-pesanan', [OrderController::class, 'index'])->name('daftar-pesanan');
        Route::get('/detail-pesanan/{order:order_number}', [OrderController::class, 'show'])->name('detail-pesanan');
        Route::post('/validasi-pesanan/{order:order_number}', [OrderController::class, 'validateOrder'])->name('validasi-pesanan');
        Route::patch('/pesanan/{order:order_number}/assign', [OrderController::class, 'assign'])->name('pesanan.assign');
        Route::post('/pesanan/{order:order_number}/update-status', [OrderController::class, 'updateStatus'])->name('pesanan.update-status');
        Route::get('/pesanan/{order:order_number}/allowed-statuses', [OrderController::class, 'allowedStatuses'])->name('pesanan.allowed-statuses');

        Route::get('/design', [DesignController::class, 'index'])->name('design');
        Route::post('/design/update/{order:order_number}', [DesignController::class, 'updateStatus'])->name('design.update');

        Route::get('/produksi', [ProductionController::class, 'index'])->name('produksi');
        Route::post('/produksi/update/{order:order_number}', [ProductionController::class, 'updateStatus'])->name('produksi.update');

        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('kelola-produk');
        Route::post('/kelola-produk', [ProductController::class, 'store'])->name('kelola-produk.store');
        Route::put('/kelola-produk/{product}', [ProductController::class, 'update'])->name('kelola-produk.update');
        Route::delete('/kelola-produk/{product}', [ProductController::class, 'destroy'])->name('kelola-produk.destroy');
        Route::patch('/kelola-produk/{product}/featured', [ProductController::class, 'toggleFeatured'])->name('kelola-produk.featured');
        Route::get('/kelola-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');
        Route::post('/kelola-pengguna', [UserController::class, 'store'])->name('kelola-pengguna.store');
        Route::put('/kelola-pengguna/{user}', [UserController::class, 'update'])->name('kelola-pengguna.update');
        Route::delete('/kelola-pengguna/{user}', [UserController::class, 'destroy'])->name('kelola-pengguna.destroy');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
        Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
        Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');
        Route::get('/daily-mental-check', [DailyMentalCheckController::class, 'index'])->name('daily-mental-check');
        Route::get('/daily-mental-check/today', [DailyMentalCheckController::class, 'getToday'])->name('daily-mental-check.today');
        Route::post('/daily-mental-check/daily', [DailyMentalCheckController::class, 'storeDailyCheck'])->name('daily-mental-check.store-daily');
        Route::post('/daily-mental-check/micro', [DailyMentalCheckController::class, 'storeMicroBreak'])->name('daily-mental-check.store-micro');
        Route::get('/daily-mental-check/history', [DailyMentalCheckController::class, 'getHistory'])->name('daily-mental-check.history');
        Route::get('/daily-mental-check/report', [DailyMentalCheckController::class, 'getReport'])->name('daily-mental-check.report');
        Route::get('/notifikasi', [NotificationController::class, 'viewPage'])->name('notifikasi');
        Route::get('/notifikasi/data', [NotificationController::class, 'index'])->name('notifikasi.data');
        Route::get('/notifikasi/preview', [NotificationController::class, 'preview'])->name('notifikasi.preview');
        Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])->name('laporan.csv');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
        Route::get('/kategori/data', [CategoryController::class, 'index'])->name('kategori.data');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
    });
