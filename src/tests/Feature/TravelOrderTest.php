<?php

namespace Tests\Feature;

use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;

    private function loginAndGetToken(User $user = null): string
    {
        $user = $user ?? User::factory()->create([
            'password' => bcrypt('senha123')
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'senha123',
        ]);

        $response->assertStatus(200);

        return $response['access_token'];
    }

    public function test_user_can_create_travel_order()
    {
        $token = $this->loginAndGetToken();

        $payload = [
            'requester_name' => 'João Silva',
            'destination'    => 'Nova York',
            'start_date'     => '2025-04-10',
            'end_date'       => '2025-07-12'
        ];

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/travel-orders', $payload)
            ->assertStatus(201)
            ->assertJsonFragment(['destination' => 'Nova York']);
    }

    public function test_user_can_view_own_travel_order()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $token = $this->loginAndGetToken($user);

        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson("/api/travel-orders/{$order->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $order->id]);
    }

    public function test_user_cannot_view_others_order()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $other = User::factory()->create(['password' => bcrypt('senha123')]);
        $token = $this->loginAndGetToken($other);

        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson("/api/travel-orders/{$order->id}")
            ->assertStatus(403);
    }

    public function test_travel_order_not_found_returns_custom_message()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $token = $this->loginAndGetToken($user);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/travel-orders/999')
            ->assertStatus(404)
            ->assertJson(['message' => 'Nenhuma ordem de viagem encontrada.']);
    }

    public function test_admin_can_update_status_and_notify_user()
    {
        Notification::fake();

        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $admin = User::factory()->create(['password' => bcrypt('senha123')]);

        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $token = $this->loginAndGetToken($admin);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/travel-orders/{$order->id}/status", ['status' => 'aprovado'])
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'aprovado']);

        Notification::assertSentTo($user, OrderStatusUpdated::class);
    }

    public function test_user_cannot_update_own_order_status()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);

        $order = TravelOrder::factory()->create(['user_id' => $user->id]);

        $token = $this->loginAndGetToken($user);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/travel-orders/{$order->id}/status", ['status' => 'aprovado'])
            ->assertStatus(403);
    }

    public function test_admin_can_cancel_approved_order()
    {
        Notification::fake();

        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $admin = User::factory()->create(['password' => bcrypt('senha123')]);

        $order = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status'  => 'aprovado'
        ]);

        $token = $this->loginAndGetToken($admin);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/travel-orders/{$order->id}/cancel")
            ->assertStatus(200)
            ->assertJsonFragment(['status' => 'cancelado']);

        Notification::assertSentTo($user, OrderStatusUpdated::class);
    }

    public function test_user_cannot_cancel_not_approved_order()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);
        $admin = User::factory()->create(['password' => bcrypt('senha123')]);

        $order = TravelOrder::factory()->create([
            'user_id' => $user->id,
            'status'  => 'solicitado'
        ]);

        $token = $this->loginAndGetToken($admin);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/travel-orders/{$order->id}/cancel")
            ->assertStatus(400)
            ->assertJsonFragment(['error' => 'Só é possível cancelar pedidos aprovados']);
    }

    public function test_can_filter_orders_by_status_and_destination()
    {
        $user = User::factory()->create(['password' => bcrypt('senha123')]);

        TravelOrder::factory()->create([
            'user_id'     => $user->id,
            'destination' => 'Rio de Janeiro',
            'status'      => 'aprovado'
        ]);
        TravelOrder::factory()->create([
            'user_id'     => $user->id,
            'destination' => 'São Paulo',
            'status'      => 'cancelado'
        ]);

        $token = $this->loginAndGetToken($user);

        $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/travel-orders?status=aprovado&destination=Rio de Janeiro')
            ->assertStatus(200)
            ->assertJsonCount(1);
    }
}
