@extends('layouts.customer')

@section('content')
{{-- Hero --}}
<div class="bg-gradient-to-br from-blue-900 to-blue-700 text-white">
    <div class="max-w-5xl mx-auto px-4 py-20 text-center">
        <h1 class="text-4xl font-bold mb-4">Tentang Novos</h1>
        <p class="text-blue-200 text-lg max-w-2xl mx-auto leading-relaxed">
            Novos adalah platform pemesanan jersey custom terpercaya untuk kebutuhan tim, komunitas, dan bisnis Anda. Kami menggabungkan kualitas premium dengan layanan yang mudah dan cepat.
        </p>
    </div>
</div>

{{-- Visi & Misi --}}
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="grid md:grid-cols-2 gap-10">
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">Visi</h2>
            <p class="text-gray-600 leading-relaxed">
                Menjadi platform jersey custom nomor satu di Indonesia yang dikenal dengan kualitas terbaik, desain inovatif, dan pelayanan yang memuaskan.
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-8">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-3">Misi</h2>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Menyediakan jersey custom berkualitas tinggi dengan bahan terbaik</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Memberikan kemudahan pemesanan melalui sistem online yang transparan</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Mendukung pelaku olahraga, komunitas, dan bisnis lokal</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Terus berinovasi dalam desain dan teknologi produksi</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- Tim --}}
<div class="bg-gray-50 py-16">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Tim Kami</h2>
        <p class="text-gray-500 text-center mb-10">Orang-orang hebat di balik Novos</p>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-blue-100 mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl font-bold text-blue-900">A</span>
                </div>
                <h3 class="font-bold text-gray-900">Ahmad Rizki</h3>
                <p class="text-sm text-gray-500">Founder & CEO</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-purple-100 mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl font-bold text-purple-900">S</span>
                </div>
                <h3 class="font-bold text-gray-900">Sarah Putri</h3>
                <p class="text-sm text-gray-500">Head of Design</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-green-100 mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl font-bold text-green-900">D</span>
                </div>
                <h3 class="font-bold text-gray-900">Dimas Pratama</h3>
                <p class="text-sm text-gray-500">Head of Production</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                <div class="w-24 h-24 rounded-full bg-amber-100 mx-auto mb-4 flex items-center justify-center">
                    <span class="text-3xl font-bold text-amber-900">R</span>
                </div>
                <h3 class="font-bold text-gray-900">Rina Fitriani</h3>
                <p class="text-sm text-gray-500">Customer Service</p>
            </div>
        </div>
    </div>
</div>

{{-- Keunggulan Layanan --}}
<div class="max-w-5xl mx-auto px-4 py-16">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Keunggulan Layanan</h2>
    <p class="text-gray-500 text-center mb-10">Kenapa memilih Novos?</p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center hover:shadow-lg transition-shadow">
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M16 15.5v.01"/><path d="M12 12v.01"/><path d="M11 17v.01"/><path d="M7 14v.01"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Kualitas Premium</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Menggunakan bahan kain terbaik dengan jahitan rapi dan presisi tinggi.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center hover:shadow-lg transition-shadow">
            <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#581c87" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Desain Bebas</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Bebas menentukan desain, warna, logo, dan ukuran sesuai keinginan Anda.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center hover:shadow-lg transition-shadow">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Tepat Waktu</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Komitmen pengiriman sesuai jadwal dengan estimasi yang akurat dan jelas.</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 text-center hover:shadow-lg transition-shadow">
            <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3 class="font-bold text-gray-900 mb-2">Harga Terjangkau</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Harga kompetitif dengan kualitas terbaik. Cocok untuk semua kalangan.</p>
        </div>
    </div>
</div>
@endsection
