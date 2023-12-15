<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_registration()
    {
        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
        ];

        $response = $this->postJson(route('register'), $requestData);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['user', 'token']]);
        // ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_that_the_user_is_authenticated_when_they_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $requestData = [
            'email' => 'john@example.com',
            'password' => 'secret123',
        ];

        $response = $this->postJson(route('login'), $requestData);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data' => ['user', 'token']])
            ->assertJson(['status' => 'success']);
    }
}
