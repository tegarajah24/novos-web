<div>
    {{-- Tabs Navigation --}}
    <div class="flex max-w-3xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
        @foreach([['key' => 'printing', 'label' => 'Printing'], ['key' => 'jahit', 'label' => 'Jahit (Sewing)'], ['key' => 'qc', 'label' => 'Quality Control (QC)']] as $tab)
        <button wire:click="$set('activeTab', '{{ $tab['key'] }}')"
            class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2
                {{ $activeTab === $tab['key'] ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <span>{{ $tab['label'] }}</span>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all
                {{ $activeTab === $tab['key'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">
                {{ collect($this->orders)->where('stage', $tab['key'])->count() }}
            </span>
        </button>
        @endforeach
    </div>

    {{-- Tabel Antrean Produksi --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                @if($activeTab === 'printing')
                <span class="text-[#1a237e]">Printing</span>
                @elseif($activeTab === 'jahit')
                <span class="text-[#1a237e]">Jahit</span>
                @else
                <span class="text-[#1a237e]">QC & Finishing</span>
                @endif
                — Daftar Antrean
            </h2>
            <span class="px-3 py-1 {{ $activeTab === 'printing' ? 'bg-blue-100 text-blue-700' : ($activeTab === 'jahit' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }} text-xs font-semibold rounded-full">
                {{ count($this->filteredOrders) }} Antrean
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">ID Pesanan</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Tim / Produk</th>
                        <th class="px-6 py-4 text-center font-medium">Qty</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $filtered = $this->filteredOrders; @endphp
                    @forelse($filtered as $order)
                    <tr class="hover:bg-indigo-50/30 transition-colors group cursor-pointer" wire:click="openDetail({{ $order['id'] }})">
                        <td class="px-6 py-4"><span class="font-bold text-[#1a237e] group-hover:underline">{{ $order['order_id'] }}</span></td>
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $order['customer'] }}</td>
                        <td class="px-6 py-4">{{ $order['team_name'] }}</td>
                        <td class="px-6 py-4 text-center"><span class="font-bold text-gray-900 bg-gray-100 px-2.5 py-1 rounded-md text-xs border border-gray-200">{{ $order['total_qty'] }} pcs</span></td>
                        <td class="px-6 py-4 text-gray-500">{{ $order['deadline'] }}</td>
                        <td class="px-6 py-4">
                            @if($order['priority'] === 'High')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-semibold border border-red-100">High</span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">Normal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] hover:border-[#1a237e] transition-colors inline-flex items-center gap-1.5">
                                Lihat Detail
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <p class="font-medium text-gray-800">Tidak ada antrean di divisi ini.</p>
                            <p class="text-xs mt-1 text-gray-400">Semua pesanan sudah diproses!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail --}}
    @if($isDetailOpen && $selectedOrder)
    @php $o = $this->selectedOrder; @endphp
    @if($o)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black/40" wire:click="closeDetail"></div>
            <div class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 uppercase">{{ $o['stage'] }}</span>
                            <h3 class="text-xl font-bold text-gray-900">Detail Pesanan: <span class="text-[#1a237e]">{{ $o['order_id'] }}</span></h3>
                        </div>
                        <p class="text-sm text-gray-500">{{ $o['customer'] }} &bull; {{ $o['customer_contact'] }}</p>
                    </div>
                    <button wire:click="closeDetail" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- KIRI --}}
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">Spesifikasi Produk</h4>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
                                <div><span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Nama Tim</span><span class="font-semibold text-gray-900 text-base">{{ $o['team_name'] }}</span></div>
                                <div><span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Deadline</span><span class="font-semibold text-red-600">{{ $o['deadline'] }}</span></div>
                                <div class="col-span-2 grid grid-cols-3 gap-4 pt-2">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Bahan</span><span class="font-medium text-gray-900">{{ $o['material'] }}</span></div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Kerah</span><span class="font-medium text-gray-900">{{ $o['collar'] }}</span></div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100"><span class="text-gray-400 block mb-0.5 text-xs">Pola</span><span class="font-medium text-gray-900">{{ $o['pattern'] }}</span></div>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-gray-500 block mb-2 text-xs font-medium uppercase tracking-wider">Catatan Produksi</span>
                                <div class="text-gray-700 bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 leading-relaxed text-sm">{!! $o['notes'] !!}</div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">Rekap Ukuran</h4>
                            <div class="grid grid-cols-6 gap-2 text-center mb-3">
                                @foreach($o['sizes'] as $size => $qty)
                                <div class="bg-purple-50 rounded-lg py-3 border border-purple-100">
                                    <div class="text-xs text-purple-500 font-medium mb-1">{{ $size }}</div>
                                    <div class="text-xl font-bold text-gray-900">{{ $qty }}</div>
                                    <div class="text-[10px] text-gray-400">pcs</div>
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-end pt-3 border-t border-gray-100">
                                <p class="text-sm text-gray-600 font-medium">Total: <span class="text-xl font-extrabold text-[#1a237e] ml-1">{{ $o['total_qty'] }} pcs</span></p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">File Desain</h4>
                            <div class="grid grid-cols-3 gap-4">
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

                    {{-- KANAN: Action Panel --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-[#1a237e]/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4"><h4 class="font-semibold text-white flex items-center gap-2 text-sm">Tindakan Produksi</h4></div>
                            <div class="p-5 space-y-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Pilih Tindakan</label>
                                    @if($o['stage'] === 'printing')
                                    <select wire:model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="proses_printing">Sedang Proses</option>
                                        <option value="selesai_printing">Selesai</option>
                                    </select>
                                    @elseif($o['stage'] === 'jahit')
                                    <select wire:model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="proses_jahit">Sedang Proses</option>
                                        <option value="selesai_jahit">Selesai</option>
                                    </select>
                                    @else
                                    <select wire:model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="selesai_qc">Selesai (Lolos QC)</option>
                                        <option value="revisi_qc">Revisi / Pengerjaan Ulang</option>
                                    </select>
                                    @endif
                                </div>

                                @if($o['stage'] === 'qc')
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider">2. Checklist Quality Control</label>
                                    <div class="space-y-2.5">
                                        @foreach([
                                            ['key' => 'qcJahitan', 'label' => 'Kualitas Jahitan', 'desc' => 'Jahitan rapi, benang tidak loncat, kelim lurus dan sesuai pola.'],
                                            ['key' => 'qcCacat', 'label' => 'Bebas Cacat Produksi', 'desc' => 'Tidak ada lubang, sobekan, noda, atau warna tidak merata.'],
                                            ['key' => 'qcUkuran', 'label' => 'Ukuran & Kuantitas Sesuai', 'desc' => 'Jumlah pcs per ukuran sesuai dengan pesanan customer.'],
                                            ['key' => 'qcDesain', 'label' => 'Desain & Sablon/Bordir', 'desc' => 'Warna, posisi, dan kualitas sablon/bordir sesuai file desain.'],
                                        ] as $item)
                                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition-colors group">
                                            <input type="checkbox" wire:model="{{ $item['key'] }}" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">{{ $item['label'] }}</p>
                                                <p class="text-[11px] text-gray-400 mt-0.5">{{ $item['desc'] }}</p>
                                            </div>
                                        </label>
                                        @endforeach
                                        <label class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-200 cursor-pointer hover:bg-red-100 hover:border-red-300 transition-colors group">
                                            <input type="checkbox" wire:model="qcPerluRevisi" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500 cursor-pointer shrink-0">
                                            <div>
                                                <p class="text-xs font-semibold text-red-700 group-hover:text-red-900">Perlu Revisi / Pengerjaan Ulang</p>
                                                <p class="text-[11px] text-red-400 mt-0.5">Centang jika ada bagian yang perlu diperbaiki.</p>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex justify-between items-center mb-1.5">
                                            <span class="text-[11px] text-gray-500">Progress QC</span>
                                            <span class="text-[11px] font-bold text-emerald-600">{{ $this->qcProgress }}/4</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, ($this->qcProgress / 4) * 100) }}%"></div>
                                        </div>
                                    </div>
                                </div>

                                <div x-data="{ open: $wire.$entangle('updateStatus') }" x-show="open === 'revisi_qc'" x-cloak>
                                    <label class="block text-xs font-semibold text-gray-700 mb-3 uppercase tracking-wider">2b. Kirim Revisi ke Bagian</label>
                                    <div class="flex gap-3">
                                        <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors bg-gray-50 border-gray-200">
                                            <input type="radio" value="printing" wire:model="targetStage" class="w-4 h-4 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-700">Printing</p>
                                                <p class="text-[11px] text-gray-400">Cetak ulang</p>
                                            </div>
                                        </label>
                                        <label class="flex-1 flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors bg-gray-50 border-gray-200">
                                            <input type="radio" value="jahit" wire:model="targetStage" class="w-4 h-4 text-amber-600 focus:ring-amber-500 cursor-pointer">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-700">Jahit</p>
                                                <p class="text-[11px] text-gray-400">Perbaikan jahitan</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endif

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">
                                        {{ $o['stage'] === 'qc' ? '3. Catatan QC (Opsional)' : '2. Catatan (Opsional)' }}
                                    </label>
                                    <textarea wire:model="productionNote" rows="3" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm resize-none"
                                        placeholder="{{ $o['stage'] === 'qc' ? 'Detail bagian yang perlu diperbaiki...' : 'Catatan produksi...' }}"></textarea>
                                </div>

                                <div class="pt-2">
                                    <button wire:click="submitProduksi" wire:loading.attr="disabled"
                                            class="w-full py-3 px-4 bg-[#1a237e] hover:bg-[#283593] text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-[#1a237e]/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                        <svg wire:loading wire:target="submitProduksi" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Update
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
