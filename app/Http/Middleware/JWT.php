<?php

namespace App\Http\Middleware;

use App\Services\Auth\JWTService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class JWT
{
    private JWTService $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return $this->unauthorizedResponse('Token not provided');
        }

        try {
            $jwtToken = $this->jwtService->parseToken($token);
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token');
        }

        if (!$this->jwtService->validateToken($jwtToken) || $jwtToken->isExpired(new \DateTimeImmutable())) {
            return $this->unauthorizedResponse('Token is invalid or expired');
        }

        $userUuid = $jwtToken->claims()->get('uuid');
        $tokenId = $jwtToken->claims()->get('jti');

        $user = User::query()
            ->where('uuid', $userUuid)
            ->hasToken($tokenId)
            ->first();

        if (!$user) {
            return $this->unauthorizedResponse('User not found');
        }

        Auth::setUser($user);

        return $next($request);
    }

    /**
     * Return a standardized unauthorized response.
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }
}

