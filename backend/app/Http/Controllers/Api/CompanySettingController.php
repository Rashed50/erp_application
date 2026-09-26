<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanySetting\UpdateCompanySettingRequest;
use App\Http\Resources\CompanySettingResource;
use App\Http\Responses\ApiResponse;
use App\Models\CompanySetting;
use App\Services\CompanySettingService;
use Illuminate\Http\JsonResponse;

class CompanySettingController extends Controller
{
    public function __construct(private readonly CompanySettingService $companySettingService) {}

    public function show(): JsonResponse
    {
        return ApiResponse::success(new CompanySettingResource(CompanySetting::current()));
    }

    public function update(UpdateCompanySettingRequest $request): JsonResponse
    {
        $setting = $this->companySettingService->update($request->validated());

        return ApiResponse::success(new CompanySettingResource($setting), 'Settings updated successfully.');
    }
}
