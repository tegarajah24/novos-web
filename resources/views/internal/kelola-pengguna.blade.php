@extends('layouts.internal')

@section('title', 'Kelola Pengguna')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Kelola Pengguna</h1>
@endsection

@section('internal-content')
    {{-- Stats Cards Carousel (Mobile) / Grid (Desktop) --}}
    <div x-data="{ activeSlide: 0 }" class="mb-4 lg:mb-8">
        <div x-ref="carousel"
             @scroll="activeSlide = Math.round($el.scrollLeft / ($el.scrollWidth / 2))"
             class="flex lg:grid lg:grid-cols-4 gap-4 lg:gap-6 overflow-x-auto lg:overflow-visible snap-x snap-mandatory scroll-smooth [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <div class="flex-none w-1/2 lg:w-auto snap-start bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-11 h-11 rounded-xl bg-[#1a237e] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900" id="totalUsers">0</h3>
                <p class="text-gray-500 text-sm mt-1">Total Pengguna</p>
            </div>
            <div class="flex-none w-1/2 lg:w-auto snap-start bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900" id="totalManager">0</h3>
                <p class="text-gray-500 text-sm mt-1">Manager</p>
            </div>
            <div class="flex-none w-1/2 lg:w-auto snap-start bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900" id="totalAdmin">0</h3>
                <p class="text-gray-500 text-sm mt-1">Admin</p>
            </div>
            <div class="flex-none w-1/2 lg:w-auto snap-start bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6 flex flex-col">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-11 h-11 rounded-xl bg-orange-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
                <h3 class="text-2xl lg:text-3xl font-bold text-gray-900" id="totalProduksiDesign">0</h3>
                <p class="text-gray-500 text-sm mt-1">Produksi &amp; Design</p>
            </div>
        </div>
        {{-- Dots Indicator (Mobile only) --}}
        <div class="flex lg:hidden justify-center gap-2 mt-4">
            <template x-for="(dot, i) in 2" :key="i">
                <button @click="$refs.carousel.scrollLeft = i * ($refs.carousel.scrollWidth / 2)"
                        class="w-2 h-2 rounded-full transition-all duration-300"
                        :class="activeSlide === i ? 'bg-[#1a237e] w-6' : 'bg-gray-300'"></button>
            </template>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" id="searchInput" placeholder="Cari pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                </div>
                <select id="roleFilter" class="w-44 px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                    <option value="">Semua Role</option>
                    <option value="Super Admin">Super Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Admin">Admin</option>
                    <option value="Design">Design</option>
                    <option value="Produksi">Produksi</option>
                </select>
            </div>
                <button onclick="resetTambahForm(); openModal('modalTambah')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors shadow-sm shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Tambah Pengguna
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto max-h-[70vh]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-3 py-4 font-semibold text-center w-16">Urutan</th>
                        <th class="px-4 py-4 font-semibold">Nama</th>
                        <th class="px-4 py-4 font-semibold">Username</th>
                        <th class="px-4 py-4 font-semibold">Email</th>
                        <th class="px-4 py-4 font-semibold whitespace-nowrap">Role</th>
                        <th class="px-4 py-4 font-semibold">Status</th>
                        <th class="px-4 py-4 font-semibold whitespace-nowrap">Tanggal Dibuat</th>
                        <th class="px-3 py-4 font-semibold text-center whitespace-nowrap w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700" id="userTableBody">
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
            <p id="infoDisplay" class="text-sm text-gray-500">Menampilkan 0 dari <span id="totalDisplay">0</span> pengguna</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 disabled:opacity-50 transition-colors" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50 disabled:opacity-50 transition-colors" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <template x-teleport="body">
    <div id="modalTambah" class="modal-wrapper fixed inset-0 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) closeModal(event,'modalTambah')">
        <div class="modal-backdrop fixed inset-0 bg-black/40"></div>
        <div class="modal-card bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Tambah Pengguna</h3>
                <button onclick="closeModal(event, 'modalTambah')" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="formTambah" class="p-6 space-y-5" autocomplete="off">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengguna</label>
                    <input type="text" name="name" id="tambahNama" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Masukkan nama pengguna">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="tambahEmail" autocomplete="off" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Masukkan email">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <input type="password" name="password" id="tambahPassword" autocomplete="new-password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Password">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="tambahPasswordConfirmation" autocomplete="new-password" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Konfirmasi password">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                        <select name="role" id="tambahRole" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                            <option value="">Pilih Role</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                            <option value="Design">Design</option>
                            <option value="Produksi">Produksi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="status" id="tambahStatus" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan Publik <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <input type="text" name="public_title" id="tambahPublicTitle" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Contoh: Owner, Founder, Head of Design">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                    <input type="file" class="filepond" name="avatar" accept="image/png,image/jpeg" data-max-file-size="5MB" data-allow-multiple="false">
                    <p class="text-xs text-gray-400 mt-1">PNG/JPG max 5MB</p>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal(event, 'modalTambah')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit" id="btnTambah" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#1a237e] hover:bg-[#283593] rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg id="spinnerTambah" class="animate-spin w-4 h-4 hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span id="btnTextTambah">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Edit --}}
    <template x-teleport="body">
    <div id="modalEdit" class="modal-wrapper fixed inset-0 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) closeModal(event,'modalEdit')">
        <div class="modal-backdrop fixed inset-0 bg-black/40"></div>
        <div class="modal-card bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Edit Pengguna</h3>
                <button onclick="closeModal(event, 'modalEdit')" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="formEdit" class="p-6 space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" id="editId" name="id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Pengguna</label>
                    <input type="text" name="name" id="editNama" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="editEmail" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" value="ahmad@novos.co.id">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-gray-400 text-xs">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" id="editPassword" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Password baru">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="editPasswordConfirmation" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Konfirmasi password">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                        <select name="role" id="editRole" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                            <option value="Super Admin">Super Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin">Admin</option>
                            <option value="Design">Design</option>
                            <option value="Produksi">Produksi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select name="status" id="editStatus" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jabatan Publik <span class="text-gray-400 text-xs">(Opsional)</span></label>
                    <input type="text" name="public_title" id="editPublicTitle" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] transition-all" placeholder="Contoh: Owner, Founder, Head of Design">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Profil</label>
                    <div class="flex items-center gap-4 mb-3">
                        <div id="editAvatarPreview" class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xs overflow-hidden shrink-0 border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <span class="text-xs text-gray-400">Foto saat ini</span>
                    </div>
                    <input type="file" class="filepond" name="avatar" accept="image/png,image/jpeg" data-max-file-size="5MB" data-allow-multiple="false">
                    <p class="text-xs text-gray-400 mt-1">Upload foto baru untuk mengganti (PNG/JPG max 5MB)</p>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeModal(event, 'modalEdit')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit" id="btnEdit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#1a237e] hover:bg-[#283593] rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg id="spinnerEdit" class="animate-spin w-4 h-4 hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span id="btnTextEdit">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </template>

    {{-- Modal Detail --}}
    <template x-teleport="body">
    <div id="modalDetail" class="modal-wrapper fixed inset-0 z-50 flex items-center justify-center p-4" onclick="if(event.target===this) closeModal(event,'modalDetail')">
        <div class="modal-backdrop fixed inset-0 bg-black/40"></div>
        <div class="modal-card bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Detail Pengguna</h3>
                <button onclick="closeModal(event, 'modalDetail')" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 overflow-hidden" id="detailAvatar">AD</div>
                <h4 class="text-xl font-bold text-gray-900" id="detailNama">Admin Dedi</h4>
                <p class="text-gray-500 text-sm mt-0.5" id="detailUsername">@admindedi</p>
            </div>
            <div class="px-6 pb-6 space-y-4">
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium text-gray-900" id="detailEmail">admin@novos.com</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Role</span>
                    <span id="detailRole"><x-badge type="blue">Admin</x-badge></span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Jabatan Publik</span>
                    <span class="text-sm font-medium text-gray-900" id="detailPublicTitle">-</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Status</span>
                    <span id="detailStatus"><x-badge type="green">Aktif</x-badge></span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Tanggal Dibuat</span>
                    <span class="text-sm font-medium text-gray-900" id="detailTanggal">1 Jan 2026</span>
                </div>
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <span class="text-sm text-gray-500">Terakhir Login</span>
                    <span class="text-sm font-medium text-gray-900" id="detailLogin">8 Jun 2026, 09:30</span>
                </div>
            </div>
            <div class="px-6 pb-6">
                <button onclick="closeModal(event, 'modalDetail')" class="w-full py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Tutup</button>
            </div>
        </div>
    </div>
    </template>

    {{-- Scripts --}}
    <script>
        var __users = @json($users);
        var filteredUsers = [...__users];
        var sortableInstance = null;

        function renderStats() {
            document.getElementById('totalUsers').textContent = __users.length;
            document.getElementById('totalManager').textContent = __users.filter(u => u.role === 'Manager').length;
            document.getElementById('totalAdmin').textContent = __users.filter(u => u.role === 'Admin').length;
            document.getElementById('totalProduksiDesign').textContent = __users.filter(u => u.role === 'Produksi' || u.role === 'Design').length;
        }

        function roleBadgeColor(role) {
            return ({ 'Super Admin': 'red', 'Manager': 'purple', 'Admin': 'blue', 'Design': 'orange', 'Produksi': 'green' })[role] || 'gray';
        }

        function initSortable() {
            const el = document.getElementById('userTableBody');
            if (!el || typeof Sortable === 'undefined') return;

            if (sortableInstance) {
                sortableInstance.destroy();
                sortableInstance = null;
            }

            const isFiltered = filteredUsers.length !== __users.length;
            if (isFiltered) return;

            sortableInstance = new Sortable(el, {
                handle: '.handle',
                animation: 200,
                ghostClass: 'bg-[#1a237e]/10',
                chosenClass: 'bg-blue-50',
                onEnd: async function () {
                    const rows = Array.from(el.querySelectorAll('tr[data-id]'));
                    const newOrderIds = rows.map(r => parseInt(r.getAttribute('data-id')));

                    rows.forEach((r, idx) => {
                        const numEl = r.querySelector('.user-index-num');
                        if (numEl) numEl.textContent = idx + 1;
                    });

                    __users.sort((a, b) => newOrderIds.indexOf(a.id) - newOrderIds.indexOf(b.id));
                    filteredUsers = [...__users];

                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        const res = await fetch("{{ route('staf.kelola-pengguna.reorder') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ order: newOrderIds })
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            Notify.error(data.message || 'Gagal memperbarui urutan');
                        } else {
                            Notify.success('Urutan berhasil diperbarui');
                        }
                    } catch (err) {
                        console.error(err);
                        Notify.error('Koneksi terputus');
                    }
                }
            });
        }

        function renderTable(data) {
            const tbody = document.getElementById('userTableBody');
            const total = document.getElementById('totalDisplay');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-10 text-center text-gray-500">Tidak ada pengguna ditemukan.</td></tr>';
                total.textContent = '0';
                document.getElementById('infoDisplay').textContent = 'Menampilkan 0 dari 0 pengguna';
                return;
            }
            const isFiltered = data.length !== __users.length;
            tbody.innerHTML = data.map((u, index) => {
                const roleBadge = {
                    'Super Admin': 'red', 'Manager': 'purple', 'Admin': 'blue', 'Design': 'orange', 'Produksi': 'green'
                }[u.role] || 'gray';
                const initials = u.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                const avatarHtml = u.avatar
                    ? `<img src="/storage/${u.avatar}" alt="${u.name}" class="w-9 h-9 rounded-full object-cover shrink-0">`
                    : `<div class="w-9 h-9 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold shrink-0">${initials}</div>`;
                return `<tr data-id="${u.id}" class="hover:bg-gray-50/80 transition-colors">
                    <td class="px-3 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <span class="handle p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors ${isFiltered ? 'opacity-30 cursor-not-allowed' : 'cursor-grab active:cursor-grabbing'}" title="${isFiltered ? 'Reset filter untuk mengubah urutan' : 'Tahan & geser untuk mengubah urutan'}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-gray-500 w-4 text-center user-index-num">${index + 1}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            ${avatarHtml}
                            <span class="font-medium text-gray-900">${u.name}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-gray-500">${u.username}</td>
                    <td class="px-4 py-4 text-gray-600">${u.email}</td>
                    <td class="px-4 py-4 whitespace-nowrap"><x-badge type="${roleBadge}">${u.role}</x-badge></td>
                    <td class="px-4 py-4"><x-badge type="${u.status === 'Nonaktif' ? 'red' : 'green'}">${u.status}</x-badge></td>
                    <td class="px-4 py-4 text-gray-500 whitespace-nowrap">${u.created_at}</td>
                    <td class="px-3 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="openDetail(${u.id})" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick="openEdit(${u.id})" class="p-1.5 rounded-lg text-gray-400 hover:text-[#1a237e] hover:bg-gray-100 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button onclick="confirmHapus(${u.id}, '${u.name}')" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            total.textContent = data.length;
            document.getElementById('infoDisplay').textContent = `Menampilkan ${data.length} dari ${__users.length} pengguna`;

            initSortable();
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const role = document.getElementById('roleFilter').value;
            filteredUsers = __users.filter(u => {
                return u.name.toLowerCase().includes(search)
                    && (!role || u.role === role);
            });
            renderTable(filteredUsers);
        }

        function initKelolaPengguna() {
            renderStats();
            renderTable(__users);
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('roleFilter').addEventListener('change', applyFilters);

            const formTambah = document.getElementById('formTambah');
            if (formTambah) {
                formTambah.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnTambah');
                    const spinner = document.getElementById('spinnerTambah');
                    const btnText = document.getElementById('btnTextTambah');
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
                    btnText.textContent = 'Menyimpan...';
                    const formData = new FormData(this);
                    const pond = FilePond.find(document.querySelector('#formTambah .filepond'));
                    if (pond) {
                        pond.getFiles().forEach(f => {
                            if (f.file instanceof File) formData.append('avatar', f.file, f.file.name);
                        });
                    }
                    const result = await submitForm(this.id, '{{ route("staf.kelola-pengguna.store") }}', 'POST', formData);
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btnText.textContent = 'Simpan';
                    if (result) {
                        Notify.success(result.message);
                        refreshTable();
                    }
                });
            }

            const formEdit = document.getElementById('formEdit');
            if (formEdit) {
                formEdit.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnEdit');
                    const spinner = document.getElementById('spinnerEdit');
                    const btnText = document.getElementById('btnTextEdit');
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
                    btnText.textContent = 'Menyimpan...';
                    const id = document.getElementById('editId').value;
                    const formData = new FormData(this);
                    const pond = FilePond.find(document.querySelector('#formEdit .filepond'));
                    if (pond) {
                        pond.getFiles().forEach(f => {
                            if (f.file instanceof File) formData.append('avatar', f.file, f.file.name);
                        });
                    }
                    formData.set('_method', 'PUT');
                    const result = await submitForm(this.id, `{{ url('staf/kelola-pengguna') }}/${id}`, 'POST', formData);
                    spinner.classList.add('hidden');
                    btn.disabled = false;
                    btnText.textContent = 'Simpan';
                    if (result) {
                        Notify.success(result.message);
                        refreshTable();
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initKelolaPengguna);
        } else {
            initKelolaPengguna();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-wrapper.active').forEach(el => {
                    el.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        });

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (window.FilePond) {
                    setTimeout(() => {
                        window.FilePond.parse(modal);
                    }, 50);
                }
            }
        }

        function closeModal(event, id) {
            document.getElementById(id).classList.remove('active');
            document.body.style.overflow = '';
            if (id === 'modalEdit') {
                const pond = FilePond.find(document.querySelector('#formEdit .filepond'));
                if (pond) pond.removeFiles();
            }
        }

        function openDetail(id) {
            const user = __users.find(u => u.id === id);
            if (!user) return;
            const initials = user.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
            const avatarEl = document.getElementById('detailAvatar');
            if (user.avatar) {
                avatarEl.innerHTML = `<img src="/storage/${user.avatar}" alt="${user.name}" class="w-full h-full object-cover">`;
            } else {
                avatarEl.innerHTML = initials;
                avatarEl.className = 'w-20 h-20 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4 overflow-hidden';
            }
            document.getElementById('detailNama').textContent = user.name;
            document.getElementById('detailEmail').textContent = user.email;
            document.getElementById('detailUsername').textContent = '@' + user.username;
            document.getElementById('detailRole').innerHTML = `<x-badge type="${roleBadgeColor(user.role)}">${user.role}</x-badge>`;
            document.getElementById('detailPublicTitle').textContent = user.public_title || '-';
            document.getElementById('detailStatus').innerHTML = `<x-badge type="${user.status === 'Nonaktif' ? 'red' : 'green'}">${user.status}</x-badge>`;
            document.getElementById('detailTanggal').textContent = user.created_at;
            openModal('modalDetail');
        }

        function resetTambahForm() {
            document.getElementById('formTambah').reset();
            document.getElementById('tambahPublicTitle').value = '';
            const pond = FilePond.find(document.querySelector('#formTambah .filepond'));
            if (pond) pond.removeFiles();
        }

        function openEdit(id) {
            const user = __users.find(u => u.id === id);
            if (!user) return;
            document.getElementById('editId').value = user.id;
            document.getElementById('editNama').value = user.name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editPublicTitle').value = user.public_title || '';
            document.getElementById('editPassword').value = '';
            document.getElementById('editPasswordConfirmation').value = '';
            const roleSelect = document.getElementById('editRole');
            for (let opt of roleSelect.options) {
                if (opt.value === user.role) { opt.selected = true; break; }
            }
            const statusSelect = document.getElementById('editStatus');
            for (let opt of statusSelect.options) {
                if (opt.value === user.status) { opt.selected = true; break; }
            }
            const avatarPreview = document.getElementById('editAvatarPreview');
            if (user.avatar) {
                avatarPreview.innerHTML = `<img src="/storage/${user.avatar}" class="w-full h-full object-cover">`;
            } else {
                const initials = user.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
                avatarPreview.innerHTML = initials;
                avatarPreview.className = 'w-14 h-14 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold shrink-0 border border-gray-200';
            }
            const editPond = FilePond.find(document.querySelector('#formEdit .filepond'));
            if (editPond) editPond.removeFiles();
            openModal('modalEdit');
        }

        async function submitForm(formId, url, method, body) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                });

                const result = await res.json();

                if (!res.ok) {
                    let msg = 'Terjadi kesalahan';
                    if (result.errors) {
                        msg = Object.values(result.errors).flat().join('\n');
                    } else if (result.message) {
                        msg = result.message;
                    }
                    Notify.error(msg);
                    return null;
                }

                return result;
            } catch (err) {
                Notify.error('Koneksi terputus');
                return null;
            }
        }

        function refreshTable() {
            window.location.reload();
        }

        function confirmHapus(id, nama) {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            Swal.fire({
                title: 'Hapus Pengguna',
                text: `Apakah Anda yakin ingin menghapus pengguna "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'px-5 py-2.5 text-sm font-semibold rounded-xl',
                    cancelButton: 'px-5 py-2.5 text-sm font-medium rounded-xl',
                    popup: 'rounded-2xl'
                }
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                try {
                    const res = await fetch(`{{ url('staf/kelola-pengguna') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        Notify.error(data.message || 'Terjadi kesalahan');
                        return;
                    }

                    Notify.success(data.message, 'Terhapus!');
                    refreshTable();
                } catch (err) {
                    Notify.error('Koneksi terputus');
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-wrapper.active').forEach(el => {
                    el.classList.remove('active');
                    document.body.style.overflow = '';
                });
            }
        });
    </script>
@endsection
