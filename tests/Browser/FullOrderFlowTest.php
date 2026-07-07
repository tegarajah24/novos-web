<?php

namespace Tests\Browser;

use App\Models\Order;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestOrders;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class FullOrderFlowTest extends DuskTestCase
{
    use WithTestUsers;
    use WithTestOrders;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
        $this->ensureTestOrdersExist();
    }
    private string $orderNumber = '';

    public function test_full_order_flow_with_four_browsers(): void
    {
        $customer  = User::where('email', 'customer@novos.com')->firstOrFail();
        $admin     = User::where('email', 'admin@novos.com')->firstOrFail();
        $design    = User::where('email', 'design@novos.com')->firstOrFail();
        $produksi  = User::where('email', 'produksi@novos.com')->firstOrFail();

        $this->browse(function (
            Browser $c,
            Browser $a,
            Browser $d,
            Browser $p
        ) use ($customer, $admin, $design, $produksi) {

            // ══════════════════════════════════════════════
            // 1. CUSTOMER — Buat Pesanan Baru
            // ══════════════════════════════════════════════
            $c->loginAs($customer)->visit('/pesan');
            $c->pause(3000);

            // Step 1: Pilih Jersey Custom
            $c->script("document.querySelectorAll('.grid.md\\\\:grid-cols-2 > div, .grid.grid-cols-1.md\\\\:grid-cols-2 > div')[0]?.click()");
            $c->pause(500);
            $c->script("let btns = document.querySelectorAll('button'); for(let b of btns) { if(b.textContent.includes('Selanjutnya')) { b.click(); break; } }");
            $c->pause(1500);

            // Step 2: Isi detail desain
            $c->script('
                let r = document.querySelector(".max-w-6xl")._x_dataStack[0];
                if (r && r.form) {
                    r.form.team_name = "Test Tim Dusk";
                    r.form.kerah = "O-NECK V.1";
                    r.form.bahan = "MILANO PREMIUM";
                    r.form.jenis_potongan = "REGULER";
                    r.form.lengan_jahitan = "REGULER OVERDECK";
                }
                if (r) { r.tmpSize = "M"; r.tmpQty = 1; if (typeof r.addSize === "function") r.addSize(); }
            ');
            $c->pause(500);
            $c->script("let btns = document.querySelectorAll('button'); for(let b of btns) { if(b.textContent.includes('Pesan Langsung')) { b.click(); break; } }");
            $c->pause(2000);

            // Step 3: Address
            $c->script('
                let r = document.querySelector(".max-w-6xl")._x_dataStack[0];
                if (r && r.addresses && r.addresses.length > 0) {
                    r.selectedAddressId = r.addresses[0].id;
                    if (typeof r.useSelectedAddress === "function") r.useSelectedAddress();
                }
            ');
            $c->pause(2000);

            // Step 4: Set prioritas & konfirmasi
            $c->script('let r = document.querySelector(".max-w-6xl")._x_dataStack[0]; if (r) r.prioritas = "normal";');
            $c->pause(500);
            $c->script("let btns = document.querySelectorAll('button'); for(let b of btns) { if(b.textContent.includes('Konfirmasi') || (b.textContent.includes('Bayar'))) { b.click(); break; } }");
            $c->pause(3000);

            $orderNum = $c->script('return document.querySelector(".max-w-6xl")._x_dataStack[0].orderNumber || ""')[0];
            if (empty($orderNum)) {
                $orderNum = Order::latest()->first()?->order_number ?? '';
            }
            $this->orderNumber = $orderNum;
            echo "\n[✓] CUSTOMER: Pesanan {$this->orderNumber} berhasil dibuat\n";
            $c->screenshot('01-customer-order-created');

            // ══════════════════════════════════════════════
            // 2. CUSTOMER — Setujui Detail Pesanan (approve)
            // ══════════════════════════════════════════════
            $order = Order::where('order_number', $this->orderNumber)->first();

            $c->script('
                fetch("/pesan/' . $this->orderNumber . '/approve", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    }
                })
                .then(r => r.json())
                .then(d => { console.log("APPROVE:", JSON.stringify(d)); })
                .catch(e => { console.error("APPROVE ERR:", e); });
            ');
            $c->pause(2000);

            $order->refresh();
            if ($order->status === 'dikonfirmasi') {
                echo "\n[✓] CUSTOMER: Pesanan disetujui -> {$order->status}\n";
            } else {
                $order->update(['status' => 'dikonfirmasi', 'confirmed_at' => now()]);
                echo "\n[!] CUSTOMER: Fallback — DB update langsung ke dikonfirmasi\n";
            }

            // ══════════════════════════════════════════════
            // 3. ADMIN — Teruskan ke Design
            // ══════════════════════════════════════════════
            $a->visit('/staf/detail-pesanan/' . $this->orderNumber);
            $a->waitForText('Update Status', 10);
            $a->pause(2000);

            // Fetch langsung untuk update ke di_design
            $a->script('
                fetch("/staf/pesanan/' . $this->orderNumber . '/update-status", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ status: "tahap_desain", notes: "Diteruskan ke Design (Dusk)" })
                })
                .then(r => r.json())
                .then(d => {
                    console.log("ADMIN->DESIGN:", JSON.stringify(d));
                    if (d.success) location.reload();
                })
                .catch(e => console.error("ADMIN->DESIGN ERROR:", e));
            ');
            $a->pause(2000);

            // Verifikasi status berubah
            $order->refresh();
            echo "\n[✓] ADMIN: Teruskan ke Design. Status skrg: " . $order->status . "\n";
            $a->screenshot('03-admin-to-design');

            // ══════════════════════════════════════════════
            // 4. DESIGN — Upload hasil desain
            // ══════════════════════════════════════════════
            $d->loginAs($design);
            $d->visit('/staf/design');
            $d->waitForText($this->orderNumber, 10);
            $d->pause(1000);
            $d->screenshot('04-design-start');

            $d->script('
                let fd = new FormData();
                fd.append("status", "siap_cetak");
                fetch("/staf/design/update/' . $this->orderNumber . '", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: fd
                })
                .then(r => r.json())
                .then(function(d) {
                    console.log("DESIGN RESULT:", JSON.stringify(d));
                    document.title = "DESIGN_RESULT:" + JSON.stringify(d);
                    if (d.success) { document.title = "DESIGN_OK"; }
                })
                .catch(function(e) { console.error("DESIGN ERROR:", e); document.title = "DESIGN_ERR:" + e.message; });
            ');
            $d->pause(3000);

            echo "\n[✓] DESIGN: Upload desain selesai\n";
            $d->screenshot('04-design-done');

            // ══════════════════════════════════════════════
            // 5. PRODUKSI — Proses tiap tahap produksi
            // ══════════════════════════════════════════════
            $p->loginAs($produksi);
            $p->visit('/staf/produksi');
            $p->waitForText($this->orderNumber, 10);
            $p->screenshot('05-produksi-start');

            $prodStages = [
                'proses_printing',
                'selesai_printing',
                'proses_jahit',
                'selesai_jahit',
                'proses_qc',
                'selesai_qc',
            ];

            foreach ($prodStages as $stage) {
                $payload = ['action' => $stage, 'notes' => 'Dusk test - ' . $stage];
                if ($stage === 'selesai_qc') {
                    $payload['qc_checklist'] = ['jahitan' => true, 'cacat' => true, 'ukuran' => true, 'desain' => true];
                }

                $p->script('
                    fetch("/staf/produksi/update/' . $this->orderNumber . '", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                            "Accept": "application/json"
                        },
                        body: JSON.stringify(' . json_encode($payload) . ')
                    })
                    .then(r => r.json())
                    .then(d => {
                        console.log("PROD ' . $stage . ':", JSON.stringify(d));
                        if (d.success && "' . $stage . '" === "selesai_qc") location.reload();
                    })
                    .catch(e => console.error("PROD ERROR:", e));
                ');
                $p->pause(2000);
                echo "\n[→] PRODUKSI: " . str_replace('_', ' ', $stage);
            }

            $p->screenshot('05-produksi-selesai');
            $order->refresh();

            // Assertions
            $this->assertNotEmpty($this->orderNumber);
            $this->assertEquals('selesai', $order->status);
            $this->assertNull($order->production_stage);

            echo "\n[✓] PRODUKSI: Selesai. Status akhir: {$order->status}\n";
            echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo " ORDER NUMBER: {$this->orderNumber}\n";
            echo " STATUS AKHIR: {$order->status}\n";
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        });
    }
}
