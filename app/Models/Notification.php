<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function send(
        int|array $userIds,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
    ): void {
        $userIds = is_array($userIds) ? $userIds : [$userIds];

        $records = [];
        foreach ($userIds as $userId) {
            $records[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data ? json_encode($data) : null,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        self::insert($records);
    }

    public static function sendToAllStaff(
        string $type,
        string $title,
        string $message,
        ?array $data = null,
    ): void {
        $staffIds = User::whereHas('role', fn($q) => $q->whereIn('name', [
            'Super Admin', 'Manager', 'Admin', 'Design', 'Produksi'
        ]))->pluck('id')->toArray();

        self::send($staffIds, $type, $title, $message, $data);
    }
}
