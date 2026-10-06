<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreItemCategoryRequest;
use App\Http\Requests\Asset\UpdateAssetStatusRequest;
use App\Http\Requests\Asset\UpdateItemCategoryRequest;
use App\Http\Resources\ItemCategoryResource;
use App\Http\Responses\ApiResponse;
use App\Models\ItemCategory;
use App\Services\ItemCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function __construct(private readonly ItemCategoryService $itemCategoryService) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->itemCategoryService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('status') ? $request->boolean('status') : null,
        );

        return ApiResponse::success([
            'item_categories' => ItemCategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
            ],
        ]);
    }

    /**
     * Active categories for dropdowns on the sub category and item forms.
     */
    public function options(): JsonResponse
    {
        $categories = ItemCategory::query()->active()->orderBy('icatg_name')->get();

        return ApiResponse::success(ItemCategoryResource::collection($categories));
    }

    public function show(ItemCategory $itemCategory): JsonResponse
    {
        return ApiResponse::success(new ItemCategoryResource($itemCategory));
    }

    public function store(StoreItemCategoryRequest $request): JsonResponse
    {
        $category = $this->itemCategoryService->create($request->validated());

        return ApiResponse::success(new ItemCategoryResource($category), __('Item category created successfully.'), 201);
    }

    public function update(UpdateItemCategoryRequest $request, ItemCategory $itemCategory): JsonResponse
    {
        $category = $this->itemCategoryService->update($itemCategory, $request->validated());

        return ApiResponse::success(new ItemCategoryResource($category), __('Item category updated successfully.'));
    }

    public function updateStatus(UpdateAssetStatusRequest $request, ItemCategory $itemCategory): JsonResponse
    {
        $category = $this->itemCategoryService->setStatus($itemCategory, $request->boolean('status'));

        return ApiResponse::success(
            new ItemCategoryResource($category),
            $category->status ? __('Item category activated successfully.') : __('Item category deactivated successfully.'),
        );
    }
}
