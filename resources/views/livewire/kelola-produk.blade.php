<div>
    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" x-data>
        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative w-full max-w-[240px]">
                    <svg class="absolute inset-y-0 left-0 pl-3 flex items-center w-4 h-4 text-gray-400 my-auto pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama jersey..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-900">
                </div>
                <select wire:model.live="categoryFilter" class="w-full max-w-[180px] px-3 py-2.5 rounded-xl border border-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] bg-white text-gray-700">
                    <option value="">Semua Kategori</option>
                    @foreach($this->categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button wire:click="openCreate" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah Produk Baru
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                        <th class="w-16 text-center">ID</th>
                        <th>Foto</th>
                        <th>Nama Jersey</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($this->products as $prod)
                    <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                        <td class="text-center text-gray-500 font-medium">{{ $prod['id'] }}</td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($prod['image_depan'])
                                    <img src="{{ $prod['image_depan'] }}" class="object-cover w-full h-full" alt="Depan">
                                    @else
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center">
                                    @if($prod['image_belakang'])
                                    <img src="{{ $prod['image_belakang'] }}" class="object-cover w-full h-full" alt="Belakang">
                                    @else
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="font-bold text-gray-900">{{ $prod['name'] }}</td>
                        <td><span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-semibold">{{ $prod['category_name'] }}</span></td>
                        <td class="font-semibold text-emerald-600">{{ number_format($prod['price'], 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button wire:click="openEdit({{ $prod['id'] }})" title="Edit Produk" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button x-data x-on:click="
                                    Swal.fire({
                                        title: 'Hapus Produk?',
                                        text: 'Apakah Anda yakin ingin menghapus produk ini?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#dc2626',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Ya, Hapus',
                                        cancelButtonText: 'Batal'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.call('delete', {{ $prod['id'] }});
                                        }
                                    });
                                " title="Hapus Produk" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500 font-medium">Tidak ada produk ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Form --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/40" wire:click="closeForm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-[564px] max-h-[665px] flex flex-col overflow-hidden mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>{{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}</span>
                </h3>
                <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 flex-1 overflow-y-auto">
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" required class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori <span class="text-red-500">*</span></label>
                            <select wire:model="category_id" required class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                <option value="">Pilih Kategori</option>
                                @foreach($this->categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="price" required min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                        @error('price') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Deskripsi Produk</label>
                        <textarea wire:model="description" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30" rows="3" style="resize:none;"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis Kerah</label>
                            <select wire:model="kerah" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                <option value="">Pilih (opsional)</option>
                                <option value="O-NECK V.1">O-NECK V.1</option>
                                <option value="O-NECK V.2">O-NECK V.2</option>
                                <option value="O-NECK V.3">O-NECK V.3</option>
                                <option value="O-NECK V.4">O-NECK V.4</option>
                                <option value="V-NECK V.5">V-NECK V.5</option>
                                <option value="V-NECK V.1">V-NECK V.1</option>
                                <option value="V-NECK V.2">V-NECK V.2</option>
                                <option value="V-NECK V.3">V-NECK V.3</option>
                                <option value="V-NECK V.4">V-NECK V.4</option>
                                <option value="V-NECK V.5">V-NECK V.5</option>
                                <option value="CLASSIC V.1">CLASSIC V.1</option>
                                <option value="CLASSIC V.2">CLASSIC V.2</option>
                                <option value="CLASSIC V.3">CLASSIC V.3</option>
                                <option value="CLASSIC V.4">CLASSIC V.4</option>
                                <option value="CLASSIC V.5">CLASSIC V.5</option>
                                <option value="V-NECK V3 TUMPUK">V-NECK V3 TUMPUK</option>
                                <option value="TIMNAS">TIMNAS</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Bahan Jersey</label>
                            <select wire:model="bahan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                <option value="">Pilih (opsional)</option>
                                <option value="BINTIK JARUM GRADE B">BINTIK JARUM GRADE B</option>
                                <option value="MILANO GRADE B">MILANO GRADE B</option>
                                <option value="BINTIK JARUM PREMIUM">BINTIK JARUM PREMIUM</option>
                                <option value="MILANO PREMIUM">MILANO PREMIUM</option>
                                <option value="RABBIT">RABBIT</option>
                                <option value="DROPPEDDLE">DROPPEDDLE</option>
                                <option value="SMASH">SMASH</option>
                                <option value="WAFFLE">WAFFLE</option>
                                <option value="EMBOSH">EMBOSH</option>
                                <option value="MICROCOOL">MICROCOOL</option>
                                <option value="JAQUARD AERO">JAQUARD AERO</option>
                                <option value="COTTON 24S">COTTON 24S</option>
                                <option value="COTTON 30S">COTTON 30S</option>
                                <option value="LOTTO">LOTTO</option>
                                <option value="PARASUT">PARASUT</option>
                                <option value="PUMA">PUMA</option>
                                <option value="ULTRALIGHT A">ULTRALIGHT A</option>
                                <option value="ULTRALIGHT B">ULTRALIGHT B</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Jenis Potongan</label>
                            <select wire:model="jenis_potongan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                <option value="">Pilih (opsional)</option>
                                <option value="REGULER">REGULER</option>
                                <option value="SLIMFIT CEWE">SLIMFIT CEWE</option>
                                <option value="OVERSIZE">OVERSIZE</option>
                                <option value="TUNIK">TUNIK</option>
                                <option value="SLIM FIT UNISEX">SLIM FIT UNISEX</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Model Lengan & Jahitan</label>
                            <select wire:model="lengan_jahitan" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                                <option value="">Pilih (opsional)</option>
                                <option value="REGULER OVERDECK">REGULER OVERDECK</option>
                                <option value="REGULER PAKAI MANSET">REGULER PAKAI MANSET</option>
                                <option value="RAGLAN A OVERDECK">RAGLAN A OVERDECK</option>
                                <option value="RAGLAN A PAKAI MANSET">RAGLAN A PAKAI MANSET</option>
                                <option value="RAGLAN B OVERDECK">RAGLAN B OVERDECK</option>
                                <option value="RAGLAN B PAKAI MANSET">RAGLAN B PAKAI MANSET</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">Foto Tampak Depan</label>
                        @if($editingProductId && $existingImage)
                        <div class="mb-2">
                            <img src="{{ $existingImage }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                        </div>
                        @endif
                        @if($image)
                        <div class="mb-2">
                            <img src="{{ $image->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                        </div>
                        @endif
                        <div>
                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-[#1a237e] text-white text-sm rounded-xl hover:bg-[#283593] transition-colors font-medium cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Pilih File
                                <input type="file" wire:model="image" class="hidden" accept="image/*">
                            </label>
                            <span class="text-sm text-gray-500 ml-2">{{ $image ? $image->getClientOriginalName() : 'No file chosen' }}</span>
                        </div>
                        @error('image') <span class="text-xs text-red-500 block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                        <button type="button" wire:click="closeForm" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm rounded-xl hover:bg-gray-50 transition-colors font-medium bg-white">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm rounded-xl hover:bg-[#283593] transition-colors font-semibold flex items-center gap-2 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $submitting ? 'Menyimpan...' : 'Simpan' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
