@extends('layouts.internal')

@section('title', 'Strest test')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Strest test</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')
@php
    $currentUser = auth()->user();
    $userRole = $currentUser->role?->name ?? 'Designer';
    $userName = $currentUser->name ?? 'Karyawan';
    $isManager = in_array($userRole, ['Manager', 'Super Admin']);
@endphp

<div x-data="stressTestApp()" x-init="initApp()" class="space-y-6">

    <!-- IF MANAGER OR SUPER ADMIN: SHOW ONLY OVERVIEW MANAGER -->
    @if($isManager)
        <!-- Summary Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total -->
            <div @click="filters.level = ''" class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:shadow transition duration-200 text-center">
                <h4 class="text-2xl font-black text-gray-800" x-text="employees.length"></h4>
                <p class="text-xs font-semibold text-gray-500 mt-1">Karyawan Terisi</p>
            </div>
            <!-- Rendah -->
            <div @click="filters.level = 'rendah'" class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:shadow transition duration-200 text-center border-l-4 border-l-green-500">
                <h4 class="text-2xl font-black text-green-600" x-text="countStats('rendah')"></h4>
                <p class="text-xs font-semibold text-green-600 mt-1">🟢 Rendah</p>
            </div>
            <!-- Sedang -->
            <div @click="filters.level = 'sedang'" class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:shadow transition duration-200 text-center border-l-4 border-l-yellow-500">
                <h4 class="text-2xl font-black text-yellow-600" x-text="countStats('sedang')"></h4>
                <p class="text-xs font-semibold text-yellow-600 mt-1">🟡 Sedang</p>
            </div>
            <!-- Tinggi -->
            <div @click="filters.level = 'tinggi'" class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:shadow transition duration-200 text-center border-l-4 border-l-orange-500">
                <h4 class="text-2xl font-black text-orange-600" x-text="countStats('tinggi')"></h4>
                <p class="text-xs font-semibold text-orange-600 mt-1">🟠 Tinggi</p>
            </div>
            <!-- Kritis -->
            <div @click="filters.level = 'kritis'" class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm cursor-pointer hover:shadow transition duration-200 text-center border-l-4 border-l-red-500">
                <h4 class="text-2xl font-black text-red-600" x-text="countStats('kritis')"></h4>
                <p class="text-xs font-semibold text-red-600 mt-1">🔴 Kritis</p>
            </div>
        </div>

        <!-- Filter & Export Bar -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search Input -->
                <div class="relative w-full md:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" placeholder="Cari Karyawan..." class="input input-sm input-bordered w-full pl-9 rounded-lg border-gray-300 focus:border-[#1a237e] focus:ring-[#1a237e]/20">
                </div>

                <!-- Dept Filter -->
                <select x-model="filters.dept" class="select select-sm select-bordered rounded-lg border-gray-300 text-sm font-medium">
                    <option value="">Semua Departemen</option>
                    <option value="Designer">Designer</option>
                    <option value="Admin">Admin</option>
                    <option value="Produksi">Produksi</option>
                    <option value="CS">CS</option>
                </select>

                <!-- Status Filter -->
                <select x-model="filters.level" class="select select-sm select-bordered rounded-lg border-gray-300 text-sm font-medium">
                    <option value="">Semua Tingkat Stres</option>
                    <option value="rendah">🟢 Rendah</option>
                    <option value="sedang">🟡 Sedang</option>
                    <option value="tinggi">🟠 Tinggi</option>
                    <option value="kritis">🔴 Kritis</option>
                </select>
            </div>

            <!-- Export Buttons -->
            <div class="flex items-center gap-2">
                <button @click="exportPDF()" class="btn btn-sm btn-outline border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 rounded-lg flex items-center gap-1">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    PDF
                </button>
                <button @click="exportCSV()" class="btn btn-sm btn-outline border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300 rounded-lg flex items-center gap-1">
                    <i data-lucide="file-spreadsheed" class="w-4 h-4"></i>
                    CSV
                </button>
                <button @click="exportExcel()" class="btn btn-sm btn-outline border-green-200 text-green-600 hover:bg-green-50 hover:border-green-300 rounded-lg flex items-center gap-1">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                    Excel
                </button>
            </div>
        </div>

        <!-- Trend Chart Section -->
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i data-lucide="trending-up" class="text-indigo-600 w-5 h-5"></i>
                📈 GRAFIK TREN STRESS KARYAWAN (6 Bulan Terakhir)
            </h3>
            <div class="h-64 md:h-72 w-full relative">
                <canvas id="trendChartCanvas"></canvas>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">
                🔵 Rata-rata Skor Stress Karyawan
            </p>
        </div>

        <!-- Employee List Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-800">📋 DAFTAR HASIL ASSESSMENT KARYAWAN</h3>
                <span class="text-xs text-gray-500 font-medium" x-text="'Total: ' + filteredEmployees.length + ' Karyawan'"></span>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="table w-full">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200">
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Tanggal</th>
                            <th>Skor</th>
                            <th>Tingkat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <template x-for="emp in paginatedEmployees" :key="emp.name">
                            <tr class="hover:bg-gray-50 border-b border-gray-100 transition">
                                <td class="font-bold text-gray-900" x-text="emp.name"></td>
                                <td class="text-gray-600" x-text="emp.dept"></td>
                                <td class="text-gray-500" x-text="emp.date"></td>
                                <td class="font-semibold text-gray-800" x-text="emp.score + '/40'"></td>
                                <td>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border"
                                          :class="getCategoryDetails(emp.score).bgClass">
                                        <span x-text="getCategoryDetails(emp.score).icon"></span>
                                        <span x-text="getCategoryDetails(emp.score).name"></span>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button @click="showDetailEmployee(emp)" class="btn btn-xs btn-outline btn-primary rounded-md flex items-center gap-1 mx-auto">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        👁️ Detail
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredEmployees.length === 0">
                            <td colspan="6" class="text-center py-8 text-gray-500 font-medium">
                                Tidak ada karyawan yang sesuai filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Pagination -->
            <div class="p-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs md:text-sm text-gray-600">
                <div x-text="'Menampilkan ' + pageStart + '–' + pageEnd + ' dari ' + filteredEmployees.length + ' karyawan'"></div>
                <div class="flex items-center gap-1">
                    <button @click="prevPage()" :disabled="currentPage === 1" class="btn btn-xs btn-outline border-gray-300 rounded-md disabled:opacity-50">
                        &lt; Prev
                    </button>
                    <template x-for="page in totalPages" :key="page">
                        <button @click="currentPage = page" 
                                :class="currentPage === page ? 'bg-[#1a237e] text-white border-[#1a237e]' : 'btn-outline border-gray-300 text-gray-700'"
                                class="btn btn-xs px-2.5 rounded-md" x-text="page"></button>
                    </template>
                    <button @click="nextPage()" :disabled="currentPage === totalPages" class="btn btn-xs btn-outline border-gray-300 rounded-md disabled:opacity-50">
                        Next &gt;
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- IF REGULAR EMPLOYEE: SHOW ONLY ASSESSMENT FORM OR RESULT -->
        
        <!-- FORM ASSESSMENT -->
        <div x-show="viewState === 'form'" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-[#1a237e] to-[#283593] p-6 text-white text-center">
                <h3 class="text-xl font-bold">📋 STRESS TEST PERIODIK</h3>
                <p class="text-sm opacity-90 mt-1">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
                <div class="max-w-xl mx-auto mt-4 text-xs bg-white/10 p-3 rounded-lg border border-white/10 backdrop-blur-sm">
                    Jawab 10 pertanyaan berikut dengan jujur. Pilih jawaban yang paling sesuai dengan kondisi Anda.
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Info Alert -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 text-blue-800 text-xs md:text-sm">
                    <i data-lucide="info" class="w-5 h-5 shrink-0 text-blue-600"></i>
                    <div>
                        <span class="font-bold">Privasi Terjaga:</span> Hasil tes ini digunakan untuk memantau kesehatan mental tim dan menentukan beban kerja yang sehat. Mohon isi sesuai keadaan sebenarnya.
                    </div>
                </div>

                <!-- 10 Questions -->
                <div class="space-y-6">
                    <template x-for="(q, index) in questions" :key="q.id">
                        <div class="bg-gray-50 p-5 rounded-xl border border-gray-100 hover:border-gray-200 transition">
                            <div class="flex gap-3 items-start">
                                <span class="bg-[#1a237e] text-white font-bold w-6 h-6 rounded-full flex items-center justify-center text-xs shrink-0 mt-0.5" x-text="index + 1"></span>
                                <div class="flex-1">
                                    <p class="text-gray-800 font-semibold text-sm md:text-base" x-text="q.text"></p>
                                    
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-4">
                                        <label :class="currentAnswers[index] === '1' ? 'border-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'border-gray-200 bg-white text-gray-600'" 
                                               class="flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition select-none text-center text-xs md:text-sm font-medium">
                                            <input type="radio" :name="'q_' + q.id" value="1" x-model="currentAnswers[index]" class="radio radio-primary radio-xs">
                                            <span>Tidak Setuju</span>
                                        </label>
                                        <label :class="currentAnswers[index] === '2' ? 'border-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'border-gray-200 bg-white text-gray-600'" 
                                               class="flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition select-none text-center text-xs md:text-sm font-medium">
                                            <input type="radio" :name="'q_' + q.id" value="2" x-model="currentAnswers[index]" class="radio radio-primary radio-xs">
                                            <span>Netral</span>
                                        </label>
                                        <label :class="currentAnswers[index] === '3' ? 'border-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'border-gray-200 bg-white text-gray-600'" 
                                               class="flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition select-none text-center text-xs md:text-sm font-medium">
                                            <input type="radio" :name="'q_' + q.id" value="3" x-model="currentAnswers[index]" class="radio radio-primary radio-xs">
                                            <span>Setuju</span>
                                        </label>
                                        <label :class="currentAnswers[index] === '4' ? 'border-[#1a237e] bg-[#1a237e]/5 text-[#1a237e]' : 'border-gray-200 bg-white text-gray-600'" 
                                               class="flex items-center justify-center gap-2 p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition select-none text-center text-xs md:text-sm font-medium">
                                            <input type="radio" :name="'q_' + q.id" value="4" x-model="currentAnswers[index]" class="radio radio-primary radio-xs">
                                            <span>Sangat Setuju</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button @click="submitAssessment()" class="btn bg-[#1a237e] hover:bg-[#283593] text-white px-8 rounded-lg shadow-md border-0 transition duration-200">
                        <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                        Submit Assessment
                    </button>
                </div>
            </div>
        </div>

        <!-- HASIL AFTER SUBMIT -->
        <div x-show="viewState === 'result'" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden max-w-3xl mx-auto" x-cloak>
            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 p-6 text-white text-center">
                <h3 class="text-xl font-bold flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                    HASIL STRESS TEST
                </h3>
                <p class="text-xs opacity-90 mt-1">Selesai pada: <span x-text="lastSubmitDate"></span></p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Score Card -->
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 text-center max-w-md mx-auto">
                    <span class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Skor Anda</span>
                    <h4 class="text-5xl font-extrabold text-gray-800 mt-2" x-text="latestResult.score + ' / 40'"></h4>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3.5 mt-4 overflow-hidden border border-gray-300">
                        <div class="h-full transition-all duration-1000 ease-out" 
                             :class="latestResult.color === 'green' ? 'bg-green-500' : (latestResult.color === 'yellow' ? 'bg-yellow-500' : (latestResult.color === 'orange' ? 'bg-orange-500' : 'bg-red-500'))" 
                             :style="'width: ' + (latestResult.score / 40 * 100) + '%'"></div>
                    </div>

                    <!-- Category Badge -->
                    <div class="mt-4 flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold border"
                              :class="latestResult.bgClass">
                            <span x-text="latestResult.icon"></span>
                            Kategori: <span class="uppercase" x-text="latestResult.level"></span>
                        </span>
                    </div>
                </div>

                <!-- Recommendation Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex items-center gap-2 font-bold text-gray-800 text-sm md:text-base">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-yellow-500"></i>
                        💡 REKOMENDASI PEMULIHAN
                    </div>
                    <div class="p-5">
                        <p class="text-gray-700 text-sm mb-4">
                            Berdasarkan hasil assessment Anda yang berada di kategori <span class="font-bold text-gray-900" x-text="latestResult.level"></span>, berikut langkah-langkah yang disarankan:
                        </p>
                        <ul class="space-y-3">
                            <template x-for="rec in latestResult.recommendations">
                                <li class="flex items-start gap-2.5 text-sm text-gray-600">
                                    <span class="text-emerald-500 shrink-0 mt-0.5">☑️</span>
                                    <span x-text="rec"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3 pt-4 border-t border-gray-100">
                    <button @click="showHistoryModal = true" class="btn btn-outline btn-sm text-gray-700 border-gray-300 hover:bg-gray-50 hover:text-gray-900 rounded-lg flex items-center gap-1.5">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        📊 Lihat Riwayat
                    </button>
                    <button @click="resetForm()" class="btn bg-orange-500 hover:bg-orange-600 text-white border-0 btn-sm rounded-lg flex items-center gap-1.5">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        🔄 Isi Ulang
                    </button>
                    <a href="/dashboard" class="btn bg-[#1a237e] hover:bg-[#283593] text-white border-0 btn-sm rounded-lg flex items-center gap-1.5">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        🏠 Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 1: RIWAYAT ASSESSMENT (USER) -->
    <div x-show="showHistoryModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl shadow-2xl border border-gray-100 w-full max-w-lg overflow-hidden" @click.away="showHistoryModal = false">
            <div class="bg-[#1a237e] p-4 text-white flex justify-between items-center">
                <h3 class="font-bold flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    Riwayat Assessment Anda
                </h3>
                <button @click="showHistoryModal = false" class="text-white/80 hover:text-white font-bold text-lg">&times;</button>
            </div>
            <div class="p-5 max-h-96 overflow-y-auto space-y-3">
                <template x-for="(hist, index) in resultsHistory" :key="index">
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium" x-text="hist.date"></p>
                            <p class="text-sm font-semibold text-gray-800 mt-1">Skor: <span x-text="hist.score + '/40'"></span></p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                              :class="getCategoryDetails(hist.score).bgClass">
                            <span x-text="getCategoryDetails(hist.score).icon"></span>
                            <span x-text="getCategoryDetails(hist.score).name"></span>
                        </span>
                    </div>
                </template>
                <div x-show="resultsHistory.length === 0" class="text-center py-6 text-gray-500">
                    Belum ada riwayat pengisian.
                </div>
            </div>
            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end">
                <button @click="showHistoryModal = false" class="btn btn-sm bg-[#1a237e] hover:bg-[#283593] border-0 text-white rounded-lg px-5">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: DETAIL JAWABAN KARYAWAN (MANAGER) -->
    <div x-show="showDetailModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" x-transition x-cloak>
        <div class="bg-white rounded-xl shadow-2xl border border-gray-100 w-full max-w-2xl overflow-hidden" @click.away="showDetailModal = false">
            <div class="bg-[#1a237e] p-4 text-white flex justify-between items-center">
                <h3 class="font-bold flex items-center gap-2">
                    <i data-lucide="user-check" class="w-5 h-5"></i>
                    Detail Hasil Assessment Karyawan
                </h3>
                <button @click="showDetailModal = false" class="text-white/80 hover:text-white font-bold text-lg">&times;</button>
            </div>
            
            <div class="p-6 max-h-[70vh] overflow-y-auto space-y-6" x-if="selectedEmployee">
                <!-- Employee Info Card -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900" x-text="selectedEmployee.name"></h4>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                            <span>Dept: <strong class="text-gray-700" x-text="selectedEmployee.dept"></strong></span>
                            <span>•</span>
                            <span>Tanggal: <strong class="text-gray-700" x-text="selectedEmployee.date"></strong></span>
                        </div>
                    </div>
                    <div class="flex flex-col items-start sm:items-end gap-1.5">
                        <span class="text-xs font-semibold text-gray-500">Total Skor: <strong class="text-sm text-gray-800" x-text="selectedEmployee.score + '/40'"></strong></span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border"
                              :class="getCategoryDetails(selectedEmployee.score).bgClass">
                            <span x-text="getCategoryDetails(selectedEmployee.score).icon"></span>
                            <span x-text="getCategoryDetails(selectedEmployee.score).name"></span>
                        </span>
                    </div>
                </div>

                <!-- Answer Breakdown -->
                <div class="space-y-4">
                    <h5 class="text-sm font-bold text-gray-800">Detail Pilihan Jawaban:</h5>
                    <div class="space-y-3">
                        <template x-for="(q, idx) in questions" :key="q.id">
                            <div class="p-3 rounded-lg border border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-3">
                                <div class="flex gap-2">
                                    <span class="text-[#1a237e] font-bold text-sm shrink-0" x-text="(idx+1) + '.'"></span>
                                    <p class="text-xs md:text-sm text-gray-700 font-medium" x-text="q.text"></p>
                                </div>
                                <div class="shrink-0 flex items-center gap-2">
                                    <span class="text-xs font-semibold text-gray-500">Jawaban:</span>
                                    <span :class="getAnswerLabelClass(selectedEmployee.answers[idx])"
                                          class="px-2.5 py-1 rounded-md text-xs font-bold"
                                          x-text="getAnswerLabel(selectedEmployee.answers[idx])"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 border-t border-gray-100 flex justify-end">
                <button @click="showDetailModal = false" class="btn btn-sm bg-[#1a237e] hover:bg-[#283593] border-0 text-white rounded-lg px-5">Tutup</button>
            </div>
        </div>
    </div>

</div>

<!-- Loading Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function stressTestApp() {
    return {
        // Authenticated user credentials from Laravel
        currentUser: {
            name: '{{ $userName }}',
            role: '{{ $userRole }}',
            isManager: {{ $isManager ? 'true' : 'false' }}
        },

        // View States
        viewState: 'form', // 'form' or 'result'
        
        // Modal states
        showHistoryModal: false,
        showDetailModal: false,
        
        // Data & history
        lastSubmitDate: '',
        latestResult: {
            score: 0,
            level: '',
            color: 'green',
            bgClass: '',
            icon: '🟢',
            recommendations: []
        },
        resultsHistory: [],
        
        // Current assessment state
        currentAnswers: Array(10).fill(null),
        
        // Question bank
        questions: [
            { id: 1, text: 'Saya merasa kewalahan dengan volume pekerjaan' },
            { id: 2, text: 'Saya sulit berkonsentrasi karena tekanan kerja' },
            { id: 3, text: 'Saya merasa lelah dan tidak bersemangat dalam bekerja' },
            { id: 4, text: 'Saya merasa tidak bisa menyelesaikan semua tugas yang diberikan' },
            { id: 5, text: 'Saya sudah tidak merasa menikmati pekerjaan saya' },
            { id: 6, text: 'Saya merasa tidak mendapat dukungan dari tim' },
            { id: 7, text: 'Saya sering melakukan kesalahan dalam bekerja' },
            { id: 8, text: 'Saya merasa waktu istirahat saya terganggu pekerjaan' },
            { id: 9, text: 'Saya merasa pekerjaan saya tidak dihargai' },
            { id: 10, text: 'Saya merasa tidak memiliki kendali atas pekerjaan saya' }
        ],
        
        // Manager Overview Sync Data (initialized in localStorage)
        employees: [],
        
        // Selected employee for modal detail
        selectedEmployee: null,
        
        // Filters
        filters: {
            search: '',
            dept: '',
            level: ''
        },
        
        // Pagination
        currentPage: 1,
        perPage: 5,
        
        // Chart object ref
        chartInstance: null,
        
        // Calculate category details dynamically
        getCategoryDetails(score) {
            if (score <= 16) {
                return {
                    name: 'Rendah',
                    color: 'green',
                    bgClass: 'bg-green-100 text-green-800 border-green-200',
                    icon: '🟢',
                    recommendations: [
                        'Pertahankan kondisi fisik dan mental Anda dengan gaya hidup sehat.',
                        'Sempatkan beristirahat sejenak di sela-sela rutinitas pekerjaan.',
                        'Lanjutkan komunikasi yang baik dengan tim dan atasan.'
                    ]
                };
            } else if (score <= 24) {
                return {
                    name: 'Sedang',
                    color: 'yellow',
                    bgClass: 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    icon: '🟡',
                    recommendations: [
                        'Evaluasi kembali skala prioritas tugas-tugas harian Anda.',
                        'Ambil jeda istirahat singkat (micro-breaks) lebih sering untuk menyegarkan pikiran.',
                        'Luangkan waktu untuk melakukan hobi atau aktivitas relaksasi setelah jam kerja.',
                        'Diskusikan dengan tim jika ada beban kerja yang bisa didelegasikan.'
                    ]
                };
            } else if (score <= 32) {
                return {
                    name: 'Tinggi',
                    color: 'orange',
                    bgClass: 'bg-orange-100 text-orange-800 border-orange-200',
                    icon: '🟠',
                    recommendations: [
                        'Segera diskusikan beban kerja Anda dengan atasan.',
                        'Ambil waktu istirahat yang cukup di sela-sela pekerjaan.',
                        'Jangan ragu untuk meminta bantuan rekan kerja.',
                        'Hubungi HRD jika Anda merasa perlu konseling lebih lanjut.'
                    ]
                };
            } else {
                return {
                    name: 'Kritis',
                    color: 'red',
                    bgClass: 'bg-red-100 text-red-800 border-red-200',
                    icon: '🔴',
                    recommendations: [
                        'Batasi lembur dan jadwalkan cuti istirahat sesegera mungkin.',
                        'Komunikasikan secara mendalam kepada atasan dan HRD mengenai kondisi kesehatan mental Anda.',
                        'Pertimbangkan berkonsultasi dengan profesional (psikolog/konselor).',
                        'Terapkan batasan yang jelas antara kehidupan pribadi dan pekerjaan (work-life boundary).'
                    ]
                };
            }
        },
        
        getAnswerLabel(scoreStr) {
            const score = parseInt(scoreStr);
            if (score === 1) return 'Tidak Setuju';
            if (score === 2) return 'Netral';
            if (score === 3) return 'Setuju';
            if (score === 4) return 'Sangat Setuju';
            return '-';
        },
        
        getAnswerLabelClass(scoreStr) {
            const score = parseInt(scoreStr);
            if (score === 1) return 'bg-gray-100 text-gray-700';
            if (score === 2) return 'bg-blue-100 text-blue-800';
            if (score === 3) return 'bg-orange-100 text-orange-800';
            if (score === 4) return 'bg-red-100 text-red-800';
            return 'bg-gray-100 text-gray-500';
        },
        
        // Helper to count statistics for the stat cards
        countStats(level) {
            return this.employees.filter(emp => {
                const cat = this.getCategoryDetails(emp.score).name.toLowerCase();
                return cat === level;
            }).length;
        },
        
        // Initialize localStorage & default values
        initApp() {
            // 1. Initializing employee sync data list in localStorage
            const storedEmployees = localStorage.getItem('nvs_employees_data');
            if (storedEmployees) {
                this.employees = JSON.parse(storedEmployees);
            } else {
                // Default dummy employee results if never stored before
                this.employees = [
                    { name: 'Andi Desainer', dept: 'Designer', date: '7 Jun 2026', score: 32, answers: [3,4,3,4,3,2,3,2,4,4] },
                    { name: 'Budi Admin', dept: 'Admin', date: '6 Jun 2026', score: 16, answers: [2,2,1,2,2,1,2,2,2,2] },
                    { name: 'Chiko Produksi', dept: 'Produksi', date: '7 Jun 2026', score: 22, answers: [3,3,2,3,3,2,2,2,2,2] },
                    { name: 'Dini CS', dept: 'CS', date: '5 Jun 2026', score: 15, answers: [2,1,1,2,1,2,1,2,2,1] },
                    { name: 'Eva Desainer', dept: 'Designer', date: '7 Jun 2026', score: 35, answers: [4,4,4,3,4,3,4,3,4,3] },
                    { name: 'Fahmi Produksi', dept: 'Produksi', date: '6 Jun 2026', score: 20, answers: [2,2,2,2,2,2,2,2,2,2] },
                    { name: 'Gita Admin', dept: 'Admin', date: '5 Jun 2026', score: 12, answers: [1,1,2,1,1,2,1,1,1,1] },
                    { name: 'Hendra Designer', dept: 'Designer', date: '7 Jun 2026', score: 26, answers: [3,3,3,2,3,2,3,2,3,2] },
                    { name: 'Irma CS', dept: 'CS', date: '4 Jun 2026', score: 24, answers: [2,3,2,3,2,2,3,2,3,2] },
                    { name: 'Joko Produksi', dept: 'Produksi', date: '7 Jun 2026', score: 38, answers: [4,4,4,4,4,3,4,4,3,4] },
                    { name: 'Kiki Admin', dept: 'Admin', date: '4 Jun 2026', score: 14, answers: [1,2,1,2,1,1,2,1,2,1] },
                    { name: 'Lani CS', dept: 'CS', date: '5 Jun 2026', score: 19, answers: [2,2,2,2,1,2,2,2,2,2] }
                ];
                localStorage.setItem('nvs_employees_data', JSON.stringify(this.employees));
            }

            // 2. Loading personal assessment history
            const historyKey = 'nvs_stress_history_' + this.currentUser.name;
            const storedHistory = localStorage.getItem(historyKey);
            if (storedHistory) {
                this.resultsHistory = JSON.parse(storedHistory);
            } else {
                // Initialize default empty history
                this.resultsHistory = [];
            }
            
            // Check if this specific user already submitted today/recently
            const latestKey = 'nvs_stress_latest_' + this.currentUser.name;
            const lastResult = localStorage.getItem(latestKey);
            if (lastResult) {
                this.latestResult = JSON.parse(lastResult);
                this.lastSubmitDate = this.latestResult.date;
                this.viewState = 'result';
            }
            
            // Initialize chart if manager
            if (this.currentUser.isManager) {
                this.$nextTick(() => {
                    this.initChart();
                });
            }
            
            // Re-render Lucide icons
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },
        
        submitAssessment() {
            // Validate all questions answered
            const unanswered = this.currentAnswers.findIndex(ans => ans === null);
            if (unanswered !== -1) {
                window.Swal.fire({
                    title: 'Form Belum Lengkap',
                    text: `Mohon jawab pertanyaan nomor ${unanswered + 1} terlebih dahulu.`,
                    icon: 'warning',
                    confirmButtonColor: '#1a237e'
                });
                return;
            }
            
            // Calculate score
            let totalScore = 0;
            this.currentAnswers.forEach(ans => {
                totalScore += parseInt(ans);
            });
            
            // Create result details
            const details = this.getCategoryDetails(totalScore);
            const dateStr = new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }) + ' WIB';
            
            this.latestResult = {
                score: totalScore,
                level: details.name,
                color: details.color,
                bgClass: details.bgClass,
                icon: details.icon,
                recommendations: details.recommendations,
                date: dateStr
            };
            
            // Save to personal history list in localStorage
            this.resultsHistory.unshift({
                date: dateStr,
                score: totalScore
            });
            const historyKey = 'nvs_stress_history_' + this.currentUser.name;
            localStorage.setItem(historyKey, JSON.stringify(this.resultsHistory));
            
            // Save latest personal result
            const latestKey = 'nvs_stress_latest_' + this.currentUser.name;
            localStorage.setItem(latestKey, JSON.stringify(this.latestResult));
            this.lastSubmitDate = dateStr;
            this.viewState = 'result';

            // Sync with Manager Overview Employee Data list!
            const dateShortStr = new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            // Find if this employee record already exists in the global list
            const empIndex = this.employees.findIndex(e => e.name === this.currentUser.name);
            const updatedRecord = {
                name: this.currentUser.name,
                dept: this.currentUser.role,
                date: dateShortStr,
                score: totalScore,
                answers: this.currentAnswers.map(Number)
            };

            if (empIndex !== -1) {
                this.employees[empIndex] = updatedRecord;
            } else {
                this.employees.unshift(updatedRecord);
            }
            localStorage.setItem('nvs_employees_data', JSON.stringify(this.employees));
            
            window.Swal.fire({
                title: 'Berhasil!',
                text: 'Assessment stress test Anda telah berhasil dikirim.',
                icon: 'success',
                confirmButtonColor: '#1a237e',
                timer: 2000
            });
            
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },
        
        resetForm() {
            this.currentAnswers = Array(10).fill(null);
            this.viewState = 'form';
            const latestKey = 'nvs_stress_latest_' + this.currentUser.name;
            localStorage.removeItem(latestKey);
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },
        
        // PDF Export & Direct Print
        exportPDF() {
            const data = this.filteredEmployees;
            const printWindow = window.open('', '_blank');
            let rowsHtml = '';
            
            data.forEach(emp => {
                const cat = this.getCategoryDetails(emp.score);
                rowsHtml += `
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px; font-weight: bold; font-size:12px;">${emp.name}</td>
                        <td style="padding: 10px; font-size:12px;">${emp.dept}</td>
                        <td style="padding: 10px; font-size:12px;">${emp.date}</td>
                        <td style="padding: 10px; font-size:12px; font-weight: bold;">${emp.score}/40</td>
                        <td style="padding: 10px; font-size:12px;"><span style="padding: 4px 8px; border-radius: 4px; font-weight: bold; background-color: ${cat.color === 'green' ? '#dcfce7' : (cat.color === 'yellow' ? '#fef9c3' : (cat.color === 'orange' ? '#ffedd5' : '#fee2e2'))}; color: ${cat.color === 'green' ? '#166534' : (cat.color === 'yellow' ? '#854d0e' : (cat.color === 'orange' ? '#9a3412' : '#991b1b'))};">${cat.name}</span></td>
                    </tr>
                `;
            });

            const summaryStats = `
                <div style="display: flex; gap: 15px; margin-bottom: 20px;">
                    <div style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center;">
                        <h3 style="margin: 0; font-size: 18px;">${data.length}</h3>
                        <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;">Total Karyawan</p>
                    </div>
                    <div style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid #22c55e;">
                        <h3 style="margin: 0; font-size: 18px; color: #22c55e;">${this.countStats('rendah')}</h3>
                        <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;">Rendah</p>
                    </div>
                    <div style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid #eab308;">
                        <h3 style="margin: 0; font-size: 18px; color: #eab308;">${this.countStats('sedang')}</h3>
                        <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;">Sedang</p>
                    </div>
                    <div style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid #f97316;">
                        <h3 style="margin: 0; font-size: 18px; color: #f97316;">${this.countStats('tinggi')}</h3>
                        <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;">Tinggi</p>
                    </div>
                    <div style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 8px; text-align: center; border-left: 4px solid #ef4444;">
                        <h3 style="margin: 0; font-size: 18px; color: #ef4444;">${this.countStats('kritis')}</h3>
                        <p style="margin: 3px 0 0 0; font-size: 11px; color: #666;">Kritis</p>
                    </div>
                </div>
            `;

            printWindow.document.write(`
                <html>
                <head>
                    <title>Laporan Stress Test Karyawan Novos</title>
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 25px; color: #333; }
                        h1 { color: #1a237e; margin-bottom: 5px; font-size: 20px; }
                        .meta { color: #777; font-size: 12px; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                        th { background-color: #1a237e; color: white; text-align: left; padding: 10px; font-size: 12px; }
                        @media print {
                            body { padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    <h1>Laporan Hasil Stress Test Karyawan</h1>
                    <div class="meta">Dicetak pada: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })} WIB</div>
                    
                    ${summaryStats}

                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Departemen</th>
                                <th>Tanggal</th>
                                <th>Skor</th>
                                <th>Tingkat</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() { window.close(); }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        },

        // CSV Export & Direct File Download
        exportCSV() {
            const data = this.filteredEmployees;
            let csvContent = "\uFEFF"; // Byte Order Mark for Excel UTF-8 compatibility
            csvContent += "Nama,Departemen,Tanggal,Skor,Tingkat Stres\n";
            
            data.forEach(emp => {
                const cat = this.getCategoryDetails(emp.score).name;
                csvContent += `"${emp.name}","${emp.dept}","${emp.date}",${emp.score},"${cat}"\n`;
            });
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.setAttribute("href", url);
            link.setAttribute("download", `laporan_stress_test_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            window.Swal.fire({
                title: 'Export Berhasil!',
                text: 'Berkas CSV sedang diunduh.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        },

        // Excel Export & Direct File Download
        exportExcel() {
            const data = this.filteredEmployees;
            let html = `
              <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
              <head>
                <!--[if gte mso 9]>
                <xml>
                  <` + `x:ExcelWorkbook>
                    <` + `x:ExcelWorksheets>
                      <` + `x:ExcelWorksheet>
                        <` + `x:Name>Stress Test Karyawan</` + `x:Name>
                        <` + `x:WorksheetOptions>
                          <` + `x:DisplayGridlines/>
                        </` + `x:WorksheetOptions>
                      </` + `x:ExcelWorksheet>
                    </` + `x:ExcelWorksheets>
                  </` + `x:ExcelWorkbook>
                </xml>
                <![endif]-->
                <meta charset="utf-8">
              </head>
              <body>
              <h2>Laporan Hasil Stress Test Karyawan</h2>
              <table border="1">
                <thead>
                  <tr style="background-color:#1a237e;color:#ffffff;font-weight:bold;">
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Tanggal</th>
                    <th>Skor</th>
                    <th>Tingkat</th>
                  </tr>
                </thead>
                <tbody>
            `;
            
            data.forEach(emp => {
                const cat = this.getCategoryDetails(emp.score).name;
                html += `
                  <tr>
                    <td>${emp.name}</td>
                    <td>${emp.dept}</td>
                    <td>${emp.date}</td>
                    <td>${emp.score}/40</td>
                    <td>${cat}</td>
                  </tr>
                `;
            });
            
            html += `
                </tbody>
              </table>
              </body>
              </html>
            `;
            
            const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement("a");
            link.href = url;
            link.download = `laporan_stress_test_${new Date().toISOString().slice(0,10)}.xls`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            window.Swal.fire({
                title: 'Export Berhasil!',
                text: 'Berkas Excel sedang diunduh.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        },
        
        showDetailEmployee(emp) {
            this.selectedEmployee = emp;
            this.showDetailModal = true;
            this.$nextTick(() => {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            });
        },
        
        // Filter and Search computed properties
        get filteredEmployees() {
            return this.employees.filter(emp => {
                // Search query filter
                const matchesSearch = emp.name.toLowerCase().includes(this.filters.search.toLowerCase());
                
                // Dept filter
                const matchesDept = this.filters.dept === '' || emp.dept === this.filters.dept;
                
                // Stress level filter
                const levelName = this.getCategoryDetails(emp.score).name.toLowerCase();
                const matchesLevel = this.filters.level === '' || levelName === this.filters.level.toLowerCase();
                
                return matchesSearch && matchesDept && matchesLevel;
            });
        },
        
        // Pagination logic
        get totalPages() {
            const count = Math.ceil(this.filteredEmployees.length / this.perPage);
            return count > 0 ? count : 1;
        },
        
        get paginatedEmployees() {
            // Bound page
            if (this.currentPage > this.totalPages) {
                this.currentPage = this.totalPages;
            }
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredEmployees.slice(start, end);
        },
        
        get pageStart() {
            if (this.filteredEmployees.length === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },
        
        get pageEnd() {
            const end = this.currentPage * this.perPage;
            return end > this.filteredEmployees.length ? this.filteredEmployees.length : end;
        },
        
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        // ChartJS initialization
        initChart() {
            const ctx = document.getElementById('trendChartCanvas');
            if (!ctx) return;
            
            // Destroy existing chart to avoid double render bugs
            if (this.chartInstance) {
                this.chartInstance.destroy();
            }
            
            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [{
                        label: 'Rata-rata Stress Karyawan',
                        data: [22, 23, 21, 25, 27, 28],
                        borderColor: '#1a237e',
                        backgroundColor: 'rgba(26, 35, 147, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#1a237e',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1a237e',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 13 },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Rata-rata Skor: ${context.parsed.y} / 40`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            min: 0,
                            max: 40,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: { size: 11, weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }
    };
}
</script>
@endsection
