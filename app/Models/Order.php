<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'production_stage',
        'notes',
        'admin_notes',
        'total_price',
        'confirmed_at',
        'assignee_id',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function orderItem(): HasOne
    {
        return $this->hasOne(OrderItem::class);
    }

    public function designRequest(): HasOne
    {
        return $this->hasOne(DesignRequest::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function productionTask(): HasOne
    {
        return $this->hasOne(ProductionTask::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class);
    }
}
