<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyMentalCheck extends Model
{
    protected $fillable = [
        'user_id',
        'check_date',
        'answers',
        'total_score',
        'category',
        'need_help',
        'help_note',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'check_date' => 'date',
            'need_help' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
