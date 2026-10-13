<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreInventoryTransferRequest;
use App\Http\Resources\InventoryTransferResource;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryTransfer;
use App\Services\InventoryTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryTransferController extends Controller
{
    public function __construct(private readonly InventoryTransferService $transferService) {}

    public function index(Request $request): JsonResponse
    {
        $transfers = $this->transferService->paginate((int) $request->integer('per_page', 15), [
            'status' => $request->string('status')->value() ?: null,
            'from_warehouse_id' => $request->filled('from_warehouse_id') ? $request->integer('from_warehouse_id') : null,
            'to_warehouse_id' => $request->filled('to_warehouse_id') ? $request->integer('to_warehouse_id') : null,
        ]);

        return ApiResponse::success([
            'transfers' => InventoryTransferResource::collection($transfers),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
                'last_page' => $transfers->lastPage(),
            ],
            'statuses' => InventoryTransfer::STATUSES,
        ]);
    }

    public function show(InventoryTransfer $transfer): JsonResponse
    {
        return ApiResponse::success(new InventoryTransferResource($transfer->load([
            'fromWarehouse', 'toWarehouse', 'requester', 'approver', 'dispatcher', 'receiver', 'items.item',
        ])));
    }

    public function store(StoreInventoryTransferRequest $request): JsonResponse
    {
        $transfer = $this->transferService->create($request->validated());

        return ApiResponse::success(new InventoryTransferResource($transfer), __('Transfer requested successfully.'), 201);
    }

    public function approve(InventoryTransfer $transfer): JsonResponse
    {
        $transfer = $this->transferService->approve($transfer);

        return ApiResponse::success(new InventoryTransferResource($transfer), __('Transfer approved successfully.'));
    }

    public function dispatch(InventoryTransfer $transfer): JsonResponse
    {
        $transfer = $this->transferService->dispatch($transfer);

        return ApiResponse::success(new InventoryTransferResource($transfer), __('Transfer dispatched successfully.'));
    }

    public function receive(InventoryTransfer $transfer): JsonResponse
    {
        $transfer = $this->transferService->receive($transfer);

        return ApiResponse::success(new InventoryTransferResource($transfer), __('Transfer received successfully.'));
    }
}
