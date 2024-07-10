<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\AuthTest;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use AuthTest;

    protected string $baseUrl = "/api/v1/admin";
    /**
     * A basic feature test example.
     */
    #[Test]
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
            'is_admin' => 1
        ];

        $response = $this->postJson("$this->baseUrl/create", $userData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'first_name' => $userData['first_name'],
                'email' => $userData['email'],
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);
    }


    #[Test]
    public function test_can_login_an_admin()
    {
        $user = $this->getAdmin();
        $userData = [
            'email' => $user->email,
            'password' => 'userpassword',
        ];

        $response = $this->postJson("$this->baseUrl/login", $userData);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['token' => true]]);
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'is_admin' => $user->is_admin
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

        $response->assertStatus(401);
    }



    public function test_can_list_users_with_admin()
    {
        $this->authenticatedRequest($this->getAdmin())
            ->get("$this->baseUrl/user-listing")->assertStatus(200);
    }

    public function test_can_list_users_with_user()
    {
        $this->authenticatedRequest(
            $this->getUser()
        )->get("$this->baseUrl/user-listing")->assertStatus(401);
    }

    public function test_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->authenticatedRequest($this->getAdmin())->getJson("$this->baseUrl/user-listing");

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    #[Test]
    public function test_can_update_a_user()
    {
        $user = User::factory()->create();
        $updateData = [
            'first_name' => 'Updated Name',
                'last_name' => 'last',
                'password' => 'qwertyiyor',
                'password_confirmation' => 'qwertyiyor',
                'address' => 'No 5 cussons street ',
                'phone_number' => '+44565849955',
                'email' => 'ab@example.com'
        ];

        $response = $this->authenticatedRequest($this->getAdmin())->putJson("$this->baseUrl/user-edit/{$user->uuid}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'uuid' => $user->uuid,
                'first_name' => 'Updated Name',
                'last_name' => 'last',
            ]
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated Name'
        ]);
    }

    #[Test]
    public function test_can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->authenticatedRequest($this->getAdmin())->deleteJson("$this->baseUrl/user-delete/{$user->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
