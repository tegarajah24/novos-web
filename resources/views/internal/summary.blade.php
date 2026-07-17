@extends('layouts.internal')
@section('title', 'Summary')



@section('topbar-left')
    <h1 class="text-xl font-bold text-[#1a237e]">Summary</h1>
@endsection

@section('internal-content')

@php
$allKpi = array_merge($kpi1, $kpi2);
$topKpi = array_slice($allKpi, 0, 4);
$moreKpi = array_slice($allKpi, 4);
@endphp

<div class="mb-5" x-data="{ showMore: false }" x-init="$nextTick(() => { if (window.innerWidth >= 1024) showMore = true })">
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-6 mb-3">
        @foreach($topKpi as $k)
        <a href="{{ $k['url'] }}" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 transition-all duration-200 hover:shadow-xl hover:border-[#1a237e]/30 lg:hover:-translate-y-1 cursor-pointer group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-4.5 h-4.5 lg:w-5 lg:h-5 {{ $k['tc'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['icon'] }}"/></svg>
                </div>
                <span class="text-xs font-semibold flex items-center gap-0.5 {{ $k['up'] ? 'text-emerald-500' : 'text-red-500' }}">
                    @if($k['up'])<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>@else<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>@endif
                    {{ $k['c'] }}
                </span>
            </div>
            <div>
                <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $k['v'] }}</h3>
                <p class="text-xs lg:text-sm text-gray-500 mt-1 font-medium">{{ $k['l'] }}</p>
            </div>
        </a>
        @endforeach

        @foreach($moreKpi as $k)
        <a href="{{ $k['url'] }}" x-show="showMore" x-transition x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-200 p-3 lg:p-5 transition-all duration-200 hover:shadow-xl hover:border-[#1a237e]/30 lg:hover:-translate-y-1 cursor-pointer group flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3 lg:mb-4">
                <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl {{ $k['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-4.5 h-4.5 lg:w-5 lg:h-5 {{ $k['tc'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $k['icon'] }}"/></svg>
                </div>
                <span class="text-xs font-semibold flex items-center gap-0.5 {{ $k['up'] ? 'text-emerald-500' : 'text-red-500' }}">
                    @if($k['up'])<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>@else<svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>@endif
                    {{ $k['c'] }}
                </span>
            </div>
            <div>
                <h3 class="text-2xl lg:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $k['v'] }}</h3>
                <p class="text-xs lg:text-sm text-gray-500 mt-1 font-medium">{{ $k['l'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    @if(count($moreKpi))
    <div class="flex justify-center lg:hidden">
        <button @click="showMore = !showMore" class="inline-flex items-center gap-1 py-2 text-sm font-medium text-[#1a237e]/70 hover:text-[#1a237e] transition-colors">
            <span x-text="showMore ? 'Sembunyikan' : 'Lihat 4 Metrik Lainnya'">Lihat 4 Metrik Lainnya</span>
            <svg class="w-4 h-4 transition-transform duration-300" :class="showMore ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
    </div>
    @endif
</div>

{{-- ─── FILTER PANEL ──────────────────────────────────────────────────── --}}
<div x-data="summaryFilter()" class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
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
                <select x-model="form.assignee" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option value="">Semua</option>
                    @foreach($assignees as $a)
                    <option value="{{ $a['id'] }}">{{ $a['name'] }} ({{ $a['role'] }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Periode</label>
                <select x-model="form.period" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option value="month">Bulan Ini</option>
                    <option value="today">Hari Ini</option>
                    <option value="7days">7 Hari Terakhir</option>
                    <option value="30days">30 Hari Terakhir</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Prioritas</label>
                <select x-model="form.priority" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option value="">Semua</option>
                    <option value="normal">Normal</option>
                    <option value="express">Express</option>
                    <option value="super_express">Super Express</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Status</label>
                <select x-model="form.status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option value="">Semua</option>
                    <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                    <option value="tahap_desain">Tahap Desain</option>
                    <option value="tahap_produksi">Produksi</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Tipe</label>
                <select x-model="form.type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#1a237e]/30">
                    <option value="">Semua</option>
                    <option value="custom">Custom</option>
                    <option value="katalog">Produk Katalog</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button @click="reset()" class="px-4 py-2 text-sm border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">Reset</button>
            <button @click="apply()" class="px-5 py-2 text-sm bg-[#1a237e] text-white rounded-lg hover:bg-[#1a237e]/90 font-medium transition-colors">Terapkan Filter</button>
        </div>
    </div>
</div>

{{-- ─── CHARTS ROW 1 ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="trending-up" class="w-4 h-4 text-[#1a237e]"></i></span> Revenue Per Minggu</h3>
        <div class="overflow-x-auto lg:overflow-visible">
            <div class="h-40 min-w-[700px] lg:min-w-0"><canvas id="chartRevenue"></canvas></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="bar-chart-3" class="w-4 h-4 text-[#1a237e]"></i></span> Pesanan Masuk vs Selesai</h3>
        <div class="overflow-x-auto lg:overflow-visible">
            <div class="h-40 min-w-[700px] lg:min-w-0"><canvas id="chartOrders"></canvas></div>
        </div>
    </div>
</div>

{{-- ─── CHARTS ROW 2 ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="layers" class="w-4 h-4 text-[#1a237e]"></i></span> Top 5 Bahan Terlaris</h3>
        <div class="overflow-x-auto lg:overflow-visible">
            <div class="h-40 min-w-[500px] lg:min-w-0"><canvas id="chartTop5"></canvas></div>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="pie-chart" class="w-4 h-4 text-[#1a237e]"></i></span> Distribusi Jenis Pesanan</h3>
        @php $distColors = ['#1a237e', '#38bdf8']; @endphp
        <div class="flex flex-col lg:flex-row items-center lg:items-center gap-4 lg:gap-6">
            <div class="relative w-36 h-36 lg:w-48 lg:h-48 flex justify-center flex-shrink-0">
                <canvas id="chartDist"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl lg:text-2xl font-extrabold text-gray-900 leading-none">{{ array_sum($distData) }}</span>
                    <span class="text-[10px] lg:text-xs text-gray-400 font-medium mt-0.5">Total Pesanan</span>
                </div>
            </div>
            <div class="w-full lg:w-auto lg:flex-1 max-w-[250px] lg:pt-2 space-y-1.5">
                @foreach($distLabels as $i => $label)
                <div class="flex items-center justify-between py-0.5">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $distColors[$i] }}"></span>
                        <span class="text-xs font-medium text-gray-600 truncate">{{ $label }}</span>
                    </div>
                    <span class="text-xs font-bold text-gray-900 flex-shrink-0 ml-2">{{ $distData[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ─── TEAM PERFORMANCE TABLE ─────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="users" class="w-4 h-4 text-[#1a237e]"></i></span> Performance & Workload Tim</h3>
    </div>
    <div class="overflow-x-auto overflow-y-auto max-h-80 lg:overflow-visible lg:max-h-none">
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
            @forelse($employees as $e)
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
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="font-medium">Belum ada data tim</p>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ─── ACTIVITY ─────────────────────────────────────────────────────────── --}}
<div class="mb-5">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-1.5"><span class="w-7 h-7 rounded-lg bg-[#1a237e]/10 flex items-center justify-center shrink-0"><i data-lucide="bell" class="w-4 h-4 text-[#1a237e]"></i></span> Recent Activity</h3>
        <div class="space-y-4 overflow-x-auto overflow-y-auto max-h-80 lg:overflow-visible lg:max-h-none">
        @forelse($activities as $a)
        <div class="flex gap-3">
            <div class="mt-1 w-2.5 h-2.5 rounded-full {{ $a['color'] }} shrink-0"></div>
            <div>
                <p class="text-xs text-gray-400 mb-0.5">{{ $a['time'] }}</p>
                <p class="text-sm text-gray-700">{{ $a['text'] }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas terbaru.</p>
        @endforelse
        </div>
    </div>
</div>



{{-- ─── CHART.JS ────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function initSummaryCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(initSummaryCharts, 100);
        return;
    }

var weeks = @json($chartWeeks);
var chartDefaults = { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ labels:{ font:{ family:"'Poppins',sans-serif", size:11 }}}}, scales:{ x:{ grid:{display:false}, border:{display:false}}, y:{ grid:{color:'#f3f4f6'}, border:{display:false}}}};

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
        labels: @json($topMaterialLabels),
        datasets:[
            {
                label:'Terjual',
                data: @json($topMaterialData),
                backgroundColor:['#1a237e','#283593','#303f9f','#3949ab','#3f51b5'],
                borderRadius:6,
                barPercentage:0.6,
                categoryPercentage:0.85
            }
        ]
    },
    options:{
        ...chartDefaults,
        indexAxis:'y',
        layout:{padding:{right:10}},
        plugins:{...chartDefaults.plugins, legend:{display:false}},
        scales:{
            x:{display:false, beginAtZero:true},
            y:{display:false}
        },
        hover:{mode:'nearest',intersect:true}
    },
    plugins:[
        {
            id:'barLabels',
            afterDraw(chart){
                var ctx=chart.ctx;
                var meta=chart.getDatasetMeta(0);
                if (!meta || !meta.data) return;
                var area=chart.chartArea;
                meta.data.forEach(function(bar,index){
                    var value=chart.data.datasets[0].data[index];
                    var label=chart.data.labels[index];
                    var maxLen=20;
                    var displayLabel=label.length>maxLen?label.substring(0,maxLen)+'…':label;
                    var barW=bar.width;
                    var barX=bar.x;
                    var barY=bar.y;
                    ctx.save();
                    ctx.textBaseline='middle';
                    if(barW>120){
                        ctx.textAlign='left';
                        ctx.font="600 11px Poppins, sans-serif";
                        ctx.fillStyle='rgba(255,255,255,0.85)';
                        ctx.fillText(displayLabel,barX-barW+10,barY-1);
                        ctx.font="bold 12px Poppins, sans-serif";
                        ctx.fillStyle='#fff';
                        ctx.fillText(value,barX-barW+10,barY+13);
                    }else{
                        ctx.textAlign='left';
                        ctx.font="600 11px Poppins, sans-serif";
                        ctx.fillStyle='#374151';
                        ctx.fillText(displayLabel,barX+8,barY-1);
                        ctx.font="bold 12px Poppins, sans-serif";
                        ctx.fillStyle='#1a237e';
                        ctx.fillText(value,barX+8,barY+13);
                    }
                    ctx.restore();
                });
            }
        },
    ]
});

new Chart(document.getElementById('chartDist'), {
    type:'doughnut',
    data:{ labels:@json($distLabels), datasets:[{ data:@json($distData), backgroundColor:['#1a237e','#38bdf8'], borderWidth:3, borderColor:'#fff', hoverOffset:4 }]},
    options:{ responsive:true, maintainAspectRatio:false, cutout:'72%', plugins:{ legend:{display:false} }}
});

}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSummaryCharts);
} else {
    initSummaryCharts();
}

function summaryFilter() {
    return {
        open: {{ (request()->has('assignee') || request()->has('period') || request()->has('priority') || request()->has('status') || request()->has('type')) ? 'true' : 'false' }},
        form: {
            assignee: '{{ $filterAssignee ?? '' }}',
            period: '{{ $filterPeriod ?? 'month' }}',
            priority: '{{ $filterPriority ?? '' }}',
            status: '{{ $filterStatus ?? '' }}',
            type: '{{ request()->query('type', '') }}',
        },
        apply() {
            const params = new URLSearchParams();
            if (this.form.assignee) params.set('assignee', this.form.assignee);
            if (this.form.period && this.form.period !== 'month') params.set('period', this.form.period);
            if (this.form.priority) params.set('priority', this.form.priority);
            if (this.form.status) params.set('status', this.form.status);
            if (this.form.type) params.set('type', this.form.type);
            const qs = params.toString();
            window.location.href = qs ? '{{ route('staf.summary') }}?' + qs : '{{ route('staf.summary') }}';
        },
        reset() {
            window.location.href = '{{ route('staf.summary') }}';
        }
    }
}

if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons({ icons: window.lucide.icons });
</script>
@endsection
