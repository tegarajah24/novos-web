@extends('layouts.internal')

@section('title', 'Tugas Produksi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Tugas Produksi</h1>
    <p class="text-sm text-gray-500 mt-0.5">Daftar antrean pesanan yang siap diproduksi</p>
@endsection

@section('internal-content')
<div x-data="produksiApp()">

    {{-- Tabel Antrean Produksi --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i data-lucide="scissors" class="w-4 h-4 text-[#1a237e]"></i>
                Daftar Pesanan Siap Produksi
            </h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i data-lucide="loader" class="w-3.5 h-3.5"></i>
                    <span x-text="orders.length"></span> Antrean
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
                    <template x-if="orders.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <i data-lucide="check-circle-2" class="w-10 h-10 mx-auto text-green-400 mb-2"></i>
                                <p class="font-medium">Tidak ada antrean produksi.</p>
                                <p class="text-xs">Kerja bagus, semua pesanan sudah selesai diproduksi!</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="order in orders" :key="order.id">
                        <tr class="hover:bg-purple-50/50 transition-colors cursor-pointer group" @click="openDetail(order)">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-[#1a237e] group-hover:underline" x-text="order.order_id"></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="order.customer"></td>
                            <td class="px-6 py-4" x-text="order.team_name"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900 bg-purple-50 text-purple-700 px-2.5 py-1 rounded-md text-xs border border-purple-100" x-text="order.total_qty + ' pcs'"></span>
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
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-purple-700 transition-colors flex items-center gap-1.5 ml-auto">
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
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div x-show="isDetailOpen" @click="isDetailOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-[#f8fafc] rounded-2xl shadow-2xl border border-gray-200">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200">Diproduksi</span>
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
                                    Penyelesaian Produksi
                                </h4>
                            </div>

                            <div class="p-5 space-y-5">

                                {{-- Status Dropdown --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Update Status</label>
                                    <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="">-- Pilih status produksi --</option>
                                        <option value="diproduksi">Sedang Diproduksi</option>
                                        <option value="selesai">Selesai Produksi (Siap Kirim)</option>
                                    </select>
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
                                        Simpan & Update Status
                                    </button>
                                </div>

                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-[11px] text-gray-400 text-center leading-relaxed">
                                        Jika status diubah ke <strong class="text-gray-600">Selesai Produksi</strong>,<br>pesanan akan otomatis dinyatakan selesai.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
function produksiApp() {
    return {
        isDetailOpen: false,
        selectedOrder: null,
        updateStatus: '',
        productionNote: '',

        orders: [
            {
                id: 1,
                order_id: 'NVS-20260607-001',
                customer: 'Budi Santoso',
                customer_contact: '0812-3456-7890',
                team_name: 'Garuda FC',
                material: 'Milano (Premium)',
                collar: 'V-Neck Rib',
                pattern: 'Full Printing',
                deadline: '10 Jun 2026',
                priority: 'High',
                total_qty: 24,
                notes: 'Warna marun harus sesuai pantone X12. Jahitan kerah diperkuat.<br>Sablon nama punggung font block tebal.',
                sizes: { 'S': 2, 'M': 8, 'L': 10, 'XL': 3, 'XXL': 1, '3XL': 0 },
                reference_files: [
                    'https://placehold.co/300x300/1a237e/ffffff?text=Mockup+Depan',
                    'https://placehold.co/300x300/1a237e/ffffff?text=Mockup+Belakang',
                    'https://placehold.co/300x300/e2e8f0/64748b?text=Detail+Warna',
                ],
                design_files: [
                    { name: 'pola_cetak_garudafc.pdf', type: 'Document/PDF' },
                    { name: 'vector_logo_garuda.cdr', type: 'Corel/CDR' },
                ]
            },
            {
                id: 2,
                order_id: 'NVS-20260607-004',
                customer: 'Siti Rahayu',
                customer_contact: '0857-1122-3344',
                team_name: 'Bina Bangsa Volley',
                material: 'Benzema',
                collar: 'O-Neck Standard',
                pattern: 'Kombinasi Polos',
                deadline: '12 Jun 2026',
                priority: 'Normal',
                total_qty: 12,
                notes: 'Sablon polyflex untuk nama punggung. Pastikan lurus.',
                sizes: { 'S': 0, 'M': 4, 'L': 6, 'XL': 2, 'XXL': 0, '3XL': 0 },
                reference_files: [
                    'https://placehold.co/300x300/1e3a5f/ffffff?text=Mockup+Kaos',
                ],
                design_files: [
                    { name: 'mockup_binabangsa.png', type: 'Image/PNG' },
                    { name: 'vector_nama_punggung.cdr', type: 'Corel/CDR' },
                ]
            }
        ],

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

            const isSelesai = this.updateStatus === 'selesai';
            const title = isSelesai ? 'Tandai Produksi Selesai?' : 'Update Status Produksi?';
            const text = isSelesai
                ? 'Pesanan ini akan ditandai SELESAI dan siap diserahkan.'
                : 'Status pesanan akan diperbarui menjadi "Sedang Diproduksi".';

            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isSelesai ? '#16a34a' : '#1a237e',
                cancelButtonColor: '#d33',
                confirmButtonText: isSelesai ? 'Ya, Selesai!' : 'Ya, Update!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: isSelesai
                            ? 'Pesanan dinyatakan selesai diproduksi.'
                            : 'Status berhasil diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        this.isDetailOpen = false;
                        if (isSelesai) {
                            // Hapus dari antrean jika selesai
                            this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
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
