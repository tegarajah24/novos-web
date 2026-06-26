<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PublicPagesTest extends DuskTestCase
{
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
                ->assertSee('Tim');
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
                ->waitForText('Log in', 5)
                ->assertSee('Email')
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
                ->waitForText('Register', 5)
                ->assertSee('Name')
                ->assertSee('Email')
                ->assertSee('Password')
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
            $b->visit('/pesan')
                ->waitForText('Buat Pesanan', 5)
                ->assertSee('Pilih Jenis Pesanan');
            echo "\n[✓] PESAN: Halaman pesanan bisa diakses guest (tanpa auth)\n";
        });
    }
}
