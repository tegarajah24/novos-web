@extends('layouts.internal')

@section('title', 'Kelola Kategori')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kategori</h1>
@endsection

@section('internal-content')
<div x-data="kategoriApp()" x-init="init()">
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 text-sm">Daftar Kategori</h2>
            <button @click="openModal()" class="px-4 py-2 bg-[#1a237e] text-white text-xs font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                + Tambah Kategori
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">Nama Kategori</th>
                        <th class="px-6 py-4 font-medium text-center">Jumlah Produk</th>
                        <th class="px-6 py-4 text-right font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="cat in categories" :key="cat.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="cat.name"></td>
                            <td class="px-6 py-4 text-center text-gray-600" x-text="cat.products_count"></td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal(cat)" class="text-gray-400 hover:text-[#1a237e] p-1.5 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button @click="hapus(cat)" class="text-gray-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-colors ml-1" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="categories.length === 0">
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada kategori</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <template x-teleport="body">
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div x-show="modalOpen" x-transition.opacity class="fixed inset-0 bg-black/40"></div>
        <div x-show="modalOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4" x-text="editId ? 'Edit Kategori' : 'Tambah Kategori'"></h3>
            <form @submit.prevent="simpan">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" x-model="name" required
                           class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]"
                           placeholder="Contoh: Jersey Basket">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" :disabled="submitting" class="px-4 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>
</div>

<script>
function kategoriApp() {
    return {
        categories: [],
        modalOpen: false,
        editId: null,
        name: '',
        submitting: false,

        async init() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const res = await fetch('{{ route("staf.kategori.data") }}', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                });
                this.categories = await res.json();
            } catch (e) {
                Notify.error('Gagal memuat data kategori.');
            }
            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
        },

        openModal(cat) {
            if (cat) {
                this.editId = cat.id;
                this.name = cat.name;
            } else {
                this.editId = null;
                this.name = '';
            }
            this.modalOpen = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons({ icons: window.lucide.icons }); });
        },

        async simpan() {
            if (!this.name.trim() || this.submitting) return;
            this.submitting = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = this.editId
                ? '/staf/kategori/' + this.editId
                : '{{ route("staf.kategori.store") }}';
            const method = this.editId ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: this.name.trim() })
                });
                const data = await res.json();
                if (data.success) {
                    Notify.success(data.message);
                    this.modalOpen = false;
                    this.init();
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan server.');
            } finally {
                this.submitting = false;
            }
        },

        hapus(cat) {
            Swal.fire({
                title: 'Hapus Kategori?',
                text: `Yakin ingin menghapus "${cat.name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (!result.isConfirmed) return;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                try {
                    const res = await fetch('/staf/kategori/' + cat.id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                    });
                    const data = await res.json();
                    if (data.success) {
                        Notify.success(data.message);
                        this.init();
                    } else {
                        Notify.error(data.message);
                    }
                } catch (e) {
                    Notify.error('Terjadi kesalahan server.');
                }
            });
        }
    }
}
</script>
@endsection
