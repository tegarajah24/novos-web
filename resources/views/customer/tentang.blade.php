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
<div class="max-w-[1200px] mx-auto px-6 py-16">
    <div class="overflow-hidden">
        {{-- Cerita di Balik Novos --}}
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
</div>

{{-- Visi & Misi --}}
<div class="bg-gray-50 py-16">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-[1fr_auto_1fr] divide-y md:divide-y-0">
                {{-- Visi --}}
                <div class="p-8 md:p-10 flex flex-col">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Visi</h2>
                    <p class="text-gray-600 leading-relaxed flex-1">
                        Menjadi platform jersey custom nomor satu di Indonesia yang dikenal dengan kualitas terbaik, desain inovatif, dan pelayanan yang memuaskan.
                    </p>
                </div>

                {{-- Divider --}}
                <div class="hidden md:flex items-stretch">
                    <div class="w-px bg-gray-100 my-8"></div>
                </div>

                {{-- Misi --}}
                <div class="p-8 md:p-10 flex flex-col">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Misi</h2>
                    <ul class="space-y-3 text-gray-600 flex-1">
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                            <span>Menyediakan jersey custom berkualitas tinggi dengan bahan terbaik</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                            <span>Memberikan kemudahan pemesanan melalui sistem online yang transparan</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                            <span>Mendukung pelaku olahraga, komunitas, dan bisnis lokal</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                            <span>Terus berinovasi dalam desain dan teknologi produksi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tim --}}
<section x-data="{ scrolledLeft: false, scrolledRight: false, updateScroll() { let el = $refs.timScroll; this.scrolledLeft = el.scrollLeft > 5; this.scrolledRight = (el.scrollLeft + el.clientWidth) >= (el.scrollWidth - 5); }, scrollLeft() { let el = $refs.timScroll; el.scrollBy({ left: -320, behavior: 'smooth' }); }, scrollRight() { let el = $refs.timScroll; el.scrollBy({ left: 320, behavior: 'smooth' }); } }" class="bg-gray-50 py-16">
    <div class="max-w-[1200px] mx-auto px-6">

        <div class="flex items-center justify-between mb-2" data-aos="fade-up">
            <div class="flex-1"></div>
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">Tim Kami</h2>
                <p class="text-gray-500 mt-1">Orang-orang hebat di balik Novos</p>
            </div>
            <div class="flex-1 flex items-center justify-end gap-1">
                <button @click="scrollLeft()"
                    :class="scrolledLeft ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &lt;
                </button>
                <button @click="scrollRight()"
                    :class="!scrolledRight ? 'text-black hover:text-gray-500 cursor-pointer' : 'text-gray-300 cursor-default'"
                    class="px-1 transition-colors font-thin text-2xl leading-none">
                    &gt;
                </button>
            </div>
        </div>
        <div class="mx-auto w-20 h-0.5 bg-gray-900 mb-8"></div>

        <div x-ref="timScroll" @scroll="updateScroll()" class="flex gap-6 overflow-x-auto pb-4 no-scrollbar scroll-smooth">
            @foreach([
                ['Ahmad Rizki', 'Founder & CEO', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&q=80'],
                ['Sarah Putri', 'Head of Design', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80'],
                ['Dimas Pratama', 'Head of Production', 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&q=80'],
                ['Rina Fitriani', 'Customer Service', 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&q=80'],
                ['Budi Santoso', 'Lead Designer', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
                ['Sari Dewi', 'Marketing Manager', 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&q=80'],
                ['Adi Nugroho', 'Production Staff', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80'],
                ['Dian Permata', 'Quality Control', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&q=80'],
                ['Fajar Hidayat', 'Logistics', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80'],
                ['Maya Anggraini', 'Admin', 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=400&q=80'],
            ] as $t)
            <div class="flex-shrink-0 w-[220px] group" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 5) * 100 }}">
                <div class="relative w-full overflow-hidden rounded-lg" style="aspect-ratio:3/4">
                    <img src="{{ $t[2] }}"
                         alt="{{ $t[0] }}"
                         class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-105">
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-gray-900 text-sm font-bold">{{ $t[0] }}</h3>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $t[1] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Keunggulan Layanan --}}
<div class="max-w-[1200px] mx-auto px-6 py-16">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Keunggulan Layanan</h2>
    <p class="text-gray-500 text-center mb-10">Kenapa memilih Novos?</p>

    <div class="bg-[#f0f4ff] rounded-xl p-8 md:p-10">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 md:divide-x md:divide-[#d0d8f0]">
            <div class="text-center px-4 py-4">
                <div class="w-12 h-12 bg-[#1a237e]/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Desain Bebas</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Bebas menentukan desain, warna, logo, dan ukuran sesuai keinginan Anda.</p>
            </div>
            <div class="text-center px-4 py-4">
                <div class="w-12 h-12 bg-[#ffd700]/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Kualitas Premium</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Bahan Dryfit Premium grade A dengan jahitan presisi tinggi dan QC ketat.</p>
            </div>
            <div class="text-center px-4 py-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Tepat Waktu</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Komitmen pengiriman sesuai jadwal dengan estimasi yang akurat dan jelas.</p>
            </div>
            <div class="text-center px-4 py-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Harga Terjangkau</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Harga kompetitif dengan kualitas terbaik, cocok untuk semua kalangan.</p>
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
