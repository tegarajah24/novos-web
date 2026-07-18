@extends('layouts.customer')

@section('title', 'Tentang Kami — Novos')

@section('content')
<script>
function timCarouselData(team) {
    return {
        team: team,
        currentSlide: 0,
        interval: null,
        startX: null,
        startY: null,
        initCarousel() {
            this.$nextTick(() => {
                const el = this.$refs.carouselWrap;
                if (!el) return;

                let isDragging = false;
                let wheelTimeout = null;
                let accumulatedDeltaX = 0;

                // ─── Touch Events (Real mobile + DevTools simulation) ─────────
                el.addEventListener('touchstart', (e) => {
                    this.startX = e.touches[0].clientX;
                    this.startY = e.touches[0].clientY;
                    this.stopAutoplay();
                }, { passive: true });

                el.addEventListener('touchmove', (e) => {
                    if (this.startX == null || this.startY == null) return;
                    const dx = this.startX - e.touches[0].clientX;
                    const dy = this.startY - e.touches[0].clientY;
                    if (Math.abs(dx) > Math.abs(dy) && e.cancelable) e.preventDefault();
                }, { passive: false });

                const handleTouchEnd = (e) => {
                    if (this.startX == null) return;
                    const endX = e.changedTouches ? e.changedTouches[0].clientX : this.startX;
                    const dx = this.startX - endX;
                    if (Math.abs(dx) > 40) {
                        dx > 0
                            ? this.currentSlide = Math.min(this.team.length - 1, this.currentSlide + 1)
                            : this.currentSlide = Math.max(0, this.currentSlide - 1);
                    }
                    this.startX = null;
                    this.startY = null;
                    this.startAutoplay();
                };

                el.addEventListener('touchend', handleTouchEnd, { passive: true });
                el.addEventListener('touchcancel', handleTouchEnd, { passive: true });

                // ─── Mouse Drag (Desktop click-and-drag) ──────────────────────
                el.addEventListener('mousedown', (e) => {
                    if (e.button !== 0) return;
                    isDragging = true;
                    this.startX = e.clientX;
                    this.startY = e.clientY;
                    this.stopAutoplay();
                    el.style.cursor = 'grabbing';
                    e.preventDefault();
                });

                const handleMouseUp = (e) => {
                    if (!isDragging) return;
                    const dx = this.startX != null ? this.startX - e.clientX : 0;
                    if (Math.abs(dx) > 40) {
                        dx > 0
                            ? this.currentSlide = Math.min(this.team.length - 1, this.currentSlide + 1)
                            : this.currentSlide = Math.max(0, this.currentSlide - 1);
                    }
                    isDragging = false;
                    this.startX = null;
                    this.startY = null;
                    el.style.cursor = 'grab';
                    this.startAutoplay();
                };

                window.addEventListener('mouseup', handleMouseUp);

                // ─── Wheel (Trackpad 2-finger swipe) — instant on threshold ──
                let slideOnCooldown = false;
                let wheelIdleTimeout = null;
                el.addEventListener('wheel', (e) => {
                    const isHorizontal = Math.abs(e.deltaX) > Math.abs(e.deltaY);
                    if (!isHorizontal) return;

                    e.preventDefault();
                    this.stopAutoplay();
                    accumulatedDeltaX += e.deltaX;

                    // Restart autoplay only after user fully stops swiping (2s idle)
                    clearTimeout(wheelIdleTimeout);
                    wheelIdleTimeout = setTimeout(() => {
                        slideOnCooldown = false;
                        accumulatedDeltaX = 0;
                        this.startAutoplay();
                    }, 2000);

                    // Immediately change slide when threshold is met
                    if (!slideOnCooldown && Math.abs(accumulatedDeltaX) > 30) {
                        if (accumulatedDeltaX > 0) {
                            this.currentSlide = Math.min(this.team.length - 1, this.currentSlide + 1);
                        } else {
                            this.currentSlide = Math.max(0, this.currentSlide - 1);
                        }
                        accumulatedDeltaX = 0;
                        // Short cooldown only to prevent double-triggering within one swipe
                        slideOnCooldown = true;
                        setTimeout(() => { slideOnCooldown = false; }, 500);
                    }
                }, { passive: false });

                // Prevent image drag ghost
                el.querySelectorAll('img').forEach(img => {
                    img.addEventListener('dragstart', e => e.preventDefault());
                });

                this.startAutoplay();
            });
        },
        startAutoplay() {
            this.stopAutoplay();
            this.interval = setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.team.length;
            }, 3000);
        },
        stopAutoplay() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        }
    };
}
</script>
{{-- Hero --}}
<section class="relative w-full bg-[#0f2040] overflow-hidden" style="min-height:400px">

    {{-- background image --}}
    <div class="absolute inset-0 z-0">
        @php $heroBg = \App\Models\Setting::get('hero_tentang_bg'); @endphp
        <img src="{{ $heroBg ? asset('storage/hero-backgrounds/' . $heroBg) : asset('images/bg-tentang.png') }}" alt=""
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
<div x-data="{ ceritaExpanded: false }" class="max-w-[1200px] mx-auto px-6 py-16">
    <div class="overflow-hidden">
        {{-- Cerita di Balik Novos --}}
        <h2 class="text-2xl font-bold text-gray-900 mb-6" data-aos="fade-up" data-aos-delay="100">Cerita di Balik Novos</h2>

        {{-- Mobile: Read More --}}
        <div class="md:hidden">
            <div :class="ceritaExpanded ? '' : 'line-clamp-3'" class="space-y-4 text-gray-600 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                @if($aboutStory)
                    {!! nl2br(e($aboutStory)) !!}
                @else
                    <p>
                        <strong class="text-gray-900">Novos</strong> lahir dari kegelisahan para founder-nya yang merupakan pegiat olahraga di Purwokerto. Mereka merasa sulit mendapatkan jersey custom berkualitas tinggi dengan harga yang masuk akal tanpa harus memesan dari luar kota. Proses yang rumit, komunikasi yang tidak jelas, dan hasil yang tidak sesuai ekspektasi menjadi masalah yang terus berulang.
                    </p>
                    <p>
                        Nama <strong class="text-gray-900">"Novos"</strong> berasal dari Bahasa Latin yang berarti <em>"baru"</em> atau <em>"pembaruan"</em>. Filosofi ini menjadi semangat kami untuk <strong class="text-gray-900">memperbarui cara orang memesan jersey custom</strong> — dari proses yang rumit menjadi mudah, dari harga yang mahal menjadi terjangkau, dari kualitas standar menjadi premium.
                    </p>
                    <p>
                        Berdiri sejak tahun 2022, Novos fokus menyediakan jersey olahraga berkualitas tinggi untuk berbagai cabang olahraga seperti sepak bola, futsal, basket, voli, hingga running. Setiap jersey yang kami produksi menggunakan bahan <strong class="text-gray-900">Dryfit Premium</strong> yang nyaman dipakai, ringan, dan cepat kering.
                    </p>
                @endif
            </div>
            <button @click="ceritaExpanded = !ceritaExpanded" class="mt-2 text-sm font-medium text-[#1a237e] hover:text-[#283593] transition-colors inline-flex items-center gap-1">
                <span x-text="ceritaExpanded ? 'Tutup' : 'Baca Selengkapnya...'"></span>
                <svg x-show="!ceritaExpanded" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                <svg x-show="ceritaExpanded" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
            </button>
        </div>

        {{-- Desktop: Full text --}}
        <div class="hidden md:block space-y-4 text-gray-600 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
            @if($aboutStory)
                {!! nl2br(e($aboutStory)) !!}
            @else
                <p>
                    <strong class="text-gray-900">Novos</strong> lahir dari kegelisahan para founder-nya yang merupakan pegiat olahraga di Purwokerto. Mereka merasa sulit mendapatkan jersey custom berkualitas tinggi dengan harga yang masuk akal tanpa harus memesan dari luar kota. Proses yang rumit, komunikasi yang tidak jelas, dan hasil yang tidak sesuai ekspektasi menjadi masalah yang terus berulang.
                </p>
                <p>
                    Nama <strong class="text-gray-900">"Novos"</strong> berasal dari Bahasa Latin yang berarti <em>"baru"</em> atau <em>"pembaruan"</em>. Filosofi ini menjadi semangat kami untuk <strong class="text-gray-900">memperbarui cara orang memesan jersey custom</strong> — dari proses yang rumit menjadi mudah, dari harga yang mahal menjadi terjangkau, dari kualitas standar menjadi premium.
                </p>
                <p>
                    Berdiri sejak tahun 2022, Novos fokus menyediakan jersey olahraga berkualitas tinggi untuk berbagai cabang olahraga seperti sepak bola, futsal, basket, voli, hingga running. Setiap jersey yang kami produksi menggunakan bahan <strong class="text-gray-900">Dryfit Premium</strong> yang nyaman dipakai, ringan, dan cepat kering.
                </p>
            @endif
        </div>
    </div>
</div>

{{-- Visi & Misi --}}
<div class="bg-gray-50 py-16">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="zoom-in">

            {{-- Desktop: side-by-side --}}
            <div class="hidden md:grid md:grid-cols-[1fr_auto_1fr]">
                {{-- Visi --}}
                <div class="p-8 md:p-10 flex flex-col" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Visi</h2>
                    <p class="text-gray-600 leading-relaxed flex-1">
                        {{ $aboutVisi ?: 'Menjadi platform jersey custom nomor satu di Indonesia yang dikenal dengan kualitas terbaik, desain inovatif, dan pelayanan yang memuaskan.' }}
                    </p>
                </div>

                {{-- Divider --}}
                <div class="hidden md:flex items-stretch">
                    <div class="w-px bg-gray-100 my-8"></div>
                </div>

                {{-- Misi --}}
                <div class="p-8 md:p-10 flex flex-col" data-aos="fade-up" data-aos-delay="200">
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Misi</h2>
                    <ul class="space-y-3 text-gray-600 flex-1">
                        @if($aboutMisi && count($aboutMisi) > 0)
                            @foreach($aboutMisi as $m)
                                @if(!empty(trim($m)))
                                <li class="flex items-start gap-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                                    <span>{{ $m }}</span>
                                </li>
                                @endif
                            @endforeach
                        @else
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
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Mobile: Tab System --}}
            <div x-data="{ vmTab: 'visi' }" class="md:hidden">
                <div class="flex border-b border-gray-100">
                    <button @click="vmTab='visi'"
                            :class="vmTab==='visi' ? 'border-b-2 border-[#1a237e] text-[#1a237e] font-bold' : 'text-gray-500 border-b-2 border-transparent'"
                            class="flex-1 py-3.5 text-sm text-center transition-colors">Visi</button>
                    <button @click="vmTab='misi'"
                            :class="vmTab==='misi' ? 'border-b-2 border-[#1a237e] text-[#1a237e] font-bold' : 'text-gray-500 border-b-2 border-transparent'"
                            class="flex-1 py-3.5 text-sm text-center transition-colors">Misi</button>
                </div>
                <div x-show="vmTab==='visi'" class="p-6">
                    <p class="text-gray-600 leading-relaxed">
                        {{ $aboutVisi ?: 'Menjadi platform jersey custom nomor satu di Indonesia yang dikenal dengan kualitas terbaik, desain inovatif, dan pelayanan yang memuaskan.' }}
                    </p>
                </div>
                <div x-show="vmTab==='misi'" class="p-6">
                    <ul class="space-y-3 text-gray-600">
                        @if($aboutMisi && count($aboutMisi) > 0)
                            @foreach($aboutMisi as $m)
                                @if(!empty(trim($m)))
                                <li class="flex items-start gap-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0 mt-2.5"></span>
                                    <span>{{ $m }}</span>
                                </li>
                                @endif
                            @endforeach
                        @else
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
                        @endif
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Tim --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-[1200px] mx-auto px-6">
        <div class="text-center mb-2" data-aos="fade-up">
            <h2 class="text-2xl font-bold text-gray-900">Tim Kami</h2>
            <p class="text-gray-500 mt-1">Orang-orang hebat di balik Novos</p>
        </div>
        <div class="mx-auto w-20 h-0.5 bg-gray-900 mb-8"></div>

        {{-- Desktop: Horizontal Scroll --}}
        <div x-data="{ scrolledLeft: false, scrolledRight: false, updateScroll() { let el = this.$refs.timScroll; this.scrolledLeft = el.scrollLeft > 5; this.scrolledRight = (el.scrollLeft + el.clientWidth) >= (el.scrollWidth - 5); }, scrollLeft() { let el = this.$refs.timScroll; el.scrollBy({ left: -320, behavior: 'smooth' }); }, scrollRight() { let el = this.$refs.timScroll; el.scrollBy({ left: 320, behavior: 'smooth' }); } }" class="hidden md:block">
            <div class="flex items-center justify-between mb-2">
                <div class="flex-1"></div>
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
            <div x-ref="timScroll" @scroll="updateScroll()" class="flex gap-6 overflow-x-auto overflow-y-hidden pb-4 no-scrollbar scroll-smooth snap-x snap-mandatory">
                @forelse($tim as $t)
                <div class="snap-start shrink-0 w-[270px] group" data-aos="fade-up" data-aos-once="true" data-aos-delay="{{ ($loop->index % 5) * 100 }}">
                    <div class="relative w-full overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center" style="aspect-ratio:3/4">
                        @if($t->avatar)
                        <img src="{{ asset('storage/' . $t->avatar) }}"
                             alt="{{ $t->fullname ?? $t->name }}"
                             class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-105">
                        @else
                        <div class="w-full h-full flex items-center justify-center bg-[#1a237e] text-white text-4xl font-bold">
                            {{ strtoupper(substr($t->fullname ?? $t->name, 0, 2)) }}
                        </div>
                        @endif
                    </div>
                    <div class="mt-3 text-center">
                        <h3 class="text-gray-900 text-sm font-bold">{{ $t->fullname ?? $t->name }}</h3>
                        <p class="text-gray-500 text-xs mt-0.5">{{ $t->role->name }}</p>
                    </div>
                </div>
                @empty
                <div class="w-full text-center py-10 text-gray-500">
                    Belum ada anggota tim.
                </div>
                @endforelse
            </div>
        </div>

        {{-- Mobile: Center Mode Carousel --}}
        <div x-data="timCarouselData({{ json_encode($tim->map(fn($t) => ['name' => $t->fullname ?? $t->name, 'role' => $t->role->name, 'avatar' => $t->avatar ? asset('storage/' . $t->avatar) : null])->values()->toArray()) }})" x-init="initCarousel()" class="md:hidden">
            <div x-ref="carouselWrap" class="relative overflow-hidden px-2" style="touch-action: pan-y; cursor: grab;">
                <div class="flex transition-transform duration-500 ease-out" :style="'transform: translateX(' + (22.5 - (currentSlide * 55)) + '%)'">
                    <template x-for="(t, i) in team" :key="i">
                        <div class="shrink-0 w-[55%] px-2 transition-all duration-500"
                             :class="i === currentSlide ? 'scale-100 opacity-100' : (Math.abs(i - currentSlide) === 1 ? 'scale-90 opacity-50' : 'scale-75 opacity-30')">
                            <div class="w-full overflow-hidden rounded-lg bg-gray-100 flex items-center justify-center" style="aspect-ratio:3/4">
                                <img x-show="t.avatar" :src="t.avatar" :alt="t.name" class="w-full h-full object-cover">
                                <div x-show="!t.avatar" class="w-full h-full flex items-center justify-center bg-[#1a237e] text-white text-4xl font-bold" x-text="t.name.substring(0, 2).toUpperCase()"></div>
                            </div>
                            <div class="mt-3 text-center">
                                <h3 class="text-gray-900 text-sm font-bold" x-text="t.name"></h3>
                                <p class="text-gray-500 text-xs mt-0.5" x-text="t.role"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        @if($tim->count() === 0)
        <div class="w-full text-center py-10 text-gray-500">
            Belum ada anggota tim.
        </div>
        @endif
    </div>
</section>

{{-- Keunggulan Layanan --}}
<div class="max-w-[1200px] mx-auto px-6 py-16">
    <h2 class="text-2xl font-bold text-gray-900 text-center mb-2" data-aos="fade-up">Keunggulan Layanan</h2>
    <p class="text-gray-500 text-center mb-10" data-aos="fade-up" data-aos-delay="100">Kenapa memilih Novos?</p>

    <div class="bg-[#f0f4ff] rounded-xl p-6 md:p-10" data-aos="zoom-in" data-aos-delay="200">
        {{-- Desktop: Grid --}}
        <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 md:divide-x md:divide-[#d0d8f0]">
            <div class="text-center px-4 py-4" data-aos="fade-up" data-aos-delay="100">
                <div class="w-12 h-12 bg-[#1a237e]/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Desain Bebas</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Bebas menentukan desain, warna, logo, dan ukuran sesuai keinginan Anda.</p>
            </div>
            <div class="text-center px-4 py-4" data-aos="fade-up" data-aos-delay="200">
                <div class="w-12 h-12 bg-[#ffd700]/20 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Kualitas Premium</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Bahan Dryfit Premium grade A dengan jahitan presisi tinggi dan QC ketat.</p>
            </div>
            <div class="text-center px-4 py-4" data-aos="fade-up" data-aos-delay="300">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Tepat Waktu</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Komitmen pengiriman sesuai jadwal dengan estimasi yang akurat dan jelas.</p>
            </div>
            <div class="text-center px-4 py-4" data-aos="fade-up" data-aos-delay="400">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Harga Terjangkau</h3>
                <p class="text-sm text-gray-500 leading-relaxed">Harga kompetitif dengan kualitas terbaik, cocok untuk semua kalangan.</p>
            </div>
        </div>

        {{-- Mobile: 2x2 Grid --}}
        <div class="grid grid-cols-2 gap-3 md:hidden">
            <div class="text-center px-2 py-3">
                <div class="w-9 h-9 bg-[#1a237e]/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xs mb-0.5">Desain Bebas</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed">Bebas menentukan desain, warna, logo, dan ukuran sesuai keinginan Anda.</p>
            </div>
            <div class="text-center px-2 py-3">
                <div class="w-9 h-9 bg-[#ffd700]/20 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xs mb-0.5">Kualitas Premium</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed">Bahan Dryfit Premium grade A dengan jahitan presisi tinggi dan QC ketat.</p>
            </div>
            <div class="text-center px-2 py-3">
                <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xs mb-0.5">Tepat Waktu</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed">Komitmen pengiriman sesuai jadwal dengan estimasi yang akurat dan jelas.</p>
            </div>
            <div class="text-center px-2 py-3">
                <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="font-bold text-gray-900 text-xs mb-0.5">Harga Terjangkau</h3>
                <p class="text-[11px] text-gray-500 leading-relaxed">Harga kompetitif dengan kualitas terbaik, cocok untuk semua kalangan.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

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
    [data-aos-delay="400"].aos-visible { transition-delay: 0.4s; }
    [data-aos-delay="500"].aos-visible { transition-delay: 0.5s; }
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
