<?php

namespace App\Services;

use App\Models\InventoryAsset;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use App\Models\ItemName;
use App\Models\MaterialRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceipt;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Inventory purchase orders and their goods receipts. Receiving puts the
 * stock into a store, registers serial numbers and, for asset items, creates
 * the asset register entries.
 */
class PurchaseOrderService
{
    public function __construct(
        private readonly InventoryStockService $stockService,
        private readonly MaterialRequestService $materialRequestService,
    ) {}

    /**
     * @param  array{status?: ?string, supplier_id?: ?int, material_request_id?: ?int}  $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return PurchaseOrder::query()
            ->with(['supplier', 'materialRequest', 'items' => fn ($query) => $query->withReceivedQuantity(), 'items.item', 'creator:id,name', 'approver:id,name'])
            ->withLineTotals()
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['supplier_id'] ?? null, fn ($query, int $id) => $query->where('supplier_id', $id))
            ->when($filters['material_request_id'] ?? null, fn ($query, int $id) => $query->where('material_request_id', $id))
            ->latest()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param  array{supplier_id: int, material_request_id?: ?int, purchase_date: string, expected_date?: ?string, remarks?: ?string, items: array<int, array{item_id: int, quantity: float, unit_price: float, discount?: ?float, tax?: ?float}>}  $data
     */
    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $materialRequest = null;

            if (! empty($data['material_request_id'])) {
                $materialRequest = MaterialRequest::query()->lockForUpdate()->findOrFail($data['material_request_id']);

                if (! $materialRequest->isPurchasable()) {
                    throw ValidationException::withMessages([
                        'material_request_id' => __('Only an approved material request can be ordered against.'),
                    ]);
                }
            }

            $order = PurchaseOrder::create([
                'po_no' => InventoryStockService::documentNumber('PO'),
                'supplier_id' => $data['supplier_id'],
                'material_request_id' => $materialRequest?->id,
                'purchase_date' => $data['purchase_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'status' => PurchaseOrder::STATUS_SUBMITTED,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // The order's subtotal, discount, tax and grand total come from these lines.
            $order->items()->createMany(collect($data['items'])->map(fn (array $line) => [
                'item_id' => $line['item_id'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'discount' => (float) ($line['discount'] ?? 0),
                'tax' => (float) ($line['tax'] ?? 0),
            ])->all());

            if ($materialRequest) {
                $this->materialRequestService->recordPurchase($materialRequest);
            }

            return $order->load(['supplier', 'materialRequest', 'items.item']);
        });
    }

    public function approve(PurchaseOrder $order): PurchaseOrder
    {
        if ($order->status !== PurchaseOrder::STATUS_SUBMITTED) {
            throw ValidationException::withMessages([
                'status' => __('Only a submitted purchase order can be approved.'),
            ]);
        }

        $order->update([
            'status' => PurchaseOrder::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return $order->load(['supplier', 'materialRequest', 'items.item', 'approver']);
    }

    /**
     * Receives goods against the order into a store (a GRN).
     *
     * @param  array{warehouse_id: int, received_date?: ?string, remarks?: ?string, items: array<int, array{purchase_order_item_id: int, quantity: float, unit_cost?: ?float, batch_no?: ?string, expiry_date?: ?string, serials?: array<int, array{serial_number: string, asset_tag?: ?string, barcode?: ?string, warranty_expiry?: ?string}>}>}  $data
     */
    public function receive(PurchaseOrder $order, array $data): PurchaseReceipt
    {
        return DB::transaction(function () use ($order, $data) {
            $order = PurchaseOrder::query()->lockForUpdate()->findOrFail($order->id);

            if (! $order->isReceivable()) {
                throw ValidationException::withMessages([
                    'status' => __('Only an approved purchase order can be received.'),
                ]);
            }

            $orderItems = $order->items()->with('item')->lockForUpdate()->get()->keyBy('id');

            $receipt = $order->receipts()->create([
                'receipt_no' => InventoryStockService::documentNumber('GRN'),
                'warehouse_id' => $data['warehouse_id'],
                'received_date' => $data['received_date'] ?? now()->toDateString(),
                'remarks' => $data['remarks'] ?? null,
                'received_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $index => $line) {
                $orderItem = $orderItems->get($line['purchase_order_item_id']);
                $quantity = (float) $line['quantity'];

                if (! $orderItem) {
                    throw ValidationException::withMessages([
                        "items.{$index}.purchase_order_item_id" => __('This line does not belong to the purchase order.'),
                    ]);
                }

                if ($quantity > $orderItem->remainingQuantity()) {
                    throw ValidationException::withMessages([
                        "items.{$index}.quantity" => __('Receiving :quantity exceeds the remaining :remaining of :item.', [
                            'quantity' => $quantity,
                            'remaining' => $orderItem->remainingQuantity(),
                            'item' => $orderItem->item->item_name,
                        ]),
                    ]);
                }

                $receiptItem = $receipt->items()->create([
                    'purchase_order_item_id' => $orderItem->id,
                    'item_id' => $orderItem->item_id,
                    'quantity' => $quantity,
                    'unit_cost' => $line['unit_cost'] ?? $orderItem->unit_price,
                    'batch_no' => $line['batch_no'] ?? null,
                    'expiry_date' => $line['expiry_date'] ?? null,
                ]);

                if (! $orderItem->item->isSerialTracked()) {
                    $this->stockService->increase($receipt->warehouse_id, $orderItem->item, $quantity, InventoryTransaction::TYPE_PURCHASE, $receipt, [
                        'remarks' => __('Purchase received.'),
                    ]);

                    continue;
                }

                $serials = $line['serials'] ?? [];

                if (floor($quantity) != $quantity || count($serials) !== (int) $quantity) {
                    throw ValidationException::withMessages([
                        "items.{$index}.serials" => __('Exactly one serial number is required per unit of :item.', ['item' => $orderItem->item->item_name]),
                    ]);
                }

                foreach ($serials as $serialData) {
                    $this->registerUnit($orderItem->item, $receipt, $receiptItem->id, (float) $receiptItem->unit_cost, $serialData);
                }
            }

            $this->refreshOrderStatus($order, $orderItems->values()->all());

            return $receipt->load(['warehouse', 'items.item', 'items.serials']);
        });
    }

    /**
     * @param  array{serial_number: string, asset_tag?: ?string, barcode?: ?string, warranty_expiry?: ?string}  $serialData
     */
    private function registerUnit(ItemName $item, PurchaseReceipt $receipt, int $receiptItemId, float $unitCost, array $serialData): void
    {
        $serial = InventoryItemSerial::create([
            'item_id' => $item->item_id,
            'serial_number' => $serialData['serial_number'],
            'barcode' => $serialData['barcode'] ?? null,
            'purchase_receipt_item_id' => $receiptItemId,
            'current_warehouse_id' => $receipt->warehouse_id,
            'status' => InventoryAsset::STATUS_IN_STOCK,
            'condition' => 'new',
            'purchase_date' => $receipt->received_date,
            'warranty_expiry' => $serialData['warranty_expiry'] ?? null,
        ]);

        if ($item->isAsset()) {
            InventoryAsset::create([
                'serial_id' => $serial->id,
                'asset_tag' => $serialData['asset_tag'] ?? $item->item_code.'-'.Str::upper(Str::random(8)),
                'purchase_cost' => $unitCost,
            ]);
        }

        $this->stockService->increase($receipt->warehouse_id, $item, 1, InventoryTransaction::TYPE_PURCHASE, $receipt, [
            'serial_id' => $serial->id,
            'remarks' => __('Purchase received.'),
        ]);
    }

    /**
     * @param  array<int, PurchaseOrderItem>  $orderItems
     */
    private function refreshOrderStatus(PurchaseOrder $order, array $orderItems): void
    {
        $fullyReceived = collect($orderItems)->every(fn ($item) => $item->remainingQuantity() <= 0);

        $order->update([
            'status' => $fullyReceived ? PurchaseOrder::STATUS_RECEIVED : PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
        ]);
    }
}
