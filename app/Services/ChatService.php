<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Order;
use Illuminate\Http\UploadedFile;

class ChatService
{
    public function createChatForOrder(Order $order): Chat
    {
        return Chat::create([
            'order_id' => $order->id,
            'customer_id' => $order->user_id,
        ]);
    }

    public function sendMessage(
        int $chatId,
        int $senderId,
        string $message = '',
        ?UploadedFile $file = null
    ): ChatMessage {
        $data = [
            'chat_id' => $chatId,
            'sender_id' => $senderId,
            'message' => $message,
        ];

        if ($file) {
            $uploadService = app(UploadService::class);
            $path = $uploadService->uploadFile($file, 'chat/' . $chatId);

            $data['file_path'] = $path;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getMimeType();
        }

        return ChatMessage::create($data);
    }
}