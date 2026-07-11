<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class CustomCategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jersey Schema Options (from Settings if set)
        $collarOptions = json_decode(Setting::get('jersey_collar_options', json_encode([
            "O-NECK V.1", "O-NECK V.2", "O-NECK V.3", "O-NECK V.4", "O-NECK V.5",
            "V-NECK V.1", "V-NECK V.2", "V-NECK V.3", "V-NECK V.4", "V-NECK V.5",
            "CLASSIC V.1", "CLASSIC V.2", "CLASSIC V.3", "CLASSIC V.4", "CLASSIC V.5",
            "V-NECK V3 TUMPUK", "TIMNAS",
        ])), true) ?? [];

        $bahanOptions = json_decode(Setting::get('jersey_bahan_options', json_encode([
            "BINTIK JARUM GRADE B", "MILANO GRADE B", "BINTIK JARUM PREMIUM", "MILANO PREMIUM",
            "RABBIT", "DROPPEDDLE", "SMASH", "WAFFLE", "EMBOSH", "MICROCOOL",
            "JAQUARD AERO", "COTTON 24S", "COTTON 30S", "LOTTO", "PARASUT",
            "PUMA", "ULTRALIGHT A", "ULTRALIGHT B",
        ])), true) ?? [];

        $potonganOptions = json_decode(Setting::get('jersey_potongan_options', json_encode([
            "REGULER", "SLIMFIT CEWE", "OVERSIZE", "TUNIK", "SLIM FIT UNISEX", "BOXY CUT", "KIDS",
        ])), true) ?? [];

        $lenganOptions = json_decode(Setting::get('jersey_lengan_options', json_encode([
            "REGULER OVERDECK", "REGULER PAKAI MANSET",
            "RAGLAN A OVERDECK", "RAGLAN A PAKAI MANSET",
            "RAGLAN B OVERDECK", "RAGLAN B PAKAI MANSET",
        ])), true) ?? [];

        $jerseySchema = [
            [
                'id'              => 'kerah',
                'name'            => 'Jenis Kerah',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => Setting::get('jersey_collar_image', 'images/jersey_collar_guide.png'),
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $collarOptions)),
            ],
            [
                'id'              => 'bahan',
                'name'            => 'Bahan Jersey',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => Setting::get('jersey_bahan_image', 'images/Bahan Jersey.png'),
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $bahanOptions)),
            ],
            [
                'id'              => 'jenis_potongan',
                'name'            => 'Jenis Potongan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => Setting::get('jersey_potongan_image', 'images/Jenis Potongan.png'),
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $potonganOptions)),
            ],
            [
                'id'              => 'lengan_jahitan',
                'name'            => 'Model Lengan & Jahitan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => Setting::get('jersey_lengan_image', 'images/Model Lengan & Jahitan.png'),
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $lenganOptions)),
            ],
        ];

        // 2. Bawahan Schema
        $bawahanBahanOptions = ["LOTTO", "PARASUT", "DIADORA", "MILANO PREMIUM", "MICROCOOL"];
        $bawahanModelOptions = ["Celana Pendek", "Celana Panjang / Training", "Rok Olahraga"];
        $bawahanSchema = [
            [
                'id'              => 'model_bawahan',
                'name'            => 'Model Bawahan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => null,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $bawahanModelOptions)),
            ],
            [
                'id'              => 'bahan_bawahan',
                'name'            => 'Bahan Bawahan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => null,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $bawahanBahanOptions)),
            ],
        ];

        // 3. Jaket Schema
        $jaketBahanOptions = ["DIADORA", "FLEECE", "MICRO", "PARASUT PUMA"];
        $jaketModelOptions = ["Hoodie Pullover", "Hoodie Zipper", "Jaket Varsity", "Jaket Track"];
        $jaketSchema = [
            [
                'id'              => 'model_jaket',
                'name'            => 'Model Jaket',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => null,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $jaketModelOptions)),
            ],
            [
                'id'              => 'bahan_jaket',
                'name'            => 'Bahan Jaket',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => null,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $jaketBahanOptions)),
            ],
        ];

        // Seed / Update Kategori Custom
        Category::updateOrCreate(
            ['name' => 'Jersey'],
            ['attributes_schema' => $jerseySchema]
        );

        Category::updateOrCreate(
            ['name' => 'Bawahan'],
            ['attributes_schema' => $bawahanSchema]
        );

        Category::updateOrCreate(
            ['name' => 'Jaket'],
            ['attributes_schema' => $jaketSchema]
        );
    }
}
