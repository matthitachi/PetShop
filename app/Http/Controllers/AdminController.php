<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Paginator;
use http\Exception;
use HttpResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AdminController extends Controller
{
    private AuthService $service;
    private Paginator $paginator;

    public function __construct(AuthService $service, Paginator $paginator)
    {
        $this->service = $service;
        $this->paginator = $paginator;
    }

    public function create(UserCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        try {
            $resource =  $this->service->create($validated, 1);

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
        $user = User::where('id', Auth::id())->where('is_admin', 1)->firstOrFail();

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

    public function index(Request $request, Paginator $paginator): JsonResponse
    {
        $userQuery = User::where('is_admin', '=', 0);
        $filters = ['first_name', 'email', 'phone', 'address',
            'created_at',
            'is_marketing',
        ];
        foreach ($filters as $filter) {
            if ($request->has($filter)) {
                $userQuery->where($filter, '=', $request->input($filter));
            }

        }
        $paginatedUsers = $this->paginator->paginate($request, $userQuery);

        return response()->json(UserResource::collection($paginatedUsers), Response::HTTP_OK);
    }

    public function update(User $user, UserUpdateRequest $request): JsonResponse
    {
        $params = $request->validated();

        $user->update($params);

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([], Response::HTTP_OK);
    }

}
