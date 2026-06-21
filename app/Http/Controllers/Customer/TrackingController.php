<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $orderData = null;

        if ($request->q) {
            $order = Order::with(['designRequest', 'orderItem'])
                ->where('order_number', $request->q)
                ->where('user_id', auth()->id())
                ->first();

            if ($order) {
                $orderData = [
                    'id'     => $order->order_number,
                    'date'   => $order->created_at->format('j F Y'),
                    'status' => $order->status,
                ];
            }
        }

        return view('customer.tracking', compact('orderData'));
    }

    public function accDesign($id)
    {
        $order = Order::where('order_number', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['di_design', 'siap_cetak'])) {
            return response()->json(['message' => 'Status pesanan tidak memungkinkan untuk ACC'], 422);
        }

        $nextStatus = match ($order->status) {
            'di_design'  => 'siap_cetak',
            'siap_cetak' => 'diproduksi',
        };

        $order->update(['status' => $nextStatus]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $nextStatus,
            'changed_by' => auth()->id(),
            'notes'      => 'Desain disetujui oleh customer',
        ]);

        return response()->json([
            'success' => true,
            'status'  => $nextStatus,
        ]);
    }

    public function revision(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:2000']);

        $order = Order::where('order_number', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['di_design', 'siap_cetak'])) {
            return response()->json(['message' => 'Tidak dapat mengirim revisi pada status ini'], 422);
        }

        if ($order->status === 'siap_cetak') {
            $order->update(['status' => 'di_design']);
        }

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $order->status,
            'changed_by' => auth()->id(),
            'notes'      => 'Revisi: ' . $request->note,
        ]);

        return response()->json(['success' => true]);
    }
}
