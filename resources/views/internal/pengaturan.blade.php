@extends('layouts.internal')

@section('title', 'Pengaturan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Pengaturan</h1>

@endsection

@section('internal-content')
<div x-data="settingApp()" x-init="init()" class="max-w-4xl mx-auto">

    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-6 bg-white border border-gray-200 rounded-2xl p-1.5 w-fit shadow-sm">
        <button @click="tab='toko'"
            :class="tab==='toko' ? 'bg-[#1a237e] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <i data-lucide="store" class="w-4 h-4"></i> Toko
        </button>
        <button @click="tab='tampilan'"
            :class="tab==='tampilan' ? 'bg-[#1a237e] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <i data-lucide="palette" class="w-4 h-4"></i> Tampilan
        </button>
        <button @click="tab='panduan'"
            :class="tab==='panduan' ? 'bg-[#1a237e] text-white shadow-md' : 'text-gray-600 hover:bg-white/70'"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            <i data-lucide="book-open" class="w-4 h-4"></i> Panduan
        </button>
    </div>

    {{-- ======================== TAB TOKO ======================== --}}
    <div x-show="tab==='toko'" x-transition>
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-[#1a237e]/10 rounded-xl flex items-center justify-center">
                    <i data-lucide="store" class="w-5 h-5 text-[#1a237e]"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Informasi Toko</h2>
                    <p class="text-xs text-gray-500">Data toko yang tampil di invoice & halaman publik</p>
                </div>
            </div>

            <form @submit.prevent="saveToko" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Toko <span class="text-red-500">*</span></label>
                        <input type="text" x-model="form.company_name" placeholder="Novos Jersey"
                               class="w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a237e]/30 focus:border-[#1a237e]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                        <input type="text" x-model="form.company_phone" placeholder="0812-3456-7890"
                               class="w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a237e]/30 focus:border-[#1a237e]">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" x-model="form.company_email" placeholder="hello@novosjersey.com"
                           class="w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a237e]/30 focus:border-[#1a237e]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                    <textarea x-model="form.company_address" rows="3" placeholder="Jl. Contoh No. 1, Kota, Provinsi"
                              class="w-full rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1a237e]/30 focus:border-[#1a237e] resize-none"></textarea>
                </div>
                <div class="pt-1">
                    <button type="submit" :disabled="saving"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all active:scale-95 disabled:opacity-50 shadow-md shadow-[#1a237e]/20">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span x-show="!saving">Simpan Perubahan</span>
                        <span x-show="saving" x-cloak>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ======================== TAB TAMPILAN ======================== --}}
    <div x-show="tab==='tampilan'" x-transition x-cloak class="space-y-5">

        {{-- Mode Tema --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="sun-moon" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Mode Tema</h2>
                    <p class="text-xs text-gray-500">Pilih antara terang, gelap, atau ikuti sistem</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="m in themeOptions" :key="m.value">
                    <button @click="applyTheme(m.value)"
                        :class="appearance.theme===m.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5' : 'hover:bg-gray-50'"
                        class="flex flex-col items-center gap-2 p-4 rounded-xl border-2 border-transparent transition-all">
                        <div class="w-12 h-8 rounded-lg overflow-hidden border border-gray-200 shadow-sm" :class="m.preview + ' theme-preview-box'"></div>
                        <span class="text-xs font-semibold text-gray-700" x-text="m.label"></span>
                        <div x-show="appearance.theme===m.value" class="w-4 h-4 bg-[#1a237e] rounded-full flex items-center justify-center">
                            <i data-lucide="check" class="w-2.5 h-2.5 text-white"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Color Palette / Scheme --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-pink-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="swatch-book" class="w-5 h-5 text-pink-500"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Color Palette</h2>
                    <p class="text-xs text-gray-500">Pilih kombinasi warna untuk seluruh elemen tampilan</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <template x-for="scheme in colorSchemes" :key="scheme.name">
                    <button @click="applyScheme(scheme)"
                        :class="appearance.scheme===scheme.name ? 'ring-2 ring-offset-2 ring-gray-400' : 'hover:scale-105'"
                        class="relative rounded-xl p-3 border border-gray-100 bg-white transition-all shadow-sm group">
                        <div class="flex gap-1 mb-2">
                            <div class="h-6 flex-1 rounded-md" :style="'background:'+scheme.primary"></div>
                            <div class="h-6 flex-1 rounded-md" :style="'background:'+scheme.secondary"></div>
                            <div class="h-6 flex-1 rounded-md" :style="'background:'+scheme.accent"></div>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 text-center" x-text="scheme.name"></p>
                        <div x-show="appearance.scheme===scheme.name"
                             class="absolute top-1.5 right-1.5 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                            <i data-lucide="check" class="w-2.5 h-2.5 text-white"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Custom Color --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="pipette" class="w-5 h-5 text-orange-500"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Warna Kustom</h2>
                    <p class="text-xs text-gray-500">Atur warna primary & secondary secara manual</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Primary (Navbar/Tombol)</label>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="color" x-model="appearance.primary"
                                   @input="applyCustomColors()"
                                   class="w-12 h-12 rounded-xl cursor-pointer border-2 border-gray-200 p-0.5">
                        </div>
                        <div>
                            <div class="text-sm font-mono font-semibold text-gray-800" x-text="appearance.primary"></div>
                            <div class="text-xs text-gray-400">Warna utama</div>
                        </div>
                        <div class="flex-1 h-10 rounded-xl shadow-sm" :style="'background:'+appearance.primary"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warna Secondary</label>
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="color" x-model="appearance.secondary"
                                   @input="applyCustomColors()"
                                   class="w-12 h-12 rounded-xl cursor-pointer border-2 border-gray-200 p-0.5">
                        </div>
                        <div>
                            <div class="text-sm font-mono font-semibold text-gray-800" x-text="appearance.secondary"></div>
                            <div class="text-xs text-gray-400">Warna pendukung</div>
                        </div>
                        <div class="flex-1 h-10 rounded-xl shadow-sm" :style="'background:'+appearance.secondary"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Font Size --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="type" class="w-5 h-5 text-teal-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Ukuran Font</h2>
                    <p class="text-xs text-gray-500">Sesuaikan ukuran teks untuk kenyamanan membaca</p>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-3">
                <template x-for="fs in fontSizes" :key="fs.value">
                    <button @click="applyFontSize(fs.value)"
                        :class="appearance.fontSize===fs.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'hover:bg-gray-50 text-gray-600'"
                        class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 border-transparent transition-all">
                        <span class="font-bold" :style="'font-size:'+fs.preview+'px'" x-text="'Aa'"></span>
                        <span class="text-xs font-semibold" x-text="fs.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Button Style --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="square" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Gaya Tombol</h2>
                    <p class="text-xs text-gray-500">Pilih tampilan tombol yang digunakan di seluruh sistem</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="bs in buttonStyles" :key="bs.value">
                    <button @click="applyButtonStyle(bs.value)"
                        :class="appearance.buttonStyle===bs.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5' : 'hover:bg-gray-50'"
                        class="flex flex-col items-center gap-3 p-5 rounded-xl border-2 border-transparent transition-all">
                        <div class="px-5 py-2 text-sm font-semibold text-white" :class="bs.previewClass" :style="'background:'+appearance.primary">
                            Simpan
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800" x-text="bs.label"></p>
                            <p class="text-xs text-gray-400" x-text="bs.desc"></p>
                        </div>
                    </button>
                </template>
            </div>
        </div>

        {{-- Rounded --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="circle-dashed" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Sudut Komponen</h2>
                    <p class="text-xs text-gray-500">Atur tingkat kelengkungan sudut elemen UI</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="r in roundedOptions" :key="r.value">
                    <button @click="applyRounded(r.value)"
                        :class="appearance.rounded===r.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5' : 'hover:bg-gray-50'"
                        class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-transparent transition-all">
                        <div class="w-12 h-8 bg-gray-300" :style="'border-radius:'+r.px+'px'"></div>
                        <span class="text-xs font-semibold text-gray-700" x-text="r.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Kepadatan Tata Letak --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="layout-grid" class="w-5 h-5 text-blue-500"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Kepadatan Tata Letak</h2>
                    <p class="text-xs text-gray-500">Atur kerapatan dan jarak elemen (padding/margin) di seluruh sistem</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="d in densities" :key="d.value">
                    <button @click="applyDensity(d.value)"
                        :class="appearance.density===d.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'hover:bg-gray-50 text-gray-600'"
                        class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 border-transparent transition-all">
                        <span class="text-sm font-bold" x-text="d.label"></span>
                        <span class="text-[10px] text-gray-400" x-text="d.desc"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Pilihan Gaya Font --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="languages" class="w-5 h-5 text-indigo-500"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Gaya Font (Tipografi)</h2>
                    <p class="text-xs text-gray-500">Pilih jenis huruf yang digunakan di seluruh panel</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <template x-for="f in fontOptions" :key="f.value">
                    <button @click="applyFontFamily(f.value)"
                        :class="appearance.fontFamily===f.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'hover:bg-gray-50 text-gray-600'"
                        class="flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 border-transparent transition-all"
                        :style="'font-family: ' + f.family + ', sans-serif;'">
                        <span class="text-lg font-bold" x-text="'Novos Web'"></span>
                        <span class="text-xs font-semibold" x-text="f.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Preview & Reset --}}
        <div class="flex items-center justify-between">
            <button @click="resetAppearance()"
                class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset ke Default
            </button>
            <button @click="saveAppearance()"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-all shadow-md shadow-[#1a237e]/20 active:scale-95">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Terapkan Tampilan
            </button>
        </div>
    </div>

    {{-- ======================== TAB PANDUAN ======================== --}}
    <div x-show="tab==='panduan'" x-transition x-cloak class="space-y-5">

        {{-- Header --}}
        <div class="glass-card rounded-2xl p-7 panduan-header-bg">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-[#1a237e] rounded-2xl flex items-center justify-center shadow-lg shadow-[#1a237e]/20">
                    <i data-lucide="book-open" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Panduan Pengguna</h2>
                    <p class="text-sm text-gray-500">Pelajari semua fitur dan cara penggunaan panel internal Novos</p>
                </div>
            </div>
        </div>

        {{-- Daftar Isi --}}
        <div class="glass-card rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="list" class="w-5 h-5 text-green-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Daftar Isi</h2>
                    <p class="text-xs text-gray-500">Navigasi cepat ke bagian panduan yang diinginkan</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                <template x-for="(item, index) in panduanMenu" :key="index">
                    <a @click.prevent="scrollTo('panduan-'+item.id)" href="#panduan-"+item.id
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 transition-colors group">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0"
                            :style="'background:'+item.color">
                            <span x-text="index+1"></span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-[#1a237e]" x-text="item.label"></p>
                            <p class="text-xs text-gray-400 truncate" x-text="item.desc"></p>
                        </div>
                    </a>
                </template>
            </div>
        </div>

        {{-- Konten Panduan --}}
        <template x-for="(item, index) in panduanMenu" :key="index">
            <div :id="'panduan-'+item.id" class="glass-card rounded-2xl p-7 scroll-mt-24">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shrink-0 shadow-md"
                        :style="'background:'+item.color">
                        <i :data-lucide="item.icon" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-base font-bold text-gray-900" x-text="item.label"></h3>
                            <span class="text-[10px] font-semibold px-2 py-1 rounded-full shrink-0 text-white"
                                :style="'background:'+item.color" x-text="item.badge"></span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5" x-text="item.desc"></p>
                    </div>
                </div>
                <div class="mt-5 space-y-4">
                    <template x-for="(section, si) in item.sections" :key="si">
                        <div class="panduan-section-card p-4 rounded-xl border border-gray-100">
                            <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-5 h-5 rounded-md flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                    :style="'background:'+item.color" x-text="si+1"></span>
                                <span x-text="section.title"></span>
                            </h4>
                            <div class="mt-2 text-sm text-gray-600 leading-relaxed space-y-2" x-html="section.body"></div>
                        </div>
                    </template>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button @click="scrollTo('panduan-'+panduanMenu[Math.max(0,index-1)].id)" x-show="index>0"
                        class="text-xs font-semibold text-gray-500 hover:text-[#1a237e] flex items-center gap-1">
                        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Sebelumnya
                    </button>
                    <button @click="scrollTo('panduan-'+panduanMenu[Math.min(panduanMenu.length-1,index+1)].id)" x-show="index<panduanMenu.length-1"
                        class="text-xs font-semibold text-gray-500 hover:text-[#1a237e] flex items-center gap-1 ml-auto">
                        Selanjutnya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
        </template>

        {{-- Footer Panduan --}}
        <div class="glass-card rounded-2xl p-6 panduan-header-bg text-center">
            <i data-lucide="heart" class="w-8 h-8 text-red-400 mx-auto mb-2"></i>
            <p class="text-sm text-gray-600">Terima kasih telah menggunakan Novos Web. <br> Tim Novos selalu siap membantu jika Anda mengalami kendala.</p>
        </div>
    </div>
</div>

<script>
function settingApp() {
    const DEFAULT_APPEARANCE = {
        theme: 'light',
        scheme: 'Novos',
        primary: '#1a237e',
        secondary: '#3949ab',
        fontSize: 'md',
        buttonStyle: 'flat',
        rounded: 'xl',
        density: 'comfortable',
        fontFamily: 'poppins',
    };

    return {
        tab: 'toko',
        saving: false,

        form: {
            company_name: '',
            company_phone: '',
            company_email: '',
            company_address: '',
        },

        appearance: { ...DEFAULT_APPEARANCE },

        themeOptions: [
            { value: 'light', label: 'Terang', preview: 'bg-white' },
            { value: 'dark',  label: 'Gelap',  preview: 'bg-gray-900' },
            { value: 'auto',  label: 'Otomatis (Sistem)', preview: 'bg-gradient-to-r from-white to-gray-900' },
        ],

        colorSchemes: [
            { name: 'Novos',    primary: '#1a237e', secondary: '#3949ab', accent: '#e8eaf6' },
            { name: 'Ocean',    primary: '#0277bd', secondary: '#0288d1', accent: '#e1f5fe' },
            { name: 'Forest',   primary: '#2e7d32', secondary: '#388e3c', accent: '#e8f5e9' },
            { name: 'Sunset',   primary: '#bf360c', secondary: '#d84315', accent: '#fbe9e7' },
            { name: 'Purple',   primary: '#6a1b9a', secondary: '#7b1fa2', accent: '#f3e5f5' },
            { name: 'Teal',     primary: '#00695c', secondary: '#00796b', accent: '#e0f2f1' },
            { name: 'Rose',     primary: '#c62828', secondary: '#e53935', accent: '#ffebee' },
            { name: 'Slate',    primary: '#37474f', secondary: '#455a64', accent: '#eceff1' },
        ],

        fontSizes: [
            { value: 'sm',  label: 'Kecil',   preview: 11 },
            { value: 'md',  label: 'Sedang',  preview: 14 },
            { value: 'lg',  label: 'Besar',   preview: 17 },
            { value: 'xl',  label: 'Ekstra',  preview: 21 },
        ],

        buttonStyles: [
            { value: 'flat',    label: 'Flat / Modern', desc: 'Datar & bersih',  previewClass: 'rounded-lg' },
            { value: '3d',      label: '3D Effect',     desc: 'Tampak timbul',   previewClass: 'rounded-lg shadow-[0_4px_0_rgba(0,0,0,0.3)] translate-y-0 active:translate-y-1' },
            { value: 'outline', label: 'Outline',       desc: 'Hanya garis tepi', previewClass: 'rounded-lg !bg-transparent border-2 !text-[var(--color-primary)]' },
        ],

        roundedOptions: [
            { value: 'none', label: 'Kotak',   px: 0  },
            { value: 'sm',   label: 'Kecil',   px: 4  },
            { value: 'xl',   label: 'Rounded', px: 12 },
        ],

        densities: [
            { value: 'compact', label: 'Compact', desc: 'Jarak padat, hemat ruang' },
            { value: 'comfortable', label: 'Comfortable', desc: 'Jarak sedang, bersih' },
            { value: 'spacious', label: 'Spacious', desc: 'Jarak luas, lega' },
        ],

        fontOptions: [
            { value: 'poppins', label: 'Poppins', family: 'Poppins' },
            { value: 'inter', label: 'Inter', family: 'Inter' },
            { value: 'outfit', label: 'Outfit', family: 'Outfit' },
        ],

        panduanMenu: [
            {
                id: 'dashboard',
                label: 'Dashboard',
                desc: 'Halaman utama ringkasan aktivitas',
                icon: 'layout-dashboard',
                color: '#1a237e',
                badge: 'Utama',
                sections: [
                    { title: 'Sekilas Dashboard', body: 'Dashboard adalah halaman pertama yang Anda lihat setelah login. Menampilkan ringkasan statistik penting seperti jumlah pesanan baru, total pendapatan hari ini, pesanan dalam proses, dan grafik tren. Cocok untuk memantau kondisi bisnis secara cepat.' },
                    { title: 'Kartu Statistik', body: 'Di bagian atas terdapat 4 kartu utama: <strong>Pesanan Baru</strong> (jumlah pesanan hari ini), <strong>Pendapatan</strong> (total pemasukan), <strong>Dalam Proses</strong> (yang sedang dikerjakan), dan <strong>Total Pelanggan</strong>. Setiap kartu memiliki ikon dan warna berbeda.' },
                    { title: 'Grafik & Tabel', body: 'Dashboard juga menampilkan grafik tren pesanan 7 hari terakhir dan tabel pesanan terbaru. Klik tombol <strong>"Lihat Detail"</strong> pada pesanan untuk membuka halaman detail pesanan.' },
                ]
            },
            {
                id: 'summary',
                label: 'Summary',
                desc: 'Rekap data & analisis mendalam',
                icon: 'pie-chart',
                color: '#0288d1',
                badge: 'Analisis',
                sections: [
                    { title: 'Fungsi Summary', body: 'Summary menyajikan rekap data yang lebih mendalam dibanding Dashboard. Anda bisa melihat distribusi status pesanan dalam bentuk diagram lingkaran (pie chart), performa tim per role, serta tren pendapatan bulanan.' },
                    { title: 'Filter Data', body: 'Gunakan dropdown filter periode (hari ini, minggu ini, bulan ini, kustom) untuk mempersempit data. Summary otomatis memperbarui grafik dan tabel sesuai filter yang dipilih.' },
                    { title: 'Ekspor Data', body: 'Klik tombol <strong>Ekspor</strong> untuk mengunduh data summary dalam format CSV atau Excel. Berguna untuk pelaporan manajemen.' },
                ]
            },
            {
                id: 'daftar-pesanan',
                label: 'Daftar Pesanan',
                desc: 'Kelola semua pesanan pelanggan',
                icon: 'shopping-bag',
                color: '#e65100',
                badge: 'Utama',
                sections: [
                    { title: 'Melihat Daftar Pesanan', body: 'Halaman ini menampilkan semua pesanan dari pelanggan. Setiap baris menampilkan nomor pesanan, nama pelanggan, status, total harga, dan tanggal. Gunakan kolom pencarian untuk mencari berdasarkan nomor pesanan atau nama.' },
                    { title: 'Filter Status', body: 'Gunakan tab filter di atas tabel untuk menyaring pesanan berdasarkan status: <strong>Semua</strong>, <strong>Menunggu Validasi</strong>, <strong>Proses Design</strong>, <strong>Produksi</strong>, <strong>Selesai</strong>, atau <strong>Dibatalkan</strong>.' },
                    { title: 'Detail & Aksi', body: 'Klik nomor pesanan untuk membuka halaman detail. Di halaman detail Anda bisa: melihat data pemesan, chat dengan pelanggan, memperbarui status, dan validasi pesanan. Tombol aksi cepat juga tersedia langsung dari tabel.' },
                    { title: 'Validasi Pesanan', body: 'Pesanan baru berstatus <strong>Menunggu Validasi</strong>. Admin harus melakukan validasi dengan mengecek data pemesan, memastikan desain bisa dikerjakan, lalu klik <strong>Validasi Pesanan</strong>. Sistem akan mengirim notifikasi ke pelanggan.' },
                    { title: 'Assign Petugas', body: 'Setelah divalidasi, Anda bisa menugaskan (assign) pesanan ke tim Design atau Produksi. Gunakan dropdown <strong>Assign ke</strong> di halaman detail untuk memilih petugas.' },
                ]
            },
            {
                id: 'chat',
                label: 'Chat',
                desc: 'Komunikasi dengan pelanggan',
                icon: 'message-circle',
                color: '#7b1fa2',
                badge: 'Komunikasi',
                sections: [
                    { title: 'Fitur Chat', body: 'Halaman Chat memungkinkan Anda berkomunikasi langsung dengan pelanggan terkait pesanan. Chat bersifat per-pesanan, sehingga riwayat percakapan tersimpan rapi.' },
                    { title: 'Mengirim Pesan', body: 'Klik percakapan dari daftar di sidebar kiri, ketik pesan di kolom bawah, lalu tekan Enter atau klik ikon kirim. Pelanggan akan menerima notifikasi jika Anda mengirim pesan baru.' },
                    { title: 'Status Dibaca', body: 'Pesan yang sudah dibaca oleh pelanggan akan ditandai dengan centang biru. Anda juga bisa melihat kapan terakhir pelanggan online.' },
                ]
            },
            {
                id: 'design',
                label: 'Design',
                desc: 'Manajemen proses desain jersey',
                icon: 'pen-tool',
                color: '#2e7d32',
                badge: 'Produksi',
                sections: [
                    { title: 'Tugas Design', body: 'Halaman Design menampilkan semua pesanan yang sudah divalidasi dan menunggu proses design. Setiap card menampilkan informasi pesanan, catatan desain dari pelanggan, dan file referensi.' },
                    { title: 'Upload Desain', body: 'Setelah mendesain, upload hasil desain dengan mengklik tombol <strong>Upload Desain</strong>. Pilih file gambar (format PNG/JPG maks 5MB). Pelanggan akan mendapat notifikasi untuk review.' },
                    { title: 'Update Status', body: 'Gunakan tombol status untuk memperbarui progress: <strong>Menunggu Desain</strong> → <strong>Desain Selesai</strong> → <strong>Revisi</strong> (jika pelanggan minta revisi) → <strong>ACC</strong> (jika sudah disetujui).' },
                ]
            },
            {
                id: 'produksi',
                label: 'Produksi',
                desc: 'Manajemen proses produksi',
                icon: 'scissors',
                color: '#bf360c',
                badge: 'Produksi',
                sections: [
                    { title: 'Daftar Produksi', body: 'Halaman ini berisi semua pesanan yang sudah ACC desain dan siap diproduksi. Ditampilkan dalam bentuk card dengan informasi: nomor pesanan, jenis jersey, ukuran, dan tenggat waktu.' },
                    { title: 'Update Progress', body: 'Tim produksi dapat memperbarui status: <strong>Belum Dikerjakan</strong> → <strong>Dalam Produksi</strong> → <strong>Siap Kirim</strong> → <strong>Selesai</strong>. Setiap perubahan status akan mencatat waktu dan pencetusnya.' },
                    { title: 'Catatan Produksi', body: 'Anda bisa menambahkan catatan produksi seperti bahan yang digunakan, kendala teknis, atau catatan khusus. Catatan ini ikut dalam riwayat pesanan.' },
                ]
            },
            {
                id: 'daily-mental-check',
                label: 'Daily Mental Check',
                desc: 'Cek kesehatan mental harian tim',
                icon: 'heart',
                color: '#e91e63',
                badge: 'Karyawan',
                sections: [
                    { title: 'Apa Itu Daily Mental Check?', body: 'Fitur ini adalah bentuk perhatian Novos terhadap kesehatan mental tim. Setiap hari, Anda akan mengisi cek singkat tentang kondisi mood, energi, dan tingkat stres. Data bersifat rahasia dan hanya dilihat oleh Anda dan Super Admin.' },
                    { title: 'Cara Mengisi', body: 'Pilih emoji yang mewakili perasaan Anda, lalu isi tingkat energi (skala 1-10) dan tingkat stres (skala 1-10). Tambahkan catatan jika perlu, lalu klik <strong>Simpan</strong>. Hanya perlu 1 menit!' },
                    { title: 'Riwayat & Laporan', body: 'Anda bisa melihat riwayat cek harian Anda sendiri. Super Admin dapat melihat laporan agregat tim untuk memantau kesejahteraan karyawan secara umum.' },
                ]
            },
            {
                id: 'laporan',
                label: 'Laporan',
                desc: 'Cetak & ekspor laporan bisnis',
                icon: 'file-text',
                color: '#37474f',
                badge: 'Analisis',
                sections: [
                    { title: 'Jenis Laporan', body: 'Halaman Laporan menyediakan beberapa jenis laporan: <strong>Laporan Pesanan</strong> (rekap semua pesanan per periode), <strong>Laporan Keuangan</strong> (pendapatan, biaya, laba), dan <strong>Laporan Produksi</strong> (produktivitas tim).' },
                    { title: 'Filter & Preview', body: 'Pilih rentang tanggal dan jenis laporan, lalu klik <strong>Tampilkan</strong> untuk melihat preview. Data akan muncul dalam bentuk tabel yang bisa diurutkan.' },
                    { title: 'Ekspor Laporan', body: 'Setelah preview, Anda bisa mengekspor laporan ke dalam format: <strong>CSV</strong> (buka di Excel), <strong>Excel</strong> (.xlsx), atau <strong>PDF</strong> (cetak/arsip). Klik tombol format yang diinginkan di bagian atas.' },
                ]
            },
            {
                id: 'kelola-produk',
                label: 'Kelola Produk',
                desc: 'Atur katalog produk jersey',
                icon: 'package',
                color: '#00695c',
                badge: 'Master Data',
                sections: [
                    { title: 'Daftar Produk', body: 'Halaman ini menampilkan semua produk jersey yang dijual di katalog publik. Setiap produk memiliki foto, nama, kategori, harga, dan status featured (unggulan).' },
                    { title: 'Tambah Produk Baru', body: 'Klik tombol <strong>Tambah Produk</strong> di bagian atas. Isi nama produk, pilih kategori, upload foto (maks 2MB, format JPG/PNG/WebP), dan masukkan harga. Klik <strong>Simpan</strong> untuk menerbitkan produk.' },
                    { title: 'Edit & Hapus', body: 'Klik ikon pensil untuk mengedit produk, atau ikon tong sampah untuk menghapus. Produk yang dihapus tidak bisa dikembalikan. Gunakan toggle <strong>Featured</strong> untuk menampilkan produk di halaman utama katalog.' },
                ]
            },
            {
                id: 'kategori',
                label: 'Kategori',
                desc: 'Kelola kategori produk',
                icon: 'folder-tree',
                color: '#f57c00',
                badge: 'Master Data',
                sections: [
                    { title: 'Manajemen Kategori', body: 'Kategori digunakan untuk mengelompokkan produk (misal: Jersey Sepak Bola, Jersey Basket, Custom Design). Halaman ini menampilkan daftar semua kategori dalam bentuk tabel.' },
                    { title: 'Tambah Kategori', body: 'Klik <strong>Tambah Kategori</strong>, masukkan nama kategori dan deskripsi singkat. Kategori akan langsung muncul di katalog publik dan bisa dipilih saat menambah produk.' },
                    { title: 'Edit & Hapus', body: 'Kategori bisa diedit atau dihapus. Perhatian: menghapus kategori akan membuat produk di dalamnya menjadi tidak berkategori. Sebaiknya pindahkan produk ke kategori lain terlebih dahulu.' },
                ]
            },
            {
                id: 'kelola-pengguna',
                label: 'Kelola Pengguna',
                desc: 'Manajemen akun staf internal',
                icon: 'users',
                color: '#4a148c',
                badge: 'Admin',
                sections: [
                    { title: 'Daftar Pengguna', body: 'Halaman ini menampilkan semua akun staf internal. Setiap baris menampilkan nama, email, role, dan status aktif. Hanya Super Admin dan Manager yang bisa mengakses halaman ini.' },
                    { title: 'Tambah Pengguna Baru', body: 'Klik <strong>Tambah Pengguna</strong>. Isi nama, email, password, dan pilih role (Super Admin, Manager, Admin, Design, atau Produksi). Password akan digunakan untuk login pertama.' },
                    { title: 'Edit Role & Hapus', body: 'Klik ikon pensil untuk mengubah data atau role pengguna. Klik ikon tong sampah untuk menonaktifkan akun. Sebaiknya jangan menghapus akun yang masih memiliki data transaksi.' },
                ]
            },
            {
                id: 'notifikasi',
                label: 'Notifikasi',
                desc: 'Pusat notifikasi & pemberitahuan',
                icon: 'bell',
                color: '#1565c0',
                badge: 'Sistem',
                sections: [
                    { title: 'Cara Kerja Notifikasi', body: 'Notifikasi muncul ketika ada kejadian penting: pesanan baru, perubahan status, pesan baru dari pelanggan, atau pengingat tugas. Nomor badge merah di ikon bell menunjukkan jumlah notifikasi belum dibaca.' },
                    { title: 'Dropdown Notifikasi', body: 'Klik ikon bell di pojok kanan atas untuk melihat 5 notifikasi terbaru. Klik <strong>Lihat Semua</strong> untuk membuka halaman notifikasi lengkap. Klik notifikasi untuk menandainya sebagai sudah dibaca.' },
                    { title: 'Tandai Dibaca', body: 'Di halaman Notifikasi, Anda bisa menandai semua sebagai sudah dibaca dengan sekali klik. Notifikasi yang sudah dibaca akan tampil lebih pudar.' },
                ]
            },
            {
                id: 'pengaturan',
                label: 'Pengaturan',
                desc: 'Konfigurasi toko & tampilan',
                icon: 'settings',
                color: '#607d8b',
                badge: 'Sistem',
                sections: [
                    { title: 'Tab Toko', body: 'Berisi informasi dasar toko seperti nama, telepon, email, dan alamat. Data ini muncul di invoice dan halaman publik. Klik <strong>Simpan Perubahan</strong> setelah mengedit.' },
                    { title: 'Tab Tampilan', body: 'Kustomisasi tampilan panel internal: tema (terang/gelap/otomatis), color palette, ukuran font, gaya tombol, sudut komponen, kepadatan tata letak, font family, efek glassmorphism, dan efek transisi halaman. Semua tersimpan otomatis di browser Anda.' },
                    { title: 'Tab Panduan', body: 'Anda sedang membacanya! Panduan ini berisi penjelasan lengkap semua fitur. Gunakan tombol <strong>Mulai Tutorial</strong> untuk panduan interaktif.' },
                ]
            },
        ],

        scrollTo(id) {
            var el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        init() {
            this.form.company_name    = @json($settings['company_name'] ?? '');
            this.form.company_phone   = @json($settings['company_phone'] ?? '');
            this.form.company_email   = @json($settings['company_email'] ?? '');
            this.form.company_address = @json($settings['company_address'] ?? '');

            const saved = localStorage.getItem('novos_appearance');
            if (saved) {
                try { this.appearance = { ...DEFAULT_APPEARANCE, ...JSON.parse(saved) }; } catch(e) {}
            }
            
            // Adjust defaults for dark mode if using defaults
            let isDark = this.appearance.theme === 'dark' || (this.appearance.theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                if (this.appearance.scheme === 'Novos' || this.appearance.primary === '#1a237e') {
                    this.appearance.scheme = 'Ocean';
                    this.appearance.primary = '#0277bd';
                    this.appearance.secondary = '#0288d1';
                }
            } else {
                if (this.appearance.scheme === 'Ocean' || this.appearance.primary === '#0277bd') {
                    this.appearance.scheme = 'Novos';
                    this.appearance.primary = '#1a237e';
                    this.appearance.secondary = '#3949ab';
                }
            }

            this.applyAll();
            this.$nextTick(() => lucide.createIcons({ icons: window.lucide.icons }));
            const mq = window.matchMedia('(prefers-color-scheme: dark)');
            mq.addEventListener('change', () => {
                if (this.appearance.theme === 'auto') {
                    this._applyTheme('auto');
                    let isDarkNow = mq.matches;
                    if (isDarkNow) {
                        if (this.appearance.scheme === 'Novos' || this.appearance.primary === '#1a237e') {
                            this.appearance.scheme = 'Ocean';
                            this.appearance.primary = '#0277bd';
                            this.appearance.secondary = '#0288d1';
                            this._applyColors('#0277bd', '#0288d1');
                        }
                    } else {
                        if (this.appearance.scheme === 'Ocean' || this.appearance.primary === '#0277bd') {
                            this.appearance.scheme = 'Novos';
                            this.appearance.primary = '#1a237e';
                            this.appearance.secondary = '#3949ab';
                            this._applyColors('#1a237e', '#3949ab');
                        }
                    }
                }
            });
        },

        async saveToko() {
            this.saving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.pengaturan.update") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (data.success) Notify.success(data.message);
                else Notify.error(data.message || 'Gagal menyimpan.');
            } catch(e) { Notify.error('Terjadi kesalahan server.'); }
            finally { this.saving = false; }
        },

        applyTheme(val) {
            this.appearance.theme = val;
            this._applyTheme(val);
            
            // Auto update default colors if they are on default scheme
            let isDark = val === 'dark' || (val === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                if (this.appearance.scheme === 'Novos' || this.appearance.primary === '#1a237e') {
                    this.appearance.scheme = 'Ocean';
                    this.appearance.primary = '#0277bd';
                    this.appearance.secondary = '#0288d1';
                    this._applyColors('#0277bd', '#0288d1');
                }
            } else {
                if (this.appearance.scheme === 'Ocean' || this.appearance.primary === '#0277bd') {
                    this.appearance.scheme = 'Novos';
                    this.appearance.primary = '#1a237e';
                    this.appearance.secondary = '#3949ab';
                    this._applyColors('#1a237e', '#3949ab');
                }
            }
        },

        _applyTheme(val) {
            const root = document.documentElement;
            let isDark = val === 'dark' || (val === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            root.setAttribute('data-theme', isDark ? 'dark' : 'light');
            document.body.classList.toggle('theme-dark', isDark);
        },

        applyScheme(scheme) {
            this.appearance.scheme    = scheme.name;
            this.appearance.primary   = scheme.primary;
            this.appearance.secondary = scheme.secondary;
            this._applyColors(scheme.primary, scheme.secondary);
        },

        applyCustomColors() {
            this.appearance.scheme = 'custom';
            this._applyColors(this.appearance.primary, this.appearance.secondary);
        },

        _applyColors(primary, secondary) {
            document.documentElement.style.setProperty('--color-primary', primary);
            document.documentElement.style.setProperty('--color-secondary', secondary);
            var hex = primary.replace('#', '');
            if (hex.length === 3) {
                hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
            }
            var r = parseInt(hex.substring(0,2), 16);
            var g = parseInt(hex.substring(2,4), 16);
            var b = parseInt(hex.substring(4,6), 16);
            if (!isNaN(r) && !isNaN(g) && !isNaN(b)) {
                document.documentElement.style.setProperty('--color-primary-rgb', r + ',' + g + ',' + b);
            }
        },

        applyFontSize(val) {
            this.appearance.fontSize = val;
            const map = { sm: '13px', md: '15px', lg: '17px', xl: '19px' };
            document.documentElement.style.setProperty('--font-size-base', map[val] || '15px');
        },

        applyButtonStyle(val) {
            this.appearance.buttonStyle = val;
            document.documentElement.setAttribute('data-btn-style', val);
        },

        applyRounded(val) {
            this.appearance.rounded = val;
            const map = { none: '0px', sm: '6px', xl: '12px', full: '9999px' };
            document.documentElement.style.setProperty('--radius-base', map[val] || '12px');
        },

        applyDensity(val) {
            this.appearance.density = val;
            document.documentElement.setAttribute('data-density', val);
        },

        applyFontFamily(val) {
            this.appearance.fontFamily = val;
            const fontMap = { poppins: "'Poppins'", inter: "'Inter'", outfit: "'Outfit'" };
            document.documentElement.style.setProperty('--font-family-base', fontMap[val] || "'Poppins'");
        },

        applyAll() {
            this._applyTheme(this.appearance.theme);
            this._applyColors(this.appearance.primary, this.appearance.secondary);
            this.applyFontSize(this.appearance.fontSize);
            this.applyButtonStyle(this.appearance.buttonStyle);
            this.applyRounded(this.appearance.rounded);
            this.applyDensity(this.appearance.density || 'comfortable');
            this.applyFontFamily(this.appearance.fontFamily || 'poppins');
        },

        saveAppearance() {
            localStorage.setItem('novos_appearance', JSON.stringify(this.appearance));
            this.applyAll();
            Notify.success('Tampilan berhasil diterapkan & disimpan.');
        },

        resetAppearance() {
            this.appearance = { ...DEFAULT_APPEARANCE };
            localStorage.removeItem('novos_appearance');
            this.applyAll();
            Notify.info('Tampilan direset ke default.');
        },
    };
}
</script>
@endsection
