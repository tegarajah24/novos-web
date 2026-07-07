<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $sql = file_get_contents(__DIR__ . '/wilayah_data.sql');
        DB::unprepared($sql);
    }
}
