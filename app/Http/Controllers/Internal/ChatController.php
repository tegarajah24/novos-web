<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $chats = Chat::with(['customer', 'messages' => function ($q) {
                $q->latest()->take(1);
            }])
            ->where(function ($q) use ($user) {
                $q->where('admin_id', $user->id)
                  ->orWhereNull('admin_id');
            })
            ->latest()
            ->get()
            ->map(function ($chat) use ($user) {
                $messages = ChatMessage::where('chat_id', $chat->id)
                    ->orderBy('created_at')
                    ->get()
                    ->map(function ($msg) use ($user) {
                        return [
                            'from' => $msg->sender_id === $user->id ? 'admin' : 'customer',
                            'text' => $msg->message,
                            'time' => $msg->created_at->format('H:i'),
                        ];
                    })
                    ->toArray();

                $lastMsg = $chat->messages->first();

                $unread = ChatMessage::where('chat_id', $chat->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id'           => $chat->id,
                    'name'         => $chat->customer->name ?? 'Unknown',
                    'time'         => $lastMsg?->created_at->format('H:i') ?? $chat->created_at->format('H:i'),
                    'lastMessage'  => $lastMsg?->message ?? 'Belum ada pesan',
                    'unread'       => $unread,
                    'online'       => false,
                    'messages'     => $messages,
                ];
            })
            ->values()
            ->toArray();

        return view('internal.chat', compact('chats'));
    }
}
