<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class RealProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = $this->getProducts();

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();

            if (!$category) {
                $this->command->warn("Kategori '{$data['category']}' tidak ditemukan, dilewati.");
                continue;
            }

            Product::create([
                'category_id'       => $category->id,
                'name'              => $data['name'],
                'description'       => $data['description'],
                'price'             => $data['price'],
                'min_qty'           => $data['min_qty'] ?? 1,
                'production_days'   => $data['production_days'] ?? 7,
                'is_active'         => true,
                'theme_color'       => $data['theme_color'] ?? null,
                'product_attributes' => $data['product_attributes'] ?? null,
            ]);

            $this->command->info("Created: {$data['name']} ({$data['category']})");
        }

        $this->command->info("Selesai! " . count($products) . " produk ditambahkan.");
    }

    private function getProducts(): array
    {
        return [
            // ──────────────────────────────────────────
            // JERSEY - FUTSAL
            // ──────────────────────────────────────────
            [
                'category'    => 'Futsal',
                'name'        => 'Jersey Futsal Novos Pro',
                'description' => 'Jersey futsal premium dengan bahan Microcool yang adem dan ringan. Cocok untuk pertandingan dan latihan rutin.',
                'price'       => 85000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => '#1E40AF',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.1',
                    'bahan'           => 'MICROCOOL',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],
            [
                'category'    => 'Futsal',
                'name'        => 'Jersey Futsal Raglan Elite',
                'description' => 'Desain raglan modern dengan bahan Milano Premium. Nyaman dipakai berjam-jam di lapangan.',
                'price'       => 95000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => '#DC2626',
                'product_attributes' => [
                    'kerah'           => 'V-NECK V.1',
                    'bahan'           => 'MILANO PREMIUM',
                    'jenis_potongan'  => 'SLIMFIT CEWE',
                    'lengan_jahitan'  => 'RAGLAN A OVERDECK',
                ],
            ],
            [
                'category'    => 'Futsal',
                'name'        => 'Jersey Futsal Volta Edition',
                'description' => 'Jersey edisi khusus pertandingan futsal. Bahan Rabbit stretch untuk mobilitas maksimal.',
                'price'       => 110000,
                'min_qty'     => 10,
                'production_days' => 10,
                'theme_color' => '#059669',
                'product_attributes' => [
                    'kerah'           => 'CLASSIC V.1',
                    'bahan'           => 'RABBIT',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'RAGLAN A PAKAI MANSET',
                ],
            ],
            [
                'category'    => 'Futsal',
                'name'        => 'Jersey Futsal Kids',
                'description' => 'Jersey futsal anak ukuran KIDS. Bahan ringan dan aman untuk kulit sensitif.',
                'price'       => 65000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => '#F59E0B',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.2',
                    'bahan'           => 'BINTIK JARUM GRADE B',
                    'jenis_potongan'  => 'KIDS',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - SEPEDA
            // ──────────────────────────────────────────
            [
                'category'    => 'Sepeda',
                'name'        => 'Jersey Sepeda Cycling Pro',
                'description' => 'Jersey sepeda full zipper dengan bahan Ultralight. Ventilasi udara optimal untuk long ride.',
                'price'       => 120000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#7C3AED',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.3',
                    'bahan'           => 'ULTRALIGHT A',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'REGULER PAKAI MANSET',
                ],
            ],
            [
                'category'    => 'Sepeda',
                'name'        => 'Jersey Sepeda MTB Trail',
                'description' => 'Jersey off-road untuk mountain biking. Bahan Droppedle tahan aus dengan potongan longgar.',
                'price'       => 105000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#16A34A',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.1',
                    'bahan'           => 'DROPPEDDLE',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'RAGLAN B OVERDECK',
                ],
            ],
            [
                'category'    => 'Sepeda',
                'name'        => 'Jersey Sepeda Road Elite',
                'description' => 'Jersey balap sepeda aerodinamis. Bahan Puma stretch fit untuk performa kompetitif.',
                'price'       => 135000,
                'min_qty'     => 6,
                'production_days' => 12,
                'theme_color' => '#0EA5E9',
                'product_attributes' => [
                    'kerah'           => 'V-NECK V.2',
                    'bahan'           => 'PUMA',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'RAGLAN A PAKAI MANSET',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - RENANG
            // ──────────────────────────────────────────
            [
                'category'    => 'Renang',
                'name'        => 'Jersey Renang Rashguard',
                'description' => 'Rashguard untuk aktivitas air. Bahan Parasut quick-dry dengan proteksi UV.',
                'price'       => 95000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#0284C7',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.4',
                    'bahan'           => 'PARASUT',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - KOMUNITAS
            // ──────────────────────────────────────────
            [
                'category'    => 'Komunitas',
                'name'        => 'Jersey Komunitas Heritage',
                'description' => 'Jersey untuk komunitas dan klub dengan desain klasik. Bahan Cotton 24s yang nyaman sehari-hari.',
                'price'       => 75000,
                'min_qty'     => 15,
                'production_days' => 7,
                'theme_color' => '#B45309',
                'product_attributes' => [
                    'kerah'           => 'CLASSIC V.2',
                    'bahan'           => 'COTTON 24S',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],
            [
                'category'    => 'Komunitas',
                'name'        => 'Jersey Komunitas Urban',
                'description' => 'Desain kekinian untuk komunitas urban. Potongan oversized yang trendy.',
                'price'       => 80000,
                'min_qty'     => 15,
                'production_days' => 7,
                'theme_color' => '#6D28D9',
                'product_attributes' => [
                    'kerah'           => 'V-NECK V3 TUMPUK',
                    'bahan'           => 'COTTON 30S',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - BASEBALL
            // ──────────────────────────────────────────
            [
                'category'    => 'Baseball',
                'name'        => 'Jersey Baseball Classic',
                'description' => 'Jersey baseball bergaya Amerika. Potongan longgar dengan kancing depan.',
                'price'       => 90000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#1E3A5F',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.5',
                    'bahan'           => 'MILANO GRADE B',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'RAGLAN B PAKAI MANSET',
                ],
            ],
            [
                'category'    => 'Baseball',
                'name'        => 'Jersey Baseball Premium',
                'description' => 'Jersey baseball premium bahan Embosh. Cocok untuk turnamen dan koleksi.',
                'price'       => 115000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#991B1B',
                'product_attributes' => [
                    'kerah'           => 'TIMNAS',
                    'bahan'           => 'EMBOSH',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'RAGLAN A PAKAI MANSET',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - PADEL
            // ──────────────────────────────────────────
            [
                'category'    => 'Padel',
                'name'        => 'Jersey Padel Match',
                'description' => 'Jersey padel performance. Bahan Waffle untuk sirkulasi udara maksimal.',
                'price'       => 90000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#0D9488',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.1',
                    'bahan'           => 'WAFFLE',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'RAGLAN A OVERDECK',
                ],
            ],
            [
                'category'    => 'Padel',
                'name'        => 'Jersey Padel Tour',
                'description' => 'Jersey turnamen padel. Desain eksklusif dengan bahan Jaquard Aero.',
                'price'       => 125000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#E11D48',
                'product_attributes' => [
                    'kerah'           => 'V-NECK V.3',
                    'bahan'           => 'JAQUARD AERO',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'RAGLAN B PAKAI MANSET',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - BADMINTON
            // ──────────────────────────────────────────
            [
                'category'    => 'Badminton',
                'name'        => 'Jersey Badminton Smash',
                'description' => 'Jersey bulu tangkis bahan Smash. Ringan dan fleksibel untuk smash keras.',
                'price'       => 85000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#EA580C',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.2',
                    'bahan'           => 'SMASH',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'REGULER PAKAI MANSET',
                ],
            ],
            [
                'category'    => 'Badminton',
                'name'        => 'Jersey Badminton Pro League',
                'description' => 'Jersey kualitas liga profesional. Bahan Milano Premium dengan potongan slim fit.',
                'price'       => 100000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#F97316',
                'product_attributes' => [
                    'kerah'           => 'CLASSIC V.3',
                    'bahan'           => 'MILANO PREMIUM',
                    'jenis_potongan'  => 'SLIMFIT CEWE',
                    'lengan_jahitan'  => 'RAGLAN A OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - TENNIS
            // ──────────────────────────────────────────
            [
                'category'    => 'Tennis',
                'name'        => 'Jersey Tennis Court',
                'description' => 'Jersey tenis elegan. Bahan Microcool dengan desain clean dan sporty.',
                'price'       => 90000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#84CC16',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.3',
                    'bahan'           => 'MICROCOOL',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],
            [
                'category'    => 'Tennis',
                'name'        => 'Jersey Tennis Grand Slam',
                'description' => 'Jersey tenis premium. Bahan Rabbit untuk kenyamanan ekstra di pertandingan panjang.',
                'price'       => 120000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#FACC15',
                'product_attributes' => [
                    'kerah'           => 'V-NECK V.4',
                    'bahan'           => 'RABBIT',
                    'jenis_potongan'  => 'BOXY CUT',
                    'lengan_jahitan'  => 'RAGLAN B OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - BASKET
            // ──────────────────────────────────────────
            [
                'category'    => 'Basket',
                'name'        => 'Jersey Basketball Street',
                'description' => 'Jersey basket street style. Bahan Bintik Jarum Premium, adem dan breathable.',
                'price'       => 95000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#7C2D12',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.4',
                    'bahan'           => 'BINTIK JARUM PREMIUM',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],
            [
                'category'    => 'Basket',
                'name'        => 'Jersey Basketball Pro Team',
                'description' => 'Jersey basket tim profesional. Potongan longgar dengan bahan Lotus untuk performa optimal.',
                'price'       => 110000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#1D4ED8',
                'product_attributes' => [
                    'kerah'           => 'TIMNAS',
                    'bahan'           => 'LOTTO',
                    'jenis_potongan'  => 'OVERSIZE',
                    'lengan_jahitan'  => 'RAGLAN A PAKAI MANSET',
                ],
            ],
            [
                'category'    => 'Basket',
                'name'        => 'Jersey Basketball Kids',
                'description' => 'Jersey basket anak. Bahan ringan dan nyaman untuk aktivitas di lapangan.',
                'price'       => 70000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#DC2626',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.1',
                    'bahan'           => 'BINTIK JARUM GRADE B',
                    'jenis_potongan'  => 'KIDS',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],

            // ──────────────────────────────────────────
            // JERSEY - RUNNING
            // ──────────────────────────────────────────
            [
                'category'    => 'Running',
                'name'        => 'Jersey Running Ultralight',
                'description' => 'Jersey lari ultra ringan. Bahan Ultralight B untuk marathon dan fun run.',
                'price'       => 105000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#06B6D4',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.5',
                    'bahan'           => 'ULTRALIGHT B',
                    'jenis_potongan'  => 'SLIM FIT UNISEX',
                    'lengan_jahitan'  => 'REGULER OVERDECK',
                ],
            ],
            [
                'category'    => 'Running',
                'name'        => 'Jersey Running Trail',
                'description' => 'Jersey trail running. Bahan Microcool dengan ventilasi mesh untuk udara panas.',
                'price'       => 95000,
                'min_qty'     => 6,
                'production_days' => 7,
                'theme_color' => '#22C55E',
                'product_attributes' => [
                    'kerah'           => 'O-NECK V.1',
                    'bahan'           => 'MICROCOOL',
                    'jenis_potongan'  => 'REGULER',
                    'lengan_jahitan'  => 'REGULER PAKAI MANSET',
                ],
            ],

            // ──────────────────────────────────────────
            // JAKET - TRAINING
            // ──────────────────────────────────────────
            [
                'category'    => 'Training',
                'name'        => 'Jaket Training Diadora',
                'description' => 'Jaket training bahan Diadora. Cocok untuk pemanasan dan aktivitas outdoor.',
                'price'       => 150000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#1E293B',
                'product_attributes' => [],
            ],
            [
                'category'    => 'Training',
                'name'        => 'Jaket Training Fleece',
                'description' => 'Jaket training hangat bahan Fleece. Nyaman untuk latihan pagi dan malam.',
                'price'       => 175000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#334155',
                'product_attributes' => [],
            ],
            [
                'category'    => 'Training',
                'name'        => 'Hoodie Training Micro',
                'description' => 'Hoodie training bahan Micro. Ringan, adem, dan cocok untuk semua cuaca.',
                'price'       => 165000,
                'min_qty'     => 6,
                'production_days' => 10,
                'theme_color' => '#475569',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // JAKET - KASUAL & KOMUNITAS
            // ──────────────────────────────────────────
            [
                'category'    => 'Kasual & Komunitas',
                'name'        => 'Hoodie Komunitas Varsity',
                'description' => 'Jaket varsity untuk komunitas. Desain klasik dengan bahan Fleece premium.',
                'price'       => 195000,
                'min_qty'     => 6,
                'production_days' => 12,
                'theme_color' => '#1E3A5F',
                'product_attributes' => [],
            ],
            [
                'category'    => 'Kasual & Komunitas',
                'name'        => 'Jaket Track Komunitas',
                'description' => 'Jaket track untuk komunitas. Bahan Parasut PUMA anti air.',
                'price'       => 185000,
                'min_qty'     => 6,
                'production_days' => 12,
                'theme_color' => '#374151',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // BAWAHAN - TRAINING PENDEK
            // ──────────────────────────────────────────
            [
                'category'    => 'Training Pendek',
                'name'        => 'Celana Training Pendek Lotto',
                'description' => 'Celana training pendek bahan Lotto. Elastis dan nyaman untuk berolahraga.',
                'price'       => 55000,
                'min_qty'     => 10,
                'production_days' => 5,
                'theme_color' => '#374151',
                'product_attributes' => [],
            ],
            [
                'category'    => 'Training Pendek',
                'name'        => 'Celana Training Pendek Diadora',
                'description' => 'Celana training pendek bahan Diadora. Ringan dengan saku samping.',
                'price'       => 60000,
                'min_qty'     => 10,
                'production_days' => 5,
                'theme_color' => '#1F2937',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // BAWAHAN - TRAINING PANJANG
            // ──────────────────────────────────────────
            [
                'category'    => 'Training Panjang',
                'name'        => 'Celana Training Panjang Parasut',
                'description' => 'Celana training panjang bahan Parasut. Cocok untuk warm-up dan outdoor.',
                'price'       => 75000,
                'min_qty'     => 10,
                'production_days' => 5,
                'theme_color' => '#111827',
                'product_attributes' => [],
            ],
            [
                'category'    => 'Training Panjang',
                'name'        => 'Celana Training Panjang Milano',
                'description' => 'Celana training panjang bahan Milano Premium. Nyaman dan elegan.',
                'price'       => 85000,
                'min_qty'     => 10,
                'production_days' => 5,
                'theme_color' => '#030712',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // BAWAHAN - SKORT
            // ──────────────────────────────────────────
            [
                'category'    => 'Skort',
                'name'        => 'Skort Olahraga Wanita',
                'description' => 'Skort (rok + celana dalam) untuk olahraga. Bahan Lotto dengan desain sporty.',
                'price'       => 65000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => '#DB2777',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KEMEJA & PAKAIAN DINAS - PDL REGULER
            // ──────────────────────────────────────────
            [
                'category'    => 'PDL Reguler',
                'name'        => 'Kemeja PDL Reguler',
                'description' => 'Kemeja Pakaian Dinas Lapangan reguler. Bahan Lotto kokoh dan tahan lama.',
                'price'       => 95000,
                'min_qty'     => 10,
                'production_days' => 10,
                'theme_color' => '#166534',
                'product_attributes' => [],
            ],
            [
                'category'    => 'PDL Reguler',
                'name'        => 'Kemeja PDL Ripstop',
                'description' => 'Kemeja PDL bahan ripstop. Anti sobek, cocok untuk dinas lapangan.',
                'price'       => 110000,
                'min_qty'     => 10,
                'production_days' => 10,
                'theme_color' => '#14532D',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KEMEJA & PAKAIAN DINAS - PDL TACTICAL
            // ──────────────────────────────────────────
            [
                'category'    => 'PDL Tactical',
                'name'        => 'Kemeja Tactical PDL',
                'description' => 'Kemeja tactical untuk dinas khusus. Bahan Diadora dengan banyak kantong.',
                'price'       => 135000,
                'min_qty'     => 10,
                'production_days' => 12,
                'theme_color' => '#3F3F46',
                'product_attributes' => [],
            ],
            [
                'category'    => 'PDL Tactical',
                'name'        => 'Kemeja Tactical Ultralight',
                'description' => 'Kemeja tactical ringan. Bahan Ultralight untuk kenyamanan maksimal.',
                'price'       => 150000,
                'min_qty'     => 10,
                'production_days' => 12,
                'theme_color' => '#52525B',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KEMEJA & PAKAIAN DINAS - PDH & WORKSHIRT
            // ──────────────────────────────────────────
            [
                'category'    => 'PDH & Workshirt',
                'name'        => 'Kemeja PDH Kantor',
                'description' => 'Kemeja Pakaian Dinas Harian untuk kantor. Bahan Cotton 30s yang adem.',
                'price'       => 85000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => '#1E3A5F',
                'product_attributes' => [],
            ],
            [
                'category'    => 'PDH & Workshirt',
                'name'        => 'Workshirt Custom Logo',
                'description' => 'Kemeja kerja custom dengan bordir logo. Cocok untuk seragam perusahaan.',
                'price'       => 95000,
                'min_qty'     => 10,
                'production_days' => 10,
                'theme_color' => '#1E40AF',
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - COTTON 16S
            // ──────────────────────────────────────────
            [
                'category'    => 'Cotton 16s',
                'name'        => 'Kaos Cotton 16s Reguler',
                'description' => 'Kaos katun tebal 16s. Cocok untuk sablon dan bordir.',
                'price'       => 35000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Cotton 16s',
                'name'        => 'Kaos Cotton 16s Oversize',
                'description' => 'Kaos katun 16s potongan oversize. Trendy dan nyaman.',
                'price'       => 40000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - COTTON 20S
            // ──────────────────────────────────────────
            [
                'category'    => 'Cotton 20s',
                'name'        => 'Kaos Cotton 20s Reguler',
                'description' => 'Kaos katun standar 20s. Nyaman untuk dipakai sehari-hari.',
                'price'       => 30000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - COTTON 24S
            // ──────────────────────────────────────────
            [
                'category'    => 'Cotton 24s',
                'name'        => 'Kaos Cotton 24s Premium',
                'description' => 'Kaos katun halus 24s. Cocok untuk distro dan brand clothing.',
                'price'       => 35000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Cotton 24s',
                'name'        => 'Kaos Cotton 24s Combed',
                'description' => 'Kaos combed 24s kualitas ekspor. Lembut dan tidak berbulu.',
                'price'       => 40000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - COTTON 30S
            // ──────────────────────────────────────────
            [
                'category'    => 'Cotton 30s',
                'name'        => 'Kaos Cotton 30s Slim',
                'description' => 'Kaos katun tipis 30s. Ringan dan adem untuk cuaca panas.',
                'price'       => 30000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - POLO COTTON
            // ──────────────────────────────────────────
            [
                'category'    => 'Polo Cotton',
                'name'        => 'Polo Shirt Cotton Reguler',
                'description' => 'Kemeja polo katun. Cocok untuk semi formal dan seragam kantor.',
                'price'       => 55000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Polo Cotton',
                'name'        => 'Polo Shirt Cotton Premium',
                'description' => 'Polo katun premium dengan bordir. Kualitas jahitan rapi.',
                'price'       => 70000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // KAOS & POLO - POLO GVC
            // ──────────────────────────────────────────
            [
                'category'    => 'Polo GVC',
                'name'        => 'Polo GVC Classic',
                'description' => 'Polo GVC klasik. Bahan pique premium, cocok untuk seragam perusahaan.',
                'price'       => 65000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Polo GVC',
                'name'        => 'Polo GVC Slim Fit',
                'description' => 'Polo GVC slim fit modern. Potongan pas di badan.',
                'price'       => 75000,
                'min_qty'     => 10,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // AKSESORIS - SCARF
            // ──────────────────────────────────────────
            [
                'category'    => 'Scarf',
                'name'        => 'Scarf Custom Logo',
                'description' => 'Scarf custom untuk komunitas dan club. Bahan halus dan warna cerah.',
                'price'       => 25000,
                'min_qty'     => 20,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // AKSESORIS - TOTEBAG
            // ──────────────────────────────────────────
            [
                'category'    => 'Totebag',
                'name'        => 'Totebag Custom Printing',
                'description' => 'Totebag custom untuk event dan promosi. Bahan canvas tebal.',
                'price'       => 20000,
                'min_qty'     => 30,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Totebag',
                'name'        => 'Totebag Spunbound',
                'description' => 'Totebag spunbound murah meriah. Cocok untuk goodie bag.',
                'price'       => 8000,
                'min_qty'     => 50,
                'production_days' => 3,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // AKSESORIS - LANYARD
            // ──────────────────────────────────────────
            [
                'category'    => 'Lanyard',
                'name'        => 'Lanyard Custom ID Card',
                'description' => 'Lanyard custom untuk ID card. Bahan satin dengan cetak tahan lama.',
                'price'       => 8000,
                'min_qty'     => 50,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Lanyard',
                'name'        => 'Lanyard Custom Event',
                'description' => 'Lanyard event custom full color. Cocok untuk seminar dan konferensi.',
                'price'       => 12000,
                'min_qty'     => 50,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // AKSESORIS - TOPI
            // ──────────────────────────────────────────
            [
                'category'    => 'Topi',
                'name'        => 'Topi Custom Bordir',
                'description' => 'Topi custom dengan bordir logo. Cocok untuk seragam dan merchandise.',
                'price'       => 25000,
                'min_qty'     => 20,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],
            [
                'category'    => 'Topi',
                'name'        => 'Topi Bucket Custom',
                'description' => 'Topi bucket custom. Trendi untuk event outdoor dan komunitas.',
                'price'       => 30000,
                'min_qty'     => 20,
                'production_days' => 7,
                'theme_color' => null,
                'product_attributes' => [],
            ],

            // ──────────────────────────────────────────
            // AKSESORIS - TAS SERUT
            // ──────────────────────────────────────────
            [
                'category'    => 'Tas Serut',
                'name'        => 'Tas Serut Custom',
                'description' => 'Tas serut custom untuk goodie bag. Bahan nylon ringan.',
                'price'       => 15000,
                'min_qty'     => 30,
                'production_days' => 5,
                'theme_color' => null,
                'product_attributes' => [],
            ],
        ];
    }
}
