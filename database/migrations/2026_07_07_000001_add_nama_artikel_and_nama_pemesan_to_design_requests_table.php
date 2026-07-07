<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->string('nama_artikel')->nullable()->after('team_name');
            $table->string('nama_pemesan')->nullable()->after('nama_artikel');
        });
    }

    public function down(): void
    {
        Schema::table('design_requests', function (Blueprint $table) {
            $table->dropColumn(['nama_artikel', 'nama_pemesan']);
        });
    }
};
