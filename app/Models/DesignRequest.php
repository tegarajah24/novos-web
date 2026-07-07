<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignRequest extends Model
{
    protected $fillable = [
        'order_id',
        'team_name',
        'nama_artikel',
        'nama_pemesan',
        'no_punggung',
        'detail_sponsor',
        'jenis_potongan',
        'lengan_jahitan',
        'logo',
        'design_files',
        'primary_color',
        'secondary_color',
        'motif',
        'material',
        'collar_style',
        'priority',
        'additional_notes',
    ];

    protected function casts(): array
    {
        return [
            'design_files' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
