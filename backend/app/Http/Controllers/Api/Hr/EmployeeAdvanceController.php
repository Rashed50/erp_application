<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreAdvanceRecoveryRequest;
use App\Http\Requests\Hr\StoreEmployeeAdvanceRequest;
use App\Http\Requests\Hr\UpdateEmployeeAdvanceRequest;
use App\Http\Resources\EmployeeAdvanceResource;
use App\Http\Responses\ApiResponse;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvanceRecovery;
use App\Services\EmployeeAdvanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    public function __construct(private readonly EmployeeAdvanceService $advanceService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            ...$request->only(['status', 'search', 'from_date', 'to_date']),
            'employee_id' => $request->integer('employee_id') ?: null,
        ];

        $advances = $this->advanceService->paginate((int) $request->integer('per_page', 15), $filters);

        return ApiResponse::success([
            'advances' => EmployeeAdvanceResource::collection($advances),
            'totals' => $this->advanceService->totals($filters),
            'meta' => [
                'current_page' => $advances->currentPage(),
                'per_page' => $advances->perPage(),
                'total' => $advances->total(),
                'last_page' => $advances->lastPage(),
            ],
        ]);
    }

    public function show(EmployeeAdvance $employeeAdvance): JsonResponse
    {
        return ApiResponse::success(new EmployeeAdvanceResource($this->advanceService->withDetail($employeeAdvance)));
    }

    public function store(StoreEmployeeAdvanceRequest $request): JsonResponse
    {
        $advance = $this->advanceService->create($request->validated());

        return ApiResponse::success(new EmployeeAdvanceResource($advance), __('Advance salary saved successfully.'), 201);
    }

    public function update(UpdateEmployeeAdvanceRequest $request, EmployeeAdvance $employeeAdvance): JsonResponse
    {
        $advance = $this->advanceService->update($employeeAdvance, $request->validated());

        return ApiResponse::success(
            new EmployeeAdvanceResource($advance),
            __('Advance salary updated. Generate any unapproved salary again to apply the new installment.'),
        );
    }

    public function destroy(EmployeeAdvance $employeeAdvance): JsonResponse
    {
        $this->advanceService->delete($employeeAdvance);

        return ApiResponse::success(message: __('Advance salary deleted successfully.'));
    }

    /**
     * Record cash paid back by the employee.
     */
    public function storeRecovery(StoreAdvanceRecoveryRequest $request, EmployeeAdvance $employeeAdvance): JsonResponse
    {
        $advance = $this->advanceService->recordCashRecovery($employeeAdvance, $request->validated());

        return ApiResponse::success(new EmployeeAdvanceResource($advance), __('Cash repayment recorded successfully.'), 201);
    }

    public function destroyRecovery(EmployeeAdvanceRecovery $advanceRecovery): JsonResponse
    {
        $advance = $this->advanceService->deleteRecovery($advanceRecovery);

        return ApiResponse::success(new EmployeeAdvanceResource($advance), __('Cash repayment deleted successfully.'));
    }
}
