<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanySetting\UpdateCompanySettingRequest;
use App\Http\Resources\CompanySettingResource;
use App\Http\Responses\ApiResponse;
use App\Models\CompanySetting;
use App\Services\CompanySettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    public function __construct(private readonly CompanySettingService $companySettingService) {}

    public function show(): JsonResponse
    {
        return ApiResponse::success(new CompanySettingResource(CompanySetting::current()));
    }

    /**
     * Public branding for the login page: only the name and logo, never contact details.
     */
    public function branding(): JsonResponse
    {
        $setting = CompanySetting::current();

        return ApiResponse::success([
            'company_name' => $setting->company_name,
            'logo_url' => $setting->logo ? Storage::disk('public')->url($setting->logo) : null,
        ]);
    }

    public function update(UpdateCompanySettingRequest $request): JsonResponse
    {
        $setting = $this->companySettingService->update($request->validated());

        return ApiResponse::success(new CompanySettingResource($setting), __('Settings updated successfully.'));
    }
}
