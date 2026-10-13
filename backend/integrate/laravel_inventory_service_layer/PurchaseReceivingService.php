<?php

namespace App\Services\Inventory;

use App\Models\InventoryAsset;
use App\Models\InventoryItem;
use App\Models\InventoryItemSerial;
use App\Models\InventoryTransaction;
use App\Models\PurchaseReceipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseReceivingService
{
    public function receive(
        PurchaseReceipt $receipt,
        array $lines,
        int $receivedBy
    ): PurchaseReceipt {
        return DB::transaction(function () use ($receipt, $lines, $receivedBy) {
            $receipt->loadMissing('purchaseOrder', 'items');

            if ($receipt->status === 'received') {
                throw new RuntimeException('This purchase receipt has already been received.');
            }

            if ($receipt->status === 'cancelled') {
                throw new RuntimeException('A cancelled receipt cannot be received.');
            }

            if (empty($lines)) {
                throw ValidationException::withMessages([
                    'items' => 'At least one receipt item is required.',
                ]);
            }

            foreach ($lines as $line) {
                $item = InventoryItem::query()->findOrFail($line['item_id']);
                $quantity = (float) $line['quantity'];

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'quantity' => "Quantity for {$item->name} must be greater than zero.",
                    ]);
                }

                $orderItem = $receipt->purchaseOrder
                    ->items()
                    ->whereKey($line['purchase_order_item_id'])
                    ->where('item_id', $item->id)
                    ->first();

                if (!$orderItem) {
                    throw ValidationException::withMessages([
                        'item_id' => "Item {$item->name} does not belong to the purchase order.",
                    ]);
                }

                $alreadyReceived = (float) $orderItem->receiptItems()->sum('quantity');
                $remaining = (float) $orderItem->quantity - $alreadyReceived;

                if ($quantity > $remaining) {
                    throw ValidationException::withMessages([
                        'quantity' => "Receiving {$quantity} exceeds remaining quantity {$remaining} for {$item->name}.",
                    ]);
                }

                $receiptItem = $receipt->items()->create([
                    'purchase_order_item_id' => $orderItem->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'unit_cost' => $line['unit_cost'] ?? $orderItem->unit_price,
                    'batch_no' => $line['batch_no'] ?? null,
                    'expiry_date' => $line['expiry_date'] ?? null,
                ]);

                if ($item->track_type === 'serial') {
                    $serials = $line['serials'] ?? [];

                    if (count($serials) !== (int) $quantity) {
                        throw ValidationException::withMessages([
                            'serials' => "Exactly {$quantity} serial number(s) are required for {$item->name}.",
                        ]);
                    }

                    foreach ($serials as $serialData) {
                        $serialNumber = is_array($serialData)
                            ? $serialData['serial_number']
                            : $serialData;

                        $serial = InventoryItemSerial::create([
                            'item_id' => $item->id,
                            'serial_number' => $serialNumber,
                            'asset_tag' => is_array($serialData) ? ($serialData['asset_tag'] ?? null) : null,
                            'barcode' => is_array($serialData) ? ($serialData['barcode'] ?? null) : null,
                            'purchase_receipt_item_id' => $receiptItem->id,
                            'current_location_id' => $receipt->warehouse_id,
                            'status' => 'in_stock',
                            'condition' => 'new',
                            'purchase_date' => $receipt->received_date,
                            'warranty_expiry' => is_array($serialData) ? ($serialData['warranty_expiry'] ?? null) : null,
                        ]);

                        if ($item->item_type === 'asset') {
                            InventoryAsset::create([
                                'item_id' => $item->id,
                                'serial_id' => $serial->id,
                                'asset_tag' => $serial->asset_tag ?: 'AST-' . strtoupper(str()->random(10)),
                                'purchase_receipt_item_id' => $receiptItem->id,
                                'purchase_cost' => $receiptItem->unit_cost,
                                'purchase_date' => $receipt->received_date,
                                'warranty_expiry' => $serial->warranty_expiry,
                                'current_warehouse_id' => $receipt->warehouse_id,
                                'status' => 'in_stock',
                                'condition' => 'new',
                            ]);
                        }

                        InventoryTransaction::create([
                            'transaction_no' => $this->transactionNo(),
                            'item_id' => $item->id,
                            'serial_id' => $serial->id,
                            'transaction_type' => 'purchase',
                            'quantity' => 1,
                            'to_location_id' => $receipt->warehouse_id,
                            'reference_type' => $receipt->getMorphClass(),
                            'reference_id' => $receipt->id,
                            'transaction_date' => now(),
                            'performed_by' => $receivedBy,
                            'remarks' => 'Purchase receipt received.',
                        ]);
                    }
                } else {
                    InventoryTransaction::create([
                        'transaction_no' => $this->transactionNo(),
                        'item_id' => $item->id,
                        'transaction_type' => 'purchase',
                        'quantity' => $quantity,
                        'to_location_id' => $receipt->warehouse_id,
                        'reference_type' => $receipt->getMorphClass(),
                        'reference_id' => $receipt->id,
                        'transaction_date' => now(),
                        'performed_by' => $receivedBy,
                        'remarks' => 'Purchase receipt received.',
                    ]);
                }

                $stock = \App\Models\InventoryStock::query()
                    ->where('warehouse_id', $receipt->warehouse_id)
                    ->where('item_id', $item->id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock) {
                    $stock = new \App\Models\InventoryStock([
                        'warehouse_id' => $receipt->warehouse_id,
                        'item_id' => $item->id,
                        'quantity' => 0,
                        'reserved_quantity' => 0,
                        'available_quantity' => 0,
                    ]);
                }

                $stock->quantity += $quantity;
                $stock->available_quantity = $stock->quantity - $stock->reserved_quantity;
                $stock->save();
            }

            $receipt->update([
                'status' => 'received',
                'received_by' => $receivedBy,
            ]);

            $this->updatePurchaseOrderStatus($receipt);

            return $receipt->fresh(['purchaseOrder', 'items']);
        });
    }

    private function updatePurchaseOrderStatus(PurchaseReceipt $receipt): void
    {
        $order = $receipt->purchaseOrder()->with('items')->lockForUpdate()->first();

        $allReceived = true;
        $anyReceived = false;

        foreach ($order->items as $item) {
            $received = (float) $item->receiptItems()->sum('quantity');

            if ($received > 0) {
                $anyReceived = true;
            }

            if ($received < (float) $item->quantity) {
                $allReceived = false;
            }
        }

        $order->update([
            'status' => $allReceived ? 'received' : ($anyReceived ? 'partially_received' : $order->status),
        ]);
    }

    private function transactionNo(): string
    {
        return 'IT-' . now()->format('YmdHisv') . '-' . random_int(100, 999);
    }
}
