<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE design_requests MODIFY COLUMN jenis_potongan VARCHAR(100) NULL");
        DB::statement("ALTER TABLE design_requests MODIFY COLUMN lengan_jahitan VARCHAR(100) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE design_requests MODIFY COLUMN jenis_potongan ENUM('REGULER','SLIMFIT CEWE','OVERSIZE','TUNIK','SLIM FIT UNISEX','BOXY CUT','KIDS') NULL");
        DB::statement("ALTER TABLE design_requests MODIFY COLUMN lengan_jahitan ENUM('REGULER OVERDECK','REGULER PAKAI MANSET','RAGLAN A OVERDECK','RAGLAN A PAKAI MANSET','RAGLAN B OVERDECK','RAGLAN B PAKAI MANSET') NULL");
    }
};
