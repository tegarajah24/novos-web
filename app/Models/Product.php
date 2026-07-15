<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'images',
        'min_qty',
        'production_days',
        'is_active',
        'theme_color',
        'kerah',
        'bahan',
        'jenis_potongan',
        'lengan_jahitan',
        'product_attributes',
    ];

    protected function casts(): array
    {
        return [
            'price'              => 'decimal:2',
            'is_active'          => 'boolean',
            'product_attributes' => 'array',
            'images'             => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
