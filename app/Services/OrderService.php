<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;

class OrderService
{
    public function createOrder(array $data): Order
    {
        $order = Order::create($data);
        return $order;
    }

    public function updateStatus(Order $order, string $status, int $changedBy, ?string $notes = null): OrderStatusHistory
    {
        $order->update(['status' => $status]);
        return OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $status,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }
}