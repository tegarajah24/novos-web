<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);

        return view('internal.laporan', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        $pdf = Pdf::loadView('internal.laporan-pdf', $data);
        $filename = 'laporan-novos-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportCsv(Request $request)
    {
        $data = $this->getReportData($request);

        $filename = 'laporan-novos-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['LAPORAN NOVOS', '', '']);
            fputcsv($file, ['Periode', $data['startDate']->format('d/m/Y') . ' s/d ' . $data['endDate']->format('d/m/Y'), '']);
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['A. Ringkasan Statistik', '', '']);
            fputcsv($file, ['No', 'Metrik', 'Nilai']);
            $ringkasan = [
                ['Total Pesanan', $data['totalPesanan']],
                ['Pesanan Selesai', $data['pesananSelesai']],
                ['Pesanan Diproses', $data['pesananDiproses']],
                ['Pesanan Dibatalkan', $data['pesananDibatalkan']],
                ['Pesanan Pending', $data['pesananPending']],
                ['Total Customer Aktif', $data['totalCustomer']],
                ['Total Pendapatan', 'Rp ' . number_format($data['totalPendapatan'], 0, ',', '.')],
                ['Rata-rata Nilai Transaksi', 'Rp ' . number_format($data['avgTransaksi'], 0, ',', '.')],
                ['Pesanan Terlambat Selesai', $data['pesananTerlambat']],
                ['Total Produk Terjual', $data['totalProdukTerjual'] . ' pcs'],
                ['Rata-rata Waktu Proses', $data['avgProcessingDays'] ? number_format($data['avgProcessingDays'], 1) . ' hari' : '-'],
            ];
            foreach ($ringkasan as $i => $row) {
                fputcsv($file, [$i + 1, $row[0], $row[1]]);
            }
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['B. Statistik Produk', '', '']);
            fputcsv($file, ['Keterangan', 'Produk', 'Jumlah Pesanan']);
            $produkTerbanyak = $data['produkTerbanyak'];
            $produkTersedikit = $data['produkTersedikit'];
            fputcsv($file, ['Paling Banyak Dipesan', $produkTerbanyak->produk ?? '-', $produkTerbanyak->jumlah_pesanan ?? 0]);
            fputcsv($file, ['Paling Sedikit Dipesan', $produkTersedikit->produk ?? '-', $produkTersedikit->jumlah_pesanan ?? 0]);
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['C. Pesanan per Kategori', '', '']);
            fputcsv($file, ['No', 'Kategori', 'Jumlah Pesanan']);
            foreach ($data['pesananPerKategori'] as $i => $row) {
                fputcsv($file, [$i + 1, $row->kategori ?? '-', $row->jumlah]);
            }
            if ($data['pesananPerKategori']->isEmpty()) {
                fputcsv($file, ['-', 'Belum ada data', '-']);
            }
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['D. Pesanan Diselesaikan per Admin', '', '']);
            fputcsv($file, ['No', 'Nama Admin', 'Jumlah Pesanan']);
            foreach ($data['pesananPerAdmin'] as $i => $row) {
                fputcsv($file, [$i + 1, $row->admin_name, $row->jumlah]);
            }
            if ($data['pesananPerAdmin']->isEmpty()) {
                fputcsv($file, ['-', 'Belum ada data', '-']);
            }
            fputcsv($file, ['', '', '']);

            fputcsv($file, ['E. Pendapatan per Periode', '', '']);
            fputcsv($file, ['Tanggal', 'Jumlah Pesanan', 'Pendapatan']);
            foreach ($data['pendapatanHarian'] as $row) {
                fputcsv($file, [
                    Carbon::parse($row->tanggal)->format('d-m-Y'),
                    $row->jumlah,
                    'Rp ' . number_format($row->pendapatan, 0, ',', '.')
                ]);
            }
            if ($data['pendapatanHarian']->isEmpty()) {
                fputcsv($file, ['-', 'Belum ada data', '-']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getReportData($request);

        $filename = 'laporan-novos-' . now()->format('Ymd-His') . '.xls';
        $headers  = [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan</x:Name></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
            echo '<body>';
            echo '<table border="1">';

            echo '<tr><th colspan="3" style="font-size:16px;text-align:center;background:#1a237e;color:white;">LAPORAN NOVOS KONVEKSI</th></tr>';
            echo '<tr><td colspan="3" style="text-align:center;font-weight:bold;">Periode: ' . $data['startDate']->format('d/m/Y') . ' s/d ' . $data['endDate']->format('d/m/Y') . '</td></tr>';
            echo '<tr><td colspan="3"></td></tr>';

            echo '<tr><th colspan="3" style="text-align:left;background:#e5e7eb;font-weight:bold;">A. Ringkasan Statistik</th></tr>';
            echo '<tr><th>No</th><th>Metrik</th><th>Nilai</th></tr>';
            $ringkasan = [
                ['Total Pesanan',       number_format($data['totalPesanan'])],
                ['Pesanan Selesai',     number_format($data['pesananSelesai'])],
                ['Pesanan Diproses',    number_format($data['pesananDiproses'])],
                ['Pesanan Dibatalkan',  number_format($data['pesananDibatalkan'])],
                ['Pesanan Pending',     number_format($data['pesananPending'])],
                ['Total Customer Aktif',number_format($data['totalCustomer'])],
                ['Total Pendapatan',    'Rp ' . number_format($data['totalPendapatan'], 0, ',', '.')],
                ['Rata-rata Nilai Transaksi', 'Rp ' . number_format($data['avgTransaksi'], 0, ',', '.')],
                ['Pesanan Terlambat Selesai', number_format($data['pesananTerlambat'])],
                ['Total Produk Terjual', number_format($data['totalProdukTerjual']) . ' pcs'],
                ['Rata-rata Waktu Proses', $data['avgProcessingDays'] ? number_format($data['avgProcessingDays'], 1) . ' hari' : '-'],
            ];
            foreach ($ringkasan as $i => $r) {
                echo '<tr><td>' . ($i + 1) . '</td><td>' . $r[0] . '</td><td style="font-weight:bold;">' . $r[1] . '</td></tr>';
            }
            echo '<tr><td colspan="3"></td></tr>';

            echo '<tr><th colspan="3" style="text-align:left;background:#e5e7eb;font-weight:bold;">B. Statistik Produk</th></tr>';
            echo '<tr><th>Keterangan</th><th>Produk</th><th>Jumlah Pesanan</th></tr>';
            $produkTerbanyak = $data['produkTerbanyak'];
            $produkTersedikit = $data['produkTersedikit'];
            echo '<tr><td>Paling Banyak Dipesan</td><td>' . e($produkTerbanyak->produk ?? '-') . '</td><td style="font-weight:bold;">' . ($produkTerbanyak->jumlah_pesanan ?? 0) . '</td></tr>';
            echo '<tr><td>Paling Sedikit Dipesan</td><td>' . e($produkTersedikit->produk ?? '-') . '</td><td style="font-weight:bold;">' . ($produkTersedikit->jumlah_pesanan ?? 0) . '</td></tr>';
            echo '<tr><td colspan="3"></td></tr>';

            echo '<tr><th colspan="3" style="text-align:left;background:#e5e7eb;font-weight:bold;">C. Pesanan per Kategori</th></tr>';
            echo '<tr><th>No</th><th>Kategori</th><th>Jumlah Pesanan</th></tr>';
            foreach ($data['pesananPerKategori'] as $i => $k) {
                echo '<tr><td>' . ($i + 1) . '</td><td>' . e($k->kategori ?? '-') . '</td><td style="font-weight:bold;">' . $k->jumlah . '</td></tr>';
            }
            if ($data['pesananPerKategori']->isEmpty()) { echo '<tr><td colspan="3" style="text-align:center;">Belum ada data</td></tr>'; }
            echo '<tr><td colspan="3"></td></tr>';

            echo '<tr><th colspan="3" style="text-align:left;background:#e5e7eb;font-weight:bold;">D. Pesanan Diselesaikan per Admin</th></tr>';
            echo '<tr><th>No</th><th>Nama Admin</th><th>Jumlah Pesanan</th></tr>';
            foreach ($data['pesananPerAdmin'] as $i => $admin) {
                echo '<tr><td>' . ($i + 1) . '</td><td>' . e($admin->admin_name) . '</td><td style="font-weight:bold;">' . $admin->jumlah . '</td></tr>';
            }
            if ($data['pesananPerAdmin']->isEmpty()) { echo '<tr><td colspan="3" style="text-align:center;">Belum ada data</td></tr>'; }
            echo '<tr><td colspan="3"></td></tr>';

            echo '<tr><th colspan="3" style="text-align:left;background:#e5e7eb;font-weight:bold;">E. Pendapatan per Periode</th></tr>';
            echo '<tr><th>Tanggal</th><th>Jumlah Pesanan</th><th>Pendapatan</th></tr>';
            foreach ($data['pendapatanHarian'] as $p) {
                echo '<tr><td>' . Carbon::parse($p->tanggal)->format('d-m-Y') . '</td><td>' . $p->jumlah . '</td><td style="font-weight:bold;">Rp ' . number_format($p->pendapatan, 0, ',', '.') . '</td></tr>';
            }
            if ($data['pendapatanHarian']->isEmpty()) { echo '<tr><td colspan="3" style="text-align:center;">Belum ada data</td></tr>'; }

            echo '</table>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(Request $request): array
    {
        $filter    = $request->get('filter', 'today');
        $startDate = null;
        $endDate   = Carbon::now()->endOfDay();

        switch ($filter) {
            case 'today':  $startDate = Carbon::today(); break;
            case 'week':   $startDate = Carbon::now()->startOfWeek(); break;
            case 'month':  $startDate = Carbon::now()->startOfMonth(); break;
            case 'custom':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : Carbon::today();
                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : Carbon::now()->endOfDay();
                break;
            default: $startDate = Carbon::today();
        }

        $baseQuery = fn() => DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalPesanan      = $baseQuery()->count();
        $pesananSelesai    = $baseQuery()->where('status', 'selesai')->count();
        $pesananDiproses   = $baseQuery()->whereIn('status', ['dikonfirmasi','disetujui','di_design','siap_cetak','diproduksi'])->count();
        $pesananDibatalkan = $baseQuery()->where('status', 'dibatalkan')->count();
        $totalCustomer     = $baseQuery()->distinct('user_id')->count('user_id');
        $totalPendapatan   = $baseQuery()->where('status', 'selesai')->sum('total_price');
        $avgTransaksi      = $pesananSelesai > 0 ? $totalPendapatan / $pesananSelesai : 0;
        $pesananTerlambat  = DB::table('production_tasks')
            ->join('orders', 'production_tasks.order_id', '=', 'orders.id')
            ->where('production_tasks.status', 'selesai')
            ->whereNotNull('production_tasks.finished_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereRaw('DATEDIFF(production_tasks.finished_at, production_tasks.started_at) > 7')
            ->count();

        $pesananPerAdmin = DB::table('order_status_histories')
            ->join('users', 'order_status_histories.changed_by', '=', 'users.id')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('order_status_histories.status', 'selesai')
            ->whereBetween('order_status_histories.created_at', [$startDate, $endDate])
            ->whereIn('roles.name', ['Admin', 'Manager', 'Super Admin'])
            ->select('users.name as admin_name', 'roles.name as role_name', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('users.id', 'users.name', 'roles.name')
            ->orderByDesc('jumlah')
            ->get();

        $produkStats = DB::table('orders')
            ->join('design_requests', 'orders.id', '=', 'design_requests.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('design_requests.material as produk', DB::raw('COUNT(*) as jumlah_pesanan'))
            ->groupBy('design_requests.material')
            ->orderByDesc('jumlah_pesanan')
            ->get();

        $pesananPerKategori = DB::table('orders')
            ->join('design_requests', 'orders.id', '=', 'design_requests.order_id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('design_requests.collar_style as kategori', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('design_requests.collar_style')
            ->orderByDesc('jumlah')
            ->get();

        $pesananPending = $baseQuery()->where('status', 'menunggu_validasi')->count();
        
        $pendapatanHarian = DB::table('orders')
            ->where('status', 'selesai')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_price) as pendapatan'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get();

        $totalProdukTerjual = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->sum('order_items.qty');

        $avgProcessingDays = Order::where('status', 'selesai')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
            ->value('avg_days');

        $produkTerbanyak = $produkStats->first();
        $produkTersedikit = $produkStats->last();

        return compact(
            'filter', 'startDate', 'endDate',
            'totalPesanan', 'pesananSelesai', 'pesananDiproses', 'pesananDibatalkan',
            'pesananPending', 'totalCustomer', 'totalPendapatan', 'avgTransaksi', 'pesananTerlambat',
            'totalProdukTerjual', 'avgProcessingDays',
            'pesananPerAdmin', 'produkStats', 'pesananPerKategori', 'pendapatanHarian',
            'produkTerbanyak', 'produkTersedikit'
        );
    }
}
