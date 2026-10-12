<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\CreateTransferRequest;
use App\Models\InventoryTransfer;
use App\Services\Inventory\InventoryTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryTransferController extends Controller
{
    public function __construct(
        private readonly InventoryTransferService $transferService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = InventoryTransfer::query()
            ->with(['fromWarehouse', 'toWarehouse', 'requester', 'receiver', 'items.item'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('from_warehouse_id')) {
            $query->where('from_warehouse_id', $request->integer('from_warehouse_id'));
        }

        if ($request->filled('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->integer('to_warehouse_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(CreateTransferRequest $request): JsonResponse
    {
        $data = $request->validated();

        $transfer = $this->transferService->create(
            $data['from_warehouse_id'],
            $data['to_warehouse_id'],
            $data['items'],
            $request->user()?->emp_auto_id,
            $data['transfer_date'] ?? null,
            $data['remarks'] ?? null
        );

        return response()->json([
            'message' => 'Inventory transfer created successfully.',
            'data' => $transfer,
        ], 201);
    }

    public function show(InventoryTransfer $inventoryTransfer): JsonResponse
    {
        return response()->json([
            'data' => $inventoryTransfer->load([
                'fromWarehouse', 'toWarehouse', 'requester', 'approver', 'receiver', 'items.item'
            ]),
        ]);
    }

    public function approve(
        Request $request,
        InventoryTransfer $inventoryTransfer
    ): JsonResponse {
        if ($inventoryTransfer->status !== 'requested') {
            return response()->json(['message' => 'Only requested transfers can be approved.'], 422);
        }

        $inventoryTransfer->update([
            'status' => 'approved',
            'approved_by' => $request->user()?->emp_auto_id,
        ]);

        return response()->json([
            'message' => 'Transfer approved successfully.',
            'data' => $inventoryTransfer->fresh(['items']),
        ]);
    }

    public function dispatch(
        Request $request,
        InventoryTransfer $inventoryTransfer
    ): JsonResponse {
        $transfer = $this->transferService->dispatch(
            $inventoryTransfer,
            $request->user()?->emp_auto_id
        );

        return response()->json([
            'message' => 'Inventory transfer dispatched successfully.',
            'data' => $transfer,
        ]);
    }

    public function receive(
        Request $request,
        InventoryTransfer $inventoryTransfer
    ): JsonResponse {
        $transfer = $this->transferService->receive(
            $inventoryTransfer,
            $request->user()?->emp_auto_id
        );

        return response()->json([
            'message' => 'Inventory transfer received successfully.',
            'data' => $transfer,
        ]);
    }
}
