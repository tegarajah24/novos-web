<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicroBreak extends Model
{
    protected $fillable = [
        'user_id',
        'check_date',
        'checklist',
        'score',
        'level',
        'eval',
        'catatan_membantu',
        'catatan_kendala',
    ];

    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'eval' => 'array',
            'check_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
