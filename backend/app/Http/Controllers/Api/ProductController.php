<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('active_status') ? $request->boolean('active_status') : null,
        );

        return ApiResponse::success([
            'products' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return ApiResponse::success(new ProductResource($product));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product created successfully.', 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product updated successfully.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return ApiResponse::success(message: 'Product deleted successfully.');
    }
}
