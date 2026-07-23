@extends('layouts.customer')
@section('title', 'Panduan Ukuran Jersey — Novos')
@section('meta_description', 'Panduan lengkap ukuran jersey custom Novos. Temukan ukuran yang tepat untuk jersey futsal, basket, voli, dan olahraga lainnya.')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
  <div class="max-w-3xl mx-auto px-4">
    <div class="bg-white border border-gray-100 p-8 shadow-sm">
      <h1 class="text-2xl font-extrabold text-[#1a237e] uppercase tracking-wider mb-2 text-center">Panduan Ukuran Jersey</h1>
      <p class="text-sm text-gray-500 text-center mb-8">Pilih ukuran yang paling sesuai dengan tubuh Anda untuk kenyamanan maksimal saat beraktivitas.</p>

      <div x-data="{ tab: 'atasan' }" class="space-y-6">
        {{-- Tabs --}}
        <div class="flex gap-1 bg-gray-100 p-1">
          <button @click="tab = 'atasan'"
            :class="tab === 'atasan' ? 'bg-white text-[#1a237e] shadow-sm' : 'text-gray-500'"
            class="flex-1 py-2.5 text-xs font-bold uppercase tracking-wider transition-all">Atasan</button>
          <button @click="tab = 'bawahan'"
            :class="tab === 'bawahan' ? 'bg-white text-[#1a237e] shadow-sm' : 'text-gray-500'"
            class="flex-1 py-2.5 text-xs font-bold uppercase tracking-wider transition-all">Bawahan</button>
        </div>

        {{-- Atasan --}} 
        <div x-show="tab === 'atasan'" class="space-y-4">
          <div class="overflow-x-auto">
            <table class="table table-compact w-full text-xs text-gray-700">
              <thead class="bg-[#e8eaf6] text-[#1a237e]">
                <tr>
                  <th class="font-bold py-3 text-left">Ukuran</th>
                  <th class="font-bold py-3 text-left">Lingkar Dada (LD)</th>
                  <th class="font-bold py-3 text-left">Panjang Baju (PB)</th>
                  <th class="font-bold py-3 text-left">Lingkar Lengan</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr><td class="font-bold text-[#1a237e] py-3">S</td><td class="py-3">94–98 cm</td><td class="py-3">66 cm</td><td class="py-3">36 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">M</td><td class="py-3">98–102 cm</td><td class="py-3">68 cm</td><td class="py-3">38 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">L</td><td class="py-3">102–106 cm</td><td class="py-3">70 cm</td><td class="py-3">40 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">XL</td><td class="py-3">106–110 cm</td><td class="py-3">72 cm</td><td class="py-3">42 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">XXL</td><td class="py-3">110–116 cm</td><td class="py-3">74 cm</td><td class="py-3">44 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">3XL</td><td class="py-3">116–122 cm</td><td class="py-3">76 cm</td><td class="py-3">46 cm</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Bawahan --}}
        <div x-show="tab === 'bawahan'" class="space-y-4">
          <div class="overflow-x-auto">
            <table class="table table-compact w-full text-xs text-gray-700">
              <thead class="bg-[#e8eaf6] text-[#1a237e]">
                <tr>
                  <th class="font-bold py-3 text-left">Ukuran</th>
                  <th class="font-bold py-3 text-left">Lingkar Pinggang</th>
                  <th class="font-bold py-3 text-left">Lingkar Pinggul</th>
                  <th class="font-bold py-3 text-left">Panjang Celana/Rok</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr><td class="font-bold text-[#1a237e] py-3">S</td><td class="py-3">66–70 cm</td><td class="py-3">88–92 cm</td><td class="py-3">90 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">M</td><td class="py-3">70–74 cm</td><td class="py-3">92–96 cm</td><td class="py-3">93 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">L</td><td class="py-3">74–78 cm</td><td class="py-3">96–100 cm</td><td class="py-3">96 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">XL</td><td class="py-3">78–82 cm</td><td class="py-3">100–104 cm</td><td class="py-3">99 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">XXL</td><td class="py-3">82–88 cm</td><td class="py-3">104–110 cm</td><td class="py-3">102 cm</td></tr>
                <tr><td class="font-bold text-[#1a237e] py-3">3XL</td><td class="py-3">88–96 cm</td><td class="py-3">110–118 cm</td><td class="py-3">105 cm</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="border-t border-gray-100 pt-6 text-center text-xs text-gray-400">
          * Estimasi toleransi ukuran jahitan ±1-2 cm.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
