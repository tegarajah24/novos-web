
@extends('layouts.customer')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Diproses</h1>
    <p class="text-gray-500 mb-8">Terima kasih, pembayaran Anda sedang diproses. Kami akan mengirimkan notifikasi jika pembayaran telah berhasil.</p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ route('tracking') }}" class="px-8 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors text-center">
            Tracking Pesanan
        </a>
        <a href="{{ route('beranda') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-center">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
