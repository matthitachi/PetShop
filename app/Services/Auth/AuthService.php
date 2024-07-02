<?php

namespace App\Services\Auth;

use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\JWTService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;

final class AuthService
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * @param array $params
     * @param string $resource
     */
    public function create(array $params, $isAdmin = 0): JsonResource
    {
        try {
            DB::beginTransaction();
            $params['is_admin'] = $isAdmin;
            $params['password'] = Hash::make((string) $params['password']);
            $user = User::create($params);
            $token = $this->jwtService->issueToken($user);

            $user->tokens()->create([
                'unique_id' => $token->claims()->get('jti'),
                'token_title' => 'access',
                'expires_at' => $token->claims()->get('exp'),
            ]);

            DB::commit();
            $resource =  $isAdmin ? (new AdminResource($user)): (new UserResource($user));
            $resource->additional(['token' => $token->toString()]);

            return $resource;
        } catch (\Throwable $e) {
            Log::debug(
                'Error occurred while creating new user',
                [$e->getMessage(), $e->getTrace()]
            );
            throw new \ErrorException('Error occurred while creating new user');
        }
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function login(User $user): Token
    {
        try {
            DB::beginTransaction();
            $token = $this->jwtService->issueToken($user);
            $user->tokens()->updateOrCreate(
                [
                    'unique_id' => $token->claims()->get('jti'),
                    'token_title' => 'access',
                    'user_id' => $user->id,
                ],
                [
                    'expires_at' => $token->claims()->get('exp'),
                ]
            );
            DB::commit();

            return $token;
        } catch (\Throwable $e) {
            Log::debug(
                'Error while logging in',
                [$e->getMessage(), $e->getTrace()]
            );
            DB::rollBack();
            throw new \ErrorException('Error');
        }
    }

    public function logout(string $token): bool
    {
        $token = $this->jwtService->parseToken($token);
        $uuid = $token->claims()->get('uuid');
        $tokenId = $token->claims()->get('jti');
        $user = User::where('uuid', $uuid)->firstOrFail();
        try {
            DB::beginTransaction();
            $user->tokens()->where('unique_id', '=', $tokenId)->delete();
            DB::commit();

            return true;
        } catch (\Throwable $e) {
            Log::debug(
                'Error while logging out',
                [$e->getMessage(), $e->getTrace()]
            );
            DB::rollBack();
            throw new \ErrorException('Error');
        }
    }

}
