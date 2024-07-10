<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{


    #[Test]
    public function can_show_a_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    #[Test]
    public function can_create_a_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password', // Password will be hashed by the controller
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'name' => $userData['name'],
                'email' => $userData['email'],
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);
    }


}
