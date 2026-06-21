@extends('layouts.internal')

@section('title', 'Daily Mental Check — Novos')

@section('topbar-left')
    <span class="font-bold text-gray-900">Daily Mental Check</span>
@endsection

@section('internal-content')
<div x-data="dailyMentalCheck()">
    {{-- Tab Navigation --}}
    <div class="flex max-w-lg gap-1 bg-white/60 backdrop-blur-sm rounded-2xl p-1.5 shadow-sm border border-white/70 mb-8">
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
        {{-- Row 1: Poster + Pesan Motivasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Poster Banner --}}
            <div class="lg:col-span-2 rounded-2xl overflow-hidden border border-indigo-200/60 min-h-[160px]">
                <img src="{{ asset('images/poster-daily-mental-check.jpg') }}" alt="Poster Kesehatan Mental" class="w-full h-full object-cover">
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

        {{-- Row 2: Skor Hari Ini + Reminder + Kepatuhan Micro-Break --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Card: Skor Hari Ini --}}
            <div class="glass-card rounded-2xl p-6 h-full">
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
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Check-in Hari Ini</h3>
                        <p class="text-sm text-gray-500 mb-4">Luangkan 2 menit untuk cek kondisi mental Anda hari ini.</p>
                        <button @click="activeTab = 1"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1a237e] text-white rounded-lg text-sm font-semibold hover:bg-[#283593] transition-colors shadow-sm">
                            Isi Daily Check Sekarang
                        </button>
                    </div>
                </template>
            </div>

            {{-- Card: Reminder Berikutnya --}}
            <div class="glass-card rounded-2xl p-6 h-full">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">Reminder Berikutnya</p>
                <div class="text-center py-4">
                    <p class="text-4xl font-bold text-[#1a237e] mb-1" x-text="nextReminder.time"></p>
                    <p class="text-sm text-gray-500" x-text="nextReminder.label"></p>
                    <template x-if="nextReminder.countdown !== '—'">
                        <p class="text-sm text-gray-400 mt-2">
                            <span class="font-semibold text-gray-700" x-text="nextReminder.countdown"></span> lagi
                        </p>
                    </template>
                    <template x-if="nextReminder.countdown === '—'">
                        <p class="text-sm text-gray-400 mt-2">Waktu kerja sudah selesai</p>
                    </template>
                </div>
            </div>

            {{-- Card: Kepatuhan Micro-Break --}}
            <div class="glass-card rounded-2xl p-6 h-full">
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

        {{-- Row 3: Riwayat 7 Hari --}}
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
    <div x-show="activeTab === 1" x-cloak x-transition:enter.duration.300 class="max-w-5xl mx-auto">
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
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Check-in Hari Ini Selesai</h3>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                        :class="dailyResult.badgeClass" x-text="dailyResult.category"></span>
                    <p class="text-sm text-gray-600 mt-4" x-text="dailyResult.message"></p>
                </div>


                {{-- Catatan bantuan --}}
                            <template x-if="form.needHelp === 'ya' && form.helpNote">
                                    <div class="glass-card rounded-2xl p-6">
                                        <h4 class="font-bold text-gray-900 text-sm mb-2">Catatan Anda</h4>
                                        <p class="text-sm text-gray-600 italic bg-white/60 rounded-xl p-4 border border-gray-100" x-text="form.helpNote"></p>
                                    </div>
                                </template>

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
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">Baik</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">Cukup</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-32">Kurang Baik</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                <template x-for="(q, i) in questions" :key="q.id">
                                                    <tr class="hover:bg-white/40 transition-colors">
                                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                                        <template x-for="val in [1, 2, 3]" :key="val">
                                                            <td class="px-4 py-3 text-center">
                                                                <label @click="form.answers[q.id] = val"
                                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                                    :class="form.answers[q.id] === val ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                                    <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                                        :class="form.answers[q.id] === val ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                                    </div>
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

    {{-- ========== TAB 3: MICRO-BREAK ========== --}}
    <div x-show="activeTab === 2" x-cloak x-transition:enter.duration.300 class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Checklist SMART-WORK Micro-Break</h2>
            <button @click="microPetunjukOpen = true" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-[#1a237e] bg-white/60 hover:bg-white rounded-lg border border-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                Petunjuk Pengisian
            </button>
        </div>

        {{-- Sudah diisi --}}
        <template x-if="microSubmitted">
            <div class="space-y-6">
                <div class="glass-card rounded-2xl p-6 text-center">
                    <span class="text-5xl block mb-3" x-text="microScore >= 7 ? '🎉' : microScore >= 4 ? '👍' : '💪'"></span>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Micro-Break Hari Ini Selesai</h3>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                        :class="microLevel.class" x-text="'Kepatuhan ' + microLevel.label"></span>
                </div>



                {{-- Catatan Pekerja --}}
                <div class="glass-card rounded-2xl p-6" x-show="microForm.catatan_membantu || microForm.catatan_kendala">
                    <h4 class="font-bold text-gray-900 text-sm mb-4">Catatan Pekerja</h4>
                    <div class="space-y-3" x-show="microForm.catatan_membantu">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Apa yang paling membantu?</p>
                        <p class="text-sm text-gray-600 italic bg-white/60 rounded-xl p-4 border border-gray-100" x-text="microForm.catatan_membantu"></p>
                    </div>
                    <div class="space-y-3" x-show="microForm.catatan_kendala">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kendala yang dialami</p>
                        <p class="text-sm text-gray-600 italic bg-white/60 rounded-xl p-4 border border-gray-100" x-text="microForm.catatan_kendala"></p>
                    </div>
                </div>

            </div>
        </template>

        {{-- Form --}}
        <template x-if="!microSubmitted">
            <div class="space-y-6">
                {{-- A. Checklist Pelaksanaan SMART-WORK Micro-Break --}}
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="px-6 pt-5 pb-2">
                        <h3 class="font-bold text-gray-900">A. Checklist Pelaksanaan SMART-WORK Micro-Break</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 w-10">No</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pernyataan</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-20">Ya</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-20">Tidak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                {{-- Tahap 1 – STOP --}}
                                <tr>
                                    <td colspan="4" class="px-4 py-2 bg-gray-50/40">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahap 1 – STOP</span>
                                    </td>
                                </tr>
                                <template x-for="q in microChecklist.filter(c => c.stage === 'STOP')" :key="q.id">
                                    <tr class="hover:bg-white/40 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 1"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 1 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 1 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 0"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 0 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 0 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Tahap 2 – TAKE A BREATH --}}
                                <tr>
                                    <td colspan="4" class="px-4 py-2 bg-gray-50/40">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahap 2 – TAKE A BREATH</span>
                                    </td>
                                </tr>
                                <template x-for="q in microChecklist.filter(c => c.stage === 'TAKE A BREATH')" :key="q.id">
                                    <tr class="hover:bg-white/40 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 1"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 1 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 1 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 0"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 0 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 0 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Tahap 3 – OBSERVE --}}
                                <tr>
                                    <td colspan="4" class="px-4 py-2 bg-gray-50/40">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahap 3 – OBSERVE</span>
                                    </td>
                                </tr>
                                <template x-for="q in microChecklist.filter(c => c.stage === 'OBSERVE')" :key="q.id">
                                    <tr class="hover:bg-white/40 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 1"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 1 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 1 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 0"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 0 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 0 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Tahap 4 – PROCEED --}}
                                <tr>
                                    <td colspan="4" class="px-4 py-2 bg-gray-50/40">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahap 4 – PROCEED</span>
                                    </td>
                                </tr>
                                <template x-for="q in microChecklist.filter(c => c.stage === 'PROCEED')" :key="q.id">
                                    <tr class="hover:bg-white/40 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 1"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 1 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 1 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 0"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 0 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 0 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Tahap 5 – AKTIVITAS PENDUKUNG --}}
                                <tr>
                                    <td colspan="4" class="px-4 py-2 bg-gray-50/40">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahap 5 – Aktivitas Pendukung</span>
                                    </td>
                                </tr>
                                <template x-for="q in microChecklist.filter(c => c.stage === 'AKTIVITAS PENDUKUNG')" :key="q.id">
                                    <tr class="hover:bg-white/40 transition-colors">
                                        <td class="px-4 py-3 text-gray-400 font-medium" x-text="q.id"></td>
                                        <td class="px-4 py-3 text-gray-700" x-text="q.text"></td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 1"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 1 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 1 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.checklist[q.id] = 0"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.checklist[q.id] === 0 ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.checklist[q.id] === 0 ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- D. Evaluasi Manfaat --}}
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="px-6 pt-5 pb-2">
                        <h3 class="font-bold text-gray-900">D. Evaluasi Manfaat Setelah Micro-Break</h3>
                        <p class="text-xs text-gray-500 mt-1">Bagaimana kondisi Anda setelah melakukan micro-break?</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/80">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Pertanyaan</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">Lebih Baik</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-20">Sama</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700 w-28">Lebih Buruk</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-white/40 transition-colors">
                                    <td class="px-4 py-3 text-gray-700">Tingkat stres saya</td>
                                    <template x-for="val in ['lebih_baik', 'sama', 'lebih_buruk']" :key="val">
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.eval.stres = val"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.eval.stres === val ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.eval.stres === val ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </template>
                                </tr>
                                <tr class="hover:bg-white/40 transition-colors">
                                    <td class="px-4 py-3 text-gray-700">Tingkat fokus saya</td>
                                    <template x-for="val in ['lebih_baik', 'sama', 'lebih_buruk']" :key="val">
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.eval.fokus = val"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.eval.fokus === val ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.eval.fokus === val ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </template>
                                </tr>
                                <tr class="hover:bg-white/40 transition-colors">
                                    <td class="px-4 py-3 text-gray-700">Tingkat kenyamanan bekerja saya</td>
                                    <template x-for="val in ['lebih_baik', 'sama', 'lebih_buruk']" :key="val">
                                        <td class="px-4 py-3 text-center">
                                            <label @click="microForm.eval.kenyamanan = val"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-full border-2 cursor-pointer transition-all"
                                                :class="microForm.eval.kenyamanan === val ? 'border-[#1a237e] bg-[#1a237e]/10 shadow-sm' : 'border-gray-200 hover:border-gray-400 hover:bg-gray-50'">
                                                <div class="w-2.5 h-2.5 rounded-full transition-all duration-150"
                                                    :class="microForm.eval.kenyamanan === val ? 'bg-[#1a237e] scale-100' : 'bg-transparent scale-0'">
                                                </div>
                                            </label>
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Catatan Pekerja --}}
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="font-bold text-gray-900 text-sm mb-4">Catatan Pekerja</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Apa yang paling membantu setelah micro-break hari ini?</label>
                            <textarea x-model="microForm.catatan_membantu" rows="2"
                                placeholder="Tuliskan..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700 mb-2">Apakah ada kendala melakukan micro-break?</label>
                            <textarea x-model="microForm.catatan_kendala" rows="2"
                                placeholder="Tuliskan..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#1a237e]/20 focus:border-[#1a237e] outline-none transition-shadow resize-none"></textarea>
            </div>
    </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button @click="submitMicroForm()" :disabled="!allChecklistAnswered || !allEvalAnswered"
                        :class="allChecklistAnswered && allEvalAnswered ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                        class="px-8 py-3 text-white rounded-xl font-semibold transition-colors shadow-sm inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Kirim Jawaban
                    </button>
                </div>
            </div>
        </template>

        {{-- Modal Petunjuk Pengisian Micro-Break --}}
        <div x-show="microPetunjukOpen" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="microPetunjukOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Petunjuk Pengisian</h3>
                    <button @click="microPetunjukOpen = false" class="p-1 text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="text-sm text-gray-600 space-y-3">
                    <p>Form ini digunakan untuk memantau pelaksanaan <strong>SMART-WORK Micro-Break</strong> yang dilakukan selama 3–5 menit saat bekerja.</p>
                    <p class="font-semibold text-gray-700">Micro-break dilakukan setiap:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>2–3 jam kerja</li>
                        <li>saat merasa lelah</li>
                        <li>saat merasa stres</li>
                        <li>saat kehilangan fokus</li>
                        <li>atau ketika beban kerja meningkat</li>
                    </ul>
                    <p>Berilah tanda pada kolom yang sesuai.</p>
                </div>
                <button @click="microPetunjukOpen = false"
                    class="w-full mt-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">
                    Mengerti
                </button>
            </div>
        </div>
    </div>

</div>

</div>

<script>
function dailyMentalCheck() {
    return {
        activeTab: 0,
        todayFilled: false,
        submitted: false,
        petunjukOpen: false,
        loading: false,

        weekHistory: [],
        compliancePercent: 0,

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

        // Micro-Break form
        microSubmitted: false,
        microPetunjukOpen: false,

        microForm: {
            checklist: { 1: null, 2: null, 3: null, 4: null, 5: null, 6: null, 7: null, 8: null },
            eval: { stres: null, fokus: null, kenyamanan: null },
            catatan_membantu: '',
            catatan_kendala: '',
        },

        microChecklist: [
            { id: 1, stage: 'STOP', text: 'Saya menghentikan pekerjaan sejenak selama micro-break' },
            { id: 2, stage: 'TAKE A BREATH', text: 'Saya melakukan latihan napas perlahan minimal 3 kali siklus' },
            { id: 3, stage: 'OBSERVE', text: 'Saya memperhatikan kondisi tubuh saya (lelah, tegang, atau nyaman)' },
            { id: 4, stage: 'OBSERVE', text: 'Saya menyadari perasaan atau emosi yang sedang saya alami' },
            { id: 5, stage: 'PROCEED', text: 'Saya kembali bekerja dengan lebih tenang' },
            { id: 6, stage: 'PROCEED', text: 'Saya menentukan kembali prioritas pekerjaan sebelum melanjutkan aktivitas' },
            { id: 7, stage: 'AKTIVITAS PENDUKUNG', text: 'Saya melakukan peregangan ringan saat micro-break' },
            { id: 8, stage: 'AKTIVITAS PENDUKUNG', text: 'Saya minum air putih saat micro-break' },
        ],

        tabs: [
            { label: 'Dashboard', icon: '&#9632;' },
            { label: 'Isi Daily Check', icon: '&#9998;' },
            { label: 'Micro-Break', icon: '&#9776;' },
        ],

        async init() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const headers = { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf };

            try {
                const todayRes = await fetch('/staf/daily-mental-check/today', { headers });
                const todayData = await todayRes.json();

                if (todayData.daily_check) {
                    this.todayFilled = true;
                    this.submitted = true;
                    const a = todayData.daily_check.answers;
                    this.form.answers = { 1: a[0] || 0, 2: a[1] || 0, 3: a[2] || 0, 4: a[3] || 0, 5: a[4] || 0 };
                    this.form.needHelp = todayData.daily_check.need_help ? 'ya' : 'tidak';
                    this.form.helpNote = todayData.daily_check.help_note || '';
                }

                if (todayData.micro_break) {
                    this.microSubmitted = true;
                    const cl = todayData.micro_break.checklist;
                    this.microForm.checklist = { 1: cl[0], 2: cl[1], 3: cl[2], 4: cl[3], 5: cl[4], 6: cl[5], 7: cl[6], 8: cl[7] };
                    this.microForm.eval = todayData.micro_break.eval;
                    this.microForm.catatan_membantu = todayData.micro_break.catatan_membantu || '';
                    this.microForm.catatan_kendala = todayData.micro_break.catatan_kendala || '';
                }
            } catch (e) { console.error('Failed to load today data:', e); }

            try {
                const histRes = await fetch('/staf/daily-mental-check/history', { headers });
                const histData = await histRes.json();
                this.weekHistory = histData.week_history.map(d => ({
                    label: d.label,
                    emoji: d.daily_check ? d.daily_check.emoji : '—',
                    filled: !!d.daily_check,
                    category: d.daily_check ? (d.daily_check.category === 'baik' ? 'Baik' : d.daily_check.category === 'perlu_perhatian' ? 'Sedang' : 'Buruk') : '',
                }));
                this.compliancePercent = histData.compliance_percent;
            } catch (e) { console.error('Failed to load history:', e); }
        },

        get allAnswered() {
            return Object.values(this.form.answers).every(v => v > 0);
        },

        get allChecklistAnswered() {
            return Object.values(this.microForm.checklist).every(v => v !== null);
        },

        get allEvalAnswered() {
            return this.microForm.eval.stres !== null && this.microForm.eval.fokus !== null && this.microForm.eval.kenyamanan !== null;
        },

        get microScore() {
            return Object.values(this.microForm.checklist).reduce((sum, v) => sum + (v === 1 ? 1 : 0), 0);
        },

        get microLevel() {
            const s = this.microScore;
            if (s >= 7) return { label: 'Tinggi', class: 'bg-emerald-100 text-emerald-800', color: 'text-emerald-700' };
            if (s >= 4) return { label: 'Sedang', class: 'bg-amber-100 text-amber-800', color: 'text-amber-700' };
            return { label: 'Rendah', class: 'bg-red-100 text-red-800', color: 'text-red-700' };
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
                    message: 'Kondisi Anda Baik. Silakan melanjutkan pekerjaan seperti biasa. Tetap semangat!',
                };
            } else if (score >= 8 && score <= 11) {
                return {
                    emoji: '😐',
                    category: 'Perlu Perhatian',
                    badgeClass: 'bg-amber-100 text-amber-800',
                    message: 'Kondisi Anda Perlu Perhatian. Sangat disarankan untuk mengambil istirahat singkat (micro-break) 3–5 menit sekarang, lakukan peregangan ringan, minum air putih, atau silakan diskusi singkat dengan PIC jika ada kendala.',
                };
            } else {
                return {
                    emoji: '😟',
                    category: 'Perlu Pendampingan',
                    badgeClass: 'bg-red-100 text-red-800',
                    message: 'Kondisi Anda Perlu Pendampingan. Harap lakukan micro-break berbasis latihan mindfulness STOP segera. Admin/Supervisor akan dihubungi untuk mengevaluasi beban kerja Anda hari ini.',
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

        async submitForm() {
            if (!this.allAnswered || this.loading) return;
            this.loading = true;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('/staf/daily-mental-check/daily', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({
                        answers: [this.form.answers[1], this.form.answers[2], this.form.answers[3], this.form.answers[4], this.form.answers[5]],
                        need_help: this.form.needHelp || 'tidak',
                        help_note: this.form.helpNote,
                    }),
                });
                if (res.ok) {
                    this.todayFilled = true;
                    this.submitted = true;
                    this.loadHistory();
                }
            } catch (e) { console.error('Failed to submit daily check:', e); }
            this.loading = false;
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

        async submitMicroForm() {
            if (!this.allChecklistAnswered || !this.allEvalAnswered || this.loading) return;
            this.loading = true;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('/staf/daily-mental-check/micro', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({
                        checklist: [this.microForm.checklist[1], this.microForm.checklist[2], this.microForm.checklist[3], this.microForm.checklist[4], this.microForm.checklist[5], this.microForm.checklist[6], this.microForm.checklist[7], this.microForm.checklist[8]],
                        eval: this.microForm.eval,
                        catatan_membantu: this.microForm.catatan_membantu,
                        catatan_kendala: this.microForm.catatan_kendala,
                    }),
                });
                if (res.ok) {
                    this.microSubmitted = true;
                    this.loadHistory();
                }
            } catch (e) { console.error('Failed to submit micro break:', e); }
            this.loading = false;
        },

        resetMicroForm() {
            this.microForm = {
                checklist: { 1: null, 2: null, 3: null, 4: null, 5: null, 6: null, 7: null, 8: null },
                eval: { stres: null, fokus: null, kenyamanan: null },
                catatan_membantu: '',
                catatan_kendala: '',
            };
            this.microSubmitted = false;
        },

        async loadHistory() {
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const res = await fetch('/staf/daily-mental-check/history', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
                const data = await res.json();
                this.weekHistory = data.week_history.map(d => ({
                    label: d.label,
                    emoji: d.daily_check ? d.daily_check.emoji : '—',
                    filled: !!d.daily_check,
                    category: d.daily_check ? (d.daily_check.category === 'baik' ? 'Baik' : d.daily_check.category === 'perlu_perhatian' ? 'Sedang' : 'Buruk') : '',
                }));
                this.compliancePercent = data.compliance_percent;
            } catch (e) { console.error('Failed to load history:', e); }
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

        // Week history loaded from DB via init()
    }
}
</script>
@endsection
