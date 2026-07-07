<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withPivot('access_level')
            ->withTimestamps();
    }

    public function hasAccess(string $permissionSlug): bool
    {
        $perm = $this->permissions->firstWhere('slug', $permissionSlug);
        return $perm && $perm->pivot->access_level !== 'none';
    }

    public function hasFullAccess(string $permissionSlug): bool
    {
        $perm = $this->permissions->firstWhere('slug', $permissionSlug);
        return $perm && $perm->pivot->access_level === 'full';
    }

    public function getAccessLevel(string $permissionSlug): string
    {
        $perm = $this->permissions->firstWhere('slug', $permissionSlug);
        return $perm ? $perm->pivot->access_level : 'none';
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
