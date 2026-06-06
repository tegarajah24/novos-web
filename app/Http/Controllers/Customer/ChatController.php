<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendChatMessageRequest;
use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    public function store(SendChatMessageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $chat = Chat::findOrFail($data['chat_id']);

        $message = $this->chatService->sendMessage(
            $chat->id,
            auth()->id(),
            $data['message'] ?? '',
            $request->file('file')
        );

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'message' => $message->message,
                'file_url' => $message->file_url,
                'file_name' => $message->file_name,
                'file_size_formatted' => $message->file_size_formatted,
                'is_image' => $message->is_image,
                'is_video' => $message->is_video,
                'is_audio' => $message->is_audio,
                'created_at' => $message->created_at->format('H:i'),
            ],
        ]);
    }
}
