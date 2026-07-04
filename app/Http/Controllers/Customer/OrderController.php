<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StoreCartOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DesignRequest;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use App\Models\User;
use App\Services\ImageService;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data, $request) {
            $addressId = $data['address_id'] ?? null;
            if ($addressId) {
                \App\Models\CustomerAddress::where('user_id', auth()->id())->update(['is_primary' => false]);
                \App\Models\CustomerAddress::where('id', $addressId)->where('user_id', auth()->id())->update(['is_primary' => true]);
            }

            $orderNumber = 'NVS-' . now()->format('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            $totalQty = $data['total_qty'] ?? 0;
            $pricePerItem = \App\Models\Setting::get('base_price_per_pcs', 85000);
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
                $imageService = app(ImageService::class);
                foreach ($request->file('design_files') as $file) {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
                    $path = $isImage
                        ? $imageService->compressAndStore($file, 'design-files/' . $orderNumber)
                        : $file->store('design-files/' . $orderNumber, 'public');
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
                'role' => $order->user->role->name,
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

    public function storeCart(StoreCartOrderRequest $request)
    {
        $data = $request->validated();

        $order = DB::transaction(function () use ($data) {
            $cartItems = \App\Models\Cart::whereIn('id', $data['cart_item_ids'])
                            ->where('user_id', auth()->id())
                            ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Keranjang kosong atau produk tidak valid.');
            }

            $addressId = $data['address_id'] ?? null;
            if ($addressId) {
                \App\Models\CustomerAddress::where('user_id', auth()->id())->update(['is_primary' => false]);
                \App\Models\CustomerAddress::where('id', $addressId)->where('user_id', auth()->id())->update(['is_primary' => true]);
            }

            $orderNumber = 'NVS-' . now()->format('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);

            $biayaPrioritas = match ($data['prioritas'] ?? 'normal') {
                'express'       => 50000,
                'super_express' => 150000,
                default         => 0,
            };

            $totalPrice = $biayaPrioritas;
            $allNotes = [];
            $designDataMerged = null;

            foreach ($cartItems as $index => $item) {
                if ($item->design_data) {
                    $itemTotalQty = collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
                    $pricePerPcs = $item->design_data['base_price_per_pcs'] ?? \App\Models\Setting::get('base_price_per_pcs', 85000);
                    $itemTotal = $itemTotalQty * $pricePerPcs;
                    
                    $totalPrice += $itemTotal;
                    $allNotes[] = "Produk " . ($index + 1) . ": " . $item->design_data['team_name'] . " (" . $item->design_data['jenis_potongan'] . ", " . $item->design_data['lengan_jahitan'] . ")";
                    
                    if (!$designDataMerged) {
                        $designDataMerged = $item->design_data;
                    }
                } else {
                    $itemTotal = $item->qty * ($item->product->price ?? 0);
                    $totalPrice += $itemTotal;
                    $allNotes[] = "Produk " . ($index + 1) . ": " . ($item->product->name ?? 'Katalog');
                }
            }

            $prioritasLabel = match ($data['prioritas'] ?? 'normal') {
                'express'       => 'Express',
                'super_express' => 'Super Express',
                default         => 'Normal',
            };

            $catatanText = "Checkout dari Keranjang (" . count($cartItems) . " Produk).\n" . implode("\n", $allNotes);

            $order = Order::create([
                'user_id'      => auth()->id(),
                'order_number' => $orderNumber,
                'status'       => 'menunggu_validasi',
                'total_price'  => $totalPrice,
                'notes'        => $catatanText,
                'admin_notes'  => 'Prioritas: ' . $prioritasLabel . ' (Rp ' . number_format($biayaPrioritas, 0, ',', '.') . ')',
            ]);

            foreach ($cartItems as $item) {
                if ($item->design_data) {
                    $sizes = $item->design_data['ukuran'] ?? [];
                    $pricePerItem = $item->design_data['base_price_per_pcs'] ?? 85000;
                    foreach ($sizes as $size => $qty) {
                        if (($qty = (int) $qty) > 0) {
                            OrderItem::create([
                                'order_id'       => $order->id,
                                'size'           => $size . ' (' . $item->design_data['team_name'] . ')',
                                'qty'            => $qty,
                                'price_per_item' => $pricePerItem,
                                'subtotal'       => $qty * $pricePerItem,
                            ]);
                        }
                    }
                } else {
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'size'           => $item->size . ' (' . ($item->product->name ?? 'Katalog') . ')',
                        'qty'            => $item->qty,
                        'price_per_item' => $item->product->price ?? 0,
                        'subtotal'       => $item->qty * ($item->product->price ?? 0),
                    ]);
                }
            }

            if ($designDataMerged) {
                DesignRequest::create([
                    'order_id'         => $order->id,
                    'team_name'        => "Multiple Orders (Lihat Catatan)",
                    'no_punggung'      => $designDataMerged['no_punggung'] ?? null,
                    'detail_sponsor'   => $designDataMerged['detail_sponsor'] ?? null,
                    'jenis_potongan'   => $designDataMerged['jenis_potongan'] ?? '-',
                    'lengan_jahitan'   => $designDataMerged['lengan_jahitan'] ?? '-',
                    'material'         => $designDataMerged['bahan'] ?? '-',
                    'collar_style'     => $designDataMerged['kerah'] ?? '-',
                    'primary_color'    => $designDataMerged['warna_utama'] ?? null,
                    'secondary_color'  => $designDataMerged['warna_sekunder'] ?? null,
                    'logo'             => null,
                    'design_files'     => [],
                    'additional_notes' => $catatanText,
                ]);
            } else {
                DesignRequest::create([
                    'order_id'         => $order->id,
                    'team_name'        => "Katalog",
                    'jenis_potongan'   => '-',
                    'lengan_jahitan'   => '-',
                    'material'         => '-',
                    'collar_style'     => '-',
                    'additional_notes' => $catatanText,
                ]);
            }

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_validasi',
                'changed_by' => auth()->id(),
                'notes'      => 'Pesanan (dari keranjang) dibuat oleh customer',
            ]);

            $this->sendSystemMessage($order, 'Pesanan Anda (via keranjang) telah dibuat dan menunggu validasi admin.');

            \App\Models\Cart::whereIn('id', $data['cart_item_ids'])->delete();

            return $order;
        });

        Notification::sendToAllStaff(
            'new_order',
            'Pesanan Baru (Keranjang)',
            "Pesanan baru dari <strong>{$order->user->name}</strong> — <strong>{$order->order_number}</strong>",
            [
                'initials' => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
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

        $admin = User::whereHas('role', fn($q) => $q->whereIn('name', Role::adminNames()))
            ->first();

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $admin?->id,
            'message'   => $message,
        ]);
    }
}
