<?php

namespace App\Services;

use App\Models\MaterialRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaterialRequestService
{
    /**
     * @param  array{status?: ?string, warehouse_id?: ?int, work_order_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return MaterialRequest::query()
            ->with(['warehouse', 'workOrder', 'requester', 'items.item'])
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->where('warehouse_id', $id))
            ->when($filters['work_order_id'] ?? null, fn ($query, int $id) => $query->where('work_order_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{warehouse_id: int, work_order_id?: ?int, request_date?: ?string, required_date?: ?string, priority: string, remarks?: ?string, items: array<int, array{item_id: int, requested_qty: float, remarks?: ?string}>}  $data
     */
    public function create(array $data): MaterialRequest
    {
        return DB::transaction(function () use ($data) {
            $materialRequest = MaterialRequest::create([
                'request_no' => InventoryStockService::documentNumber('MR'),
                'warehouse_id' => $data['warehouse_id'],
                'work_order_id' => $data['work_order_id'] ?? null,
                'request_date' => $data['request_date'] ?? now()->toDateString(),
                'required_date' => $data['required_date'] ?? null,
                'priority' => $data['priority'],
                'status' => MaterialRequest::STATUS_SUBMITTED,
                'remarks' => $data['remarks'] ?? null,
                'requested_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $line) {
                $materialRequest->items()->create([
                    'item_id' => $line['item_id'],
                    'requested_qty' => $line['requested_qty'],
                    'remarks' => $line['remarks'] ?? null,
                ]);
            }

            return $materialRequest->load(['warehouse', 'workOrder', 'items.item']);
        });
    }

    /**
     * @param  array<int, array{id: int, approved_qty: float}>  $lines
     */
    public function approve(MaterialRequest $materialRequest, array $lines): MaterialRequest
    {
        return DB::transaction(function () use ($materialRequest, $lines) {
            $this->ensureSubmitted($materialRequest);

            $items = $materialRequest->items()->get()->keyBy('id');

            foreach ($lines as $line) {
                $requestItem = $items->get($line['id']);

                if (! $requestItem) {
                    throw ValidationException::withMessages([
                        'items' => __('An approved line does not belong to this material request.'),
                    ]);
                }

                if ((float) $line['approved_qty'] > (float) $requestItem->requested_qty) {
                    throw ValidationException::withMessages([
                        'items' => __('Approved quantity cannot exceed the requested quantity.'),
                    ]);
                }

                $requestItem->update(['approved_qty' => $line['approved_qty']]);
            }

            $materialRequest->update([
                'status' => MaterialRequest::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            return $materialRequest->load(['warehouse', 'workOrder', 'items.item', 'approver']);
        });
    }

    public function reject(MaterialRequest $materialRequest, ?string $remarks = null): MaterialRequest
    {
        $this->ensureSubmitted($materialRequest);

        $materialRequest->update([
            'status' => MaterialRequest::STATUS_REJECTED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks' => $remarks ?? $materialRequest->remarks,
        ]);

        return $materialRequest->load(['warehouse', 'workOrder', 'items.item', 'approver']);
    }

    /**
     * Counts ordered quantities against the request's approved lines and
     * moves it to (partially) purchased.
     *
     * @param  array<int, array{item_id: int, quantity: float}>  $orderedLines
     */
    public function recordPurchase(MaterialRequest $materialRequest, array $orderedLines): void
    {
        $items = $materialRequest->items()->lockForUpdate()->get();

        foreach ($orderedLines as $line) {
            $remaining = (float) $line['quantity'];

            foreach ($items->where('item_id', $line['item_id']) as $requestItem) {
                if ($remaining <= 0) {
                    break;
                }

                $open = max(0, (float) $requestItem->approved_qty - (float) $requestItem->purchased_qty);
                $applied = min($open, $remaining);

                if ($applied > 0) {
                    $requestItem->purchased_qty = (float) $requestItem->purchased_qty + $applied;
                    $requestItem->save();
                    $remaining -= $applied;
                }
            }
        }

        $fullyPurchased = $items->every(fn ($item) => (float) $item->purchased_qty >= (float) $item->approved_qty);

        $materialRequest->update([
            'status' => $fullyPurchased ? MaterialRequest::STATUS_PURCHASED : MaterialRequest::STATUS_PARTIALLY_PURCHASED,
        ]);
    }

    private function ensureSubmitted(MaterialRequest $materialRequest): void
    {
        if ($materialRequest->status !== MaterialRequest::STATUS_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => __('Only a submitted material request can be approved or rejected.'),
            ]);
        }
    }
}
