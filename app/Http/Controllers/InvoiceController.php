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

        // Group items by size + customizations combination
        $groupedItems = [];
        $excludeKeys = ['tipe_bawahan', 'size_bawahan'];
        foreach ($items as $item) {
            $cust = $item->customizations ?? [];
            $filtered = array_filter($cust, fn($v, $k) => $v && !in_array($k, $excludeKeys), ARRAY_FILTER_USE_BOTH);
            ksort($filtered);
            $key = $item->size . '|' . json_encode($filtered);
            if (!isset($groupedItems[$key])) {
                $groupedItems[$key] = [
                    'size'           => $item->size,
                    'customizations' => $filtered,
                    'qty'            => 0,
                    'price'          => $item->price,
                    'subtotal'       => 0,
                ];
            }
            $groupedItems[$key]['qty']++;
            $groupedItems[$key]['subtotal'] += $item->price;
        }
        $groupedItems = collect($groupedItems)->values();

        $companyName      = Setting::get('company_name', 'NOVOS');
        $companyPhone     = Setting::get('company_phone', '081399903888');
        $companyBank      = Setting::get('company_bank_info', '');
        $companyInstagram = Setting::get('company_instagram', '');
        $companyAddress   = Setting::get('company_address', 'Jl. Gelora Indah 2 No.11, Mangunjaya., BANYUMAS, JAWA TENGAH, 53114');
        $companyEmail     = Setting::get('company_email', 'cvmerdekaberdikarisejahtera@gmail.com');
        $companyNpwp      = Setting::get('company_npwp', '21.157.880.2-521.000');

        $customerAddress = \App\Models\CustomerAddress::where('user_id', $order->user_id)
            ->where('is_primary', true)
            ->first() ?? \App\Models\CustomerAddress::where('user_id', $order->user_id)->first();

        $terbilang = $this->terbilang($subtotal);

        return [
            'order'             => $order,
            'design'            => $design,
            'items'             => $items,
            'grouped_items'     => $groupedItems,
            'payment'           => $payment,
            'subtotal'          => $subtotal,
            'dp_paid'           => $dpPaid,
            'sisa_bayar'        => $sisaBayar,
            'company_name'      => $companyName,
            'company_phone'     => $companyPhone,
            'company_bank'      => $companyBank,
            'company_instagram' => $companyInstagram,
            'company_address'   => $companyAddress,
            'company_email'     => $companyEmail,
            'company_npwp'      => $companyNpwp,
            'customer_address'  => $customerAddress,
            'terbilang'         => $terbilang,
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

    private function penyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->penyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
            $temp = $this->penyebut((int)($nilai/10)) . " puluh" . $this->penyebut($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->penyebut($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->penyebut((int)($nilai/100)) . " ratus" . $this->penyebut($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->penyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->penyebut((int)($nilai/1000)) . " ribu" . $this->penyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->penyebut((int)($nilai/1000000)) . " juta" . $this->penyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->penyebut((int)($nilai/1000000000)) . " milyar" . $this->penyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->penyebut((int)($nilai/1000000000000)) . " trilyun" . $this->penyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
    }

    private function terbilang($nilai) {
        if($nilai<0) {
            $hasil = "minus ". trim($this->penyebut($nilai));
        } else {
            $hasil = trim($this->penyebut($nilai));
        }     
        return strtoupper($hasil . " RUPIAH");
    }
}
