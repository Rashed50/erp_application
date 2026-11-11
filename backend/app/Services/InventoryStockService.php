<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\ItemName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * The only place that changes inventory_stocks. Every change also writes the
 * matching inventory_transactions ledger row, so the snapshot and the ledger
 * never drift. Callers are expected to run inside a DB transaction.
 */
class InventoryStockService
{
    /**
     * @param  array{serial_id?: ?int, employee_id?: ?int, from_warehouse_id?: ?int, remarks?: ?string}  $context
     */
    public function increase(int $warehouseId, ItemName $item, float $quantity, string $type, Model $reference, array $context = []): InventoryStock
    {
        $stock = $this->lockedStock($warehouseId, $item->item_id)
            ?? new InventoryStock(['warehouse_id' => $warehouseId, 'item_id' => $item->item_id]);

        $stock->quantity = (float) $stock->quantity + $quantity;
        $stock->save();

        $this->record($item, $quantity, $type, $reference, [
            'from_warehouse_id' => $context['from_warehouse_id'] ?? null,
            'to_warehouse_id' => $warehouseId,
            ...$context,
        ]);

        return $stock;
    }

    /**
     * @param  array{serial_id?: ?int, employee_id?: ?int, to_warehouse_id?: ?int, remarks?: ?string}  $context
     *
     * @throws ValidationException when the store does not hold enough available stock
     */
    public function decrease(int $warehouseId, ItemName $item, float $quantity, string $type, Model $reference, array $context = []): InventoryStock
    {
        $stock = $this->lockedStock($warehouseId, $item->item_id);

        if (! $stock || $stock->availableQuantity() < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => __('Insufficient stock of :item in the selected store.', ['item' => $item->item_name]),
            ]);
        }

        $stock->quantity = (float) $stock->quantity - $quantity;
        $stock->save();

        $this->record($item, $quantity, $type, $reference, [
            'from_warehouse_id' => $warehouseId,
            'to_warehouse_id' => $context['to_warehouse_id'] ?? null,
            ...$context,
        ]);

        return $stock;
    }

    /**
     * @param  array{warehouse_id?: ?int, item_id?: ?int, low_stock?: bool}  $filters
     */
    public function paginateStocks(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryStock::query()
            ->with(['warehouse', 'item.unit'])
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->where('warehouse_id', $id))
            ->when($filters['item_id'] ?? null, fn ($query, int $id) => $query->where('item_id', $id))
            ->when($filters['low_stock'] ?? false, fn ($query) => $query->whereHas(
                'item',
                fn ($query) => $query->whereRaw('inventory_stocks.quantity - inventory_stocks.reserved_quantity <= item_names.minimum_stock'),
            ))
            ->orderBy('warehouse_id')
            ->orderBy('item_id')
            ->paginate($perPage);
    }

    /**
     * @param  array{item_id?: ?int, warehouse_id?: ?int, employee_id?: ?int, transaction_type?: ?string, date_from?: ?string, date_to?: ?string}  $filters
     */
    public function paginateTransactions(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return InventoryTransaction::query()
            ->with(['item', 'serial', 'fromWarehouse', 'toWarehouse', 'employee', 'performer'])
            ->when($filters['item_id'] ?? null, fn ($query, int $id) => $query->where('item_id', $id))
            ->when($filters['warehouse_id'] ?? null, fn ($query, int $id) => $query->where(
                fn ($query) => $query->where('from_warehouse_id', $id)->orWhere('to_warehouse_id', $id),
            ))
            ->when($filters['employee_id'] ?? null, fn ($query, int $id) => $query->where('employee_id', $id))
            ->when($filters['transaction_type'] ?? null, fn ($query, string $type) => $query->where('transaction_type', $type))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('transaction_date', '<=', $date))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Available (unreserved) quantity of an item in a store.
     */
    public function available(int $warehouseId, int $itemId): float
    {
        return (float) InventoryStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->value(DB::raw('quantity - reserved_quantity'));
    }

    /**
     * A human-facing document number such as PO-20261012-7KQ2ZD.
     */
    public static function documentNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    private function lockedStock(int $warehouseId, int $itemId): ?InventoryStock
    {
        return InventoryStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('item_id', $itemId)
            ->lockForUpdate()
            ->first();
    }

    /**
     * @param  array{serial_id?: ?int, employee_id?: ?int, from_warehouse_id?: ?int, to_warehouse_id?: ?int, remarks?: ?string}  $context
     */
    private function record(ItemName $item, float $quantity, string $type, Model $reference, array $context): void
    {
        $transaction = new InventoryTransaction([
            'transaction_no' => self::documentNumber('IT').'-'.Str::upper(Str::random(4)),
            'item_id' => $item->item_id,
            'serial_id' => $context['serial_id'] ?? null,
            'transaction_type' => $type,
            'quantity' => $quantity,
            'from_warehouse_id' => $context['from_warehouse_id'] ?? null,
            'to_warehouse_id' => $context['to_warehouse_id'] ?? null,
            'employee_id' => $context['employee_id'] ?? null,
            'transaction_date' => now(),
            'performed_by' => Auth::id(),
            'remarks' => $context['remarks'] ?? null,
        ]);

        $transaction->reference()->associate($reference);
        $transaction->save();
    }
}
