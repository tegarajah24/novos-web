<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    public function test_register_new_customer(): void
    {
        $email = 'dusk-reg-' . time() . '@test.com';

        $this->browse(function (Browser $b) use ($email) {
            $b->visit('/register');
            $b->waitForText('Register', 5);

            $b->type('name', 'Dusk Register Test');
            $b->type('email', $email);
            $b->type('password', 'password123');
            $b->type('password_confirmation', 'password123');
            $b->press('Register');
            $b->waitForLocation('/pesan', 10);

            $this->assertStringContainsString('/pesan', $b->driver->getCurrentURL());
            echo "\n[✓] AUTH: Register sukses dengan email {$email}\n";
        });

        User::where('email', $email)->delete();
    }

    public function test_login_existing_customer(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login');
            $b->waitForText('Log in', 5);

            $b->type('name', 'customer@novos.com');
            $b->type('password', 'password');
            $b->press('Log in');
            $b->waitForLocation('/pesan', 10);

            echo "\n[✓] AUTH: Login customer sukses\n";
        });
    }

    public function test_login_existing_admin(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login');
            $b->waitForText('Log in', 5);

            $b->type('name', 'admin@novos.com');
            $b->type('password', 'password');
            $b->press('Log in');
            $b->waitForLocation('/staf/dashboard', 10);

            echo "\n[✓] AUTH: Login admin sukses, redirect ke /staf/dashboard\n";
        });
    }

    public function test_login_wrong_password(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login');
            $b->waitForText('Log in', 3);

            $b->type('name', 'customer@novos.com');
            $b->type('password', 'wrongpass');
            $b->press('Log in');
            $b->waitForText('These credentials do not match our records', 5);

            echo "\n[✓] AUTH: Login gagal dengan password salah\n";
        });
    }

    public function test_logout(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'customer@novos.com')->first());
            $b->visit('/pesan');
            $b->waitForLocation('/pesan', 5);

            $b->script("document.querySelector('form[action*=\"logout\"]')?.submit()");
            $b->waitForLocation('/login', 10);

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

    public function test_customer_route_blocked_for_staff(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs(User::where('email', 'admin@novos.com')->first());
            $b->visit('/tracking');
            $b->assertSee('403');

            echo "\n[✓] AUTH: Customer route diblokir untuk staff\n";
        });
    }

    public function test_login_empty_fields(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/login');
            $b->waitForText('Log in', 3);
            $b->press('Log in');
            $b->waitForText('is required', 5);

            echo "\n[✓] AUTH: Validasi login dengan field kosong\n";
        });
    }

    public function test_password_reset_page(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/forgot-password');
            $b->waitForText('Forgot your password', 5)
                ->assertSee('Email')
                ->assertPresent('button[type="submit"]');
            echo "\n[✓] AUTH: Halaman forgot password tampil\n";
        });
    }
}
