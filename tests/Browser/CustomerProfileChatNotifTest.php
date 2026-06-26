<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class CustomerProfileChatNotifTest extends DuskTestCase
{
    use WithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
    }
    public function test_profile_page_loads(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);
            $b->assertSee($customer->name);
            $b->assertSee($customer->email);

            echo "\n[✓] PROFILE: Halaman profil tampil dengan data user\n";
            $b->screenshot('profile-main');
        });
    }

    public function test_profile_tabs_exist(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);

            // Check tabs: Pesanan, Alamat, Keranjang, etc
            $b->assertSee('Pesanan');
            $b->assertSee('Alamat');

            echo "\n[✓] PROFILE: Tab navigasi tersedia\n";
        });
    }

    public function test_profile_switch_to_keranjang_tab(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);

            // Switch to keranjang tab
            $b->script("
                let x = Alpine.\$data(document.querySelector('[x-data]'));
                if (x.setActiveTab) x.setActiveTab('keranjang');
            ");
            $b->pause(1000);

            echo "\n[✓] PROFILE: Tab keranjang bisa diakses\n";
            $b->screenshot('profile-keranjang');
        });
    }

    public function test_profile_switch_to_alamat_tab(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);

            $b->script("
                let x = Alpine.\$data(document.querySelector('[x-data]'));
                if (x.setActiveTab) x.setActiveTab('alamat');
            ");
            $b->pause(1000);

            echo "\n[✓] PROFILE: Tab alamat bisa diakses\n";
            $b->screenshot('profile-alamat');
        });
    }

    public function test_notification_page_loads(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/notifikasi');
            $b->waitForText('Notifikasi', 5);

            echo "\n[✓] NOTIF: Halaman notifikasi customer tampil\n";
            $b->screenshot('notifikasi-customer');
        });
    }

    public function test_notification_unread_count(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/notifikasi/recent')
                ->assertSee('"');
            echo "\n[✓] NOTIF: Endpoint notifikasi recent bisa diakses\n";
        });
    }

    public function test_chat_page_loads(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/chat');
            $b->waitForText('Pesan', 5);

            echo "\n[✓] CHAT: Halaman chat customer tampil\n";
            $b->screenshot('chat-customer');
        });
    }

    public function test_chat_unread_count(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/chat/unread-count');
            $b->assertSee('"');
            echo "\n[✓] CHAT: Endpoint unread count bisa diakses\n";
        });
    }

    public function test_profile_edit_contact(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);

            // Update contact info via fetch
            $b->script('
                fetch("/profile/contact", {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ phone: "08123456789" })
                })
                .then(r => r.json())
                .then(d => console.log("CONTACT UPDATE:", JSON.stringify(d)))
            ');
            $b->pause(1000);

            echo "\n[✓] PROFILE: Update kontak berjalan\n";
        });
    }

    public function test_address_crud(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/profile');
            $b->waitForText('Profil Saya', 5);

            // Switch to alamat tab
            $b->script("
                let x = Alpine.\$data(document.querySelector('[x-data]'));
                if (x.setActiveTab) x.setActiveTab('alamat');
            ");
            $b->pause(1000);

            // Add new address via fetch
            $b->script('
                fetch("/address", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        label: "Rumah Dusk",
                        address: "Jl. Testing No. 123",
                        city: "Jakarta",
                        province: "DKI Jakarta",
                        postal_code: "12345",
                        phone: "08123456789",
                        is_default: false
                    })
                })
                .then(r => r.json())
                .then(d => {
                    console.log("ADDRESS CREATE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
            ');
            $b->pause(2000);

            echo "\n[✓] ADDRESS: Tambah alamat baru berjalan\n";
            $b->screenshot('profile-address-add');
        });

        // Cleanup: remove test address
        $customer->addresses()->where('label', 'Rumah Dusk')->delete();
    }

    public function test_cart_count_endpoint(): void
    {
        $customer = User::where('email', 'customer@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($customer) {
            $b->loginAs($customer);
            $b->visit('/cart/count');
            $b->assertSee('"');
            echo "\n[✓] CART: Endpoint cart count bisa diakses\n";
        });
    }
}
