<?php

namespace App\Services;

use App\Models\Order;

class TrackingService
{
    public function getOrderTracking(int $orderId): Order
    {
        return Order::with(['orderStatusHistories', 'payments', 'designRequest', 'productionTask'])->findOrFail($orderId);
    }
}