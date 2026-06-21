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
        $orders = Order::with(['user', 'designRequest', 'orderItem'])
            ->whereIn('status', ['siap_cetak', 'diproduksi'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $dr = $order->designRequest;

                $sizes = [];
                if ($order->orderItem) {
                    $sizes[$order->orderItem->size] = $order->orderItem->qty;
                }

                $stage = $order->production_stage ?? 'printing';

                return [
                    'id'                => $order->id,
                    'order_id'          => $order->order_number,
                    'customer'          => $order->user->name ?? '-',
                    'customer_contact'  => $order->user->phone ?? '-',
                    'team_name'         => $dr?->team_name ?? 'Jersey Custom',
                    'status'            => $order->status,
                    'production_stage'  => $stage,
                    'deadline'          => $order->created_at->addDays(7)->format('d M Y'),
                    'priority'          => $dr?->priority ?? 'Normal',
                    'material'          => $dr?->material ?? '-',
                    'collar'            => $dr?->collar_style ?? '-',
                    'pattern'           => $dr?->motif ?? '-',
                    'notes'             => nl2br(e($dr?->additional_notes ?? $order->notes ?? 'Tidak ada catatan')),
                    'total_qty'         => $order->orderItem?->qty ?? 0,
                    'sizes'             => $sizes,
                    'reference_files'   => $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
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
            'action'  => 'required|in:proses_printing,selesai_printing,proses_jahit,selesai_jahit,proses_qc,selesai_qc',
            'notes'   => 'nullable|string|max:2000',
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
        ];

        $mapping = $statusMap[$data['action']];
        $newOrderStatus = $mapping['order_status'];
        $newStage = $mapping['stage'];

        DB::transaction(function () use ($order, $newOrderStatus, $newStage, $data, $user) {
            $updateData = ['status' => $newOrderStatus];
            if ($newStage) {
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

        return response()->json([
            'success' => true,
            'message' => $newOrderStatus === 'selesai'
                ? 'Pesanan selesai diproduksi.'
                : 'Status produksi berhasil diperbarui.',
            'production_stage' => $newStage,
            'status' => $newOrderStatus,
        ]);
    }
}
