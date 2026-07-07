{{-- ============================================================ --}}
{{-- FOOTER CUSTOMER --}}
{{-- ============================================================ --}}
<footer class="bg-[#0d0d2b] pt-[60px] pb-6">
    <div class="max-w-[1200px] mx-auto px-6">
        {{-- 4 columns --}}
        <div class="flex flex-col md:flex-row gap-12 mb-12">
            {{-- Col 1: Brand --}}
            <div class="flex-1">
                <p class="text-white text-2xl font-extrabold tracking-tight mb-4">NOVOS</p>
                <p class="text-sm text-[#9e9e9e] leading-relaxed mb-6">Jl. Jatisari No.56, Karangmiri, Sumampir, Kec. Purwokerto Utara, Kabupaten Banyumas, Jawa Tengah 53121</p>
                <div class="flex items-center gap-3">
                    {{-- WhatsApp --}}
                    <a href="#" class="w-9 h-9 rounded-full border border-[#00e5ff]/40 flex items-center justify-center text-[#00e5ff] hover:bg-[#00e5ff] hover:text-[#0d0d2b] transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.75 12a9.75 9.75 0 01-14.55 8.393l-3.45 1.157 1.157-3.45A9.75 9.75 0 1121.75 12z" />
                            <path d="M15.75 13.5a.75.75 0 100-1.5.75.75 0 000 1.5zM12 13.5a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                        </svg>
                    </a>
                    {{-- Instagram --}}
                    <a href="#" class="w-9 h-9 rounded-full border border-[#00e5ff]/40 flex items-center justify-center text-[#00e5ff] hover:bg-[#00e5ff] hover:text-[#0d0d2b] transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                            <circle cx="12" cy="12" r="4.5" />
                            <circle cx="17.5" cy="6.5" r="1" />
                        </svg>
                    </a>
                    {{-- Email --}}
                    <a href="#" class="w-9 h-9 rounded-full border border-[#00e5ff]/40 flex items-center justify-center text-[#00e5ff] hover:bg-[#00e5ff] hover:text-[#0d0d2b] transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Col 2: Menu --}}
            <div class="flex-1">
                <p class="text-white font-bold text-sm mb-5">Menu</p>
                <ul class="space-y-3">
                    <li><a href="{{ route('beranda') }}" class="footer-navlink text-sm text-[#bdbdbd] hover:text-[#00e5ff] transition-colors">Beranda</a></li>
                    <li><a href="{{ route('tentang') }}" class="footer-navlink text-sm text-[#bdbdbd] hover:text-[#00e5ff] transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('katalog') }}" class="footer-navlink text-sm text-[#bdbdbd] hover:text-[#00e5ff] transition-colors">Katalog</a></li>
                    <li><a href="{{ route('pemesanan') }}" class="footer-navlink text-sm text-[#bdbdbd] hover:text-[#00e5ff] transition-colors">Buat Pesanan</a></li>
                </ul>
            </div>

            {{-- Col 3: Kontak --}}
            <div class="flex-1">
                <p class="text-white font-bold text-sm mb-5">Kontak</p>
                <ul class="space-y-4">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#00e5ff] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                        </svg>
                        <span class="text-sm text-[#bdbdbd]">+62 812 3456 7890</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#00e5ff] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="text-sm text-[#bdbdbd]">hello@novosjersey.com</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#00e5ff] flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                            <circle cx="12" cy="12" r="4.5" />
                            <circle cx="17.5" cy="6.5" r="1" />
                        </svg>
                        <span class="text-sm text-[#bdbdbd]">@novosjersey</span>
                    </li>
                </ul>
            </div>

            {{-- Col 4: Jam Operasional --}}
            <div class="flex-1">
                <p class="text-white font-bold text-sm mb-5">Jam Operasional</p>
                <ul class="space-y-3">
                    <li class="flex justify-between text-sm">
                        <span class="text-[#bdbdbd]">Sen - Jum</span>
                        <span class="text-white font-medium">08.00 - 17.00</span>
                    </li>
                    <li class="flex justify-between text-sm">
                        <span class="text-[#bdbdbd]">Sabtu</span>
                        <span class="text-white font-medium">08.00 - 13.00</span>
                    </li>
                    <li class="flex justify-between text-sm">
                        <span class="text-[#bdbdbd]">Minggu</span>
                        <span class="text-[#9e9e9e]">Libur</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-[#ffffff]/10 pt-6">
            <p class="text-xs text-[#757575] text-center">&copy; {{ date('Y') }} Novos. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener('click', function (e) {
    var link = e.target.closest('.footer-navlink');
    if (!link) return;

    var currentPath = window.location.pathname.replace(/\/+$/, '');
    var linkPath = new URL(link.href).pathname.replace(/\/+$/, '');

    if (currentPath === linkPath) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
