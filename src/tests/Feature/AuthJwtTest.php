<?php

namespace Feature;

use App\Models\TravelOrder;
use App\Models\User;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthJwtTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_access_protected_route_with_jwt()
    {
        // Cria um usuário com senha conhecida
        $user = User::factory()->create([
            "name"     => "João Silva",
            "email"    => "joao@onfly.com",
            "password" => bcrypt('senha123')
        ]);

        // Realiza o login
        $response = $this->postJson('/api/login', [
            'email'    => 'joao@onfly.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token', $response->json());

        $access_token = $response->json('access_token');

        // Usa o token para acessar uma rota protegida
        $this->withHeader('Authorization', "Bearer $access_token")
            ->getJson('/api/travel-orders')
            ->assertStatus(200);
    }

    public function test_invalid_login_returns_error()
    {
        // Tenta logar com credenciais inválidas
        $response = $this->postJson('/api/login', [
            'email'    => 'naoexiste@example.com',
            'password' => 'errado',
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure(['error']);
    }
}
