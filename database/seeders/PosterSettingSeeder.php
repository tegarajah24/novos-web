<?php

namespace Database\Seeders;

use App\Models\PosterSetting;
use Illuminate\Database\Seeder;

class PosterSettingSeeder extends Seeder
{
    public function run(): void
    {
        PosterSetting::setRotation('daily');
    }
}
