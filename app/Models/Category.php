<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'description',
        'attributes_schema',
        'form_config',
        'base_price',
    ];

    protected function casts(): array
    {
        return [
            'attributes_schema' => 'array',
            'form_config' => 'array',
            'base_price' => 'decimal:2',
        ];
    }

    public function getFormConfigAttribute($value)
    {
        $defaults = [
            'show_team_name' => true,
            'show_nama_artikel' => true,
            'show_detail_sponsor' => true,
        ];

        if (!$value) {
            return $defaults;
        }

        $arr = is_string($value) ? json_decode($value, true) : $value;
        if (is_array($arr)) {
            foreach ($arr as $key => $val) {
                $arr[$key] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
            }
        }
        return array_merge($defaults, is_array($arr) ? $arr : []);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
