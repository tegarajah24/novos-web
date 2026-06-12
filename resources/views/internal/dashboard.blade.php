@extends('layouts.internal')

@section('title', 'Dashboard')

@section('topbar-left')
    <h1 class="text-xl font-bold text-black">Dashboard</h1>
@endsection

@section('internal-content')
    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card 1: Total Pesanan -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-[#1a237e]/30 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#1a237e] flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Clipboard List icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                    <div class="flex items-center gap-1 text-xs font-semibold {{ $totalTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($totalTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $totalTrend >= 0 ? '+'.$totalTrend : $totalTrend }}</span>
                </div>
                <h3 class="text-4xl font-bold text-gray-900 tracking-tight">127</h3>
                <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $totalOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Total Pesanan</p>
        </div>

        <!-- Card 2: Menunggu Verifikasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-orange-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Clock icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $pendingTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($pendingTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $pendingTrend >= 0 ? '+'.$pendingTrend : $pendingTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $pendingOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Menunggu Verifikasi</p>
        </div>

        <!-- Card 3: Sedang Diproses -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-blue-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Cog / Settings icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $processTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($processTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $processTrend >= 0 ? '+'.$processTrend : $processTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $inProcessOrders }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Sedang Diproses</p>
        </div>

        <!-- Card 4: Selesai Hari Ini -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-xl hover:border-green-300/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    {{-- Check Circle icon (Heroicons) --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-1 text-xs font-semibold {{ $completedTrend >= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-red-500 bg-red-50' }} px-2 py-1 rounded-full">
                    @if($completedTrend >= 0)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                    @endif
                    <span>{{ $completedTrend >= 0 ? '+'.$completedTrend : $completedTrend }}</span>
                </div>
            </div>
            <h3 class="text-4xl font-bold text-gray-900 tracking-tight">{{ $completedToday }}</h3>
            <p class="text-gray-500 text-sm mt-2 font-medium">Selesai Hari Ini</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Line Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Pesanan Per Minggu</h3>
            <div class="h-64">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <!-- Donut Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
            <h3 class="font-bold text-gray-900 mb-6 text-lg">Status Pesanan Saat Ini</h3>
            <div class="h-64 flex justify-center">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Table Row -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-white">
            <h3 class="font-bold text-gray-900 text-lg">Pesanan Terbaru</h3>
            <a href="{{ url('staf/daftar-pesanan') }}" class="text-sm font-semibold text-[#1a237e] hover:underline flex items-center gap-1">
                Lihat Semua <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Order ID</th>
                        <th class="px-6 py-4 font-semibold">Customer</th>
                        <th class="px-6 py-4 font-semibold">Produk</th>
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($recentOrders as $order)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4">{{ $order->user->name }}</td>
                        <td class="px-6 py-4">{{ $order->designRequest?->team_name ?? 'Pesanan #'.$order->id }}</td>
                        <td class="px-6 py-4">{{ $order->created_at->format('j M Y') }}</td>
                        <td class="px-6 py-4"><x-badge type="{{ $order->status }}">{{ $order->status }}</x-badge></td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ url('staf/detail-pesanan/'.$order->id) }}" class="text-gray-400 hover:text-[#1a237e] inline-block">
                                <i data-lucide="eye" class="w-5 h-5"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">Belum ada pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Line Chart (Pesanan Per Minggu)
            const ctxLine = document.getElementById('lineChart');
            if (ctxLine) {
                new Chart(ctxLine.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($weeklyLabels),
                        datasets: [{
                            label: 'Pesanan',
                            data: @json($weeklyData),
                            borderColor: '#1a237e',
                            backgroundColor: 'rgba(26, 35, 126, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#1a237e',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1a237e',
                                padding: 12,
                                titleFont: { size: 13, family: "'Poppins', sans-serif" },
                                bodyFont: { size: 13, family: "'Poppins', sans-serif" },
                                displayColors: false,
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: '#f3f4f6', borderDash: [4, 4] },
                                border: { display: false }
                            },
                            x: { 
                                grid: { display: false },
                                border: { display: false }
                            }
                        }
                    }
                });
            }

            // Donut Chart (Status Pesanan Saat Ini)
            const ctxDonut = document.getElementById('donutChart');
            if (ctxDonut) {
                new Chart(ctxDonut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: [
                                '#eab308', // yellow-500
                                '#3b82f6', // blue-500
                                '#f97316', // orange-500
                                '#a855f7', // purple-500
                                '#22c55e'  // green-500
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    padding: 20, 
                                    usePointStyle: true, 
                                    pointStyle: 'circle',
                                    font: { family: "'Poppins', sans-serif", size: 12 }
                                }
                            },
                            tooltip: {
                                padding: 12,
                                titleFont: { size: 13, family: "'Poppins', sans-serif" },
                                bodyFont: { size: 13, family: "'Poppins', sans-serif" },
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
