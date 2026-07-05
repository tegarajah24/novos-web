<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerChatRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->forceFill(['chat_active_at' => now()])->save();

        Chat::firstOrCreate(['customer_id' => $user->id]);

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
                'online'    => $chat->admin?->chat_active_at && $chat->admin->chat_active_at->gt(now()->subMinutes(2)),
                'unassigned' => $chat->admin_id === null,
                'messages' => $chat->messages->map(fn($msg) => [
                    'id'                 => $msg->id,
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

    public function poll(Request $request, Chat $chat)
    {
        if ($chat->customer_id !== auth()->id()) {
            abort(403);
        }

        auth()->user()->forceFill(['chat_active_at' => now()])->save();

        $afterId = $request->integer('after', 0);

        $messages = ChatMessage::where('chat_id', $chat->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(fn($msg) => [
                'id'                 => $msg->id,
                'from'               => $msg->sender_id === auth()->id() ? 'customer' : 'admin',
                'text'               => $msg->message,
                'time'               => $msg->created_at->format('H:i'),
                'file_url'           => $msg->file_url,
                'file_name'          => $msg->file_name,
                'file_size_formatted' => $msg->file_size_formatted,
                'is_image'           => $msg->is_image,
                'is_video'           => $msg->is_video,
                'sender_avatar_url'  => $msg->sender_avatar_url,
                'is_admin'           => $msg->sender_id === $chat->admin_id,
            ]);

        $unreadCount = ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'messages'  => $messages,
            'unread'    => $unreadCount,
            'unassigned' => $chat->admin_id === null,
            'admin'     => $chat->admin_id ? [
                'name'       => $chat->admin->name,
                'avatar_url' => $chat->admin->avatar ? Storage::url($chat->admin->avatar) : null,
                'online'     => $chat->admin->chat_active_at && $chat->admin->chat_active_at->gt(now()->subMinutes(2)),
            ] : null,
        ]);
    }

    public function store(StoreCustomerChatRequest $request)
    {
        $data = $request->validated();

        $message = $data['message'] ?? null;

        if (!$message && !$request->hasFile('file')) {
            return response()->json(['message' => 'Pesan atau file harus diisi'], 422);
        }

        $user = auth()->user();

        $chatId = $data['chat_id'] ?? null;

        // Use provided chat_id if valid and belongs to customer, otherwise create/get default chat
        if ($chatId) {
            $chat = Chat::where('id', $chatId)
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
            $extension = strtolower($file->getClientOriginalExtension());
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
            $filePath = $isImage
                ? app(ImageService::class)->compressAndStore($file, 'chat-files')
                : $file->store('chat-files', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $isImage ? Storage::disk('public')->size($filePath) : $file->getSize();
            $fileType = $file->getMimeType();
        }

        $chatMessage = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $user->id,
            'message'   => $data['message'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'file_type' => $fileType,
        ]);

        $chatMessage->load('sender');

        $chat->load('order');
        Notification::sendToAllStaff(
            'chat',
            'Pesan Baru',
            "Pesan baru dari customer <strong>{$user->name}</strong>" . ($chat->order ? " untuk <strong>{$chat->order->order_number}</strong>" : '') . ($data['message'] ?? null ? ": {$data['message']}" : ''),
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
                'role_initial' => 'C',
                'role_color' => '#d53f8c',
            ]
        );

        return response()->json([
            'message' => [
                'id'                 => $chatMessage->id,
                'message'            => $chatMessage->message,
                'file_url'           => $chatMessage->file_url,
                'file_name'          => $chatMessage->file_name,
                'file_size_formatted' => $chatMessage->file_size_formatted,
                'is_image'           => $chatMessage->is_image,
                'is_video'           => $chatMessage->is_video,
                'created_at'         => $chatMessage->created_at->format('H:i'),
            ],
        ]);
    }

    public function download(ChatMessage $chatMessage)
    {
        $user = auth()->user();

        if ($chatMessage->chat->customer_id !== $user->id) {
            abort(403);
        }

        if (!$chatMessage->file_path || !Storage::disk('public')->exists($chatMessage->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $chatMessage->file_path,
            $chatMessage->file_name
        );
    }
}
