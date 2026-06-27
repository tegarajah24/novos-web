<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosterSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getRotation(): string
    {
        return static::where('key', 'poster_rotation')->value('value') ?? 'daily';
    }

    public static function setRotation(string $value): void
    {
        static::updateOrCreate(
            ['key' => 'poster_rotation'],
            ['value' => $value]
        );
    }
}
