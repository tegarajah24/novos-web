<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // JSON schema yang mendefinisikan atribut-atribut yang bisa dikustomisasi per kategori produk.
            // Contoh: Jersey punya kerah, bahan, jenis_potongan, lengan_jahitan.
            // Jaket punya tipe_jaket, tutup_kepala, dst.
            $table->json('attributes_schema')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('attributes_schema');
        });
    }
};
