<?php

use App\Models\AssetAssignment;
use App\Models\Employee;
use App\Models\InventoryAsset;
use App\Models\InventoryStock;
use App\Models\InventoryWarehouse;
use App\Models\ItemName;
use App\Models\MaterialRequestItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

/**
 * Creates an approved purchase order for one item.
 */
function approvedPurchaseOrder(User $actor, ItemName $item, float $quantity): PurchaseOrder
{
    $orderId = test()->actingAs($actor, 'sanctum')
        ->postJson('/api/asset/purchase-orders', [
            'supplier_id' => Supplier::factory()->create()->id,
            'purchase_date' => '2026-10-12',
            'items' => [['item_id' => $item->item_id, 'quantity' => $quantity, 'unit_price' => 1000]],
        ])
        ->assertCreated()
        ->json('data.id');

    test()->actingAs($actor, 'sanctum')
        ->postJson("/api/asset/purchase-orders/{$orderId}/approve")
        ->assertOk();

    return PurchaseOrder::with('items')->findOrFail($orderId);
}

/**
 * Receives the given quantity (or the given serial numbers) of an item into a store.
 *
 * @param  array<int, string>  $serials
 */
function receiveIntoStore(User $actor, InventoryWarehouse $store, ItemName $item, float $quantity, array $serials = []): PurchaseOrder
{
    $order = approvedPurchaseOrder($actor, $item, $quantity);

    test()->actingAs($actor, 'sanctum')
        ->postJson("/api/asset/purchase-orders/{$order->id}/receive", [
            'warehouse_id' => $store->id,
            'items' => [[
                'purchase_order_item_id' => $order->items->first()->id,
                'quantity' => $quantity,
                'serials' => array_map(fn (string $serial) => ['serial_number' => $serial], $serials),
            ]],
        ])
        ->assertCreated();

    return $order->fresh('items');
}

function stockOf(InventoryWarehouse $store, ItemName $item): float
{
    return (float) InventoryStock::query()
        ->where('warehouse_id', $store->id)
        ->where('item_id', $item->item_id)
        ->value('quantity');
}

describe('setup', function () {
    it('returns 403 for a user without the warehouses.view permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/asset/warehouses')
            ->assertForbidden();
    });

    it('creates a unit and a store', function () {
        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/units', ['name' => 'Pieces', 'short_name' => 'pcs'])
            ->assertCreated()
            ->assertJsonPath('data.short_name', 'pcs');

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/warehouses', [
                'warehouse_code' => 'CW-01',
                'name' => 'Center Warehouse',
                'warehouse_type' => InventoryWarehouse::TYPE_CENTER,
            ])
            ->assertCreated()
            ->assertJsonPath('data.warehouse_type_name', 'Center Warehouse');
    });

    it('always tracks asset items by serial', function () {
        $subCategory = ItemName::factory()->create()->subCategory;

        $this->actingAs(adminUser(), 'sanctum')
            ->postJson('/api/asset/item-names', [
                'icatg_id' => $subCategory->icatg_id,
                'iscatg_id' => $subCategory->iscatg_id,
                'itype_id' => ItemName::TYPE_ASSET,
                'item_name' => 'Laptop',
                'item_title' => 'Office Laptop',
                'item_code' => 'LP-01',
                'track_type' => ItemName::TRACK_QUANTITY,
            ])
            ->assertCreated()
            ->assertJsonPath('data.track_type', ItemName::TRACK_SERIAL);
    });
});

describe('lookups', function () {
    it('returns active items and stores for the dropdowns', function () {
        ItemName::factory()->create();
        ItemName::factory()->inactive()->create();
        InventoryWarehouse::factory()->create();
        InventoryWarehouse::factory()->inactive()->create();

        $this->actingAs(adminUser(), 'sanctum')
            ->getJson('/api/asset/lookups')
            ->assertOk()
            ->assertJsonCount(1, 'data.items')
            ->assertJsonCount(1, 'data.warehouses')
            ->assertJsonPath('data.items.0.track_type', ItemName::TRACK_SERIAL);
    });

    it('lists the units of an item held in a store', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->create();
        receiveIntoStore($actor, $store, $item, 2, ['SN-1', 'SN-2']);

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/asset/lookups/serials?warehouse_id={$store->id}&item_id={$item->item_id}")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('returns 403 for a user without any inventory permission', function () {
        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/asset/lookups')
            ->assertForbidden();
    });
});

describe('procurement', function () {
    it('orders against an approved material request and counts the purchased quantity', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->projectStore()->create();
        $item = ItemName::factory()->nonAsset()->create();

        $request = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/material-requests', [
                'warehouse_id' => $store->id,
                'priority' => 'high',
                'items' => [['item_id' => $item->item_id, 'requested_qty' => 10]],
            ])
            ->assertCreated()
            ->json('data');

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/material-requests/{$request['id']}/approve", [
                'items' => [['id' => $request['items'][0]['id'], 'approved_qty' => 8]],
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/purchase-orders', [
                'supplier_id' => Supplier::factory()->create()->id,
                'material_request_id' => $request['id'],
                'purchase_date' => '2026-10-12',
                'items' => [['item_id' => $item->item_id, 'quantity' => 5, 'unit_price' => 20, 'discount' => 10]],
            ])
            ->assertCreated()
            ->assertJsonPath('data.grand_total', 90);

        $this->assertDatabaseHas('material_requests', ['id' => $request['id'], 'status' => 'partially_purchased']);
        expect(MaterialRequestItem::find($request['items'][0]['id'])->purchased_qty)->toBe(5.0);
    });

    it('refuses to receive an order that is not approved', function () {
        $actor = adminUser();
        $item = ItemName::factory()->nonAsset()->create();
        $orderId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/purchase-orders', [
                'supplier_id' => Supplier::factory()->create()->id,
                'purchase_date' => '2026-10-12',
                'items' => [['item_id' => $item->item_id, 'quantity' => 5, 'unit_price' => 20]],
            ])
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/purchase-orders/{$orderId}/receive", [
                'warehouse_id' => InventoryWarehouse::factory()->create()->id,
                'items' => [['purchase_order_item_id' => PurchaseOrder::find($orderId)->items->first()->id, 'quantity' => 5]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status'], 'data');
    });

    it('receives quantity items into stock and partially receives the order', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->nonAsset()->create();
        $order = approvedPurchaseOrder($actor, $item, 10);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/purchase-orders/{$order->id}/receive", [
                'warehouse_id' => $store->id,
                'items' => [['purchase_order_item_id' => $order->items->first()->id, 'quantity' => 4]],
            ])
            ->assertCreated();

        expect(stockOf($store, $item))->toBe(4.0)
            ->and($order->fresh()->status)->toBe(PurchaseOrder::STATUS_PARTIALLY_RECEIVED);

        $this->assertDatabaseHas('inventory_transactions', [
            'item_id' => $item->item_id,
            'transaction_type' => 'purchase',
            'to_warehouse_id' => $store->id,
            'performed_by' => $actor->id,
        ]);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/purchase-orders/{$order->id}/receive", [
                'warehouse_id' => $store->id,
                'items' => [['purchase_order_item_id' => $order->items->first()->id, 'quantity' => 7]],
            ])
            ->assertUnprocessable();
    });

    it('registers an asset per serial when receiving an asset item', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->create();

        $order = receiveIntoStore($actor, $store, $item, 2, ['SN-1', 'SN-2']);

        expect($order->status)->toBe(PurchaseOrder::STATUS_RECEIVED)
            ->and(stockOf($store, $item))->toBe(2.0)
            ->and(InventoryAsset::whereSerial('current_warehouse_id', $store->id)->whereSerial('status', 'in_stock')->count())->toBe(2);
    });

    it('requires one serial number per unit of a serial-tracked item', function () {
        $actor = adminUser();
        $item = ItemName::factory()->create();
        $order = approvedPurchaseOrder($actor, $item, 2);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/purchase-orders/{$order->id}/receive", [
                'warehouse_id' => InventoryWarehouse::factory()->create()->id,
                'items' => [['purchase_order_item_id' => $order->items->first()->id, 'quantity' => 2, 'serials' => [['serial_number' => 'SN-1']]]],
            ])
            ->assertUnprocessable();

        expect(InventoryAsset::count())->toBe(0);
    });
});

describe('transfers', function () {
    it('moves quantity stock and serial units from one store to another', function () {
        $actor = adminUser();
        $center = InventoryWarehouse::factory()->create();
        $project = InventoryWarehouse::factory()->projectStore($center)->create();
        $cement = ItemName::factory()->nonAsset()->create();
        $laptop = ItemName::factory()->create();
        receiveIntoStore($actor, $center, $cement, 50);
        receiveIntoStore($actor, $center, $laptop, 2, ['SN-1', 'SN-2']);

        $transferId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/transfers', [
                'from_warehouse_id' => $center->id,
                'to_warehouse_id' => $project->id,
                'items' => [
                    ['item_id' => $cement->item_id, 'quantity' => 20],
                    ['item_id' => $laptop->item_id, 'quantity' => 1],
                ],
            ])
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/transfers/{$transferId}/dispatch")->assertUnprocessable();
        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/transfers/{$transferId}/approve")->assertOk();
        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/transfers/{$transferId}/dispatch")
            ->assertOk()
            ->assertJsonPath('data.status', 'in_transit');

        expect(stockOf($center, $cement))->toBe(30.0)
            ->and(stockOf($project, $cement))->toBe(0.0)
            ->and(InventoryAsset::whereSerial('status', 'in_transit')->count())->toBe(1);

        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/transfers/{$transferId}/receive")
            ->assertOk()
            ->assertJsonPath('data.status', 'received');

        expect(stockOf($project, $cement))->toBe(20.0)
            ->and(stockOf($project, $laptop))->toBe(1.0)
            ->and(stockOf($center, $laptop))->toBe(1.0)
            ->and(InventoryAsset::whereSerial('current_warehouse_id', $project->id)->whereSerial('status', 'in_stock')->count())->toBe(1);
    });

    it('rejects a transfer the source store cannot cover', function () {
        $actor = adminUser();
        $center = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->nonAsset()->create();
        receiveIntoStore($actor, $center, $item, 5);

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/transfers', [
                'from_warehouse_id' => $center->id,
                'to_warehouse_id' => InventoryWarehouse::factory()->create()->id,
                'items' => [['item_id' => $item->item_id, 'quantity' => 6]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.quantity'], 'data');
    });
});

describe('custody', function () {
    it('assigns an asset to an employee and takes it back', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->create();
        $employee = Employee::factory()->create();
        receiveIntoStore($actor, $store, $item, 1, ['SN-1']);
        $asset = InventoryAsset::firstOrFail();

        $assignmentId = $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/assets/{$asset->id}/assign", ['employee_id' => $employee->id])
            ->assertCreated()
            ->assertJsonPath('data.employee_name', $employee->name)
            ->json('data.id');

        expect($asset->fresh()->serial->status)->toBe(InventoryAsset::STATUS_ASSIGNED)
            ->and($asset->fresh()->serial->current_employee_id)->toBe($employee->id)
            ->and(stockOf($store, $item))->toBe(0.0);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/assets/{$asset->id}/assign", ['employee_id' => $employee->id])
            ->assertUnprocessable();

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/asset-assignments/{$assignmentId}/return", [
                'destination_warehouse_id' => $store->id,
                'condition' => 'beyond_repair',
            ])
            ->assertOk();

        expect($asset->fresh()->serial->status)->toBe(InventoryAsset::STATUS_DAMAGED)
            ->and($asset->fresh()->serial->current_employee_id)->toBeNull()
            ->and(AssetAssignment::find($assignmentId)->status)->toBe(AssetAssignment::STATUS_RETURNED)
            ->and(stockOf($store, $item))->toBe(1.0);
    });

    it('issues consumables to an employee and accepts partial returns', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->nonAsset()->create();
        $employee = Employee::factory()->create();
        receiveIntoStore($actor, $store, $item, 10);

        $issueId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/employee-inventory/issue', [
                'warehouse_id' => $store->id,
                'employee_id' => $employee->id,
                'item_id' => $item->item_id,
                'quantity' => 4,
            ])
            ->assertCreated()
            ->json('data.id');

        expect(stockOf($store, $item))->toBe(6.0);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/employee-inventory/{$issueId}/return", [
                'quantity' => 1,
                'destination_warehouse_id' => $store->id,
                'condition' => 'good',
            ])
            ->assertOk();

        $this->actingAs($actor, 'sanctum')
            ->getJson("/api/asset/employee-inventory/{$issueId}")
            ->assertOk()
            ->assertJsonPath('data.status', 'issued')
            ->assertJsonPath('data.outstanding_qty', 3);

        $this->actingAs($actor, 'sanctum')
            ->postJson("/api/asset/employee-inventory/{$issueId}/return", [
                'quantity' => 4,
                'destination_warehouse_id' => $store->id,
                'condition' => 'good',
            ])
            ->assertUnprocessable();

        expect(stockOf($store, $item))->toBe(7.0);
    });

    it('sends asset items through the asset register instead of employee issue', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->create();
        receiveIntoStore($actor, $store, $item, 1, ['SN-1']);

        $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/employee-inventory/issue', [
                'warehouse_id' => $store->id,
                'employee_id' => Employee::factory()->create()->id,
                'item_id' => $item->item_id,
                'quantity' => 1,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['item_id'], 'data');
    });
});

describe('disposal', function () {
    it('writes stock off only after approval', function () {
        $actor = adminUser();
        $store = InventoryWarehouse::factory()->create();
        $item = ItemName::factory()->create();
        receiveIntoStore($actor, $store, $item, 1, ['SN-1']);
        $asset = InventoryAsset::firstOrFail();

        $disposalId = $this->actingAs($actor, 'sanctum')
            ->postJson('/api/asset/disposals', [
                'warehouse_id' => $store->id,
                'reason' => 'Broken screen',
                'items' => [['item_id' => $item->item_id, 'quantity' => 1, 'serial_id' => $asset->serial_id]],
            ])
            ->assertCreated()
            ->json('data.id');

        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/disposals/{$disposalId}/dispose")->assertUnprocessable();
        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/disposals/{$disposalId}/approve")->assertOk();
        $this->actingAs($actor, 'sanctum')->postJson("/api/asset/disposals/{$disposalId}/dispose")
            ->assertOk()
            ->assertJsonPath('data.status', 'disposed');

        expect($asset->fresh()->serial->status)->toBe(InventoryAsset::STATUS_DISPOSED)
            ->and(stockOf($store, $item))->toBe(0.0);
    });
});
