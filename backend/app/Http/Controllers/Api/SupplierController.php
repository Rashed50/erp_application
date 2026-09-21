<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Http\Responses\ApiResponse;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(private readonly SupplierService $supplierService) {}

    public function index(Request $request): JsonResponse
    {
        $suppliers = $this->supplierService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('active_status') ? $request->boolean('active_status') : null,
        );

        return ApiResponse::success([
            'suppliers' => SupplierResource::collection($suppliers),
            'meta' => [
                'current_page' => $suppliers->currentPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
                'last_page' => $suppliers->lastPage(),
            ],
        ]);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return ApiResponse::success(new SupplierResource($this->supplierService->find($supplier)));
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return ApiResponse::success(new SupplierResource($supplier), 'Supplier created successfully.', 201);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return ApiResponse::success(new SupplierResource($supplier), 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        if (! $supplier->canBeDeleted()) {
            return ApiResponse::error('This supplier has ledger transactions or purchases and cannot be deleted.', 422);
        }

        $this->supplierService->delete($supplier);

        return ApiResponse::success(message: 'Supplier deleted successfully.');
    }
}
