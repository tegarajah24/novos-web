<div>
    {{-- Tab Navigation --}}
    <div class="flex max-w-2xl gap-1 bg-white rounded-2xl p-1.5 shadow-sm border border-gray-200 mb-8">
        <button wire:click="$set('activeTab', 0)" class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $activeTab === 0 ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            Dashboard
        </button>
        <button wire:click="$set('activeTab', 1)" class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $activeTab === 1 ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            Check-in
        </button>
        <button wire:click="loadActiveTab(2)" class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $activeTab === 2 ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            Riwayat
        </button>
        @if(auth()->user()->role->name !== 'Customer')
        <button wire:click="loadActiveTab(3)" class="flex-1 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ $activeTab === 3 ? 'bg-[#1a237e] text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
            Laporan
        </button>
        @endif
    </div>

    {{-- Tab 0: Dashboard --}}
    @if($activeTab === 0)
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-2xl overflow-hidden border border-indigo-200/60 min-h-[160px] relative bg-gray-100">
                @if($posterUrl)
                <img src="{{ $posterUrl }}" alt="Poster" class="w-full h-full object-cover">
                @else
                <div class="flex items-center justify-center h-full text-gray-400 text-sm">Belum ada poster</div>
                @endif
            </div>
            <div x-data="{ q: {{ Js::from($quotes[array_rand($quotes)]) }} }" class="bg-white rounded-2xl shadow-sm p-6 flex flex-col">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-3">Pesan Hari Ini</p>
                <div class="flex-1 flex flex-col justify-center">
                    <p class="text-gray-800 text-sm leading-relaxed italic mb-3">"<span x-text="q.text"></span>"</p>
                    <p class="text-xs text-gray-400" x-text="q.author"></p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Cek Hari Ini</h3>
            @if($todayCheck)
            <div class="flex items-center gap-4">
                <span class="text-3xl">{{ $todayCheck['category'] === 'baik' ? '😊' : ($todayCheck['category'] === 'perlu_perhatian' ? '😐' : '😟') }}</span>
                <div>
                    <p class="font-semibold text-gray-900">Skor: {{ $todayCheck['total_score'] }}/15</p>
                    <p class="text-xs text-gray-500">Kategori: {{ $todayCheck['category'] }}</p>
                </div>
            </div>
            @else
            <p class="text-sm text-gray-500">Belum melakukan cek hari ini. Buka tab <strong>Check-in</strong> untuk memulai.</p>
            @endif
        </div>
    </div>
    @endif

    {{-- Tab 1: Check-in --}}
    @if($activeTab === 1)
    <div class="space-y-6">
        {{-- Daily Check --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-1">Daily Check-in</h3>
            <p class="text-xs text-gray-400 mb-5">Seberapa setuju Anda dengan pernyataan berikut? (1 = tidak setuju, 3 = sangat setuju)</p>
            @if($todayCheck)
            <div class="p-4 bg-green-50 rounded-xl border border-green-200">
                <p class="text-sm font-semibold text-green-700">✔ Cek hari ini sudah dilakukan.</p>
                <p class="text-xs text-green-600 mt-1">Skor: {{ $todayCheck['total_score'] }}/15</p>
            </div>
            @else
            <div x-data="dailyCheckForm()" class="space-y-4">
                <template x-for="(q, i) in questions" :key="i">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-sm font-medium text-gray-800 mb-3" x-text="(i+1) + '. ' + q"></p>
                        <div class="flex gap-2">
                            <template x-for="val in [1,2,3]" :key="val">
                                <label class="flex-1 flex flex-col items-center gap-1 p-2 rounded-lg border cursor-pointer transition-all"
                                    :class="answers[i] === val ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'bg-white text-gray-600 border-gray-200'">
                                    <input type="radio" :name="'q'+i" :value="val" x-model="answers[i]" class="sr-only">
                                    <span class="text-xs font-bold" x-text="val === 1 ? 'Tidak' : (val === 2 ? 'Netral' : 'Setuju')"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="needHelp" class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                        <span class="text-sm font-medium text-amber-800">Saya ingin bicara dengan konselor / butuh bantuan</span>
                    </label>
                    <textarea x-show="needHelp" x-model="helpNote" rows="2" class="mt-3 w-full rounded-xl border border-amber-300 text-sm p-3" placeholder="Ceritakan apa yang Anda rasakan..."></textarea>
                </div>
                <button @click="submit()" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">Simpan Cek Harian</button>
            </div>
            <script>
            function dailyCheckForm() {
                return {
                    answers: [null, null, null, null, null],
                    questions: [
                        'Saya merasa bersemangat menjalani hari ini.',
                        'Saya mampu mengelola stres dengan baik.',
                        'Saya merasa didukung oleh rekan kerja.',
                        'Saya bisa fokus menyelesaikan pekerjaan.',
                        'Saya merasa cukup istirahat dan tidur.',
                    ],
                    needHelp: false,
                    helpNote: '',
                    submit() {
                        if (this.answers.some(a => a === null)) {
                            window.Notify?.warning('Harap jawab semua pertanyaan.');
                            return;
                        }
                        $wire.storeDailyCheck(this.answers, this.needHelp ? 'ya' : 'tidak', this.helpNote);
                    },
                };
            }
            </script>
            @endif
        </div>

        {{-- Micro Break --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-1">Micro Break Check</h3>
            <p class="text-xs text-gray-400 mb-5">Centang aktivitas yang sudah Anda lakukan hari ini:</p>
            @if($todayMicro)
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-200">
                <p class="text-sm font-semibold text-blue-700">✔ Micro break hari ini sudah dicatat.</p>
                <p class="text-xs text-blue-600 mt-1">Skor: {{ $todayMicro['score'] }}/9</p>
            </div>
            @else
            <div x-data="microBreakForm()" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <template x-for="(item, i) in items" :key="i">
                        <label class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:bg-green-50 hover:border-green-200 transition-all">
                            <input type="checkbox" x-model="checklist[i]" class="mt-0.5 w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <div>
                                <p class="text-xs font-semibold text-gray-800" x-text="item.label"></p>
                                <p class="text-[10px] text-gray-400" x-text="item.desc"></p>
                            </div>
                        </label>
                    </template>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Evaluasi (1-5):</label>
                    <div class="flex gap-2">
                        <template x-for="v in [1,2,3,4,5]" :key="v">
                            <label class="flex-1 flex flex-col items-center gap-1 p-2 rounded-lg border cursor-pointer transition-all"
                                :class="evalScore === v ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'bg-white text-gray-600 border-gray-200'">
                                <input type="radio" :value="v" x-model="evalScore" class="sr-only">
                                <span class="text-xs" x-text="v"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Apa yang paling membantu?</label>
                        <textarea x-model="membantu" rows="2" class="w-full rounded-xl border border-gray-200 text-sm p-3"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kendala?</label>
                        <textarea x-model="kendala" rows="2" class="w-full rounded-xl border border-gray-200 text-sm p-3"></textarea>
                    </div>
                </div>
                <button @click="submit()" class="px-6 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition-colors">Simpan Micro Break</button>
            </div>
            <script>
            function microBreakForm() {
                return {
                    checklist: Array(9).fill(false),
                    evalScore: 3,
                    membantu: '',
                    kendala: '',
                    items: [
                        { label: 'Peregangan', desc: 'Stretching ringan 2-3 menit' },
                        { label: 'Jalan Kaki', desc: 'Jalan-jalan sebentar' },
                        { label: 'Minum Air', desc: 'Minum air putih 1-2 gelas' },
                        { label: 'Meditasi', desc: 'Meditasi / tarik napas dalam' },
                        { label: 'Snack Sehat', desc: 'Makan camilan bergizi' },
                        { label: 'Dengar Musik', desc: 'Mendengarkan musik' },
                        { label: 'Ngobrol', desc: 'Chat dengan teman' },
                        { label: 'Tulis Jurnal', desc: 'Menulis perasaan' },
                        { label: 'Lainnya', desc: 'Aktivitas micro break lainnya' },
                    ],
                    submit() {
                        if (this.checklist.every(c => !c)) {
                            window.Notify?.warning('Centang setidaknya satu aktivitas.');
                            return;
                        }
                        $wire.storeMicroBreak(this.checklist, this.evalScore, this.membantu, this.kendala);
                    },
                };
            }
            </script>
            @endif
        </div>
    </div>
    @endif

    {{-- Tab 2: Riwayat --}}
    @if($activeTab === 2)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-5">
            <h3 class="font-bold text-gray-900">Riwayat 7 Hari</h3>
            <span class="text-xs text-gray-500">Kepatuhan: {{ $compliancePercent }}%</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="p-3 text-left">Hari</th>
                        <th class="p-3 text-center">Tanggal</th>
                        <th class="p-3 text-center">Daily</th>
                        <th class="p-3 text-center">Skor</th>
                        <th class="p-3 text-center">Micro</th>
                        <th class="p-3 text-center">Level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($weekHistory as $day)
                    <tr class="border-t border-gray-100">
                        <td class="p-3 font-semibold text-gray-800">{{ $day['label'] }}</td>
                        <td class="p-3 text-center text-gray-500">{{ $day['date'] }}</td>
                        <td class="p-3 text-center text-xl">
                            @if($day['daily_check'])
                            {{ $day['daily_check']['category'] === 'baik' ? '😊' : ($day['daily_check']['category'] === 'perlu_perhatian' ? '😐' : '😟') }}
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">{{ $day['daily_check']['score'] ?? '—' }}</td>
                        <td class="p-3 text-center">
                            @if($day['micro_break'])
                            <span class="text-green-500">✓</span>
                            @else
                            <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">{{ $day['micro_break']['level'] ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Tab 3: Laporan --}}
    @if($activeTab === 3 && auth()->user()->role->name !== 'Customer')
    @if($reportData)
    <div class="space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="text-2xl font-bold text-gray-900">{{ $reportData['today_summary']['total_staff'] }}</p>
                <p class="text-xs text-gray-500">Total Staf</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="text-2xl font-bold text-green-600">{{ $reportData['today_summary']['checked'] }}</p>
                <p class="text-xs text-gray-500">Sudah Cek</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="text-2xl font-bold text-orange-500">{{ $reportData['today_summary']['unchecked'] }}</p>
                <p class="text-xs text-gray-500">Belum Cek</p>
            </div>
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="text-2xl font-bold text-red-500">{{ $reportData['today_summary']['need_attention'] }}</p>
                <p class="text-xs text-gray-500">Perlu Perhatian</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h4 class="font-bold text-gray-900 mb-4">Ringkasan Mingguan</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <th class="p-3 text-left">Hari</th>
                            <th class="p-3 text-center">Diisi</th>
                            <th class="p-3 text-center">Rata-rata</th>
                            <th class="p-3 text-center">Baik</th>
                            <th class="p-3 text-center">Perlu Perhatian</th>
                            <th class="p-3 text-center">Butuh Pendampingan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['week_summary'] as $day)
                        <tr class="border-t border-gray-100">
                            <td class="p-3 font-semibold text-gray-800">{{ $day['label'] }}</td>
                            <td class="p-3 text-center">{{ $day['total_filled'] }}</td>
                            <td class="p-3 text-center">{{ $day['avg_score'] ?? '—' }}</td>
                            <td class="p-3 text-center"><span class="text-green-600 font-medium">{{ $day['baik'] }}</span></td>
                            <td class="p-3 text-center"><span class="text-orange-500 font-medium">{{ $day['perlu_perhatian'] }}</span></td>
                            <td class="p-3 text-center"><span class="text-red-500 font-medium">{{ $day['perlu_pendampingan'] }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-8 text-gray-500">Memuat data...</div>
    @endif
    @endif
</div>
