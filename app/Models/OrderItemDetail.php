<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDetail extends Model
{
    protected $fillable = [
        'order_id',
        'no_punggung',
        'nama_punggung',
        'model_lengan',
        'size',
        'keterangan',
        'customizations',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'customizations' => 'array',
            'price' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
