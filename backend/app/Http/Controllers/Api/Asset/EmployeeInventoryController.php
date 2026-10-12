<?php

namespace App\Http\Controllers\Api\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Asset\IssueEmployeeInventoryRequest;
use App\Http\Requests\Asset\ReturnEmployeeInventoryRequest;
use App\Http\Resources\EmployeeInventoryResource;
use App\Http\Resources\EmployeeInventoryReturnResource;
use App\Http\Responses\ApiResponse;
use App\Models\EmployeeInventory;
use App\Services\EmployeeInventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeInventoryController extends Controller
{
    public function __construct(private readonly EmployeeInventoryService $employeeInventoryService) {}

    public function index(Request $request): JsonResponse
    {
        $records = $this->employeeInventoryService->paginate((int) $request->integer('per_page', 15), [
            'employee_id' => $request->filled('employee_id') ? $request->integer('employee_id') : null,
            'status' => $request->string('status')->value() ?: null,
            'warehouse_id' => $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
            'work_order_id' => $request->filled('work_order_id') ? $request->integer('work_order_id') : null,
        ]);

        return ApiResponse::success([
            'employee_inventory' => EmployeeInventoryResource::collection($records),
            'meta' => [
                'current_page' => $records->currentPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'last_page' => $records->lastPage(),
            ],
        ]);
    }

    public function show(EmployeeInventory $employeeInventory): JsonResponse
    {
        return ApiResponse::success(new EmployeeInventoryResource($employeeInventory->load([
            'employee', 'item', 'serial', 'warehouse', 'workOrder', 'issuer',
            'returns.destinationWarehouse', 'returns.receiver',
        ])));
    }

    public function issue(IssueEmployeeInventoryRequest $request): JsonResponse
    {
        $record = $this->employeeInventoryService->issue($request->validated());

        return ApiResponse::success(new EmployeeInventoryResource($record), __('Item issued to the employee successfully.'), 201);
    }

    public function returnItem(ReturnEmployeeInventoryRequest $request, EmployeeInventory $employeeInventory): JsonResponse
    {
        $return = $this->employeeInventoryService->return($employeeInventory, $request->validated());

        return ApiResponse::success(new EmployeeInventoryReturnResource($return), __('Item returned successfully.'));
    }
}
