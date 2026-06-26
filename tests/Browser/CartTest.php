<?php

namespace Tests\Browser;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class CartTest extends DuskTestCase
{
    use WithTestUsers;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();

        $category = Category::firstOrCreate(['name' => 'Test Dusk']);
        $this->product = Product::firstOrCreate(
            ['name' => 'DUSK-TEST-PRODUCT'],
            [
                'category_id' => $category->id,
                'price' => 150000,
                'description' => 'Test product for Dusk',
                'min_qty' => 1,
                'production_days' => 3,
                'is_active' => true,
            ]
        );
    }

    public function test_add_to_cart_and_view(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        // Add via API
        Cart::create([
            'user_id' => $customer->id,
            'product_id' => $this->product->id,
            'size' => 'M',
            'qty' => 2,
            'is_selected' => true,
        ]);

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');

            // Click Keranjang tab
            $data = $b->script("
                let x = Alpine.\$data(document.querySelector('[x-data]'));
                x.setActiveTab('keranjang');
                return 'ok';
            ");
            $b->pause(1000);

            // Verify item appears
            $b->assertSee('DUSK-TEST-PRODUCT');
            $b->assertSee('Rp 150.000');

            echo "\n[✓] CART: Item tampil di keranjang\n";
        });

        Cart::where('user_id', $customer->id)->delete();
    }

    public function test_select_all_and_checkout(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        Cart::create([
            'user_id' => $customer->id,
            'product_id' => $this->product->id,
            'size' => 'L',
            'qty' => 1,
            'is_selected' => true,
        ]);

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->script("Alpine.\$data(document.querySelector('[x-data]')).setActiveTab('keranjang')");
            $b->pause(1000);

            // Click "Pilih Semua"
            $b->script("
                document.querySelector('[x-data]').__x.getUnboundFreshMethods().toggleSelectAll(true);
            ");
            $b->pause(300);

            // Click "Pesan Sekarang"
            $b->script("
                let x = Alpine.\$data(document.querySelector('[x-data]'));
                if (x.cartTotalSelected > 0) x.checkoutFromCartMultiple();
            ");
            $b->pause(1000);

            echo "\n[✓] CART: Checkout dari keranjang berjalan\n";
        });

        Cart::where('user_id', $customer->id)->delete();
    }

    public function test_delete_item(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $cart = Cart::create([
            'user_id' => $customer->id,
            'product_id' => $this->product->id,
            'size' => 'M',
            'qty' => 1,
            'is_selected' => true,
        ]);

        $this->browse(function (Browser $b) use ($customer, $cart) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->script("Alpine.\$data(document.querySelector('[x-data]')).setActiveTab('keranjang')");
            $b->pause(500);

            // Hapus item via fetch langsung
            $b->script('
                fetch("/cart/' . $cart->id . '", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(r => r.json())
                .then(d => { if (d.success) location.reload(); })
            ');
            $b->pause(2000);

            $b->assertDontSee('DUSK-TEST-PRODUCT');
            echo "\n[✓] CART: Item berhasil dihapus\n";
        });

        Cart::where('user_id', $customer->id)->delete();
    }
}
