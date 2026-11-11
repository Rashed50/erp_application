<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\ReturnEmployeeInventoryRequest;
use App\Http\Requests\Inventory\StoreIssueRequest;
use App\Models\EmployeeInventory;
use App\Services\Inventory\EmployeeInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeInventoryController extends Controller
{
    public function __construct(
        private readonly EmployeeInventoryService $inventoryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = EmployeeInventory::query()
            ->with(['employee', 'item', 'serial', 'project', 'warehouse', 'issuer'])
            ->latest();

        if ($request->filled('employee_auto_id')) {
            $query->where('employee_auto_id', $request->integer('employee_auto_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 20)));
    }

    public function issue(StoreIssueRequest $request): JsonResponse
    {
        $data = $request->validated();

        $record = $this->inventoryService->issue(
            $data['warehouse_id'],
            $data['employee_auto_id'],
            $data['item_id'],
            (float) $data['quantity'],
            $request->user()?->emp_auto_id ?? $data['issued_by'],
            $data['project_id'] ?? null,
            $data['serial_id'] ?? null,
            $data['expected_return_date'] ?? null,
            $data['remarks'] ?? null
        );

        return response()->json([
            'message' => 'Inventory issued to employee successfully.',
            'data' => $record,
        ], 201);
    }

    public function show(EmployeeInventory $employeeInventory): JsonResponse
    {
        return response()->json([
            'data' => $employeeInventory->load([
                'employee', 'item', 'serial', 'project', 'warehouse', 'issuer', 'returns'
            ]),
        ]);
    }

    public function return(
        ReturnEmployeeInventoryRequest $request,
        EmployeeInventory $employeeInventory
    ): JsonResponse {
        $data = $request->validated();

        $return = $this->inventoryService->return(
            $employeeInventory,
            (float) $data['quantity'],
            $data['destination_warehouse_id'],
            $request->user()?->emp_auto_id ?? $data['received_by'],
            $data['condition'],
            $data['reason'] ?? null,
            $data['remarks'] ?? null
        );

        return response()->json([
            'message' => 'Inventory returned successfully.',
            'data' => $return,
        ]);
    }
}
