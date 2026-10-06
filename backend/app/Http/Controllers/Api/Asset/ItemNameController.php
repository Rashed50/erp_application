<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreItemNameRequest;
use App\Http\Requests\Asset\UpdateAssetStatusRequest;
use App\Http\Requests\Asset\UpdateItemNameRequest;
use App\Http\Resources\ItemNameResource;
use App\Http\Responses\ApiResponse;
use App\Models\ItemName;
use App\Services\ItemNameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemNameController extends Controller
{
    public function __construct(private readonly ItemNameService $itemNameService) {}

    public function index(Request $request): JsonResponse
    {
        $items = $this->itemNameService->paginate((int) $request->integer('per_page', 15), [
            'search' => $request->string('search')->value() ?: null,
            'status' => $request->filled('status') ? $request->boolean('status') : null,
            'icatg_id' => $request->filled('icatg_id') ? $request->integer('icatg_id') : null,
            'iscatg_id' => $request->filled('iscatg_id') ? $request->integer('iscatg_id') : null,
            'itype_id' => $request->filled('itype_id') ? $request->integer('itype_id') : null,
        ]);

        return ApiResponse::success([
            'item_names' => ItemNameResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
            'types' => collect(ItemName::TYPES)
                ->map(fn (string $name, int $id) => ['id' => $id, 'name' => $name])
                ->values(),
        ]);
    }

    public function show(ItemName $itemName): JsonResponse
    {
        return ApiResponse::success(new ItemNameResource($itemName->load(['category', 'subCategory'])));
    }

    public function store(StoreItemNameRequest $request): JsonResponse
    {
        $item = $this->itemNameService->create($request->validated());

        return ApiResponse::success(new ItemNameResource($item), __('Item created successfully.'), 201);
    }

    public function update(UpdateItemNameRequest $request, ItemName $itemName): JsonResponse
    {
        $item = $this->itemNameService->update($itemName, $request->validated());

        return ApiResponse::success(new ItemNameResource($item), __('Item updated successfully.'));
    }

    public function updateStatus(UpdateAssetStatusRequest $request, ItemName $itemName): JsonResponse
    {
        $item = $this->itemNameService->setStatus($itemName, $request->boolean('status'));

        return ApiResponse::success(
            new ItemNameResource($item),
            $item->item_status ? __('Item activated successfully.') : __('Item deactivated successfully.'),
        );
    }
}
