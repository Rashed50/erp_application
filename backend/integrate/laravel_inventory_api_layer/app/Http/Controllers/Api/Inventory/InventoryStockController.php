<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryStockController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InventoryStock::query()
            ->with(['warehouse', 'item.category', 'item.unit'])
            ->whereHas('warehouse', fn ($q) => $q->where('status', true));

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->integer('warehouse_id'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        if ($request->boolean('low_stock')) {
            $query->whereColumn('available_quantity', '<=', 'inventory_items.minimum_stock')
                ->join('inventory_items', 'inventory_items.id', '=', 'inventory_stocks.item_id')
                ->select('inventory_stocks.*');
        }

        return response()->json($query->paginate($request->integer('per_page', 30)));
    }

    public function transactions(Request $request): JsonResponse
    {
        $query = InventoryTransaction::query()
            ->with(['item', 'serial', 'fromLocation', 'toLocation', 'performedBy'])
            ->latest('transaction_date');

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->integer('item_id'));
        }

        if ($request->filled('warehouse_id')) {
            $warehouseId = $request->integer('warehouse_id');
            $query->where(function ($q) use ($warehouseId) {
                $q->where('from_location_id', $warehouseId)
                  ->orWhere('to_location_id', $warehouseId);
            });
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->string('transaction_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date('date_to'));
        }

        return response()->json($query->paginate($request->integer('per_page', 50)));
    }
}
