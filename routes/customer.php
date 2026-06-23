<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\TrackingController;
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\NotificationController;

// Public routes
Route::get('/tentang-kami', function () {
    return view('customer.tentang-kami');
})->name('tentang');

Route::get('/katalog', [ProductController::class, 'index'])->name('katalog');

Route::get('/pesan', function () {
        $produk = request('produk');
        $kategori = request('kategori');
        $harga = request('harga');
        $gambar = request('gambar');

        $produkData = $produk ? compact('produk', 'kategori', 'harga', 'gambar') : null;

        return view('customer.pemesanan', compact('produkData'));
    })->name('pemesanan');

// Public routes (Midtrans callback)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::post('/pesan', [OrderController::class, 'store'])->name('pesan.store');
    Route::post('/payment/approve/{order:order_number}', [PaymentController::class, 'approveAndPay'])->name('payment.approve');
    Route::post('/payment/snap/{order:order_number}', [PaymentController::class, 'snapToken'])->name('payment.snap');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/{id}/acc', [TrackingController::class, 'accDesign'])->name('tracking.acc');
    Route::post('/tracking/{id}/revision', [TrackingController::class, 'revision'])->name('tracking.revision');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');

    Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');
    Route::get('/notifikasi/unread-count', [NotificationController::class, 'countUnread'])->name('notifikasi.unread-count');
    Route::get('/notifikasi/recent', [NotificationController::class, 'recentJson'])->name('notifikasi.recent');

});
