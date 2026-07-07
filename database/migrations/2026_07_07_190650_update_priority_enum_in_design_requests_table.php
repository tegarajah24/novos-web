<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE design_requests MODIFY COLUMN priority VARCHAR(50) NOT NULL DEFAULT 'normal'");

        DB::table('design_requests')
            ->where('priority', 'Normal')
            ->update(['priority' => 'normal']);

        DB::table('design_requests')
            ->where('priority', 'High')
            ->update(['priority' => 'express']);
    }

    public function down(): void
    {
        DB::table('design_requests')
            ->where('priority', 'express')
            ->update(['priority' => 'High']);

        DB::table('design_requests')
            ->where('priority', 'super_express')
            ->update(['priority' => 'High']);

        DB::table('design_requests')
            ->where('priority', 'normal')
            ->update(['priority' => 'Normal']);

        DB::statement("ALTER TABLE design_requests MODIFY COLUMN priority ENUM('Normal', 'High') NOT NULL DEFAULT 'Normal'");
    }
};
