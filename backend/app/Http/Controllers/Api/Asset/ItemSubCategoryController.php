<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreItemSubCategoryRequest;
use App\Http\Requests\Asset\UpdateAssetStatusRequest;
use App\Http\Requests\Asset\UpdateItemSubCategoryRequest;
use App\Http\Resources\ItemSubCategoryResource;
use App\Http\Responses\ApiResponse;
use App\Models\ItemSubCategory;
use App\Services\ItemSubCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemSubCategoryController extends Controller
{
    public function __construct(private readonly ItemSubCategoryService $itemSubCategoryService) {}

    public function index(Request $request): JsonResponse
    {
        $subCategories = $this->itemSubCategoryService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('status') ? $request->boolean('status') : null,
            $request->filled('icatg_id') ? $request->integer('icatg_id') : null,
        );

        return ApiResponse::success([
            'item_sub_categories' => ItemSubCategoryResource::collection($subCategories),
            'meta' => [
                'current_page' => $subCategories->currentPage(),
                'per_page' => $subCategories->perPage(),
                'total' => $subCategories->total(),
                'last_page' => $subCategories->lastPage(),
            ],
        ]);
    }

    /**
     * Active sub categories, optionally of one category, for the item form dropdown.
     */
    public function options(Request $request): JsonResponse
    {
        $subCategories = ItemSubCategory::query()
            ->active()
            ->when($request->filled('icatg_id'), fn ($query) => $query->where('icatg_id', $request->integer('icatg_id')))
            ->orderBy('iscatg_name')
            ->get();

        return ApiResponse::success(ItemSubCategoryResource::collection($subCategories));
    }

    public function show(ItemSubCategory $itemSubCategory): JsonResponse
    {
        return ApiResponse::success(new ItemSubCategoryResource($itemSubCategory->load('category')));
    }

    public function store(StoreItemSubCategoryRequest $request): JsonResponse
    {
        $subCategory = $this->itemSubCategoryService->create($request->validated());

        return ApiResponse::success(new ItemSubCategoryResource($subCategory), __('Item sub category created successfully.'), 201);
    }

    public function update(UpdateItemSubCategoryRequest $request, ItemSubCategory $itemSubCategory): JsonResponse
    {
        $subCategory = $this->itemSubCategoryService->update($itemSubCategory, $request->validated());

        return ApiResponse::success(new ItemSubCategoryResource($subCategory), __('Item sub category updated successfully.'));
    }

    public function updateStatus(UpdateAssetStatusRequest $request, ItemSubCategory $itemSubCategory): JsonResponse
    {
        $subCategory = $this->itemSubCategoryService->setStatus($itemSubCategory, $request->boolean('status'));

        return ApiResponse::success(
            new ItemSubCategoryResource($subCategory),
            $subCategory->status ? __('Item sub category activated successfully.') : __('Item sub category deactivated successfully.'),
        );
    }
}
