<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\SaleResource;
use App\Http\Responses\ApiResponse;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService) {}

    public function index(Request $request): JsonResponse
    {
        $sales = $this->saleService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->integer('customer_id') ?: null,
        );

        return ApiResponse::success([
            'sales' => SaleResource::collection($sales),
            'meta' => [
                'current_page' => $sales->currentPage(),
                'per_page' => $sales->perPage(),
                'total' => $sales->total(),
                'last_page' => $sales->lastPage(),
            ],
        ]);
    }

    public function show(Sale $sale): JsonResponse
    {
        return ApiResponse::success(new SaleResource($this->saleService->find($sale)));
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->saleService->create($request->validated());

        return ApiResponse::success(new SaleResource($sale), 'Sale created successfully.', 201);
    }

    public function update(UpdateSaleRequest $request, Sale $sale): JsonResponse
    {
        $sale = $this->saleService->update($sale, $request->validated());

        return ApiResponse::success(new SaleResource($sale), 'Sale updated successfully.');
    }

    public function destroy(Sale $sale): JsonResponse
    {
        $this->saleService->delete($sale);

        return ApiResponse::success(message: 'Sale deleted successfully.');
    }
}
