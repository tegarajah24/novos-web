@extends('layouts.internal')

@section('title', 'Pengaturan')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Pengaturan</h1>
    <p class="text-sm text-gray-500 mt-0.5">Kelola pengaturan toko</p>
@endsection

@section('internal-content')
<div x-data="settingApp()" x-init="init()" class="max-w-2xl mx-auto">
    <div class="glass-card rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Informasi Toko</h2>

        <form @submit.prevent="save" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                <input type="text" x-model="form.company_name"
                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" x-model="form.company_phone"
                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" x-model="form.company_email"
                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e]">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea x-model="form.company_address" rows="3"
                          class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:ring-[#1a237e] focus:border-[#1a237e] resize-none"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="saving"
                        class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors disabled:opacity-50">
                    <span x-show="!saving">Simpan Pengaturan</span>
                    <span x-show="saving" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function settingApp() {
    return {
        form: {
            company_name: '',
            company_phone: '',
            company_email: '',
            company_address: '',
        },
        saving: false,

        init() {
            this.form.company_name = @json($settings['company_name'] ?? '');
            this.form.company_phone = @json($settings['company_phone'] ?? '');
            this.form.company_email = @json($settings['company_email'] ?? '');
            this.form.company_address = @json($settings['company_address'] ?? '');
        },

        async save() {
            this.saving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('{{ route("staf.pengaturan.update") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (data.success) {
                    this.form.company_name = data.data?.company_name ?? this.form.company_name;
                    this.form.company_phone = data.data?.company_phone ?? this.form.company_phone;
                    this.form.company_email = data.data?.company_email ?? this.form.company_email;
                    this.form.company_address = data.data?.company_address ?? this.form.company_address;
                    Notify.success(data.message);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal menyimpan pengaturan.');
                    Notify.error(msg);
                }
            } catch (e) {
                Notify.error('Terjadi kesalahan server.');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endsection
