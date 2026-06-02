<?php

namespace App\Services;

use Midtrans;

class MidtransService
{
    public function __construct()
    {
        Midtrans\Config::$serverKey = config('midtrans.server_key');
        Midtrans\Config::$isProduction = config('midtrans.is_production', false);
        Midtrans\Config::$isSanitized = true;
        Midtrans\Config::$is3ds = true;
    }

    public function createTransaction(array $params): array
    {
        return Midtrans\Snap::createTransaction($params);
    }
}