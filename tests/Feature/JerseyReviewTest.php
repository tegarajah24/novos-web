<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JerseyReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $anotherCustomer;
    private Order $completedOrder;
    private Order $pendingOrder;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $roles = ['Super Admin', 'Manager', 'Admin', 'Design', 'Produksi', 'Customer'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $customerRole = Role::where('name', 'Customer')->first();

        $this->customer = User::factory()->create([
            'role_id' => $customerRole->id,
        ]);

        $this->anotherCustomer = User::factory()->create([
            'role_id' => $customerRole->id,
        ]);

        // Create completed order for customer
        $this->completedOrder = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'NVS-20260708-001',
            'status' => 'selesai',
            'total_price' => 150000.00
        ]);

        // Create pending order for customer
        $this->pendingOrder = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'NVS-20260708-002',
            'status' => 'menunggu_pembayaran',
            'total_price' => 200000.00
        ]);
    }

    public function test_guest_cannot_submit_review()
    {
        $response = $this->postJson(route('profile.pembelian.review'), [
            'order_id' => $this->completedOrder->id,
            'rating' => 5,
            'comment' => 'Bagus sekali!'
        ]);

        $response->assertUnauthorized();
    }

    public function test_customer_can_submit_review_for_own_completed_order()
    {
        $response = $this->actingAs($this->customer)->postJson(route('profile.pembelian.review'), [
            'order_id' => $this->completedOrder->id,
            'rating' => 5,
            'comment' => 'Kualitas mantap!'
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('reviews', [
            'order_id' => $this->completedOrder->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'comment' => 'Kualitas mantap!'
        ]);
    }

    public function test_customer_cannot_review_another_user_order()
    {
        $response = $this->actingAs($this->anotherCustomer)->postJson(route('profile.pembelian.review'), [
            'order_id' => $this->completedOrder->id,
            'rating' => 5,
            'comment' => 'Mencoba merating order orang'
        ]);

        $response->assertForbidden();
    }

    public function test_customer_cannot_review_uncompleted_order()
    {
        $response = $this->actingAs($this->customer)->postJson(route('profile.pembelian.review'), [
            'order_id' => $this->pendingOrder->id,
            'rating' => 4,
            'comment' => 'Belum dikirim tapi mau rating'
        ]);

        $response->assertStatus(400);
    }
}
