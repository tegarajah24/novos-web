<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'size',
        'qty',
        'is_selected',
        'design_data',
        'notes',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'is_selected' => 'boolean',
            'design_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
