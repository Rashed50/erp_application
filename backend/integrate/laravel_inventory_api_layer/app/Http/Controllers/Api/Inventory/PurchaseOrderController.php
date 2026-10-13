<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePurchaseOrderRequest;
use App\Models\PurchaseOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PurchaseOrder::query()
            ->with(['supplier', 'materialRequest', 'items.item'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $data = $request->validated();

        $purchaseOrder = DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $discount = 0;
            $tax = 0;

            foreach ($data['items'] as $line) {
                $lineBase = $line['quantity'] * $line['unit_price'];
                $discount += $line['discount'] ?? 0;
                $tax += $line['tax'] ?? 0;
                $subtotal += $lineBase;
            }

            $order = PurchaseOrder::create([
                'po_no' => 'PO-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'supplier_id' => $data['supplier_id'],
                'material_request_id' => $data['material_request_id'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'status' => 'submitted',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'grand_total' => $subtotal - $discount + $tax,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => $request->user()?->emp_auto_id,
            ]);

            foreach ($data['items'] as $line) {
                $lineTotal = ($line['quantity'] * $line['unit_price'])
                    - ($line['discount'] ?? 0)
                    + ($line['tax'] ?? 0);

                $order->items()->create([
                    'item_id' => $line['item_id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount' => $line['discount'] ?? 0,
                    'tax' => $line['tax'] ?? 0,
                    'total' => $lineTotal,
                ]);
            }

            return $order;
        });

        return response()->json([
            'message' => 'Purchase order created successfully.',
            'data' => $purchaseOrder->load(['supplier', 'materialRequest', 'items.item']),
        ], 201);
    }

    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        return response()->json([
            'data' => $purchaseOrder->load([
                'supplier', 'materialRequest', 'items.item', 'receipts.items'
            ]),
        ]);
    }
}
