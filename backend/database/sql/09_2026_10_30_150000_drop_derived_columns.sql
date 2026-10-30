-- Migration: 2026_10_30_150000_drop_derived_columns
-- Import in phpMyAdmin AFTER 08_...

-- Totals and states that are worked out from other columns or rows; the
-- application now computes them instead of storing a copy.

-- quantity - reserved_quantity
ALTER TABLE `inventory_stocks`
    DROP COLUMN `available_quantity`;

-- amount + bank_charge + vat
ALTER TABLE `fund_transfers`
    DROP COLUMN `total_amount`;

-- bill_amount + bank_charge
ALTER TABLE `supplier_payments`
    DROP COLUMN `total_amount`;

-- Sum of salary_payments.
ALTER TABLE `salary_history`
    DROP COLUMN `paid_amount`;

-- Sum of employee_advance_recoveries; Running until fully recovered.
ALTER TABLE `employee_advances`
    DROP INDEX `employee_advances_status_index`,
    DROP COLUMN `recovered_amount`,
    DROP COLUMN `status`;

-- Sum of employee_inventory_returns; issued until fully returned. The
-- composite index also served the employee_id foreign key, so employee_id
-- gets its own index first.
ALTER TABLE `employee_inventory`
    ADD INDEX `employee_inventory_employee_id_index` (`employee_id`);
ALTER TABLE `employee_inventory`
    DROP INDEX `employee_inventory_employee_id_status_index`,
    DROP COLUMN `returned_qty`,
    DROP COLUMN `status`;

-- Sum of purchase_receipt_items.
ALTER TABLE `purchase_order_items`
    DROP COLUMN `received_qty`;

-- What the request's purchase orders ordered, up to the approved quantity.
ALTER TABLE `material_request_items`
    DROP COLUMN `purchased_qty`;

-- opening_balance + the active ledger entries.
ALTER TABLE `customers`
    DROP COLUMN `current_balance`;
ALTER TABLE `suppliers`
    DROP COLUMN `current_balance`;

-- The line items plus the overall discount and VAT.
ALTER TABLE `sales`
    DROP COLUMN `total_amount`,
    DROP COLUMN `discount_amount`,
    DROP COLUMN `vat_amount`,
    DROP COLUMN `net_total`;
ALTER TABLE `purchases`
    DROP COLUMN `total_amount`,
    DROP COLUMN `discount_amount`,
    DROP COLUMN `vat_amount`,
    DROP COLUMN `net_total`;

-- The depth below the parent accounts.
ALTER TABLE `chart_of_accounts`
    DROP COLUMN `sibling_level`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_150000_drop_derived_columns', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
