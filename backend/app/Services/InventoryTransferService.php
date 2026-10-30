<?php

namespace App\Services;

use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\ItemName;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Store-to-store movement (center -> zonal -> project store, or back).
 * Stock leaves the source on dispatch and lands in the destination on
 * receipt; serial-tracked units are in transit in between.
 */
class InventoryTransferService
{
    public function __construct(private readonly InventoryStockService $stockService) {}

    /**
     * @param  array{status?: ?string, from_warehouse_id?: ?int, to_warehouse_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryTransfer::query()
            ->with(['fromWarehouse', 'toWarehouse', 'requester', 'approver', 'items.item'])
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['from_warehouse_id'] ?? null, fn ($query, int $id) => $query->where('from_warehouse_id', $id))
            ->when($filters['to_warehouse_id'] ?? null, fn ($query, int $id) => $query->where('to_warehouse_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{from_warehouse_id: int, to_warehouse_id: int, transfer_date?: ?string, remarks?: ?string, items: array<int, array{item_id: int, quantity: float}>}  $data
     */
    public function create(array $data): InventoryTransfer
    {
        return DB::transaction(function () use ($data) {
            foreach ($data['items'] as $index => $line) {
                if ($this->stockService->available($data['from_warehouse_id'], $line['item_id']) < (float) $line['quantity']) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => __('Insufficient stock in the source store.'),
                    ]);
                }
            }

            $transfer = InventoryTransfer::create([
                'transfer_no' => InventoryStockService::documentNumber('TR'),
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'transfer_date' => $data['transfer_date'] ?? now()->toDateString(),
                'status' => InventoryTransfer::STATUS_REQUESTED,
                'remarks' => $data['remarks'] ?? null,
                'requested_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $line) {
                $transfer->items()->create([
                    'item_id' => $line['item_id'],
                    'requested_qty' => $line['quantity'],
                ]);
            }

            return $this->loadDetails($transfer);
        });
    }

    public function approve(InventoryTransfer $transfer): InventoryTransfer
    {
        $this->ensureStatus($transfer, InventoryTransfer::STATUS_REQUESTED, __('Only a requested transfer can be approved.'));

        $transfer->update([
            'status' => InventoryTransfer::STATUS_APPROVED,
            'approved_by' => Auth::id(),
        ]);

        return $this->loadDetails($transfer);
    }

    public function dispatch(InventoryTransfer $transfer): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer) {
            $transfer = InventoryTransfer::query()->lockForUpdate()->findOrFail($transfer->id);
            $this->ensureStatus($transfer, InventoryTransfer::STATUS_APPROVED, __('Only an approved transfer can be dispatched.'));

            foreach ($transfer->items()->with('item')->get() as $line) {
                $quantity = (float) $line->requested_qty;

                if ($line->item->isSerialTracked()) {
                    $this->dispatchUnits($transfer, $line->item, $quantity);
                } else {
                    $this->stockService->decrease($transfer->from_warehouse_id, $line->item, $quantity, InventoryTransaction::TYPE_TRANSFER_OUT, $transfer, [
                        'to_warehouse_id' => $transfer->to_warehouse_id,
                        'remarks' => __('Transfer dispatched.'),
                    ]);
                }
            }

            $transfer->update([
                'status' => InventoryTransfer::STATUS_IN_TRANSIT,
                'dispatched_by' => Auth::id(),
            ]);

            return $this->loadDetails($transfer);
        });
    }

    public function receive(InventoryTransfer $transfer): InventoryTransfer
    {
        return DB::transaction(function () use ($transfer) {
            $transfer = InventoryTransfer::query()->lockForUpdate()->findOrFail($transfer->id);
            $this->ensureStatus($transfer, InventoryTransfer::STATUS_IN_TRANSIT, __('Only an in-transit transfer can be received.'));

            foreach ($transfer->items()->with('item')->get() as $line) {
                // Everything requested was sent on dispatch.
                $quantity = (float) $line->requested_qty;

                if ($line->item->isSerialTracked()) {
                    $this->receiveUnits($transfer, $line->item);
                } else {
                    $this->stockService->increase($transfer->to_warehouse_id, $line->item, $quantity, InventoryTransaction::TYPE_TRANSFER_IN, $transfer, [
                        'from_warehouse_id' => $transfer->from_warehouse_id,
                        'remarks' => __('Transfer received.'),
                    ]);
                }
            }

            $transfer->update([
                'status' => InventoryTransfer::STATUS_RECEIVED,
                'received_by' => Auth::id(),
            ]);

            return $this->loadDetails($transfer);
        });
    }

    /**
     * Takes the oldest in-stock units of the item out of the source store.
     */
    private function dispatchUnits(InventoryTransfer $transfer, ItemName $item, float $quantity): void
    {
        $serials = InventoryItemSerial::query()
            ->where('item_id', $item->item_id)
            ->where('current_warehouse_id', $transfer->from_warehouse_id)
            ->where('status', InventoryAsset::STATUS_IN_STOCK)
            ->orderBy('id')
            ->limit((int) $quantity)
            ->lockForUpdate()
            ->get();

        if ($serials->count() !== (int) $quantity) {
            throw ValidationException::withMessages([
                'items' => __('Not enough in-stock units of :item in the source store.', ['item' => $item->item_name]),
            ]);
        }

        foreach ($serials as $serial) {
            $this->stockService->decrease($transfer->from_warehouse_id, $item, 1, InventoryTransaction::TYPE_TRANSFER_OUT, $transfer, [
                'serial_id' => $serial->id,
                'to_warehouse_id' => $transfer->to_warehouse_id,
                'remarks' => __('Transfer dispatched.'),
            ]);

            $this->moveUnit($serial, null, InventoryAsset::STATUS_IN_TRANSIT);
        }
    }

    /**
     * Lands the exact units that were dispatched on this transfer.
     */
    private function receiveUnits(InventoryTransfer $transfer, ItemName $item): void
    {
        $serialIds = InventoryTransaction::query()
            ->whereMorphedTo('reference', $transfer)
            ->where('transaction_type', InventoryTransaction::TYPE_TRANSFER_OUT)
            ->where('item_id', $item->item_id)
            ->pluck('serial_id');

        $serials = InventoryItemSerial::query()->whereKey($serialIds)->lockForUpdate()->get();

        foreach ($serials as $serial) {
            $this->moveUnit($serial, $transfer->to_warehouse_id, InventoryAsset::STATUS_IN_STOCK);

            $this->stockService->increase($transfer->to_warehouse_id, $item, 1, InventoryTransaction::TYPE_TRANSFER_IN, $transfer, [
                'serial_id' => $serial->id,
                'from_warehouse_id' => $transfer->from_warehouse_id,
                'remarks' => __('Transfer received.'),
            ]);
        }
    }

    private function moveUnit(InventoryItemSerial $serial, ?int $warehouseId, string $status): void
    {
        $serial->update(['current_warehouse_id' => $warehouseId, 'status' => $status]);
    }

    private function ensureStatus(InventoryTransfer $transfer, string $status, string $message): void
    {
        if ($transfer->status !== $status) {
            throw ValidationException::withMessages(['status' => $message]);
        }
    }

    private function loadDetails(InventoryTransfer $transfer): InventoryTransfer
    {
        return $transfer->load(['fromWarehouse', 'toWarehouse', 'requester', 'approver', 'dispatcher', 'receiver', 'items.item']);
    }
}
