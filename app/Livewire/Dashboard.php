<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $totalOrders = 0;
    public $totalTrend = 0;
    public $pendingOrders = 0;
    public $pendingTrend = 0;
    public $inProcessOrders = 0;
    public $processTrend = 0;
    public $completedToday = 0;
    public $completedTrend = 0;
    public $totalRevenue = 0;
    public $revenueTrend = 0;
    public $isSAOManager = false;
    public $isDesign = false;
    public $isProduction = false;
    public $designWaiting = 0;
    public $designInProgress = 0;
    public $designWaitingAcc = 0;
    public $printQueue = 0;
    public $sewingQueue = 0;
    public $recentOrders = [];
    public $weeklyLabels = [];
    public $weeklyData = [];
    public $statusLabels = [];
    public $statusData = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $user = auth()->user();
        $this->isSAOManager = $user->isAdmin();
        $this->isDesign = $user->isDesign();
        $this->isProduction = $user->isProduction();

        $this->totalOrders = Order::whereIn('status', ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])->count();
        $totalLastWeek = Order::whereIn('status', ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $this->totalTrend = $this->totalOrders - $totalLastWeek;

        $this->pendingOrders = Order::where('status', 'menunggu_validasi')->count();
        $pendingLastWeek = Order::where('status', 'menunggu_validasi')
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $this->pendingTrend = $this->pendingOrders - $pendingLastWeek;

        $this->inProcessOrders = Order::whereIn('status', ['di_design', 'siap_cetak', 'diproduksi'])->count();
        $processLastWeek = Order::whereIn('status', ['di_design', 'siap_cetak', 'diproduksi'])
            ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $this->processTrend = $this->inProcessOrders - $processLastWeek;

        $this->completedToday = Order::where('status', 'selesai')->whereDate('updated_at', today())->count();
        $completedYesterday = Order::where('status', 'selesai')->whereDate('updated_at', today()->subDay())->count();
        $this->completedTrend = $this->completedToday - $completedYesterday;

        $recent = Order::with(['user', 'designRequest'])
            ->whereIn('status', ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'])
            ->latest()->take(5)->get();

        $this->recentOrders = $recent->map(fn($o) => [
            'order_number' => $o->order_number,
            'customer_name' => $o->user->name ?? '-',
            'team_name' => $o->designRequest?->team_name ?? 'Pesanan #' . $o->id,
            'created_at' => $o->created_at->format('j M Y'),
            'status' => $o->status,
            'detail_url' => route('staf.detail-pesanan', $o->order_number),
        ])->toArray();

        $labels = [];
        $data = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $labels[] = 'W' . (8 - $i);
            $data[] = Order::whereBetween('created_at', [$weekStart, $weekEnd])->count();
        }
        $this->weeklyLabels = $labels;
        $this->weeklyData = $data;

        $pending = Order::where('status', 'menunggu_validasi')->count();
        $design = Order::whereIn('status', ['dikonfirmasi', 'disetujui', 'di_design'])->count();
        $acc = Order::where('status', 'disetujui')->count();
        $produksi = Order::whereIn('status', ['siap_cetak', 'diproduksi'])->count();
        $selesai = Order::where('status', 'selesai')->count();

        $this->statusLabels = ['Menunggu Validasi', 'Desain', 'Menunggu ACC', 'Produksi', 'Selesai'];
        $this->statusData = [$pending, $design, $acc, $produksi, $selesai];

        if ($this->isSAOManager) {
            $totalRevenue = Payment::where('status', 'success')->sum('amount');
            $lastMonthRevenue = Payment::where('status', 'success')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('amount');
            $this->totalRevenue = $totalRevenue;
            $this->revenueTrend = $totalRevenue > 0 && $lastMonthRevenue > 0
                ? round(($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue * 100) : 0;
        }

        $this->designWaiting = Order::where('status', 'dikonfirmasi')->count();
        $this->designInProgress = Order::where('status', 'di_design')->count();
        $this->designWaitingAcc = Order::where('status', 'disetujui')->count();
        $this->printQueue = Order::where('status', 'siap_cetak')->count();
        $this->sewingQueue = Order::where('status', 'diproduksi')->count();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
