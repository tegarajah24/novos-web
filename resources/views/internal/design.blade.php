@extends('layouts.internal')

@section('title', 'Tugas Design')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Tugas Design</h1>
    <p class="text-sm text-gray-500 mt-0.5">Daftar antrean desain yang perlu dikerjakan</p>
@endsection

@section('internal-content')
<div x-data="designApp()">

    {{-- Tabel Antrean Design --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 flex items-center gap-2 text-sm">
                <i data-lucide="pen-tool" class="w-4 h-4 text-[#1a237e]"></i>
                Daftar Pesanan Belum Didesain
            </h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-blue-100 text-[#1a237e] text-xs font-semibold rounded-full flex items-center gap-1">
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
                                <p class="font-medium">Tidak ada antrean desain.</p>
                                <p class="text-xs">Kerja bagus, semua pesanan sudah dikerjakan!</p>
                            </td>
                        </tr>
                    </template>
                    <template x-for="order in orders" :key="order.id">
                        <tr class="hover:bg-blue-50/50 transition-colors cursor-pointer group" @click="openDetail(order)">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-[#1a237e] group-hover:underline" x-text="order.order_id"></span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="order.customer"></td>
                            <td class="px-6 py-4" x-text="order.team_name"></td>
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
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 text-xs font-medium hover:bg-gray-50 hover:text-[#1a237e] transition-colors flex items-center gap-1.5 ml-auto">
                                    Lihat Detail <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Detail Pesanan & Upload --}}
    <div x-show="isDetailOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="isDetailOpen" @click="isDetailOpen = false" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" aria-hidden="true"></div>

            <div x-show="isDetailOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-[#f8fafc] rounded-2xl shadow-2xl border border-gray-200">
                
                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">Di Design</span>
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
                    
                    {{-- KIRI: Spesifikasi & Referensi (2 Kolom) --}}
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
                                        <span class="text-gray-400 block mb-0.5 text-xs">Pola</span>
                                        <span class="font-medium text-gray-900" x-text="selectedOrder?.pattern"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-t border-gray-100">
                                <span class="text-gray-500 block mb-2 text-xs font-medium uppercase tracking-wider flex items-center gap-1.5">
                                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i> Catatan Customer / Admin
                                </span>
                                <div class="text-gray-700 bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 leading-relaxed text-sm" x-html="selectedOrder?.notes"></div>
                            </div>
                        </div>

                        {{-- Logo & Referensi --}}
                        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-gray-900 mb-4 flex items-center gap-2 text-sm border-b border-gray-100 pb-3">
                                <i data-lucide="image" class="w-4 h-4 text-[#1a237e]"></i>
                                File & Logo Referensi
                            </h4>
                            <div class="grid grid-cols-4 gap-4">
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
                        </div>

                    </div>

                    {{-- KANAN: Upload Design & Action (1 Kolom) --}}
                    <div class="lg:col-span-1 space-y-6">
                        
                        {{-- Form Upload & Update --}}
                        <div class="bg-white rounded-xl border border-[#1a237e]/20 shadow-lg shadow-blue-900/5 overflow-hidden sticky top-6">
                            <div class="bg-[#1a237e] px-5 py-4">
                                <h4 class="font-semibold text-white flex items-center gap-2 text-sm">
                                    <i data-lucide="check-square" class="w-4 h-4"></i>
                                    Penyelesaian Design
                                </h4>
                            </div>
                            
                            <div class="p-5 space-y-5">
                                {{-- Dropzone Upload --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">1. Upload Hasil Design</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-[#1a237e] hover:bg-blue-50/50 transition-colors cursor-pointer"
                                         @click="$refs.fileInput.click()">
                                        <input type="file" x-ref="fileInput" class="hidden" multiple accept="image/*,.pdf,.zip,.rar" @change="handleFileUpload">
                                        <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3 text-[#1a237e]">
                                            <i data-lucide="cloud-upload" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-[#1a237e]">Klik untuk upload</p>
                                        <p class="text-xs text-gray-500 mt-1">Mockup / Pola (Max 20MB)</p>
                                    </div>

                                    {{-- List Uploaded Files --}}
                                    <div class="mt-3 space-y-2" x-show="uploadedFiles.length > 0">
                                        <template x-for="(file, index) in uploadedFiles" :key="index">
                                            <div class="flex items-center gap-3 p-2 bg-blue-50/50 border border-blue-100 rounded-lg">
                                                <div class="w-8 h-8 rounded bg-white flex items-center justify-center shrink-0 shadow-sm">
                                                    <i data-lucide="file" class="w-4 h-4 text-[#1a237e]"></i>
                                                </div>
                                                <p class="text-xs font-medium text-gray-700 truncate flex-1" x-text="file.name"></p>
                                                <button @click="removeFile(index)" class="text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-md transition-colors">
                                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Status Dropdown --}}
                                <div class="border-t border-gray-100 pt-5">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">2. Update Status</label>
                                    <select x-model="updateStatus" class="w-full text-sm border-gray-300 rounded-lg focus:ring-[#1a237e] focus:border-[#1a237e] shadow-sm py-2.5">
                                        <option value="">-- Pilih status selanjutnya --</option>
                                        <option value="siap_cetak">Selesai Design (Teruskan ke Admin / Produksi)</option>
                                        <option value="menunggu_acc">Menunggu ACC Customer</option>
                                    </select>
                                </div>

                                {{-- Submit Button --}}
                                <div class="pt-2">
                                    <button @click="submitDesign" :disabled="!updateStatus || uploadedFiles.length === 0" 
                                            class="w-full py-3 px-4 bg-[#1a237e] hover:bg-blue-900 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-blue-900/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center justify-center gap-2">
                                        <i data-lucide="send" class="w-4 h-4"></i>
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
</div>

<script>
function designApp() {
    return {
        isDetailOpen: false,
        selectedOrder: null,
        uploadedFiles: [],
        updateStatus: '',
        
        orders: @json($orders),

        openDetail(order) {
            this.selectedOrder = order;
            this.uploadedFiles = [];
            this.updateStatus = '';
            this.isDetailOpen = true;
            
            // Re-initialize Lucide icons when modal opens
            setTimeout(() => { 
                if(window.lucide) {
                    window.lucide.createIcons(); 
                }
            }, 50);
        },

        handleFileUpload(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                // Validasi ukuran file (Max 20MB)
                if(file.size <= 20 * 1024 * 1024) {
                    this.uploadedFiles.push(file);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: `Ukuran file ${file.name} melebihi 20MB.`
                    });
                }
            });
            e.target.value = ''; // reset input
            
            // Re-initialize Lucide for newly added file icons
            setTimeout(() => { if(window.lucide) window.lucide.createIcons(); }, 50);
        },

        removeFile(index) {
            this.uploadedFiles.splice(index, 1);
        },

        submitDesign() {
            if(!this.updateStatus) return;
            if(this.uploadedFiles.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Upload File',
                    text: 'Silakan upload minimal 1 file hasil design (mockup/pola)'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menyimpan dan meneruskan desain ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1a237e',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Teruskan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simulasi API Request
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Hasil desain telah diupload dan status pesanan diperbarui.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        this.isDetailOpen = false;
                        // Hapus dari list (simulasi sukses)
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
