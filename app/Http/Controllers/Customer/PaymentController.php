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
                $transactionStatus == 'expire' => 'expired',
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
        $orderId = $request->query('order_id');
        return view('customer.payment-finish', compact('orderId'));
    }

    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        return view('customer.payment-finish', compact('orderId'));
    }
}
