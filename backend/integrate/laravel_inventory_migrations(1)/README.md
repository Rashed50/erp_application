# Laravel Inventory Management Migrations

These migrations implement the multi-level inventory flow:

Project Material Request
    -> Purchase Order
    -> Purchase Receipt into Center Warehouse
    -> Center Warehouse -> Zonal Warehouse
    -> Zonal Warehouse -> Project Store
    -> Project Store -> Employee
    -> Employee Return / Asset Return
    -> Repair / Re-stock / Disposal

## Existing application tables used

- employee_infos.emp_auto_id
- project_infos.proj_id

The migrations assume both referenced existing primary keys are `UNSIGNED BIGINT`.

## Migration order

1. inventory_categories
2. inventory_units
3. inventory_items
4. inventory_warehouses
5. suppliers
6. material_requests
7. material_request_items
8. purchase_orders
9. purchase_order_items
10. purchase_receipts
11. purchase_receipt_items
12. inventory_item_serials
13. inventory_stocks
14. inventory_transfers
15. inventory_transfer_items
16. inventory_transactions
17. employee_inventory
18. employee_inventory_returns
19. inventory_assets
20. asset_assignments
21. asset_returns
22. inventory_disposals
23. inventory_disposal_items

## Important integration note

Before running `php artisan migrate`, verify that:

- `employee_infos.emp_auto_id` is `BIGINT UNSIGNED`.
- `project_infos.proj_id` is `BIGINT UNSIGNED`.
- Both existing tables are created before these migrations run.

If either existing primary key is `INT UNSIGNED` rather than `BIGINT UNSIGNED`, change the corresponding `unsignedBigInteger(...)` columns and foreign-key definitions to `unsignedInteger(...)` / matching integer type.

## Inventory design principle

`inventory_transactions` is the audit ledger. Stock quantities should be changed through application service/transaction logic that records the corresponding ledger entry.

`inventory_stocks` is the current stock snapshot for fast reporting.

Serial-tracked assets use `inventory_item_serials` and `inventory_assets`, while quantity-tracked consumables use `inventory_stocks`.

## Suggested next implementation phase

After migrations, create:

- Eloquent models + relationships
- Form Requests
- Inventory service layer using DB transactions
- Purchase receiving workflow
- Center -> Zone transfer workflow
- Zone -> Project Store transfer workflow
- Project Store -> Employee issue workflow
- Employee return workflow
- Asset assignment/return workflow
- Disposal workflow
- Stock and movement reports
