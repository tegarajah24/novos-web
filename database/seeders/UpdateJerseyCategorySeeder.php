<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateJerseyCategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pindah produk dari "Running" ke "Jersey"
        $running = Category::where('name', 'Running')->first();
        $jersey  = Category::where('name', 'Jersey')->first();

        if ($running && $jersey) {
            $moved = Product::where('category_id', $running->id)->update(['category_id' => $jersey->id]);
            $this->command->info("Memindahkan {$moved} produk dari Running ke Jersey.");

            // Hapus kategori Running jika sudah kosong
            if (Product::where('category_id', $running->id)->count() === 0) {
                $running->delete();
                $this->command->info("Kategori Running dihapus (sudah kosong).");
            }
        } elseif ($jersey) {
            $this->command->warn("Kategori Running tidak ditemukan, skip pemindahan produk.");
        }

        // 2. Set base_price Jersey
        if ($jersey) {
            $jersey->update(['base_price' => 85000]);
            $this->command->info("Base price Jersey: Rp85.000");
        }

        // 3. Update attributes_schema Jersey dengan price_modifier
        $jerseySchema = [
            [
                'id'              => 'kerah',
                'name'            => 'Jenis Kerah',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => 'images/jersey_collar_guide.png',
                'options'         => [
                    ['value' => 'O-NECK V.1',          'price_modifier' => 0],
                    ['value' => 'O-NECK V.2',          'price_modifier' => 0],
                    ['value' => 'O-NECK V.3',          'price_modifier' => 0],
                    ['value' => 'O-NECK V.4',          'price_modifier' => 0],
                    ['value' => 'O-NECK V.5',          'price_modifier' => 0],
                    ['value' => 'V-NECK V.1',          'price_modifier' => 5000],
                    ['value' => 'V-NECK V.2',          'price_modifier' => 5000],
                    ['value' => 'V-NECK V.3',          'price_modifier' => 5000],
                    ['value' => 'V-NECK V.4',          'price_modifier' => 5000],
                    ['value' => 'V-NECK V.5',          'price_modifier' => 5000],
                    ['value' => 'CLASSIC V.1',         'price_modifier' => 0],
                    ['value' => 'CLASSIC V.2',         'price_modifier' => 0],
                    ['value' => 'CLASSIC V.3',         'price_modifier' => 0],
                    ['value' => 'CLASSIC V.4',         'price_modifier' => 0],
                    ['value' => 'CLASSIC V.5',         'price_modifier' => 0],
                    ['value' => 'V-NECK V3 TUMPUK',    'price_modifier' => 10000],
                    ['value' => 'TIMNAS',              'price_modifier' => 10000],
                ],
            ],
            [
                'id'              => 'bahan',
                'name'            => 'Bahan Jersey',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => 'images/Bahan Jersey.png',
                'options'         => [
                    ['value' => 'BINTIK JARUM GRADE B',  'price_modifier' => 0],
                    ['value' => 'MILANO GRADE B',         'price_modifier' => 0],
                    ['value' => 'BINTIK JARUM PREMIUM',   'price_modifier' => 10000],
                    ['value' => 'MILANO PREMIUM',          'price_modifier' => 10000],
                    ['value' => 'RABBIT',                  'price_modifier' => 25000],
                    ['value' => 'DROPPEDDLE',              'price_modifier' => 20000],
                    ['value' => 'SMASH',                   'price_modifier' => 15000],
                    ['value' => 'WAFFLE',                  'price_modifier' => 10000],
                    ['value' => 'EMBOSH',                  'price_modifier' => 10000],
                    ['value' => 'MICROCOOL',               'price_modifier' => 10000],
                    ['value' => 'JAQUARD AERO',            'price_modifier' => 20000],
                    ['value' => 'COTTON 24S',              'price_modifier' => 5000],
                    ['value' => 'COTTON 30S',              'price_modifier' => 5000],
                    ['value' => 'LOTTO',                   'price_modifier' => 5000],
                    ['value' => 'PARASUT',                 'price_modifier' => 0],
                    ['value' => 'PUMA',                    'price_modifier' => 15000],
                    ['value' => 'ULTRALIGHT A',            'price_modifier' => 15000],
                    ['value' => 'ULTRALIGHT B',            'price_modifier' => 15000],
                ],
            ],
            [
                'id'              => 'jenis_potongan',
                'name'            => 'Jenis Potongan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => 'images/Jenis Potongan.png',
                'options'         => [
                    ['value' => 'REGULER',           'price_modifier' => 0],
                    ['value' => 'SLIMFIT CEWE',      'price_modifier' => 0],
                    ['value' => 'OVERSIZE',          'price_modifier' => 5000],
                    ['value' => 'TUNIK',             'price_modifier' => 5000],
                    ['value' => 'SLIM FIT UNISEX',   'price_modifier' => 0],
                    ['value' => 'BOXY CUT',          'price_modifier' => 5000],
                    ['value' => 'KIDS',              'price_modifier' => -5000],
                ],
            ],
            [
                'id'              => 'lengan_jahitan',
                'name'            => 'Model Lengan & Jahitan',
                'type'            => 'select',
                'required'        => true,
                'reference_image' => 'images/Model Lengan & Jahitan.png',
                'options'         => [
                    ['value' => 'REGULER OVERDECK',         'price_modifier' => 0],
                    ['value' => 'REGULER PAKAI MANSET',     'price_modifier' => 5000],
                    ['value' => 'RAGLAN A OVERDECK',        'price_modifier' => 10000],
                    ['value' => 'RAGLAN A PAKAI MANSET',    'price_modifier' => 15000],
                    ['value' => 'RAGLAN B OVERDECK',        'price_modifier' => 10000],
                    ['value' => 'RAGLAN B PAKAI MANSET',    'price_modifier' => 15000],
                ],
            ],
        ];

        if ($jersey) {
            $jersey->update(['attributes_schema' => $jerseySchema]);
            $this->command->info("Attributes schema Jersey berhasil diupdate dengan price_modifier.");
        }
    }
}
