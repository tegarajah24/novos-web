<?php

namespace Tests\Browser\Concerns;

use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductionTask;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Carbon;

trait WithTestCatalogOrders
{
    protected function ensureCatalogOrdersExist(): void
    {
        $today = now()->format('Ymd');

        if (Order::where('order_number', "NVS-{$today}-007")->exists()) {
            return;
        }

        $customer = User::where('email', 'customer@novos.com')->first();
        $admin    = User::where('email', 'admin@novos.com')->first();
        $design   = User::where('email', 'design@novos.com')->first();
        $produksi = User::where('email', 'produksi@novos.com')->first();

        if (!$customer || !$admin || !$design || !$produksi) {
            return;
        }

        $running   = Product::where('name', 'Novos Running Pro')->first();
        $sepakbola = Product::where('name', 'Novos Jersey Stadion')->first();
        $futsal    = Product::where('name', 'Novos Futsal Pro')->first();
        $price     = 100000;

        // ── Catalog Order 1: menunggu_pembayaran ──
        $o1 = Order::create([
            'user_id'      => $customer->id,
            'order_number' => "NVS-{$today}-005",
            'status'       => 'menunggu_pembayaran',
            'total_price'  => 200000,
            'notes'        => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
        ]);
        OrderItem::create(['order_id' => $o1->id, 'size' => 'M (Novos Running Pro)', 'qty' => 2, 'price_per_item' => $price, 'subtotal' => 2 * $price]);
        DesignRequest::create([
            'order_id' => $o1->id, 'team_name' => 'Katalog',
            'jenis_potongan' => 'REGULER', 'lengan_jahitan' => 'REGULER OVERDECK', 'material' => 'MILANO PREMIUM', 'collar_style' => 'O-NECK V.1',
        ]);
        $this->recordHistoryCatalog($o1->id, 'menunggu_pembayaran', $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');

        // ── Catalog Order 2: dikonfirmasi ──
        $o2 = Order::create([
            'user_id'      => $customer->id,
            'order_number' => "NVS-{$today}-006",
            'status'       => 'dikonfirmasi',
            'total_price'  => 500000,
            'confirmed_at' => Carbon::now()->subDay(),
            'notes'        => "Checkout dari Keranjang (2 Produk).\nProduk 1: Novos Jersey Stadion\nProduk 2: Novos Futsal Pro",
            'admin_notes'  => 'Prioritas: Express (Rp 50.000)',
        ]);
        OrderItem::create(['order_id' => $o2->id, 'size' => 'L (Novos Jersey Stadion)', 'qty' => 3, 'price_per_item' => $price, 'subtotal' => 3 * $price]);
        OrderItem::create(['order_id' => $o2->id, 'size' => 'M (Novos Futsal Pro)',    'qty' => 2, 'price_per_item' => $price, 'subtotal' => 2 * $price]);
        DesignRequest::create([
            'order_id' => $o2->id, 'team_name' => 'Katalog',
            'jenis_potongan' => 'REGULER', 'lengan_jahitan' => 'REGULER OVERDECK', 'material' => 'DRIFIT POLYESTER', 'collar_style' => 'V-NECK',
        ]);
        $this->recordHistoryCatalog($o2->id, 'menunggu_pembayaran', $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');
        $this->recordHistoryCatalog($o2->id, 'dikonfirmasi',        $customer->id, 'Pesanan dikonfirmasi');

        // ── Catalog Order 3: siap_cetak ──
        $o3 = Order::create([
            'user_id'      => $customer->id,
            'order_number' => "NVS-{$today}-007",
            'status'       => 'siap_cetak',
            'total_price'  => 500000,
            'production_stage' => 'printing',
            'confirmed_at' => Carbon::now()->subDays(5),
            'notes'        => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
        ]);
        OrderItem::create(['order_id' => $o3->id, 'size' => 'XL (Novos Running Pro)', 'qty' => 5, 'price_per_item' => $price, 'subtotal' => 5 * $price]);
        DesignRequest::create([
            'order_id' => $o3->id, 'team_name' => 'Katalog',
            'jenis_potongan' => 'REGULER', 'lengan_jahitan' => 'REGULER OVERDECK', 'material' => 'MILANO PREMIUM', 'collar_style' => 'O-NECK V.1',
        ]);
        $this->recordHistoryCatalog($o3->id, 'menunggu_pembayaran', $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');
        $this->recordHistoryCatalog($o3->id, 'dikonfirmasi',        $customer->id, 'Pesanan dikonfirmasi');
        $this->recordHistoryCatalog($o3->id, 'disetujui',           $admin->id,    'Teruskan ke Design');
        $this->recordHistoryCatalog($o3->id, 'di_design',           $admin->id,    'Dikerjakan Design');
        $this->recordHistoryCatalog($o3->id, 'siap_cetak',          $design->id,   'Design selesai');
        ProductionTask::create([
            'order_id'    => $o3->id,
            'assigned_to' => $produksi->id,
            'status'      => 'pending',
            'notes'       => 'STAGE:printing',
        ]);
    }

    private function recordHistoryCatalog(int $orderId, string $status, int $changedBy, string $notes): void
    {
        OrderStatusHistory::create([
            'order_id'   => $orderId,
            'status'     => $status,
            'changed_by' => $changedBy,
            'notes'      => $notes,
        ]);
    }
}
