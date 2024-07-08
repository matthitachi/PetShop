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
        $this->middleware(['jwt', 'role:user'])->except(['index', 'show']);
        $this->paginator = $paginator;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product",
     *     tags={"Products"},
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
    public function index(Request $request): JsonResponse
    {
        $productQuery = Product::query();
        $paginatedResults = $this->paginator->paginate($request, $productQuery);

        return response()->json(ProductResource::collection($paginatedResults), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                   property="category_uuid",
     *                   description="Category UUID parameter",
     *                   type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Product title"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     description="Product price",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Product description",
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     type="object",
     *                     description="Product metadata",
     *                     example={"image": "string","brand": "string"}
     *                 ),
     *                 required={"category_uuid", "title", "price", "description", "metadata"}
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
    public function store(ProductCreateRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
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
    public function show(Product $product): JsonResponse
    {
        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(
     *                   property="category_uuid",
     *                   description="Category UUID parameter",
     *                   type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Product title"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     description="Product price",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Product description",
     *                 ),
     *                 @OA\Property(
     *                     property="metadata",
     *                     type="object",
     *                     description="Product metadata",
     *                     example={"image": "string","brand": "string"}
     *                 ),
     *                 required={"category_uuid", "title", "price", "description", "metadata"}
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
    public function update(
        ProductUpdateRequest $request,
        Product $product
    ): JsonResponse {
        $product->update($request->validated());

        return response()->json(new ProductResource($product), Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/product/{uuid}",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
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
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([], Response::HTTP_OK);
    }
}
