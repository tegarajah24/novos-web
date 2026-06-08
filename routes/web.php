<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', fn() => view('customer.beranda'));

Route::middleware(['auth', 'role:Super Admin,Manager,Admin,Design,Produksi'])->group(function () {
    Route::get('/internal/summary', function () {
        return view('internal.summary');
    });

    Route::get('/internal/daftarpesanan', function () {
        return view('internal.daftar-pesanan');
    });

    Route::get('/internal/detail-pesanan/{id}', function ($id) {
        return view('internal.detail-pesanan', ['id' => $id]);
    });

    Route::get('/internal/chat', function () {
        return view('internal.chat');
    })->name('internal.chat');

    Route::get('/internal/stress-test', function () {
        return view('internal.stress-test');
    });

    Route::get('/internal/laporan', function () {
        return view('internal.laporan');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role?->name;
        return match($role) {
            'Super Admin' => redirect('/superadmin/dashboard'),
            'Manager'     => redirect('/manager/dashboard'),
            'Admin'       => redirect('/admin/dashboard'),
            'Design'      => redirect('/design/dashboard'),
            'Produksi'    => redirect('/produksi/dashboard'),
            default       => redirect('/customer/dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', function () {
        $role = auth()->user()->role?->name;
        if (in_array($role, ['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi'])) {
            return redirect()->route('internal.chat');
        }
        return redirect()->route('customer.chat');
    });
});


require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/pegawai.php';
require __DIR__.'/customer.php';
