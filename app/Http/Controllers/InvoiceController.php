<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Ambil order + data invoice untuk ditampilkan/digenerate.
     */
    private function resolveOrder(string $orderNumber, bool $isInternal = false): Order
    {
        $order = Order::with([
            'user',
            'designRequest',
            'orderItems',
            'itemDetails',
            'payment',
        ])->where('order_number', $orderNumber)->firstOrFail();

        if (!$isInternal) {
            // customer hanya boleh lihat milik sendiri
            abort_if($order->user_id !== auth()->id(), 403);
        }

        return $order;
    }

    /**
     * Siapkan array data invoice.
     */
    private function buildInvoiceData(Order $order): array
    {
        $design   = $order->designRequest;
        $items    = $order->itemDetails;
        $payment  = $order->payment;
        $subtotal = (float) $order->total_price;

        // DP yang sudah dikonfirmasi admin (success)
        $dpPaid = $payment && $payment->status === 'lunas'
            ? (float) ($payment->dp_amount ?? 0)
            : 0;

        // Jika payment punya dp_amount field; fallback ke 10% subtotal jika belum ada
        if ($payment && isset($payment->dp_amount)) {
            $dpPaid = (float) $payment->dp_amount;
        }

        $sisaBayar = max(0, $subtotal - $dpPaid);

        $companyName  = Setting::get('company_name', 'Novos Jersey');
        $companyPhone = Setting::get('company_phone', '');
        $companyBank  = Setting::get('company_bank_info', '');

        return [
            'order'         => $order,
            'design'        => $design,
            'items'         => $items,
            'payment'       => $payment,
            'subtotal'      => $subtotal,
            'dp_paid'       => $dpPaid,
            'sisa_bayar'    => $sisaBayar,
            'company_name'  => $companyName,
            'company_phone' => $companyPhone,
            'company_bank'  => $companyBank,
        ];
    }

    // ─────────────────────────────────────────────
    // Customer routes
    // ─────────────────────────────────────────────

    /** Lihat invoice di browser (customer). */
    public function show(string $orderNumber)
    {
        $order = $this->resolveOrder($orderNumber, false);
        $data  = $this->buildInvoiceData($order);
        return view('invoice.invoice', $data);
    }

    /** Download PDF (customer). */
    public function download(string $orderNumber)
    {
        $order = $this->resolveOrder($orderNumber, false);
        $data  = $this->buildInvoiceData($order);

        $pdf = Pdf::loadView('invoice.invoice', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    // ─────────────────────────────────────────────
    // Internal (staf) routes
    // ─────────────────────────────────────────────

    /** Lihat invoice di browser (staf). */
    public function showInternal(string $orderNumber)
    {
        $order = $this->resolveOrder($orderNumber, true);
        $data  = $this->buildInvoiceData($order);
        $data['is_internal'] = true;
        return view('invoice.invoice', $data);
    }

    /** Download PDF (staf). */
    public function downloadInternal(string $orderNumber)
    {
        $order = $this->resolveOrder($orderNumber, true);
        $data  = $this->buildInvoiceData($order);

        $pdf = Pdf::loadView('invoice.invoice', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    /** Simpan jumlah DP yang sudah dibayar (admin). */
    public function saveDp(Request $request, string $orderNumber)
    {
        $request->validate([
            'dp_amount' => 'required|numeric|min:0',
        ]);

        $order = $this->resolveOrder($orderNumber, true);

        $payment = $order->payment ?? new Payment(['order_id' => $order->id]);
        $payment->fill([
            'order_id'      => $order->id,
            'amount'        => (float) $order->total_price,
            'dp_amount'     => (float) $request->dp_amount,
            'payment_method' => 'transfer',
            'notes'         => $request->notes ?? null,
        ]);

        // Tentukan status berdasarkan apakah DP sudah mencukupi pelunasan
        $payment->status = $payment->dp_amount >= $payment->amount ? 'lunas' : 'pending';

        if (!$payment->exists) {
            $payment->save();
        } else {
            $payment->save();
        }

        return response()->json([
            'success'   => true,
            'dp_amount' => $payment->dp_amount,
            'status'    => $payment->status,
        ]);
    }
}
