@extends('layouts.customer')
@php use Illuminate\Support\Str; @endphp
@section('title', $product->name . ' — Novos')

@section('content')
<div x-data="produkDetail()" class="min-h-screen bg-gray-50/50 py-8">
  <div class="max-w-6xl mx-auto px-4">
    
    {{-- Breadcrumbs --}}
    <div class="mb-6 flex items-center justify-between">
      <div class="text-xs text-gray-400 flex items-center gap-1.5">
        <a href="{{ route('katalog') }}" class="hover:text-[#1a237e] transition-colors">Katalog</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
        @if($product->category)
          <a href="{{ route('katalog', ['kategori' => Str::slug($product->category->name)]) }}" class="hover:text-[#1a237e] transition-colors">{{ $product->category->name }}</a>
          <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
        @endif
        <span class="text-[#1a237e] font-semibold truncate max-w-xs">{{ $product->name }}</span>
      </div>
      
      <a href="{{ route('katalog') }}" class="text-xs font-semibold text-[#1a237e] hover:underline flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Katalog
      </a>
    </div>

    {{-- Main Product Card --}}
    <div class="bg-white border border-gray-100 shadow-sm overflow-hidden p-6 md:p-8">
      <div class="grid lg:grid-cols-12 gap-8 lg:gap-12">

        {{-- LEFT COLUMN: Photo Swipe Gallery (5 Cols) --}}
        <div class="lg:col-span-5 space-y-4">
          <div class="relative bg-gray-50 border border-gray-100 overflow-hidden aspect-[4/5] group">
            
            {{-- Sliding gallery --}}
            <div class="w-full h-full flex transition-transform duration-500 ease-out" 
                 :style="`transform: translateX(-${activeImg * 100}%)`"
                 @touchstart="touchStart($event)"
                 @touchend="touchEnd($event)">
              @if($imageUrl)
                <div class="w-full h-full shrink-0 flex items-center justify-center relative cursor-zoom-in" @click="openLightbox(0)">
                  <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
              @endif
              @if($imageBelakangUrl)
                <div class="w-full h-full shrink-0 flex items-center justify-center relative cursor-zoom-in" @click="openLightbox(1)">
                  <img src="{{ $imageBelakangUrl }}" alt="{{ $product->name }} Belakang" class="w-full h-full object-cover">
                </div>
              @endif
              @if(!$imageUrl && !$imageBelakangUrl)
                <div class="w-full h-full shrink-0 flex items-center justify-center bg-gray-100">
                  <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375 0 11-.75 0 .375 0 01.75 0z"/></svg>
                </div>
              @endif
            </div>

            {{-- Slider Controls --}}
            @if($imageUrl && $imageBelakangUrl)
              <button type="button" @click="prevSlide()"
                class="absolute left-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 backdrop-blur-sm shadow flex items-center justify-center text-gray-700 hover:bg-white transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
              </button>
              <button type="button" @click="nextSlide()"
                class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/80 backdrop-blur-sm shadow flex items-center justify-center text-gray-700 hover:bg-white transition-all opacity-0 group-hover:opacity-100">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
              </button>
            @endif

            {{-- Photo Indicator/Zoom Hint --}}
            <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white px-2 py-1 text-[10px] tracking-wide rounded uppercase flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
              Klik untuk Zoom
            </div>
          </div>

          {{-- Thumbnails --}}
          @if($imageUrl && $imageBelakangUrl)
            <div class="flex gap-2">
              <button @click="activeImg = 0"
                :class="activeImg === 0 ? 'border-2 border-[#1a237e] opacity-100' : 'border border-gray-200 opacity-60 hover:opacity-100'"
                class="w-16 h-20 bg-gray-50 overflow-hidden transition-all duration-200">
                <img src="{{ $imageUrl }}" class="w-full h-full object-cover" alt="Depan">
              </button>
              <button @click="activeImg = 1"
                :class="activeImg === 1 ? 'border-2 border-[#1a237e] opacity-100' : 'border border-gray-200 opacity-60 hover:opacity-100'"
                class="w-16 h-20 bg-gray-50 overflow-hidden transition-all duration-200">
                <img src="{{ $imageBelakangUrl }}" class="w-full h-full object-cover" alt="Belakang">
              </button>
            </div>
          @endif
        </div>

        {{-- RIGHT COLUMN: Product Info & Actions (7 Cols) --}}
        <div class="lg:col-span-7 flex flex-col gap-6">
          
          {{-- Title, Rating, Wishlist, and Share Row --}}
          <div>
            {{-- Category label --}}
            @if($product->category)
              <a href="{{ route('katalog', ['kategori' => Str::slug($product->category->name)]) }}" 
                 class="text-xs font-bold text-black uppercase tracking-wider inline-block mb-2 hover:underline">
                {{ $product->category->name }}
              </a>
            @else
              <span class="text-xs font-bold text-black uppercase tracking-wider inline-block mb-2">
                Katalog
              </span>
            @endif

            <h1 class="text-2xl md:text-3xl font-extrabold text-[#1a237e] leading-tight">{{ $product->name }}</h1>
            
            {{-- Display rating (no-click, read-only from database calculate) and share/wishlist (emoji only, no boxes) --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mt-3 py-1">
              
              {{-- Golden Stars Rating --}}
              <div class="flex items-center gap-2">
                <div class="flex items-center gap-0.5">
                  @php
                    $fullStars = floor($avgRating);
                    $halfStar = ($avgRating - $fullStars) >= 0.25 && ($avgRating - $fullStars) < 0.75 ? 1 : 0;
                    if (($avgRating - $fullStars) >= 0.75) {
                        $fullStars++;
                    }
                    $emptyStars = 5 - $fullStars - $halfStar;
                  @endphp
                  
                  {{-- Render full gold stars --}}
                  @for ($i = 0; $i < $fullStars; $i++)
                    <svg class="w-5 h-5 text-[#fbbf24] fill-[#fbbf24]" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  @endfor

                  {{-- Render half gold star overlay --}}
                  @if ($halfStar)
                    <div class="relative">
                      <svg class="w-5 h-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                      <div class="absolute top-0 left-0 w-1/2 overflow-hidden h-full">
                        <svg class="w-5 h-5 text-[#fbbf24] fill-[#fbbf24]" viewBox="0 0 20 20" fill="currentColor">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                      </div>
                    </div>
                  @endif

                  {{-- Render empty stars --}}
                  @for ($i = 0; $i < $emptyStars; $i++)
                    <svg class="w-5 h-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  @endfor
                </div>
                
                {{-- Average rating text & Shortcut Lihat Ulasan --}}
                <span class="text-xs text-gray-500 font-semibold">
                  <span x-text="avgRating > 0 ? parseFloat(avgRating).toFixed(1) : '0.0'"></span>
                  (<button type="button" @click="showReviewsModal = true" class="hover:text-[#1a237e] underline font-semibold transition-colors focus:outline-none">{{ $ratingCount }} ulasan</button>)
                </span>
              </div>

              {{-- Wishlist (Love) & Share (Emoji / Icon only, no box, no text) --}}
              <div class="flex items-center gap-3">
                {{-- Love Button --}}
                <button type="button" @click="toggleWishlist()"
                  class="p-1 text-gray-400 hover:text-red-500 transition-transform active:scale-90"
                  title="Simpan ke Wishlist">
                  <svg class="w-6 h-6 transition-colors duration-200" 
                       :class="wishlisted ? 'text-red-500 fill-red-500' : ''"
                       fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                  </svg>
                </button>

                {{-- Share Button (Lucide share-2) --}}
                <button type="button" @click="shareProduct()"
                  class="p-1 text-gray-400 hover:text-gray-600 transition-transform active:scale-90"
                  title="Bagikan Produk">
                  <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="18" cy="5" r="3"/>
                    <circle cx="6" cy="12" r="3"/>
                    <circle cx="18" cy="19" r="3"/>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                  </svg>
                </button>
              </div>

            </div>
          </div>

          {{-- Pricing --}}
          <div class="py-1">
            @if($product->price)
              <p class="text-3xl font-extrabold text-[#1a237e]">
                Rp {{ number_format($product->price, 0, ',', '.') }}<span class="text-sm font-medium text-gray-400"> / pcs</span>
              </p>
            @else
              <p class="text-lg text-gray-400">Hubungi Admin untuk penawaran harga</p>
            @endif
          </div>

          {{-- Specifications Section --}}
          @if($product->kerah || $product->bahan || $product->jenis_potongan || $product->lengan_jahitan)
            <div class="border border-gray-100 p-4 bg-gray-50/50">
              <h3 class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-3">Spesifikasi Produk</h3>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                @if($product->kerah)
                  <div>
                    <p class="text-gray-400 mb-1">Jenis Kerah</p>
                    <span class="font-bold text-gray-800">{{ $product->kerah }}</span>
                  </div>
                @endif
                @if($product->bahan)
                  <div>
                    <p class="text-gray-400 mb-1">Bahan Jersey</p>
                    <span class="font-bold text-gray-800">{{ $product->bahan }}</span>
                  </div>
                @endif
                @if($product->jenis_potongan)
                  <div>
                    <p class="text-gray-400 mb-1">Jenis Potongan</p>
                    <span class="font-bold text-gray-800">{{ $product->jenis_potongan }}</span>
                  </div>
                @endif
                @if($product->lengan_jahitan)
                  <div>
                    <p class="text-gray-400 mb-1">Model Lengan</p>
                    <span class="font-bold text-gray-800">{{ $product->lengan_jahitan }}</span>
                  </div>
                @endif
              </div>
            </div>
          @endif

          {{-- Scrollable Description Section (Transparent background, black text, scrollable container) --}}
          @if($product->description)
            <div>
              <h3 class="text-xs font-bold text-[#1a237e] uppercase tracking-wider mb-2">Deskripsi</h3>
              <div class="text-sm text-black leading-relaxed max-h-40 overflow-y-auto pr-3 scrollbar-thin scrollbar-thumb-gray-200 text-justify">
                {!! nl2br(e($product->description)) !!}
              </div>
            </div>
          @endif


          {{-- Size Selector (Resolved Active/Hover Bug & Square/No Rounding) --}}
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold text-[#1a237e] uppercase tracking-wider">Pilih Ukuran <span class="text-red-500">*</span></span>
              <button @click="showReferensiProduk = true" type="button" class="text-xs text-[#1a237e] font-semibold underline underline-offset-2 hover:text-[#283593]">Referensi Produk</button>
            </div>
            
            <div class="flex flex-wrap gap-2">
              <template x-for="s in sizes" :key="s">
                <button type="button" @click="selectedSize = s"
                  :class="selectedSize === s 
                    ? 'w-12 h-12 border-2 border-[#1a237e] bg-[#1a237e] text-white text-sm font-bold flex items-center justify-center cursor-pointer transition-all duration-150' 
                    : 'w-12 h-12 border border-gray-300 text-gray-700 hover:border-[#1a237e] hover:text-[#1a237e] hover:bg-blue-50/50 text-sm font-bold flex items-center justify-center cursor-pointer transition-all duration-150'"
                  x-text="s">
                </button>
              </template>
            </div>
            
            <p x-show="!selectedSize && submitted" class="text-xs text-red-500 mt-1.5 font-medium">Silakan pilih ukuran terlebih dahulu sebelum memesan.</p>
          </div>

          {{-- Quantity --}}
          <div>
            <span class="text-xs font-bold text-[#1a237e] uppercase tracking-wider block mb-2">Jumlah Pesanan</span>
            <div class="flex items-center gap-3">
              <div class="flex items-center border border-gray-300 bg-white">
                <button type="button" @click="qty = Math.max(minQty, qty - 1)"
                  class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors font-bold text-lg">−</button>
                <span x-text="qty" class="w-12 text-center font-bold text-gray-900 text-sm"></span>
                <button type="button" @click="qty++"
                  class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors font-bold text-lg">+</button>
              </div>
              <span class="text-xs text-gray-400 font-medium">Min. {{ $minQty }} pcs</span>
              @if($product->price)
                <span class="ml-auto text-sm font-bold text-[#1a237e]" x-text="'Subtotal: Rp ' + (qty * {{ (int)$product->price }}).toLocaleString('id-ID')"></span>
              @endif
            </div>
          </div>

          {{-- Action Buttons (Square / No melengkung) --}}
          <div class="flex flex-col sm:flex-row gap-3 pt-3">
            
            {{-- Tambah Keranjang: Square border, gray outline, no blue transition --}}
            <button type="button" @click="addToCart()"
              :disabled="cartLoading"
              class="flex-1 h-12 rounded-none border border-gray-300 bg-white text-gray-700 hover:border-gray-500 hover:bg-gray-50 font-bold transition-all duration-150 flex items-center justify-center gap-2">
              <svg x-show="!cartLoading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              <svg x-show="cartLoading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              <span x-text="cartLoading ? 'Menambahkan...' : 'Masukkan Keranjang'"></span>
            </button>

            {{-- Beli Sekarang: Square border, solid navy background --}}
            <button type="button" @click="buyNow()"
              class="flex-1 h-12 rounded-none bg-[#1a237e] hover:bg-[#283593] text-white font-bold transition-all duration-150 flex items-center justify-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              Beli Sekarang
            </button>
          </div>

          @if($product->production_days)
            <div class="flex items-center gap-2 text-xs text-gray-400">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              Estimasi produksi {{ $product->production_days }} hari kerja
            </div>
          @endif

        </div>

      </div>
    </div>

{{-- Shortcut: Lihat Ulasan --}}
<div class="max-w-6xl mx-auto px-4 mt-6 mb-10">
  <div class="flex items-center justify-between py-3 px-5 bg-white border border-gray-200">
    <div class="flex items-center gap-3">
      {{-- Stars summary --}}
      <div class="flex items-center gap-0.5">
        @for ($i = 0; $i < $fullStars; $i++)
          <svg class="w-4 h-4 text-[#fbbf24] fill-[#fbbf24]" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
        @endfor
        @if ($halfStar)
          <div class="relative w-4 h-4">
            <svg class="w-4 h-4 text-gray-300 fill-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            <div class="absolute top-0 left-0 w-1/2 overflow-hidden h-full">
              <svg class="w-4 h-4 text-[#fbbf24] fill-[#fbbf24]" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
          </div>
        @endif
        @for ($i = 0; $i < $emptyStars; $i++)
          <svg class="w-4 h-4 text-gray-300 fill-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        @endfor
      </div>
      <span class="text-sm font-bold text-[#1a237e]">{{ number_format($avgRating, 1) }}</span>
      <span class="text-xs text-gray-400">({{ $ratingCount }} ulasan)</span>
    </div>
    <button type="button" @click="showReviewsModal = true"
      class="flex items-center gap-1.5 text-sm font-semibold text-[#1a237e] hover:text-[#283593] transition-colors">
      Lihat Ulasan
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </button>
  </div>
</div>

{{-- Modal: Ulasan Produk --}}
<template x-teleport="body">
  <div x-show="showReviewsModal" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/45"
    @keydown.escape.window="showReviewsModal = false">
    <div x-show="showReviewsModal" x-transition.opacity class="fixed inset-0 bg-black/40" @click="showReviewsModal = false"></div>
    <div x-show="showReviewsModal"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 scale-95 translate-y-4"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 scale-100 translate-y-0"
      x-transition:leave-end="opacity-0 scale-95 translate-y-4"
      class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden relative z-10 flex flex-col max-h-[85vh]"
      @click.stop>

      {{-- Header --}}
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
          <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-[#fbbf24] fill-[#fbbf24]" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            Ulasan Produk
          </h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $product->name }}</p>
        </div>
        <div class="flex items-center gap-4">
          {{-- Rating summary in header --}}
          <div class="flex items-center gap-1.5">
            <span class="text-2xl font-extrabold text-[#1a237e]">{{ number_format($avgRating, 1) }}</span>
            <div class="flex flex-col">
              <div class="flex items-center gap-0.5">
                @for ($i = 1; $i <= 5; $i++)
                  <svg class="w-3.5 h-3.5 {{ $i <= round($avgRating) ? 'text-[#fbbf24] fill-[#fbbf24]' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                  </svg>
                @endfor
              </div>
              <span class="text-[10px] text-gray-400">{{ $ratingCount }} ulasan</span>
            </div>
          </div>
          <button @click="showReviewsModal = false"
            class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      {{-- Body --}}
      <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4 bg-gray-50/50">
        @if(count($reviews) > 0)
          @foreach($reviews as $r)
            <div class="p-5 rounded-xl border border-gray-200 bg-white flex flex-col gap-3 shadow-sm hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                  @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $r['rating'] ? 'text-[#fbbf24] fill-[#fbbf24]' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  @endfor
                </div>
                <span class="text-xs text-gray-400">{{ $r['date'] }}</span>
              </div>
              @if(!empty($r['comment']))
                <p class="text-sm text-gray-700 leading-relaxed font-medium">"{{ $r['comment'] }}"</p>
              @else
                <p class="text-sm text-gray-400 italic">Memberikan rating {{ $r['rating'] }} dari 5 bintang.</p>
              @endif
              <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <div class="w-8 h-8 rounded-full bg-[#e0f7fa] flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-[#00acc1]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm-7 8a7 7 0 0 1 14 0H5z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-xs font-bold text-gray-800">{{ $r['user'] }}</p>
                  <p class="text-[10px] text-gray-400">Customer Novos</p>
                </div>
              </div>
            </div>
          @endforeach
        @else
          <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
            <svg class="w-14 h-14 mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
            </svg>
            <p class="text-sm font-semibold text-gray-500">Belum ada ulasan</p>
            <p class="text-xs text-gray-400 mt-1">Jadilah yang pertama memberikan ulasan untuk produk ini.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</template>

{{-- Full Photo Swipe Lightbox with zoom and swipe --}}
<template x-teleport="body">
  <div x-show="lightboxOpen" x-cloak @click="lightboxOpen = false"
    class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center p-4 select-none"
    x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    {{-- Close Btn --}}
    <button @click.stop="lightboxOpen = false" class="absolute top-4 right-4 text-white/70 hover:text-white p-2 focus:outline-none transition-colors">
      <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- Main Lightbox Gallery --}}
    <div class="relative max-h-[85vh] max-w-[90vw] flex items-center justify-center" @click.stop>
      <img :src="lightboxSrc" :class="zoomed ? 'scale-150 cursor-zoom-out' : 'scale-100 cursor-zoom-in'" 
           class="max-h-[85vh] max-w-[90vw] object-contain transition-transform duration-300 ease-out shadow-2xl rounded"
           @click="zoomed = !zoomed">
    </div>

    {{-- Prev/Next Lightbox Controls --}}
    @if($imageUrl && $imageBelakangUrl)
      <button type="button" @click.stop="prevSlide(); lightboxSrc = images[activeImg]"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <button type="button" @click.stop="nextSlide(); lightboxSrc = images[activeImg]"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </button>
    @endif

    {{-- Zoom Hint bottom --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/60 text-xs tracking-wider uppercase font-medium bg-black/40 px-3 py-1.5 rounded-full">
      Klik gambar untuk zoom masuk/keluar
    </div>
  </div>
</template>

{{-- Referensi Produk Modal --}}
<template x-teleport="body">
  <div x-show="showReferensiProduk" x-cloak
    class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
    @click="showReferensiProduk = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0">
    <div @click.stop class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden relative flex flex-col max-h-[90vh]">
      {{-- Header --}}
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
        <div>
          <h3 class="text-base font-bold text-gray-900">Referensi Produk</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $product->name }} — {{ $product->jenis_potongan ?? 'REGULER' }}</p>
        </div>
        <button @click="showReferensiProduk = false"
          class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition-colors">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      {{-- Body: Image --}}
      <div class="p-6 overflow-y-auto flex items-center justify-center bg-gray-50/50 flex-1">
        <img src="{{ $referensiUkuranUrl }}" alt="Referensi Ukuran {{ $product->jenis_potongan ?? 'REGULER' }}"
          class="max-w-full max-h-[70vh] object-contain rounded-lg shadow-sm cursor-zoom-in"
          @click="openReferensiPhotoSwipe()">
      </div>
    </div>
  </div>
</template>

<script>
function produkDetail() {
    return {
        activeImg: 0,
        lightboxOpen: false,
        lightboxSrc: '',
        zoomed: false,
        sizes: ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
        selectedSize: null,
        qty: {{ $minQty }},
        minQty: {{ $minQty }},
        cartLoading: false,
        submitted: false,
        wishlisted: {{ $wishlisted ? 'true' : 'false' }},
        avgRating: {{ $avgRating }},
        ratingCount: {{ $ratingCount }},
        userRating: {{ $userRating }},
        isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
        showReviewsModal: false,
        showReferensiProduk: false,
        
        // Custom name and back number
        namesetName: '',
        namesetNumber: '',

        // Touch gallery vars
        touchStartX: 0,
        touchEndX: 0,

        images: [
            @if($imageUrl) '{{ $imageUrl }}', @endif
            @if($imageBelakangUrl) '{{ $imageBelakangUrl }}', @endif
        ],

        // Swipe controls for mobile
        touchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        touchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        },
        handleSwipe() {
            const threshold = 50;
            if (this.touchStartX - this.touchEndX > threshold) {
                this.nextSlide();
            } else if (this.touchEndX - this.touchStartX > threshold) {
                this.prevSlide();
            }
        },

        nextSlide() {
            if (this.images.length > 1) {
                this.activeImg = (this.activeImg + 1) % this.images.length;
            }
        },
        prevSlide() {
            if (this.images.length > 1) {
                this.activeImg = (this.activeImg - 1 + this.images.length) % this.images.length;
            }
        },

        openLightbox(idx) {
            this.activeImg = idx;
            this.lightboxSrc = this.images[idx] || '';
            this.zoomed = false;
            if (this.lightboxSrc) this.lightboxOpen = true;
        },

        openReferensiPhotoSwipe() {
            const src = '{{ $referensiUkuranUrl }}';
            const img = new Image();
            img.onload = function () {
                if (typeof window.openPhotoSwipe === 'function') {
                    window.openPhotoSwipe([{ src: src, width: img.naturalWidth, height: img.naturalHeight }], 0);
                }
            };
            img.src = src;
        },

        validate() {
            this.submitted = true;
            return !!this.selectedSize;
        },

        // Share product link
        shareProduct() {
            const el = document.createElement('textarea');
            el.value = window.location.href;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);

            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Tautan disalin ke papan klip!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        },

        // Wishlist handler
        async toggleWishlist() {
            if (!this.isLoggedIn) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Harap Login',
                        text: 'Silakan masuk ke akun Anda terlebih dahulu untuk menyimpan produk favorit.'
                    });
                }
                return;
            }

            try {
                const res = await fetch('{{ route("api.wishlist.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }}
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.wishlisted = data.wishlisted;
                    if (window.Swal) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: this.wishlisted ? 'Produk disimpan ke wishlist!' : 'Produk dihapus dari wishlist.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            } catch(e) {
                console.error(e);
            }
        },

        // Format nameset notes
        getNamesetNotes() {
            if (this.namesetName.trim() || this.namesetNumber.trim()) {
                let nameStr = this.namesetName.trim().toUpperCase() || '-';
                let numStr = this.namesetNumber.trim() || '-';
                return `Nameset: ${nameStr} (No. ${numStr})`;
            }
            return '';
        },

        // Add to Cart
        async addToCart() {
            if (!this.validate()) return;
            this.cartLoading = true;
            try {
                const res = await fetch('{{ route("cart.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        size: this.selectedSize,
                        qty: this.qty,
                        notes: this.getNamesetNotes()
                    }),
                });
                const data = await res.json();
                if (data.success) {
                    // Reload page to allow laravel-notify to flash from session
                    window.location.reload();
                } else {
                    if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.' });
                }
            } catch (e) {
                if (window.Swal) Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server.' });
            } finally {
                this.cartLoading = false;
            }
        },

        // Buy Now direct checkout
        buyNow() {
            if (!this.validate()) return;
            const state = {
                mode: 'katalog_direct',
                step: 2,
                subStep: 2,
                jenis: 'katalog',
                prioritas: 'normal',
                selectedAddressId: null,
                notes: this.getNamesetNotes(),
                katalogItem: {
                    product_id: {{ $product->id }},
                    name: '{{ addslashes($product->name) }}',
                    category: '{{ addslashes($product->category?->name ?? "Katalog") }}',
                    size: this.selectedSize,
                    qty: this.qty,
                    price: {{ (int)($product->price ?? 0) }},
                    image: '{{ $imageUrl ?? "" }}',
                    kerah: '{{ addslashes($product->kerah ?? "") }}',
                    bahan: '{{ addslashes($product->bahan ?? "") }}',
                    jenis_potongan: '{{ addslashes($product->jenis_potongan ?? "") }}',
                    lengan_jahitan: '{{ addslashes($product->lengan_jahitan ?? "") }}',
                }
            };
            localStorage.setItem('checkout_state', JSON.stringify(state));
            window.location.href = '{{ route("pemesanan") }}';
        },
    }
}
</script>
@endsection
