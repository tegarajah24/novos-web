<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DesignController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'designRequest'])
            ->whereIn('status', ['disetujui', 'di_design'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $dr = $order->designRequest;
                return [
                    'id'                => $order->id,
                    'order_id'          => $order->order_number,
                    'customer'          => $order->user->name ?? '-',
                    'customer_contact'  => $order->user->phone ?? '-',
                    'team_name'         => $dr?->team_name ?? 'Jersey Custom',
                    'deadline'          => $order->created_at->addDays(7)->format('d M Y'),
                    'priority'          => 'Normal',
                    'material'          => $dr?->material ?? '-',
                    'collar'            => $dr?->collar_style ?? '-',
                    'pattern'           => $dr?->motif ?? '-',
                    'notes'             => nl2br(e($dr?->additional_notes ?? $order->notes ?? 'Tidak ada catatan')),
                    'reference_files'   => $dr?->logo ? [asset('storage/' . $dr->logo)] : [],
                ];
            })
            ->values()
            ->toArray();

        return view('internal.design', compact('orders'));
    }
}
