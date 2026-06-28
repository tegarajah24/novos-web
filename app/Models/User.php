<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'address', 'role_id', 'avatar', 'fullname'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isInternal(): bool
    {
        return $this->role && $this->role->name !== 'Customer';
    }

    public function isAdmin(): bool
    {
        return $this->role && in_array($this->role->name, Role::adminNames());
    }

    public function isDesign(): bool
    {
        return $this->role?->name === 'Design';
    }

    public function isProduction(): bool
    {
        return $this->role?->name === 'Produksi';
    }

    public function isCustomer(): bool
    {
        return $this->role?->name === 'Customer';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'customer_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
