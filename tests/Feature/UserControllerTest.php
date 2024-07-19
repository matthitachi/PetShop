<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\AuthTest;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use AuthTest;

    protected string $baseUrl = '/api/v1/user';

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    #[Test]
    public function test_can_create_a_user()
    {
        $userData = [
            'first_name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'password' => \Hash::make('password'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
        ];

        $response = $this->postJson("$this->baseUrl/create", $userData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'first_name' => $userData['first_name'],
                'email' => $userData['email'],
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    #[Test]
    public function test_can_login_a_user()
    {
        $user = $this->getUser();
        $userData = [
            'email' => $user->email,
            'password' => 'userpassword',
        ];

        $response = $this->postJson("$this->baseUrl/login", $userData);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['token' => true]]);
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'is_admin' => $user->is_admin,
        ]);
    }

    #[Test]
    public function test_can_show_a_user()
    {
        $user = $this->getUser();

        $response = $this->authenticatedRequest($user)->getJson("$this->baseUrl");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'uuid' => $user->uuid,
                'first_name' => $user->first_name,
                'email' => $user->email,
            ],
        ]);
    }

    #[Test]
    public function test_can_list_user_orders()
    {
        $user = $this->getUser();

        $response = $this->authenticatedRequest($user)->getJson("$this->baseUrl/orders");

        $response->assertStatus(200);
    }

    #[Test]
    public function test_can_delete_a_user()
    {
        $user = $this->getUser();

        $response = $this->authenticatedRequest($user)->deleteJson("$this->baseUrl");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
