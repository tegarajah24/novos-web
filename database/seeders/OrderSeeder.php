<?php

namespace Database\Seeders;

use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductionTask;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $customer   = User::where('email', 'customer@novos.com')->first();
        $admin      = User::where('email', 'admin@novos.com')->first();
        $design     = User::where('email', 'design@novos.com')->first();
        $produksi   = User::where('email', 'produksi@novos.com')->first();

        if (! $customer || ! $admin || ! $design || ! $produksi) {
            $this->command->warn('User belum lengkap. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // =====================================================================
        // PESANAN 1 — Stage PRINTING (status order: siap_cetak)
        // Tim Badminton Garuda FC, baru turun dari Design, menunggu dicetak
        // =====================================================================
        $order1 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260610-001'],
            [
                'user_id'     => $customer->id,
                'status'      => 'siap_cetak',
                'notes'       => 'Tolong logo tim di bagian dada kiri, warna primary biru navy. Deadline mepet karena turnamen 20 Juni.',
                'admin_notes' => 'Desain sudah diapprove customer. Kirim ke produksi segera.',
                'total_price' => 3750000.00,
                'confirmed_at' => Carbon::now()->subDays(8),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'size' => 'M'],
            ['qty' => 8, 'price_per_item' => 150000, 'subtotal' => 1200000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'size' => 'L'],
            ['qty' => 10, 'price_per_item' => 150000, 'subtotal' => 1500000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order1->id, 'size' => 'XL'],
            ['qty' => 7, 'price_per_item' => 150000, 'subtotal' => 1050000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'team_name'        => 'Badminton Garuda FC',
                'primary_color'    => '#1a237e',
                'secondary_color'  => '#ffffff',
                'motif'            => 'Sublimasi full-print gradient diagonal',
                'material'         => 'Drifit Polyester 180gsm',
                'collar_style'     => 'V-Neck',
                'additional_notes' => 'Logo sponsor di punggung bawah, nomor punggung 1-25.',
            ]
        );

        // Riwayat status pesanan 1
        $this->recordHistory($order1->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order1->id, 'menunggu_pembayaran', $admin->id,    'Admin memvalidasi pesanan');
        $this->recordHistory($order1->id, 'dikonfirmasi', $customer->id, 'Pembayaran berhasil dikonfirmasi');
        $this->recordHistory($order1->id, 'disetujui',   $admin->id,    'Diteruskan ke tim Design');
        $this->recordHistory($order1->id, 'di_design',   $admin->id,    'Diteruskan ke tim Design');
        $this->recordHistory($order1->id, 'siap_cetak',  $design->id,   'Design selesai, siap ke Produksi');

        // Production task untuk pesanan 1 → stage PRINTING (task status: pending)
        ProductionTask::firstOrCreate(
            ['order_id' => $order1->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'pending',
                'notes'       => 'STAGE:printing | Siap dicetak, menunggu giliran mesin printing.',
            ]
        );

        // =====================================================================
        // PESANAN 2 — Stage PRINTING (status order: siap_cetak)
        // Tim Futsal SMK Nusantara
        // =====================================================================
        $order2 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260612-002'],
            [
                'user_id'     => $customer->id,
                'status'      => 'siap_cetak',
                'notes'       => 'Jersey futsal polos, nama pemain di punggung, nomor 1-16.',
                'admin_notes' => 'Konfirmasi ukuran sudah sesuai PO customer.',
                'total_price' => 2400000.00,
                'confirmed_at' => Carbon::now()->subDays(6),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'size' => 'S'],
            ['qty' => 4, 'price_per_item' => 135000, 'subtotal' => 540000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'size' => 'M'],
            ['qty' => 6, 'price_per_item' => 135000, 'subtotal' => 810000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'size' => 'L'],
            ['qty' => 5, 'price_per_item' => 135000, 'subtotal' => 675000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order2->id, 'size' => 'XL'],
            ['qty' => 1, 'price_per_item' => 135000, 'subtotal' => 135000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'team_name'        => 'Tim Futsal SMK Nusantara',
                'primary_color'    => '#e53935',
                'secondary_color'  => '#ffd600',
                'motif'            => 'Blocking warna merah-kuning, motif lightning di bahu',
                'material'         => 'Hycon PE Knit',
                'collar_style'     => 'Round Neck',
                'additional_notes' => 'Font nama pemain: Bold Arial. Font nomor: Impact.',
            ]
        );

        $this->recordHistory($order2->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order2->id, 'menunggu_pembayaran', $admin->id,    'Admin memvalidasi');
        $this->recordHistory($order2->id, 'dikonfirmasi', $customer->id, 'Pembayaran dikonfirmasi');
        $this->recordHistory($order2->id, 'disetujui',   $admin->id,    'Ke tim Design');
        $this->recordHistory($order2->id, 'di_design',   $admin->id,    'Ke tim Design');
        $this->recordHistory($order2->id, 'siap_cetak',  $design->id,   'Design finish');

        ProductionTask::firstOrCreate(
            ['order_id' => $order2->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'pending',
                'notes'       => 'STAGE:printing | Antrian ke-2 mesin printing hari ini.',
            ]
        );

        // =====================================================================
        // PESANAN 3 — Stage JAHIT (status order: diproduksi, task: pending)
        // Klub Basket Rajawali — Sudah selesai printing, masuk jahit
        // =====================================================================
        $order3 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260605-003'],
            [
                'user_id'     => $customer->id,
                'status'      => 'diproduksi',
                'notes'       => 'Jersey basket, ada patch border di leher dan lengan. Pastikan jahitan rapi.',
                'admin_notes' => 'Printing selesai 13 Juni, masuk jahit.',
                'total_price' => 5250000.00,
                'confirmed_at' => Carbon::now()->subDays(14),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'size' => 'M'],
            ['qty' => 5, 'price_per_item' => 175000, 'subtotal' => 875000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'size' => 'L'],
            ['qty' => 15, 'price_per_item' => 175000, 'subtotal' => 2625000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'size' => 'XL'],
            ['qty' => 8, 'price_per_item' => 175000, 'subtotal' => 1400000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order3->id, 'size' => 'XXL'],
            ['qty' => 2, 'price_per_item' => 175000, 'subtotal' => 350000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order3->id],
            [
                'team_name'        => 'Klub Basket Rajawali',
                'primary_color'    => '#6a1b9a',
                'secondary_color'  => '#f9a825',
                'motif'            => 'Mesh pattern di samping, nomor dada besar',
                'material'         => 'Mesh Polyester Jersey',
                'collar_style'     => 'V-Neck',
                'additional_notes' => 'Patch border emas di leher dan ujung lengan. Nama dan nomor wajib presisi.',
            ]
        );

        $this->recordHistory($order3->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order3->id, 'menunggu_pembayaran', $admin->id,   'Admin validasi');
        $this->recordHistory($order3->id, 'dikonfirmasi', $customer->id, 'Pembayaran dikonfirmasi');
        $this->recordHistory($order3->id, 'disetujui',   $admin->id,   'Ke Design');
        $this->recordHistory($order3->id, 'di_design',   $admin->id,   'Ke Design');
        $this->recordHistory($order3->id, 'siap_cetak',  $design->id,  'Design selesai');
        $this->recordHistory($order3->id, 'diproduksi',  $admin->id,   'Masuk divisi Produksi — Printing selesai');

        ProductionTask::firstOrCreate(
            ['order_id' => $order3->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'dikerjakan', // ← task dikerjakan = stage JAHIT
                'started_at'  => Carbon::now()->subDays(3),
                'notes'       => 'STAGE:jahit | Printing selesai 13 Jun. Proses jahit dimulai.',
            ]
        );

        // =====================================================================
        // PESANAN 4 — Stage JAHIT (status order: diproduksi, task: dikerjakan)
        // Seragam Voli Instansi Dinas Pemuda
        // =====================================================================
        $order4 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260601-004'],
            [
                'user_id'     => $customer->id,
                'status'      => 'diproduksi',
                'notes'       => 'Seragam voli wanita, ukuran S lebih banyak. Ada detail renda di lengan.',
                'admin_notes' => 'Printing selesai 10 Juni. Proses jahit.',
                'total_price' => 4200000.00,
                'confirmed_at' => Carbon::now()->subDays(18),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order4->id, 'size' => 'S'],
            ['qty' => 12, 'price_per_item' => 165000, 'subtotal' => 1980000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order4->id, 'size' => 'M'],
            ['qty' => 10, 'price_per_item' => 165000, 'subtotal' => 1650000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order4->id, 'size' => 'L'],
            ['qty' => 3, 'price_per_item' => 165000, 'subtotal' => 495000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order4->id],
            [
                'team_name'        => 'Tim Voli Dinas Pemuda Kota',
                'primary_color'    => '#00838f',
                'secondary_color'  => '#ffffff',
                'motif'            => 'Geometric print warna tosca-putih',
                'material'         => 'Spandex Jersey 4-Way Stretch',
                'collar_style'     => 'Round Neck',
                'additional_notes' => 'Kancing di bahu kiri untuk seragam wanita. Logo dinas di dada kanan.',
            ]
        );

        $this->recordHistory($order4->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order4->id, 'menunggu_pembayaran', $admin->id,   'Admin validasi');
        $this->recordHistory($order4->id, 'dikonfirmasi', $customer->id, 'Pembayaran dikonfirmasi');
        $this->recordHistory($order4->id, 'disetujui',   $admin->id,   'Ke Design');
        $this->recordHistory($order4->id, 'di_design',   $admin->id,   'Ke Design');
        $this->recordHistory($order4->id, 'siap_cetak',  $design->id,  'Design selesai');
        $this->recordHistory($order4->id, 'diproduksi',  $admin->id,   'Masuk Produksi');

        ProductionTask::firstOrCreate(
            ['order_id' => $order4->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'dikerjakan', // ← stage JAHIT
                'started_at'  => Carbon::now()->subDays(5),
                'notes'       => 'STAGE:jahit | Jahit seragam wanita, ekstra hati-hati di bagian kancing bahu.',
            ]
        );

        // =====================================================================
        // PESANAN 5 — Stage QC (status order: diproduksi, task: selesai)
        // Jersey Sepak Bola SSB Tunas Muda — Jahit selesai, masuk QC
        // =====================================================================
        $order5 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260528-005'],
            [
                'user_id'     => $customer->id,
                'status'      => 'diproduksi',
                'notes'       => 'Jersey anak-anak SSB, ukuran S dan M. Warna terang. Pastikan jahitan kuat.',
                'admin_notes' => 'Jahit selesai 17 Juni. Masuk QC final.',
                'total_price' => 2925000.00,
                'confirmed_at' => Carbon::now()->subDays(22),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order5->id, 'size' => 'S'],
            ['qty' => 13, 'price_per_item' => 125000, 'subtotal' => 1625000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order5->id, 'size' => 'M'],
            ['qty' => 10, 'price_per_item' => 125000, 'subtotal' => 1250000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order5->id],
            [
                'team_name'        => 'SSB Tunas Muda Jaya',
                'primary_color'    => '#43a047',
                'secondary_color'  => '#ffffff',
                'motif'            => 'Stripe vertikal hijau-putih, motif rumput di bawah',
                'material'         => 'Drifit Polyester 160gsm',
                'collar_style'     => 'V-Neck',
                'additional_notes' => 'Nama pemain huruf kapital semua. Logo SSB di dada kiri ukuran 6cm.',
            ]
        );

        $this->recordHistory($order5->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order5->id, 'menunggu_pembayaran', $admin->id,   'Admin validasi');
        $this->recordHistory($order5->id, 'dikonfirmasi', $customer->id, 'Pembayaran dikonfirmasi');
        $this->recordHistory($order5->id, 'disetujui',   $admin->id,   'Ke Design');
        $this->recordHistory($order5->id, 'di_design',   $admin->id,   'Ke Design');
        $this->recordHistory($order5->id, 'siap_cetak',  $design->id,  'Design selesai');
        $this->recordHistory($order5->id, 'diproduksi',  $admin->id,   'Masuk Produksi');

        ProductionTask::firstOrCreate(
            ['order_id' => $order5->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'selesai', // ← task selesai = stage QC
                'started_at'  => Carbon::now()->subDays(10),
                'finished_at' => Carbon::now()->subDays(2),
                'notes'       => 'STAGE:qc | Printing & Jahit selesai. Menunggu pengecekan QC final.',
            ]
        );

        // =====================================================================
        // PESANAN 6 — Stage QC (status order: diproduksi, task: selesai)
        // Polo Shirt Komunitas Sepeda
        // =====================================================================
        $order6 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260525-006'],
            [
                'user_id'     => $customer->id,
                'status'      => 'diproduksi',
                'notes'       => 'Polo shirt untuk komunitas sepeda, ada kerah polo, tombol 3.',
                'admin_notes' => 'QC terakhir. Pastikan kancing berfungsi semua.',
                'total_price' => 6500000.00,
                'confirmed_at' => Carbon::now()->subDays(25),
            ]
        );

        OrderItem::firstOrCreate(
            ['order_id' => $order6->id, 'size' => 'S'],
            ['qty' => 5, 'price_per_item' => 200000, 'subtotal' => 1000000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order6->id, 'size' => 'M'],
            ['qty' => 15, 'price_per_item' => 200000, 'subtotal' => 3000000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order6->id, 'size' => 'L'],
            ['qty' => 10, 'price_per_item' => 200000, 'subtotal' => 2000000]
        );
        OrderItem::firstOrCreate(
            ['order_id' => $order6->id, 'size' => 'XL'],
            ['qty' => 2, 'price_per_item' => 200000, 'subtotal' => 400000]
        );

        DesignRequest::firstOrCreate(
            ['order_id' => $order6->id],
            [
                'team_name'        => 'Komunitas Sepeda Nusantara',
                'primary_color'    => '#f57c00',
                'secondary_color'  => '#263238',
                'motif'            => 'Pola honeycomb di samping, logo komunitas di dada',
                'material'         => 'CoolMax Polo Fabric',
                'collar_style'     => 'Polo Neck',
                'additional_notes' => 'Kancing 3 biji warna hitam. Punggung ada slogan komunitas.',
            ]
        );

        $this->recordHistory($order6->id, 'menunggu_validasi',  $customer->id, 'Pesanan masuk');
        $this->recordHistory($order6->id, 'menunggu_pembayaran', $admin->id,   'Admin validasi');
        $this->recordHistory($order6->id, 'dikonfirmasi', $customer->id, 'Pembayaran dikonfirmasi');
        $this->recordHistory($order6->id, 'disetujui',   $admin->id,   'Ke Design');
        $this->recordHistory($order6->id, 'di_design',   $admin->id,   'Ke Design');
        $this->recordHistory($order6->id, 'siap_cetak',  $design->id,  'Design selesai');
        $this->recordHistory($order6->id, 'diproduksi',  $admin->id,   'Masuk Produksi');

        ProductionTask::firstOrCreate(
            ['order_id' => $order6->id],
            [
                'assigned_to' => $produksi->id,
                'status'      => 'selesai', // ← stage QC
                'started_at'  => Carbon::now()->subDays(12),
                'finished_at' => Carbon::now()->subDays(1),
                'notes'       => 'STAGE:qc | Semua proses selesai. QC final sebelum packing.',
            ]
        );

        $this->command->info('OrderSeeder selesai: 6 pesanan berhasil dibuat (2 Printing, 2 Jahit, 2 QC).');
    }

    /**
     * Helper: Catat riwayat status pesanan.
     * Hanya insert jika belum ada (idempotent).
     */
    private function recordHistory(int $orderId, string $status, int $changedBy, string $notes): void
    {
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $orderId, 'status' => $status],
            ['changed_by' => $changedBy, 'notes' => $notes]
        );
    }
}
