<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])->count();
        $totalLastWeek = Order::whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();
        $totalTrend = $totalOrders - $totalLastWeek;

        $user = auth()->user();
        $isDesign     = $user->isDesign();
        $isProduction = $user->isProduction();

        $pendingOrders = Order::where('status', 'menunggu_pembayaran')->count();
        $pendingLastWeek = Order::where('status', 'menunggu_pembayaran')
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();
        $pendingTrend = $pendingOrders - $pendingLastWeek;

        $inProcessOrders = Order::whereIn('status', ['di_design', 'siap_cetak', 'menunggu_spk', 'diproduksi'])->count();
        $processLastWeek = Order::whereIn('status', ['di_design', 'siap_cetak', 'menunggu_spk', 'diproduksi'])
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();
        $processTrend = $inProcessOrders - $processLastWeek;

        $completedToday = Order::where('status', 'selesai')
            ->whereDate('updated_at', today())
            ->count();
        $completedYesterday = Order::where('status', 'selesai')
            ->whereDate('updated_at', today()->subDay())
            ->count();
        $completedTrend = $completedToday - $completedYesterday;

        $recentOrders = Order::with(['user', 'designRequest'])
            ->whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->latest()
            ->take(5)
            ->get();

        $pending = Order::where('status', 'menunggu_pembayaran')->count();
        $design = Order::whereIn('status', ['dikonfirmasi', 'disetujui', 'di_design'])->count();
        $acc = Order::where('status', 'disetujui')->count();
        $produksi = Order::whereIn('status', ['siap_cetak', 'menunggu_spk', 'diproduksi'])->count();
        $selesai = Order::where('status', 'selesai')->count();

        $statusLabels = ['Menunggu Pembayaran', 'Desain', 'Menunggu ACC', 'Produksi', 'Selesai'];
        $statusData = [$pending, $design, $acc, $produksi, $selesai];

        // Data khusus Design
        $designWaiting     = Order::where('status', 'dikonfirmasi')->count();
        $designInProgress  = Order::where('status', 'di_design')->count();
        $designWaitingAcc  = Order::where('status', 'disetujui')->count();

        // Data khusus Produksi
        $printQueue  = Order::whereIn('status', ['siap_cetak', 'menunggu_spk'])->count();
        $sewingQueue = Order::where('status', 'diproduksi')->count();

        // Preload all chart data for instant and responsive UI
        $allChartData = [
            'day' => $this->getChartDataForFilter('day'),
            'week' => $this->getChartDataForFilter('week'),
            'month' => $this->getChartDataForFilter('month'),
            'year' => $this->getChartDataForFilter('year'),
        ];

        return view('internal.dashboard', compact(
            'totalOrders', 'totalTrend',
            'pendingOrders', 'pendingTrend',
            'inProcessOrders', 'processTrend',
            'completedToday', 'completedTrend',
            'recentOrders',
            'statusLabels', 'statusData',
            'isDesign', 'isProduction',
            'designWaiting', 'designInProgress', 'designWaitingAcc',
            'printQueue', 'sewingQueue',
            'allChartData',
        ));
    }

    public function summary(Request $request)
    {
        $filterAssignee = $request->query('assignee');
        $filterPeriod = $request->query('period', 'month');
        $filterPriority = $request->query('priority');
        $filterStatus = $request->query('status');

        $today = now()->startOfDay();
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        $totalOrders = Order::whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])->count();
        $lastMonthOrders = Order::whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();

        $totalRevenue = Payment::where('status', 'success')->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'success')
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('amount');

        $activeCustomers = Order::whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')->count('user_id');

        $avgDays = Order::where('status', 'selesai')
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
        $avgDaysFormatted = $avgDays ? number_format($avgDays, 1) . ' hari' : '0 hari';

        $kpi1 = [
            [
                'v' => (string) $totalOrders,
                'l' => 'Total Pesanan',
                'c' => $lastMonthOrders > 0 ? '+' . round(($totalOrders - $lastMonthOrders) / $lastMonthOrders * 100) . '%' : '0%',
                'up' => $totalOrders >= $lastMonthOrders,
                'bg' => 'bg-blue-50', 'tc' => 'text-blue-600',
                'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
                'url' => route('staf.daftar-pesanan'),
            ],
            [
                'v' => $totalRevenue > 0 ? 'Rp ' . number_format($totalRevenue / 1000000, 1) . 'jt' : 'Rp 0',
                'l' => 'Revenue',
                'c' => $lastMonthRevenue > 0 ? '+' . round(($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100) . '%' : '0%',
                'up' => $totalRevenue >= $lastMonthRevenue,
                'bg' => 'bg-green-50', 'tc' => 'text-green-600',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'url' => route('staf.laporan', ['filter' => 'month']),
            ],
            [
                'v' => (string) $activeCustomers,
                'l' => 'Customer Aktif',
                'c' => '+' . $activeCustomers,
                'up' => true,
                'bg' => 'bg-indigo-50', 'tc' => 'text-indigo-600',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'url' => route('staf.laporan'),
            ],
            [
                'v' => $avgDaysFormatted,
                'l' => 'Avg Processing Time',
                'c' => $avgDays ? number_format($avgDays, 1) . ' hari' : '-',
                'up' => false,
                'bg' => 'bg-orange-50', 'tc' => 'text-orange-500',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'url' => route('staf.laporan'),
            ],
        ];

        $pendingCount = Order::where('status', 'menunggu_pembayaran')->count();
        $designCount = Order::whereIn('status', ['dikonfirmasi', 'di_design'])->count();
        $completedMonth = Order::where('status', 'selesai')
            ->whereMonth('updated_at', now()->month)->count();
        $totalSold = DB::table('order_items')->sum('qty');

        $kpi2 = [
            [
                'v' => (string) $pendingCount,
                'l' => 'Menunggu Pembayaran',
                'c' => '+' . $pendingCount,
                'up' => $pendingCount > 0,
                'bg' => 'bg-yellow-50', 'tc' => 'text-yellow-600',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'url' => route('staf.daftar-pesanan', ['status' => 'menunggu_pembayaran']),
            ],
            [
                'v' => (string) $designCount,
                'l' => 'Tahap Desain',
                'c' => $designCount > 0 ? '+' . $designCount : '0',
                'up' => true,
                'bg' => 'bg-purple-50', 'tc' => 'text-purple-600',
                'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
                'url' => route('staf.daftar-pesanan', ['status' => 'di_design']),
            ],
            [
                'v' => (string) $completedMonth,
                'l' => 'Selesai Bulan Ini',
                'c' => '+' . $completedMonth,
                'up' => true,
                'bg' => 'bg-green-50', 'tc' => 'text-green-600',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'url' => route('staf.daftar-pesanan', ['status' => 'selesai', 'date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->endOfMonth()->format('Y-m-d')]),
            ],
            [
                'v' => (string) $totalSold,
                'l' => 'Produk Terjual',
                'c' => $totalSold > 0 ? '+' . $totalSold : '0',
                'up' => true,
                'bg' => 'bg-teal-50', 'tc' => 'text-teal-600',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'url' => route('staf.laporan'),
            ],
        ];

        $employees = User::with('role')
            ->withCount('orders')
            ->whereHas('role', fn($q) => $q->whereNot('name', 'Customer'))
            ->get()
            ->map(function ($user) {
                $orderCount = $user->orders_count;
                return [
                    'name' => $user->name,
                    'role' => $user->role->name,
                    'orders' => $orderCount,
                    'avg' => $orderCount > 0 ? round(24 / $orderCount, 1) . ' hari' : '0 hari',
                    'load' => min($orderCount * 10, 100),
                ];
            })
            ->toArray();

        $activities = OrderStatusHistory::with(['order', 'changedBy'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($history) {
                $time = $history->created_at->diffForHumans();
                $userName = $history->changedBy?->name ?? 'Sistem';
                $colors = ['bg-green-500', 'bg-yellow-500', 'bg-blue-500', 'bg-purple-500', 'bg-red-500'];
                return [
                    'time' => $history->created_at->format('j M Y, H:i'),
                    'color' => $colors[array_rand($colors)],
                    'text' => ($history->order?->order_number ?? 'Pesanan #' . $history->order_id) . " → {$history->status} oleh {$userName}",
                ];
            })
            ->toArray();

        $chartWeeks = [];
        $chartRevenue = [];
        $chartOrdersIn = [];
        $chartOrdersOut = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $chartWeeks[] = 'W' . (8 - $i);
            $revenue = Payment::where('status', 'success')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('amount');
            $chartRevenue[] = $revenue > 0 ? round($revenue / 1000000, 1) : 0;
            $chartOrdersIn[] = Order::whereBetween('created_at', [$weekStart, $weekEnd])->count();
            $chartOrdersOut[] = Order::where('status', 'selesai')
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->count();
        }

        $topMaterials = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('design_requests', 'orders.id', '=', 'design_requests.order_id')
            ->select('design_requests.material', DB::raw('SUM(order_items.qty) as total_qty'))
            ->whereNotNull('design_requests.material')
            ->groupBy('design_requests.material')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
        $topMaterialLabels = $topMaterials->pluck('material')->toArray();
        $topMaterialData = $topMaterials->pluck('total_qty')->toArray();

        $customCount = Order::whereHas('designRequest')->count();
        $catalogCount = Order::whereDoesntHave('designRequest')->count();
        $totalBoth = max($customCount + $catalogCount, 1);
        $distLabels = ['Custom', 'Produk Katalog'];
        $distData = [round($customCount / $totalBoth * 100), round($catalogCount / $totalBoth * 100)];

        $assignees = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'Admin'))
            ->orderBy('name')
            ->get()
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'role' => $u->role->name])
            ->toArray();

        return view('internal.summary', compact(
            'kpi1', 'kpi2', 'employees', 'activities',
            'chartWeeks', 'chartRevenue', 'chartOrdersIn', 'chartOrdersOut',
            'topMaterialLabels', 'topMaterialData',
            'distLabels', 'distData',
            'assignees', 'filterAssignee', 'filterPeriod', 'filterPriority', 'filterStatus',
        ));
    }

    public function chartData(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'day');
        return response()->json($this->getChartDataForFilter($filter));
    }

    private function getChartDataForFilter(string $filter): array
    {
        $labels = [];
        $data = [];

        switch ($filter) {
            case 'day':
                $start = now()->subDays(13)->startOfDay();
                $results = Order::where('created_at', '>=', $start)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupByRaw('DATE(created_at)')
                    ->pluck('count', 'date');

                for ($i = 13; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $key = $date->format('Y-m-d');
                    $labels[] = $date->format('j/n');
                    $data[] = (int) ($results[$key] ?? 0);
                }
                break;

            case 'week':
                $start = now()->subWeeks(7)->startOfWeek();
                $results = Order::where('created_at', '>=', $start)
                    ->selectRaw('YEARWEEK(created_at, 1) as week_num, COUNT(*) as count')
                    ->groupByRaw('YEARWEEK(created_at, 1)')
                    ->pluck('count', 'week_num');

                for ($i = 7; $i >= 0; $i--) {
                    $weekStart = now()->subWeeks($i)->startOfWeek();
                    $weekKey = (int) $weekStart->format('oW');
                    $labels[] = 'W' . (8 - $i);
                    $data[] = (int) ($results[$weekKey] ?? 0);
                }
                break;

            case 'month':
                $start = now()->subMonths(11)->startOfMonth();
                $results = Order::where('created_at', '>=', $start)
                    ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                    ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
                    ->pluck('count', 'month');

                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $key = $date->format('Y-m');
                    $labels[] = $date->format('M Y');
                    $data[] = (int) ($results[$key] ?? 0);
                }
                break;

            case 'year':
                $currentYear = now()->year;
                $results = Order::whereYear('created_at', '>=', $currentYear - 4)
                    ->selectRaw('YEAR(created_at) as year, COUNT(*) as count')
                    ->groupByRaw('YEAR(created_at)')
                    ->pluck('count', 'year');

                for ($i = 4; $i >= 0; $i--) {
                    $year = $currentYear - $i;
                    $labels[] = (string) $year;
                    $data[] = (int) ($results[$year] ?? 0);
                }
                break;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
            'filter' => $filter,
        ];
    }
}
