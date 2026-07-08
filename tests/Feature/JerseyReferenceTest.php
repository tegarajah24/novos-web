<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JerseyReferenceTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $roles = ['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi', 'Customer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $adminRole = Role::where('name', 'Admin')->first();

        $this->superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    public function test_guest_cannot_access_reference_apis()
    {
        $response = $this->getJson(route('staf.kelola-produk.get-referensi'));
        $response->assertUnauthorized();

        $response = $this->postJson(route('staf.kelola-produk.update-referensi'));
        $response->assertUnauthorized();
    }

    public function test_non_super_admin_staff_can_view_but_cannot_update_references()
    {
        $response = $this->actingAs($this->admin)->getJson(route('staf.kelola-produk.get-referensi'));
        $response->assertOk();
        $response->assertJsonStructure(['collar', 'bahan', 'potongan', 'lengan']);

        $response = $this->actingAs($this->admin)->postJson(route('staf.kelola-produk.update-referensi'), [
            'type' => 'collar',
            'options' => ['Option A']
        ]);
        $response->assertForbidden();
    }

    public function test_super_admin_can_update_options_and_image_references()
    {
        Storage::fake('public');

        $response = $this->actingAs($this->superAdmin)->postJson(route('staf.kelola-produk.update-referensi'), [
            'type' => 'collar',
            'options' => json_encode(['O-NECK NEW V1', 'O-NECK NEW V2']),
            'image' => UploadedFile::fake()->image('collar_new.png')
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Check if options are saved in settings
        $savedOptions = json_decode(Setting::get('jersey_collar_options'), true);
        $this->assertEquals(['O-NECK NEW V1', 'O-NECK NEW V2'], $savedOptions);

        // Check if image is uploaded
        $savedImage = Setting::get('jersey_collar_image');
        $this->assertNotNull($savedImage);
        $this->assertStringContainsString('settings/', $savedImage);
        Storage::disk('public')->assertExists($savedImage);
    }
}
