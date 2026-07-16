<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class JerseyAttributesSchemaSeeder extends Seeder
{
    /**
     * Seed attributes_schema ke semua kategori yang sudah ada di database,
     * menggunakan data yang sudah tersimpan di tabel Settings (jersey_*_options).
     *
     * Jalankan: php artisan db:seed --class=JerseyAttributesSchemaSeeder
     */
    public function run(): void
    {
        // Ambil opsi yang ada di Settings (jika sudah diatur admin), atau pakai default
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

        $collarImageSetting = Setting::get('jersey_collar_image', 'images/jersey_collar_guide.png');
        $bahanImageSetting  = Setting::get('jersey_bahan_image', 'images/Bahan Jersey.png');
        $potonganImageSetting = Setting::get('jersey_potongan_image', 'images/Jenis Potongan.png');
        $lenganImageSetting = Setting::get('jersey_lengan_image', 'images/Model Lengan & Jahitan.png');

        $jerseySchema = [
            [
                'id'              => 'kerah',
                'name'            => 'Jenis Kerah',
                'type'            => 'select',
                'required'        => true,
                'system_tag'      => 'is_collar_type',
                'reference_image' => $collarImageSetting,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $collarOptions)),
            ],
            [
                'id'              => 'bahan',
                'name'            => 'Bahan Jersey',
                'type'            => 'select',
                'required'        => true,
                'system_tag'      => 'is_fabric_type',
                'reference_image' => $bahanImageSetting,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $bahanOptions)),
            ],
            [
                'id'              => 'jenis_potongan',
                'name'            => 'Jenis Potongan',
                'type'            => 'select',
                'required'        => true,
                'system_tag'      => 'is_cut_type',
                'reference_image' => $potonganImageSetting,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $potonganOptions)),
            ],
            [
                'id'              => 'lengan_jahitan',
                'name'            => 'Model Lengan & Jahitan',
                'type'            => 'select',
                'required'        => true,
                'system_tag'      => 'is_sleeve_joint_type',
                'reference_image' => $lenganImageSetting,
                'options'         => array_values(array_map(fn($v) => ['value' => $v], $lenganOptions)),
            ],
            [
                'id'              => 'lengan',
                'name'            => 'Panjang/Pendek Lengan',
                'type'            => 'select',
                'required'        => true,
                'system_tag'      => 'is_sleeve_type',
                'reference_image' => null,
                'options'         => [
                    ['value' => 'Lengan Pendek', 'sleeve' => 'short'],
                    ['value' => 'Lengan Panjang', 'sleeve' => 'long'],
                ],
            ],
        ];

        // Temukan semua kategori yang namanya mengandung "jersey" (case-insensitive)
        // atau update semua kategori yang belum punya schema (bergantung kondisi DB)
        $categories = Category::all();

        $updated = 0;
        foreach ($categories as $category) {
            // Jika kategori sudah punya schema, skip (jangan timpa pengaturan manual)
            if (!empty($category->attributes_schema)) {
                $this->command->info("Kategori '{$category->name}' sudah punya schema — dilewati.");
                continue;
            }

            // Untuk kategori yang namanya mengandung jersey / kaos, assign jersey schema
            $isJersey = stripos($category->name, 'jersey') !== false
                || stripos($category->name, 'kaos') !== false
                || stripos($category->name, 'baju') !== false;

            if ($isJersey) {
                $category->update(['attributes_schema' => $jerseySchema]);
                $this->command->info("Kategori '{$category->name}' → Jersey schema di-assign.");
                $updated++;
            } else {
                $this->command->warn("Kategori '{$category->name}' → Tidak dikenali sebagai jersey, schema kosong. Isi manual via dashboard.");
            }
        }

        $this->command->info("Selesai. {$updated} kategori diupdate dengan Jersey schema.");
        $this->command->info("Untuk kategori lain (celana, jaket, dll.), isi schema-nya di Dashboard → Kelola Kategori → Kelola Atribut.");
    }
}
