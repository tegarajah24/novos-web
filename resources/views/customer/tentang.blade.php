@extends('layouts.customer')

@section('content')
{{-- Hero --}}
<section class="relative w-full bg-[#0f2040] overflow-hidden" style="min-height:400px">

    {{-- background image --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/bg-tentang.png') }}" alt=""
             class="w-full h-full object-cover opacity-[0.50]">
    </div>

    {{-- mesh overlay --}}
    <div class="absolute inset-0 opacity-[0.03] z-[1]"
         style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:20px 20px"></div>

    <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>
    <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-[#00e5ff] opacity-[0.05] rounded-full blur-3xl z-[1]"></div>

    {{-- content --}}
    <div class="relative z-10 max-w-[1200px] mx-auto px-6 flex items-center" style="min-height:400px">

        <div class="max-w-2xl">
            <h1 class="text-4xl md:text-[56px] font-bold leading-tight text-white mb-5" data-aos="fade-up" data-aos-delay="100">
                Tentang <span class="text-[#00e5ff]">Novos</span>
            </h1>
            <p class="text-base md:text-lg text-[#c8d6e0] leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                Platform pemesanan jersey custom terpercaya untuk kebutuhan tim, komunitas, dan bisnis Anda. Kualitas premium, layanan mudah dan cepat.
            </p>
        </div>
    </div>
</section>

{{-- Profil Singkat & Identitas Brand --}}
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="grid md:grid-cols-5 gap-10 items-start">
        {{-- Cerita di Balik Novos --}}
        <div class="md:col-span-3">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Cerita di Balik Novos</h2>
            <div class="space-y-4 text-gray-600 leading-relaxed">
                <p>
                    <strong class="text-gray-900">Novos</strong> lahir dari kegelisahan para founder-nya yang merupakan pegiat olahraga di Purwokerto. Mereka merasa sulit mendapatkan jersey custom berkualitas tinggi dengan harga yang masuk akal tanpa harus memesan dari luar kota. Proses yang rumit, komunikasi yang tidak jelas, dan hasil yang tidak sesuai ekspektasi menjadi masalah yang terus berulang.
                </p>
                <p>
                    Nama <strong class="text-gray-900">"Novos"</strong> berasal dari Bahasa Latin yang berarti <em>"baru"</em> atau <em>"pembaruan"</em>. Filosofi ini menjadi semangat kami untuk <strong class="text-gray-900">memperbarui cara orang memesan jersey custom</strong> — dari proses yang rumit menjadi mudah, dari harga yang mahal menjadi terjangkau, dari kualitas standar menjadi premium.
                </p>
                <p>
                    Berdiri sejak tahun 2022, Novos fokus menyediakan jersey olahraga berkualitas tinggi untuk berbagai cabang olahraga seperti sepak bola, futsal, basket, voli, hingga running. Setiap jersey yang kami produksi menggunakan bahan <strong class="text-gray-900">Dryfit Premium</strong> yang nyaman dipakai, ringan, dan cepat kering.
                </p>
            </div>
        </div>

        {{-- Lokasi (Lokal Prides) --}}
        <div class="md:col-span-2">
            <div class="bg-gradient-to-br from-[#1a237e] to-[#283593] rounded-xl p-6 text-white">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Lokal Prides</h3>
                <p class="text-white/80 text-sm leading-relaxed mb-4">
                    Novos adalah <strong class="text-white">brand lokal asli Purwokerto</strong> yang bangga menjadi bagian dari ekosistem olahraga regional.
                </p>
                <div class="flex items-center gap-3 bg-white/10 rounded-lg px-4 py-3">
                    <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span class="text-sm">Purwokerto, Jawa Tengah</span>
                </div>
                <p class="text-white/60 text-xs mt-4 leading-relaxed">
                    Dengan menjadi bagian dari Novos, Anda turut mendukung pertumbuhan industri kreatif dan UMKM lokal di Purwokerto.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Visi & Misi (Split Screen) --}}
<div class="bg-gray-50 py-16">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-0 rounded-2xl overflow-hidden shadow-lg">
            {{-- Kiri: Gambar --}}
            <div class="relative h-72 md:h-auto bg-[#1a237e] overflow-hidden">
                <img
                    src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=800&q=80"
                    alt="Jersey Olahraga Novos"
                    class="absolute inset-0 w-full h-full object-cover opacity-90"
                >
                <div class="absolute inset-0 bg-gradient-to-r from-[#1a237e]/60 to-transparent md:bg-gradient-to-t md:from-[#1a237e]/40 md:to-transparent"></div>
            </div>

            {{-- Kanan: Visi & Misi --}}
            <div class="bg-white p-8 md:p-12 flex flex-col justify-center space-y-8">
                {{-- Visi --}}
                <div>
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Visi</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi platform jersey custom nomor satu di Indonesia yang dikenal dengan kualitas terbaik, desain inovatif, dan pelayanan yang memuaskan.
                    </p>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-100"></div>

                {{-- Misi --}}
                <div>
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1e3a5f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Misi</h2>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Menyediakan jersey custom berkualitas tinggi dengan bahan terbaik</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Memberikan kemudahan pemesanan melalui sistem online yang transparan</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Mendukung pelaku olahraga, komunitas, dan bisnis lokal</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Terus berinovasi dalam desain dan teknologi produksi</span>
                        </li>
                    </ul>
                </div>
            </div>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Baris Atas --}}

        {{-- Card 1: Desain Bebas (gambar overlay) --}}
        <div class="relative rounded-xl overflow-hidden bg-[#1a237e] min-h-[300px] md:min-h-[360px] group">
            <img
                src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80"
                alt="Desain Jersey"
                class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-70 transition-opacity duration-500"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a237e]/90 via-[#1a237e]/50 to-transparent"></div>
            <div class="relative h-full p-8 flex flex-col justify-end">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Desain Bebas</h3>
                <p class="text-sm text-white/80 leading-relaxed max-w-md">Bebas menentukan desain, warna, logo, dan ukuran sesuai keinginan Anda. Tim desain kami siap mewujudkan konsep terbaik Anda.</p>
            </div>
        </div>

        {{-- Card 2: Kualitas Premium (navy gelap) --}}
        <div class="rounded-xl bg-[#0d1b3e] p-8 md:p-10 min-h-[300px] md:min-h-[360px] flex flex-col justify-end group hover:bg-[#0a1633] transition-colors duration-300">
            <div class="w-12 h-12 bg-[#ffd700]/20 rounded-xl flex items-center justify-center mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffd700" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-3">Kualitas Premium</h3>
            <p class="text-sm text-gray-300 leading-relaxed mb-6 max-w-md">Kami hanya menggunakan bahan Dryfit Premium grade A dengan jahitan presisi tinggi. Setiap jersey melewati kontrol kualitas ketat sebelum dikirim.</p>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#ffd700]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Bahan Premium
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#ffd700]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Jahitan Rapi
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#ffd700]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Quality Check
                </span>
            </div>
        </div>

        {{-- Baris Bawah --}}

        {{-- Card 3: Tepat Waktu (memanjang) --}}
        <div class="rounded-xl bg-white border border-gray-200 p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Tepat Waktu</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Komitmen pengiriman sesuai jadwal dengan estimasi yang akurat dan jelas.</p>
            </div>
        </div>

        {{-- Card 4: Harga Terjangkau (memanjang) --}}
        <div class="rounded-xl bg-white border border-gray-200 p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">Harga Terjangkau</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Harga kompetitif dengan kualitas terbaik. Cocok untuk semua kalangan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    [data-aos] {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.7s ease-out, transform 0.7s ease-out;
    }
    [data-aos].aos-visible {
        opacity: 1;
        transform: translateY(0);
    }
    [data-aos="zoom-in"] {
        opacity: 0;
        transform: scale(0.95);
    }
    [data-aos="zoom-in"].aos-visible {
        opacity: 1;
        transform: scale(1);
    }
    [data-aos-delay="100"].aos-visible { transition-delay: 0.1s; }
    [data-aos-delay="200"].aos-visible { transition-delay: 0.2s; }
    [data-aos-delay="300"].aos-visible { transition-delay: 0.3s; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('[data-aos]').forEach(function(el) {
        observer.observe(el);
    });
});
</script>
@endpush
