<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hr\StoreDesignationRequest;
use App\Http\Requests\Hr\UpdateDesignationRequest;
use App\Http\Requests\Hr\UpdateSetupStatusRequest;
use App\Http\Resources\DesignationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Designation;
use App\Services\DesignationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function __construct(private readonly DesignationService $designationService) {}

    public function index(Request $request): JsonResponse
    {
        $designations = $this->designationService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('status') ? $request->boolean('status') : null,
        );

        return ApiResponse::success([
            'designations' => DesignationResource::collection($designations),
            'meta' => [
                'current_page' => $designations->currentPage(),
                'per_page' => $designations->perPage(),
                'total' => $designations->total(),
                'last_page' => $designations->lastPage(),
            ],
        ]);
    }

    public function show(Designation $designation): JsonResponse
    {
        return ApiResponse::success(new DesignationResource($designation));
    }

    public function store(StoreDesignationRequest $request): JsonResponse
    {
        $designation = $this->designationService->create($request->validated());

        return ApiResponse::success(new DesignationResource($designation), __('Designation created successfully.'), 201);
    }

    public function update(UpdateDesignationRequest $request, Designation $designation): JsonResponse
    {
        $designation = $this->designationService->update($designation, $request->validated());

        return ApiResponse::success(new DesignationResource($designation), __('Designation updated successfully.'));
    }

    public function updateStatus(UpdateSetupStatusRequest $request, Designation $designation): JsonResponse
    {
        $designation = $this->designationService->setStatus($designation, $request->boolean('status'));

        return ApiResponse::success(
            new DesignationResource($designation),
            $designation->status ? __('Designation activated successfully.') : __('Designation deactivated successfully.'),
        );
    }
}
