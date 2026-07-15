<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // JSON berisi nilai default atribut untuk ditampilkan di halaman katalog produk.
            // Contoh: {"kerah": "O-NECK V.1", "bahan": "MILANO PREMIUM"}
            $table->json('product_attributes')->nullable()->after('theme_color');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_attributes');
        });
    }
};
