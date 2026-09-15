<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private readonly CustomerService $customerService) {}

    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            // `filled` (not `has`) so a query string like `?active_status=` from a
            // reset dropdown is treated as "no filter" rather than "false".
            $request->filled('active_status') ? $request->boolean('active_status') : null,
        );

        return ApiResponse::success([
            'customers' => CustomerResource::collection($customers),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'last_page' => $customers->lastPage(),
            ],
        ]);
    }

    public function show(Customer $customer): JsonResponse
    {
        return ApiResponse::success(new CustomerResource($this->customerService->find($customer)));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());

        return ApiResponse::success(new CustomerResource($customer), 'Customer created successfully.', 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        return ApiResponse::success(new CustomerResource($customer), 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): JsonResponse
    {
        if (! $customer->canBeDeleted()) {
            return ApiResponse::error('This customer has ledger transactions and cannot be deleted.', 422);
        }

        $this->customerService->delete($customer);

        return ApiResponse::success(message: 'Customer deleted successfully.');
    }
}
