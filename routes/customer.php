<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\OrderController;

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

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::post('/pesan', [OrderController::class, 'store'])->name('pesan.store');

    Route::get('/tracking', function () {
        return view('customer.tracking');
    })->name('tracking');

    Route::get('/chat', function () {
        return view('customer.chat');
    })->name('chat');

    Route::post('/chat/send', [App\Http\Controllers\Customer\ChatController::class, 'store'])->name('chat.send');

});
