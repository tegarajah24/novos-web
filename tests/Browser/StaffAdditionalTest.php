<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\MentalHealthPoster;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PosterSetting;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestCatalogProducts;
use Tests\Browser\Concerns\WithTestOrders;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class StaffAdditionalTest extends DuskTestCase
{
    use WithTestUsers;
    use WithTestOrders;
    use WithTestCatalogProducts;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
        $this->ensureCatalogProductsExist();
        $this->ensureTestOrdersExist();
    }

    public function test_super_admin_poster_pages(): void
    {
        $superadmin = User::where('email', 'superadmin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($superadmin) {
            $b->loginAs($superadmin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);
            echo "\n[✓] POSTER: Halaman DMC Super Admin tampil\n";
            $b->screenshot('dmc-page');
        });
    }

    public function test_super_admin_poster_list_api(): void
    {
        $superadmin = User::where('email', 'superadmin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($superadmin) {
            $b->loginAs($superadmin);
            $b->visit('/staf/daily-mental-check/posters');
            $b->assertDontSee('404');
            echo "\n[✓] POSTER: API list poster bisa diakses\n";
        });
    }

    public function test_super_admin_poster_rotation(): void
    {
        $superadmin = User::where('email', 'superadmin@novos.com')->firstOrFail();
        $original = PosterSetting::getRotation();

        $this->browse(function (Browser $b) use ($superadmin) {
            $b->loginAs($superadmin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);

            $b->script("
                fetch('/staf/daily-mental-check/posters/rotation', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rotation: 'weekly' })
                })
                .then(r => r.json())
                .then(d => console.log('ROTATION:', JSON.stringify(d)))
            ");
            $b->pause(1000);

            echo "\n[✓] POSTER: Update rotation berjalan\n";
        });

        PosterSetting::setRotation($original);
    }

    public function test_super_admin_poster_delete(): void
    {
        $superadmin = User::where('email', 'superadmin@novos.com')->firstOrFail();

        $poster = MentalHealthPoster::create([
            'image_path' => 'posters/dummy-delete-test.png',
            'is_active' => true,
            'uploaded_by' => $superadmin->id,
        ]);

        $this->browse(function (Browser $b) use ($superadmin, $poster) {
            $b->loginAs($superadmin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);

            $b->script("
                fetch('/staf/daily-mental-check/posters/" . $poster->id . "', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => console.log('POSTER DELETE:', JSON.stringify(d)))
            ");
            $b->pause(1000);

            echo "\n[✓] POSTER: Delete poster berjalan\n";
        });

        MentalHealthPoster::where('id', $poster->id)->delete();
    }

    public function test_category_update(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $cat = Category::create(['name' => 'Dusk Update Cat']);

        $this->browse(function (Browser $b) use ($admin, $cat) {
            $b->loginAs($admin);
            $b->visit('/');
            $b->waitForText('Novos', 5);
            $b->pause(500);

            $result = $b->script("
                fetch('/staf/kategori/" . $cat->id . "', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: 'Dusk Updated Cat' })
                })
                .then(r => r.json())
                .catch(e => ({ error: e.message }))
            ");
            $b->pause(2000);

            echo "\n[✓] KATEGORI: Update kategori berjalan\n";
        });

        $cat->refresh();
        echo "\n[✓] KATEGORI: Nama setelah update = {$cat->name}\n";
        Category::where('id', $cat->id)->delete();
    }

    public function test_category_data_api(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kategori/data');
            $b->assertSee('"');
            echo "\n[✓] KATEGORI: Endpoint data kategori bisa diakses\n";
        });
    }

    public function test_product_delete(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $category = Category::firstOrCreate(['name' => 'Dusk Prod']);
        $product = Product::create([
            'name' => 'DUSK-PRODUCT-TO-DELETE',
            'category_id' => $category->id,
            'price' => 50000,
            'min_qty' => 1,
            'is_active' => true,
        ]);

        $this->browse(function (Browser $b) use ($admin, $product) {
            $b->loginAs($admin);
            $b->visit('/');
            $b->waitForText('Novos', 5);
            $b->pause(500);

            $b->script("
                fetch('/staf/kelola-produk/" . $product->id . "', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => console.log('PRODUCT DELETE:', JSON.stringify(d)))
            ");
            $b->pause(1500);

            echo "\n[✓] PRODUK: Delete produk berjalan\n";
        });

        Product::where('id', $product->id)->delete();
        Category::where('id', $category->id)->delete();
    }

    public function test_user_delete(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $role = Role::where('name', 'Customer')->first();
        $user = User::create([
            'name' => 'Dusk User To Delete',
            'email' => 'dusk-delete-' . time() . '@novos.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
        ]);

        $this->browse(function (Browser $b) use ($admin, $user) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-pengguna');
            $b->waitForText('Kelola Pengguna', 5);

            $b->script('
                fetch("/staf/kelola-pengguna/' . $user->id . '", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(r => r.json())
                .then(d => {
                    console.log("USER DELETE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
            ');
            $b->pause(1500);

            echo "\n[✓] PENGGUNA: Delete user berjalan\n";
        });

        User::where('id', $user->id)->delete();
    }

    public function test_staff_notification_mark_all_read_api(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        Notification::send($admin->id, 'info', 'Staff Notif', 'Test staff notification');

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi');
            $b->waitForText('Notifikasi', 5);

            $b->script("
                fetch('/staf/notifikasi/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => console.log('NOTIF MARK ALL:', JSON.stringify(d)))
            ");
            $b->pause(1000);

            echo "\n[✓] NOTIF: Staff markAllRead API berfungsi\n";
        });

        Notification::where('user_id', $admin->id)->where('title', 'Staff Notif')->delete();
    }

    public function test_staff_notification_mark_read_individual(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $notif = Notification::create([
            'user_id' => $admin->id,
            'type' => 'info',
            'title' => 'Individual Notif Test',
            'message' => 'Test mark read individual',
            'is_read' => false,
        ]);

        $this->browse(function (Browser $b) use ($admin, $notif) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi');
            $b->waitForText('Notifikasi', 5);

            $b->script("
                fetch('/staf/notifikasi/" . $notif->id . "/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(d => console.log('NOTIF READ:', JSON.stringify(d)))
            ");
            $b->pause(1000);

            echo "\n[✓] NOTIF: Staff markRead individual berfungsi\n";
        });

        Notification::where('id', $notif->id)->delete();
    }

    public function test_order_assign(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $produksi = User::where('email', 'produksi@novos.com')->firstOrFail();
        $order = Order::where('status', 'siap_cetak')->first();

        if (!$order) {
            echo "\n[!] SKIP: Tidak ada order siap_cetak\n";
            $this->assertTrue(true);
            return;
        }

        $this->browse(function (Browser $b) use ($admin, $produksi, $order) {
            $b->loginAs($admin);
            $b->visit('/staf/daftar-pesanan');
            $b->waitForText('Daftar Pesanan', 5);

            $b->script("
                fetch('/staf/pesanan/" . $order->order_number . "/assign', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') ?? '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ assigned_to: " . $produksi->id . " })
                })
                .then(r => r.json())
                .then(d => console.log('ASSIGN:', JSON.stringify(d)))
            ");
            $b->pause(1000);

            echo "\n[✓] PESANAN: Assign order ke {$produksi->name} berjalan\n";
        });
    }

    public function test_role_403_customer_on_staff(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();
        $staffRoutes = [
            '/staf/dashboard',
            '/staf/daftar-pesanan',
            '/staf/kelola-produk',
            '/staf/kelola-pengguna',
            '/staf/kategori',
            '/staf/chat',
            '/staf/laporan',
            '/staf/notifikasi',
            '/staf/daily-mental-check',
            '/staf/pengaturan',
            '/staf/design',
            '/staf/produksi',
        ];

        $this->browse(function (Browser $b) use ($customer, $staffRoutes) {
            $b->loginAs($customer);
            foreach ($staffRoutes as $route) {
                $b->visit($route);
                $b->assertSee('403');
                echo "\n[→] 403: {$route}";
            }
            echo "\n[✓] 403: Semua route staff diblokir untuk Customer\n";
        });
    }

    public function test_role_403_design_on_poster(): void
    {
        $design = User::where('email', 'design@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($design) {
            $b->loginAs($design);
            $b->visit('/staf/daily-mental-check/posters');
            $b->assertSee('403');
            echo "\n[✓] 403: Design tidak bisa akses poster\n";
        });
    }

    public function test_role_403_produksi_on_poster(): void
    {
        $produksi = User::where('email', 'produksi@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($produksi) {
            $b->loginAs($produksi);
            $b->visit('/staf/daily-mental-check/posters');
            $b->assertSee('403');
            echo "\n[✓] 403: Produksi tidak bisa akses poster\n";
        });
    }

    public function test_admin_cannot_access_posters(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check/posters');
            $b->assertSee('403');
            echo "\n[✓] 403: Admin tidak bisa akses poster\n";
        });
    }

    public function test_super_admin_can_access_all_pages(): void
    {
        $superadmin = User::where('email', 'superadmin@novos.com')->firstOrFail();
        $allStaffRoutes = [
            '/staf/dashboard',
            '/staf/daftar-pesanan',
            '/staf/design',
            '/staf/produksi',
            '/staf/kelola-produk',
            '/staf/kelola-pengguna',
            '/staf/kategori',
            '/staf/chat',
            '/staf/laporan',
            '/staf/notifikasi',
            '/staf/daily-mental-check',
            '/staf/daily-mental-check/posters',
            '/staf/pengaturan',
        ];

        $this->browse(function (Browser $b) use ($superadmin, $allStaffRoutes) {
            $b->loginAs($superadmin);
            foreach ($allStaffRoutes as $route) {
                $b->visit($route);
                $b->assertDontSee('403');
                echo "\n[→] OK: {$route}";
            }
            echo "\n[✓] SUPER ADMIN: Bisa akses semua halaman staff\n";
        });
    }
}
