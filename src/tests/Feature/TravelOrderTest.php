<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TravelOrderTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // tests/Feature/TravelOrderTest.php

    public function test_status_update_by_non_owner()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)
            ->patchJson("/api/travel-orders/{$order->id}/status", [
                'status' => 'aprovado'
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('travel_orders', ['id' => $order->id, 'status' => 'aprovado']);
    }

    public function test_invalid_status_transition()
    {
        $user = User::factory()->create();
        $order = TravelOrder::factory()->create(['status' => 'cancelado']);

        $response = $this->actingAs($user)
            ->patchJson("/api/travel-orders/{$order->id}/status", [
                'status' => 'aprovado'
            ]);

        $response->assertStatus(422); // Validação falha
    }

    public function test_user_can_create_order()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/travel-orders', [
            'requester_name' => 'John Doe',
            'destination'    => 'Paris',
            'start_date'     => '2024-01-01',
            'end_date'       => '2024-01-10',
        ]);

        $response->assertStatus(201);
    }
}
