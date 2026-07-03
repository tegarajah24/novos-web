<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\TrackingController;
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Api\SummaryController;

// Public routes
Route::get('/tentang-kami', [HomeController::class, 'tentang'])->name('tentang');

Route::get('/katalog', [ProductController::class, 'index'])->name('katalog');

Route::get('/pesan', function () {
        $produk = request('produk');
        $kategori = request('kategori');
        $harga = request('harga');
        $gambar = request('gambar');
        $kerah = request('kerah');
        $bahan = request('bahan');
        $jenis_potongan = request('jenis_potongan');
        $lengan_jahitan = request('lengan_jahitan');

        $produkData = $produk ? compact('produk', 'kategori', 'harga', 'gambar', 'kerah', 'bahan', 'jenis_potongan', 'lengan_jahitan') : null;
        $addresses = auth()->check() ? auth()->user()->addresses : collect([]);

        return view('customer.pemesanan', compact('produkData', 'addresses'));
    })->name('pemesanan');

// Public routes (Midtrans callback)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Public route (shared tracking)
Route::get('/tracking/shared/{token}', [TrackingController::class, 'shared'])->name('tracking.shared');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/api/user-summary', [SummaryController::class, 'index'])->name('api.user-summary');

    Route::post('/pesan', [OrderController::class, 'store'])->name('pesan.store');
    Route::post('/pesan/cart', [OrderController::class, 'storeCart'])->name('pesan.store-cart');
    Route::post('/payment/approve/{order:order_number}', [PaymentController::class, 'approveAndPay'])->name('payment.approve');
    Route::post('/payment/snap/{order:order_number}', [PaymentController::class, 'snapToken'])->name('payment.snap');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/{id}/acc', [TrackingController::class, 'accDesign'])->name('tracking.acc');
    Route::post('/tracking/{id}/revision', [TrackingController::class, 'revision'])->name('tracking.revision');
    Route::post('/tracking/{id}/share-token', [TrackingController::class, 'generateToken'])->name('tracking.share-token');
    Route::get('/tracking/search/json', [TrackingController::class, 'search'])->name('tracking.search');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');

    Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');
    Route::put('/address/{address}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{address}', [AddressController::class, 'destroy'])->name('address.destroy');
    Route::patch('/profile/contact', [AddressController::class, 'updateProfile'])->name('profile.update.contact');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/design', [CartController::class, 'storeDesign'])->name('cart.store-design');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/{cart}/toggle-select', [CartController::class, 'toggleSelect'])->name('cart.toggle-select');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');
    Route::get('/notifikasi/unread-count', [NotificationController::class, 'countUnread'])->name('notifikasi.unread-count');
    Route::get('/notifikasi/recent', [NotificationController::class, 'recentJson'])->name('notifikasi.recent');

});
