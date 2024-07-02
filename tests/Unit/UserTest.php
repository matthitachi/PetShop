<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function can_create_user()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password')
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
    }

    public function can_update_user()
    {
        $user = User::factory()->create();
        $user->name = 'Jane Doe';
        $user->save();

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Doe'
        ]);
    }

    public function can_delete_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertDatabaseMissing('users', [
            'email' => $user->email
        ]);
    }

}
