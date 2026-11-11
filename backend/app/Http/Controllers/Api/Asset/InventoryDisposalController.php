<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\StoreInventoryDisposalRequest;
use App\Http\Resources\InventoryDisposalResource;
use App\Http\Responses\ApiResponse;
use App\Models\InventoryDisposal;
use App\Services\InventoryDisposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryDisposalController extends Controller
{
    public function __construct(private readonly InventoryDisposalService $disposalService) {}

    public function index(Request $request): JsonResponse
    {
        $disposals = $this->disposalService->paginate((int) $request->integer('per_page', 15), [
            'status' => $request->string('status')->value() ?: null,
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
        ]);

        return ApiResponse::success([
            'disposals' => InventoryDisposalResource::collection($disposals),
            'meta' => [
                'current_page' => $disposals->currentPage(),
                'per_page' => $disposals->perPage(),
                'total' => $disposals->total(),
                'last_page' => $disposals->lastPage(),
            ],
            'statuses' => InventoryDisposal::STATUSES,
        ]);
    }

    public function show(InventoryDisposal $disposal): JsonResponse
    {
        return ApiResponse::success(new InventoryDisposalResource($disposal->load([
            'warehouse', 'creator', 'approver', 'disposer', 'items.item', 'items.serial',
        ])));
    }

    public function store(StoreInventoryDisposalRequest $request): JsonResponse
    {
        $disposal = $this->disposalService->create($request->validated());

        return ApiResponse::success(new InventoryDisposalResource($disposal), __('Disposal drafted successfully.'), 201);
    }

    public function approve(InventoryDisposal $disposal): JsonResponse
    {
        $disposal = $this->disposalService->approve($disposal);

        return ApiResponse::success(new InventoryDisposalResource($disposal), __('Disposal approved successfully.'));
    }

    public function dispose(InventoryDisposal $disposal): JsonResponse
    {
        $disposal = $this->disposalService->dispose($disposal);

        return ApiResponse::success(new InventoryDisposalResource($disposal), __('Items disposed successfully.'));
    }
}
