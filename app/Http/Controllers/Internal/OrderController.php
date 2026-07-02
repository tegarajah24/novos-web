<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        return view('internal.daftar-pesanan');
    }

    public function show(Order $order)
    {
        return view('internal.detail-pesanan', ['order' => $order]);
    }
}
