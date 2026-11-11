<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = InventoryItem::query()
            ->with(['category', 'unit'])
            ->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->string('item_type'));
        }

        if ($request->filled('track_type')) {
            $query->where('track_type', $request->string('track_type'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function show(InventoryItem $inventoryItem): JsonResponse
    {
        return response()->json([
            'data' => $inventoryItem->load(['category', 'unit', 'stocks.warehouse']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item_code' => ['required', 'string', 'max:50', 'unique:inventory_items,item_code'],
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:inventory_categories,id'],
            'unit_id' => ['required', 'integer', 'exists:inventory_units,id'],
            'item_type' => ['required', 'in:asset,non_asset'],
            'track_type' => ['required', 'in:quantity,serial'],
            'minimum_stock' => ['nullable', 'numeric', 'gte:0'],
            'description' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::create($data);

        return response()->json([
            'message' => 'Inventory item created successfully.',
            'data' => $item->load(['category', 'unit']),
        ], 201);
    }
}
