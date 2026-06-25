<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $chats = Chat::with(['messages.sender', 'admin'])
            ->where('customer_id', $user->id)
            ->latest()
            ->get()
            ->map(fn($chat) => [
                'id'          => $chat->id,
                'name'        => $chat->admin?->name ?? 'Admin Novos',
                'sender_avatar_url' => $chat->admin?->avatar ? Storage::url($chat->admin->avatar) : null,
                'lastMessage' => $chat->messages->last()?->message
                    ?? ($chat->messages->last()?->file_name
                        ? '📎 ' . $chat->messages->last()->file_name
                        : 'Mulai percakapan'),
                'time'   => $chat->messages->last()?->created_at?->format('H:i') ?? '',
                'unread' => $chat->messages
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $user->id)
                    ->count(),
                'online'  => false,
                'messages' => $chat->messages->map(fn($msg) => [
                    'from'               => $msg->sender_id === $user->id ? 'customer' : 'admin',
                    'text'               => $msg->message,
                    'time'               => $msg->created_at->format('H:i'),
                    'file_url'           => $msg->file_url,
                    'file_name'          => $msg->file_name,
                    'file_size_formatted' => $msg->file_size_formatted,
                    'is_image'           => $msg->is_image,
                    'is_video'           => $msg->is_video,
                    'sender_avatar_url' => $msg->sender_avatar_url,
                    'is_admin'           => $msg->sender_id === $chat->admin_id,
                ])->values()->toArray(),
            ])->values();

        return view('customer.chat', compact('chats'));
    }

    public function unreadCount()
    {
        $count = Chat::where('customer_id', auth()->id())
            ->withCount(['messages' => fn($q) => $q->where('is_read', false)->where('sender_id', '!=', auth()->id())])
            ->get()
            ->sum('messages_count');
        return response()->json(['count' => $count]);
    }

    public function markRead(Chat $chat)
    {
        if ($chat->customer_id !== auth()->id()) {
            abort(403);
        }
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'chat_id' => 'nullable|exists:chats,id',
            'message' => 'nullable|string|max:2000',
            'file'    => 'nullable|file|max:20480',
        ]);

        if (!$data['message'] && !$request->hasFile('file')) {
            return response()->json(['message' => 'Pesan atau file harus diisi'], 422);
        }

        $user = auth()->user();

        // Use provided chat_id if valid and belongs to customer, otherwise create/get default chat
        if ($data['chat_id']) {
            $chat = Chat::where('id', $data['chat_id'])
                ->where('customer_id', $user->id)
                ->first();
            
            if (!$chat) {
                return response()->json(['message' => 'Chat tidak ditemukan'], 404);
            }
        } else {
            $chat = Chat::firstOrCreate(['customer_id' => $user->id]);
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('chat-files', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();
        }

                $message = ChatMessage::create([
                    'chat_id'   => $chat->id,
                    'sender_id' => $user->id,
                    'message'   => $data['message'] ?? null,
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                ]);

                $sender = $user->id === $chat->admin_id ? $chat->admin : $chat->customer;

        $message->load('sender');

        $chat->load('order');
        Notification::sendToAllStaff(
            'chat',
            'Pesan Baru',
            "Pesan baru dari customer <strong>{$user->name}</strong>" . ($chat->order ? " untuk <strong>{$chat->order->order_number}</strong>" : '') . ($data['message'] ? ": {$data['message']}" : ''),
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => 'Customer',
                'role_initial' => 'C',
                'role_color' => '#d53f8c',
            ]
        );

        return response()->json([
            'message' => [
                'id'                 => $message->id,
                'message'            => $message->message,
                'file_url'           => $message->file_url,
                'file_name'          => $message->file_name,
                'file_size_formatted' => $message->file_size_formatted,
                'is_image'           => $message->is_image,
                'is_video'           => $message->is_video,
                'created_at'         => $message->created_at->format('H:i'),
            ],
        ]);
    }
}
