<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevisionRequest;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderStatusHistory;

class TrackingController extends Controller
{
    public function index(Request $request)
    {
        $orderData = null;
        $shareUrl = null;

        if ($request->q) {
            $order = Order::with(['designRequest', 'orderItems'])
                ->where('order_number', $request->q)
                ->where('user_id', auth()->id())
                ->first();

            if ($order) {
                $designFiles = [];
                if ($order->designRequest && $order->designRequest->design_files) {
                    $designFiles = collect($order->designRequest->design_files)->map(fn($f) => [
                        'name' => $f['name'],
                        'url'  => asset('storage/' . $f['path']),
                    ])->values()->toArray();
                }

                $orderData = [
                    'id'           => $order->order_number,
                    'date'         => $order->created_at->format('j F Y'),
                    'status'       => $order->status,
                    'design_files' => $designFiles,
                    'team_name'    => $order->designRequest?->team_name,
                ];

                $shareUrl = $order->share_token
                    ? route('tracking.shared', $order->share_token)
                    : null;
            }
        }

        return view('customer.tracking', compact('orderData', 'shareUrl'));
    }

    public function search(Request $request)
    {
        $query = $request->q;

        if (!$query) {
            return response()->json(['found' => false, 'message' => 'Masukkan nomor pesanan']);
        }

        $order = Order::with(['designRequest', 'orderItems'])
            ->where('order_number', $query)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json([
                'found' => false,
                'message' => 'Pesanan dengan nomor "' . e($query) . '" tidak ditemukan',
            ]);
        }

        $designFiles = [];
        if ($order->designRequest && $order->designRequest->design_files) {
            $designFiles = collect($order->designRequest->design_files)->map(fn($f) => [
                'name' => $f['name'],
                'url'  => asset('storage/' . $f['path']),
            ])->values()->toArray();
        }

        $orderData = [
            'id'           => $order->order_number,
            'date'         => $order->created_at->format('j F Y'),
            'status'       => $order->status,
            'design_files' => $designFiles,
            'team_name'    => $order->designRequest?->team_name,
        ];

        $shareUrl = $order->share_token
            ? route('tracking.shared', $order->share_token)
            : null;

        return response()->json([
            'found' => true,
            'data'  => $orderData,
            'share_url' => $shareUrl,
        ]);
    }

    public function generateToken($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$order->share_token) {
            $order->update(['share_token' => Str::random(32)]);
        }

        return response()->json([
            'url' => route('tracking.shared', $order->share_token),
        ]);
    }

    public function shared($token)
    {
        $order = Order::with(['designRequest', 'orderItems'])
            ->where('share_token', $token)
            ->firstOrFail();

        $designFiles = [];
        if ($order->designRequest && $order->designRequest->design_files) {
            $designFiles = collect($order->designRequest->design_files)->map(fn($f) => [
                'name' => $f['name'],
                'url'  => asset('storage/' . $f['path']),
            ])->values()->toArray();
        }

        $orderData = [
            'id'           => $order->order_number,
            'date'         => $order->created_at->format('j F Y'),
            'status'       => $order->status,
            'design_files' => $designFiles,
            'team_name'    => $order->designRequest?->team_name,
        ];

        $shared = true;

        return view('customer.tracking', compact('orderData', 'shared'));
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

        $currentUser = auth()->user();
        Notification::sendToAllStaff(
            'design_acc',
            'Desain Disetujui',
            "Customer <strong>{$currentUser->name}</strong> menyetujui desain untuk <strong>{$order->order_number}</strong> — status berubah ke <strong>{$nextStatus}</strong>.",
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
                'role_initial' => 'C',
                'role_color' => '#6b46c1',
                'order_number' => $order->order_number,
            ]
        );

        return response()->json([
            'success' => true,
            'status'  => $nextStatus,
        ]);
    }

    public function revision(RevisionRequest $request, $id)
    {
        $note = $request->validated()['note'];

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

        $currentUser = auth()->user();
        Notification::sendToAllStaff(
            'design_revision',
            'Revisi Desain',
            "Customer <strong>{$currentUser->name}</strong> meminta revisi untuk <strong>{$order->order_number}</strong>: {$request->note}",
            [
                'initials' => collect(explode(' ', $currentUser->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                'role' => auth()->user()->role->name,
                'role_initial' => 'C',
                'role_color' => '#d97706',
                'order_number' => $order->order_number,
            ]
        );

        return response()->json(['success' => true]);
    }
}
