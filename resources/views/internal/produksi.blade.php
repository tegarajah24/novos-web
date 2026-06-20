@extends('layouts.internal')

@section('title', 'Tugas Produksi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Tugas Produksi</h1>
    <p class="text-sm text-gray-500 mt-0.5">Daftar antrean pesanan yang siap diproduksi</p>
@endsection

@section('internal-content')
<div x-data="produksiApp()" x-init="init()">


    {{-- Tabs Navigation Component (Pill Tabs style) --}}
    <div class="flex flex-wrap gap-2.5 mb-6">
        <button @click="activeTab = 'printing'"
            :class="activeTab === 'printing' ? 'bg-[#1a237e] text-white shadow-sm border-[#1a237e]' : 'bg-white text-gray-600 hover:text-[#1a237e] hover:bg-gray-50 border-gray-200'"
            class="px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 flex items-center gap-2 border shadow-sm">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Printing</span>
            <span :class="activeTab === 'printing' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200'"
                class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all duration-200"
                x-text="orders.filter(o => o.stage === 'printing').length"></span>
        </button>
        <button @click="activeTab = 'jahit'"
            :class="activeTab === 'jahit' ? 'bg-[#1a237e] text-white shadow-sm border-[#1a237e]' : 'bg-white text-gray-600 hover:text-[#1a237e] hover:bg-gray-50 border-gray-200'"
            class="px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 flex items-center gap-2 border shadow-sm">
            <i data-lucide="scissors" class="w-4 h-4"></i>
            <span>Jahit (Sewing)</span>
            <span :class="activeTab === 'jahit' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200'"
                class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all duration-200"
                x-text="orders.filter(o => o.stage === 'jahit').length"></span>
        </button>
        <button @click="activeTab = 'qc'"
            :class="activeTab === 'qc' ? 'bg-[#1a237e] text-white shadow-sm border-[#1a237e]' : 'bg-white text-gray-600 hover:text-[#1a237e] hover:bg-gray-50 border-gray-200'"
            class="px-5 py-2.5 rounded-full font-semibold text-sm transition-all duration-200 flex items-center gap-2 border shadow-sm">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
            <span>Quality Control (QC)</span>
            <span :class="activeTab === 'qc' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500 border border-gray-200'"
                class="px-2.5 py-0.5 rounded-full text-xs font-bold transition-all duration-200"
                x-text="orders.filter(o => o.stage === 'qc').length"></span>
        </button>
    </div>

    {{-- Tabel Antrean Produksi berdasarkan Active Tab --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i x-show="activeTab === 'printing'" data-lucide="printer" class="w-4 h-4 text-[#1a237e]"></i>
                <i x-show="activeTab === 'jahit'" data-lucide="scissors" class="w-4 h-4 text-[#1a237e]"></i>
                <i x-show="activeTab === 'qc'" data-lucide="shield-check" class="w-4 h-4 text-[#1a237e]"></i>
                <span x-text="activeTab === 'printing' ? 'Daftar Antrean Cetak (Printing)' : (activeTab === 'jahit' ? 'Daftar Antrean Jahit' : 'Daftar Antrean QC & Finishing')"></span>
            </h2>
            <div class="flex gap-2">
                <span :class="activeTab === 'printing' ? 'bg-blue-100 text-blue-700' : (activeTab === 'jahit' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700')"
                    class="px-3 py-1 text-xs font-semibold rounded-full flex items-center gap-1 transition-all duration-300">
                    <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i>
                    <span x-text="filteredOrders().length"></span> Antrean
                </span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">ID Pesanan</th>
                        <th class="px-6 py-4 font-medium">Customer</th>
                        <th class="px-6 py-4 font-medium">Tim / Produk</th>
                        <th class="px-6 py-4 text-center font-medium">Total Qty</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="filteredOrders().length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                                <p class="font-medium text-gray-800">Tidak ada antrean di divisi ini.</p>
                                <p class="text-xs mt-1 text-gray-400" x-text="activeTab === 'printing' ? 'Semua pesanan selesai diprint!' : (activeTab === 'jahit' ? 'Semua pesanan selesai dijahit!' : 'Semua pesanan lolos QC!')"></p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="order in filteredOrders()" :key="order.id">
                        <tr class="hover:bg-indigo-50/30 transition-colors cursor-pointer group" @click="openDetail(order)">
                            <td class="px-6 py-4">
                                <span class="font-bold text-[#1a237e] group-hover:underline" x-text="order.order_id"></span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900" x-text="order.customer"></td>
                            <td class="px-6 py-4" x-text="order.team_name"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900 bg-gray-100 px-2.5 py-1 rounded-md text-xs border border-gray-200" x-text="order.total_qty + ' pcs'"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span x-text="order.deadline"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span x-show="order.priority === 'High'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-red-50 text-red-700 text-xs font-semibold border border-red-100">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> High
                                </span>
                                <span x-show="order.priority !== 'High'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                                    Normal
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] hover:border-[#1a237e] transition-colors flex items-center gap-1.5 ml-auto">
                                    Lihat Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Pesanan & Penyelesaian --}}
    <template x-teleport="body">
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div x-show="isDetailOpen" @click="isDetailOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200 uppercase"
                                x-text="selectedOrder?.stage"></span>
                            <h3 class="text-xl font-bold text-gray-900">Detail Pesanan: <span x-text="selectedOrder?.order_id" class="text-[#1a237e]"></span></h3>
                        </div>
                        <p class="text-sm text-gray-500 flex items-center gap-1.5">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer"></span>
                            &bull; <i data-lucide="phone" class="w-3.5 h-3.5"></i> <span x-text="selectedOrder?.customer_contact"></span>
                        </p>
                    </div>
                    <button @click="isDetailOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- KIRI: Spesifikasi, Ukuran, Referensi (2 Kolom) --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Spesifikasi Produk --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="shirt" class="w-4 h-4 text-[#1a237e]"></i>
                                Spesifikasi Produk
                            </h4>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6 text-sm">
                                <div>
                                    <span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Nama Tim / Instansi</span>
                                    <span class="font-semibold text-gray-900 text-base" x-text="selectedOrder?.team_name"></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block mb-1 text-xs font-medium uppercase tracking-wider">Deadline</span>
                                    <span class="font-semibold text-red-600" x-text="selectedOrder?.deadline"></span>
                                </div>
                                <div class="col-span-2 grid grid-cols-3 gap-4 pt-2">
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-gray-400 block mb-0.5 text-xs">Bahan</span>
                                        <span class="font-medium text-gray-900" x-text="selectedOrder?.material"></span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-gray-400 block mb-0.5 text-xs">Kerah</span>
                                        <span class="font-medium text-gray-900" x-text="selectedOrder?.collar"></span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <span class="text-gray-400 block mb-0.5 text-xs">Pola Jahitan</span>
                                        <span class="font-medium text-gray-900" x-text="selectedOrder?.pattern"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-gray-500 block mb-2 text-xs font-medium uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i> Catatan Produksi
                                </span>
                                <div class="text-gray-700 bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 leading-relaxed text-sm" x-html="selectedOrder?.notes"></div>
                            </div>
                        </div>

                        {{-- Rekap Ukuran --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="table" class="w-4 h-4 text-[#1a237e]"></i>
                                Rekap Ukuran & Kuantitas
                            </h4>
                            <div class="grid grid-cols-6 gap-2 text-center mb-3">
                                <template x-for="(qty, size) in selectedOrder?.sizes" :key="size">
                                    <div class="bg-purple-50 rounded-lg py-3 border border-purple-100">
                                        <div class="text-xs text-purple-500 font-medium mb-1" x-text="size"></div>
                                        <div class="text-xl font-bold text-gray-900" x-text="qty"></div>
                                        <div class="text-[10px] text-gray-400">pcs</div>
                                    </div>
                                </template>
                            </div>
                            <div class="flex justify-end pt-3 border-t border-gray-100">
                                <p class="text-sm text-gray-600 font-medium">
                                    Total: <span class="text-xl font-extrabold text-[#1a237e] ml-1" x-text="selectedOrder?.total_qty + ' pcs'"></span>
                                </p>
                            </div>
                        </div>

                        {{-- File Desain dari Tim Design --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="file-check-2" class="w-4 h-4 text-[#1a237e]"></i>
                                File Desain & Pola Cetak (Dari Tim Design)
                            </h4>
                            <div class="grid grid-cols-3 gap-4">
                                <template x-for="img in selectedOrder?.reference_files" :key="img">
                                    <div class="aspect-square rounded-xl border border-gray-200 overflow-hidden bg-gray-100 relative group cursor-pointer hover:border-[#1a237e] hover:shadow-md transition-all">
                                        <img :src="img" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-[#1a237e]/80 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity gap-2">
                                            <i data-lucide="download" class="w-6 h-6 text-white"></i>
                                            <span class="text-white text-xs font-medium">Download</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-4 space-y-2">
                                <template x-for="file in selectedOrder?.design_files" :key="file.name">
                                    <div class="flex items-center gap-3 p-2.5 bg-blue-50/50 border border-blue-100 rounded-lg">
                                        <div class="w-8 h-8 rounded bg-white flex items-center justify-center shrink-0 shadow-sm">
                                            <i data-lucide="file" class="w-4 h-4 text-[#1a237e]"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-800 truncate" x-text="file.name"></p>
                                            <p class="text-[10px] text-gray-400" x-text="file.type"></p>
                                        </div>
                                        <button class="text-[#1a237e] bg-white border border-blue-100 hover:bg-[#1a237e] hover:text-white p-1.5 rounded-md transition-colors shrink-0" title="Download">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN: Update Status & Penyelesaian (1 Kolom) --}}
                    <div class="lg:col-span-1 space-y-6">

                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-blue-900/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4">
                                <h4 class="font-semibold text-white flex items-center gap-2 text-sm">
                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                    Tindakan Produksi
                                </h4>
                            </div>

                            <div class="p-5 space-y-5">

                                {{-- Status Dropdown berdasarkan stage --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Pilih Tindakan</label>
                                    
                                    <!-- Printing Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'printing'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="">-- Pilih tindakan cetak --</option>
                                            <option value="diproduksi">Selesai Printing & Kirim ke Jahit</option>
                                            <option value="selesai">Langsung Selesaikan Pesanan (Skip)</option>
                                        </select>
                                    </div>

                                    <!-- Jahit Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'jahit'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="">-- Pilih tindakan jahit --</option>
                                            <option value="diproduksi">Selesai Jahit & Kirim ke QC</option>
                                            <option value="selesai">Langsung Selesaikan Pesanan (Skip)</option>
                                        </select>
                                    </div>

                                    <!-- QC Stage Actions -->
                                    <div x-show="selectedOrder?.stage === 'qc'">
                                        <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                            <option value="">-- Pilih tindakan QC --</option>
                                            <option value="selesai">Lolos QC & Selesaikan Pesanan</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Catatan Opsional --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">2. Catatan (Opsional)</label>
                                    <textarea x-model="productionNote" rows="3"
                                        class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm resize-none"
                                        placeholder="Misal: ada kelebihan 1 pcs size L, warna sedikit lebih tua..."></textarea>
                                </div>

                                {{-- Tombol Submit --}}
                                <div class="pt-2">
                                    <button @click="submitProduksi"
                                        :disabled="!updateStatus"
                                        class="w-full py-3 px-4 bg-[#1a237e] hover:bg-blue-900 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2">
                                        <i data-lucide="send" class="w-4 h-4"></i>
                                        <span x-text="updateStatus === 'selesai' ? 'Selesaikan Pesanan' : 'Kirim ke Divisi Berikutnya'"></span>
                                    </button>
                                </div>

                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'printing'">
                                        Memilih <strong class="text-gray-600">Selesai Printing & Kirim ke Jahit</strong> akan memindahkan pesanan ke divisi Jahit.
                                    </p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'jahit'">
                                        Memilih <strong class="text-gray-600">Selesai Jahit & Kirim ke QC</strong> akan memindahkan pesanan ke divisi QC.
                                    </p>
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed" x-show="selectedOrder?.stage === 'qc'">
                                        Memilih <strong class="text-gray-600">Lolos QC & Selesaikan Pesanan</strong> akan memfinalisasi pesanan ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    </template>

</div>

<script>
function produksiApp() {
    return {
        isDetailOpen: false,
        selectedOrder: null,
        updateStatus: '',
        productionNote: '',
        activeTab: 'printing',

        orders: @json($orders).map(order => {
            if (order.status === 'siap_cetak') {
                order.stage = 'printing';
            } else if (order.status === 'diproduksi') {
                const num = parseInt(order.id) || 0;
                order.stage = (num % 2 === 0) ? 'jahit' : 'qc';
            } else {
                order.stage = 'printing';
            }
            return order;
        }),

        init() {
            this.$watch('activeTab', value => {
                this.$nextTick(() => {
                    if (window.lucide) window.lucide.createIcons();
                });
            });
        },

        filteredOrders() {
            return this.orders.filter(o => o.stage === this.activeTab);
        },

        openDetail(order) {
            this.selectedOrder = order;
            this.updateStatus = '';
            this.productionNote = '';
            this.isDetailOpen = true;
            setTimeout(() => {
                if (window.lucide) window.lucide.createIcons();
            }, 50);
        },

        submitProduksi() {
            if (!this.updateStatus) return;

            const targetStatus = this.updateStatus;
            const currentStage = this.selectedOrder.stage;
            let nextStage = '';
            let isSelesai = false;

            if (targetStatus === 'selesai') {
                isSelesai = true;
            } else if (targetStatus === 'diproduksi') {
                if (currentStage === 'printing') {
                    nextStage = 'jahit';
                } else if (currentStage === 'jahit') {
                    nextStage = 'qc';
                }
            }

            const title = isSelesai ? 'Tandai Produksi Selesai?' : 'Lanjutkan ke Tahap Berikutnya?';
            let text = '';
            if (isSelesai) {
                text = 'Pesanan ini akan ditandai SELESAI dan siap diserahkan.';
            } else {
                if (currentStage === 'printing') {
                    text = 'Pesanan akan dikirim ke divisi Jahit.';
                } else if (currentStage === 'jahit') {
                    text = 'Pesanan akan dikirim ke divisi Quality Control (QC).';
                }
            }

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isSelesai ? '#16a34a' : '#1a237e',
                cancelButtonColor: '#d33',
                confirmButtonText: isSelesai ? 'Ya, Selesai!' : 'Ya, Kirim!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: isSelesai
                            ? 'Pesanan dinyatakan selesai diproduksi.'
                            : (currentStage === 'printing' ? 'Pesanan dikirim ke divisi Jahit.' : 'Pesanan dikirim ke divisi QC.'),
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        this.isDetailOpen = false;
                        if (isSelesai) {
                            // Hapus dari antrean jika selesai
                            this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
                        } else {
                            // Update stage dan status pesanan lokal
                            this.orders = this.orders.map(o => {
                                if (o.id === this.selectedOrder.id) {
                                    o.stage = nextStage;
                                    o.status = 'diproduksi';
                                }
                                return o;
                            });
                        }
                    });
                }
            });
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
