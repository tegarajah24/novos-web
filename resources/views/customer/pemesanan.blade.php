@extends('layouts.customer')

@section('content')
@auth
@php
    $produkData = $produk ? [
        'produk'  => $produk,
        'kategori' => $kategori,
        'harga'   => $harga,
        'gambar'  => $gambar,
    ] : null;
@endphp

<div class="max-w-5xl mx-auto px-4 py-8" x-data="pemesananForm({{ json_encode($produkData) }})">
    {{-- Header --}}
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Buat Pesanan</h1>
        <p class="text-gray-500 mt-1">Pesan jersey custom impianmu dalam 4 langkah mudah</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center mb-10 px-4">
        <template x-for="(s, index) in steps" :key="index">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div
                        :class="step >= index + 1 ? 'bg-blue-900 text-white' : 'bg-gray-200 text-gray-500'"
                        class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                    >
                        <svg x-show="step > index + 1" class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span x-show="step <= index + 1" x-text="index + 1"></span>
                    </div>
                    <span
                        :class="step >= index + 1 ? 'text-blue-900 font-semibold' : 'text-gray-400'"
                        class="text-xs mt-1.5 transition-colors hidden sm:block whitespace-nowrap"
                        x-text="s"
                    ></span>
                </div>
                <div
                    x-show="index < steps.length - 1"
                    :class="step > index + 1 ? 'bg-blue-900' : 'bg-gray-200'"
                    class="w-10 sm:w-16 md:w-24 h-0.5 mx-1 sm:mx-2 transition-colors"
                ></div>
            </div>
        </template>
    </div>

    {{-- Step 1: Pilih Jenis Pesanan --}}
    <div x-show="step === 1" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">
        <h2 class="text-lg font-semibold text-gray-900">Pilih Jenis Pesanan</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih jenis pesanan yang sesuai kebutuhan Anda</p>

        <div class="grid md:grid-cols-2 gap-6 mt-6">
            {{-- Jersey Custom --}}
            <div
                @click="jenis = 'custom'"
                :class="jenis === 'custom' ? 'border-blue-900 bg-blue-50 ring-2 ring-blue-900' : 'border-gray-200 hover:border-gray-300'"
                class="border-2 rounded-xl p-6 cursor-pointer transition-all duration-200 animate-fade-slide"
                style="animation-delay:0.1s"
            >
                <div :class="jenis === 'custom' ? 'bg-blue-900 text-white' : 'bg-gray-100 text-gray-400'" class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/></svg>
                </div>
                <h3 class="font-bold text-lg mt-4">Jersey Custom</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed">Desain jersey sepenuhnya sesuai keinginan Anda. Upload logo, pilih warna, dan tentukan detail desain sendiri.</p>
            </div>

            {{-- Produk Katalog --}}
            <div
                @click="jenis = 'katalog'"
                :class="jenis === 'katalog' ? 'border-blue-900 bg-blue-50 ring-2 ring-blue-900' : 'border-gray-200 hover:border-gray-300'"
                class="border-2 rounded-xl p-6 cursor-pointer transition-all duration-200 animate-fade-slide"
                style="animation-delay:0.2s"
            >
                <div :class="jenis === 'katalog' ? 'bg-blue-900 text-white' : 'bg-gray-100 text-gray-400'" class="w-14 h-14 rounded-xl flex items-center justify-center transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5A2.5 2.5 0 0 1 4 19.5Z"/><path d="M12 6v7l2-2 2 2V6"/></svg>
                </div>
                <h3 class="font-bold text-lg mt-4">Produk Katalog</h3>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed">Pilih dari koleksi desain yang sudah tersedia. Tinggal pilih ukuran dan jumlah pesanan.</p>
            </div>
        </div>

        <div class="flex justify-end mt-8">
            <button
                @click="if(jenis === 'katalog') window.location.href = '{{ route('katalog') }}'; else step = 2;"
                :disabled="!jenis"
                :class="jenis ? 'bg-blue-900 hover:bg-blue-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                class="text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2"
            >
                Selanjutnya
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>

    {{-- Step 2: Detail & Upload --}}
    <div x-show="step === 2" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">
        <h2 class="text-lg font-semibold text-gray-900">Detail & Upload</h2>
        <p class="text-sm text-gray-500 mt-1">Lengkapi informasi pesanan dan upload file desain Anda</p>

        {{-- Selected product from catalog --}}
        <template x-if="catalogProduct">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6 flex items-center gap-4">
                <img :src="catalogProduct.gambar || 'https://placehold.co/80x80/1a237e/ffffff?text=Jersey'" class="w-16 h-16 object-cover rounded-lg shrink-0">
                <div class="min-w-0">
                    <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">Produk dari Katalog</p>
                    <p class="font-semibold text-gray-900 truncate" x-text="catalogProduct.produk"></p>
                    <p class="text-sm text-gray-500" x-text="catalogProduct.kategori"></p>
                    <template x-if="catalogProduct.harga">
                        <p class="text-sm font-bold text-blue-900" x-text="'Rp ' + parseInt(catalogProduct.harga).toLocaleString('id-ID') + '/pcs'"></p>
                    </template>
                </div>
            </div>
        </template>

        <div class="space-y-5 mt-6">
            {{-- Row: Nama Tim + Ukuran --}}
            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Nama Tim / Event --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Tim / Event <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        x-model="form.team_name"
                        placeholder="Contoh: FC Harapan Jaya"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                    >
                </div>

                {{-- No Punggung --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No Punggung</label>
                    <input
                        type="text"
                        x-model="form.no_punggung"
                        placeholder="Contoh: 10, 7, 9"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                    >
                </div>

                {{-- Detail Sponsor --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Detail Sponsor</label>
                    <input
                        type="text"
                        x-model="form.detail_sponsor"
                        placeholder="Contoh: Logo sponsor di dada"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                    >
                </div>

                {{-- Ukuran (Qty per Ukuran) --}}
                <div x-data="{ showSizeGuide: false }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Ukuran (Qty per Ukuran)</label>
                        <button
                            type="button"
                            @click="showSizeGuide = true"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 border border-blue-200 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.4 2.4 0 0 1 0-3.4l2.6-2.6a2.4 2.4 0 0 1 3.4 0Z"/>
                                <path d="m14.5 12.5 2-2"/>
                                <path d="m11.5 9.5 2-2"/>
                                <path d="m8.5 6.5 2-2"/>
                                <path d="m17.5 15.5 2-2"/>
                            </svg>
                            Panduan Ukuran
                        </button>
                    </div>

                    {{-- Modal Panduan Ukuran --}}
                    <div
                        x-show="showSizeGuide"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        style="background: rgba(0,0,0,0.45);"
                        @click.self="showSizeGuide = false"
                    >
                        <div
                            x-show="showSizeGuide"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
                            @click.stop
                        >
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.4 2.4 0 0 1 0-3.4l2.6-2.6a2.4 2.4 0 0 1 3.4 0Z"/>
                                            <path d="m14.5 12.5 2-2"/>
                                            <path d="m11.5 9.5 2-2"/>
                                            <path d="m8.5 6.5 2-2"/>
                                            <path d="m17.5 15.5 2-2"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900">Panduan Ukuran Jersey</h3>
                                </div>
                                <button
                                    @click="showSizeGuide = false"
                                    class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-6 py-4 overflow-y-auto max-h-[70vh]">
                                <p class="text-xs text-gray-500 mb-4">Semua ukuran dalam satuan <strong>cm</strong>. Pilih ukuran yang sesuai dengan tubuh Anda.</p>
                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="bg-blue-900 text-white">
                                                <th class="px-4 py-3 text-left font-semibold">Ukuran</th>
                                                <th class="px-4 py-3 text-left font-semibold">Lebar Dada (cm)</th>
                                                <th class="px-4 py-3 text-left font-semibold">Lingkar Dada (cm)</th>
                                                <th class="px-4 py-3 text-left font-semibold">Panjang Baju (cm)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">XS</td>
                                                <td class="px-4 py-2.5 text-gray-700">44 – 46</td>
                                                <td class="px-4 py-2.5 text-gray-700">88 – 92</td>
                                                <td class="px-4 py-2.5 text-gray-700">64 – 66</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">S</td>
                                                <td class="px-4 py-2.5 text-gray-700">47 – 49</td>
                                                <td class="px-4 py-2.5 text-gray-700">94 – 98</td>
                                                <td class="px-4 py-2.5 text-gray-700">66 – 68</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">M</td>
                                                <td class="px-4 py-2.5 text-gray-700">50 – 52</td>
                                                <td class="px-4 py-2.5 text-gray-700">100 – 104</td>
                                                <td class="px-4 py-2.5 text-gray-700">69 – 71</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">L</td>
                                                <td class="px-4 py-2.5 text-gray-700">53 – 55</td>
                                                <td class="px-4 py-2.5 text-gray-700">106 – 110</td>
                                                <td class="px-4 py-2.5 text-gray-700">72 – 74</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">XL</td>
                                                <td class="px-4 py-2.5 text-gray-700">56 – 58</td>
                                                <td class="px-4 py-2.5 text-gray-700">112 – 116</td>
                                                <td class="px-4 py-2.5 text-gray-700">74 – 76</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">2XL / XXL</td>
                                                <td class="px-4 py-2.5 text-gray-700">59 – 61</td>
                                                <td class="px-4 py-2.5 text-gray-700">118 – 122</td>
                                                <td class="px-4 py-2.5 text-gray-700">76 – 78</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">3XL</td>
                                                <td class="px-4 py-2.5 text-gray-700">62 – 64</td>
                                                <td class="px-4 py-2.5 text-gray-700">124 – 128</td>
                                                <td class="px-4 py-2.5 text-gray-700">78 – 80</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">4XL</td>
                                                <td class="px-4 py-2.5 text-gray-700">65 – 67</td>
                                                <td class="px-4 py-2.5 text-gray-700">130 – 134</td>
                                                <td class="px-4 py-2.5 text-gray-700">80 – 82</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">5XL</td>
                                                <td class="px-4 py-2.5 text-gray-700">68 – 70</td>
                                                <td class="px-4 py-2.5 text-gray-700">136 – 140</td>
                                                <td class="px-4 py-2.5 text-gray-700">82 – 84</td>
                                            </tr>
                                            <tr class="hover:bg-blue-50 transition-colors">
                                                <td class="px-4 py-2.5 font-bold text-blue-900">6XL</td>
                                                <td class="px-4 py-2.5 text-gray-700">71 – 73</td>
                                                <td class="px-4 py-2.5 text-gray-700">142 – 146</td>
                                                <td class="px-4 py-2.5 text-gray-700">84 – 86</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-xs text-gray-400 mt-3">* Ukuran dapat sedikit berbeda tergantung bahan yang dipilih. Hubungi kami jika butuh konsultasi ukuran.</p>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                                <button
                                    @click="showSizeGuide = false"
                                    class="px-6 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors"
                                >
                                    Mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2">
                        <template x-for="size in ['XS', 'S', 'M', 'L', 'XL', '2XL / XXL', '3XL', '4XL', '5XL', '6XL']" :key="size">
                            <div class="text-center">
                                <span class="block text-xs font-medium text-gray-500 mb-1" x-text="size"></span>
                                <input
                                    type="number"
                                    x-model="form.ukuran[size]"
                                    min="0"
                                    class="w-full px-2 py-2 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow"
                                >
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-2 gap-6">
                {{-- Jenis Kerah --}}
                <div x-data="{ showCollarGuide: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Jenis Kerah <span class="text-red-500">*</span></label>
                        <button
                            type="button"
                            @click="showCollarGuide = true"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 border border-blue-200 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Detail Kerah
                        </button>
                    </div>
                    <select
                        x-model="form.kerah"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow bg-white"
                    >
                        <option value="">Pilih Jenis Kerah</option>
                        <option value="O-NECK V.1">O-NECK V.1</option>
                        <option value="O-NECK V.2">O-NECK V.2</option>
                        <option value="O-NECK V.3">O-NECK V.3</option>
                        <option value="O-NECK V.4">O-NECK V.4</option>
                        <option value="V-NECK V.5">V-NECK V.5</option>
                        <option value="V-NECK V.1">V-NECK V.1</option>
                        <option value="V-NECK V.2">V-NECK V.2</option>
                        <option value="V-NECK V.3">V-NECK V.3</option>
                        <option value="V-NECK V.4">V-NECK V.4</option>
                        <option value="V-NECK V.5">V-NECK V.5</option>
                        <option value="CLASSIC V.1">CLASSIC V.1</option>
                        <option value="CLASSIC V.2">CLASSIC V.2</option>
                        <option value="CLASSIC V.3">CLASSIC V.3</option>
                        <option value="CLASSIC V.4">CLASSIC V.4</option>
                        <option value="CLASSIC V.5">CLASSIC V.5</option>
                        <option value="V-NECK V3 TUMPUK">V-NECK V3 TUMPUK</option>
                        <option value="TIMNAS">TIMNAS</option>
                    </select>

                    {{-- Modal Detail Kerah --}}
                    <div
                        x-show="showCollarGuide"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        style="background: rgba(0,0,0,0.55);"
                        @click.self="showCollarGuide = false"
                    >
                        <div
                            x-show="showCollarGuide"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden"
                            @click.stop
                        >
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23Z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900">Detail Jenis Kerah Jersey</h3>
                                </div>
                                <button
                                    @click="showCollarGuide = false"
                                    class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-6 py-5 overflow-y-auto max-h-[75vh]">
                                <p class="text-xs text-gray-500 mb-4">Panduan referensi variasi desain kerah jersey. Pilih jenis kerah yang sesuai dengan selera Anda.</p>

                                {{-- Gambar Panduan Kerah --}}
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <img
                                        src="{{ asset('images/jersey_collar_guide.png') }}"
                                        alt="Panduan Desain Kerah Jersey"
                                        class="w-full h-auto object-contain"
                                    >
                                </div>



                                <p class="text-xs text-gray-400 mt-3">* Detail variasi spesifik dapat dikonsultasikan lebih lanjut dengan tim desain kami.</p>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                                <button
                                    @click="showCollarGuide = false"
                                    class="px-6 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors"
                                >
                                    Mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bahan Jersey --}}
                <div x-data="{ showBahanGuide: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Bahan Jersey <span class="text-red-500">*</span></label>
                        <button
                            type="button"
                            @click="showBahanGuide = true"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 border border-blue-200 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Detail Bahan
                        </button>
                    </div>
                    <select
                        x-model="form.bahan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow bg-white"
                    >
                        <option value="">Pilih Bahan Jersey</option>
                        <option value="BINTIK JARUM GRADE B">BINTIK JARUM GRADE B</option>
                        <option value="MILANO GRADE B">MILANO GRADE B</option>
                        <option value="BINTIK JARUM PREMIUM">BINTIK JARUM PREMIUM</option>
                        <option value="MILANO PREMIUM">MILANO PREMIUM</option>
                        <option value="RABBIT">RABBIT</option>
                        <option value="DROPPEDDLE">DROPPEDDLE</option>
                        <option value="SMASH">SMASH</option>
                        <option value="WAFFLE">WAFFLE</option>
                        <option value="EMBOSH">EMBOSH</option>
                        <option value="MICROCOOL">MICROCOOL</option>
                        <option value="JAQUARD AERO">JAQUARD AERO</option>
                        <option value="COTTON 24S">COTTON 24S</option>
                        <option value="COTTON 30S">COTTON 30S</option>
                        <option value="LOTTO">LOTTO</option>
                        <option value="PARASUT">PARASUT</option>
                        <option value="PUMA">PUMA</option>
                        <option value="ULTRALIGHT A">ULTRALIGHT A</option>
                        <option value="ULTRALIGHT B">ULTRALIGHT B</option>
                    </select>

                    {{-- Modal Detail Bahan --}}
                    <div
                        x-show="showBahanGuide"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        style="background: rgba(0,0,0,0.55);"
                        @click.self="showBahanGuide = false"
                    >
                        <div
                            x-show="showBahanGuide"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden"
                            @click.stop
                        >
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 16v-4"/>
                                            <path d="M12 8h.01"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">Jenis Bahan Jersey</h3>
                                </div>
                                <button
                                    @click="showBahanGuide = false"
                                    class="w-7 h-7 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-5 py-4 overflow-y-auto max-h-[65vh]">
                                <p class="text-xs text-gray-500 mb-3">Panduan referensi jenis bahan jersey yang tersedia. Pilih bahan yang sesuai dengan kebutuhan Anda.</p>
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <img
                                        src="{{ asset('images/Bahan Jersey.png') }}"
                                        alt="Jenis Bahan Jersey"
                                        class="w-full h-auto object-contain"
                                    >
                                </div>
                                <p class="text-xs text-gray-400 mt-3">* Konsultasikan pilihan bahan dengan tim kami jika butuh informasi lebih lanjut.</p>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-5 py-3.5 border-t border-gray-100 flex justify-end">
                                <button
                                    @click="showBahanGuide = false"
                                    class="px-5 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors"
                                >
                                    Mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jenis Potongan --}}
                <div x-data="{ showPotonganGuide: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-gray-700">Jenis Potongan <span class="text-red-500">*</span></label>
                        <button
                            type="button"
                            @click="showPotonganGuide = true"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700 border border-blue-200 bg-blue-50 hover:bg-blue-100 hover:border-blue-400 px-2.5 py-1 rounded-lg transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            Detail Potongan
                        </button>
                    </div>
                    <select
                        x-model="form.jenis_potongan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow bg-white"
                    >
                        <option value="">Pilih Jenis Potongan</option>
                        <option value="REGULER">REGULER</option>
                        <option value="SLIMFIT CEWE">SLIMFIT CEWE</option>
                        <option value="OVERSIZE">OVERSIZE</option>
                        <option value="TUNIK">TUNIK</option>
                        <option value="SLIM FIT UNISEX">SLIM FIT UNISEX</option>
                    </select>

                    {{-- Modal Detail Potongan --}}
                    <div
                        x-show="showPotonganGuide"
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        style="background: rgba(0,0,0,0.55);"
                        @click.self="showPotonganGuide = false"
                    >
                        <div
                            x-show="showPotonganGuide"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden"
                            @click.stop
                        >
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1e3a8a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 16v-4"/>
                                            <path d="M12 8h.01"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">Jenis Potongan Jersey</h3>
                                </div>
                                <button
                                    @click="showPotonganGuide = false"
                                    class="w-7 h-7 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-5 py-4 overflow-y-auto max-h-[65vh]">
                                <p class="text-xs text-gray-500 mb-3">Panduan referensi jenis-jenis potongan jersey yang tersedia.</p>
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm bg-gray-55">
                                    <img
                                        src="{{ asset('images/Jenis Potongan.png') }}"
                                        alt="Jenis Potongan Jersey"
                                        class="w-full h-auto object-contain"
                                    >
                                </div>
                                <p class="text-xs text-gray-400 mt-3">* Konsultasikan pilihan potongan dengan tim kami jika Anda membutuhkan penyesuaian khusus.</p>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-5 py-3.5 border-t border-gray-100 flex justify-end">
                                <button
                                    @click="showPotonganGuide = false"
                                    class="px-5 py-2 bg-blue-900 hover:bg-blue-800 text-white text-sm font-semibold rounded-lg transition-colors"
                                >
                                    Mengerti
                                </button>
                            </div>
                        </div>
                </div>

                {{-- Model Lengan & Jahitan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Model Lengan & Jahitan <span class="text-red-500">*</span></label>
                    <select
                        x-model="form.lengan_jahitan"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow bg-white"
                    >
                        <option value="">Pilih Model Lengan & Jahitan</option>
                        <option value="REGULER OVERDECK">REGULER OVERDECK</option>
                        <option value="REGULER PAKAI MANSET">REGULER PAKAI MANSET</option>
                        <option value="RAGLAN A OVERDECK">RAGLAN A OVERDECK</option>
                        <option value="RAGLAN A PAKAI MANSET">RAGLAN A PAKAI MANSET</option>
                        <option value="RAGLAN B OVERDECK">RAGLAN B OVERDECK</option>
                        <option value="RAGLAN B PAKAI MANSET">RAGLAN B PAKAI MANSET</option>
                    </select>
                </div>
            </div>

            {{-- Catatan Desain --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Desain</label>
                <textarea
                    x-model="form.catatan"
                    rows="4"
                    placeholder="Deskripsi keseluruhan desain Anda..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-900 focus:border-blue-900 outline-none transition-shadow resize-none"
                ></textarea>
            </div>
        </div>

        {{-- Upload Section --}}
        <div class="grid lg:grid-cols-2 gap-6 mt-8">
            {{-- Upload Logo Tim --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Logo Tim</label>
                <div
                    @dragover.prevent="dragOver = true"
                    @dragleave.prevent="dragOver = false"
                    @drop.prevent="handleDrop($event)"
                    @click="document.getElementById('logoInput').click()"
                    :class="dragOver ? 'border-blue-900 bg-blue-50' : 'border-gray-300'"
                    class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer min-h-[180px] flex items-center justify-center"
                >
                    <template x-if="uploads.length === 0">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="text-gray-500 text-sm font-medium">Drag & drop atau klik untuk upload</p>
                            <p class="text-gray-400 text-xs mt-1">PNG, JPG, AI (max. 10MB)</p>
                        </div>
                    </template>
                    <template x-if="uploads.length > 0">
                        <div class="w-full">
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="(file, i) in uploads" :key="i">
                                    <div class="relative group">
                                        <img :src="file.url" class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200">
                                        <button
                                            @click.stop="uploads.splice(i, 1)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 hover:bg-red-600 transition-all"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <p class="text-sm text-blue-900 font-medium mt-2 hover:underline">Tambah file lagi</p>
                        </div>
                    </template>
                </div>
                <input type="file" id="logoInput" accept="image/png,image/jpeg,image/jpg,image/svg+xml" class="hidden" @change="handleFileSelect($event)">
            </div>

            {{-- Referensi Desain --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Desain</label>
                <div
                    @dragover.prevent="dragOverRef = true"
                    @dragleave.prevent="dragOverRef = false"
                    @drop.prevent="handleDropRef($event)"
                    @click="document.getElementById('refInput').click()"
                    :class="dragOverRef ? 'border-blue-900 bg-blue-50' : 'border-gray-300'"
                    class="border-2 border-dashed rounded-xl p-6 text-center transition-colors cursor-pointer min-h-[180px] flex items-center justify-center"
                >
                    <template x-if="refUploads.length === 0">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-300 mb-3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="text-gray-500 text-sm font-medium">Drag & drop atau klik untuk upload</p>
                            <p class="text-gray-400 text-xs mt-1">Multiple files (diperbolehkan)</p>
                        </div>
                    </template>
                    <template x-if="refUploads.length > 0">
                        <div class="w-full">
                            <div class="grid grid-cols-2 gap-3">
                                <template x-for="(file, i) in refUploads" :key="i">
                                    <div class="relative group">
                                        <img :src="file.url" class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200">
                                        <button
                                            @click.stop="refUploads.splice(i, 1)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 hover:bg-red-600 transition-all"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <p class="text-sm text-blue-900 font-medium mt-2 hover:underline">Tambah file lagi</p>
                        </div>
                    </template>
                </div>
                <input type="file" id="refInput" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp" multiple class="hidden" @change="handleFileSelectRef($event)">
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <button
                @click="step = 1"
                class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors"
            >
                Kembali
            </button>
            <button
                @click="step = 3"
                :disabled="!validateStep2"
                :class="validateStep2 ? 'bg-blue-900 hover:bg-blue-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                class="text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2"
            >
                Selanjutnya
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>

    {{-- Step 3: Prioritas & Pembayaran --}}
    <div x-show="step === 3" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-6">
        <h2 class="text-lg font-semibold text-gray-900">Prioritas &amp; Pembayaran</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih prioritas pengerjaan dan metode pembayaran</p>

        <div class="grid lg:grid-cols-2 gap-8 mt-6">
            {{-- Left: Prioritas Pengerjaan --}}
            <div>
                <h3 class="text-base font-semibold text-gray-800 mb-4">Prioritas Pengerjaan</h3>
                <div class="space-y-3">
                    <template x-for="(p, i) in prioritasOptions" :key="i">
                        <div
                            @click="prioritas = p.value"
                            :class="prioritas === p.value ? 'border-blue-900 bg-blue-50 ring-2 ring-blue-900' : 'border-gray-200 hover:border-gray-300'"
                            class="border-2 rounded-xl p-4 cursor-pointer transition-all animate-fade-slide"
                            :style="`animation-delay: ${0.05 + i * 0.07}s`"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <div :class="prioritas === p.value ? 'bg-blue-900 border-blue-900' : 'border-gray-300'" class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors shrink-0">
                                        <div x-show="prioritas === p.value" class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900" x-text="p.label"></span>
                                </div>
                                <span class="text-sm font-bold text-gray-900" x-text="p.harga"></span>
                            </div>
                            <p class="text-sm text-gray-500 ml-8" x-text="'Estimasi ' + p.desc"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: Ringkasan + Pembayaran --}}
            <div class="space-y-6">
                {{-- Ringkasan Pesanan --}}
                <div class="bg-white border border-gray-200 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jenis</span>
                            <span class="font-medium text-gray-900" x-text="jenis === 'custom' ? 'Custom' : 'Katalog'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tim</span>
                            <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">No Punggung</span>
                            <span class="font-medium text-gray-900" x-text="form.no_punggung || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Sponsor</span>
                            <span class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kerah</span>
                            <span class="font-medium text-gray-900" x-text="form.kerah || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bahan</span>
                            <span class="font-medium text-gray-900" x-text="form.bahan || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jenis Potongan</span>
                            <span class="font-medium text-gray-900" x-text="form.jenis_potongan || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Lengan & Jahitan</span>
                            <span class="font-medium text-gray-900" x-text="form.lengan_jahitan || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jumlah</span>
                            <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                        </div>
                        <template x-for="(qty, size) in form.ukuran" :key="size">
                            <div x-show="parseInt(qty) > 0" class="flex justify-between pl-4 text-xs text-gray-400">
                                <span class="font-medium" x-text="'Ukuran ' + size"></span>
                                <span x-text="parseInt(qty) + ' pcs'"></span>
                            </div>
                        </template>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Harga dasar</span>
                            <span class="font-medium text-gray-900" x-text="formatRupiah(hargaDasar)"></span>
                        </div>
                        <template x-if="biayaPrioritas > 0">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Biaya prioritas</span>
                                <span class="font-medium text-gray-900" x-text="formatRupiah(biayaPrioritas)"></span>
                            </div>
                        </template>
                    </div>
                    <hr class="my-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700 font-semibold">Total</span>
                        <span class="text-xl font-bold text-blue-900" x-text="formatRupiah(estimasiTotal)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between mt-8">
            <button
                @click="step = 2"
                class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors"
            >
                Kembali
            </button>
            <button
                @click="submitOrder"
                :disabled="!validateStep3 || loading"
                :class="(validateStep3 && !loading) ? 'bg-blue-900 hover:bg-blue-800 cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                class="text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2"
            >
                <span x-show="!loading" class="inline-flex items-center gap-2">
                    Konfirmasi & Bayar
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </span>
                <span x-show="loading" class="inline-flex items-center gap-2">
                    <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Memproses...
                </span>
            </button>
        </div>
    </div>

    {{-- Step 4: Konfirmasi --}}
    <div x-show="step === 4" x-cloak
         x-transition:enter="transition ease-out duration-400"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="text-center max-w-lg mx-auto py-4">
            {{-- Green Checkmark --}}
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center animate-[scaleIn_0.5s_ease-out]">
                    <svg class="w-10 h-10 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
            </div>

            <h2 class="text-xl font-bold text-green-600 mb-2 animate-fade-slide" style="animation-delay:0.15s">Pesanan Berhasil Dibuat!</h2>
            <p class="text-gray-500 text-sm mb-8 animate-fade-slide" style="animation-delay:0.3s">Tim kami akan segera memproses pesanan Anda. Pantau status pesanan melalui halaman Tracking.</p>

            {{-- Order ID --}}
            <div class="mb-6 animate-fade-slide" style="animation-delay:0.45s">
                <p class="text-sm text-gray-500 mb-1">Order ID:</p>
                <div class="flex items-center justify-center gap-2">
                    <span class="text-lg font-mono font-bold text-gray-900 tracking-wider" x-text="orderNumber" id="orderNumber"></span>
                    <button
                        @click="copyOrderNumber"
                        class="text-gray-400 hover:text-blue-900 transition-colors p-1.5 rounded-lg hover:bg-blue-50"
                        title="Salin nomor pesanan"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    </button>
                </div>
            </div>

            {{-- Ringkasan Pesanan --}}
            <div class="bg-white border border-gray-200 rounded-xl p-5 mb-8 text-left max-w-sm mx-auto animate-fade-slide" style="animation-delay:0.6s">
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tim</span>
                        <span class="font-medium text-gray-900" x-text="form.team_name || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">No Punggung</span>
                        <span class="font-medium text-gray-900" x-text="form.no_punggung || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sponsor</span>
                        <span class="font-medium text-gray-900" x-text="form.detail_sponsor || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kerah</span>
                        <span class="font-medium text-gray-900" x-text="form.kerah || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bahan</span>
                        <span class="font-medium text-gray-900" x-text="form.bahan || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jenis Potongan</span>
                        <span class="font-medium text-gray-900" x-text="form.jenis_potongan || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Lengan & Jahitan</span>
                        <span class="font-medium text-gray-900" x-text="form.lengan_jahitan || '-'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Jumlah</span>
                        <span class="font-medium text-gray-900" x-text="totalQty + ' pcs'"></span>
                    </div>
                    <template x-for="(qty, size) in form.ukuran" :key="size">
                        <div x-show="parseInt(qty) > 0" class="flex justify-between pl-4 text-xs text-gray-400">
                            <span class="font-medium" x-text="'Ukuran ' + size"></span>
                            <span x-text="parseInt(qty) + ' pcs'"></span>
                        </div>
                    </template>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Prioritas</span>
                        <span class="font-medium text-gray-900 capitalize" x-text="prioritasText"></span>
                    </div>
                </div>
                <hr class="my-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 font-semibold">Total</span>
                    <span class="text-lg font-bold text-blue-900" x-text="formatRupiah(estimasiTotal)"></span>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-center animate-fade-slide" style="animation-delay:0.75s">
                <a href="{{ route('tracking') }}" class="px-8 py-3 bg-blue-900 text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors text-center">
                    Tracking Pesanan
                </a>
                <a href="{{ route('beranda') }}" class="px-8 py-3 border-2 border-gray-300 text-gray-600 rounded-lg font-semibold hover:border-gray-400 hover:text-gray-800 transition-colors text-center">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

@keyframes scaleIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-fade-slide {
    opacity: 0;
    animation: fadeSlideUp 0.5s ease-out forwards;
}
</style>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function pemesananForm(catalogProduct = null) {
    return {
        step: 1,
        steps: ['Pilih Jenis', 'Detail & Upload', 'Prioritas & Bayar', 'Konfirmasi'],
        jenis: null,
        catalogProduct: catalogProduct,
        form: {
            team_name: '',
            no_punggung: '',
            detail_sponsor: '',
            kerah: '',
            bahan: '',
            jenis_potongan: '',
            lengan_jahitan: '',

            warna_utama: '#1e3a5f',
            warna_sekunder: '#ffffff',
            catatan: '',
            ukuran: { XS: 0, S: 0, M: 0, L: 0, XL: 0, '2XL / XXL': 0, '3XL': 0, '4XL': 0, '5XL': 0, '6XL': 0 }
        },
        prioritas: 'normal',
        pembayaran: 'midtrans',
        uploads: [],
        orderNumber: null,
        dragOver: false,
        dragOverRef: false,
        loading: false,

        init() {
            if (this.catalogProduct) {
                this.jenis = 'katalog';
                if (this.catalogProduct.harga) {
                    this.basePricePerPcs = parseInt(this.catalogProduct.harga);
                }
                this.step = 2;
            }
        },
        refUploads: [],
        prioritasOptions: [
            { value: 'normal', label: 'Normal', desc: '7\u201314 hari kerja', harga: 'Gratis' },
            { value: 'express', label: 'Express', desc: '3\u20136 hari kerja', harga: '+Rp50.000' },
            { value: 'super_express', label: 'Super Express', desc: '1\u20132 hari kerja', harga: '+Rp150.000' }
        ],
        basePricePerPcs: 85000,

        get totalQty() {
            let sizes = this.form.ukuran;
            return Object.values(sizes).reduce((a, b) => a + (parseInt(b) || 0), 0);
        },

        get prioritasText() {
            const p = this.prioritasOptions.find(o => o.value === this.prioritas);
            return p ? p.label : '';
        },

        get hargaDasar() {
            return this.totalQty * this.basePricePerPcs;
        },

        get biayaPrioritas() {
            if (this.prioritas === 'express') return 50000;
            if (this.prioritas === 'super_express') return 150000;
            return 0;
        },

        get estimasiTotal() {
            return this.hargaDasar + this.biayaPrioritas;
        },

        get validateStep2() {
            return this.form.team_name.trim() !== '' && this.form.kerah !== '' && this.form.bahan !== '' && this.form.jenis_potongan !== '' && this.form.lengan_jahitan !== '';
        },

        get validateStep3() {
            return this.pembayaran !== null;
        },

        formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        },

        handleDrop(event) {
            this.dragOver = false;
            this.processFiles(event.dataTransfer.files);
        },

        handleFileSelect(event) {
            this.processFiles(event.target.files);
            event.target.value = '';
        },

        handleDropRef(event) {
            this.dragOverRef = false;
            this.processRefFiles(event.dataTransfer.files);
        },

        handleFileSelectRef(event) {
            this.processRefFiles(event.target.files);
            event.target.value = '';
        },

        processRefFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.match('image.*')) return;
                if (file.size > 10 * 1024 * 1024) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.refUploads.push({ file, url: e.target.result, name: file.name });
                };
                reader.readAsDataURL(file);
            });
        },

        processFiles(files) {
            Array.from(files).forEach(file => {
                if (!file.type.match('image.*')) return;
                if (file.size > 5 * 1024 * 1024) return;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploads.push({ file, url: e.target.result, name: file.name });
                };
                reader.readAsDataURL(file);
            });
        },

        submitOrder() {
            if (this.loading) return;
            this.loading = true;

            const payload = {
                team_name: this.form.team_name,
                no_punggung: this.form.no_punggung,
                detail_sponsor: this.form.detail_sponsor,
                kerah: this.form.kerah,
                bahan: this.form.bahan,
                jenis_potongan: this.form.jenis_potongan,
                lengan_jahitan: this.form.lengan_jahitan,
                catatan: this.form.catatan,
                ukuran: this.form.ukuran,
                total_qty: this.totalQty || this.form.jumlah,
                jumlah: this.form.jumlah,
                prioritas: this.prioritas,
                pembayaran: this.pembayaran,
                warna_utama: this.form.warna_utama,
                warna_sekunder: this.form.warna_sekunder,
            };

            fetch('{{ route('pesan.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload),
            })
            .then(res => {
                if (!res.ok) {
                    return res.text().then(text => { throw new Error(text.substring(0, 150)); });
                }
                return res.json();
            })
            .then(data => {
                if (!data.success) throw new Error('Gagal membuat pesanan');
                this.orderNumber = data.orderNumber;
                this.step = 4;
                this.loading = false;
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Oops...', text: err.message || 'Terjadi kesalahan' });
                this.loading = false;
            });
        },

        copyOrderNumber() {
            navigator.clipboard.writeText(this.orderNumber).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: 'Nomor pesanan berhasil disalin',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    }
}
</script>
@else
<div class="max-w-5xl mx-auto px-4 py-16">
    <div class="text-center">
        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-blue-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Masuk untuk Memesan</h1>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Silakan login atau daftar akun terlebih dahulu untuk dapat membuat pesanan jersey custom.</p>
        <a href="{{ request()->fullUrlWithQuery(['auth' => 'login']) }}"
           class="inline-flex items-center gap-2 px-8 py-3 bg-blue-900 text-white text-sm font-semibold rounded-lg hover:bg-blue-800 transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            Login / Daftar
        </a>
        <p class="mt-6 text-sm text-gray-400">
            <a href="{{ route('beranda') }}" class="text-blue-900 hover:underline">Kembali ke Beranda</a>
        </p>
    </div>
</div>
@endauth
@endsection
