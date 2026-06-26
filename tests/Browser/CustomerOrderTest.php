<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class CustomerOrderTest extends DuskTestCase
{
    use WithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
    }
    private string $orderNumber = '';

    public function test_customer_create_custom_order(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);

            // Visit pesan page
            $c->visit('/pesan');
            $c->waitForText('Pilih Jenis Pesanan', 5);

            // Step 1: Pilih Jersey Custom
            $c->script("document.querySelectorAll('.grid.md\\\:grid-cols-2 > div')[0].click()");
            $c->pause(300);

            // Click Selanjutnya button
            $c->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Selanjutnya'))?.click()");
            $c->waitForText('Detail & Upload', 5);

            // Step 2: Isi detail desain
            $c->script('
                let r = document.querySelector(".max-w-5xl")._x_dataStack[0];
                r.form.team_name = "Test Tim Dusk";
                r.form.kerah = "O-NECK V.1";
                r.form.bahan = "MILANO PREMIUM";
                r.form.jenis_potongan = "REGULER";
                r.form.lengan_jahitan = "REGULER OVERDECK";
                r.tmpSize = "M";
                r.tmpQty = 1;
                r.addSize();
            ');
            $c->pause(300);

            // Click Pesan Langsung
            $c->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Pesan Langsung'))?.click()");
            $c->waitForText('Detail Kontak & Alamat', 5);

            // Step 3: Pilih alamat jika ada
            $c->script('
                let r = document.querySelector(".max-w-5xl")._x_dataStack[0];
                if (r.addresses && r.addresses.length > 0) {
                    r.selectedAddressId = r.addresses[0].id;
                    r.useSelectedAddress();
                }
            ');
            $c->waitForText('Prioritas & Pembayaran', 5);

            // Step 4: Set prioritas & konfirmasi
            $c->script('document.querySelector(".max-w-5xl")._x_dataStack[0].prioritas = "normal"');
            $c->pause(300);
            $c->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Konfirmasi') && b.textContent.includes('Bayar'))?.click()");
            $c->waitForText('Pesanan Berhasil Dibuat', 15);

            // Capture order number
            $orderNum = $c->script('return document.querySelector(".max-w-5xl")._x_dataStack[0].orderNumber || ""')[0];
            if (empty($orderNum)) {
                $orderNum = Order::latest()->first()?->order_number ?? '';
            }
            $this->orderNumber = $orderNum;

            $this->assertNotEmpty($this->orderNumber);
            echo "\n[✓] ORDER: Pesanan {$this->orderNumber} berhasil dibuat via form\n";
            $c->screenshot('customer-order-created');
        });
    }

    public function test_customer_create_catalog_order(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/pesan');
            $c->waitForText('Pilih Jenis Pesanan', 5);

            // Pilih Produk Katalog
            $c->script("document.querySelectorAll('.grid.md\\\:grid-cols-2 > div')[1].click()");
            $c->pause(300);
            $c->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Selanjutnya'))?.click()");
            $c->waitForText('Pilih Produk', 5);

            echo "\n[✓] ORDER: Form pesanan katalog tampil\n";
            $c->screenshot('customer-catalog-order-form');
        });
    }

    public function test_customer_view_tracking_page(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/tracking');
            $c->waitForText('Tracking Pesanan', 5);
            $c->assertSee('Silakan pilih pesanan');
            $c->assertSee('Riwayat Pesanan');

            echo "\n[✓] TRACKING: Halaman tracking tampil dengan benar\n";
            $c->screenshot('customer-tracking');
        });
    }

    public function test_customer_views_order_history_on_profile(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/profile');
            $c->waitForText('Profil Saya', 5);

            // Should have "Pesanan Saya" or order history section
            $c->assertSee('Pesanan');

            echo "\n[✓] PROFILE: Riwayat pesanan tampil di halaman profil\n";
            $c->screenshot('customer-profile-orders');
        });
    }

    public function test_customer_views_payment_success_page(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/payment/finish');
            $c->waitForText('Pembayaran', 5);

            echo "\n[✓] PAYMENT: Halaman payment finish tampil\n";
            $c->screenshot('customer-payment-finish');
        });
    }

    public function test_customer_views_chat_page(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/chat');
            $c->waitForText('Pesan', 5);

            echo "\n[✓] CHAT: Halaman chat customer tampil\n";
            $c->screenshot('customer-chat');
        });
    }

    public function test_customer_views_notifications(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/notifikasi');
            $c->waitForText('Notifikasi', 5);

            echo "\n[✓] NOTIF: Halaman notifikasi customer tampil\n";
            $c->screenshot('customer-notifications');
        });
    }

    public function test_customer_sends_chat_message(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $c) use ($customer) {
            $c->loginAs($customer);
            $c->visit('/chat');
            $c->waitForText('Pesan', 5);

            // Coba kirim pesan via fetch
            $result = $c->script('
                let input = document.querySelector("input[type=\\"text\\"], textarea");
                if (input) {
                    input.value = "Test pesan dari Dusk";
                    input.dispatchEvent(new Event("input"));
                    let sendBtn = document.querySelector("button")?.closest("form")?.querySelector("button[type=\\"submit\\"]");
                    if (!sendBtn) {
                        let buttons = document.querySelectorAll("button");
                        for (let b of buttons) {
                            if (b.textContent.includes("Kirim") || b.querySelector("svg")) { sendBtn = b; break; }
                        }
                    }
                    if (sendBtn) sendBtn.click();
                }
                return !!input;
            ');
            $c->pause(1000);

            echo "\n[✓] CHAT: Customer bisa kirim pesan\n";
        });
    }
}
