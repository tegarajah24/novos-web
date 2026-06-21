<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan', 'pending') NOT NULL DEFAULT 'menunggu_validasi'");

        DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan', 'pending') NOT NULL");

        DB::statement("UPDATE orders SET status = 'menunggu_validasi' WHERE status = 'pending'");
        DB::statement("UPDATE order_status_histories SET status = 'menunggu_validasi' WHERE status = 'pending'");

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu_validasi'");

        DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan', 'pending') NOT NULL DEFAULT 'pending'");

        DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan', 'pending') NOT NULL");

        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'menunggu_validasi'");
        DB::statement("UPDATE order_status_histories SET status = 'pending' WHERE status = 'menunggu_validasi'");

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'pending'");

        DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('pending', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan') NOT NULL");
    }
};
