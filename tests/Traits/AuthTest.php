<?php

namespace Tests\Traits;

use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

trait AuthTest
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    protected function getAdmin()
    {
        return User::factory()->create(['is_admin' => 1]);
    }

    protected function getUser()
    {
        return User::factory()->create(['is_admin' => 0]);
    }

    protected function authenticatedRequest(User $user): self
    {
        $token = app(AuthService::class)->login($user);

        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token->toString(),
            'Accept' => 'application/json',
        ]);
    }
}
