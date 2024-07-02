<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Models\User;
use App\Services\Auth\AuthService;
use http\Exception;
use HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthenticationController extends Controller
{
    private AuthService $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function create(UserCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $resource =  $this->service->create($validated);

            return \response()->json(
                $resource, Response::HTTP_OK);
        } catch (\ErrorException $e) {
            return \response()->json([
                'status' => 'error',
                'message' => 'An error occurred!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        if ( !Auth::attempt($request->safe()->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred!'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::where('id', Auth::id())->firstOrFail();

        try {
            $token = $this->service->login($user);
        } catch (\ErrorException|Throwable $e) {
            return \response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return \response()->json(['token' => $token->toString()], Response::HTTP_OK);

    }

    /**
     * @throws Exception
     */
    public function logout(
        Request $request
    ): JsonResponse {
        $token = $request->bearerToken();
        if (is_null($token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred!'
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $this->service->logout((string) $token);
        } catch (\ErrorException $e) {
            return \response()->json([
                'status' => 'error',
                'message' => 'An error occurred!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return \response()->json([], Response::HTTP_OK);
    }

}
