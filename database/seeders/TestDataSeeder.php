<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductionTask;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureRoles();
        $this->ensureUsers();
        $this->ensureCategoriesAndProducts();
        $this->ensureCustomOrders();
        $this->ensureCatalogOrders();

        $this->command?->info('✅ Test data seeded: 3 custom orders + 3 catalog orders');
    }

    protected function ensureRoles(): void
    {
        foreach (['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi', 'Customer'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }

    protected function ensureUsers(): void
    {
        $roles = [
            'superadmin@novos.com' => 'Super Admin',
            'admin@novos.com'     => 'Admin',
            'customer@novos.com'  => 'Customer',
            'design@novos.com'    => 'Design',
            'produksi@novos.com'  => 'Produksi',
            'testing@novos.com'   => 'Customer',
        ];

        foreach ($roles as $email => $roleName) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => match ($email) {
                        'superadmin@novos.com' => 'Super Admin',
                        'admin@novos.com'     => 'Admin Novos',
                        'customer@novos.com'  => 'Customer Test',
                        'design@novos.com'    => 'Tim Design',
                        'produksi@novos.com'  => 'Tim Produksi',
                        'testing@novos.com'   => 'Testing User',
                        default               => ucfirst($roleName),
                    },
                    'password' => bcrypt('password'),
                    'role_id'  => Role::where('name', $roleName)->first()->id,
                ]
            );
        }
    }

    protected function ensureCategoriesAndProducts(): void
    {
        $categories = ['Running', 'Sepak Bola', 'Futsal', 'Basket', 'Training'];

        foreach ($categories as $catName) {
            $category = Category::firstOrCreate(['name' => $catName]);

            $productNames = match ($catName) {
                'Running'   => ['Novos Running Pro', 'Novos Running Lite'],
                'Sepak Bola' => ['Novos Jersey Stadion', 'Novos Jersey Elite'],
                'Futsal'    => ['Novos Futsal Pro'],
                'Basket'    => ['Novos Basket Pro', 'Novos Basket Retro'],
                'Training'  => ['Novos Training Set'],
                default     => [],
            };

            foreach ($productNames as $name) {
                Product::firstOrCreate(
                    ['name' => $name],
                    [
                        'category_id'     => $category->id,
                        'description'     => "Jersey {$catName} kualitas premium.",
                        'price'           => 100000,
                        'image'           => null,
                        'min_qty'         => 1,
                        'production_days' => 7,
                        'is_active'       => true,
                    ]
                );
            }
        }
    }

    protected function ensureCustomOrders(): void
    {
        $customer  = User::where('email', 'customer@novos.com')->first();
        $admin     = User::where('email', 'admin@novos.com')->first();
        $design    = User::where('email', 'design@novos.com')->first();
        $produksi  = User::where('email', 'produksi@novos.com')->first();

        // ── Custom Order 1: menunggu_pembayaran ──
        $o1 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-001'],
            [
                'user_id'     => $customer->id,
                'status'      => 'menunggu_pembayaran',
                'total_price' => 1700000,
                'notes'       => "Jenis Potongan: REGULER\nModel Lengan & Jahitan: REGULER OVERDECK\nCatatan: Logo di dada kiri",
                'admin_notes' => 'Prioritas: Normal (0)',
            ]
        );
        if ($o1->wasRecentlyCreated) {
            OrderItem::create(['order_id' => $o1->id, 'size' => 'M',  'qty' => 10, 'price_per_item' => 85000, 'subtotal' => 850000]);
            OrderItem::create(['order_id' => $o1->id, 'size' => 'L',  'qty' => 10, 'price_per_item' => 85000, 'subtotal' => 850000]);
            DesignRequest::create([
                'order_id'       => $o1->id,
                'team_name'      => 'Garuda FC',
                'no_punggung'    => 10,
                'jenis_potongan' => 'REGULER',
                'lengan_jahitan' => 'REGULER OVERDECK',
                'material'       => 'MILANO PREMIUM',
                'collar_style'   => 'O-NECK V.1',
                'primary_color'  => '#1a237e',
                'secondary_color' => '#ffffff',
            ]);
            $this->recordHistory($o1->id, 'menunggu_pembayaran', $customer->id, 'Pesanan dibuat oleh customer');
        }

        // ── Custom Order 2: dikonfirmasi ──
        $o2 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-002'],
            [
                'user_id'     => $customer->id,
                'status'      => 'dikonfirmasi',
                'total_price' => 2600000,
                'notes'       => "Jenis Potongan: SLIMFIT CEWE\nModel Lengan & Jahitan: RAGLAN B PAKAI MANSET",
                'admin_notes' => 'Prioritas: Normal (0)',
            ]
        );
        if ($o2->wasRecentlyCreated) {
            OrderItem::create(['order_id' => $o2->id, 'size' => 'S',  'qty' => 10, 'price_per_item' => 85000, 'subtotal' => 850000]);
            OrderItem::create(['order_id' => $o2->id, 'size' => 'M',  'qty' => 15, 'price_per_item' => 85000, 'subtotal' => 1275000]);
            OrderItem::create(['order_id' => $o2->id, 'size' => 'L',  'qty' => 5,  'price_per_item' => 85000, 'subtotal' => 425000]);
            DesignRequest::create([
                'order_id'       => $o2->id,
                'team_name'      => 'Elang Putih',
                'jenis_potongan' => 'SLIMFIT CEWE',
                'lengan_jahitan' => 'RAGLAN B PAKAI MANSET',
                'material'       => 'DRIFIT POLYESTER',
                'collar_style'   => 'V-NECK',
                'primary_color'  => '#e53935',
            ]);
            $this->recordHistory($o2->id, 'menunggu_pembayaran',   $customer->id, 'Pesanan dibuat oleh customer');
            $this->recordHistory($o2->id, 'dikonfirmasi',          $customer->id, 'Pesanan dikonfirmasi');
        }

        // ── Custom Order 3: dikonfirmasi ──
        $o3 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-003'],
            [
                'user_id'      => $customer->id,
                'status'       => 'dikonfirmasi',
                'total_price'  => 1325000,
                'confirmed_at' => Carbon::now()->subDays(2),
                'notes'        => "Jenis Potongan: REGULER\nModel Lengan & Jahitan: REGULER OVERDECK",
                'admin_notes'  => 'Prioritas: Normal (0)',
            ]
        );
        if ($o3->wasRecentlyCreated) {
            OrderItem::create(['order_id' => $o3->id, 'size' => 'M',  'qty' => 15, 'price_per_item' => 85000, 'subtotal' => 1275000]);
            OrderItem::create(['order_id' => $o3->id, 'size' => 'XL', 'qty' => 1,  'price_per_item' => 85000, 'subtotal' => 85000]);
            DesignRequest::create([
                'order_id'       => $o3->id,
                'team_name'      => 'Macan Putih',
                'jenis_potongan' => 'REGULER',
                'lengan_jahitan' => 'REGULER OVERDECK',
                'material'       => 'MILANO PREMIUM',
                'collar_style'   => 'O-NECK V.1',
                'primary_color'  => '#43a047',
            ]);
            $this->recordHistory($o3->id, 'menunggu_pembayaran', $customer->id, 'Pesanan dibuat oleh customer');
            $this->recordHistory($o3->id, 'dikonfirmasi',        $customer->id, 'Pesanan dikonfirmasi');
        }
    }

    protected function ensureCatalogOrders(): void
    {
        $customer  = User::where('email', 'customer@novos.com')->first();
        $admin     = User::where('email', 'admin@novos.com')->first();
        $design    = User::where('email', 'design@novos.com')->first();
        $produksi  = User::where('email', 'produksi@novos.com')->first();

        $products = Product::whereIn('name', ['Novos Running Pro', 'Novos Jersey Stadion', 'Novos Futsal Pro'])->get();
        $running  = $products->where('name', 'Novos Running Pro')->first();
        $sepakbola = $products->where('name', 'Novos Jersey Stadion')->first();
        $futsal   = $products->where('name', 'Novos Futsal Pro')->first();
        $price    = 100000;

        // ── Catalog Order 1: menunggu_pembayaran (single product) ──
        $o1 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-005'],
            [
                'user_id'     => $customer->id,
                'status'      => 'menunggu_pembayaran',
                'total_price' => 200000,
                'notes'       => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
                'admin_notes' => 'Prioritas: Normal (Rp 0)',
            ]
        );
        if ($o1->wasRecentlyCreated) {
            OrderItem::create([
                'order_id'       => $o1->id,
                'size'           => 'M (Novos Running Pro)',
                'qty'            => 2,
                'price_per_item' => $price,
                'subtotal'       => 2 * $price,
            ]);
            DesignRequest::create([
                'order_id'       => $o1->id,
                'team_name'      => 'Katalog',
                'jenis_potongan' => 'REGULER',
                'lengan_jahitan' => 'REGULER OVERDECK',
                'material'       => 'MILANO PREMIUM',
                'collar_style'   => 'O-NECK V.1',
                'additional_notes' => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
            ]);
            $this->recordHistory($o1->id, 'menunggu_pembayaran', $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');
        }

        // ── Catalog Order 2: dikonfirmasi (multiple products) ──
        $o2 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-006'],
            [
                'user_id'      => $customer->id,
                'status'       => 'dikonfirmasi',
                'total_price'  => 500000,
                'confirmed_at' => Carbon::now()->subDay(),
                'notes'        => "Checkout dari Keranjang (2 Produk).\nProduk 1: Novos Jersey Stadion\nProduk 2: Novos Futsal Pro",
                'admin_notes'  => 'Prioritas: Express (Rp 50.000)',
            ]
        );
        if ($o2->wasRecentlyCreated) {
            OrderItem::create([
                'order_id'       => $o2->id,
                'size'           => 'L (Novos Jersey Stadion)',
                'qty'            => 3,
                'price_per_item' => $price,
                'subtotal'       => 3 * $price,
            ]);
            OrderItem::create([
                'order_id'       => $o2->id,
                'size'           => 'M (Novos Futsal Pro)',
                'qty'            => 2,
                'price_per_item' => $price,
                'subtotal'       => 2 * $price,
            ]);
            DesignRequest::create([
                'order_id'       => $o2->id,
                'team_name'      => 'Katalog',
                'jenis_potongan' => 'REGULER',
                'lengan_jahitan' => 'REGULER OVERDECK',
                'material'       => 'DRIFIT POLYESTER',
                'collar_style'   => 'V-NECK',
                'additional_notes' => "Checkout dari Keranjang (2 Produk).\nProduk 1: Novos Jersey Stadion\nProduk 2: Novos Futsal Pro",
            ]);
            $this->recordHistory($o2->id, 'menunggu_validasi',   $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');
            $this->recordHistory($o2->id, 'menunggu_pembayaran', $admin->id,    'Admin validasi');
            $this->recordHistory($o2->id, 'dikonfirmasi',        $customer->id, 'Pembayaran dikonfirmasi');
        }

        // ── Catalog Order 3: siap_cetak ──
        $o3 = Order::firstOrCreate(
            ['order_number' => 'NVS-20260703-007'],
            [
                'user_id'      => $customer->id,
                'status'       => 'siap_cetak',
                'total_price'  => 500000,
                'production_stage' => 'printing',
                'confirmed_at' => Carbon::now()->subDays(5),
                'notes'        => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
                'admin_notes'  => 'Prioritas: Normal (Rp 0)',
            ]
        );
        if ($o3->wasRecentlyCreated) {
            OrderItem::create([
                'order_id'       => $o3->id,
                'size'           => 'XL (Novos Running Pro)',
                'qty'            => 5,
                'price_per_item' => $price,
                'subtotal'       => 5 * $price,
            ]);
            DesignRequest::create([
                'order_id'       => $o3->id,
                'team_name'      => 'Katalog',
                'jenis_potongan' => 'REGULER',
                'lengan_jahitan' => 'REGULER OVERDECK',
                'material'       => 'MILANO PREMIUM',
                'collar_style'   => 'O-NECK V.1',
                'additional_notes' => "Checkout dari Keranjang (1 Produk).\nProduk 1: Novos Running Pro",
            ]);
            $this->recordHistory($o3->id, 'menunggu_validasi',   $customer->id, 'Pesanan (dari keranjang) dibuat oleh customer');
            $this->recordHistory($o3->id, 'menunggu_pembayaran', $admin->id,    'Admin validasi');
            $this->recordHistory($o3->id, 'dikonfirmasi',        $customer->id, 'Pembayaran dikonfirmasi');
            $this->recordHistory($o3->id, 'disetujui',           $admin->id,    'Teruskan ke Design');
            $this->recordHistory($o3->id, 'di_design',           $admin->id,    'Dikerjakan Design');
            $this->recordHistory($o3->id, 'siap_cetak',          $design->id,   'Design selesai');
            ProductionTask::create([
                'order_id'    => $o3->id,
                'assigned_to' => $produksi->id,
                'status'      => 'pending',
                'notes'       => 'STAGE:printing',
            ]);
        }
    }

    protected function recordHistory(int $orderId, string $status, int $changedBy, string $notes): void
    {
        OrderStatusHistory::firstOrCreate(
            ['order_id' => $orderId, 'status' => $status],
            [
                'changed_by' => $changedBy,
                'notes'      => $notes,
            ]
        );
    }
}
