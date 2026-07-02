<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function preview()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($n) => $this->formatNotification($n));

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function viewPage()
    {
        return view('internal.notifikasi');
    }

    private function formatNotification(Notification $n): array
    {
        $data = $n->data ?? [];
        $typeColors = [
            'new_order' => ['color' => '#1a237e', 'badge_class' => 'bg-yellow-100 text-yellow-700', 'badge' => 'Baru'],
            'order_validated' => ['color' => '#16a34a', 'badge_class' => 'bg-green-100 text-green-700', 'badge' => 'Divalidasi'],
            'payment_success' => ['color' => '#16a34a', 'badge_class' => 'bg-green-100 text-green-700', 'badge' => 'Lunas'],
            'design_acc' => ['color' => '#6b46c1', 'badge_class' => 'bg-purple-100 text-purple-700', 'badge' => 'ACC Desain'],
            'design_revision' => ['color' => '#d97706', 'badge_class' => 'bg-orange-100 text-orange-700', 'badge' => 'Revisi'],
            'design_upload' => ['color' => '#6b46c1', 'badge_class' => 'bg-purple-100 text-purple-700', 'badge' => 'Siap Cetak'],
            'production_done' => ['color' => '#0284c7', 'badge_class' => 'bg-blue-100 text-blue-700', 'badge' => 'Selesai'],
            'order_cancelled' => ['color' => '#dc2626', 'badge_class' => 'bg-red-100 text-red-700', 'badge' => 'Dibatalkan'],
            'chat_message' => ['color' => '#0891b2', 'badge_class' => 'bg-gray-100 text-gray-600', 'badge' => 'Pesan'],
        ];

        $tc = $typeColors[$n->type] ?? ['color' => '#6b7280', 'badge_class' => 'bg-gray-100 text-gray-700', 'badge' => $n->type];

        return [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'message' => $n->message,
            'read' => $n->is_read,
            'time' => $n->created_at->diffForHumans(),
            'datetime' => $n->created_at->format('j M Y, H:i'),
            'color' => $tc['color'],
            'badgeClass' => $tc['badge_class'],
            'badge' => $tc['badge'],
            'initials' => $data['initials'] ?? 'NN',
            'role' => $data['role'] ?? 'Sistem',
            'roleInitial' => $data['role_initial'] ?? 'S',
            'roleColor' => $data['role_color'] ?? '#6b7280',
            'order_number' => $data['order_number'] ?? null,
            'order_url' => ($data['order_number'] ?? null)
                ? route('staf.detail-pesanan', $data['order_number'])
                : null,
        ];
    }
}
