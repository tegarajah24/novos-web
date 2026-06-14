<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected array $images = [
        'NF-001' => ['file' => 'novos-coklat-Photoroom.png',  'category' => 'Running'],
        'NF-002' => ['file' => 'novos2-Photoroom.png',        'category' => 'Running'],
        'NF-003' => ['file' => 'novos-merah-Photoroom.png',   'category' => 'Sepak Bola'],
        'NF-004' => ['file' => 'novos3-Photoroom.png',        'category' => 'Futsal'],
        'NF-005' => ['file' => 'novos4-Photoroom.png',        'category' => 'Sepak Bola'],
        'NF-006' => ['file' => 'novos5-Photoroom.png',        'category' => 'Tenis'],
        'NF-007' => ['file' => 'novos6-Photoroom.png',        'category' => 'Tenis'],
        'NF-008' => ['file' => 'novos-Photoroom.png',         'category' => 'Basket'],
        'NF-009' => ['file' => 'novos7-Photoroom.png',        'category' => 'Basket'],
        'NF-010' => ['file' => 'novos-hijau-Photoroom.png',  'category' => 'Gym'],
        'NF-011' => ['file' => 'novos8-Photoroom.png',        'category' => 'Training'],
    ];

    public function run(): void
    {
        $sourceDir = 'C:\Users\Ai\Downloads\folder produk';
        $targetDir = storage_path('app/public/products');

        $categoryCache = [];

        foreach ($this->images as $code => $data) {
            $sourceFile = $sourceDir . DIRECTORY_SEPARATOR . $data['file'];
            $ext = pathinfo($data['file'], PATHINFO_EXTENSION);
            $targetFile = $targetDir . DIRECTORY_SEPARATOR . strtolower($code) . '.' . $ext;

            if (!file_exists($sourceFile)) {
                $this->command->warn("Source file not found: {$sourceFile}");
                continue;
            }

            copy($sourceFile, $targetFile);

            if (!isset($categoryCache[$data['category']])) {
                $category = Category::firstOrCreate(['name' => $data['category']]);
                $categoryCache[$data['category']] = $category->id;
            }

            Product::create([
                'category_id' => $categoryCache[$data['category']],
                'name' => $code,
                'description' => 'Jersey custom ' . $data['category'] . ' kualitas premium.',
                'price' => 100000,
                'image' => 'products/' . strtolower($code) . '.' . $ext,
                'min_qty' => 1,
                'production_days' => 7,
                'is_active' => true,
            ]);

            $this->command->info("Created {$code} ({$data['category']})");
        }
    }
}
