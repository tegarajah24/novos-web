@extends('layouts.internal')

@section('title', 'Daily Mental Check')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Daily Mental Check</h1>
@endsection

@section('internal-content')
<div x-data="dailyMentalCheck({ role: '{{ auth()->user()->role->name }}', posterUrl: '{{ $posterUrl }}', reminderTimes: {{ json_encode($reminderTimes) }} })">
    {{-- Tab Navigation --}}
    <div class="hidden md:flex max-w-2xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
        <template x-for="(tab, i) in tabs" :key="i">
            <button @click="activeTab = i; location.hash = 'dmc=' + i"
                :class="activeTab === i ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
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
            <template x-if="posterUrl">
            <div class="lg:col-span-2 rounded-2xl overflow-hidden border border-indigo-200/60 min-h-[160px] relative">
                <img :src="posterUrl" alt="Poster Kesehatan Mental" loading="lazy" class="w-full h-full object-cover">
                <button x-show="userRole === 'Super Admin'" @click="toggleManagePosters()"
                    class="absolute top-2 right-2 z-10 px-2.5 py-1.5 bg-white/90 hover:bg-white text-[#1a237e] text-xs font-semibold rounded-lg shadow-sm border border-gray-200 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                    Kelola Poster
                </button>
            </div>
            </template>
            <template x-if="!posterUrl">
            <div class="lg:col-span-2 rounded-2xl border border-dashed border-gray-300 min-h-[160px] flex items-center justify-center bg-gray-50/50 relative">
                <div class="text-center">
                    <i data-lucide="image" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                    <p class="text-sm text-gray-400">Belum ada poster</p>
                    <button x-show="userRole === 'Super Admin'" @click="toggleManagePosters()"
                        class="mt-2 px-3 py-1.5 bg-[#1a237e] text-white text-xs font-semibold rounded-lg hover:bg-[#283593] transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                        Upload Poster
                    </button>
                </div>
            </div>
            </template>

            {{-- Card: Pesan Motivasi Hari Ini --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pesan Hari Ini</p>
                    <button @click="refreshQuote" class="p-1.5 text-gray-400 hover:text-[#1a237e] rounded-lg hover:bg-gray-50 transition-colors" title="Ganti kutipan">
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
            <div class="bg-white rounded-2xl shadow-sm p-6 h-full">
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
            <div class="bg-white rounded-2xl shadow-sm p-6 h-full">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Reminder Berikutnya</p>
                    <button x-show="userRole === 'Super Admin'" @click="openSettings()"
                        class="p-1.5 text-gray-400 hover:text-[#1a237e] rounded-lg hover:bg-gray-50 transition-colors" title="Atur Jadwal Reminder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </button>
                </div>
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
            <div class="bg-white rounded-2xl shadow-sm p-6 h-full">
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
        <div class="bg-white rounded-2xl shadow-sm p-6">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-4">Riwayat 7 Hari Terakhir</p>
                <div class="flex items-center gap-3 justify-center flex-wrap max-md:max-h-[180px] max-md:overflow-y-auto">
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
        <div class="flex items-start justify-between max-md:mb-4 mb-6 gap-3">
            <h2 class="max-md:text-[20px] max-md:leading-[1.3] text-xl font-bold text-gray-900">Bagaimana kondisi Anda hari ini?</h2>
            <button @click="petunjukOpen = true" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-[#1a237e] bg-white hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                Petunjuk Pengisian
            </button>
        </div>

        {{-- Sudah diisi hari ini --}}
        <template x-if="submitted">
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <span class="text-5xl block mb-3" x-text="dailyResult.emoji"></span>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Check-in Hari Ini Selesai</h3>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                        :class="dailyResult.badgeClass" x-text="dailyResult.category"></span>
                    <p class="text-sm text-gray-600 mt-4" x-text="dailyResult.message"></p>
                </div>


                {{-- Catatan bantuan --}}
                            <template x-if="form.needHelp === 'ya' && form.helpNote">
                                    <div class="bg-white rounded-2xl shadow-sm p-6">
                                        <h4 class="font-bold text-gray-900 text-sm mb-2">Catatan Anda</h4>
                                        <p class="text-sm text-gray-600 italic bg-white rounded-xl p-4 border border-gray-100" x-text="form.helpNote"></p>
                                    </div>
                                </template>

                            </div>
                        </template>

                        {{-- Form --}}
                        <template x-if="!submitted">
                            <div class="space-y-6">
                                {{-- Tabel Pertanyaan --}}
                                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
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
                                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                <div class="bg-white rounded-2xl shadow-sm p-6">
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
                                    <button @click="submitForm()" :disabled="!allAnswered || loading"
                                        :class="allAnswered && !loading ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
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
            <button @click="microPetunjukOpen = true" class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-500 hover:text-[#1a237e] bg-white hover:bg-gray-50 rounded-lg border border-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                Petunjuk Pengisian
            </button>
        </div>

        {{-- Sudah diisi --}}
        <template x-if="microSubmitted">
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <span class="text-5xl block mb-3" x-text="microScore >= 7 ? '🎉' : microScore >= 4 ? '👍' : '💪'"></span>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Micro-Break Hari Ini Selesai</h3>
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                        :class="microLevel.class" x-text="'Kepatuhan ' + microLevel.label"></span>
                </div>



                {{-- Catatan Pekerja --}}
                <div class="bg-white rounded-2xl shadow-sm p-6" x-show="microForm.catatan_membantu || microForm.catatan_kendala">
                    <h4 class="font-bold text-gray-900 text-sm mb-4">Catatan Pekerja</h4>
                    <div class="space-y-3" x-show="microForm.catatan_membantu">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Apa yang paling membantu?</p>
                        <p class="text-sm text-gray-600 italic bg-white rounded-xl p-4 border border-gray-100" x-text="microForm.catatan_membantu"></p>
                    </div>
                    <div class="space-y-3" x-show="microForm.catatan_kendala">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Kendala yang dialami</p>
                        <p class="text-sm text-gray-600 italic bg-white rounded-xl p-4 border border-gray-100" x-text="microForm.catatan_kendala"></p>
                    </div>
                </div>

            </div>
        </template>

        {{-- Form --}}
        <template x-if="!microSubmitted">
            <div class="space-y-6">
                {{-- A. Checklist Pelaksanaan SMART-WORK Micro-Break --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 pt-5 pb-2">
                        <h3 class="font-bold text-gray-900 max-md:text-sm">Checklist Pelaksanaan SMART-WORK Micro-Break</h3>
                    </div>
                    <div class="overflow-x-auto max-md:max-h-[300px] max-md:overflow-y-auto">
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
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                                    <tr class="hover:bg-gray-50 transition-colors">
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
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 pt-5 pb-2">
                        <h3 class="font-bold text-gray-900 max-md:text-sm">Evaluasi Manfaat Setelah Micro-Break</h3>
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
                                <tr class="hover:bg-gray-50 transition-colors">
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
                                <tr class="hover:bg-gray-50 transition-colors">
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
                                <tr class="hover:bg-gray-50 transition-colors">
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
                <div class="bg-white rounded-2xl shadow-sm p-6">
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
                    <button @click="submitMicroForm()" :disabled="!allChecklistAnswered || !allEvalAnswered || loading"
                        :class="allChecklistAnswered && allEvalAnswered && !loading ? 'bg-[#1a237e] hover:bg-[#283593] cursor-pointer' : 'bg-gray-300 cursor-not-allowed'"
                        class="px-8 py-3 text-white rounded-xl font-semibold transition-colors shadow-sm inline-flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Kirim Jawaban
                    </button>
                </div>
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
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

    {{-- ========== TAB 4: LAPORAN (Super Admin & Manager only) ========== --}}
    <div x-show="activeTab === 3" x-cloak x-transition:enter.duration.300 class="space-y-6">
        {{-- Export Buttons --}}
        <div class="flex justify-end gap-2 max-md:justify-between max-md:items-center max-md:bg-white max-md:rounded-2xl max-md:shadow-sm max-md:p-4" x-show="reportLoaded">
            <div class="hidden max-md:block">
                <p class="font-bold text-gray-900 text-sm">Unduh File</p>
            </div>
            <div class="flex gap-2 shrink-0">
                <a href="{{ route('staf.daily-mental-check.export-csv') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('staf.daily-mental-check.export-excel') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-600 border border-green-600 rounded-lg text-xs font-semibold text-white hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" x-show="reportLoaded">
            <div class="bg-white rounded-2xl shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-gray-900" x-text="reportData.today_summary.total_staff"></p>
                <p class="text-xs text-gray-500 mt-1">Total Staff</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-emerald-600" x-text="reportData.today_summary.checked"></p>
                <p class="text-xs text-gray-500 mt-1">Sudah Check-in Hari Ini</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-gray-400" x-text="reportData.today_summary.unchecked"></p>
                <p class="text-xs text-gray-500 mt-1">Belum Check-in</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-red-500" x-text="reportData.today_summary.need_attention"></p>
                <p class="text-xs text-gray-500 mt-1">Perlu Perhatian</p>
            </div>
        </div>

        {{-- Tabel Staff Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Kondisi Staff Hari Ini</h3>
                <span class="text-xs text-gray-400" x-text="todayDate"></span>
            </div>
            <div class="overflow-x-auto max-md:overflow-y-auto max-md:max-h-[300px]" x-show="reportLoaded">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80">
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Staff</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Role</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Daily Check</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Skor</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Butuh Bantuan</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-700">Micro-Break</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="s in reportData.staff_today" :key="s.user_id">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <template x-if="s.avatar">
                                            <img :src="'/storage/' + s.avatar" class="w-7 h-7 rounded-full object-cover shrink-0">
                                        </template>
                                        <template x-if="!s.avatar">
                                            <div class="w-7 h-7 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold shrink-0" x-text="s.name.charAt(0).toUpperCase()"></div>
                                        </template>
                                        <span class="font-medium text-gray-900" x-text="s.name"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-500 text-xs" x-text="s.role"></td>
                                <td class="px-4 py-3 text-center">
                                    <template x-if="s.daily_check">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                            :class="s.daily_check.category === 'baik' ? 'bg-emerald-100 text-emerald-800' : s.daily_check.category === 'perlu_perhatian' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'"
                                            x-text="s.daily_check.category === 'baik' ? 'Baik' : s.daily_check.category === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan'">
                                        </span>
                                    </template>
                                    <template x-if="!s.daily_check">
                                        <span class="text-gray-300">—</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold" x-text="s.daily_check ? s.daily_check.score : '—'"></td>
                                <td class="px-4 py-3 text-center">
                                    <template x-if="s.daily_check?.need_help">
                                        <span class="text-red-500 text-xs font-semibold" title="Butuh bantuan" x-text="s.daily_check.help_note ? 'Ya' : 'Ya'"></span>
                                    </template>
                                    <template x-if="!s.daily_check?.need_help">
                                        <span class="text-gray-300">—</span>
                                    </template>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <template x-if="s.micro_break">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                            :class="s.micro_break.level === 'tinggi' ? 'bg-emerald-100 text-emerald-800' : s.micro_break.level === 'sedang' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'"
                                            x-text="s.micro_break.level">
                                        </span>
                                    </template>
                                    <template x-if="!s.micro_break">
                                        <span class="text-gray-300">—</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div x-show="!reportLoaded && !reportError" class="text-center py-8 text-gray-400 text-sm">
                <p>Memuat data laporan...</p>
            </div>
            <div x-show="reportError" class="text-center py-8 text-red-400 text-sm">
                <p x-text="reportError"></p>
            </div>
        </div>

        {{-- Ringkasan Mingguan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Tabel Ringkasan Minggu --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Ringkasan 7 Hari</h3>
                </div>
                <div class="overflow-x-auto" x-show="reportLoaded">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-3 py-2.5 text-left font-semibold text-gray-600">Hari</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Diisi</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Rata-rata</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Baik</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Perhatian</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Pendampingan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(day, i) in reportData.week_summary" :key="i">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2.5 font-medium text-gray-900" x-text="day.label"></td>
                                    <td class="px-3 py-2.5 text-center text-gray-700" x-text="day.total_filled + '/' + reportData.today_summary.total_staff"></td>
                                    <td class="px-3 py-2.5 text-center font-semibold" x-text="day.avg_score ?? '—'"></td>
                                    <td class="px-3 py-2.5 text-center text-emerald-600 font-medium" x-text="day.baik"></td>
                                    <td class="px-3 py-2.5 text-center text-amber-600 font-medium" x-text="day.perlu_perhatian"></td>
                                    <td class="px-3 py-2.5 text-center text-red-600 font-medium" x-text="day.perlu_pendampingan"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Statistik per Staff --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Statistik Staff (7 Hari)</h3>
                </div>
                <div class="overflow-x-auto max-h-[400px] overflow-y-auto" x-show="reportLoaded">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-3 py-2.5 text-left font-semibold text-gray-600">Staff</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Hari</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Rata-rata</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Terburuk</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-gray-600">Micro</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="s in reportData.staff_stats" :key="s.user_id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-3 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <template x-if="s.avatar">
                                                <img :src="'/storage/' + s.avatar" class="w-6 h-6 rounded-full object-cover shrink-0">
                                            </template>
                                            <template x-if="!s.avatar">
                                                <div class="w-6 h-6 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold shrink-0" x-text="s.name.charAt(0).toUpperCase()"></div>
                                            </template>
                                            <span class="text-gray-900 font-medium text-xs" x-text="s.name"></span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-gray-700 text-xs" x-text="s.total_days + '/7'"></td>
                                    <td class="px-3 py-2.5 text-center font-semibold text-xs" x-text="s.avg_score ?? '—'"></td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span x-show="s.worst_category" class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold"
                                            :class="s.worst_category === 'baik' ? 'bg-emerald-100 text-emerald-800' : s.worst_category === 'perlu_perhatian' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800'"
                                            x-text="s.worst_category === 'baik' ? 'Baik' : s.worst_category === 'perlu_perhatian' ? 'Perhatian' : 'Pendampingan'">
                                        </span>
                                        <span x-show="!s.worst_category" class="text-gray-300">—</span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-gray-700 text-xs" x-text="s.micro_days + '/7'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <template x-teleport="body">
    {{-- ========== MODAL: KELOLA POSTER (Super Admin only) ========== --}}
    <div x-show="managePosterOpen && userRole === 'Super Admin'" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

            <div x-show="managePosterOpen && userRole === 'Super Admin'" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" aria-hidden="true"></div>

            <div x-show="managePosterOpen && userRole === 'Super Admin'" x-transition.scale.origin.bottom class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">

                {{-- Header Modal --}}
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Kelola Poster</h3>
                    <button @click="managePosterOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-120px)]">

            {{-- Rotation Setting --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-sm font-semibold text-gray-800 mb-3">Periode Pergantian Poster</p>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" value="daily" x-model="rotationPeriod" class="w-4 h-4 text-[#1a237e] accent-[#1a237e] border-gray-300">
                        <span class="text-sm text-gray-700">Ganti Setiap Hari</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" value="weekly" x-model="rotationPeriod" class="w-4 h-4 text-[#1a237e] accent-[#1a237e] border-gray-300">
                        <span class="text-sm text-gray-700">Ganti Setiap Minggu</span>
                    </label>
                    <button @click="saveRotation()" :disabled="rotationSaving"
                        class="ml-auto px-3 py-1.5 bg-[#1a237e] text-white text-xs font-semibold rounded-lg hover:bg-[#283593] transition-colors disabled:opacity-50 inline-flex items-center gap-1.5">
                        <template x-if="rotationSaving">
                            <div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </template>
                        Simpan
                    </button>
                </div>
            </div>

            {{-- Upload New Poster --}}
            <div class="mb-6 p-4 bg-blue-50/50 rounded-xl border border-blue-200">
                <p class="text-sm font-semibold text-gray-800 mb-3">Upload Poster Baru</p>
                <input type="file" class="filepond" id="pondPoster" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" data-max-file-size="5MB" data-allow-multiple="false">
                <div class="mt-3">
                    <button @click="handleUpload()" :disabled="uploading"
                        class="px-4 py-1.5 bg-[#1a237e] text-white text-xs font-semibold rounded-lg hover:bg-[#283593] transition-colors disabled:opacity-50 inline-flex items-center gap-1.5">
                        <template x-if="uploading">
                            <div class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                        </template>
                        <template x-if="!uploading">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                        </template>
                        Upload
                    </button>
                </div>
            </div>

            {{-- Poster List --}}
            <div>
                <p class="text-sm font-semibold text-gray-800 mb-3">Poster Tersimpan</p>
                <template x-if="posterList.length === 0">
                    <p class="text-sm text-gray-400 italic text-center py-6">Belum ada poster. Upload poster pertama Anda.</p>
                </template>
                <div class="space-y-2.5">
                    <template x-for="p in posterList" :key="p.id">
                        <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="w-16 h-10 rounded-lg overflow-hidden bg-gray-200 shrink-0">
                                <img :src="p.url" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-800" x-text="p.created_at"></p>
                                <p class="text-[11px] text-gray-400" x-text="'Oleh: ' + (p.uploaded_by || '—')"></p>
                            </div>
                            <button @click="handleDelete(p.id)"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </template>

    {{-- MODAL: ATUR JADWAL REMINDER (Super Admin only) --}}
    <template x-teleport="body">
    <div x-show="settingsOpen && userRole === 'Super Admin'" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="settingsOpen" x-transition.opacity class="fixed inset-0 transition-opacity bg-black/40" aria-hidden="true"></div>
            <div x-show="settingsOpen" x-transition.scale.origin.bottom class="inline-block w-full max-w-lg p-6 my-8 text-left align-middle transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-200">
                <div class="flex justify-between items-center mb-6 bg-white -mx-6 -mt-6 p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Atur Jadwal Reminder</h3>
                    <button @click="settingsOpen = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <p class="text-sm text-gray-500 mb-4">Atur waktu dan pesan pengingat micro-break untuk staff.</p>

                <div class="space-y-4 mb-6">
                    <template x-for="(item, i) in editTimes" :key="i">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider" x-text="'Reminder ' + (i + 1)"></span>
                                <button @click="removeTime(i)" x-show="editTimes.length > 1"
                                    class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                            {{-- Time picker --}}
                            <div class="relative mb-2">
                                <button @click="toggleTimePicker(i)"
                                    class="w-full flex items-center justify-between bg-white border border-gray-200 rounded-lg px-3 py-2.5 text-sm hover:border-[#1a237e]/30 transition-colors text-left">
                                    <span class="text-gray-900 font-medium" x-text="item.time"></span>
                                    <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </button>
                                <div x-show="timePickerIndex === i" x-cloak @click.away="timePickerIndex = null"
                                    class="absolute top-full left-0 right-0 z-10 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-3">
                                    <div class="flex gap-4">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Jam</p>
                                            <div class="grid grid-cols-6 gap-1 max-h-36 overflow-y-auto">
                                                <template x-for="h in 24" :key="h">
                                                    <button @click="setTimeHour(i, h - 1)"
                                                        class="px-1.5 py-1 text-xs rounded-md transition-colors"
                                                        :class="Number(item.time.split(':')[0]) === h - 1 ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                                        x-text="String(h - 1).padStart(2, '0')">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-semibold text-gray-400 uppercase mb-1">Menit</p>
                                            <div class="grid grid-cols-4 gap-1">
                                                <template x-for="m in [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55]" :key="m">
                                                    <button @click="setTimeMinute(i, m)"
                                                        class="px-1.5 py-1 text-xs rounded-md transition-colors"
                                                        :class="Number(item.time.split(':')[1]) === m ? 'bg-[#1a237e] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                                        x-text="String(m).padStart(2, '0')">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Pesan --}}
                            <textarea x-model="item.message" rows="2"
                                placeholder="Tulis pesan reminder..."
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30 resize-none"></textarea>
                        </div>
                    </template>
                </div>

                <button @click="addTime()"
                    class="w-full mb-6 px-3 py-2 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-500 hover:text-[#1a237e] hover:border-[#1a237e] transition-colors inline-flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Waktu
                </button>

                <div class="flex gap-3">
                    <button @click="settingsOpen = false"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button @click="saveSettings()"
                        class="flex-1 px-4 py-2.5 bg-[#1a237e] text-white rounded-xl text-sm font-semibold hover:bg-[#283593] transition-colors inline-flex items-center justify-center gap-2">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>

<script>
function dailyMentalCheck(config = {}) {
    return {
        userRole: config.role || '',
        posterUrl: config.posterUrl || '',
        reminderTimes: config.reminderTimes || [
            { time: '10:00', message: 'Saatnya Micro-Break! \u2014 Minum air putih, peregangan ringan, dan tarik napas 3 kali.' },
            { time: '13:00', message: 'Istirahat Sejenak \u2014 Lakukan teknik STOP: Stop, Take a breath, Observe, Proceed.' },
            { time: '15:00', message: 'Sudahkah Anda Beristirahat? \u2014 Berdiri, tarik napas, dan kembali bekerja dengan lebih segar.' },
        ],
        settingsOpen: false,
        editTimes: [],
        timePickerIndex: null,
        activeTab: 0,
        todayFilled: false,
        submitted: false,
        petunjukOpen: false,
        loading: false,

        weekHistory: [],
        compliancePercent: 0,

        reportData: {
            today_summary: { total_staff: 0, checked: 0, unchecked: 0, need_attention: 0 },
            staff_today: [],
            week_summary: [],
            staff_stats: [],
        },
        reportLoaded: false,
        reportError: '',

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
            ...(['Super Admin', 'Manager'].includes(config.role) ? [{ label: 'Laporan', icon: '&#9733;' }] : []),
        ],

        async init() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
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

            const dmcMatch = location.hash.match(/^#dmc=(\d)$/);
            if (dmcMatch) this.activeTab = parseInt(dmcMatch[1]);

            window.addEventListener('hashchange', () => {
                const m = location.hash.match(/^#dmc=(\d)$/);
                if (m) this.activeTab = parseInt(m[1]);
            });

            if (['Super Admin', 'Manager'].includes(this.userRole)) {
                await this.fetchReport();
            }

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
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
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
                    await this.loadHistory();
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
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
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
                    await this.loadHistory();
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
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
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

        async fetchReport() {
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/staf/daily-mental-check/report', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                this.reportData = data;
                this.reportLoaded = true;
                this.reportError = '';
            } catch (e) {
                console.error('Failed to load report:', e);
                this.reportLoaded = false;
                this.reportError = 'Gagal memuat laporan. Coba refresh halaman.';
            }
        },

        // Poster Management
        managePosterOpen: false,
        posterList: [],
        uploading: false,
        rotationPeriod: 'daily',
        rotationSaving: false,

        async toggleManagePosters() {
            this.managePosterOpen = !this.managePosterOpen;
            if (!this.managePosterOpen) return;
            const pond = FilePond.find(document.querySelector('#pondPoster'));
            if (pond) pond.removeFiles();
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/staf/daily-mental-check/posters', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
                const data = await res.json();
                this.posterList = data.posters;
                this.rotationPeriod = data.rotation;
                this.$nextTick(() => {
                    try { if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons({ icons: window.lucide.icons }); } catch(e) {}
                });
            } catch (e) { console.error('Failed to load posters:', e); }
        },

        async handleUpload() {
            if (this.uploading) return;
            const pond = FilePond.find(document.querySelector('#pondPoster'));
            if (!pond || pond.getFiles().length === 0) return;
            const fileItem = pond.getFiles()[0];
            if (!(fileItem.file instanceof File)) return;
            this.uploading = true;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const fd = new FormData();
                fd.append('image', fileItem.file, fileItem.file.name);
                const res = await fetch('/staf/daily-mental-check/posters', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: fd,
                });
                if (res.ok) {
                    const data = await res.json();
                    this.posterList.unshift(data.poster);
                    this.posterUrl = data.poster.url;
                    pond.removeFiles();
                    this.$nextTick(() => {
                        try { if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons({ icons: window.lucide.icons }); } catch(e) {}
                    });
                }
            } catch (e) { console.error('Failed to upload poster:', e); }
            this.uploading = false;
        },

        async handleDelete(id) {
            const result = await Swal.fire({
                title: 'Hapus Poster?',
                text: 'Poster dan file gambar akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            });
            if (!result.isConfirmed) return;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/staf/daily-mental-check/posters/' + id, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
                if (res.ok) {
                    this.posterList = this.posterList.filter(p => p.id !== id);
                    // Refresh poster URL
                    const rotRes = await fetch('/staf/daily-mental-check/posters', {
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    });
                    const rotData = await rotRes.json();
                    if (rotData.posters.length > 0) {
                        this.posterUrl = rotData.posters[0].url;
                    } else {
                        this.posterUrl = '';
                    }
                    Notify.success('Poster berhasil dihapus');
                }
            } catch (e) { console.error('Failed to delete poster:', e); }
        },

        async saveRotation() {
            this.rotationSaving = true;
            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/staf/daily-mental-check/posters/rotation', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ rotation: this.rotationPeriod }),
                });
                if (res.ok) {
                    const data = await res.json();
                    this.posterUrl = data.posterUrl;
                    Notify.success('Periode pergantian poster diperbarui');
                }
            } catch (e) { console.error('Failed to save rotation:', e); }
            this.rotationSaving = false;
        },

        // Reminder Settings
        openSettings() {
            this.editTimes = this.reminderTimes.map(r => ({
                time: typeof r === 'string' ? r : r.time,
                message: r.message || 'Waktunya Micro-Break!',
            }));
            this.settingsOpen = true;
        },

        addTime() {
            this.editTimes.push({ time: '16:00', message: '' });
        },

        removeTime(index) {
            if (this.editTimes.length > 1) {
                this.editTimes.splice(index, 1);
            }
        },

        toggleTimePicker(i) {
            this.timePickerIndex = this.timePickerIndex === i ? null : i;
        },

        setTimeHour(i, h) {
            const [_, m] = this.editTimes[i].time.split(':').map(Number);
            this.editTimes[i].time = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        },

        setTimeMinute(i, m) {
            const [h, _] = this.editTimes[i].time.split(':').map(Number);
            this.editTimes[i].time = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        },

        async saveSettings() {
            if (this.editTimes.length === 0) return;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch('/staf/daily-mental-check/reminder-times', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ times: this.editTimes }),
                });
                const data = await res.json();
                if (data.success) {
                    this.reminderTimes = this.editTimes.map(r => ({ ...r }));
                    this.settingsOpen = false;
                    Notify.success(data.message);
                } else {
                    Notify.error(data.message || 'Gagal menyimpan.');
                }
            } catch (e) {
                Notify.error('Gagal menyimpan pengaturan.');
            }
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

            const times = this.reminderTimes.map(t => {
                const timeStr = typeof t === 'string' ? t : t.time;
                const [h, m] = timeStr.split(':').map(Number);
                let label;
                const hourNum = h + m / 60;
                if (hourNum < 12) label = 'Micro-Break Pagi ☕';
                else if (hourNum < 16) label = 'Micro-Break Siang 🌤️';
                else label = 'Micro-Break Sore ☕';
                return { time: timeStr, label, hour: h, min: m };
            }).sort((a, b) => a.hour * 60 + a.min - (b.hour * 60 + b.min));

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
