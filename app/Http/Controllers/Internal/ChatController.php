<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternalChatRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                        $isAdmin = $msg->sender_id === $user->id;
                        return [
                            'from'               => $isAdmin ? 'admin' : 'customer',
                            'text'               => $msg->message,
                            'time'               => $msg->created_at->format('H:i'),
                            'file_url'           => $msg->file_url,
                            'file_name'          => $msg->file_name,
                            'file_size_formatted' => $msg->file_size_formatted,
                            'is_image'           => $msg->is_image,
                            'is_video'           => $msg->is_video,
                            'sender_avatar_url'  => $msg->sender_avatar_url,
                            'is_admin'           => $isAdmin,
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
                    'lastMessage'  => $lastMsg?->message
                        ?? ($lastMsg?->file_name
                            ? '📎 ' . $lastMsg->file_name
                            : 'Belum ada pesan'),
                    'unread'       => $unread,
                    'online'       => false,
                    'messages'     => $messages,
                ];
            })
            ->values()
            ->toArray();

        return view('internal.chat', compact('chats'));
    }

    public function unreadCount()
    {
        $user = auth()->user();
        $chatIds = Chat::where(function ($q) use ($user) {
                $q->where('admin_id', $user->id)
                  ->orWhereNull('admin_id');
            })
            ->pluck('id');

        $count = ChatMessage::whereIn('chat_id', $chatIds)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markRead(Chat $chat)
    {
        $user = auth()->user();
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function store(StoreInternalChatRequest $request)
    {
        $data = $request->validated();

        if (!$data['message'] && !$request->hasFile('file')) {
            return response()->json(['message' => 'Pesan atau file harus diisi'], 422);
        }

        $user = auth()->user();

        $chat = Chat::findOrFail($data['chat_id']);

        // Assign admin jika belum ada
        if (!$chat->admin_id) {
            $chat->update(['admin_id' => $user->id]);
        }

        $filePath = null;
        $fileName = null;
        $fileSize = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
            $filePath = $isImage
                ? app(ImageService::class)->compressAndStore($file, 'chat-files')
                : $file->store('chat-files', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $isImage ? Storage::disk('public')->size($filePath) : $file->getSize();
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

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id'                 => $message->id,
                'from'               => 'admin',
                'text'               => $message->message,
                'time'               => $message->created_at->format('H:i'),
                'file_url'           => $message->file_url,
                'file_name'          => $message->file_name,
                'file_size_formatted' => $message->file_size_formatted,
                'is_image'           => $message->is_image,
                'is_video'           => $message->is_video,
            ],
        ]);
    }
}
