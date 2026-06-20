@extends('layouts.internal')

@section('title', 'Daily Mental Check — Novos')

@section('topbar-left')
    <span class="font-bold text-gray-900">Daily Mental Check</span>
@endsection

@section('internal-content')
<div x-data="dailyMentalCheck()">
    {{-- Tab Navigation --}}
    <div class="flex gap-1 bg-white/60 backdrop-blur-sm rounded-2xl p-1.5 shadow-sm border border-white/70 max-w-2xl mr-auto mb-8">
        <template x-for="(tab, i) in tabs" :key="i">
            <button @click="activeTab = i"
                :class="activeTab === i ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-white/60'"
                class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2"
            >
                    <span x-text="tab.label"></span>
            </button>
        </template>
    </div>

    {{-- ========== TAB 1: DASHBOARD ========== --}}
    <div x-show="activeTab === 0" x-cloak x-transition:enter.duration.300 class="space-y-6">
        {{-- Row 1: Skor Hari Ini + Pesan Motivasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Card: Skor Hari Ini --}}
            <div class="lg:col-span-2 glass-card rounded-2xl p-6">
                <template x-if="todayFilled">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Skor Hari Ini</p>
                                <p class="text-sm text-gray-500 mt-0.5" x-text="todayDate"></p>
                            </div>
                            <span class="text-4xl" x-text="todayResult.emoji"></span>
                        </div>
                        <div class="mb-4">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold"
                                :class="todayResult.badgeClass" x-text="todayResult.category"></span>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed" x-text="todayResult.message"></p>
                    </div>
                </template>
                <template x-if="!todayFilled">
                    <div class="text-center py-6">
                        <span class="text-5xl block mb-3">🧠</span>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Check-in Hari Ini</h3>
                        <p class="text-sm text-gray-500 mb-6">Luangkan 2 menit untuk cek kondisi mental Anda hari ini.</p>
                        <button @click="activeTab = 1"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-[#1a237e] text-white rounded-xl font-semibold hover:bg-[#283593] transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Isi Daily Check Sekarang
                        </button>
                    </div>
                </template>
            </div>

            {{-- Card: Pesan Motivasi Hari Ini --}}
            <div class="glass-card rounded-2xl p-6 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pesan Hari Ini</p>
                    <button @click="refreshQuote" class="p-1.5 text-gray-400 hover:text-[#1a237e] rounded-lg hover:bg-white/60 transition-colors" title="Ganti kutipan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    </button>
                </div>
                <div class="flex-1 flex flex-col justify-center">
                    <p class="text-gray-800 text-sm leading-relaxed italic mb-3">"<span x-text="currentQuote.text"></span>"</p>
                    <p class="text-xs text-gray-400 font-medium" x-text="currentQuote.author"></p>
                </div>
            </div>
        </div>

        {{-- Row 2: Edukasi + Reminder + Kepatuhan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card: Edukasi Minggu Ini --}}
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">Edukasi Minggu Ini</p>
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br flex items-center justify-center text-2xl shrink-0"
                        :style="'background: ' + currentEdukasi.gradient"
                        x-html="currentEdukasi.icon">
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm mb-1" x-text="currentEdukasi.title"></h4>
                        <p class="text-xs text-gray-500 leading-relaxed" x-text="currentEdukasi.desc"></p>
                    </div>
                </div>
            </div>

            {{-- Card: Reminder Berikutnya --}}
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">Reminder Berikutnya</p>
                <div class="text-center py-3">
                    <p class="text-3xl font-bold text-[#1a237e] mb-1" x-text="nextReminder.time"></p>
                    <p class="text-xs text-gray-500" x-text="nextReminder.label"></p>
                    <template x-if="nextReminder.countdown !== '—'">
                        <p class="text-xs text-gray-400 mt-2">
                            <span class="font-semibold text-gray-700" x-text="nextReminder.countdown"></span> lagi
                        </p>
                    </template>
                    <template x-if="nextReminder.countdown === '—'">
                        <p class="text-xs text-gray-400 mt-2">Waktu kerja sudah selesai</p>
                    </template>
                </div>
            </div>

            {{-- Card: Kepatuhan Micro-Break --}}
            <div class="glass-card rounded-2xl p-6">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">Kepatuhan Micro-Break</p>
                <div class="text-center py-2">
                    <div class="relative w-20 h-20 mx-auto mb-2">
                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 72 72">
                            <circle cx="36" cy="36" r="30" fill="none" stroke="#e5e7eb" stroke-width="6"/>
                            <circle cx="36" cy="36" r="30" fill="none" stroke="currentColor" stroke-width="6"
                                stroke-linecap="round" :stroke-dasharray="188.5"
                                :stroke-dashoffset="188.5 - (188.5 * compliancePercent / 100)"
                                class="text-emerald-500 transition-all duration-700"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-lg font-bold text-gray-900" x-text="compliancePercent + '%'"></span>
                    </div>
                    <p class="text-xs text-gray-500">Minggu ini</p>
                </div>
            </div>
        </div>

        {{-- Row 3: Mini Riwayat 7 Hari --}}
        <div class="glass-card rounded-2xl p-6">
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-4">Riwayat 7 Hari Terakhir</p>
            <div class="flex items-center gap-3 justify-center flex-wrap">
                <template x-for="(day, i) in weekHistory" :key="i">
                    <div class="flex flex-col items-center gap-1.5 min-w-[52px]">
                        <span class="text-2xl" x-text="day.emoji"></span>
                        <span class="text-[10px] font-medium text-gray-500" x-text="day.label"></span>
                        <span class="text-[10px] font-semibold" :class="day.filled ? '' : 'text-gray-300'" x-text="day.filled ? day.category : '—'"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ========== TAB 2: ISI DAILY CHECK ========== --}}
    <div x-show="activeTab === 1" x-cloak x-transition:enter.duration.300 class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Bagaimana kondisi Anda hari ini?</h2>
            <button @click="petunjukOpen = true" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-[#1a237e] bg-white/60 hover:bg-white rounded-lg border border-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                Petunjuk Pengisian
            </button>
        </div>

        {{-- Sudah diisi hari ini --}}
        <template x-if="submitted">
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6 text-center">
                    <span class="text-5xl block mb-3" x-text="dailyResult.emoji"></span>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Check-in Hari Ini Selesai</h3>
                    <p class="text-sm text-gray-500 mb-4">Skor Anda: <strong class="text-gray-900" x-text="totalScore + '/15'"></strong></p>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                        :class="dailyResult.badgeClass" x-text="dailyResult.category"></span>
                    <p class="text-sm text-gray-600 mt-4" x-text="dailyResult.message"></p>
                </div>

                {{-- Interpretasi --}}
                <div class="glass-card rounded-2xl p-6">
                    <h4 class="font-bold text-gray-900 text-sm mb-4">Interpretasi Hasil</h4>
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-700">Skor Total</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-gray-700">Kategori</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr :class="totalScore >= 5 && totalScore <= 7 ? 'bg-emerald-50 font-semibold' : ''">
                                    <td class="px-4 py-2.5 text-gray-700">5 – 7</td>
                                    <td class="px-4 py-2.5"><span :class="totalScore >= 5 && totalScore <= 7 ? 'text-emerald-700' : 'text-gray-600'">😊 Kondisi Baik</span></td>
                                </tr>
                                <tr :class="totalScore >= 8 && totalScore <= 11 ? 'bg-amber-50 font-semibold' : ''">
                                    <td class="px-4 py-2.5 text-gray-700">8 – 11</td>
                                    <td class="px-4 py-2.5"><span :class="totalScore >= 8 && totalScore <= 11 ? 'text-amber-700' : 'text-gray-600'">😐 Perlu Perhatian</span></td>
                                </tr>
                                <tr :class="totalScore >= 12 && totalScore <= 15 ? 'bg-red-50 font-semibold' : ''">
                                    <td class="px-4 py-2.5 text-gray-700">12 – 15</td>
                                    <td class="px-4 py-2.5"><span :class="totalScore >= 12 && totalScore <= 15 ? 'text-red-700' : 'text-gray-600'">😟 Perlu Pendampingan</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan bantuan --}}
                            <template x-if="form.needHelp === 'ya' && form.helpNote">
                                    <div class="glass-card rounded-2xl p-6">
                                        <h4 class="font-bold text-gray-900 text-sm mb-2">Catatan Anda</h4>
                                        <p class="text-sm text-gray-600 italic bg-white/60 rounded-xl p-4 border border-gray-100" x-text="form.helpNote"></p>
                                    </div>
                                </template>

                                <div class="text-center">
                                    <button @click="resetForm()" class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white/60 hover:bg-white rounded-xl border border-gray-200 transition-colors">
                                        Isi Ulang
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Form --}}
                        <template x-if="!submitted">
                            <div class="space-y-6">
                                {{-- Tabel Pertanyaan --}}
                                <div class="glass-card rounded-2xl overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-50/80">
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-10">No</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pertanyaan</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">😊 Baik (1)</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">😐 Cukup (2)</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-32">😟 Kurang Baik (3)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <template x-for="(q, i) in questions" :key="q.id">
                                                    <tr class="hover:bg-white/40 transition-colors">
                                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                                        <template x-for="val in [1, 2, 3]" :key="val">
                                                            <td class="px-4 py-3 text-center">
                                                                <label class="inline-flex items-center justify-center w-10 h-10 rounded-full border-2 cursor-pointer transition-all relative"
                                                                    :class="form.answers[q.id] === val ? 'border-[#1a237e] bg-[#1a237e]/5 shadow-sm' : 'border-gray-200 hover:border-gray-400'">
                                                                    <input type="radio" :name="'q' + q.id" :value="val" x-model="form.answers[q.id]"
                                                                        class="absolute inset-0 opacity-0 cursor-pointer">
                                                                    <span x-show="form.answers[q.id] === val" class="w-4 h-4 rounded-full bg-[#1a237e]"></span>
                                                                </label>
                                                            </td>
                                                        </template>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Butuh bantuan --}}
                                <div class="glass-card rounded-2xl p-6">
                                    <p class="font-semibold text-gray-900 text-sm mb-4">Apakah Anda membutuhkan bantuan atau dukungan hari ini?</p>
                                    <div class="flex gap-6 mb-4">
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="radio" value="tidak" x-model="form.needHelp"
                                                class="w-4 h-4 text-[#1a237e] accent-[#1a237e] border-gray-300">
                                            <span class="text-sm text-gray-700">Tidak</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer">
                                            <input type="radio" value="ya" x-model="form.needHelp"
                                                class="w-4 h-4 text-[#1a237e] accent-[#1a237e] border-gray-300">
                                            <span class="text-sm text-gray-700">Ya</span>
                                        </label>
                                    </div>
                                    <template x-if="form.needHelp === 'ya'">
                                        <textarea x-model="form.helpNote" rows="3"
                                            placeholder="Tuliskan secara singkat..."
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow resize-none"></textarea>
                                    </template>
                                </div>

                                {{-- Submit --}}
                                <div class="text-center">
                                    <button @click="submitForm()" :disabled="!allAnswered"
                                        :class="allAnswered ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                                        class="px-8 py-3 text-white rounded-xl font-semibold transition-colors shadow-sm inline-flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        Kirim Jawaban
                                    </button>
                                </div>
                            </div>
                        </template>

        {{-- Modal Petunjuk Pengisian --}}
        <div x-show="petunjukOpen" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="petunjukOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Petunjuk Pengisian</h3>
                    <button @click="petunjukOpen = false" class="p-1 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <ul class="space-y-3 text-sm text-gray-600">
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-xs font-bold text-[#1a237e] shrink-0 mt-0.5">1</span>
                        Instrumen ini digunakan untuk mengetahui kondisi pekerja sebelum atau selama bekerja.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-xs font-bold text-[#1a237e] shrink-0 mt-0.5">2</span>
                        Mohon isi sesuai kondisi Anda hari ini.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-xs font-bold text-[#1a237e] shrink-0 mt-0.5">3</span>
                        Pilih satu jawaban yang paling sesuai.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-xs font-bold text-[#1a237e] shrink-0 mt-0.5">4</span>
                        Tidak ada jawaban benar atau salah.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-5 h-5 rounded-full bg-[#1a237e]/10 flex items-center justify-center text-xs font-bold text-[#1a237e] shrink-0 mt-0.5">5</span>
                        Waktu pengisian kurang dari 1 menit.
                    </li>
                </ul>
                <button @click="petunjukOpen = false"
                    class="w-full mt-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

    {{-- ========== TAB 3: RIWAYAT ========== --}}
    <div x-show="activeTab === 2" x-cloak x-transition:enter.duration.300 class="text-center py-20">
        <span class="text-5xl block mb-3">📊</span>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Riwayat</h3>
        <p class="text-sm text-gray-500">Riwayat akan segera hadir.</p>
    </div>

    {{-- ========== TAB 4: EDUKASI ========== --}}
    <div x-show="activeTab === 3" x-cloak x-transition:enter.duration.300 class="text-center py-20">
        <span class="text-5xl block mb-3">📖</span>
        <h3 class="text-lg font-bold text-gray-900 mb-1">Pusat Edukasi</h3>
        <p class="text-sm text-gray-500">Konten edukasi akan segera hadir.</p>
    </div>
</div>

<script>
function dailyMentalCheck() {
    return {
        activeTab: 0,
        todayFilled: false,
        submitted: false,
        petunjukOpen: false,

        form: {
            answers: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 },
            needHelp: null,
            helpNote: '',
        },

        questions: [
            { id: 1, text: 'Tingkat stres saya hari ini' },
            { id: 2, text: 'Tingkat kelelahan saya hari ini' },
            { id: 3, text: 'Kemampuan fokus saya hari ini' },
            { id: 4, text: 'Suasana hati (mood) saya hari ini' },
            { id: 5, text: 'Semangat saya untuk bekerja hari ini' },
        ],

        tabs: [
            { label: 'Dashboard', icon: '&#9632;' },
            { label: 'Isi Daily Check', icon: '&#9998;' },
            { label: 'Riwayat',    icon: '&#9776;' },
            { label: 'Edukasi',    icon: '&#9733;' },
        ],

        get allAnswered() {
            return Object.values(this.form.answers).every(v => v > 0);
        },

        get totalScore() {
            return Object.values(this.form.answers).reduce((sum, v) => sum + v, 0);
        },

        get dailyResult() {
            const score = this.totalScore;
            if (score >= 5 && score <= 7) {
                return {
                    emoji: '😊',
                    category: 'Kondisi Baik',
                    badgeClass: 'bg-emerald-100 text-emerald-800',
                    message: 'Kondisi mental Anda dalam keadaan baik hari ini. Pertahankan semangat dan jangan lupa untuk tetap melakukan micro-break secara teratur.',
                };
            } else if (score >= 8 && score <= 11) {
                return {
                    emoji: '😐',
                    category: 'Perlu Perhatian',
                    badgeClass: 'bg-amber-100 text-amber-800',
                    message: 'Kondisi mental Anda perlu mendapat perhatian. Jangan ragu untuk mengambil micro-break sejenak atau bicarakan dengan atasan/rekan kerja jika diperlukan.',
                };
            } else {
                return {
                    emoji: '😟',
                    category: 'Perlu Pendampingan',
                    badgeClass: 'bg-red-100 text-red-800',
                    message: 'Kondisi mental Anda memerlukan pendampingan. Segera hubungi PIC atau manajer Anda untuk mendapatkan dukungan yang diperlukan.',
                };
            }
        },

        // Keep todayResult for dashboard backward compatibility
        get todayResult() {
            return this.dailyResult;
        },

        get todayDate() {
            return new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },

        submitForm() {
            if (!this.allAnswered) return;
            this.todayFilled = true;
            this.submitted = true;
            this.activeTab = 0;
        },

        resetForm() {
            this.form = {
                answers: { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 },
                needHelp: null,
                helpNote: '',
            };
            this.todayFilled = false;
            this.submitted = false;
        },

        // Quotes
        quotes: [
            { text: 'Kesehatan mental bukanlah tujuan, melainkan proses. Ini tentang bagaimana Anda berkendara, bukan ke mana Anda pergi.', author: '— Noam Shpancer' },
            { text: 'Istirahat bukanlah kemalasan. Terkadang Anda perlu menjauh untuk melihat sesuatu dengan lebih jelas.', author: '— Unknown' },
            { text: 'Stres yang berlebihan bukanlah tanda bahwa Anda harus berhenti, melainkan tanda bahwa Anda perlu istirahat.', author: '— Unknown' },
            { text: 'Di sela-sela kesibukan, jangan lupa untuk mendengarkan diri sendiri. Kesehatan mental adalah prioritas, bukan pelengkap.', author: '— Novos Team' },
            { text: 'Anda tidak bisa menuangkan dari cangkir yang kosong. Isi ulang energi Anda dengan micro-break secara teratur.', author: '— Unknown' },
            { text: 'Langkah kecil setiap hari membawa perubahan besar. Mulailah dengan 5 menit untuk diri sendiri.', author: '— Novos Team' },
            { text: 'Sadarilah bahwa istirahat bukanlah kelemahan — itu adalah strategi untuk bertahan dan berkembang.', author: '— Unknown' },
            { text: 'Micro-break bukan tentang berhenti bekerja. Ini tentang bekerja lebih cerdas dengan jeda yang tepat.', author: '— Novos Team' },
        ],
        currentQuoteIndex: 0,

        get currentQuote() {
            return this.quotes[this.currentQuoteIndex];
        },

        refreshQuote() {
            let newIndex;
            do {
                newIndex = Math.floor(Math.random() * this.quotes.length);
            } while (newIndex === this.currentQuoteIndex && this.quotes.length > 1);
            this.currentQuoteIndex = newIndex;
        },

        // Edukasi
        edukasiTopics: [
            {
                title: 'Kenali Stres Kerja',
                desc: 'Pelajari tanda-tanda awal stres kerja dan cara mengelolanya sebelum berdampak pada performa.',
                icon: '&#9888;',
                gradient: 'linear-gradient(135deg, #fef3c7, #fde68a)',
            },
            {
                title: 'Pekerja Sehat',
                desc: 'Tips menjaga kesehatan fisik dan mental selama bekerja, dari postur duduk hingga manajemen waktu.',
                icon: '&#10003;',
                gradient: 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
            },
            {
                title: 'Micro-Break Bukan Malas',
                desc: 'Istirahat singkat 3-5 menit terbukti meningkatkan fokus dan produktivitas — bukan kemalasan.',
                icon: '&#8986;',
                gradient: 'linear-gradient(135deg, #dbeafe, #93c5fd)',
            },
            {
                title: 'Mindfulness STOP',
                desc: 'Teknik S-T-O-P: Stop, Take a breath, Observe, Proceed. Redakan stres dalam 1 menit.',
                icon: '&#9775;',
                gradient: 'linear-gradient(135deg, #ede9fe, #c4b5fd)',
            },
        ],

        get currentEdukasi() {
            const week = this.getWeekNumber(new Date());
            return this.edukasiTopics[week % this.edukasiTopics.length];
        },

        getWeekNumber(date) {
            const startOfYear = new Date(date.getFullYear(), 0, 1);
            const diff = date - startOfYear;
            return Math.ceil((diff / 86400000 + startOfYear.getDay() + 1) / 7);
        },

        // Reminder
        get nextReminder() {
            const now = new Date();
            const jam = now.getHours();
            const menit = now.getMinutes();
            const times = [
                { time: '10:00', label: 'Micro-Break Pagi ☕', hour: 10, min: 0 },
                { time: '13:00', label: 'Micro-Break Siang 🌤️', hour: 13, min: 0 },
                { time: '15:00', label: 'Micro-Break Sore ☕', hour: 15, min: 0 },
            ];

            const currentMinutes = jam * 60 + menit;

            for (const t of times) {
                const targetMinutes = t.hour * 60 + t.min;
                if (targetMinutes > currentMinutes) {
                    const diff = targetMinutes - currentMinutes;
                    const h = Math.floor(diff / 60);
                    const m = diff % 60;
                    let countdown = '';
                    if (h > 0) countdown += h + ' jam ';
                    countdown += m + ' menit';
                    return { ...t, countdown: countdown.trim() };
                }
            }
            return { time: '—', label: 'Besok', countdown: '—' };
        },

        get compliancePercent() {
            return 85;
        },

        // Week history
        get weekHistory() {
            const days = [];
            const now = new Date();
            const results = ['😊', '😊', '😐', '😊', '😢', null, null];
            const labels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            for (let i = 6; i >= 0; i--) {
                const d = new Date(now);
                d.setDate(d.getDate() - i);
                const dayIdx = d.getDay();
                const result = results[6 - i];
                const filled = result !== null;
                days.push({
                    label: labels[dayIdx],
                    emoji: filled ? result : '—',
                    filled,
                    category: filled ? (result === '😊' ? 'Baik' : result === '😐' ? 'Sedang' : 'Buruk') : '',
                });
            }
            return days;
        },
    }
}
</script>
@endsection
