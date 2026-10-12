# Inventory Service Layer

Place the PHP files under:

`app/Services/Inventory/`

## Services

### InventoryStockService
Low-level stock operations:
- increase()
- decrease()
- reserve()
- releaseReservation()

### PurchaseReceivingService
Receives purchased material into the Center Warehouse and:
- validates received quantities
- creates receipt items
- creates serial numbers
- creates asset records for asset items
- updates inventory stock
- writes inventory transactions
- updates purchase order status

### InventoryTransferService
Handles:
- create()
- dispatch()
- receive()

This supports:
- Center -> Zonal
- Zonal -> Project Store
- Project Store -> another permitted warehouse

### EmployeeInventoryService
Handles:
- issue()
- return()

Supports both quantity-tracked and serial-tracked items.

### AssetService
Handles:
- assign()
- return()

Maintains employee custody and serial/asset current location.

### InventoryDisposalService
Handles disposal of quantity-tracked and serial-tracked inventory.

## Recommended controller pattern

Controllers should validate HTTP input and call services.

Example:

```php
public function receive(StorePurchaseReceiptRequest $request, PurchaseReceipt $receipt)
{
    $result = $this->purchaseReceivingService->receive(
        $receipt,
        $request->validated('items'),
        auth()->user()->emp_auto_id
    );

    return redirect()->back()->with('success', 'Purchase received successfully.');
}
```

## Important architecture rule

Do not put stock-changing logic directly into controllers.

Use:

Controller
    -> Form Request validation
    -> Inventory Service
    -> DB::transaction()
    -> Stock + Serial/Custody + Ledger

This prevents partial inventory updates.

## Important production note

The current services use application-level transaction numbers such as `IT-YYYYMMDDHHMMSSMS-RRR`.
For very high concurrency, replace this with a dedicated sequence/number-generator service or UUID/ULID strategy.

## Polymorphic transaction references

`InventoryTransaction` uses:

- `reference_type`
- `reference_id`

The service records the business document that caused the movement, allowing audit reports to trace a transaction back to its receipt, transfer, employee return/issue, or disposal.
