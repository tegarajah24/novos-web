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
use App\Http\Controllers\Internal\CategoryAttributeController;
use App\Http\Controllers\Internal\SettingController;
use App\Http\Controllers\InvoiceController;

Route::prefix('staf')
    ->middleware(['auth', 'role:Super Admin,Manager,Admin,Design,Produksi'])
    ->name('staf.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('/summary', [DashboardController::class, 'summary'])->name('summary');

        Route::get('/daftar-pesanan', [OrderController::class, 'index'])->name('daftar-pesanan');
Route::get('/daftar-pesanan/export', [OrderController::class, 'exportDaftarPesanan'])->name('daftar-pesanan.export');
        Route::get('/detail-pesanan/{order:order_number}', [OrderController::class, 'show'])->name('detail-pesanan');
        Route::patch('/pesanan/{order:order_number}/assign', [OrderController::class, 'assign'])->name('pesanan.assign');
        Route::post('/pesanan/{order:order_number}/update-status', [OrderController::class, 'updateStatus'])->name('pesanan.update-status');
        Route::post('/pesanan/{order:order_number}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('pesanan.payment-status');
        Route::get('/pesanan/{order:order_number}/allowed-statuses', [OrderController::class, 'allowedStatuses'])->name('pesanan.allowed-statuses');
        Route::get('/pesanan/{order:order_number}/export-csv', [OrderController::class, 'exportCsv'])->name('pesanan.export-csv');
        Route::get('/pesanan/{order:order_number}/export-excel', [OrderController::class, 'exportExcel'])->name('pesanan.export-excel');
        Route::get('/pesanan/{order:order_number}/export-spk', [OrderController::class, 'exportSpk'])->name('pesanan.export-spk');
        Route::post('/pesanan/{order:order_number}/upload-spk-file', [OrderController::class, 'uploadSpkFile'])->name('pesanan.upload-spk-file');
        Route::post('/pesanan/{order:order_number}/delete-spk-file', [OrderController::class, 'deleteSpkFile'])->name('pesanan.delete-spk-file');
        Route::post('/pesanan/{order:order_number}/update-spk-notes', [OrderController::class, 'updateSpkNotes'])->name('pesanan.update-spk-notes');
        Route::patch('/pesanan/{order:order_number}/design-request', [OrderController::class, 'updateDesignRequest'])->name('pesanan.update-design-request');
        Route::put('/pesanan/{order:order_number}/items', [OrderController::class, 'updateItems'])->name('pesanan.update-items');

        Route::get('/pesanan/{orderNumber}/invoice', [InvoiceController::class, 'showInternal'])->name('pesanan.invoice');
        Route::get('/pesanan/{orderNumber}/invoice/download', [InvoiceController::class, 'downloadInternal'])->name('pesanan.invoice.download');
        Route::post('/pesanan/{orderNumber}/invoice/dp', [InvoiceController::class, 'saveDp'])->name('pesanan.invoice.dp');


        Route::get('/design', [DesignController::class, 'index'])->name('design');
        Route::post('/design/update/{order:order_number}', [DesignController::class, 'updateStatus'])->name('design.update');

        Route::get('/produksi', [ProductionController::class, 'index'])->name('produksi');
        Route::post('/produksi/update/{order:order_number}', [ProductionController::class, 'updateStatus'])->name('produksi.update');

        Route::get('/kelola-produk', [ProductController::class, 'index'])->name('kelola-produk');
        Route::post('/kelola-produk', [ProductController::class, 'store'])->name('kelola-produk.store');
        Route::put('/kelola-produk/{product}', [ProductController::class, 'update'])->name('kelola-produk.update');
        Route::delete('/kelola-produk/{product}', [ProductController::class, 'destroy'])->name('kelola-produk.destroy');
        Route::get('/kelola-produk/referensi', [ProductController::class, 'getReferensi'])->name('kelola-produk.get-referensi');
        Route::post('/kelola-produk/referensi', [ProductController::class, 'updateReferensi'])->name('kelola-produk.update-referensi');
        Route::get('/kelola-pengguna', [UserController::class, 'index'])->name('kelola-pengguna');
        Route::post('/kelola-pengguna', [UserController::class, 'store'])->name('kelola-pengguna.store');
        Route::put('/kelola-pengguna/{user}', [UserController::class, 'update'])->name('kelola-pengguna.update');
        Route::delete('/kelola-pengguna/{user}', [UserController::class, 'destroy'])->name('kelola-pengguna.destroy');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat');
        Route::post('/chat/heartbeat', [ChatController::class, 'heartbeat'])->name('chat.heartbeat');
        Route::get('/chat/poll', [ChatController::class, 'poll'])->name('chat.poll');
        Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
        Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
        Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');
        Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy');
        Route::delete('/chat/message/{chatMessage}', [ChatController::class, 'destroyMessage'])->name('chat.message.destroy');
        Route::get('/chat/download/{chatMessage}', [ChatController::class, 'download'])->name('chat.download');
        Route::get('/daily-mental-check', [DailyMentalCheckController::class, 'index'])->name('daily-mental-check');
        Route::get('/daily-mental-check/today', [DailyMentalCheckController::class, 'getToday'])->name('daily-mental-check.today');
        Route::post('/daily-mental-check/daily', [DailyMentalCheckController::class, 'storeDailyCheck'])->name('daily-mental-check.store-daily');
        Route::post('/daily-mental-check/micro', [DailyMentalCheckController::class, 'storeMicroBreak'])->name('daily-mental-check.store-micro');
        Route::get('/daily-mental-check/history', [DailyMentalCheckController::class, 'getHistory'])->name('daily-mental-check.history');
        Route::get('/daily-mental-check/report', [DailyMentalCheckController::class, 'getReport'])->name('daily-mental-check.report');
        Route::get('/daily-mental-check/export/csv', [DailyMentalCheckController::class, 'exportReportCsv'])->name('daily-mental-check.export-csv');
        Route::get('/daily-mental-check/export/excel', [DailyMentalCheckController::class, 'exportReportExcel'])->name('daily-mental-check.export-excel');
        Route::patch('/daily-mental-check/reminder-times', [DailyMentalCheckController::class, 'updateReminderTimes'])->name('daily-mental-check.reminder-times')->middleware('role:Super Admin');
        Route::get('/notifikasi', [NotificationController::class, 'viewPage'])->name('notifikasi');
        Route::get('/notifikasi/data', [NotificationController::class, 'index'])->name('notifikasi.data');
        Route::get('/notifikasi/preview', [NotificationController::class, 'preview'])->name('notifikasi.preview');
        Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
        Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/data', [LaporanController::class, 'getData'])->name('laporan.data');
        Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])->name('laporan.csv');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

        Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori');
        Route::get('/kategori/data', [CategoryController::class, 'getData'])->name('kategori.data');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
        Route::put('/kategori/{category}', [CategoryController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('kategori.destroy');
        // Route atribut dinamis per kategori
        Route::get('/kategori/{category}/attributes', [CategoryAttributeController::class, 'getSchema'])->name('kategori.attributes');
        Route::put('/kategori/{category}/attributes', [CategoryAttributeController::class, 'updateSchema'])->name('kategori.attributes.update');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
        Route::post('/pengaturan/upload-hero', [SettingController::class, 'uploadHero'])->name('pengaturan.upload-hero');
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
