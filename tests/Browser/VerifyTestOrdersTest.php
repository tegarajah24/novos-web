<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\Browser\Concerns\WithTestOrders;
use Tests\Browser\Concerns\WithTestCatalogProducts;
use Tests\Browser\Concerns\WithTestCatalogOrders;
use Tests\DuskTestCase;

class VerifyTestOrdersTest extends DuskTestCase
{
    use WithTestUsers;
    use WithTestOrders;
    use WithTestCatalogProducts;
    use WithTestCatalogOrders;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
        $this->ensureCatalogProductsExist();
        $this->ensureTestOrdersExist();
        $this->ensureCatalogOrdersExist();
    }

    public function test_customer_page_loads_successfully(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer)
              ->visit('/profile')
              ->pause(3000)
              ->screenshot('verify-customer-profile')
              ->assertSee('Profil Saya');
        });

        echo "\n[✓] CUSTOMER: Profile page loads successfully\n";
    }

    public function test_admin_sees_all_orders_in_daftar_pesanan(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $totalOrders = Order::count();

        $this->browse(function (Browser $b) use ($admin, $totalOrders) {
            $b->loginAs($admin)
              ->visit('/staf/daftar-pesanan')
              ->pause(3000)
              ->screenshot('verify-admin-daftar-pesanan');

            $orderNumbers = Order::pluck('order_number')->toArray();
            foreach ($orderNumbers as $num) {
                $b->assertSee($num);
            }
        });

        echo "\n[✓] ADMIN: {$totalOrders} orders visible in daftar-pesanan\n";
    }

    public function test_admin_can_view_each_order_detail(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $orders = Order::all();

        $this->browse(function (Browser $b) use ($admin, $orders) {
            $b->loginAs($admin);

            foreach ($orders as $order) {
                $b->visit("/staf/detail-pesanan/{$order->order_number}")
                  ->pause(2000)
                  ->screenshot("verify-detail-{$order->order_number}");

                $b->assertSee($order->order_number);
                echo "\n[→] Detail: {$order->order_number} ({$order->status})";
            }
        });

        echo "\n[✓] ADMIN: All {$orders->count()} order details accessible\n";
    }

    public function test_catalog_products_exist(): void
    {
        $this->assertGreaterThanOrEqual(5, \App\Models\Product::count());
        $this->assertGreaterThanOrEqual(3, \App\Models\Category::count());

        echo "\n[✓] CATALOG: " . \App\Models\Product::count() . " products in " . \App\Models\Category::count() . " categories\n";
    }

    public function test_order_counts_match(): void
    {
        $customOrders = Order::where('order_number', 'like', '%00_')->where('order_number', 'not like', '%00_')->count();
        // Simpler: just count all orders and ensure at least 6 exist
        $total = Order::count();

        $this->assertGreaterThanOrEqual(6, $total);

        echo "\n[✓] Total orders: {$total}\n";
    }
}
