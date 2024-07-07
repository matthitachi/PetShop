<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Paginator;
use http\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserController extends Controller
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
        $user = User::where('id', Auth::id())->where('is_admin', 0)->firstOrFail();

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
    public function logout(Request $request): JsonResponse {
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
    public function index(): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();

        return response()->json(new UserResource($user), Response::HTTP_OK );
    }

    public function edit(UserUpdateRequest $request): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $user->update($request->validated());

        return response()->json(new UserResource($user), Response::HTTP_OK );
    }

    public function destroy(): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $user->delete();

        return response()->json([], Response::HTTP_OK );
    }

    public function passwordResetToken(Request $request): JsonResponse
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ]
        );

        $user = User::where('email', $request->email)->firstOrFail();
        $token = Password::createToken($user);

        return response()->json(['token' => $token], Response::HTTP_OK );
    }

    public function passwordReset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8',
        ]);
        $response = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been successfully updated'], Response::HTTP_OK );
        }
        if ($response === Password::INVALID_TOKEN) {
            return response()->json(['message' => 'Invalid or expired token'], Response::HTTP_OK );
        }
        return response()->json(['message' => 'Unable to reset password.'], Response::HTTP_BAD_REQUEST );
    }

    public function orders(Request $request, Paginator $paginator): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $query = $user->orders();
        $paginatedResult = $paginator->paginate($request, $query);

    //     $query->getCollection()->transform(function ($value) {
    //        $value->products = json_decode($value->products, true);

    //        return $value;
    //    });

        return response()->json(OrderResource::collection($paginatedResult), Response::HTTP_OK);
    }
}
