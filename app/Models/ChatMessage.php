<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_id',
        'reply_to_id',
        'message',
        'is_read',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    public function getSenderAvatarUrlAttribute(): ?string
    {
        if (!$this->sender || !$this->sender->avatar) return null;

        return Storage::url($this->sender->avatar);
    }

    public function getAdminAvatarUrlAttribute(): ?string
    {
        if (!$this->chat || !$this->chat->admin || !$this->chat->admin->avatar) return null;

        return Storage::url($this->chat->admin->avatar);
    }

    public function getCustomerAvatarUrlAttribute(): ?string
    {
        if (!$this->chat || !$this->chat->customer || !$this->chat->customer->avatar) return null;

        return Storage::url($this->chat->customer->avatar);
    }

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;

        return Storage::url($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return $this->file_type && str_starts_with($this->file_type, 'image/');
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->file_type && str_starts_with($this->file_type, 'video/');
    }

    public function getIsAudioAttribute(): bool
    {
        return $this->file_type && str_starts_with($this->file_type, 'audio/');
    }

    public function getFileSizeFormattedAttribute(): ?string
    {
        if (!$this->file_size) return null;

        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, 1) . ' ' . $units[$i];
    }
}
