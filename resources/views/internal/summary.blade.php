@extends('layouts.internal')
@section('title', 'Summary')

@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Summary</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
@endsection

@section('internal-content')
@php
$kpi1 = [
    ['v'=>'127','l'=>'Total Pesanan','c'=>'+12%','up'=>true,'bg'=>'bg-blue-50','tc'=>'text-blue-600','icon'=>'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
    ['v'=>'Rp 234,5jt','l'=>'Revenue','c'=>'+7%','up'=>true,'bg'=>'bg-green-50','tc'=>'text-green-600','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['v'=>'87','l'=>'Customer Aktif','c'=>'+5%','up'=>true,'bg'=>'bg-indigo-50','tc'=>'text-indigo-600','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ['v'=>'5.2 hari','l'=>'Avg Processing Time','c'=>'-0.3 hari','up'=>false,'bg'=>'bg-orange-50','tc'=>'text-orange-500','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
];
$kpi2 = [
    ['v'=>'8','l'=>'Menunggu Verifikasi','c'=>'+3','up'=>true,'bg'=>'bg-yellow-50','tc'=>'text-yellow-600','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['v'=>'23','l'=>'Tahap Desain','c'=>'-2','up'=>false,'bg'=>'bg-purple-50','tc'=>'text-purple-600','icon'=>'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z'],
    ['v'=>'15','l'=>'Selesai Bulan Ini','c'=>'+8','up'=>true,'bg'=>'bg-green-50','tc'=>'text-green-600','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['v'=>'42','l'=>'Produk Terjual','c'=>'+15%','up'=>true,'bg'=>'bg-teal-50','tc'=>'text-teal-600','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
];
$employees = [
    ['name'=>'Andi Desainer','role'=>'Designer','orders'=>42,'avg'=>'2.2 hari','load'=>65],
    ['name'=>'Budi Admin','role'=>'Admin','orders'=>127,'avg'=>'0.5 hari','load'=>30],
    ['name'=>'Chiko Produksi','role'=>'Produksi','orders'=>89,'avg'=>'4.1 hari','load'=>85],
    ['name'=>'Dini CS','role'=>'CS','orders'=>230,'avg'=>'0.2 hari','load'=>15],
    ['name'=>'Eva Desainer','role'=>'Designer','orders'=>38,'avg'=>'2.5 hari','load'=>60],
];
$activities = [
    ['time'=>'Hari ini, 10:30','color'=>'bg-green-500','text'=>'Pesanan baru dari Budi S. (NVS-001)'],
    ['time'=>'Kemarin, 14:15','color'=>'bg-yellow-500','text'=>'Status NVS-002 diupdate ke "Tahap Desain"'],
    ['time'=>'2 Jun 2026','color'=>'bg-blue-500','text'=>'Desain untuk NVS-003 telah diupload oleh Andi'],
    ['time'=>'1 Jun 2026','color'=>'bg-purple-500','text'=>'Pembayaran NVS-004 terverifikasi oleh Budi'],
];
@endphp

{{-- ─── KPI BARIS 1 ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
@foreach($kpi1 as $k)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
    <div class="flex justify-between items-start mb-3">
        <div class="w-10 h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center">
            <svg class="w-5 h-5 {{ $k['tc'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['icon'] }}"/></svg>
        </div>
        <span class="text-xs font-semibold flex items-center gap-0.5 {{ $k['up'] ? 'text-emerald-500' : 'text-red-500' }}">
            @if($k['up'])<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>@else<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>@endif
            {{ $k['c'] }}
        </span>
    </div>
    <div class="text-2xl font-bold text-gray-900">{{ $k['v'] }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ $k['l'] }}</div>
</div>
@endforeach
</div>

{{-- ─── KPI BARIS 2 ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
@foreach($kpi2 as $k)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
    <div class="flex justify-between items-start mb-3">
        <div class="w-10 h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center">
            <svg class="w-5 h-5 {{ $k['tc'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['icon'] }}"/></svg>
        </div>
        <span class="text-xs font-semibold flex items-center gap-0.5 {{ $k['up'] ? 'text-emerald-500' : 'text-red-500' }}">
            @if($k['up'])<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>@else<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>@endif
            {{ $k['c'] }}
        </span>
    </div>
    <div class="text-2xl font-bold text-gray-900">{{ $k['v'] }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ $k['l'] }}</div>
</div>
@endforeach
</div>

{{-- ─── FILTER PANEL ──────────────────────────────────────────────────── --}}
<div x-data="{ open: false }" class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
    <button @click="open=!open" class="w-full flex items-center justify-between px-6 py-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 rounded-xl transition-colors">
        <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-[#1a237e]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filter Laporan
        </span>
        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180':''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-cloak x-transition class="px-6 pb-5 border-t border-gray-100">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mt-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Assignee</label>
                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option>Semua</option>
                    <option>Andi Desainer</option><option>Budi Admin</option><option>Chiko Produksi</option><option>Dini CS</option><option>Eva Desainer</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Periode</label>
                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option>Bulan Ini</option><option>Hari Ini</option><option>7 Hari Terakhir</option><option>30 Hari Terakhir</option><option>Custom</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Prioritas</label>
                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option>Semua</option><option>Normal</option><option>Express</option><option>Super Express</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option>Semua</option><option>Menunggu Verifikasi</option><option>Tahap Desain</option><option>Menunggu ACC</option><option>Produksi</option><option>Selesai</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Tipe</label>
                <select class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option>Semua</option><option>Custom</option><option>Produk Katalog</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">Reset</button>
            <button class="px-5 py-2 text-sm bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 font-medium transition-colors">Terapkan Filter</button>
        </div>
    </div>
</div>

{{-- ─── CHARTS ROW 1 ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">📈 Revenue Per Minggu</h3>
        <div class="h-56"><canvas id="chartRevenue"></canvas></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">📊 Pesanan Masuk vs Selesai</h3>
        <div class="h-56"><canvas id="chartOrders"></canvas></div>
    </div>
</div>

{{-- ─── CHARTS ROW 2 ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">🏆 Top 5 Produk Terlaris</h3>
        <div class="h-56"><canvas id="chartTop5"></canvas></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">🥧 Distribusi Jenis Pesanan</h3>
        <div class="h-56 flex justify-center"><canvas id="chartDist"></canvas></div>
    </div>
</div>

{{-- ─── TEAM PERFORMANCE TABLE ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">👥 Performance & Workload Tim</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">Nama</th>
                    <th class="px-6 py-3 text-left font-semibold">Role</th>
                    <th class="px-6 py-3 text-left font-semibold">Pesanan Diproses</th>
                    <th class="px-6 py-3 text-left font-semibold">Avg Time</th>
                    <th class="px-6 py-3 text-left font-semibold">Beban Kerja</th>
                    <th class="px-6 py-3 text-left font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @foreach($employees as $e)
            @php
                $loadColor = $e['load'] >= 75 ? 'bg-red-500' : ($e['load'] >= 50 ? 'bg-yellow-400' : 'bg-emerald-500');
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $e['name'] }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $e['role'] }}</td>
                <td class="px-6 py-4 text-gray-700">{{ $e['orders'] }}</td>
                <td class="px-6 py-4 text-gray-700">{{ $e['avg'] }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 max-w-24">
                            <div class="{{ $loadColor }} h-2 rounded-full" style="width:{{ $e['load'] }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-600">{{ $e['load'] }}%</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>Aktif
                    </span>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ─── ACTIVITY + PRIORITY ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    {{-- Recent Activity --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">🔔 Recent Activity</h3>
        <div class="space-y-4">
        @foreach($activities as $a)
        <div class="flex gap-3">
            <div class="mt-1 w-2.5 h-2.5 rounded-full {{ $a['color'] }} shrink-0"></div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ $a['time'] }}</p>
                <p class="text-sm text-gray-700">{{ $a['text'] }}</p>
            </div>
        </div>
        @endforeach
        </div>
    </div>

    {{-- Priority Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4">⚡ Priority Breakdown</h3>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1.5"><span class="font-medium text-gray-700">Normal</span><span class="text-gray-500 font-semibold">78%</span></div>
                <div class="bg-gray-100 rounded-full h-2.5"><div class="bg-emerald-500 h-2.5 rounded-full" style="width:78%"></div></div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1.5"><span class="font-medium text-gray-700">Express</span><span class="text-gray-500 font-semibold">15%</span></div>
                <div class="bg-gray-100 rounded-full h-2.5"><div class="bg-yellow-400 h-2.5 rounded-full" style="width:15%"></div></div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1.5"><span class="font-medium text-gray-700">Super Express</span><span class="text-gray-500 font-semibold">7%</span></div>
                <div class="bg-gray-100 rounded-full h-2.5"><div class="bg-red-500 h-2.5 rounded-full" style="width:7%"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- ─── EXPORT ──────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-wrap gap-3 items-center justify-between">
    <span class="font-semibold text-gray-700 text-sm">📎 Export Laporan</span>
    <div class="flex gap-3">
        <button onclick="alert('Fitur sedang dalam pengembangan')" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors font-medium">
            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Download PDF
        </button>
        <button onclick="alert('Fitur sedang dalam pengembangan')" class="flex items-center gap-2 px-4 py-2 bg-[#1a237e] text-white rounded-lg text-sm hover:bg-[#1a237e]/90 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export Excel
        </button>
    </div>
</div>

{{-- ─── CHART.JS ────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const weeks = ['W1','W2','W3','W4','W5','W6','W7','W8'];
const chartDefaults = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ font:{ family:"'Poppins',sans-serif", size:11 }}}}, scales:{ x:{ grid:{display:false}, border:{display:false}}, y:{ grid:{color:'#f3f4f6'}, border:{display:false}}}};

new Chart(document.getElementById('chartRevenue'), {
    type:'line',
    data:{ labels:weeks, datasets:[{ label:'Revenue (jt)', data:[45,52,48,62,58,71,68,85], borderColor:'#1a237e', backgroundColor:'rgba(26,35,126,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#1a237e', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 }]},
    options:{...chartDefaults, plugins:{...chartDefaults.plugins, legend:{display:false}}}
});

new Chart(document.getElementById('chartOrders'), {
    type:'line',
    data:{ labels:weeks, datasets:[
        { label:'Masuk', data:[12,15,18,22,20,25,28,32], borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#3b82f6', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 },
        { label:'Selesai', data:[8,10,14,18,19,22,26,30], borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#22c55e', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 }
    ]},
    options:chartDefaults
});

new Chart(document.getElementById('chartTop5'), {
    type:'bar',
    data:{
        labels:['Jersey Futsal','Jersey Basket','Jersey Sepak Bola','Jersey Voli','Jersey Running'],
        datasets:[{ label:'Terjual', data:[42,38,35,28,25], backgroundColor:['#1a237e','#283593','#303f9f','#3949ab','#3f51b5'], borderRadius:6 }]
    },
    options:{...chartDefaults, indexAxis:'y', plugins:{...chartDefaults.plugins, legend:{display:false}}}
});

new Chart(document.getElementById('chartDist'), {
    type:'doughnut',
    data:{ labels:['Custom','Produk Katalog'], datasets:[{ data:[65,35], backgroundColor:['#1a237e','#38bdf8'], borderWidth:3, borderColor:'#fff', hoverOffset:4 }]},
    options:{ responsive:true, maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{ position:'bottom', labels:{ padding:20, usePointStyle:true, pointStyle:'circle', font:{ family:"'Poppins',sans-serif", size:12 }}}}}
});
</script>
@endsection
