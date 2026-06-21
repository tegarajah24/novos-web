<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createSnapToken(array $params): string
    {
        $snap = Snap::createTransaction($params);
        return $snap->token;
    }

    public function handleNotification(): object
    {
        return new Notification();
    }

    public function checkTransactionStatus(string $orderId): object
    {
        return Transaction::status($orderId);
    }
}
