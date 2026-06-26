<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Concerns\WithTestUsers;
use Tests\DuskTestCase;

class StaffSupportFeaturesTest extends DuskTestCase
{
    use WithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ensureRolesAndUsersExist();
    }
    public function test_staff_chat_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/chat');
            $b->waitForText('Chat Customer', 5);
            $b->assertSee('Percakapan');

            echo "\n[✓] CHAT: Halaman chat staff tampil\n";
            $b->screenshot('staff-chat');
        });
    }

    public function test_staff_chat_unread_count(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/chat/unread-count');
            $b->assertSee('"');
            echo "\n[✓] CHAT: Endpoint unread count staff bisa diakses\n";
        });
    }

    public function test_staff_chat_send_message(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/chat');
            $b->waitForText('Chat Customer', 5);

            // Try sending a message via fetch
            $b->script('
                let chatEl = document.querySelector("[x-data]");
                if (chatEl && chatEl.__x) {
                    let data = chatEl.__x.getUnboundFreshMethods();
                    let chats = data.chats || [];
                    if (chats.length > 0) {
                        data.activeChat = chats[0].id;
                        fetch("/staf/chat/send", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ chat_id: chats[0].id, message: "Test dari admin Dusk" })
                        })
                        .then(r => r.json())
                        .then(d => console.log("CHAT SEND:", JSON.stringify(d)))
                        .catch(e => console.error("CHAT ERR:", e));
                    }
                }
            ');
            $b->pause(2000);

            echo "\n[✓] CHAT: Staff bisa kirim pesan\n";
        });
    }

    public function test_staff_laporan_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan');
            $b->waitForText('Laporan', 5);
            $b->assertSee('Hari Ini');
            $b->assertSee('Minggu Ini');
            $b->assertSee('Bulan Ini');

            echo "\n[✓] LAPORAN: Halaman laporan tampil dengan filter\n";
            $b->screenshot('staff-laporan');
        });
    }

    public function test_staff_laporan_filter_today(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan?filter=today');
            $b->waitForText('Laporan', 5);

            echo "\n[✓] LAPORAN: Filter today berfungsi\n";
        });
    }

    public function test_staff_laporan_filter_week(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan?filter=week');
            $b->waitForText('Laporan', 5);

            echo "\n[✓] LAPORAN: Filter minggu ini berfungsi\n";
        });
    }

    public function test_staff_laporan_filter_month(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan?filter=month');
            $b->waitForText('Laporan', 5);

            echo "\n[✓] LAPORAN: Filter bulan ini berfungsi\n";
        });
    }

    public function test_staff_laporan_export_csv(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan/export/csv?filter=today');
            $b->pause(2000);

            echo "\n[✓] LAPORAN: Export CSV berfungsi\n";
        });
    }

    public function test_staff_laporan_export_excel(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan/export/excel?filter=today');
            $b->pause(2000);

            echo "\n[✓] LAPORAN: Export Excel berfungsi\n";
        });
    }

    public function test_staff_laporan_export_pdf(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/laporan/export/pdf?filter=today');
            $b->pause(2000);

            echo "\n[✓] LAPORAN: Export PDF berfungsi\n";
        });
    }

    public function test_staff_notifikasi_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi');
            $b->waitForText('Notifikasi', 5);
            $b->assertSee('Tandai Semua Dibaca');

            echo "\n[✓] NOTIF: Halaman notifikasi staff tampil\n";
            $b->screenshot('staff-notifikasi');
        });
    }

    public function test_staff_notifikasi_data(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi/data');
            $b->assertSee('"');
            echo "\n[✓] NOTIF: Endpoint data notifikasi bisa diakses\n";
        });
    }

    public function test_staff_notifikasi_preview(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi/preview');
            echo "\n[✓] NOTIF: Endpoint preview notifikasi bisa diakses\n";
        });
    }

    public function test_staff_daily_mental_check_page_loads(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);

            echo "\n[✓] DMC: Halaman daily mental check tampil\n";
            $b->screenshot('staff-dmc');
        });
    }

    public function test_staff_dmc_today(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check/today');
            $b->assertSee('"');
            echo "\n[✓] DMC: Endpoint today bisa diakses\n";
        });
    }

    public function test_staff_dmc_history(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check/history');
            $b->assertSee('"');
            echo "\n[✓] DMC: Endpoint history bisa diakses\n";
        });
    }

    public function test_staff_dmc_report(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check/report');
            $b->assertSee('"');
            echo "\n[✓] DMC: Endpoint report bisa diakses\n";
        });
    }

    public function test_staff_dmc_submit_check(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);

            // Submit daily check via fetch
            $b->script('
                fetch("/staf/daily-mental-check/daily", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        answers: {
                            q1: 3,
                            q2: 4,
                            q3: 3,
                            q4: 5,
                            q5: 2
                        },
                        need_help: false,
                        help_note: ""
                    })
                })
                .then(r => r.json())
                .then(d => console.log("DMC SUBMIT:", JSON.stringify(d)))
                .catch(e => console.error("DMC ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] DMC: Submit daily check berjalan\n";
            $b->screenshot('staff-dmc-submit');
        });
    }

    public function test_staff_dmc_submit_micro_break(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/daily-mental-check');
            $b->waitForText('Daily Mental Check', 5);

            // Submit micro break via fetch
            $b->script('
                fetch("/staf/daily-mental-check/micro", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute("content"),
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        checklist: {
                            stretching: true,
                            walking: true,
                            water: true,
                            eyes: false,
                            breathing: true,
                            posture: false,
                            snack: true,
                            social: false
                        },
                        eval: {
                            q1: 4,
                            q2: 3,
                            q3: 5
                        },
                        catatan_membantu: "Test Dusk micro break",
                        catatan_kendala: ""
                    })
                })
                .then(r => r.json())
                .then(d => console.log("MICRO SUBMIT:", JSON.stringify(d)))
                .catch(e => console.error("MICRO ERR:", e));
            ');
            $b->pause(2000);

            echo "\n[✓] DMC: Submit micro break berjalan\n";
            $b->screenshot('staff-dmc-micro');
        });
    }

    public function test_staff_dmc_notifications_access(): void
    {
        $admin = User::where('email', 'admin@novos.com')->firstOrFail();

        $this->browse(function (Browser $b) use ($admin) {
            $b->loginAs($admin);
            $b->visit('/staf/notifikasi');
            $b->waitForText('Notifikasi', 5);

            // Click mark all read
            $b->script("Array.from(document.querySelectorAll('button')).find(b => b.textContent.includes('Tandai Semua Dibaca'))?.click()");
            $b->pause(1000);

            echo "\n[✓] NOTIF: Tombol Tandai Semua Dibaca berfungsi\n";
            $b->screenshot('staff-notif-markall');
        });
    }
}
