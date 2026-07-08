<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\ProductController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\TrackingController;
use App\Http\Controllers\Customer\ChatController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\WilayahController;
use App\Models\Wilayah;

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
        $hasOrders = auth()->check() ? auth()->user()->orders()->exists() : false;
        $provinces = Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->orderBy('nama')->get()->map(fn($i) => ['id' => $i->kode, 'name' => $i->nama]);

        return view('customer.pemesanan', compact('produkData', 'addresses', 'hasOrders', 'provinces'));
    })->name('pemesanan');

// Public routes for wilayah data
Route::get('/api/wilayah/provinces', [WilayahController::class, 'provinces'])->name('api.wilayah.provinces');
Route::get('/api/wilayah/regencies/{provinceCode}', [WilayahController::class, 'regencies'])->name('api.wilayah.regencies');
Route::get('/api/wilayah/districts/{regencyCode}', [WilayahController::class, 'districts'])->name('api.wilayah.districts');

// Public route (shared tracking)
Route::get('/tracking/shared/{token}', [TrackingController::class, 'shared'])->name('tracking.shared');

// Authenticated routes
Route::middleware('auth')->group(function () {

    Route::get('/api/user-summary', [SummaryController::class, 'index'])->name('api.user-summary');

    Route::post('/pesan', [OrderController::class, 'store'])->name('pesan.store');
    Route::post('/pesan/cart', [OrderController::class, 'storeCart'])->name('pesan.store-cart');
    Route::post('/pesan/{order:order_number}/approve', [OrderController::class, 'approve'])->name('pesan.approve');
    Route::post('/pesan/{order:order_number}/payment-proof', [OrderController::class, 'uploadPaymentProof'])->name('pesan.payment-proof');

    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::post('/tracking/{id}/acc', [TrackingController::class, 'accDesign'])->name('tracking.acc');
    Route::post('/tracking/{id}/revision', [TrackingController::class, 'revision'])->name('tracking.revision');
    Route::post('/tracking/{id}/share-token', [TrackingController::class, 'generateToken'])->name('tracking.share-token');
    Route::get('/tracking/search/json', [TrackingController::class, 'search'])->name('tracking.search');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('/chat/poll-list', [ChatController::class, 'pollList'])->name('chat.poll-list');

    Route::post('/chat/{chat}/read', [ChatController::class, 'markRead'])->name('chat.read');
    Route::get('/chat/{chat}/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/send', [ChatController::class, 'store'])->name('chat.send');
    Route::delete('/chat/{chat}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::delete('/chat/message/{chatMessage}', [ChatController::class, 'destroyMessage'])->name('chat.message.destroy');
    Route::get('/chat/download/{chatMessage}', [ChatController::class, 'download'])->name('chat.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');
    Route::put('/address/{address}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{address}', [AddressController::class, 'destroy'])->name('address.destroy');
    Route::patch('/profile/contact', [AddressController::class, 'updateProfile'])->name('profile.update.contact');
    Route::post('/profile/pembelian/review', [ReviewController::class, 'store'])->name('profile.pembelian.review');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::post('/cart/design', [CartController::class, 'storeDesign'])->name('cart.store-design');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::put('/cart/{cart}/update-sizes', [CartController::class, 'updateSizes'])->name('cart.update-sizes');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/{cart}/toggle-select', [CartController::class, 'toggleSelect'])->name('cart.toggle-select');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifikasi');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifikasi.read-all');
    Route::get('/notifikasi/unread-count', [NotificationController::class, 'countUnread'])->name('notifikasi.unread-count');
    Route::get('/notifikasi/recent', [NotificationController::class, 'recentJson'])->name('notifikasi.recent');
    Route::get('/notifikasi/json', [NotificationController::class, 'paginatedJson'])->name('notifikasi.json');

});
