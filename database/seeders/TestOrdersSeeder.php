<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $adminUserId = 39; // Martin Lingga Widiawan (Manager)
        $customerId = 38;  // Testing Customer
        $designUserId = DB::table('users')->where('role_id', 42)->first()?->id;
        $produksiUserId = DB::table('users')->where('role_id', 43)->first()?->id;

        $now = now();
        $orders = [];

        // ═══════════════════════════════════════════════════════════════
        // ORDER 1: Jersey Futsal — status: di_design
        // ═══════════════════════════════════════════════════════════════
        $items1 = [
            ['size' => 'M', 'no' => '10', 'nama' => 'Raka', 'customizations' => ['jenis_kerah' => 'O-Neck V1', 'jenis_bahan' => 'Milano Premium', 'size' => 'M', 'jenis_pola' => 'Reguler Overdeck', 'lengan' => 'Lengan Pendek', 'saku' => '', 'kerah_manset_knit' => '', 'logo_timbul' => '', 'sablon' => ''], 'price' => 95000],
            ['size' => 'L', 'no' => '11', 'nama' => 'Dimas', 'customizations' => ['jenis_kerah' => 'O-Neck V1', 'jenis_bahan' => 'Milano Premium', 'size' => 'L', 'jenis_pola' => 'Reguler Overdeck', 'lengan' => 'Lengan Pendek', 'saku' => '', 'kerah_manset_knit' => '', 'logo_timbul' => '', 'sablon' => ''], 'price' => 95000],
            ['size' => 'XL', 'no' => '12', 'nama' => 'Fajar', 'customizations' => ['jenis_kerah' => 'O-Neck V1', 'jenis_bahan' => 'Milano Premium', 'size' => 'XL', 'jenis_pola' => 'Reguler Overdeck', 'lengan' => 'Lengan Pendek', 'saku' => 'Saku Bobok', 'kerah_manset_knit' => '', 'logo_timbul' => '', 'sablon' => ''], 'price' => 102000],
        ];
        $totalQty1 = count($items1);
        $totalPrice1 = array_sum(array_map(fn($i) => $i['price'], $items1));

        $orderId1 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-002',
            'status' => 'di_design',
            'notes' => "Jenis Kerah: O-Neck V1\nJenis Bahan: Milano Premium\nSize: M, L, XL\n=== Detail Pesanan ===\n10, Raka, -, M, Milano Premium\n11, Dimas, -, L, Milano Premium\n12, Fajar, -, XL, Milano Premium (Saku Bobok)",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice1,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId1,
            'team_name' => 'FC Garuda',
            'nama_artikel' => 'Jersey Futsal Home',
            'nama_pemesan' => 'Budi Santoso',
            'no_punggung' => null,
            'detail_sponsor' => 'PT Maju Jaya',
            'primary_color' => '#1a3a8a',
            'secondary_color' => '#ffffff',
            'motif' => 'Gradient navy to white',
            'material' => 'Milano Premium',
            'collar_style' => 'O-Neck V1',
            'priority' => 'normal',
            'additional_notes' => "Jenis Kerah: O-Neck V1\nJenis Bahan: Milano Premium\n=== Detail Pesanan ===\n10, Raka, -, M, Milano Premium\n11, Dimas, -, L, Milano Premium\n12, Fajar, -, XL, Milano Premium (Saku Bobok)",
            'customizations' => json_encode([
                'jenis_kerah' => 'O-Neck V1',
                'jenis_bahan' => 'Milano Premium',
                'size' => 'M',
                'jenis_pola' => 'Reguler Overdeck',
                'lengan' => 'Lengan Pendek',
            ]),
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        $item1Id = DB::table('order_items')->insertGetId([
            'order_id' => $orderId1,
            'size' => 'Mix',
            'qty' => $totalQty1,
            'price_per_item' => $totalPrice1 / $totalQty1,
            'subtotal' => $totalPrice1,
            'created_at' => $now->copy()->subDays(5),
            'updated_at' => $now->copy()->subDays(5),
        ]);

        foreach ($items1 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId1,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => $item['customizations']['saku'] ?: null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId1, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            ['order_id' => $orderId1, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(4), 'updated_at' => $now->copy()->subDays(4)],
            ['order_id' => $orderId1, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            ['order_id' => $orderId1, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(2)],
        ]);

        // ═══════════════════════════════════════════════════════════════
        // ORDER 2: Jaket Training — status: siap_cetak
        // ═══════════════════════════════════════════════════════════════
        $items2 = [
            ['size' => 'L', 'no' => '01', 'nama' => 'Andi', 'customizations' => ['tipe_jaket' => 'Training', 'furing' => 'Furing'], 'price' => 195000],
            ['size' => 'XL', 'no' => '02', 'nama' => 'Budi', 'customizations' => ['tipe_jaket' => 'Training', 'furing' => 'Furing'], 'price' => 195000],
            ['size' => 'XXL', 'no' => '03', 'nama' => 'Citra', 'customizations' => ['tipe_jaket' => 'Training', 'furing' => 'Furing'], 'price' => 195000],
            ['size' => 'L', 'no' => '04', 'nama' => 'Dian', 'customizations' => ['tipe_jaket' => 'Training', 'furing' => 'Non Furing'], 'price' => 175000],
        ];
        $totalQty2 = count($items2);
        $totalPrice2 = array_sum(array_map(fn($i) => $i['price'], $items2));

        $orderId2 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-003',
            'status' => 'siap_cetak',
            'notes' => "Tipe Jaket: Training\nFuring: Furing\n=== Detail Pesanan ===\n01, Andi, -, L, Training, Furing\n02, Budi, -, XL, Training, Furing\n03, Citra, -, XXL, Training, Furing\n04, Dian, -, L, Training, Non Furing",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice2,
            'confirmed_at' => $now->copy()->subDays(10),
            'created_at' => $now->copy()->subDays(14),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId2,
            'team_name' => 'Basketball Club',
            'nama_artikel' => 'Jaket Training 2026',
            'nama_pemesan' => 'Hendra Wijaya',
            'primary_color' => '#c41e3a',
            'secondary_color' => '#000000',
            'motif' => 'Stripes',
            'material' => 'Diadora',
            'collar_style' => '-',
            'jenis_potongan' => '-',
            'lengan_jahitan' => '-',
            'priority' => 'express',
            'additional_notes' => "Tipe Jaket: Training\nFuring: Furing\n=== Detail Pesanan ===\n01, Andi, -, L, Training, Furing\n02, Budi, -, XL, Training, Furing\n03, Citra, -, XXL, Training, Furing\n04, Dian, -, L, Training, Non Furing",
            'customizations' => json_encode([
                'tipe_jaket' => 'Training',
                'furing' => 'Furing',
            ]),
            'design_files' => json_encode([
                ['name' => 'mockup-depan.jpg', 'type' => 'image/jpeg', 'path' => 'design-files/' . 'NVS-20260723-003/mockup-depan.jpg', 'role' => 'mockup_depan'],
            ]),
            'created_at' => $now->copy()->subDays(14),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        DB::table('order_items')->insertGetId([
            'order_id' => $orderId2,
            'size' => 'Mix',
            'qty' => $totalQty2,
            'price_per_item' => $totalPrice2 / $totalQty2,
            'subtotal' => $totalPrice2,
            'created_at' => $now->copy()->subDays(14),
            'updated_at' => $now->copy()->subDays(14),
        ]);

        foreach ($items2 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId2,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(14),
                'updated_at' => $now->copy()->subDays(14),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId2, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(14), 'updated_at' => $now->copy()->subDays(14)],
            ['order_id' => $orderId2, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(13), 'updated_at' => $now->copy()->subDays(13)],
            ['order_id' => $orderId2, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(12), 'updated_at' => $now->copy()->subDays(12)],
            ['order_id' => $orderId2, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(11), 'updated_at' => $now->copy()->subDays(11)],
            ['order_id' => $orderId2, 'status' => 'siap_cetak', 'changed_by' => $designUserId, 'notes' => 'Design selesai, siap diproduksi', 'created_at' => $now->copy()->subDays(1), 'updated_at' => $now->copy()->subDays(1)],
        ]);

        // Payment for Order 2
        DB::table('payments')->insert([
            'order_id' => $orderId2,
            'amount' => $totalPrice2 + 50000, // express surcharge
            'dp_amount' => $totalPrice2 * 0.5,
            'status' => 'success',
            'payment_method' => 'Transfer BCA',
            'paid_at' => $now->copy()->subDays(12),
            'created_at' => $now->copy()->subDays(12),
            'updated_at' => $now->copy()->subDays(12),
        ]);

        // ═══════════════════════════════════════════════════════════════
        // ORDER 3: Kemeja PDL Tactical — status: diproduksi
        // ═══════════════════════════════════════════════════════════════
        $items3 = [
            ['size' => 'L', 'no' => '01', 'nama' => 'Sergio', 'customizations' => ['tipe_kemeja' => 'PDL Tactical', 'bahan' => 'Ripstop'], 'price' => 260000],
            ['size' => 'L', 'no' => '02', 'nama' => 'Ricardo', 'customizations' => ['tipe_kemeja' => 'PDL Tactical', 'bahan' => 'Ripstop'], 'price' => 260000],
            ['size' => 'XL', 'no' => '03', 'nama' => 'Marco', 'customizations' => ['tipe_kemeja' => 'PDL Tactical', 'bahan' => 'Ripstop'], 'price' => 260000],
            ['size' => 'M', 'no' => '04', 'nama' => 'Pablo', 'customizations' => ['tipe_kemeja' => 'PDL Tactical', 'bahan' => 'Nagata'], 'price' => 270000],
            ['size' => 'XXL', 'no' => '05', 'nama' => 'Carlos', 'customizations' => ['tipe_kemeja' => 'PDL Tactical', 'bahan' => 'Ripstop'], 'price' => 260000],
        ];
        $totalQty3 = count($items3);
        $totalPrice3 = array_sum(array_map(fn($i) => $i['price'], $items3));

        $orderId3 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-004',
            'status' => 'diproduksi',
            'notes' => "Tipe Kemeja: PDL Tactical\nBahan: Ripstop\n=== Detail Pesanan ===\n01, Sergio, -, L, PDL Tactical, Ripstop\n02, Ricardo, -, L, PDL Tactical, Ripstop\n03, Marco, -, XL, PDL Tactical, Ripstop\n04, Pablo, -, M, PDL Tactical, Nagata\n05, Carlos, -, XXL, PDL Tactical, Ripstop",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice3,
            'confirmed_at' => $now->copy()->subDays(20),
            'created_at' => $now->copy()->subDays(25),
            'updated_at' => $now->copy()->subHours(6),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId3,
            'team_name' => 'Security Team Alpha',
            'nama_artikel' => 'Kemeja PDL Security',
            'nama_pemesan' => 'Ahmad Fauzi',
            'primary_color' => '#2d4a22',
            'secondary_color' => '#1a1a1a',
            'material' => 'Ripstop',
            'collar_style' => '-',
            'priority' => 'super_express',
            'additional_notes' => "Tipe Kemeja: PDL Tactical\nBahan: Ripstop\n=== Detail Pesanan ===\n01, Sergio, -, L, PDL Tactical, Ripstop\n02, Ricardo, -, L, PDL Tactical, Ripstop\n03, Marco, -, XL, PDL Tactical, Ripstop\n04, Pablo, -, M, PDL Tactical, Nagata\n05, Carlos, -, XXL, PDL Tactical, Ripstop",
            'customizations' => json_encode([
                'tipe_kemeja' => 'PDL Tactical',
                'bahan' => 'Ripstop',
            ]),
            'created_at' => $now->copy()->subDays(25),
            'updated_at' => $now->copy()->subDays(5),
        ]);

        DB::table('order_items')->insertGetId([
            'order_id' => $orderId3,
            'size' => 'Mix',
            'qty' => $totalQty3,
            'price_per_item' => $totalPrice3 / $totalQty3,
            'subtotal' => $totalPrice3,
            'created_at' => $now->copy()->subDays(25),
            'updated_at' => $now->copy()->subDays(25),
        ]);

        foreach ($items3 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId3,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(25),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId3, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(25), 'updated_at' => $now->copy()->subDays(25)],
            ['order_id' => $orderId3, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(24), 'updated_at' => $now->copy()->subDays(24)],
            ['order_id' => $orderId3, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(23), 'updated_at' => $now->copy()->subDays(23)],
            ['order_id' => $orderId3, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(22), 'updated_at' => $now->copy()->subDays(22)],
            ['order_id' => $orderId3, 'status' => 'siap_cetak', 'changed_by' => $designUserId, 'notes' => 'Design selesai', 'created_at' => $now->copy()->subDays(10), 'updated_at' => $now->copy()->subDays(10)],
            ['order_id' => $orderId3, 'status' => 'menunggu_spk', 'changed_by' => $adminUserId, 'notes' => 'SPK disiapkan', 'created_at' => $now->copy()->subDays(8), 'updated_at' => $now->copy()->subDays(8)],
            ['order_id' => $orderId3, 'status' => 'diproduksi', 'changed_by' => $produksiUserId, 'notes' => 'Mulai produksi - tahap printing', 'created_at' => $now->copy()->subDays(6), 'updated_at' => $now->copy()->subDays(6)],
        ]);

        DB::table('payments')->insert([
            'order_id' => $orderId3,
            'amount' => $totalPrice3 + 150000, // super_express surcharge
            'dp_amount' => $totalPrice3 * 0.5,
            'status' => 'success',
            'payment_method' => 'Transfer Mandiri',
            'paid_at' => $now->copy()->subDays(23),
            'created_at' => $now->copy()->subDays(23),
            'updated_at' => $now->copy()->subDays(23),
        ]);

        DB::table('production_tasks')->insert([
            'order_id' => $orderId3,
            'assigned_to' => $produksiUserId,
            'status' => 'dikerjakan',
            'started_at' => $now->copy()->subDays(6),
            'notes' => 'Tahap printing sedang berlangsung',
            'created_at' => $now->copy()->subDays(6),
            'updated_at' => $now->copy()->subDays(6),
        ]);

        // ═══════════════════════════════════════════════════════════════
        // ORDER 4: Aksesoris Lanyard — status: diproduksi
        // Tests depends_on attributes (Lanyard → tipe_cetak, card_holder)
        // ═══════════════════════════════════════════════════════════════
        $items4 = [
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Lanyard', 'tipe_cetak' => 'Print 2 Sisi', 'card_holder' => 'PVC'], 'price' => 15000],
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Lanyard', 'tipe_cetak' => 'Print 2 Sisi', 'card_holder' => 'PVC'], 'price' => 15000],
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Lanyard', 'tipe_cetak' => 'Print 1 Sisi', 'card_holder' => 'PVC'], 'price' => 14000],
        ];
        $totalQty4 = count($items4);
        $totalPrice4 = array_sum(array_map(fn($i) => $i['price'], $items4));

        $orderId4 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-005',
            'status' => 'diproduksi',
            'notes' => "Tipe Aksesoris: Lanyard\nTipe Cetak: Print 2 Sisi\nCard Holder: PVC\n=== Detail Pesanan ===\n50 pcs Lanyard, Print 2 Sisi, Card Holder PVC",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice4,
            'confirmed_at' => $now->copy()->subDays(15),
            'created_at' => $now->copy()->subDays(18),
            'updated_at' => $now->copy()->subHours(3),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId4,
            'team_name' => 'Event Organizer',
            'nama_artikel' => 'Lanyard ID Card Event',
            'nama_pemesan' => 'Sari Dewi',
            'primary_color' => '#ff6600',
            'secondary_color' => '#ffffff',
            'material' => null,
            'collar_style' => null,
            'priority' => 'normal',
            'additional_notes' => "Tipe Aksesoris: Lanyard\nTipe Cetak: Print 2 Sisi\nCard Holder: PVC",
            'customizations' => json_encode([
                'tipe_aksesoris' => 'Lanyard',
                'tipe_cetak' => 'Print 2 Sisi',
                'card_holder' => 'PVC',
            ]),
            'created_at' => $now->copy()->subDays(18),
            'updated_at' => $now->copy()->subDays(8),
        ]);

        DB::table('order_items')->insertGetId([
            'order_id' => $orderId4,
            'size' => '-',
            'qty' => $totalQty4,
            'price_per_item' => $totalPrice4 / $totalQty4,
            'subtotal' => $totalPrice4,
            'created_at' => $now->copy()->subDays(18),
            'updated_at' => $now->copy()->subDays(18),
        ]);

        foreach ($items4 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId4,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(18),
                'updated_at' => $now->copy()->subDays(18),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId4, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(18), 'updated_at' => $now->copy()->subDays(18)],
            ['order_id' => $orderId4, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(17), 'updated_at' => $now->copy()->subDays(17)],
            ['order_id' => $orderId4, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(16), 'updated_at' => $now->copy()->subDays(16)],
            ['order_id' => $orderId4, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(15), 'updated_at' => $now->copy()->subDays(15)],
            ['order_id' => $orderId4, 'status' => 'siap_cetak', 'changed_by' => $designUserId, 'notes' => 'Design selesai', 'created_at' => $now->copy()->subDays(10), 'updated_at' => $now->copy()->subDays(10)],
            ['order_id' => $orderId4, 'status' => 'menunggu_spk', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(9), 'updated_at' => $now->copy()->subDays(9)],
            ['order_id' => $orderId4, 'status' => 'diproduksi', 'changed_by' => $produksiUserId, 'notes' => 'Mulai produksi lanyard', 'created_at' => $now->copy()->subHours(3), 'updated_at' => $now->copy()->subHours(3)],
        ]);

        DB::table('payments')->insert([
            'order_id' => $orderId4,
            'amount' => $totalPrice4,
            'dp_amount' => $totalPrice4,
            'status' => 'success',
            'payment_method' => 'Transfer BCA',
            'paid_at' => $now->copy()->subDays(16),
            'created_at' => $now->copy()->subDays(16),
            'updated_at' => $now->copy()->subDays(16),
        ]);

        DB::table('production_tasks')->insert([
            'order_id' => $orderId4,
            'assigned_to' => $produksiUserId,
            'status' => 'dikerjakan',
            'started_at' => $now->copy()->subHours(3),
            'notes' => 'Mulai cetak lanyard',
            'created_at' => $now->copy()->subHours(3),
            'updated_at' => $now->copy()->subHours(3),
        ]);

        // ═══════════════════════════════════════════════════════════════
        // ORDER 5: Kaos & Polo Cotton 30s — status: di_design
        // ═══════════════════════════════════════════════════════════════
        $items5 = [
            ['size' => 'S', 'no' => '07', 'nama' => 'Rina', 'customizations' => ['tipe_kaos' => 'Cotton 30s'], 'price' => 70000],
            ['size' => 'M', 'no' => '08', 'nama' => 'Dewi', 'customizations' => ['tipe_kaos' => 'Cotton 30s'], 'price' => 70000],
            ['size' => 'L', 'no' => '09', 'nama' => 'Maya', 'customizations' => ['tipe_kaos' => 'Cotton 30s'], 'price' => 70000],
            ['size' => 'XL', 'no' => '10', 'nama' => 'Ratna', 'customizations' => ['tipe_kaos' => 'Cotton 30s'], 'price' => 70000],
            ['size' => '2XL', 'no' => '11', 'nama' => 'Putri', 'customizations' => ['tipe_kaos' => 'Cotton 30s'], 'price' => 80000],
        ];
        $totalQty5 = count($items5);
        $totalPrice5 = array_sum(array_map(fn($i) => $i['price'], $items5));

        $orderId5 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-006',
            'status' => 'di_design',
            'notes' => "Tipe Kaos/Polo: Cotton 30s\nSize: S-XL\n=== Detail Pesanan ===\n07, Rina, -, S, Cotton 30s\n08, Dewi, -, M, Cotton 30s\n09, Maya, -, L, Cotton 30s\n10, Ratna, -, XL, Cotton 30s\n11, Putri, -, 2XL, Cotton 30s",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice5,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId5,
            'team_name' => 'Komunitas Hijabers',
            'nama_artikel' => 'Kaos Komunitas 2026',
            'nama_pemesan' => 'Fitri Handayani',
            'primary_color' => '#8b5cf6',
            'secondary_color' => '#f5f5f5',
            'material' => 'Cotton 30s',
            'collar_style' => 'O-Neck',
            'priority' => 'normal',
            'additional_notes' => "Tipe Kaos/Polo: Cotton 30s\n=== Detail Pesanan ===\n07, Rina, -, S, Cotton 30s\n08, Dewi, -, M, Cotton 30s\n09, Maya, -, L, Cotton 30s\n10, Ratna, -, XL, Cotton 30s\n11, Putri, -, 2XL, Cotton 30s",
            'customizations' => json_encode([
                'tipe_kaos' => 'Cotton 30s',
            ]),
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        DB::table('order_items')->insertGetId([
            'order_id' => $orderId5,
            'size' => 'Mix',
            'qty' => $totalQty5,
            'price_per_item' => $totalPrice5 / $totalQty5,
            'subtotal' => $totalPrice5,
            'created_at' => $now->copy()->subDays(2),
            'updated_at' => $now->copy()->subDays(2),
        ]);

        foreach ($items5 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId5,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId5, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(2)],
            ['order_id' => $orderId5, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(1), 'updated_at' => $now->copy()->subDays(1)],
            ['order_id' => $orderId5, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subHours(20), 'updated_at' => $now->copy()->subHours(20)],
            ['order_id' => $orderId5, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(1), 'updated_at' => $now->copy()->subDays(1)],
        ]);

        // ═══════════════════════════════════════════════════════════════
        // ORDER 6: Aksesoris Scarf — status: siap_cetak
        // Tests depends_on attributes (Scarf → ukuran_scarf, bahan_scarf)
        // ═══════════════════════════════════════════════════════════════
        $items6 = [
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Scarf', 'ukuran_scarf' => '75 x 75', 'bahan_scarf' => 'Micro Poly'], 'price' => 45000],
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Scarf', 'ukuran_scarf' => '75 x 75', 'bahan_scarf' => 'Micro Poly'], 'price' => 45000],
            ['size' => '-', 'no' => null, 'nama' => null, 'customizations' => ['tipe_aksesoris' => 'Scarf', 'ukuran_scarf' => '55 x 55', 'bahan_scarf' => 'Micro Poly'], 'price' => 25000],
        ];
        $totalQty6 = count($items6);
        $totalPrice6 = array_sum(array_map(fn($i) => $i['price'], $items6));

        $orderId6 = DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_number' => 'NVS-20260723-007',
            'status' => 'siap_cetak',
            'notes' => "Tipe Aksesoris: Scarf\nUkuran: 75 x 75\nBahan: Micro Poly\n=== Detail Pesanan ===\n2 pcs 75x75, 1 pc 55x55",
            'admin_notes' => 'Prioritas: Normal (0)',
            'total_price' => $totalPrice6,
            'confirmed_at' => $now->copy()->subDays(7),
            'created_at' => $now->copy()->subDays(9),
            'updated_at' => $now->copy()->subDays(3),
        ]);

        DB::table('design_requests')->insert([
            'order_id' => $orderId6,
            'team_name' => 'Sekolah Islam',
            'nama_artikel' => 'Scarf Logo Sekolah',
            'nama_pemesan' => 'Ustadzah Nurul',
            'primary_color' => '#006633',
            'secondary_color' => '#ffffff',
            'material' => 'Micro Poly',
            'collar_style' => null,
            'priority' => 'normal',
            'additional_notes' => "Tipe Aksesoris: Scarf\nUkuran: 75 x 75\nBahan: Micro Poly",
            'customizations' => json_encode([
                'tipe_aksesoris' => 'Scarf',
                'ukuran_scarf' => '75 x 75',
                'bahan_scarf' => 'Micro Poly',
            ]),
            'created_at' => $now->copy()->subDays(9),
            'updated_at' => $now->copy()->subDays(3),
        ]);

        DB::table('order_items')->insertGetId([
            'order_id' => $orderId6,
            'size' => '-',
            'qty' => $totalQty6,
            'price_per_item' => $totalPrice6 / $totalQty6,
            'subtotal' => $totalPrice6,
            'created_at' => $now->copy()->subDays(9),
            'updated_at' => $now->copy()->subDays(9),
        ]);

        foreach ($items6 as $item) {
            DB::table('order_item_details')->insert([
                'order_id' => $orderId6,
                'no_punggung' => $item['no'],
                'nama_punggung' => $item['nama'],
                'model_lengan' => null,
                'size' => $item['size'],
                'keterangan' => null,
                'customizations' => json_encode($item['customizations']),
                'price' => $item['price'],
                'created_at' => $now->copy()->subDays(9),
                'updated_at' => $now->copy()->subDays(9),
            ]);
        }

        DB::table('order_status_histories')->insert([
            ['order_id' => $orderId6, 'status' => 'menunggu_pembayaran', 'changed_by' => $customerId, 'notes' => 'Pesanan dibuat oleh customer', 'created_at' => $now->copy()->subDays(9), 'updated_at' => $now->copy()->subDays(9)],
            ['order_id' => $orderId6, 'status' => 'dikonfirmasi', 'changed_by' => $customerId, 'notes' => null, 'created_at' => $now->copy()->subDays(8), 'updated_at' => $now->copy()->subDays(8)],
            ['order_id' => $orderId6, 'status' => 'disetujui', 'changed_by' => $adminUserId, 'notes' => null, 'created_at' => $now->copy()->subDays(7), 'updated_at' => $now->copy()->subDays(7)],
            ['order_id' => $orderId6, 'status' => 'di_design', 'changed_by' => $adminUserId, 'notes' => 'Diteruskan ke tim design', 'created_at' => $now->copy()->subDays(6), 'updated_at' => $now->copy()->subDays(6)],
            ['order_id' => $orderId6, 'status' => 'siap_cetak', 'changed_by' => $designUserId, 'notes' => 'Design scarf selesai', 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
        ]);

        DB::table('payments')->insert([
            'order_id' => $orderId6,
            'amount' => $totalPrice6,
            'dp_amount' => $totalPrice6,
            'status' => 'success',
            'payment_method' => 'Transfer BCA',
            'paid_at' => $now->copy()->subDays(7),
            'created_at' => $now->copy()->subDays(7),
            'updated_at' => $now->copy()->subDays(7),
        ]);

        // ═══════════════════════════════════════════════════════════════
        // Summary
        // ═══════════════════════════════════════════════════════════════
        $this->command->info("✓ 6 pesanan test berhasil dibuat:");
        $this->command->table(
            ['#', 'Order Number', 'Kategori', 'Status', 'Qty', 'Total'],
            [
                ['1', 'NVS-20260723-002', 'Jersey Futsal', 'di_design', $totalQty1, 'Rp ' . number_format($totalPrice1, 0, ',', '.')],
                ['2', 'NVS-20260723-003', 'Jaket Training', 'siap_cetak', $totalQty2, 'Rp ' . number_format($totalPrice2, 0, ',', '.')],
                ['3', 'NVS-20260723-004', 'Kemeja PDL Tactical', 'diproduksi', $totalQty3, 'Rp ' . number_format($totalPrice3, 0, ',', '.')],
                ['4', 'NVS-20260723-005', 'Aksesoris Lanyard', 'diproduksi', $totalQty4, 'Rp ' . number_format($totalPrice4, 0, ',', '.')],
                ['5', 'NVS-20260723-006', 'Kaos Cotton 30s', 'di_design', $totalQty5, 'Rp ' . number_format($totalPrice5, 0, ',', '.')],
                ['6', 'NVS-20260723-007', 'Aksesoris Scarf', 'siap_cetak', $totalQty6, 'Rp ' . number_format($totalPrice6, 0, ',', '.')],
            ]
        );
    }
}
