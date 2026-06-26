<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    public function test_homepage_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Novos');
        });
    }

    public function test_homepage_has_nav(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');
            $html = $browser->driver->getPageSource();
            $this->assertStringContainsString('navbar', strtolower($html), 'Navbar harus ada');
            echo "\n[✓] ASSETS: Navbar terload\n";
        });
    }
}
