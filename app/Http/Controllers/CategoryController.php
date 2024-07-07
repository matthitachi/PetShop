<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\CategoryCreateRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Models\Brand;
use App\Models\Category;
use App\Services\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

final class CategoryController extends Controller
{
    private Paginator $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function index(Request $request, Paginator $paginator): JsonResponse
    {
        $categoryQuery = Category::query();
        $paginatedResults = $this->paginator->paginate($request, $categoryQuery);

        return response()->json(CategoryResource::collection($paginatedResults), Response::HTTP_OK);
    }

    public function store(CategoryCreateRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json(new CategoryResource($category), Response::HTTP_OK);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json(new CategoryResource($category), Response::HTTP_OK);
    }

    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json(new CategoryResource($category), Response::HTTP_OK);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([], Response::HTTP_OK);
    }
}
