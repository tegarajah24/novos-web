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


        Route::get('/design', [DesignController::class, 'index'])->name('design');
        Route::get('/produksi', [ProductionController::class, 'index'])->name('produksi');

        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('kelola-produk');
        Route::get('/kelola-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
        Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
        Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');
        Route::get('/daily-mental-check', [DailyMentalCheckController::class, 'index'])->name('daily-mental-check');
        Route::get('/notifikasi', [NotificationController::class, 'viewPage'])->name('notifikasi');
        Route::get('/notifikasi/preview', [NotificationController::class, 'preview'])->name('notifikasi.preview');
        Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/data', [LaporanController::class, 'getData'])->name('laporan.data');
        Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])->name('laporan.csv');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan');
    });

// Poster management — Super Admin only
Route::prefix('staf/daily-mental-check')
    ->middleware(['auth', 'role:Super Admin'])
    ->name('staf.daily-mental-check.')
    ->group(function () {
        Route::get('/posters', [DailyMentalCheckController::class, 'listPosters'])->name('posters');
        Route::post('/posters', [DailyMentalCheckController::class, 'uploadPoster'])->name('posters.upload');
        Route::delete('/posters/{poster}', [DailyMentalCheckController::class, 'deletePoster'])->name('posters.delete');
        Route::patch('/posters/rotation', [DailyMentalCheckController::class, 'updateRotation'])->name('posters.rotation');
    });
