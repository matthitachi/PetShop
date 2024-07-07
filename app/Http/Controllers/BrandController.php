<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\BradCreateRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\UserResource;
use App\Models\Brand;
use App\Services\Auth\AuthService;
use App\Services\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

class BrandController extends Controller
{
    private Paginator $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function index(Request $request, Paginator $paginator): JsonResponse
    {
        $brandQuery = Brand::query();
        $paginatedResults = $this->paginator->paginate($request, $brandQuery);

        return response()->json(BrandResource::collection($paginatedResults), Response::HTTP_OK);
    }


    public function store(BradCreateRequest $request): JsonResponse
    {
        $brand = Brand::create($request->validated());

        return response()->json(new BrandResource($brand), Response::HTTP_OK);
    }


    public function show(Brand $brand): JsonResponse
    {
        return response()->json(new BrandResource($brand), Response::HTTP_OK);
    }


    public function update(BrandUpdateRequest $request, Brand $brand): JsonResponse
    {
        $brand->update($request->safe()->all());

        return response()->json(new BrandResource($brand), Response::HTTP_OK);
    }


    public function destroy(Brand $brand): JsonResponse
    {
        $brand->delete();

        return response()->json([], Response::HTTP_OK);
    }
}
