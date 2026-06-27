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
            <div class="grid grid-cols-4 gap-3">
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

        {{-- Efek Transisi --}}
        <div class="bg-white shadow-sm rounded-2xl p-7">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-cyan-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="move" class="w-5 h-5 text-cyan-600"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Efek Transisi</h2>
                    <p class="text-xs text-gray-500">Pilih efek transisi halaman untuk seluruh menu internal</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <template x-for="t in transitionEffects" :key="t.value">
                    <button @click="applyTransition(t.value)"
                        :class="appearance.transition===t.value ? 'ring-2 ring-[#1a237e] bg-[#1a237e]/5' : 'hover:bg-gray-50'"
                        class="flex flex-col items-center gap-2.5 p-4 rounded-xl border-2 border-transparent transition-all">
                        <div class="w-full h-14 rounded-lg overflow-hidden border border-gray-100 bg-gradient-to-br from-[#1a237e]/5 to-purple-50 flex items-center justify-center" x-html="t.preview"></div>
                        <span class="text-xs font-semibold text-gray-700" x-text="t.label"></span>
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
        transition: 'fade',
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
            { value: 'full', label: 'Bulat',   px: 24 },
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

        transitionEffects: [
            { value: 'fade',        label: 'Fade',       preview: '<div class="w-8 h-8 rounded-lg bg-[#1a237e]/20 animate-pulse"></div>' },
            { value: 'slide-up',    label: 'Slide Up',   preview: '<svg class="w-6 h-6 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>' },
            { value: 'slide-down',  label: 'Slide Down', preview: '<svg class="w-6 h-6 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>' },
            { value: 'zoom-in',     label: 'Zoom In',    preview: '<svg class="w-6 h-6 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>' },
            { value: 'flip',        label: 'Flip',       preview: '<svg class="w-6 h-6 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' },
            { value: 'elastic',     label: 'Elastic',    preview: '<svg class="w-6 h-6 text-[#1a237e]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="7 13 12 18 17 13"/><polyline points="7 6 12 11 17 6"/></svg>' },
        ],

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
            this.$nextTick(() => lucide.createIcons());
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

        applyTransition(val) {
            this.appearance.transition = val;
            document.documentElement.setAttribute('data-transition', val);
        },

        applyAll() {
            this._applyTheme(this.appearance.theme);
            this._applyColors(this.appearance.primary, this.appearance.secondary);
            this.applyFontSize(this.appearance.fontSize);
            this.applyButtonStyle(this.appearance.buttonStyle);
            this.applyRounded(this.appearance.rounded);
            this.applyDensity(this.appearance.density || 'comfortable');
            this.applyFontFamily(this.appearance.fontFamily || 'poppins');
            this.applyTransition(this.appearance.transition || 'fade');
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
