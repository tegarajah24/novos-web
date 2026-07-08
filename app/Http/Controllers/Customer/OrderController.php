<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\StoreCartOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemDetail;
use App\Models\DesignRequest;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Notification;
use App\Models\Payment;
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

            $todayPrefix = 'NVS-' . now()->format('Ymd') . '-';
            $lastOrder = Order::where('order_number', 'like', $todayPrefix . '%')->orderBy('order_number', 'desc')->lockForUpdate()->first();
            $nextSeq = $lastOrder ? (int) substr($lastOrder->order_number, -3) + 1 : 1;
            $orderNumber = $todayPrefix . str_pad($nextSeq, 3, '0', STR_PAD_LEFT);

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

            $catatanText = "Jenis Potongan: " . $data['jenis_potongan'] . "\nModel Lengan & Jahitan: " . $data['lengan_jahitan'] . ($data['catatan'] ? "\n=== Detail Pesanan ===\n" . $data['catatan'] : "");

            $order = Order::create([
                'user_id'     => auth()->id(),
                'order_number' => $orderNumber,
                'status'      => 'menunggu_pembayaran',
                'total_price' => $totalPrice,
                'notes'       => $catatanText,
                'admin_notes' => 'Prioritas: ' . $prioritasLabel . ' (' . $biayaPrioritas . ')',
            ]);

            // Create single order item with total qty
            OrderItem::create([
                'order_id'       => $order->id,
                'size'           => '-',
                'qty'            => $totalQty,
                'price_per_item' => $pricePerItem,
                'subtotal'       => $totalQty * $pricePerItem,
            ]);

            // Parse Detail Pesanan (catatan) ke order_item_details
            if (!empty($data['catatan'])) {
                $lines = explode("\n", trim($data['catatan']));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    $parts = array_map('trim', explode(',', $line));
                    OrderItemDetail::create([
                        'order_id'     => $order->id,
                        'no_punggung'  => $parts[0] ?? null,
                        'nama_punggung' => $parts[1] ?? null,
                        'model_lengan' => $parts[2] ?? null,
                        'size'         => $parts[3] ?? null,
                        'keterangan'   => $parts[4] ?? null,
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
                'nama_artikel'     => $data['nama_artikel'] ?? null,
                'nama_pemesan'     => $data['nama_pemesan'] ?? null,
                'detail_sponsor'   => $data['detail_sponsor'] ?? null,
                'jenis_potongan'   => $data['jenis_potongan'],
                'lengan_jahitan'   => $data['lengan_jahitan'],
                'material'         => $data['bahan'],
                'collar_style'     => $data['kerah'],
                'priority'         => $data['prioritas'] ?? 'normal',
                'logo'             => $logoPath,
                'design_files'     => $designFiles,
                'additional_notes' => $catatanText,
            ]);

            if (!empty($data['phone'])) {
                $request->user()->update(['phone' => $data['phone']]);
            }

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_pembayaran',
                'changed_by' => auth()->id(),
                'notes'      => 'Pesanan dibuat oleh customer',
            ]);

            $this->sendSystemMessage($order, 'Pesanan Anda telah dibuat. Silakan lakukan pembayaran DP minimal 10%.');

            return $order;
        });

        Notification::sendToAllStaff(
            'new_order',
            'Pesanan Baru',
            "Pesanan baru dari <strong>{$order->user->name}</strong> — <strong>{$order->order_number}</strong> — menunggu pembayaran",
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
                    $itemTotalQty = $item->design_data['total_qty'] ?? collect($item->design_data['ukuran'] ?? [])->sum(fn($v) => (int) $v);
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
                'status'       => 'menunggu_pembayaran',
                'total_price'  => $totalPrice,
                'notes'        => $catatanText,
                'admin_notes'  => 'Prioritas: ' . $prioritasLabel . ' (Rp ' . number_format($biayaPrioritas, 0, ',', '.') . ')',
            ]);

            foreach ($cartItems as $item) {
                if ($item->design_data) {
                    $pricePerItem = $item->design_data['base_price_per_pcs'] ?? 85000;
                    $sizes = $item->design_data['ukuran'] ?? [];
                    if (!empty($sizes)) {
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
                        $totalQty = $item->design_data['total_qty'] ?? 0;
                        OrderItem::create([
                            'order_id'       => $order->id,
                            'size'           => '-',
                            'qty'            => $totalQty,
                            'price_per_item' => $pricePerItem,
                            'subtotal'       => $totalQty * $pricePerItem,
                        ]);
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
                    'nama_artikel'     => $designDataMerged['nama_artikel'] ?? null,
                    'nama_pemesan'     => $designDataMerged['nama_pemesan'] ?? null,
                    'detail_sponsor'   => $designDataMerged['detail_sponsor'] ?? null,
                    'jenis_potongan'   => $designDataMerged['jenis_potongan'] ?? '-',
                    'lengan_jahitan'   => $designDataMerged['lengan_jahitan'] ?? '-',
                    'material'         => $designDataMerged['bahan'] ?? '-',
                    'collar_style'     => $designDataMerged['kerah'] ?? '-',
                    'priority'         => $data['prioritas'] ?? 'normal',
                    'logo'             => null,
                    'design_files'     => [],
                    'additional_notes' => $catatanText,
                ]);
            } else {
                DesignRequest::create([
                    'order_id'         => $order->id,
                    'team_name'        => "Katalog",
                    'nama_artikel'     => null,
                    'nama_pemesan'     => null,
                    'jenis_potongan'   => '-',
                    'lengan_jahitan'   => '-',
                    'material'         => '-',
                    'collar_style'     => '-',
                    'priority'         => $data['prioritas'] ?? 'normal',
                    'additional_notes' => $catatanText,
                ]);
            }

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_pembayaran',
                'changed_by' => auth()->id(),
                'notes'      => 'Pesanan (dari keranjang) dibuat oleh customer',
            ]);

            $this->sendSystemMessage($order, 'Pesanan Anda (via keranjang) telah dibuat. Silakan lakukan pembayaran DP minimal 10%.');

            \App\Models\Cart::whereIn('id', $data['cart_item_ids'])->delete();

            return $order;
        });

        Notification::sendToAllStaff(
            'new_order',
            'Pesanan Baru (Keranjang)',
            "Pesanan baru dari <strong>{$order->user->name}</strong> — <strong>{$order->order_number}</strong> — menunggu pembayaran",
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

    public function approve(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'menunggu_pembayaran') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan tidak memungkinkan untuk disetujui.',
            ], 422);
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'dikonfirmasi',
                'confirmed_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'dikonfirmasi',
                'changed_by' => auth()->id(),
                'notes'      => 'Customer menyetujui detail pesanan',
            ]);

            $chat = Chat::firstOrCreate([
                'order_id'    => $order->id,
                'customer_id' => $order->user_id,
            ]);
            ChatMessage::create([
                'chat_id'   => $chat->id,
                'sender_id' => auth()->id(),
                'message'   => 'Saya telah menyetujui detail pesanan. ' . $order->order_number,
            ]);
        });

        Notification::sendToAllStaff(
            'payment_success',
            'Pesanan Dikonfirmasi',
            "Pesanan <strong>{$order->order_number}</strong> telah dikonfirmasi oleh customer.",
            [
                'initials' => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $order->user->role->name,
                'role_initial' => 'C',
                'role_color' => '#16a34a',
                'order_number' => $order->order_number,
            ]
        );

        return response()->json([
            'success'     => true,
            'order'       => $order,
            'orderNumber' => $order->order_number,
        ]);
    }

    public function uploadPaymentProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'menunggu_pembayaran') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dalam status menunggu pembayaran.',
            ], 422);
        }

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $file = $request->file('payment_proof');
        $path = $file->store('payment-proofs/' . $order->order_number, 'public');

        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_proof'      => $path,
                'payment_proof_name' => $file->getClientOriginalName(),
                'amount'             => $order->total_price ?? 0,
                'status'             => 'pending',
            ]
        );

        $chat = Chat::firstOrCreate([
            'order_id'    => $order->id,
            'customer_id' => $order->user_id,
        ]);

        $extension = strtolower($file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png']);
        if ($isImage) {
            $compressedPath = app(ImageService::class)->compressAndStore($file, 'chat-files');
            $chatFilePath = $compressedPath;
            $chatFileSize = Storage::disk('public')->size($compressedPath);
        } else {
            $chatFilePath = $file->store('chat-files', 'public');
            $chatFileSize = $file->getSize();
        }

        ChatMessage::create([
            'chat_id'     => $chat->id,
            'sender_id'   => auth()->id(),
            'message'     => 'Saya telah mengupload bukti pembayaran untuk ' . $order->order_number,
            'file_path'   => $chatFilePath,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $chatFileSize,
            'file_type'   => $file->getMimeType(),
        ]);

        Notification::sendToAllStaff(
            'payment_upload',
            'Bukti Pembayaran',
            "Customer <strong>{$order->user->name}</strong> telah mengupload bukti pembayaran untuk <strong>{$order->order_number}</strong>.",
            [
                'initials'      => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role'          => $order->user->role->name,
                'role_initial'  => 'C',
                'role_color'    => '#16a34a',
                'order_number'  => $order->order_number,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload.',
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
