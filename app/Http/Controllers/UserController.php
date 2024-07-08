<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Paginator;
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

    /**
     * @OA\Post(
     *     path="/api/v1/user/create",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *                @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *               @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address",
     *                 ),
     *               @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone number",
     *                 ),
     *               @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string",
     *                     enum={"0", "1"},
     *                 ),
     *                 required={"first_name", "last_name", "email","password","address","phone_number"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     *
     * @throws Throwable
     */
    public function create(UserCreateRequest $request): JsonResponse
    {

        $validated = $request->validated();
        try {
            $resource =  $this->service->create($validated);

            return \response()->formatted(true,
                new UserResource($resource), Response::HTTP_OK);
        } catch (\ErrorException $e) {
            return \response()->formatted(0, [], Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e->getTrace());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/login",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Admin email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Admin password",
     *                 ),
     *                 required={"email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function login(UserLoginRequest $request): JsonResponse
    {

        if ( !Auth::attempt($request->safe()->only('email', 'password'))) {
            return response()->formatted(0, [], Response::HTTP_UNAUTHORIZED, 'Failed to authenticate user');
        }
        $user = User::query()->where('id', Auth::id())->where('is_admin', 0)->firstOrFail();

        try {
            $token = $this->service->login($user);
        } catch (\ErrorException|Throwable $e) {
            return \response()->formatted(0, [], Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e->getTrace());

        }

        return \response()->formatted(true, ['token' => $token->toString()]);

    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/logout",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse {
        $token = $request->bearerToken();
        if (is_null($token)) {
            return response()->formatted(0, [], Response::HTTP_UNAUTHORIZED, 'Failed to authenticate user');
        }
        try {
            $this->service->logout((string) $token);
        } catch (\ErrorException $e) {
            return \response()->formatted(0, [], Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage(), [], $e->getTrace());
        }

        return \response()->formatted(true, []);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();

        return  \response()->formatted(true, new UserResource($user));
    }

    /**
     * @OA\Put(
     *     path="/api/v1/user/edit",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="first_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="string",
     *                     description="User First Name",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User email",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="User password",
     *                 ),
     *                @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     description="User password"
     *                 ),
     *               @OA\Property(
     *                     property="address",
     *                     type="string",
     *                     description="User address",
     *                 ),
     *               @OA\Property(
     *                     property="phone_number",
     *                     type="string",
     *                     description="User phone number",
     *                 ),
     *               @OA\Property(
     *                     property="is_marketing",
     *                     description="User marketing preferences",
     *                     type="string",
     *                     enum={"0", "1"},
     *                 ),
     *                 required={"first_name", "last_name", "email","password","address","phone_number"}
     *             )
     *         )
     *    ),
     *    @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     * )
     */
    public function edit(UserUpdateRequest $request): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $user->update($request->validated());

        return response()->formatted(true, new UserResource($user), Response::HTTP_OK );
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy(): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $user->delete();

        return response()->formatted(true, [], Response::HTTP_OK );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/forgot-password",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function passwordResetToken(Request $request): JsonResponse
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ]
        );

        $user = User::where('email', $request->email)->firstOrFail();
        $token = Password::createToken($user);

        return response()->formatted(true, ['token' => $token], Response::HTTP_OK );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user/reset-password-token",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="User reset token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
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
            return response()->formatted(true, ['message' => 'Password has been successfully updated'], Response::HTTP_OK );
        }
        if ($response === Password::INVALID_TOKEN) {
            return response()->formatted(true, ['message' => 'Invalid or expired token'], Response::HTTP_OK );
        }
        return response()->formatted(true, ['message' => 'Unable to reset password.'], Response::HTTP_BAD_REQUEST );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user/orders",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="desc",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function orders(Request $request, Paginator $paginator): JsonResponse
    {
        $user = User::whereId(Auth::id())->firstOrFail();
        $query = $user->orders();
        $paginatedResult = $paginator->paginate($request, $query);

        return response()->formatted(true, OrderResource::collection($paginatedResult), Response::HTTP_OK);
    }
}
