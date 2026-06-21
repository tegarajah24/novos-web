<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DesignRequest;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'team_name'  => 'required|string|max:255',
            'kerah'      => 'required|string|max:100',
            'bahan'      => 'required|string|max:100',
            'catatan'    => 'nullable|string|max:2000',
            'ukuran'     => 'nullable|array',
            'ukuran.*'   => 'integer|min:0',
            'total_qty'  => 'nullable|integer|min:1',
            'prioritas'  => 'nullable|string|in:normal,express,super_express',
            'pembayaran' => 'nullable|string|max:50',
            'warna_utama'    => 'nullable|string|max:7',
            'warna_sekunder' => 'nullable|string|max:7',
        ]);

        $order = DB::transaction(function () use ($data) {
            $orderNumber = 'NVS-' . now()->format('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

            $totalQty = $data['total_qty'] ?? 0;
            $pricePerItem = 85000;
            $biayaPrioritas = match ($data['prioritas'] ?? 'normal') {
                'express'       => 50000,
                'super_express' => 150000,
                default         => 0,
            };
            $subtotal = $totalQty * $pricePerItem;
            $totalPrice = $subtotal + $biayaPrioritas;

            $prioritasLabel = match ($data['prioritas'] ?? 'normal') {
                'express'       => 'Express',
                'super_express' => 'Super Express',
                default         => 'Normal',
            };

            $order = Order::create([
                'user_id'     => auth()->id(),
                'order_number' => $orderNumber,
                'status'      => 'menunggu_validasi',
                'total_price' => $totalPrice,
                'notes'       => $data['catatan'] ?? null,
                'admin_notes' => 'Prioritas: ' . $prioritasLabel . ' (' . $biayaPrioritas . ')',
            ]);

            $sizes = $data['ukuran'] ?? [];
            foreach ($sizes as $size => $qty) {
                if (($qty = (int) $qty) > 0) {
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'size'           => $size,
                        'qty'            => $qty,
                        'price_per_item' => $pricePerItem,
                        'subtotal'       => $qty * $pricePerItem,
                    ]);
                }
            }

            DesignRequest::create([
                'order_id'         => $order->id,
                'team_name'        => $data['team_name'],
                'material'         => $data['bahan'],
                'collar_style'     => $data['kerah'],
                'primary_color'    => $data['warna_utama'] ?? null,
                'secondary_color'  => $data['warna_sekunder'] ?? null,
                'additional_notes' => $data['catatan'] ?? null,
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_validasi',
                'changed_by' => auth()->id(),
                'notes'      => 'Pesanan dibuat oleh customer',
            ]);

            $this->sendSystemMessage($order, 'Pesanan Anda telah dibuat dan menunggu validasi admin.');

            return $order;
        });

        return response()->json([
            'success'     => true,
            'order'       => $order,
            'orderNumber' => $order->order_number,
        ]);
    }

    private function sendSystemMessage(Order $order, string $message): void
    {
        $chat = Chat::firstOrCreate([
            'order_id'    => $order->id,
            'customer_id' => $order->user_id,
        ]);

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $order->user_id,
            'message'   => $message,
        ]);
    }
}
