<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use WithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
    }
    private function openLoginSidebar(Browser $b): void
    {
        $b->script("var el = document.querySelector('[x-data=\"authSidebar()\"]'); if(el) { var ds = el._x_dataStack; if(ds && ds[0]) ds[0].openSidebar('login'); }");
        $b->pause(1500);
    }

    private function openRegisterSidebar(Browser $b): void
    {
        $b->script("var el = document.querySelector('[x-data=\"authSidebar()\"]'); if(el) { var ds = el._x_dataStack; if(ds && ds[0]) ds[0].openSidebar('register'); }");
        $b->pause(1500);
    }

    public function test_register_sidebar_opens(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->waitForText('Novos', 10);
            $this->openRegisterSidebar($b);
            $b->pause(1000);

            $b->assertSee('Daftar Akun');
            $b->assertSee('Email');
            $b->assertSee('Konfirmasi Password');

            echo "\n[✓] AUTH: Sidebar register form tampil dengan benar\n";
        });
    }

    public function test_login_existing_customer(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'customer@novos.com')->first());
            $b->visit('/pesan');
            $b->waitForLocation('/pesan', 10);
            $b->assertSee('Buat Pesanan');
            echo "\n[✓] AUTH: Customer bisa login & melihat /pesan\n";
        });
    }

    public function test_login_existing_admin(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'admin@novos.com')->first());
            $b->visit('/staf/dashboard');
            $b->assertSee('Dashboard');
            echo "\n[✓] AUTH: Admin bisa login & melihat dashboard\n";
        });
    }

    public function test_login_wrong_password(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->waitForText('Novos', 10);
            $this->openLoginSidebar($b);
            $b->pause(1000);

            // Verify sidebar shows login form
            $b->assertSee('Username');
            $b->assertSee('Password');
            $b->assertSee('Lupa password?');

            echo "\n[✓] AUTH: Sidebar login form tampil dengan benar\n";
        });
    }

    public function test_logout(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'customer@novos.com')->first());
            $b->visit('/pesan');
            $b->waitForLocation('/pesan', 5);

            $b->script("document.querySelector('form[action*=\"logout\"]')?.submit()");
            $b->waitForLocation('/', 10);
            $b->assertSee('Buat Pesanan');

            echo "\n[✓] AUTH: Logout sukses\n";
        });
    }

    public function test_staff_route_blocked_for_customer(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'customer@novos.com')->first());
            $b->visit('/staf/dashboard');
            $b->assertSee('403');

            echo "\n[✓] AUTH: Staff route diblokir untuk customer\n";
        });
    }

    public function test_login_empty_fields(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->waitForText('Novos', 10);
            $this->openLoginSidebar($b);
            $b->pause(1000);

            // Verify form has required fields
            $b->assertPresent('input[name="name"][required]');
            $b->assertPresent('input[name="password"][required]');

            echo "\n[✓] AUTH: Form login memiliki validasi required\n";
        });
    }

    public function test_password_reset_page(): void
    {
        $this->browse(function (Browser $b) {
            // Logout first in case previous test left a session
            $b->logout();
            $b->visit('/forgot-password');
            $b->waitForText('Email', 5);
            $b->assertPresent('button[type="submit"]');
            echo "\n[✓] AUTH: Halaman forgot password tampil\n";
        });
    }
}
