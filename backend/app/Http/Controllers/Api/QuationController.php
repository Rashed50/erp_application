<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quotation\StoreQuotationRequest;
use App\Http\Requests\Quotation\UpdateQuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct(private readonly QuotationService $quotationService) {}

    public function index(Request $request): JsonResponse
    {
        $quotations = $this->quotationService->paginate(
            (int) $request->integer('per_page', 15),
            $request->string('search')->value() ?: null,
            $request->integer('customer_id') ?: null,
        );

        return ApiResponse::success([
            'quotations' => QuotationResource::collection($quotations),
            'meta' => [
                'current_page' => $quotations->currentPage(),
                'per_page' => $quotations->perPage(),
                'total' => $quotations->total(),
                'last_page' => $quotations->lastPage(),
            ],
        ]);
    }

    public function show(Quotation $quotation): JsonResponse
    {
        return ApiResponse::success(new QuotationResource($this->quotationService->find($quotation)));
    }

    public function store(StoreQuotationRequest $request): JsonResponse
    {
        $quotation = $this->quotationService->create($request->validated());

        return ApiResponse::success(new QuotationResource($quotation), __('Quotation created successfully.'), 201);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation): JsonResponse
    {
        $quotation = $this->quotationService->update($quotation, $request->validated());

        return ApiResponse::success(new QuotationResource($quotation), __('Quotation updated successfully.'));
    }

    public function destroy(Quotation $quotation): JsonResponse
    {
        $this->quotationService->delete($quotation);

        return ApiResponse::success(message: __('Quotation deleted successfully.'));
    }
}
