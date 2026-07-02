<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class Summary extends Component
{
    public $kpi1 = [];
    public $kpi2 = [];
    public $employees = [];
    public $activities = [];
    public $chartWeeks = [];
    public $chartRevenue = [];
    public $chartOrdersIn = [];
    public $chartOrdersOut = [];
    public $topProductLabels = [];
    public $topProductData = [];
    public $distLabels = ['Custom', 'Produk Katalog'];
    public $distData = [50, 50];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $today = now()->startOfDay();
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $activeStatuses = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];

        $totalOrders = Order::whereIn('status', $activeStatuses)->count();
        $lastMonthOrders = Order::whereIn('status', $activeStatuses)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'success')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');

        $activeCustomers = Order::whereIn('status', $activeStatuses)
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')->count('user_id');

        $avgDays = Order::where('status', 'selesai')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
        $avgDaysFormatted = $avgDays ? number_format($avgDays, 1) . ' hari' : '0 hari';

        $this->kpi1 = [
            ['v' => (string)$totalOrders, 'l' => 'Total Pesanan', 'c' => $lastMonthOrders > 0 ? '+' . round(($totalOrders - $lastMonthOrders) / $lastMonthOrders * 100) . '%' : '0%', 'up' => $totalOrders >= $lastMonthOrders, 'bg' => 'bg-blue-50', 'tc' => 'text-blue-600', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'url' => route('staf.daftar-pesanan')],
            ['v' => $totalRevenue > 0 ? 'Rp ' . number_format($totalRevenue / 1000000, 1) . 'jt' : 'Rp 0', 'l' => 'Revenue', 'c' => $lastMonthRevenue > 0 ? '+' . round(($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100) . '%' : '0%', 'up' => $totalRevenue >= $lastMonthRevenue, 'bg' => 'bg-green-50', 'tc' => 'text-green-600', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'url' => route('staf.laporan', ['filter' => 'month'])],
            ['v' => (string)$activeCustomers, 'l' => 'Customer Aktif', 'c' => '+' . $activeCustomers, 'up' => true, 'bg' => 'bg-indigo-50', 'tc' => 'text-indigo-600', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'url' => route('staf.laporan')],
            ['v' => $avgDaysFormatted, 'l' => 'Avg Processing Time', 'c' => $avgDays ? number_format($avgDays, 1) . ' hari' : '-', 'up' => false, 'bg' => 'bg-orange-50', 'tc' => 'text-orange-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'url' => route('staf.laporan')],
        ];

        $pendingCount = Order::where('status', 'menunggu_validasi')->count();
        $designCount = Order::whereIn('status', ['dikonfirmasi', 'di_design'])->count();
        $completedMonth = Order::where('status', 'selesai')->whereMonth('updated_at', now()->month)->count();
        $totalSold = DB::table('order_items')->sum('qty');

        $this->kpi2 = [
            ['v' => (string)$pendingCount, 'l' => 'Menunggu Verifikasi', 'c' => '+' . $pendingCount, 'up' => $pendingCount > 0, 'bg' => 'bg-yellow-50', 'tc' => 'text-yellow-600', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'url' => route('staf.daftar-pesanan', ['status' => 'menunggu_verifikasi'])],
            ['v' => (string)$designCount, 'l' => 'Tahap Desain', 'c' => $designCount > 0 ? '+' . $designCount : '0', 'up' => true, 'bg' => 'bg-purple-50', 'tc' => 'text-purple-600', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', 'url' => route('staf.daftar-pesanan', ['status' => 'di_design'])],
            ['v' => (string)$completedMonth, 'l' => 'Selesai Bulan Ini', 'c' => '+' . $completedMonth, 'up' => true, 'bg' => 'bg-green-50', 'tc' => 'text-green-600', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'url' => route('staf.daftar-pesanan', ['status' => 'selesai', 'date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')])],
            ['v' => (string)$totalSold, 'l' => 'Produk Terjual', 'c' => '+' . $totalSold, 'up' => true, 'bg' => 'bg-teal-50', 'tc' => 'text-teal-600', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'url' => route('staf.laporan')],
        ];

        $this->employees = User::with('role')
            ->whereHas('role', fn($q) => $q->whereNot('name', 'Customer'))
            ->get()
            ->map(fn($user) => [
                'name' => $user->name,
                'role' => $user->role->name,
                'orders' => $user->orders()->count(),
                'avg' => ($c = $user->orders()->count()) > 0 ? round(24 / $c, 1) . ' hari' : '0 hari',
                'load' => min($user->orders()->count() * 10, 100),
            ])
            ->toArray();

        $this->activities = OrderStatusHistory::with(['order', 'changedBy'])
            ->latest()->take(10)->get()
            ->map(fn($h) => [
                'time' => $h->created_at->format('j M Y, H:i'),
                'color' => ['bg-green-500', 'bg-yellow-500', 'bg-blue-500', 'bg-purple-500', 'bg-red-500'][array_rand(['a', 'b', 'c', 'd', 'e'])],
                'text' => ($h->order?->order_number ?? 'Pesanan #' . $h->order_id) . " → {$h->status} oleh " . ($h->changedBy?->name ?? 'Sistem'),
            ])
            ->toArray();

        $this->chartWeeks = [];
        $this->chartRevenue = [];
        $this->chartOrdersIn = [];
        $this->chartOrdersOut = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $this->chartWeeks[] = 'W' . (8 - $i);
            $revenue = Payment::where('status', 'success')->whereBetween('created_at', [$weekStart, $weekEnd])->sum('amount');
            $this->chartRevenue[] = $revenue > 0 ? round($revenue / 1000000, 1) : 0;
            $this->chartOrdersIn[] = Order::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $this->chartOrdersOut[] = Order::where('status', 'selesai')->whereBetween('updated_at', [$weekStart, $weekEnd])->count();
        }

        $topProducts = DB::table('order_items')->select('size', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('size')->orderByDesc('total_qty')->take(5)->get();
        $this->topProductLabels = $topProducts->pluck('size')->map(fn($s) => 'Size ' . $s)->toArray();
        $this->topProductData = $topProducts->pluck('total_qty')->toArray();

        $customCount = Order::whereHas('designRequest')->count();
        $catalogCount = Order::whereDoesntHave('designRequest')->count();
        $totalBoth = max($customCount + $catalogCount, 1);
        $this->distData = [round($customCount / $totalBoth * 100), round($catalogCount / $totalBoth * 100)];
    }

    public function render()
    {
        return view('livewire.summary');
    }
}
