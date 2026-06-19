@extends('layouts.internal')
@section('title', 'Summary')

@section('topbar-left')
    <h1 class="text-xl font-bold text-black">Summary</h1>
@endsection

@section('internal-content')

{{-- ─── KPI BARIS 1 ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-5">
@foreach($kpi1 as $k)
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
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
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-1">
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

{{-- ─── ACTIVITY ─────────────────────────────────────────────────────────── --}}
<div class="mb-5">
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
</div>



{{-- ─── CHART.JS ────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const weeks = @json($chartWeeks);
const chartDefaults = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ font:{ family:"'Poppins',sans-serif", size:11 }}}}, scales:{ x:{ grid:{display:false}, border:{display:false}}, y:{ grid:{color:'#f3f4f6'}, border:{display:false}}}};

new Chart(document.getElementById('chartRevenue'), {
    type:'line',
    data:{ labels:weeks, datasets:[{ label:'Revenue (jt)', data:@json($chartRevenue), borderColor:'#1a237e', backgroundColor:'rgba(26,35,126,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#1a237e', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 }]},
    options:{...chartDefaults, plugins:{...chartDefaults.plugins, legend:{display:false}}}
});

new Chart(document.getElementById('chartOrders'), {
    type:'line',
    data:{ labels:weeks, datasets:[
        { label:'Masuk', data:@json($chartOrdersIn), borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#3b82f6', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 },
        { label:'Selesai', data:@json($chartOrdersOut), borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.07)', borderWidth:2, tension:0.4, fill:true, pointBackgroundColor:'#22c55e', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4 }
    ]},
    options:chartDefaults
});

new Chart(document.getElementById('chartTop5'), {
    type:'bar',
    data:{
        labels: @json($topProductLabels),
        datasets:[
            {
                label:'Terjual',
                data: @json($topProductData),
                backgroundColor:['#1a237e','#283593','#303f9f','#3949ab','#3f51b5'],
                borderRadius:6,
                barPercentage:0.7,
                categoryPercentage:0.85
            }
        ]
    },
    options:{
        ...chartDefaults,
        indexAxis:'y',
        plugins:{...chartDefaults.plugins, legend:{display:false}},
        animation:{
            x:{from:0,duration:1200,easing:'easeOutQuart',delay:(ctx)=>ctx.dataIndex*150}
        },
        scales:{
            x:{beginAtZero:true,grid:{color:'rgba(0,0,0,0.04)'},border:{display:false},ticks:{font:{size:11}}},
            y:{grid:{display:false},border:{display:false},ticks:{font:{size:12,family:"'Poppins',sans-serif"}}}
        },
        hover:{mode:'nearest',intersect:true}
    },
    plugins:[
        {
            id:'rankingBadges',
            afterDatasetsDraw(chart){
                const ctx=chart.ctx;
                const meta=chart.getDatasetMeta(0);
                const badges=['🥇','🥈','🥉'];
                meta.data.forEach((bar,index)=>{
                    if(index<3){
                        ctx.save();
                        ctx.font='16px serif';
                        ctx.textAlign='right';
                        ctx.textBaseline='middle';
                        ctx.fillText(badges[index],bar.base-12,bar.y);
                        ctx.restore();
                    }
                });
            }
        },
        {
            id:'valueLabels',
            afterDatasetsDraw(chart){
                const ctx=chart.ctx;
                const meta=chart.getDatasetMeta(0);
                meta.data.forEach((bar,index)=>{
                    const value=chart.data.datasets[0].data[index];
                    ctx.save();
                    ctx.fillStyle='#1a237e';
                    ctx.font="bold 11px Poppins, sans-serif";
                    ctx.textAlign='left';
                    ctx.textBaseline='middle';
                    ctx.fillText(value+' terjual',bar.x+10,bar.y);
                    ctx.restore();
                });
            }
        },
        {
            id:'hoverEnlarge',
            beforeDatasetsDraw(chart){
                const active=chart.getActiveElements();
                if(!active.length) return;
                const ctx=chart.ctx;
                active.forEach(el=>{
                    const {x,base,y,width}=el.element;
                    const thickness=width*1.4;
                    const newY=y-thickness/2;
                    ctx.save();
                    ctx.fillStyle='rgba(26,35,126,0.12)';
                    ctx.fillRect(base,newY,x-base,thickness);
                    ctx.restore();
                });
            }
        }
    ]
});

new Chart(document.getElementById('chartDist'), {
    type:'doughnut',
    data:{ labels:@json($distLabels), datasets:[{ data:@json($distData), backgroundColor:['#1a237e','#38bdf8'], borderWidth:3, borderColor:'#fff', hoverOffset:4 }]},
    options:{ responsive:true, maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{ position:'bottom', labels:{ padding:20, usePointStyle:true, pointStyle:'circle', font:{ family:"'Poppins',sans-serif", size:12 }}}}}
});
</script>
@endsection
