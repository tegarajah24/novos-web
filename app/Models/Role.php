<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function internalNames(): array
    {
        return static::where('name', '!=', 'Customer')->pluck('name')->toArray();
    }

    public static function adminNames(): array
    {
        return ['Super Admin', 'Manager', 'Admin'];
    }

    public static function customerName(): string
    {
        return static::where('name', 'Customer')->value('name') ?? 'Customer';
    }
}
