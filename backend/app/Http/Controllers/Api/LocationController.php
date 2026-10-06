<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\District;
use App\Models\Division;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Bangladesh division → district → thana (upazila) lists for cascading
 * address dropdowns, plus quick-add for a missing entry.
 */
class LocationController extends Controller
{
    public function divisions(): JsonResponse
    {
        return ApiResponse::success(Division::query()->orderBy('name')->get(['id', 'name', 'bn_name']));
    }

    public function districts(Request $request): JsonResponse
    {
        $request->validate(['division_id' => ['required', 'integer']]);

        return ApiResponse::success(
            District::query()->where('division_id', $request->integer('division_id'))->orderBy('name')->get(['id', 'division_id', 'name', 'bn_name']),
        );
    }

    public function upazilas(Request $request): JsonResponse
    {
        $request->validate(['district_id' => ['required', 'integer']]);

        return ApiResponse::success(
            Upazila::query()->where('district_id', $request->integer('district_id'))->orderBy('name')->get(['id', 'district_id', 'name', 'bn_name']),
        );
    }

    public function storeDivision(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('divisions', 'name')],
            'bn_name' => ['nullable', 'string', 'max:255'],
        ]);

        $division = Division::create($data);

        return ApiResponse::success($division->only(['id', 'name', 'bn_name']), __('Division created successfully.'), 201);
    }

    public function storeDistrict(Request $request): JsonResponse
    {
        $data = $request->validate([
            'division_id' => ['required', 'integer', Rule::exists('divisions', 'id')],
            'name' => ['required', 'string', 'max:255', Rule::unique('districts', 'name')->where('division_id', $request->integer('division_id'))],
            'bn_name' => ['nullable', 'string', 'max:255'],
        ]);

        $district = District::create($data);

        return ApiResponse::success($district->only(['id', 'division_id', 'name', 'bn_name']), __('District created successfully.'), 201);
    }

    public function storeUpazila(Request $request): JsonResponse
    {
        $data = $request->validate([
            'district_id' => ['required', 'integer', Rule::exists('districts', 'id')],
            'name' => ['required', 'string', 'max:255', Rule::unique('upazilas', 'name')->where('district_id', $request->integer('district_id'))],
            'bn_name' => ['nullable', 'string', 'max:255'],
        ]);

        $upazila = Upazila::create($data);

        return ApiResponse::success($upazila->only(['id', 'district_id', 'name', 'bn_name']), __('Thana created successfully.'), 201);
    }
}
