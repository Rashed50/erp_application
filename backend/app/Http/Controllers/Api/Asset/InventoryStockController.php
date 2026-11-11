<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Resources\InventoryStockResource;
use App\Http\Resources\InventoryTransactionResource;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryTransaction;
use App\Services\InventoryStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryStockController extends Controller
{
    public function __construct(private readonly InventoryStockService $stockService) {}

    /**
     * Current stock per store and item.
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = $this->stockService->paginateStocks((int) $request->integer('per_page', 15), [
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
            'item_id' => $request->filled('item_id') ? $request->integer('item_id') : null,
            'low_stock' => $request->boolean('low_stock'),
        ]);

        return ApiResponse::success([
            'stocks' => InventoryStockResource::collection($stocks),
            'meta' => [
                'current_page' => $stocks->currentPage(),
                'per_page' => $stocks->perPage(),
                'total' => $stocks->total(),
                'last_page' => $stocks->lastPage(),
            ],
        ]);
    }

    /**
     * The stock movement ledger.
     */
    public function transactions(Request $request): JsonResponse
    {
        $transactions = $this->stockService->paginateTransactions((int) $request->integer('per_page', 15), [
            'item_id' => $request->filled('item_id') ? $request->integer('item_id') : null,
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
            'employee_id' => $request->filled('employee_id') ? $request->integer('employee_id') : null,
            'transaction_type' => $request->string('transaction_type')->value() ?: null,
            'date_from' => $request->string('date_from')->value() ?: null,
            'date_to' => $request->string('date_to')->value() ?: null,
        ]);

        return ApiResponse::success([
            'transactions' => InventoryTransactionResource::collection($transactions),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
            'types' => InventoryTransaction::TYPES,
        ]);
    }
}
