<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerChatRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        return view('customer.chat');
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

    public function store(StoreCustomerChatRequest $request)
    {
        $data = $request->validated();

        if (!$data['message'] && !$request->hasFile('file')) {
            return response()->json(['message' => 'Pesan atau file harus diisi'], 422);
        }

        $user = auth()->user();

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

        $chat->load('order');
        Notification::sendToAllStaff(
            'chat',
            'Pesan Baru',
            "Pesan baru dari customer <strong>{$user->name}</strong>" . ($chat->order ? " untuk <strong>{$chat->order->order_number}</strong>" : '') . ($data['message'] ? ": {$data['message']}" : ''),
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
                'role_initial' => 'C',
                'role_color' => '#d53f8c',
            ]
        );

        return response()->json([
            'message' => [
                'id'                  => $message->id,
                'message'             => $message->message,
                'file_url'            => $message->file_url,
                'file_name'           => $message->file_name,
                'file_size_formatted' => $message->file_size_formatted,
                'is_image'            => $message->is_image,
                'is_video'            => $message->is_video,
                'created_at'          => $message->created_at->format('H:i'),
            ],
        ]);
    }
}
