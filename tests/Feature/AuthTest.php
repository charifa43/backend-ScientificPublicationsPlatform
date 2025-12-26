<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Professor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
            'grade' => 'DOCTOR',
            'role' => 'professor',
        ]);

        // Ajoutez ceci pour voir la réponse
        $response->dump();

        $response->assertStatus(201)
                 ->assertJsonStructure(['token']);
    }

    public function test_user_can_login()
    {
        $professor = Professor::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'login' . time() . '@test.com',
            'password' => Hash::make('password123'),
            'grade' => 'DOCTOR',
            'role' => 'professor',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $professor->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_protected_route_requires_auth()
    {
        $response = $this->getJson('/api/professor');
        $response->assertStatus(401);
    }
}