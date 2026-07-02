<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Role;
use Carbon\Carbon;

class Laporan extends Component
{
    public $filter = 'today';
    public $customStart = '';
    public $customEnd = '';

    public $startDate;
    public $endDate;

    public $totalPesanan = 0;
    public $pesananSelesai = 0;
    public $pesananDiproses = 0;
    public $pesananDibatalkan = 0;
    public $pesananPending = 0;
    public $totalCustomer = 0;
    public $totalPendapatan = 0;
    public $avgTransaksi = 0;
    public $pesananTerlambat = 0;
    public $totalProdukTerjual = 0;
    public $avgProcessingDays = null;
    public $produkTerbanyak = null;
    public $produkTersedikit = null;
    public $pesananPerAdmin = [];
    public $pesananPerKategori = [];
    public $pendapatanHarian = [];

    protected function getListeners()
    {
        return ['notify'];
    }

    public function mount()
    {
        $this->refreshData();
    }

    public function applyFilter($filter)
    {
        $this->filter = $filter;
        $this->refreshData();
    }

    public function applyCustomFilter()
    {
        $this->filter = 'custom';
        $this->refreshData();
    }

    public function refreshData()
    {
        $endDate = Carbon::now()->endOfDay();

        switch ($this->filter) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'custom':
                $startDate = $this->customStart ? Carbon::parse($this->customStart)->startOfDay() : Carbon::today();
                $endDate = $this->customEnd ? Carbon::parse($this->customEnd)->endOfDay() : Carbon::now()->endOfDay();
                break;
            default:
                $startDate = Carbon::today();
        }

        $this->startDate = $startDate->format('d M Y');
        $this->endDate = $endDate->format('d M Y');

        $baseQuery = fn() => DB::table('orders')->whereBetween('created_at', [$startDate, $endDate]);

        $this->totalPesanan = $baseQuery()->count();
        $this->pesananSelesai = $baseQuery()->where('status', 'selesai')->count();
        $this->pesananDiproses = $baseQuery()->whereIn('status', ['dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi'])->count();
        $this->pesananDibatalkan = $baseQuery()->where('status', 'dibatalkan')->count();
        $this->pesananPending = $baseQuery()->where('status', 'menunggu_validasi')->count();
        $this->totalCustomer = $baseQuery()->distinct('user_id')->count('user_id');
        $this->totalPendapatan = (int) $baseQuery()->where('status', 'selesai')->sum('total_price');
        $this->avgTransaksi = $this->pesananSelesai > 0 ? $this->totalPendapatan / $this->pesananSelesai : 0;

        $this->pesananTerlambat = DB::table('production_tasks')
            ->join('orders', 'production_tasks.order_id', '=', 'orders.id')
            ->where('production_tasks.status', 'selesai')
            ->whereNotNull('production_tasks.finished_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereRaw('DATEDIFF(production_tasks.finished_at, production_tasks.started_at) > 7')
            ->count();

        $this->pesananPerAdmin = DB::table('order_status_histories')
            ->join('users', 'order_status_histories.changed_by', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('order_status_histories.status', 'selesai')
            ->whereBetween('order_status_histories.created_at', [$startDate, $endDate])
            ->whereIn('roles.name', Role::adminNames())
            ->select('users.name as admin_name', 'roles.name as role_name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('users.id', 'users.name', 'roles.name')
            ->orderByDesc('jumlah')
            ->get()->toArray();

        $produkStats = DB::table('orders')
            ->join('design_requests', 'orders.id', '=', 'design_requests.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('design_requests.material as produk', DB::raw('COUNT(*) as jumlah_pesanan'))
            ->groupBy('design_requests.material')
            ->orderByDesc('jumlah_pesanan')
            ->get();

        $this->produkTerbanyak = $produkStats->first() ? (array) $produkStats->first() : null;
        $this->produkTersedikit = $produkStats->last() ? (array) $produkStats->last() : null;

        $this->pesananPerKategori = DB::table('orders')
            ->join('design_requests', 'orders.id', '=', 'design_requests.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('design_requests.collar_style as kategori', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('design_requests.collar_style')
            ->orderByDesc('jumlah')
            ->get()->toArray();

        $this->pendapatanHarian = DB::table('orders')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(total_price) as pendapatan'), DB::raw('COUNT(*) as jumlah'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get()->toArray();

        $this->totalProdukTerjual = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('order_items.qty');

        $this->avgProcessingDays = Order::where('status', 'selesai')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');
    }

    public function render()
    {
        return view('livewire.laporan');
    }
}
