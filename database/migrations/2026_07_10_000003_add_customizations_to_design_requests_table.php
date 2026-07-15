<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            // JSON berisi pilihan atribut customer saat memesan.
            // Menggantikan kolom hardcode (collar_style, material, jenis_potongan, lengan_jahitan)
            // secara bertahap. Kolom lama dibiarkan nullable untuk data historis.
            // Contoh: {"kerah": "O-NECK V.1", "bahan": "MILANO PREMIUM", "jenis_potongan": "REGULER"}
            $table->json('customizations')->nullable()->after('additional_notes');
        });
    }

    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropColumn('customizations');
        });
    }
};
