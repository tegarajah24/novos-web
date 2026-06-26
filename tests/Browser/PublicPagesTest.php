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

    public function test_login_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login')
                ->waitForText('Username', 10)
                ->assertSee('Password')
                ->assertSee('Forgot your password')
                ->assertPresent('button[type="submit"]');
            echo "\n[✓] LOGIN: Halaman login tampil dengan form\n";
        });
    }

    public function test_register_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/register')
                ->waitForText('Name', 10)
                ->assertSee('Email')
                ->assertSee('Confirm Password')
                ->assertPresent('button[type="submit"]');
            echo "\n[✓] REGISTER: Halaman register tampil dengan form\n";
        });
    }

    public function test_forgot_password_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/forgot-password')
                ->waitForText('Forgot your password', 5)
                ->assertSee('Email')
                ->assertPresent('button[type="submit"]');
            echo "\n[✓] FORGOT: Halaman lupa password tampil\n";
        });
    }

    public function test_403_forbidden_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/staf/dashboard') // tanpa login harusnya redirect ke login, bukan 403
                ->waitForLocation('/login', 5);
            echo "\n[✓] 403: Halaman staf redirect ke login untuk guest\n";
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
            $b->waitForText('Buat Pesanan', 5);
            $b->pause(2000); // Wait for Alpine.js to initialize
            $b->assertSee('Pilih');
            echo "\n[✓] PESAN: Halaman pesanan bisa diakses guest (tanpa auth)\n";
        });
    }
}
