<div>
    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                Daftar Pesanan Belum Didesain
            </h2>
            <span class="px-3 py-1 bg-blue-100 text-[#1a237e] text-xs font-semibold rounded-full flex items-center gap-1">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                <span>{{ count($orders) }}</span> Antrean
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">ID Pesanan</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Tim / Produk</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer group" wire:click="openDetail({{ $order['id'] }})">
                        <td class="px-6 py-4"><span class="font-semibold text-[#1a237e] group-hover:underline">{{ $order['order_id'] }}</span></td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $order['customer'] }}</td>
                        <td class="px-6 py-4">{{ $order['team_name'] }}</td>
                        <td class="px-6 py-4"><span class="text-gray-500">{{ $order['deadline'] }}</span></td>
                        <td class="px-6 py-4">
                            @if($order['priority'] === 'High')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-semibold border border-red-100">High</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] transition-colors inline-flex items-center gap-1.5">
                                Lihat Detail
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                            <svg class="w-10 h-10 mx-auto text-green-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="font-medium">Tidak ada antrean desain.</p>
                            <p class="text-xs">Kerja bagus, semua pesanan sudah dikerjakan!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail --}}
    @if($isDetailOpen && $selectedOrderId)
    @php $o = collect($orders)->firstWhere('id', $selectedOrderId); @endphp
    @if($o)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40" wire:click="closeDetail"></div>
            <div class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Di Design</span>
                            <h3 class="text-xl font-bold text-gray-900">Detail Pesanan: <span class="text-[#1a237e]">{{ $o['order_id'] }}</span></h3>
                        </div>
                        <p class="text-sm text-gray-500 flex items-center gap-1.5">{{ $o['customer'] }} &bull; {{ $o['customer_contact'] }}</p>
                    </div>
                    <button wire:click="closeDetail" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- LEFT: Specs & References --}}
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">Spesifikasi Produk</h4>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
                                <div><span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Nama Tim / Instansi</span><span class="font-semibold text-gray-900 text-base">{{ $o['team_name'] }}</span></div>
                                <div><span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Deadline</span><span class="font-semibold text-red-600">{{ $o['deadline'] }}</span></div>
                                <div class="col-span-2 grid grid-cols-3 gap-4 pt-2">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Bahan</span><span class="font-medium text-gray-900">{{ $o['material'] }}</span></div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Kerah</span><span class="font-medium text-gray-900">{{ $o['collar'] }}</span></div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Pola</span><span class="font-medium text-gray-900">{{ $o['pattern'] }}</span></div>
                                </div>
                            </div>
                            @if($o['revision_note'])
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-orange-600 block mb-2 text-xs font-medium uppercase tracking-wider flex items-center gap-1.5">Revisi Terakhir dari Customer</span>
                                <div class="text-gray-700 bg-orange-50 p-4 rounded-xl border border-orange-200/60 leading-relaxed text-sm">{{ $o['revision_note'] }}</div>
                            </div>
                            @endif
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-gray-500 block mb-2 text-xs font-medium uppercase tracking-wider">Catatan Customer / Admin</span>
                                <div class="text-gray-700 bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 leading-relaxed text-sm">{!! $o['notes'] !!}</div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">File & Logo Referensi</h4>
                            <div class="grid grid-cols-4 gap-4">
                                @foreach($o['reference_files'] as $img)
                                <a href="{{ $img }}" target="_blank" class="aspect-square rounded-xl border border-gray-200 overflow-hidden bg-gray-100 relative group cursor-pointer hover:border-[#1a237e] hover:shadow-md transition-all block">
                                    <img src="{{ $img }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-[#1a237e]/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity gap-2">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span class="text-white text-xs font-medium">Download</span>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: Upload & Action --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-[#1a237e]/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4"><h4 class="font-semibold text-white flex items-center gap-2 text-sm">Penyelesaian Design</h4></div>
                            <div class="p-5 space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Upload Hasil Design</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-[#1a237e] hover:bg-blue-50/50 transition-colors cursor-pointer" onclick="document.getElementById('design-file-input').click()">
                                        <input type="file" id="design-file-input" class="hidden" multiple accept="image/*,.pdf,.zip,.rar" wire:model.live="uploadedFiles">
                                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3 text-[#1a237e]">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <p class="text-sm font-semibold text-[#1a237e]">Klik untuk upload</p>
                                        <p class="text-xs text-gray-500 mt-1">Mockup / Pola (Max 20MB)</p>
                                    </div>
                                    @error('uploadedFiles.*') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                    @error('uploadedFiles') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror

                                    @if(count($uploadedFiles) > 0)
                                    <div class="mt-3 space-y-2">
                                        @foreach($uploadedFiles as $index => $file)
                                        <div class="flex items-center gap-3 p-2 bg-blue-50/50 border border-blue-100 rounded-lg" wire:key="file-{{ $index }}">
                                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center shrink-0 shadow-sm">
                                                <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <p class="text-xs font-medium text-gray-700 truncate flex-1">{{ $file->getClientOriginalName() }}</p>
                                            <button wire:click="removeFile({{ $index }})" class="text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>

                                <div class="border-t border-gray-100 pt-5">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">2. Update Status</label>
                                    <select wire:model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="">-- Pilih status selanjutnya --</option>
                                        <option value="siap_cetak">Selesai Design (Teruskan ke Produksi)</option>
                                    </select>
                                    @error('updateStatus') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="pt-2">
                                    <button wire:click="submitDesign" wire:loading.attr="disabled"
                                            class="w-full py-3 px-4 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-[#1a237e]/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="submitDesign" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Simpan & Teruskan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
