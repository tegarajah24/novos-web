<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'designRequest', 'orderItems', 'statusHistories'])
            ->whereIn('status', ['siap_cetak', 'diproduksi'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $dr = $order->designRequest;

                $sizes = [];
                foreach ($order->orderItems as $item) {
                    $sizes[$item->size] = $item->qty;
                }

                $stage = $order->production_stage ?? 'printing';

                $priority = 'Normal';
                if ($order->admin_notes && preg_match('/Prioritas: (Express|Super Express)/', $order->admin_notes, $matches)) {
                    $priority = $matches[1];
                }

                // Collect production notes from status histories
                $prodHistoryNotes = $order->statusHistories
                    ->whereIn('status', ['siap_cetak', 'diproduksi'])
                    ->filter(fn($h) => !empty($h->notes) && !str_starts_with($h->notes, 'Status berubah'))
                    ->map(fn($h) => '[' . $h->created_at->format('d M H:i') . '] ' . $h->notes)
                    ->values();

                $originalNotes = $dr?->additional_notes ?? $order->notes;
                $allNotes = $originalNotes
                    ? collect([$originalNotes])->merge($prodHistoryNotes)->implode("\n\n")
                    : ($prodHistoryNotes->isNotEmpty() ? $prodHistoryNotes->implode("\n\n") : 'Tidak ada catatan');

                return [
                    'id'                => $order->id,
                    'order_id'          => $order->order_number,
                    'customer'          => $order->user->name ?? '-',
                    'customer_contact'  => $order->user->phone ?? '-',
                    'team_name'         => $dr?->team_name ?? 'Jersey Custom',
                    'status'            => $order->status,
                    'production_stage'  => $stage,
                    'deadline'          => $order->created_at->addDays(7)->format('d M Y'),
                    'priority'          => $priority,
                    'material'          => $dr?->material ?? '-',
                    'collar'            => $dr?->collar_style ?? '-',
                    'pattern'           => $dr?->motif ?? '-',
                    'notes'             => nl2br(e($allNotes)),
                    'total_qty'         => $order->orderItems->sum('qty'),
                    'sizes'             => $sizes,
                    'reference_files'   => array_merge(
                        $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
                        collect($dr?->design_files ?? [])->map(fn($f) => asset('storage/' . $f['path']))->values()->toArray(),
                    ),
                    'design_files'      => [],
                ];
            })
            ->values()
            ->toArray();

        return view('internal.produksi', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'action'       => 'required|in:proses_printing,selesai_printing,proses_jahit,selesai_jahit,proses_qc,selesai_qc,revisi_qc',
            'notes'        => 'nullable|string|max:2000',
            'target_stage' => 'nullable|required_if:action,revisi_qc|in:jahit,printing',
        ]);

        $user = auth()->user();
        $oldStatus = $order->status;

        $statusMap = [
            'proses_printing'  => ['stage' => 'printing', 'order_status' => 'siap_cetak'],
            'selesai_printing' => ['stage' => 'jahit',    'order_status' => 'diproduksi'],
            'proses_jahit'     => ['stage' => 'jahit',    'order_status' => 'diproduksi'],
            'selesai_jahit'    => ['stage' => 'qc',       'order_status' => 'diproduksi'],
            'proses_qc'        => ['stage' => 'qc',       'order_status' => 'diproduksi'],
            'selesai_qc'       => ['stage' => null,       'order_status' => 'selesai'],
            'revisi_qc'        => ['stage' => null,       'order_status' => 'diproduksi'], // stage from target_stage
        ];

        $mapping = $statusMap[$data['action']];
        $newOrderStatus = $mapping['order_status'];
        $newStage = $mapping['stage'];

        DB::transaction(function () use ($order, $newOrderStatus, $newStage, $data, $user) {
            $updateData = ['status' => $newOrderStatus];

            if ($data['action'] === 'revisi_qc') {
                $updateData['production_stage'] = $data['target_stage'] ?? 'jahit';
            } elseif ($newStage) {
                $updateData['production_stage'] = $newStage;
            } else {
                $updateData['production_stage'] = null;
            }

            $order->update($updateData);

            $order->statusHistories()->create([
                'status'     => $newOrderStatus,
                'changed_by' => $user->id,
                'notes'      => $data['notes'] ?? ('Produksi: ' . str_replace('_', ' ', $data['action'])),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        $notifType = str_starts_with($data['action'], 'selesai') ? 'production_done' : 'production_update';

        Notification::sendToAllStaff(
            $notifType,
            $newOrderStatus === 'selesai' ? 'Pesanan Selesai' : 'Update Produksi',
            $newOrderStatus === 'selesai'
                ? "Pesanan <strong>{$order->order_number}</strong> telah selesai diproduksi."
                : "Produksi pesanan <strong>{$order->order_number}</strong> telah diupdate oleh <strong>{$user->name}</strong>.",
            [
                'initials' => collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => $user->role->name,
                'role_initial' => substr($user->role->name, 0, 1),
                'role_color' => '#0284c7',
                'order_number' => $order->order_number,
            ]
        );

        if ($newOrderStatus === 'selesai') {
            Notification::sendToCustomer(
                $order->user_id,
                'production_done',
                'Pesanan Selesai',
                'Pesanan ' . $order->order_number . ' telah selesai diproduksi dan siap untuk dikirim/diambil.',
                [
                    'order_number' => $order->order_number,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => $newOrderStatus === 'selesai'
                ? 'Pesanan selesai diproduksi.'
                : 'Status produksi berhasil diperbarui.',
            'production_stage' => $newStage,
            'status' => $newOrderStatus,
            'target_stage' => $data['action'] === 'revisi_qc' ? ($data['target_stage'] ?? 'jahit') : null,
        ]);
    }
}
