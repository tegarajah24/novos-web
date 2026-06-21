<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with(['messages.sender', 'admin'])
            ->where('customer_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn($chat) => [
                'id'          => $chat->id,
                'name'        => $chat->admin?->name ?? 'Admin Novos',
                'lastMessage' => $chat->messages->last()?->message
                    ?? ($chat->messages->last()?->file_name
                        ? '📎 ' . $chat->messages->last()->file_name
                        : 'Mulai percakapan'),
                'time'   => $chat->messages->last()?->created_at?->format('H:i') ?? '',
                'unread' => $chat->messages
                    ->where('is_read', false)
                    ->where('sender_id', '!=', auth()->id())
                    ->count(),
                'online'  => false,
                'messages' => $chat->messages->map(fn($msg) => [
                    'from'               => $msg->sender_id === auth()->id() ? 'customer' : 'admin',
                    'text'               => $msg->message,
                    'time'               => $msg->created_at->format('H:i'),
                    'file_url'           => $msg->file_url,
                    'file_name'          => $msg->file_name,
                    'file_size_formatted' => $msg->file_size_formatted,
                    'is_image'           => $msg->is_image,
                    'is_video'           => $msg->is_video,
                ])->values()->toArray(),
            ])->values();

        return view('customer.chat', compact('chats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'nullable|string|max:2000',
            'file'    => 'nullable|file|max:20480',
        ]);

        if (!$data['message'] && !$request->hasFile('file')) {
            return response()->json(['message' => 'Pesan atau file harus diisi'], 422);
        }

        $chat = Chat::firstOrCreate(['customer_id' => auth()->id()]);

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
            'sender_id' => auth()->id(),
            'message'   => $data['message'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
        ]);

        $message->load('sender');

        $currentUser = auth()->user();
        $chat->load('order');
        Notification::sendToAllStaff(
            'chat',
            'Pesan Baru',
            "Pesan baru dari customer <strong>{$currentUser->name}</strong>" . ($chat->order ? " untuk <strong>{$chat->order->order_number}</strong>" : '') . ($data['message'] ? ": {$data['message']}" : ''),
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
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
