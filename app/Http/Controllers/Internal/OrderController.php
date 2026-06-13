<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function index()
    {
        $dbStatuses = ['pending', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai', 'dibatalkan'];

        $statusMap = [
            'pending'      => 'menunggu_verifikasi',
            'dikonfirmasi' => 'menunggu_acc',
            'disetujui'    => 'tahap_desain',
            'di_design'    => 'tahap_desain',
            'siap_cetak'   => 'tahap_produksi',
            'diproduksi'   => 'tahap_produksi',
            'selesai'      => 'selesai',
            'dibatalkan'   => 'dibatalkan',
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

    public function show($id)
    {
        $record = Order::with([
            'user',
            'orderItem',
            'designRequest',
            'payment',
            'statusHistories.changedBy',
            'productionTask.assignedTo',
        ])->findOrFail($id);

        // Status view mapping
        $badgeStatusMap = [
            'pending'      => ['label' => 'Menunggu Verifikasi', 'badge' => 'yellow'],
            'dikonfirmasi' => ['label' => 'Menunggu ACC',        'badge' => 'orange'],
            'disetujui'    => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'di_design'    => ['label' => 'Tahap Desain',        'badge' => 'blue'],
            'siap_cetak'   => ['label' => 'Produksi',            'badge' => 'purple'],
            'diproduksi'   => ['label' => 'Produksi',            'badge' => 'purple'],
            'selesai'      => ['label' => 'Selesai',             'badge' => 'green'],
            'dibatalkan'   => ['label' => 'Dibatalkan',          'badge' => 'red'],
        ];

        $badgeStatusCodeMap = [
            'pending'      => 'menunggu_verifikasi',
            'dikonfirmasi' => 'menunggu_acc',
            'disetujui'    => 'tahap_desain',
            'di_design'    => 'tahap_desain',
            'siap_cetak'   => 'tahap_produksi',
            'diproduksi'   => 'tahap_produksi',
            'selesai'      => 'selesai',
            'dibatalkan'   => 'dibatalkan',
        ];

        $badgeLabel = $badgeStatusMap[$record->status]['label'] ?? $record->status;
        $badgeType  = $badgeStatusMap[$record->status]['badge'] ?? 'gray';

        // Sizes
        $sizes = [];
        if ($record->orderItem) {
            $sizes[$record->orderItem->size] = $record->orderItem->qty;
        }

        // Design files
        $designFiles = [];
        if ($record->designRequest && $record->designRequest->logo) {
            $designFiles[] = ['name' => basename($record->designRequest->logo)];
        }

        // History notes
        $historyNotes = [];
        foreach ($record->statusHistories as $h) {
            $historyNotes[] = [
                'date' => $h->created_at->format('j M Y, H:i'),
                'user' => $h->changedBy?->name ?? 'Sistem',
                'note' => $h->notes ?? 'Status berubah menjadi ' . ($badgeStatusMap[$h->status]['label'] ?? $h->status),
            ];
        }

        // Status history table
        $statusHistory = [];
        foreach ($record->statusHistories as $h) {
            $statusHistory[] = [
                'date'   => $h->created_at->format('j M Y, H:i'),
                'status' => $badgeStatusCodeMap[$h->status] ?? $h->status,
                'note'   => $h->notes ?? '-',
            ];
        }

        // Stepper
        $stepOrder = ['pending', 'dikonfirmasi', 'disetujui', 'di_design', 'siap_cetak', 'diproduksi', 'selesai'];
        $stepLabels = [
            'pending'      => 'Pesanan Masuk',
            'dikonfirmasi' => 'Dikonfirmasi Admin',
            'disetujui'    => 'Disetujui Customer',
            'di_design'    => 'Proses Desain',
            'siap_cetak'   => 'Siap Cetak',
            'diproduksi'   => 'Produksi',
            'selesai'      => 'Selesai',
        ];

        $currentIdx = array_search($record->status, $stepOrder);

        if ($currentIdx === false && $record->status === 'dibatalkan') {
            $lastNonCancel = null;
            foreach ($record->statusHistories as $h) {
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
        foreach ($record->statusHistories as $h) {
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
            'order_id'      => $record->order_number,
            'last_update'   => $record->updated_at->format('j M Y, H:i'),
            'customer'      => [
                'name'  => $record->user->name ?? '-',
                'email' => $record->user->email ?? '-',
                'phone' => $record->user->phone ?? '-',
            ],
            'product'       => [
                'type'  => $record->designRequest ? 'Jersey Custom' : 'Produk Katalog',
                'sport' => $record->designRequest?->motif ?? 'Umum',
                'notes' => $record->designRequest?->additional_notes ?? $record->notes ?? '-',
            ],
            'sizes'         => $sizes,
            'design_files'  => $designFiles,
            'history_notes' => $historyNotes,
            'status_history' => $statusHistory,
            'payment'       => [
                'subtotal'        => (float) ($record->orderItem?->subtotal ?? 0),
                'biaya_prioritas' => 0,
                'total'           => (float) ($record->payment?->amount ?? $record->total_price ?? 0),
                'method'          => $record->payment?->payment_method ?? '-',
                'status'          => $record->payment?->status === 'success' ? 'lunas' : 'pending',
            ],
        ];

        return view('internal.detail-pesanan', compact('order', 'badgeType', 'badgeLabel', 'steps'));
    }
}
