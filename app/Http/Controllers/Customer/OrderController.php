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
use App\Models\Notification;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'team_name'      => 'required|string|max:255',
            'no_punggung'    => 'nullable|string|max:100',
            'detail_sponsor' => 'nullable|string|max:255',
            'kerah'          => 'required|string|max:100',
            'bahan'          => 'required|string|max:100',
            'jenis_potongan' => 'required|string|in:REGULER,SLIMFIT CEWE,OVERSIZE,TUNIK,SLIM FIT UNISEX',
            'lengan_jahitan' => 'required|string|in:REGULER OVERDECK,REGULER PAKAI MANSET,RAGLAN A OVERDECK,RAGLAN A PAKAI MANSET,RAGLAN B OVERDECK,RAGLAN B PAKAI MANSET',
            'catatan'        => 'nullable|string|max:2000',
            'ukuran'         => 'nullable|array',
            'ukuran.*'       => 'integer|min:0',
            'total_qty'      => 'nullable|integer|min:1',
            'prioritas'      => 'nullable|string|in:normal,express,super_express',
            'pembayaran'     => 'nullable|string|max:50',
            'warna_utama'    => 'nullable|string|max:7',
            'warna_sekunder' => 'nullable|string|max:7',
            'logo'           => 'nullable|file|mimes:jpg,jpeg,png,ai,eps,psd|max:5120',
            'design_files'   => 'nullable|array',
            'design_files.*' => 'file|mimes:jpg,jpeg,png,pdf,ai,eps,psd,zip,rar|max:20480',
        ]);

        $order = DB::transaction(function () use ($data, $request) {
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

            $catatanText = "Jenis Potongan: " . $data['jenis_potongan'] . "\nModel Lengan & Jahitan: " . $data['lengan_jahitan'] . ($data['catatan'] ? "\nCatatan: " . $data['catatan'] : "");

            $order = Order::create([
                'user_id'     => auth()->id(),
                'order_number' => $orderNumber,
                'status'      => 'menunggu_validasi',
                'total_price' => $totalPrice,
                'notes'       => $catatanText,
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

            $designFiles = [];
            if ($request->hasFile('design_files')) {
                foreach ($request->file('design_files') as $file) {
                    $path = $file->store('design-files/' . $orderNumber, 'public');
                    $designFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize(),
                        'type' => $file->getMimeType(),
                    ];
                }
            }

            $logoPath = !empty($designFiles) ? $designFiles[0]['path'] : null;

            DesignRequest::create([
                'order_id'         => $order->id,
                'team_name'        => $data['team_name'],
                'no_punggung'      => $data['no_punggung'] ?? null,
                'detail_sponsor'   => $data['detail_sponsor'] ?? null,
                'jenis_potongan'   => $data['jenis_potongan'],
                'lengan_jahitan'   => $data['lengan_jahitan'],
                'material'         => $data['bahan'],
                'collar_style'     => $data['kerah'],
                'primary_color'    => $data['warna_utama'] ?? null,
                'secondary_color'  => $data['warna_sekunder'] ?? null,
                'logo'             => $logoPath,
                'design_files'     => $designFiles,
                'additional_notes' => $catatanText,
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

        Notification::sendToAllStaff(
            'new_order',
            'Pesanan Baru',
            "Pesanan baru dari <strong>{$order->user->name}</strong> — <strong>{$order->order_number}</strong>",
            [
                'initials' => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => 'Customer',
                'role_initial' => 'C',
                'role_color' => '#e53e3e',
                'order_number' => $order->order_number,
            ]
        );

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
