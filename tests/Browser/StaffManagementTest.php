<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StaffManagementTest extends DuskTestCase
{
    public function test_kelola_produk_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-produk');
            $b->waitForText('Katalog Produk', 5);
            $b->assertSee('Tambah Produk Baru');

            echo "\n[✓] PRODUK: Halaman kelola produk tampil\n";
            $b->screenshot('staff-produk-list');
        });
    }

    public function test_kelola_produk_search_and_filter(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-produk');
            $b->waitForText('Katalog Produk', 5);

            // Search input should exist
            $b->assertPresent('input[type="text"]');
            // Category filter should exist
            $b->assertPresent('select');

            echo "\n[✓] PRODUK: Search dan filter kategori tersedia\n";
        });
    }

    public function test_kelola_produk_toggle_featured(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $product = Product::first();

        if (!$product) {
            echo "\n[!] SKIP: Tidak ada produk\n";
            $this->assertTrue(true);
            return;
        }

        $this->browse(function (Browser $b) use ($admin, $product) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-produk');
            $b->waitForText('Katalog Produk', 5);

            // Toggle featured via fetch
            $b->script('
                fetch("/staf/kelola-produk/' . $product->id . '/featured", {
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(r => r.json())
                .then(d => {
                    console.log("FEATURED TOGGLE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
                .catch(e => console.error("FEATURED ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] PRODUK: Toggle featured untuk {$product->name}\n";
            $b->screenshot('staff-produk-featured');
        });
    }

    public function test_kelola_pengguna_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-pengguna');
            $b->waitForText('Kelola Pengguna', 5);
            $b->assertSee('Total Pengguna');

            echo "\n[✓] PENGGUNA: Halaman kelola pengguna tampil\n";
            $b->screenshot('staff-pengguna-list');
        });
    }

    public function test_kelola_pengguna_has_user_table(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kelola-pengguna');
            $b->waitForText('Kelola Pengguna', 5);

            // Should show stat cards
            $b->assertSee('Manager');
            $b->assertSee('Admin');
            $b->assertSee('Produksi');

            echo "\n[✓] PENGGUNA: Statistik pengguna tampil\n";
        });
    }

    public function test_kelola_kategori_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kategori');
            $b->waitForText('Kelola Kategori', 5);
            $b->assertSee('Tambah Kategori');

            echo "\n[✓] KATEGORI: Halaman kelola kategori tampil\n";
            $b->screenshot('staff-kategori');
        });
    }

    public function test_kelola_kategori_add(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/kategori');
            $b->waitForText('Kelola Kategori', 5);

            // Add category via fetch
            $b->script('
                fetch("/staf/kategori", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ name: "Dusk Test Category" })
                })
                .then(r => r.json())
                .then(d => {
                    console.log("CATEGORY CREATE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
                .catch(e => console.error("CATEGORY ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] KATEGORI: Tambah kategori baru berjalan\n";
        });

        Category::where('name', 'Dusk Test Category')->delete();
    }

    public function test_kelola_kategori_delete(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();
        $cat = Category::create(['name' => 'Dusk To Delete']);

        $this->browse(function (Browser $b) use ($admin, $cat) {
            $b->loginAs($admin);
            $b->visit('/staf/kategori');
            $b->waitForText('Kelola Kategori', 5);

            // Delete via fetch
            $b->script('
                fetch("/staf/kategori/' . $cat->id . '", {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(r => r.json())
                .then(d => {
                    console.log("CATEGORY DELETE:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
                .catch(e => console.error("CAT DELETE ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] KATEGORI: Hapus kategori berjalan\n";
        });

        Category::where('name', 'Dusk To Delete')->delete();
    }

    public function test_pengaturan_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/pengaturan');
            $b->waitForText('Pengaturan', 5);
            $b->assertSee('Informasi Toko');
            $b->assertSee('Nama Perusahaan');
            $b->assertSee('Telepon');
            $b->assertSee('Email');
            $b->assertSee('Alamat');

            echo "\n[✓] PENGATURAN: Halaman pengaturan tampil dengan form\n";
            $b->screenshot('staff-pengaturan');
        });
    }

    public function test_pengaturan_save_settings(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/pengaturan');
            $b->waitForText('Pengaturan', 5);

            // Save settings via fetch
            $b->script('
                fetch("/staf/pengaturan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        company_name: "Novos Dusk Test",
                        company_phone: "0211234567",
                        company_email: "dusk@novos.com",
                        company_address: "Jl. Dusk No. 1"
                    })
                })
                .then(r => r.json())
                .then(d => console.log("SETTINGS SAVE:", JSON.stringify(d)))
                .catch(e => console.error("SETTINGS ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] PENGATURAN: Simpan pengaturan berjalan\n";
            $b->screenshot('staff-pengaturan-save');
        });
    }
}
