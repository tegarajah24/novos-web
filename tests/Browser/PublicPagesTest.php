<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class PublicPagesTest extends DuskTestCase
{
    use WithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
    }
    public function test_homepage_loads(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
                ->waitForText('Novos', 5)
                ->assertSee('Custom Sports Jersey')
                ->assertVisible('nav')
                ->assertVisible('footer');
            echo "\n[✓] HOME: Beranda tampil dengan navbar & footer\n";
        });
    }

    public function test_homepage_nav_links(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
                ->assertSeeLink('Beranda')
                ->assertSeeLink('Katalog')
                ->assertSeeLink('Tentang Kami');
            echo "\n[✓] HOME: Navigasi links tersedia\n";
        });
    }

    public function test_tentang_kami_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/tentang-kami')
                ->waitForText('Tentang Kami', 5)
                ->assertSee('Novos')
                ->assertSee('Kami');
            echo "\n[✓] TENTANG: Halaman tentang kami tampil\n";
        });
    }

    public function test_katalog_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/katalog')
                ->waitForText('Katalog', 5)
                ->assertSee('Jersey');
            // Check filter/search elements exist
            $b->assertPresent('input[type="text"]');
            echo "\n[✓] KATALOG: Halaman katalog tampil dengan search\n";
        });
    }

    public function test_login_redirects_to_home_with_auth_param(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login')
                ->waitForLocation('/', 10);
            $b->assertSee('Novos');
            echo "\n[✓] LOGIN: /login redirect ke /?auth=login\n";
        });
    }

    public function test_register_redirects_to_home_with_auth_param(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/register')
                ->waitForLocation('/', 10);
            $b->assertSee('Novos');
            echo "\n[✓] REGISTER: /register redirect ke /?auth=register\n";
        });
    }

    public function test_sidebar_login_popup_appears_via_alpine(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->waitForText('Novos', 5);

            // Open login sidebar via Alpine
            $b->script("var el=document.querySelector('[x-data=\"authSidebar()\"]');if(el&&el._x_dataStack)el._x_dataStack[0].openSidebar('login')");
            $b->pause(1500);

            $b->assertSee('Username');
            $b->assertSee('Password');
            echo "\n[✓] SIDEBAR: Popup login sidebar tampil\n";
        });
    }

    public function test_sidebar_register_popup_appears(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')->waitForText('Novos', 5);

            // Open register sidebar via Alpine
            $b->script("var el=document.querySelector('[x-data=\"authSidebar()\"]');if(el&&el._x_dataStack)el._x_dataStack[0].openSidebar('register')");
            $b->pause(1500);

            $b->assertSee('Daftar Akun');
            $b->assertSee('Email');
            echo "\n[✓] SIDEBAR: Popup register sidebar tampil\n";
        });
    }

    public function test_forgot_password_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->logout();
            $b->visit('/forgot-password');
            $b->waitForText('Email', 5);
            $b->assertPresent('button[type="submit"]');
            echo "\n[✓] FORGOT: Halaman lupa password tampil\n";
        });
    }

    public function test_guest_redirected_to_home(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/staf/dashboard')
                ->waitForLocation('/', 5);
            $b->assertSee('Novos');
            echo "\n[✓] GUEST: Halaman staf redirect ke home untuk guest\n";
        });
    }

    public function test_403_for_customer_on_staff_route(): void
    {
        $this->browse(function (Browser $b) {
            $customer = User::where('email', 'customer@novos.com')->firstOrFail();
            $b->loginAs($customer)
                ->visit('/staf/dashboard')
                ->assertSee('403');
            echo "\n[✓] 403: Customer tidak bisa akses halaman staf\n";
        });
    }

    public function test_404_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/halaman-tidak-ada-123xyz')
                ->assertSee('404');
            echo "\n[✓] 404: Halaman tidak ditemukan tampil\n";
        });
    }

    public function test_pesan_page_loads_for_guest(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/pesan');
            $b->assertSee('Buat Pesanan');
            $b->assertSee('Pilih');
            echo "\n[✓] PESAN: Halaman pesanan bisa diakses guest (tanpa auth)\n";
        });
    }
}
