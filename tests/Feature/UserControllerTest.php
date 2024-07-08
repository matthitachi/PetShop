<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthTest;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use AuthTest;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_a_user()
    {
        $userData = [
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'password' => \Hash::make('password'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
        ];

        $response = $this->postJson('/api/user/create', $userData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $userData['first_name'],
                'email' => $userData['email'],
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);
    }

    /** @test */
    public function can_login_a_user()
    {
        $user = $this->getUser();
        $userData = [
            'email' => $user->email,
            'password' => 'userpassword',
        ];

        $response = $this->postJson('/api/user/login', $userData);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['token' => true]]);
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'is_admin' => $user->is_admin
        ]);
    }

    /** @test */
    public function can_show_a_user()
    {
        $user = $this->getUser();

        $response = $this->getJson("/api/user");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->first_name,
                'email' => $user->email,
            ]
        ]);
    }
    /** @test */
    public function can_list_user_orders()
    {
        $user = $this->getUser();

        $response = $this->authenticatedRequest($user)->getJson("/api/user/orders");

        $response->assertStatus(200);
    }

    /** @test */
    public function can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->authenticatedRequest($this->getUser())->deleteJson("/api/user/{$user->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
