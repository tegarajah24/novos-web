<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderStatusHistory;
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
                try {
                    $status = $this->midtrans->checkTransactionStatus($midtransOrderId);
                    $transactionStatus = $status->transaction_status;

                    if (in_array($transactionStatus, ['capture', 'settlement'])) {
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
                    }
                } catch (\Exception $e) {
                    // Abaikan error — callback Midtrans akan handle jika nanti reachable
                }
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
