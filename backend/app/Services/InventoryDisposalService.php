<?php

namespace App\Services;

use App\Models\InventoryAsset;
use App\Models\InventoryDisposal;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use App\Models\ItemName;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Write-off of stock from a store: drafted with its lines, approved, then
 * disposed (which takes the stock out).
 */
class InventoryDisposalService
{
    public function __construct(private readonly InventoryStockService $stockService) {}

    /**
     * @param  array{status?: ?string, warehouse_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryDisposal::query()
            ->with(['warehouse', 'items.item', 'items.serial'])
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->where('warehouse_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{warehouse_id: int, disposal_date?: ?string, reason: string, remarks?: ?string, items: array<int, array{item_id: int, quantity: float, serial_id?: ?int, reason?: ?string}>}  $data
     */
    public function create(array $data): InventoryDisposal
    {
        return DB::transaction(function () use ($data) {
            $disposal = InventoryDisposal::create([
                'disposal_no' => InventoryStockService::documentNumber('DIS'),
                'warehouse_id' => $data['warehouse_id'],
                'disposal_date' => $data['disposal_date'] ?? now()->toDateString(),
                'reason' => $data['reason'],
                'status' => InventoryDisposal::STATUS_DRAFT,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $index => $line) {
                $item = ItemName::query()->findOrFail($line['item_id']);

                if ($item->isSerialTracked() && (empty($line['serial_id']) || (float) $line['quantity'] != 1)) {
                    throw ValidationException::withMessages([
                        "items.{$index}.serial_id" => __('A serial-tracked item is disposed one unit at a time with its serial number.'),
                    ]);
                }

                $disposal->items()->create([
                    'item_id' => $item->item_id,
                    'serial_id' => $item->isSerialTracked() ? $line['serial_id'] : null,
                    'quantity' => $line['quantity'],
                    'reason' => $line['reason'] ?? null,
                ]);
            }

            return $disposal->load(['warehouse', 'items.item', 'items.serial']);
        });
    }

    public function approve(InventoryDisposal $disposal): InventoryDisposal
    {
        if ($disposal->status !== InventoryDisposal::STATUS_DRAFT) {
            throw ValidationException::withMessages([
                'status' => __('Only a draft disposal can be approved.'),
            ]);
        }

        $disposal->update([
            'status' => InventoryDisposal::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return $disposal->load(['warehouse', 'items.item', 'items.serial', 'approver']);
    }

    public function dispose(InventoryDisposal $disposal): InventoryDisposal
    {
        return DB::transaction(function () use ($disposal) {
            $disposal = InventoryDisposal::query()->lockForUpdate()->findOrFail($disposal->id);

            if ($disposal->status !== InventoryDisposal::STATUS_APPROVED) {
                throw ValidationException::withMessages([
                    'status' => __('Only an approved disposal can be processed.'),
                ]);
            }

            foreach ($disposal->items()->with('item')->get() as $line) {
                if ($line->serial_id) {
                    $this->disposeUnit($disposal, $line->item, $line->serial_id);

                    continue;
                }

                $this->stockService->decrease($disposal->warehouse_id, $line->item, (float) $line->quantity, InventoryTransaction::TYPE_DISPOSAL, $disposal, [
                    'remarks' => $line->reason ?? $disposal->reason,
                ]);
            }

            $disposal->update([
                'status' => InventoryDisposal::STATUS_DISPOSED,
                'disposed_by' => Auth::id(),
                'disposed_at' => now(),
            ]);

            return $disposal->load(['warehouse', 'items.item', 'items.serial', 'approver', 'disposer']);
        });
    }

    private function disposeUnit(InventoryDisposal $disposal, ItemName $item, int $serialId): void
    {
        $serial = InventoryItemSerial::query()->lockForUpdate()->findOrFail($serialId);

        if ($serial->item_id !== $item->item_id || ! $serial->isAvailableIn($disposal->warehouse_id)) {
            throw ValidationException::withMessages([
                'items' => __('Serial :serial is not held in the disposal store.', ['serial' => $serial->serial_number]),
            ]);
        }

        $this->stockService->decrease($disposal->warehouse_id, $item, 1, InventoryTransaction::TYPE_DISPOSAL, $disposal, [
            'serial_id' => $serial->id,
            'remarks' => $disposal->reason,
        ]);

        $location = [
            'current_warehouse_id' => null,
            'current_employee_id' => null,
            'status' => InventoryAsset::STATUS_DISPOSED,
        ];

        $serial->update($location);
        InventoryAsset::query()->where('serial_id', $serial->id)->update($location);
    }
}
