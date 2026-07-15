<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allAsArray(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    public static function getDeadlineDaysForPriority(?string $priority): int
    {
        $priority = $priority ?: 'normal';
        
        $defaultDays = match ($priority) {
            'super_express' => 2,
            'express'       => 6,
            default         => 14,
        };

        $estimasi = static::get("prioritas_{$priority}_estimasi");
        if (!$estimasi) {
            return $defaultDays;
        }

        preg_match_all('/\d+/', $estimasi, $matches);
        if (!empty($matches[0])) {
            return (int) end($matches[0]);
        }

        return $defaultDays;
    }
}
