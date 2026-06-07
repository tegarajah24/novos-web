@extends('layouts.internal')

@section('title', 'Tugas Produksi')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Tugas Produksi</h1>
    <p class="text-sm text-gray-500 mt-0.5">Daftar pesanan yang siap diproduksi</p>
@endsection

@section('internal-content')
<div x-data="produksiApp()">

    {{-- Tabel Antrean Produksi --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i data-lucide="scissors" class="w-4 h-4 text-[#1a237e]"></i>
                Daftar Pesanan Siap Cetak
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
                        <th class="px-6 py-4 font-medium">Tim / Produk</th>
                        <th class="px-6 py-4 font-medium text-center">Total Qty</th>
                        <th class="px-6 py-4 font-medium">Deadline</th>
                        <th class="px-6 py-4 font-medium">Prioritas</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-if="orders.length === 0">
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
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
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="order.team_name"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded" x-text="order.total_qty + ' pcs'"></span>
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
                                    Lihat File & Ukuran <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Produksi --}}
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="isDetailOpen" @click="isDetailOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-[#f8fafc] rounded-2xl shadow-2xl border border-gray-200">
                
                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-purple-100 text-purple-700 text-xs font-bold border border-purple-200">Sedang Diproduksi</span>
                            <h3 class="text-xl font-bold text-gray-900">Detail Produksi: <span x-text="selectedOrder?.order_id" class="text-[#1a237e]"></span></h3>
                        </div>
                        <p class="text-sm text-gray-500 flex items-center gap-1.5">
                            <i data-lucide="users" class="w-3.5 h-3.5"></i> Tim: <span class="font-medium text-gray-800" x-text="selectedOrder?.team_name"></span> 
                        </p>
                    </div>
                    <button @click="isDetailOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- KIRI: Rekap Ukuran & Spek (2 Kolom) --}}
                    <div class="lg:col-span-2 space-y-6">
                        
                        {{-- Data Rekap Ukuran --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="table" class="w-4 h-4 text-[#1a237e]"></i>
                                Rekap Ukuran & Kuantitas
                            </h4>
                            
                            <div class="grid grid-cols-6 gap-2 text-center mb-4">
                                <template x-for="(qty, size) in selectedOrder?.sizes" :key="size">
                                    <div class="bg-gray-50 rounded-lg py-3 border border-gray-100">
                                        <div class="text-xs text-gray-500 mb-1" x-text="size"></div>
                                        <div class="text-xl font-bold text-gray-900" x-text="qty"></div>
                                    </div>
                                </template>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-gray-100">
                                <p class="text-sm text-gray-600 font-medium">Total: <span class="text-lg font-bold text-[#1a237e] ml-1" x-text="selectedOrder?.total_qty + ' pcs'"></span></p>
                            </div>
                        </div>

                        {{-- Spesifikasi & Catatan --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                    <i data-lucide="info" class="w-4 h-4 text-[#1a237e]"></i>
                                    Spesifikasi Baju
                                </h4>
                                <div class="space-y-3 text-sm">
                                    <div><span class="text-gray-400 text-xs block">Bahan</span><span class="font-medium text-gray-900" x-text="selectedOrder?.material"></span></div>
                                    <div><span class="text-gray-400 text-xs block">Kerah</span><span class="font-medium text-gray-900" x-text="selectedOrder?.collar"></span></div>
                                    <div><span class="text-gray-400 text-xs block">Pola Jahitan</span><span class="font-medium text-gray-900" x-text="selectedOrder?.pattern"></span></div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                                <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                    <i data-lucide="message-square" class="w-4 h-4 text-[#1a237e]"></i>
                                    Catatan Produksi
                                </h4>
                                <p class="text-sm text-gray-700 bg-amber-50 p-3 rounded-lg border border-amber-100/50 leading-relaxed" x-html="selectedOrder?.production_notes"></p>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN: File Desain & Update Status (1 Kolom) --}}
                    <div class="lg:col-span-1 space-y-6">
                        
                        {{-- File Hasil Desain (Dari Tim Design) --}}
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="file-check-2" class="w-4 h-4 text-[#1a237e]"></i>
                                File Desain / Pola Cetak
                            </h4>
                            <div class="space-y-3">
                                <template x-for="file in selectedOrder?.design_files" :key="file.name">
                                    <div class="flex items-center justify-between p-3 bg-blue-50/50 border border-blue-100 rounded-lg group hover:border-[#1a237e]/30 transition-colors">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded bg-white flex items-center justify-center shrink-0 shadow-sm">
                                                <i data-lucide="file-image" class="w-4 h-4 text-[#1a237e]"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold text-gray-800 truncate" x-text="file.name"></p>
                                                <p class="text-[10px] text-gray-500" x-text="file.type"></p>
                                            </div>
                                        </div>
                                        <button class="text-[#1a237e] bg-white border border-blue-100 hover:bg-[#1a237e] hover:text-white p-1.5 rounded-md transition-colors" title="Download">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Konfirmasi Penyelesaian --}}
                        <div class="bg-white rounded-xl border border-green-200 shadow-lg shadow-green-900/5 overflow-hidden sticky top-6">
                            <div class="bg-green-600 px-5 py-4">
                                <h4 class="font-semibold text-white flex items-center gap-2 text-sm">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                    Selesaikan Produksi
                                </h4>
                            </div>
                            
                            <div class="p-5 space-y-4">
                                <p class="text-xs text-gray-600">Pastikan seluruh proses produksi (printing, press, jahit, QC) sudah selesai dilakukan dan barang siap dikemas.</p>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                                    <textarea x-model="completionNote" rows="2" class="w-full text-sm border-gray-300 rounded-lg focus:ring-green-600 focus:border-green-600 shadow-sm resize-none" placeholder="Misal: Terdapat kelebihan 1 pcs size L..."></textarea>
                                </div>

                                <button @click="finishProduction" 
                                        class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-green-900/20 flex items-center justify-center gap-2 mt-2">
                                    <i data-lucide="box" class="w-4 h-4"></i>
                                    Pesanan Selesai
                                </button>
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
        completionNote: '',
        
        // Data Dummy untuk UI Produksi
        orders: [
            {
                id: 1,
                order_id: 'NVS-20260607-001',
                team_name: 'Garuda FC',
                total_qty: 24,
                material: 'Milano (Premium)',
                collar: 'V-Neck Rib',
                pattern: 'Full Printing',
                deadline: '10 Jun 2026',
                priority: 'High',
                production_notes: 'Warna marun harus sesuai pantone X12. Jahitan kerah diperkuat.',
                sizes: {
                    'S': 2,
                    'M': 8,
                    'L': 10,
                    'XL': 3,
                    'XXL': 1,
                    '3XL': 0
                },
                design_files: [
                    { name: 'mockup_garudafc_final.jpg', type: 'Image/JPG' },
                    { name: 'pola_cetak_marun.pdf', type: 'Document/PDF' }
                ]
            },
            {
                id: 2,
                order_id: 'NVS-20260607-004',
                team_name: 'Bina Bangsa Volley',
                total_qty: 12,
                material: 'Benzema',
                collar: 'O-Neck Standard',
                pattern: 'Kombinasi Polos',
                deadline: '12 Jun 2026',
                priority: 'Normal',
                production_notes: 'Sablon polyflex untuk nama punggung. Pastikan lurus.',
                sizes: {
                    'S': 0,
                    'M': 4,
                    'L': 6,
                    'XL': 2,
                    'XXL': 0,
                    '3XL': 0
                },
                design_files: [
                    { name: 'mockup_binabangsa.png', type: 'Image/PNG' },
                    { name: 'vector_nama_punggung.cdr', type: 'Corel/CDR' }
                ]
            }
        ],

        openDetail(order) {
            this.selectedOrder = order;
            this.completionNote = '';
            this.isDetailOpen = true;
            
            setTimeout(() => { 
                if(window.lucide) window.lucide.createIcons(); 
            }, 50);
        },

        finishProduction() {
            Swal.fire({
                title: 'Produksi Selesai?',
                text: "Apakah Anda yakin pesanan ini sudah selesai diproduksi dan siap diserahkan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a', // green-600
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status pesanan berhasil diperbarui menjadi Selesai.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        this.isDetailOpen = false;
                        // Hapus dari antrean produksi (simulasi)
                        this.orders = this.orders.filter(o => o.id !== this.selectedOrder.id);
                    });
                }
            })
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection
