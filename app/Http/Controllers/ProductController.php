<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ProductCreateRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Throwable;

final class ProductController extends Controller
{
    private Paginator $paginator;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    public function index(Request $request, Paginator $paginator): JsonResponse
    {
        $productQuery = Product::query();
        $paginatedResults = $this->paginator->paginate($request, $productQuery);

        return response()->json(ProductResource::collection($paginatedResults), Response::HTTP_OK);
    }


    public function store(ProductCreateRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }


    public function show(Product $product): JsonResponse
    {
        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }

    public function update(
        ProductUpdateRequest $request,
        Product $product
    ): JsonResponse {
        $product->update($request->validated());

        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }


    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([], Response::HTTP_OK);
    }
}
