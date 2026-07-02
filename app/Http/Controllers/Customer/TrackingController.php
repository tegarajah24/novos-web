<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $orderData = null;
        $shared = false;

        if ($request->q) {
            $order = Order::with(['designRequest', 'orderItems'])
                ->where('order_number', $request->q)
                ->where('user_id', auth()->id())
                ->first();

            if ($order) {
                $orderData = $this->formatOrderData($order);
                $orderData['share_url'] = $order->share_token
                    ? route('tracking.shared', $order->share_token)
                    : null;
            }
        }

        return view('customer.tracking', compact('orderData', 'shared'));
    }

    public function shared($token)
    {
        $order = Order::with(['designRequest', 'orderItems'])
            ->where('share_token', $token)
            ->firstOrFail();

        $orderData = $this->formatOrderData($order);
        $orderData['share_url'] = null;
        $shared = true;

        return view('customer.tracking', compact('orderData', 'shared'));
    }

    private function formatOrderData($order): array
    {
        $designFiles = [];
        if ($order->designRequest && $order->designRequest->design_files) {
            $designFiles = collect($order->designRequest->design_files)->map(fn($f) => [
                'name' => $f['name'],
                'url'  => asset('storage/' . $f['path']),
            ])->values()->toArray();
        }

        return [
            'id'           => $order->order_number,
            'date'         => $order->created_at->format('j F Y'),
            'status'       => $order->status,
            'design_files' => $designFiles,
            'team_name'    => $order->designRequest?->team_name,
        ];
    }
}
