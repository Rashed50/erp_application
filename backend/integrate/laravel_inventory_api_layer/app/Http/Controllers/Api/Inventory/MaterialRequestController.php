<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ApproveMaterialRequest;
use App\Http\Requests\Inventory\StoreMaterialRequest;
use App\Models\MaterialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MaterialRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MaterialRequest::query()
            ->with(['project', 'requester', 'items.item'])
            ->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $data = $request->validated();

        $materialRequest = MaterialRequest::create([
            'request_no' => 'MR-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
            'project_id' => $data['project_id'],
            'requested_by' => $request->user()?->emp_auto_id,
            'request_date' => $data['request_date'] ?? now()->toDateString(),
            'required_date' => $data['required_date'] ?? null,
            'priority' => $data['priority'],
            'status' => 'submitted',
            'remarks' => $data['remarks'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            $materialRequest->items()->create([
                'item_id' => $item['item_id'],
                'requested_qty' => $item['requested_qty'],
                'remarks' => $item['remarks'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Material request created successfully.',
            'data' => $materialRequest->load(['project', 'items.item']),
        ], 201);
    }

    public function show(MaterialRequest $materialRequest): JsonResponse
    {
        return response()->json([
            'data' => $materialRequest->load([
                'project', 'requester', 'approver', 'items.item', 'purchaseOrders'
            ]),
        ]);
    }

    public function approve(
        ApproveMaterialRequest $request,
        MaterialRequest $materialRequest
    ): JsonResponse {
        if (!in_array($materialRequest->status, ['submitted'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Only submitted material requests can be approved.',
            ]);
        }

        foreach ($request->validated()['items'] as $line) {
            $requestItem = $materialRequest->items()->findOrFail($line['id']);

            if ((float) $line['approved_qty'] > (float) $requestItem->requested_qty) {
                throw ValidationException::withMessages([
                    'approved_qty' => 'Approved quantity cannot exceed requested quantity.',
                ]);
            }

            $requestItem->update(['approved_qty' => $line['approved_qty']]);
        }

        $materialRequest->update([
            'status' => 'approved',
            'approved_by' => $request->user()?->emp_auto_id,
            'approved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Material request approved successfully.',
            'data' => $materialRequest->fresh(['items.item', 'approver']),
        ]);
    }
}
