<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Http\Responses\ApiResponse;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService) {}

    public function index(Request $request): JsonResponse
    {
        $purchases = $this->purchaseService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->integer('supplier_id') ?: null,
        );

        return ApiResponse::success([
            'purchases' => PurchaseResource::collection($purchases),
            'meta' => [
                'current_page' => $purchases->currentPage(),
                'per_page' => $purchases->perPage(),
                'total' => $purchases->total(),
                'last_page' => $purchases->lastPage(),
            ],
        ]);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return ApiResponse::success(new PurchaseResource($this->purchaseService->find($purchase)));
    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->create($request->validated());

        return ApiResponse::success(new PurchaseResource($purchase), 'Purchase created successfully.', 201);
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): JsonResponse
    {
        $purchase = $this->purchaseService->update($purchase, $request->validated());

        return ApiResponse::success(new PurchaseResource($purchase), 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $this->purchaseService->delete($purchase);

        return ApiResponse::success(message: 'Purchase deleted successfully.');
    }
}
