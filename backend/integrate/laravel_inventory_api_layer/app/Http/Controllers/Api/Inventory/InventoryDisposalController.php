<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\DisposeInventoryRequest;
use App\Models\InventoryDisposal;
use App\Services\Inventory\InventoryDisposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryDisposalController extends Controller
{
    public function __construct(
        private readonly InventoryDisposalService $disposalService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = InventoryDisposal::query()
            ->with(['warehouse', 'approver', 'disposer', 'items.item', 'items.serial'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'warehouse_id' => ['required', 'integer', 'exists:inventory_warehouses,id'],
            'disposal_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        $disposal = InventoryDisposal::create([
            'disposal_no' => 'DIS-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
            'warehouse_id' => $data['warehouse_id'],
            'disposal_date' => $data['disposal_date'] ?? now()->toDateString(),
            'reason' => $data['reason'],
            'status' => 'draft',
            'remarks' => $data['remarks'] ?? null,
        ]);

        return response()->json([
            'message' => 'Disposal document created.',
            'data' => $disposal,
        ], 201);
    }

    public function approve(
        Request $request,
        InventoryDisposal $inventoryDisposal
    ): JsonResponse {
        $inventoryDisposal->update([
            'status' => 'approved',
            'approved_by' => $request->user()?->emp_auto_id,
        ]);

        return response()->json([
            'message' => 'Disposal approved.',
            'data' => $inventoryDisposal->fresh(),
        ]);
    }

    public function dispose(
        DisposeInventoryRequest $request,
        InventoryDisposal $inventoryDisposal
    ): JsonResponse {
        $disposal = $this->disposalService->dispose(
            $inventoryDisposal,
            $request->validated()['items'],
            $request->user()?->emp_auto_id
        );

        return response()->json([
            'message' => 'Inventory disposed successfully.',
            'data' => $disposal,
        ]);
    }
}
