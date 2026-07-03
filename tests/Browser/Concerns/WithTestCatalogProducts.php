<?php

namespace Tests\Browser\Concerns;

use App\Models\Category;
use App\Models\Product;

trait WithTestCatalogProducts
{
    protected function ensureCatalogProductsExist(): void
    {
        if (Category::count() > 0 && Product::count() > 0) {
            return;
        }

        $categories = [
            'Running'    => ['Novos Running Pro', 'Novos Running Lite'],
            'Sepak Bola' => ['Novos Jersey Stadion', 'Novos Jersey Elite'],
            'Futsal'     => ['Novos Futsal Pro'],
            'Basket'     => ['Novos Basket Pro', 'Novos Basket Retro'],
            'Training'   => ['Novos Training Set'],
        ];

        foreach ($categories as $catName => $products) {
            $category = Category::firstOrCreate(['name' => $catName]);

            foreach ($products as $name) {
                Product::firstOrCreate(
                    ['name' => $name],
                    [
                        'category_id'     => $category->id,
                        'description'     => "Jersey {$catName} kualitas premium.",
                        'price'           => 100000,
                        'image'           => null,
                        'min_qty'         => 1,
                        'production_days' => 7,
                        'is_active'       => true,
                    ]
                );
            }
        }
    }
}
