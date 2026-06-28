<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Role;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans
    ) {}

    public function snapToken(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $midtransOrderId = 'ORDER-' . $order->id . '-' . now()->timestamp;

        $payment = $order->payment;
        if (!$payment) {
            $payment = Payment::create([
                'order_id'         => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'amount'           => $order->total_price,
                'status'           => 'pending',
            ]);
        } else {
            $payment->update(['midtrans_order_id' => $midtransOrderId]);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
            ],
        ];

        try {
            $snapToken = $this->midtrans->createSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans snapToken error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params,
                'server_key' => config('midtrans.server_key'),
                'client_key' => config('midtrans.client_key'),
                'is_production' => config('midtrans.is_production'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke payment gateway: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'snap_token' => $snapToken,
            'order_id'   => $order->id,
            'midtrans_order_id' => $midtransOrderId,
            'order_number' => $order->order_number,
        ]);
    }

    public function approveAndPay(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'menunggu_pembayaran') {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan tidak memungkinkan untuk pembayaran.',
            ], 422);
        }

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => 'menunggu_pembayaran',
            'changed_by' => auth()->id(),
            'notes'      => 'Customer menyetujui detail pesanan dan melanjutkan ke pembayaran',
        ]);

        $midtransOrderId = 'ORDER-' . $order->id . '-' . now()->timestamp;

        $payment = $order->payment;
        if (!$payment) {
            $payment = Payment::create([
                'order_id'          => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'amount'            => $order->total_price,
                'status'            => 'pending',
            ]);
        } else {
            $payment->update(['midtrans_order_id' => $midtransOrderId]);
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
            ],
        ];

        try {
            $snapToken = $this->midtrans->createSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans approveAndPay error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params,
                'server_key' => config('midtrans.server_key'),
                'client_key' => config('midtrans.client_key'),
                'is_production' => config('midtrans.is_production'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke payment gateway: ' . $e->getMessage(),
            ], 500);
        }

        $chat = Chat::firstOrCreate([
            'order_id'    => $order->id,
            'customer_id' => $order->user_id,
        ]);

        ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $order->user_id,
            'message'   => 'Saya telah menyetujui detail pesanan dan melanjutkan ke pembayaran.',
        ]);

        return response()->json([
            'success'     => true,
            'snap_token' => $snapToken,
            'order_id'   => $order->id,
            'midtrans_order_id' => $midtransOrderId,
            'order_number' => $order->order_number,
        ]);
    }

    public function callback(Request $request)
    {
        try {
            $notification = $this->midtrans->handleNotification();

            $orderId = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $fraudStatus = $notification->fraud_status;

            $payment = Payment::where('midtrans_order_id', $orderId)->first();

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $status = match (true) {
                $transactionStatus == 'capture' && $fraudStatus == 'accept' => 'success',
                $transactionStatus == 'settlement' => 'success',
                $transactionStatus == 'pending' => 'pending',
                in_array($transactionStatus, ['deny', 'cancel', 'expire']) => 'failed',
                default => $payment->status,
            };

            $payment->update([
                'status'                  => $status,
                'payment_method'          => $paymentType,
                'midtrans_transaction_id' => $notification->transaction_id,
                'paid_at'                 => $status === 'success' ? now() : $payment->paid_at,
            ]);

            if ($status === 'success') {
                $payment->order->update(['status' => 'dikonfirmasi']);

                DB::table('order_status_histories')->insert([
                    'order_id'   => $payment->order_id,
                    'status'     => 'dikonfirmasi',
                    'changed_by' => $payment->order->user_id,
                    'notes'      => 'Pembayaran berhasil dikonfirmasi via Midtrans',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $order = $payment->order;
                $chat = Chat::firstOrCreate([
                    'order_id'    => $order->id,
                    'customer_id' => $order->user_id,
                ]);
                DB::table('chat_messages')->insert([
                    'chat_id'    => $chat->id,
                    'sender_id'  => $order->user_id,
                    'message'    => 'Pembayaran untuk pesanan ' . $order->order_number . ' telah berhasil dikonfirmasi.',
                    'is_read'    => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Notification::sendToAllStaff(
                    'payment_success',
                    'Pembayaran Dikonfirmasi',
                    "Pembayaran untuk <strong>{$order->order_number}</strong> telah berhasil dikonfirmasi.",
                    [
                        'initials' => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                        'role' => Role::customerName(),
                        'role_initial' => 'C',
                        'role_color' => '#16a34a',
                        'order_number' => $order->order_number,
                    ]
                );

                Notification::sendToCustomer(
                    $order->user_id,
                    'payment_success',
                    'Pembayaran Berhasil',
                    'Pembayaran untuk pesanan ' . $order->order_number . ' telah berhasil dikonfirmasi. Pesanan Anda akan segera diproses.',
                    [
                        'order_number' => $order->order_number,
                    ]
                );
            }

            if (in_array($status, ['failed', 'expired'])) {
                $order = $payment->order;

                $order->update(['status' => 'dibatalkan']);

                OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'status'     => 'dibatalkan',
                    'changed_by' => $order->user_id,
                    'notes'      => 'Pembayaran ' . $status . ' — otomatis dibatalkan oleh sistem',
                ]);

                Notification::sendToCustomer(
                    $order->user_id,
                    'payment_failed',
                    'Pembayaran Gagal',
                    'Pembayaran untuk ' . $order->order_number . ' ' . $status . '. Pesanan dibatalkan.',
                    [
                        'order_number' => $order->order_number,
                    ]
                );

                Notification::sendToAllStaff(
                    'payment_failed',
                    'Pembayaran Gagal',
                    "Pembayaran untuk <strong>{$order->order_number}</strong> {$status}.",
                    [
                        'initials' => collect(explode(' ', $order->user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode(''),
                        'role' => Role::customerName(),
                        'role_initial' => 'C',
                        'role_color' => '#dc2626',
                        'order_number' => $order->order_number,
                    ]
                );
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function finish(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        $orderNumber = null;

        if ($midtransOrderId) {
            $payment = Payment::where('midtrans_order_id', $midtransOrderId)->first();

            if ($payment && $payment->status !== 'success') {
                // Verifikasi status ke Midtrans
                try {
                    $midtransService = app(MidtransService::class);
                    $transactionStatus = $midtransService->checkTransactionStatus($midtransOrderId);
                    $midtransStatus = $transactionStatus->transaction_status ?? null;

                    if ($midtransStatus !== 'settlement' && $midtransStatus !== 'capture') {
                        return redirect()->route('tracking', ['q' => $payment->order->order_number])
                            ->with('error', 'Pembayaran belum dikonfirmasi oleh sistem.');
                    }
                } catch (\Exception $e) {
                    return redirect()->route('tracking', ['q' => $payment->order->order_number])
                        ->with('error', 'Gagal memverifikasi pembayaran. Silakan coba lagi.');
                }

                $payment->update([
                    'status' => 'success',
                    'paid_at' => now(),
                ]);

                $order = $payment->order;
                $order->update(['status' => 'dikonfirmasi']);
                $orderNumber = $order->order_number;

                OrderStatusHistory::create([
                    'order_id'   => $order->id,
                    'status'     => 'dikonfirmasi',
                    'changed_by' => $order->user_id,
                    'notes'      => 'Pembayaran berhasil dikonfirmasi',
                ]);

                $chat = Chat::firstOrCreate([
                    'order_id'    => $order->id,
                    'customer_id' => $order->user_id,
                ]);
                ChatMessage::create([
                    'chat_id'   => $chat->id,
                    'sender_id' => $order->user_id,
                    'message'   => 'Pembayaran untuk pesanan ' . $order->order_number . ' telah berhasil dikonfirmasi.',
                ]);
            } elseif ($payment && $payment->status === 'success') {
                $orderNumber = $payment->order->order_number;
            }
        }

        if ($orderNumber) {
            return redirect()->route('tracking', ['q' => $orderNumber]);
        }

        return redirect()->route('tracking');
    }

    public function unfinish(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        $orderNumber = null;
        if ($midtransOrderId) {
            $payment = Payment::where('midtrans_order_id', $midtransOrderId)->first();
            $orderNumber = $payment?->order?->order_number;
        }
        return view('customer.payment-finish', compact('orderNumber'));
    }

    public function error(Request $request)
    {
        $midtransOrderId = $request->query('order_id');
        $orderNumber = null;
        if ($midtransOrderId) {
            $payment = Payment::where('midtrans_order_id', $midtransOrderId)->first();
            $orderNumber = $payment?->order?->order_number;
        }
        return view('customer.payment-finish', compact('orderNumber'));
    }
}
