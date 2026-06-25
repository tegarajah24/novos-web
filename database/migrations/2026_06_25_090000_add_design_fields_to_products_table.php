<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'kerah')) {
                $table->string('kerah')->nullable();
            }
            if (!Schema::hasColumn('products', 'bahan')) {
                $table->string('bahan')->nullable();
            }
            if (!Schema::hasColumn('products', 'jenis_potongan')) {
                $table->string('jenis_potongan')->nullable();
            }
            if (!Schema::hasColumn('products', 'lengan_jahitan')) {
                $table->string('lengan_jahitan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['kerah', 'bahan', 'jenis_potongan', 'lengan_jahitan']);
        });
    }
};
