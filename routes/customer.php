<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\TrackingController;
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\Customer\ProfileController;

// Public routes
Route::get('/tentang-kami', function () {
    return view('customer.tentang-kami');
})->name('tentang');

Route::get('/katalog', [ProductController::class, 'index'])->name('katalog');

Route::get('/pesan', function () {
        return view('customer.pemesanan', [
            'produk'  => request('produk'),
            'kategori' => request('kategori'),
            'harga'   => request('harga'),
            'gambar'  => request('gambar'),
        ]);
    })->name('pemesanan');

// Public routes (Midtrans callback)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::post('/pesan', [OrderController::class, 'store'])->name('pesan.store');
    Route::post('/payment/snap/{order}', [PaymentController::class, 'snapToken'])->name('payment.snap');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::get('/tracking/search', [TrackingController::class, 'search'])->name('tracking.search');
    Route::post('/tracking/{id}/acc', [TrackingController::class, 'accDesign'])->name('tracking.acc');
    Route::post('/tracking/{id}/revision', [TrackingController::class, 'revision'])->name('tracking.revision');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');

    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});
