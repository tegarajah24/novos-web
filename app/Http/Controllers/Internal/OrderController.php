<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $dbStatuses = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan'];

        $statusMap = [
            'menunggu_validasi'  => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'tahap_produksi',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];

        $orders = Order::with(['user', 'orderItem', 'designRequest', 'productionTask.assignedTo'])
            ->whereIn('status', $dbStatuses)
            ->latest()
            ->get()
            ->map(function ($order) use ($statusMap) {
                $produk = $order->designRequest
                    ? 'Jersey ' . $order->designRequest->team_name
                    : 'Jersey Custom';

                $assigneeName = null;
                if ($order->productionTask && $order->productionTask->assignedTo) {
                    $assigneeName = $order->productionTask->assignedTo->name;
                }

                return [
                    'order_id' => $order->order_number,
                    'customer' => $order->user->name ?? 'Unknown',
                    'produk'   => $produk,
                    'qty'      => $order->orderItem?->qty ?? 0,
                    'total'    => (float) ($order->total_price ?? 0),
                    'assignee' => $assigneeName,
                    'status'   => $statusMap[$order->status] ?? $order->status,
                ];
            })
            ->toArray();

        $colorKeys = ['purple', 'blue', 'orange', 'green', 'gray'];
        $assignees = User::with('role')
            ->whereHas('role', fn($q) => $q->whereIn('name', ['Admin', 'Design', 'Produksi', 'Manager']))
            ->get()
            ->map(function ($user) use ($colorKeys) {
                return [
                    'name'  => $user->name,
                    'color' => $colorKeys[array_rand($colorKeys)],
                ];
            })
            ->toArray();

        return view('internal.daftar-pesanan', compact('orders', 'assignees'));
    }

    public function show(Order $order)
    {
        $rawStatus = $order->status;

        $order->load([
            'user',
            'orderItem',
            'designRequest',
            'payment',
            'statusHistories.changedBy',
            'productionTask.assignedTo',
        ]);

        // Status view mapping
        $badgeStatusMap = [
            'menunggu_validasi'  => ['label' => 'Menunggu Verifikasi', 'badge' => 'yellow'],
            'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'badge' => 'orange'],
            'dikonfirmasi'       => ['label' => 'Dikonfirmasi',        'badge' => 'blue'],
            'disetujui'          => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'di_design'          => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'siap_cetak'         => ['label' => 'Produksi',            'badge' => 'purple'],
            'diproduksi'         => ['label' => 'Produksi',            'badge' => 'purple'],
            'selesai'            => ['label' => 'Selesai',             'badge' => 'green'],
            'dibatalkan'         => ['label' => 'Dibatalkan',          'badge' => 'red'],
        ];

        $badgeStatusCodeMap = [
            'menunggu_validasi'  => 'menunggu_verifikasi',
            'menunggu_pembayaran' => 'menunggu_pembayaran',
            'dikonfirmasi'       => 'menunggu_acc',
            'disetujui'          => 'tahap_desain',
            'di_design'          => 'tahap_desain',
            'siap_cetak'         => 'tahap_produksi',
            'diproduksi'         => 'tahap_produksi',
            'selesai'            => 'selesai',
            'dibatalkan'         => 'dibatalkan',
        ];

        $badgeLabel = $badgeStatusMap[$order->status]['label'] ?? $order->status;
        $badgeType  = $badgeStatusMap[$order->status]['badge'] ?? 'gray';

        // Sizes
        $sizes = [];
        if ($order->orderItem) {
            $sizes[$order->orderItem->size] = $order->orderItem->qty;
        }

        // Design files
        $designFiles = [];
        if ($order->designRequest && $order->designRequest->logo) {
            $designFiles[] = ['name' => basename($order->designRequest->logo)];
        }

        // History notes
        $historyNotes = [];
        foreach ($order->statusHistories as $h) {
            $historyNotes[] = [
                'date' => $h->created_at->format('j M Y, H:i'),
                'user' => $h->changedBy?->name ?? 'Sistem',
                'note' => $h->notes ?? 'Status berubah menjadi ' . ($badgeStatusMap[$h->status]['label'] ?? $h->status),
            ];
        }

        // Status history table
        $statusHistory = [];
        foreach ($order->statusHistories as $h) {
            $statusHistory[] = [
                'date'   => $h->created_at->format('j M Y, H:i'),
                'status' => $badgeStatusCodeMap[$h->status] ?? $h->status,
                'note'   => $h->notes ?? '-',
            ];
        }

        // Stepper
        $stepOrder = ['menunggu_validasi', 'menunggu_pembayaran', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
            'menunggu_validasi'  => 'Menunggu Validasi',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi'       => 'Dikonfirmasi',
            'disetujui'          => 'Tahap Desain',
            'di_design'          => 'Proses Desain',
            'siap_cetak'         => 'Siap Cetak',
            'diproduksi'         => 'Produksi',
            'selesai'            => 'Selesai',
        ];

        $currentIdx = array_search($order->status, $stepOrder);

        if ($currentIdx === false && $order->status === 'dibatalkan') {
            $lastNonCancel = null;
            foreach ($order->statusHistories as $h) {
                if ($h->status !== 'dibatalkan') {
                    $lastNonCancel = $h;
                }
            }
            $currentIdx = $lastNonCancel
                ? array_search($lastNonCancel->status, $stepOrder)
                : 0;
            if ($currentIdx === false) $currentIdx = -1;
        } elseif ($currentIdx === false) {
            $currentIdx = -1;
        }

        $stepDates = [];
        foreach ($order->statusHistories as $h) {
            if (!isset($stepDates[$h->status])) {
                $stepDates[$h->status] = $h->created_at->format('j M Y');
            }
        }

        $steps = [];
        foreach ($stepOrder as $idx => $status) {
            $steps[] = [
                'label'   => $stepLabels[$status],
                'date'    => $stepDates[$status] ?? null,
                'done'    => $currentIdx !== false && $idx < $currentIdx,
                'current' => $currentIdx !== false && $idx === $currentIdx,
            ];
        }

        $order = [
            'order_id'      => $order->order_number,
            'last_update'   => $order->updated_at->format('j M Y, H:i'),
            'customer'      => [
                'name'  => $order->user->name ?? '-',
                'email' => $order->user->email ?? '-',
                'phone' => $order->user->phone ?? '-',
            ],
            'product'       => [
                'type'  => $order->designRequest ? 'Jersey Custom' : 'Produk Katalog',
                'sport' => $order->designRequest?->motif ?? 'Umum',
                'notes' => $order->designRequest?->additional_notes ?? $order->notes ?? '-',
            ],
            'sizes'         => $sizes,
            'design_files'  => $designFiles,
            'history_notes' => $historyNotes,
            'status_history' => $statusHistory,
            'payment'       => [
                'subtotal'        => (float) ($order->orderItem?->subtotal ?? 0),
                'biaya_prioritas' => 0,
                'total'           => (float) ($order->payment?->amount ?? $order->total_price ?? 0),
                'method'          => $order->payment?->payment_method ?? '-',
                'status'          => $order->payment?->status === 'success' ? 'lunas' : 'pending',
            ],
        ];

        return view('internal.detail-pesanan', compact('order', 'badgeType', 'badgeLabel', 'steps') + ['rawStatus' => $rawStatus]);
    }

    public function validateOrder(Request $request, Order $order)
    {
        if ($order->status !== 'menunggu_validasi') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan tidak dapat divalidasi.',
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => 'menunggu_pembayaran']);

            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'status'     => 'menunggu_pembayaran',
                'changed_by' => auth()->id(),
                'notes'      => $request->note ?? 'Pesanan divalidasi oleh admin',
            ]);

            $chat = Chat::firstOrCreate([
                'order_id'    => $order->id,
                'customer_id' => $order->user_id,
            ]);

            ChatMessage::create([
                'chat_id'   => $chat->id,
                'sender_id' => auth()->id(),
                'message'   => 'Pesanan ' . $order->order_number . ' telah divalidasi. Silakan lakukan pembayaran.',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil divalidasi.',
        ]);
    }
}
