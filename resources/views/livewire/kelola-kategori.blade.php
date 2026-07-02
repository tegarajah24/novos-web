<div>
    <div class="bg-white shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900 text-sm">Daftar Kategori</h2>
            <button wire:click="openModal()" class="px-4 py-2 bg-[#1a237e] text-white text-xs font-semibold rounded-xl hover:bg-[#283593] transition-colors">
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
                    @foreach($categories as $cat)
                    <tr class="hover:bg-gray-50 transition-colors" wire:key="cat-{{ $cat['id'] }}">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $cat['name'] }}</td>
                        <td class="px-6 py-4 text-center text-gray-600">{{ $cat['products_count'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openModal({{ $cat['id'] }})" class="text-gray-400 hover:text-[#1a237e] p-1.5 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button x-on:click.prevent="
                                Swal.fire({
                                    title: 'Hapus Kategori?',
                                    text: 'Yakin ingin menghapus &quot;{{ $cat['name'] }}&quot;?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#dc2626',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Ya, Hapus',
                                    cancelButtonText: 'Batal'
                                }).then((r) => { if (r.isConfirmed) $wire.hapus({{ $cat['id'] }}); });
                            " class="text-gray-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-colors ml-1" title="Hapus">
                                <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($categories) === 0)
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada kategori</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($modalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" wire:click="$set('modalOpen', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $editId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
            <form wire:submit="simpan">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" wire:model="name" required
                           class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]"
                           placeholder="Contoh: Jersey Basket">
                    @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="$set('modalOpen', false)" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg wire:loading wire:target="simpan" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
