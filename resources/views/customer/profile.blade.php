@extends('layouts.customer')

@section('title', 'Profil Saya — Novos')

@push('styles')
<style>
    .profile-card {
        border-radius: 16px;
        background: #fff;
        border: 1px solid #f0f0f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola informasi akun Anda</p>
    </div>

    {{-- Alert sukses --}}
    @if (session('status') === 'profile-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3.5">
        <svg class="w-5 h-5 shrink-0 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p class="text-sm font-medium text-green-800">Profil berhasil diperbarui!</p>
    </div>
    @endif

    @if (session('status') === 'password-updated')
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-5 py-3.5">
        <svg class="w-5 h-5 shrink-0 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p class="text-sm font-medium text-green-800">Password berhasil diubah!</p>
    </div>
    @endif

    {{-- Informasi Profil --}}
    <div class="profile-card p-6 mb-6">
        <div class="flex items-center gap-4 pb-6 border-b border-gray-100 mb-6">
            <div class="w-16 h-16 rounded-full bg-[#e8eaf6] flex items-center justify-center">
                <svg class="w-8 h-8 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
            @csrf
            @method('patch')

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Perubahan
            </button>
        </form>
    </div>

    {{-- Ganti Password --}}
    <div class="profile-card p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Ganti Password</h2>
        <p class="text-sm text-gray-500 mb-6">Pastikan akun Anda menggunakan password yang kuat dan aman.</p>

        <form method="POST" action="{{ route('password.update') }}" @submit.prevent="if ($event.target.checkValidity()) $event.target.submit()">
            @csrf
            @method('put')

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" required autocomplete="current-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm @error('current_password', 'updatePassword') border-red-400 @enderror"
                        placeholder="Masukkan password saat ini">
                    @error('current_password', 'updatePassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                    <input type="password" name="password" required autocomplete="new-password" minlength="8"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm @error('password', 'updatePassword') border-red-400 @enderror"
                        placeholder="Minimal 8 karakter">
                    @error('password', 'updatePassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow text-sm"
                        placeholder="Ulangi password baru">
                </div>
            </div>

            <button type="submit"
                class="w-full mt-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-lg hover:bg-[#283593] transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                Simpan Password
            </button>
        </form>
    </div>

    {{-- Hapus Akun --}}
    <div class="profile-card p-6 border-red-100">
        <h2 class="text-lg font-bold text-red-700 mb-1">Hapus Akun</h2>
        <p class="text-sm text-gray-500 mb-4">Setelah akun dihapus, semua data Anda akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Semua data akan hilang permanen.')">
            @csrf
            @method('delete')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Masukkan Password untuk Konfirmasi</label>
                <input type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500/20 focus:border-red-400 outline-none transition-shadow text-sm @error('password', 'userDeletion') border-red-400 @enderror"
                    placeholder="Password saat ini">
                @error('password', 'userDeletion')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                Hapus Akun
            </button>
        </form>
    </div>
</div>
@endsection
