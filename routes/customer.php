<?php

use Illuminate\Support\Facades\Route;

Route::prefix('customer')->name('customer.')->group(function () {

    // Public routes
    Route::get('/beranda', function () {
        return view('customer.beranda');
    })->name('beranda');

    Route::get('/tentang', function () {
        return view('customer.tentang');
    })->name('tentang');

    Route::get('/katalog', function () {
        return view('customer.katalog');
    })->name('katalog');

    // Authenticated routes
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', function () {
            $orders = auth()->user()->orders()->latest()->get();
            return view('customer.dashboard', compact('orders'));
        })->name('dashboard');

        Route::get('/pemesanan', function () {
            return view('customer.pemesanan', [
                'produk'  => request('produk'),
                'kategori' => request('kategori'),
                'harga'   => request('harga'),
                'gambar'  => request('gambar'),
            ]);
        })->name('pemesanan');

        Route::get('/tracking', function () {
            return view('customer.tracking');
        })->name('tracking');

        Route::get('/chat', function () {
            return view('customer.chat');
        })->name('chat');

    });

});
