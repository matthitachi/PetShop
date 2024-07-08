<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\AuthTest;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use AuthTest;
    /**
     * A basic feature test example.
     */
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
            'is_admin' => 1
        ];

        $response = $this->postJson('/api/admin/create', $userData);

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
    public function can_login_an_admin()
    {
        $user = $this->getAdmin();
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
    public function can_login_a_user()
    {
        $user = $this->getUser();
        $userData = [
            'email' => $user->email,
            'password' => 'userpassword',
        ];

        $response = $this->postJson('/api/user/login', $userData);

        $response->assertStatus(401);
    }

    /** @test */
    public function can_show_a_user()
    {
        $user = $this->getUser();

        $response = $this->getJson("/api/user-listing/$user->uuid");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->first_name,
                'email' => $user->email,
            ]
        ]);
    }

    public function can_list_users_with_admin()
    {
        $this->authenticatedRequest($this->getAdmin())
            ->get(route('/api/user-listing'))->assertStatus(200);
    }

    public function can_list_users_with_user()
    {
        $this->authenticatedRequest(
            $this->getUser()
        )->get(route('/api/user-listing'))->assertStatus(401);
    }

    public function can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->authenticatedRequest($this->getAdmin())->getJson('/api/user-listing');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function can_update_a_user()
    {
        $user = User::factory()->create();
        $updateData = [
            'name' => 'Updated Name'
        ];

        $response = $this->authenticatedRequest($this->getAdmin())->putJson("/api/user-listing/{$user->uuid}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => 'Updated Name',
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    /** @test */
    public function can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->authenticatedRequest($this->getAdmin())->deleteJson("/api/user/{$user->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
