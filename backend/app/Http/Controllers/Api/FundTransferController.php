<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FundTransfer\StoreFundTransferRequest;
use App\Http\Resources\FundTransferResource;
use App\Http\Responses\ApiResponse;
use App\Models\FundTransfer;
use App\Services\FundTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FundTransferController extends Controller
{
    public function __construct(private readonly FundTransferService $transferService) {}

    public function index(Request $request): JsonResponse
    {
        $transfers = $this->transferService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->filled('account_id') ? $request->integer('account_id') : null,
            $request->string('from_date')->value() ?: null,
            $request->string('to_date')->value() ?: null,
        );

        return ApiResponse::success([
            'transfers' => FundTransferResource::collection($transfers),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
                'last_page' => $transfers->lastPage(),
            ],
        ]);
    }

    public function show(FundTransfer $fund_transfer): JsonResponse
    {
        return ApiResponse::success(new FundTransferResource($this->transferService->find($fund_transfer)));
    }

    public function store(StoreFundTransferRequest $request): JsonResponse
    {
        $transfer = $this->transferService->create($request->validated());

        return ApiResponse::success(new FundTransferResource($transfer), 'Fund transfer saved successfully.', 201);
    }

    public function destroy(FundTransfer $fund_transfer): JsonResponse
    {
        $this->transferService->delete($fund_transfer);

        return ApiResponse::success(message: 'Fund transfer deleted and reversed successfully.');
    }
}
