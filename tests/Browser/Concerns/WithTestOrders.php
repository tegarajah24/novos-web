<?php

namespace Tests\Browser\Concerns;

use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductionTask;
use App\Models\User;
use Carbon\Carbon;

trait WithTestOrders
{
    protected function ensureTestOrdersExist(): void
    {
        if (Order::count() > 0) {
            return;
        }

        $customer = User::where('email', 'customer@novos.com')->first();
        $admin = User::where('email', 'admin@novos.com')->first();
        $design = User::where('email', 'design@novos.com')->first();
        $produksi = User::where('email', 'produksi@novos.com')->first();

        if (!$customer || !$admin || !$design || !$produksi) {
            return;
        }

        // Order 1: menunggu_pembayaran
        $order1 = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'NVS-' . now()->format('Ymd') . '-001',
            'status' => 'menunggu_pembayaran',
            'total_price' => 1500000,
            'notes' => 'Test order Dusk',
        ]);
        OrderItem::create(['order_id' => $order1->id, 'size' => 'M', 'qty' => 5, 'price_per_item' => 150000, 'subtotal' => 750000]);
        OrderItem::create(['order_id' => $order1->id, 'size' => 'L', 'qty' => 5, 'price_per_item' => 150000, 'subtotal' => 750000]);
        DesignRequest::create(['order_id' => $order1->id, 'team_name' => 'Tim Dusk 1', 'primary_color' => '#1a237e', 'material' => 'MILANO PREMIUM', 'collar_style' => 'O-NECK V.1']);
        $this->recordHistory($order1->id, 'menunggu_pembayaran', $customer->id, 'Pesanan masuk');

        // Order 2: dikonfirmasi
        $order2 = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'NVS-' . now()->format('Ymd') . '-002',
            'status' => 'dikonfirmasi',
            'total_price' => 2000000,
            'confirmed_at' => Carbon::now()->subDay(),
        ]);
        OrderItem::create(['order_id' => $order2->id, 'size' => 'XL', 'qty' => 10, 'price_per_item' => 200000, 'subtotal' => 2000000]);
        DesignRequest::create(['order_id' => $order2->id, 'team_name' => 'Tim Dusk 2', 'primary_color' => '#e53935', 'material' => 'MILANO PREMIUM', 'collar_style' => 'V-Neck']);
        $this->recordHistory($order2->id, 'menunggu_pembayaran', $customer->id, 'Pesanan masuk');
        $this->recordHistory($order2->id, 'dikonfirmasi', $customer->id, 'Pesanan dikonfirmasi');

        // Order 3: siap_cetak (design selesai)
        $order3 = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'NVS-' . now()->format('Ymd') . '-003',
            'status' => 'siap_cetak',
            'production_stage' => 'printing',
            'total_price' => 3750000,
            'confirmed_at' => Carbon::now()->subDays(3),
        ]);
        OrderItem::create(['order_id' => $order3->id, 'size' => 'M', 'qty' => 15, 'price_per_item' => 150000, 'subtotal' => 2250000]);
        OrderItem::create(['order_id' => $order3->id, 'size' => 'L', 'qty' => 10, 'price_per_item' => 150000, 'subtotal' => 1500000]);
        DesignRequest::create(['order_id' => $order3->id, 'team_name' => 'Tim Dusk 3', 'primary_color' => '#43a047', 'material' => 'Drifit Polyester', 'collar_style' => 'Round Neck']);
        $this->recordHistory($order3->id, 'menunggu_pembayaran', $customer->id, 'Pesanan masuk');
        $this->recordHistory($order3->id, 'dikonfirmasi', $customer->id, 'Pesanan dikonfirmasi');
        $this->recordHistory($order3->id, 'disetujui', $admin->id, 'Ke Design');
        $this->recordHistory($order3->id, 'di_design', $admin->id, 'Ke Design');
        $this->recordHistory($order3->id, 'siap_cetak', $design->id, 'Design selesai');
        ProductionTask::create([
            'order_id' => $order3->id,
            'assigned_to' => $produksi->id,
            'status' => 'pending',
            'notes' => 'STAGE:printing',
        ]);

        // Order 4: diproduksi, stage jahit
        $order4 = Order::create([
            'user_id' => $customer->id,
            'order_number' => 'NVS-' . now()->format('Ymd') . '-004',
            'status' => 'diproduksi',
            'production_stage' => 'jahit',
            'total_price' => 5250000,
            'confirmed_at' => Carbon::now()->subDays(7),
        ]);
        OrderItem::create(['order_id' => $order4->id, 'size' => 'L', 'qty' => 20, 'price_per_item' => 175000, 'subtotal' => 3500000]);
        OrderItem::create(['order_id' => $order4->id, 'size' => 'XL', 'qty' => 10, 'price_per_item' => 175000, 'subtotal' => 1750000]);
        DesignRequest::create(['order_id' => $order4->id, 'team_name' => 'Tim Dusk 4', 'primary_color' => '#6a1b9a', 'material' => 'Mesh Polyester', 'collar_style' => 'V-Neck']);
        $this->recordHistory($order4->id, 'menunggu_pembayaran', $customer->id, 'Pesanan masuk');
        $this->recordHistory($order4->id, 'dikonfirmasi', $customer->id, 'Pesanan dikonfirmasi');
        $this->recordHistory($order4->id, 'disetujui', $admin->id, 'Ke Design');
        $this->recordHistory($order4->id, 'di_design', $admin->id, 'Ke Design');
        $this->recordHistory($order4->id, 'siap_cetak', $design->id, 'Design selesai');
        $this->recordHistory($order4->id, 'diproduksi', $admin->id, 'Masuk Produksi');
        ProductionTask::create([
            'order_id' => $order4->id,
            'assigned_to' => $produksi->id,
            'status' => 'dikerjakan',
            'started_at' => Carbon::now()->subDay(),
            'notes' => 'STAGE:jahit',
        ]);
    }

    private function recordHistory(int $orderId, string $status, int $changedBy, string $notes): void
    {
        OrderStatusHistory::create([
            'order_id' => $orderId,
            'status' => $status,
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }
}
