<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Order;

class ChatService
{
    public function createChatForOrder(Order $order): Chat
    {
        return Chat::create([
            'order_id' => $order->id,
            'customer_id' => $order->user_id,
        ]);
    }

    public function sendMessage(int $chatId, int $senderId, string $message): ChatMessage
    {
        return ChatMessage::create([
            'chat_id' => $chatId,
            'sender_id' => $senderId,
            'message' => $message,
        ]);
    }
}