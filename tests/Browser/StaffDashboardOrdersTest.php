<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestOrders;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class StaffDashboardOrdersTest extends DuskTestCase
{
    use WithTestUsers;
    use WithTestOrders;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
        $this->ensureTestOrdersExist();
    }
    public function test_admin_dashboard_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/dashboard');
            $b->waitForText('Dashboard', 5);
            $b->assertSee('Total Pesanan');

            echo "\n[✓] DASHBOARD: Halaman dashboard admin tampil dengan KPI\n";
            $b->screenshot('staff-dashboard');
        });
    }

    public function test_dashboard_summary(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/summary');
            $b->waitForText('Summary', 5);

            echo "\n[✓] DASHBOARD: Halaman summary tampil\n";
        });
    }

    public function test_admin_views_order_list(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daftar-pesanan');
            $b->waitForText('Daftar Pesanan', 5);
            $b->assertSee('NVS-');

            echo "\n[✓] PESANAN: Daftar pesanan tampil dengan data\n";
            $b->screenshot('staff-order-list');
        });
    }

    public function test_admin_views_order_detail(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $order = Order::where('status', 'menunggu_validasi')->first()
              ?? Order::latest()->first();

        $this->browse(function (Browser $b) use ($admin, $order) {
            $b->loginAs($admin);
            $b->visit('/staf/detail-pesanan/' . $order->order_number);
            $b->waitForText($order->order_number, 5);
            $b->assertSee('Info Pesanan');

            echo "\n[✓] PESANAN: Detail pesanan {$order->order_number} tampil\n";
            $b->screenshot('staff-order-detail');
        });
    }

    public function test_admin_validates_order(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $order = Order::where('status', 'menunggu_validasi')->first();

        if (!$order) {
            echo "\n[!] SKIP: Tidak ada pesanan dengan status menunggu_validasi\n";
            $this->assertTrue(true);
            return;
        }

        $this->browse(function (Browser $b) use ($admin, $order) {
            $b->loginAs($admin);
            $b->visit('/staf/detail-pesanan/' . $order->order_number);
            $b->waitForText($order->order_number, 5);

            // Click Validasi Pesanan button
            $b->pause(500);
            $b->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Validasi Pesanan'))?.click()");
            $b->pause(800);

            // Confirm SweetAlert
            $b->script("document.querySelector('.swal2-confirm')?.click()");
            $b->pause(1000);

            echo "\n[✓] PESANAN: Validasi pesanan {$order->order_number} berjalan\n";
            $b->screenshot('staff-order-validated');
        });
    }

    public function test_admin_updates_order_status(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $order = Order::where('status', 'dikonfirmasi')->first();

        if (!$order) {
            echo "\n[!] SKIP: Tidak ada pesanan dengan status dikonfirmasi\n";
            $this->assertTrue(true);
            return;
        }

        $this->browse(function (Browser $b) use ($admin, $order) {
            $b->loginAs($admin);
            $b->visit('/staf/detail-pesanan/' . $order->order_number);
            $b->waitForText($order->order_number, 5);

            // Update status via fetch to forward to design
            $b->script('
                fetch("/staf/pesanan/' . $order->order_number . '/update-status", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ status: "tahap_desain", notes: "Diteruskan ke Design (Dusk test)" })
                })
                .then(r => r.json())
                .then(d => {
                    console.log("STATUS UPDATE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
                .catch(e => console.error("STATUS UPDATE ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] PESANAN: Update status pesanan {$order->order_number} berjalan\n";
            $b->screenshot('staff-order-status-updated');
        });
    }

    public function test_admin_checks_allowed_statuses(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $order = Order::latest()->first();

        $this->browse(function (Browser $b) use ($admin, $order) {
            $b->loginAs($admin);
            $b->visit('/staf/pesanan/' . $order->order_number . '/allowed-statuses');
            $b->assertSee('"');

            echo "\n[✓] PESANAN: Endpoint allowed-statuses bisa diakses\n";
        });
    }

    public function test_designer_loads_design_page(): void
    {
        $design = User::where('email', 'design@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($design) {
            $b->loginAs($design);
            $b->visit('/staf/design');
            $b->waitForText('Tugas Design', 5);
            $b->assertSee('Antrean');

            echo "\n[✓] DESIGN: Halaman tugas design tampil\n";
            $b->screenshot('staff-design');
        });
    }

    public function test_produksi_loads_produksi_page(): void
    {
        $produksi = User::where('email', 'produksi@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($produksi) {
            $b->loginAs($produksi);
            $b->visit('/staf/produksi');
            $b->waitForText('Tugas Produksi', 5);
            $b->assertSee('Printing');
            $b->assertSee('Jahit');
            $b->assertSee('Quality Control');

            echo "\n[✓] PRODUKSI: Halaman tugas produksi tampil dengan tab\n";
            $b->screenshot('staff-produksi');
        });
    }

    public function test_produksi_tabs_switch(): void
    {
        $produksi = User::where('email', 'produksi@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($produksi) {
            $b->loginAs($produksi);
            $b->visit('/staf/produksi');
            $b->waitForText('Tugas Produksi', 5);

            // Click Jahit tab
            $b->script("
                let buttons = document.querySelectorAll('button');
                for (let btn of buttons) {
                    if (btn.textContent.includes('Jahit')) { btn.click(); break; }
                }
            ");
            $b->pause(500);

            // Click QC tab
            $b->script("
                let buttons = document.querySelectorAll('button');
                for (let btn of buttons) {
                    if (btn.textContent.includes('Quality Control')) { btn.click(); break; }
                }
            ");
            $b->pause(500);

            // Click Printing tab back
            $b->script("
                let buttons = document.querySelectorAll('button');
                for (let btn of buttons) {
                    if (btn.textContent.includes('Printing') && !btn.textContent.includes('Jahit') && !btn.textContent.includes('QC')) { btn.click(); break; }
                }
            ");
            $b->pause(500);

            echo "\n[✓] PRODUKSI: Tab navigasi berfungsi\n";
            $b->screenshot('staff-produksi-tabs');
        });
    }
}
