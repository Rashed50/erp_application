-- Migration: 2026_10_30_140000_drop_redundant_columns
-- Import in phpMyAdmin AFTER 07_...

-- Columns that only repeated what a related row already holds.

-- The salary's employee.
ALTER TABLE `salary_payments`
    DROP FOREIGN KEY `salary_payments_employee_id_foreign`;
ALTER TABLE `salary_payments`
    DROP COLUMN `employee_id`;

-- The advance's employee and the deducting salary's month.
ALTER TABLE `employee_advance_recoveries`
    DROP FOREIGN KEY `employee_advance_recoveries_employee_id_foreign`;
ALTER TABLE `employee_advance_recoveries`
    DROP INDEX `employee_advance_recoveries_salary_month_index`,
    DROP COLUMN `employee_id`,
    DROP COLUMN `salary_month`;

-- The issued row's employee, item and serial.
ALTER TABLE `employee_inventory_returns`
    DROP FOREIGN KEY `employee_inventory_returns_employee_id_foreign`,
    DROP FOREIGN KEY `employee_inventory_returns_item_id_foreign`,
    DROP FOREIGN KEY `employee_inventory_returns_serial_id_foreign`;
ALTER TABLE `employee_inventory_returns`
    DROP INDEX `employee_inventory_returns_employee_id_return_date_index`,
    DROP COLUMN `employee_id`,
    DROP COLUMN `item_id`,
    DROP COLUMN `serial_id`;

-- The assignment's asset and employee.
ALTER TABLE `asset_returns`
    DROP FOREIGN KEY `asset_returns_asset_id_foreign`,
    DROP FOREIGN KEY `asset_returns_employee_id_foreign`;
ALTER TABLE `asset_returns`
    DROP INDEX `asset_returns_asset_id_return_date_index`,
    DROP COLUMN `asset_id`,
    DROP COLUMN `employee_id`;

-- The serial's item, receipt, dates, store, custodian, status and condition.
ALTER TABLE `inventory_assets`
    DROP FOREIGN KEY `inventory_assets_item_id_foreign`,
    DROP FOREIGN KEY `inventory_assets_purchase_receipt_item_id_foreign`,
    DROP FOREIGN KEY `inventory_assets_current_warehouse_id_foreign`,
    DROP FOREIGN KEY `inventory_assets_current_employee_id_foreign`;
ALTER TABLE `inventory_assets`
    DROP INDEX `inventory_assets_current_warehouse_id_index`,
    DROP INDEX `inventory_assets_current_employee_id_index`,
    DROP INDEX `inventory_assets_status_condition_index`,
    DROP COLUMN `item_id`,
    DROP COLUMN `purchase_receipt_item_id`,
    DROP COLUMN `purchase_date`,
    DROP COLUMN `warranty_expiry`,
    DROP COLUMN `current_warehouse_id`,
    DROP COLUMN `current_employee_id`,
    DROP COLUMN `status`,
    DROP COLUMN `condition`;

-- The sub category's category.
ALTER TABLE `item_names`
    DROP COLUMN `icatg_id`;

-- Covered by the polymorphic source_type / source_id.
ALTER TABLE `account_transactions`
    DROP FOREIGN KEY `account_transactions_sale_id_foreign`,
    DROP FOREIGN KEY `account_transactions_purchase_id_foreign`;
ALTER TABLE `account_transactions`
    DROP COLUMN `sale_id`,
    DROP COLUMN `purchase_id`;

-- Only free-text lines keep a name; a product line is named by its product.
ALTER TABLE `sale_items`
    MODIFY `item_name` VARCHAR(255) NULL;
UPDATE `sale_items` SET `item_name` = NULL WHERE `product_id` IS NOT NULL;

ALTER TABLE `purchase_items`
    MODIFY `item_name` VARCHAR(255) NULL;
UPDATE `purchase_items` SET `item_name` = NULL WHERE `product_id` IS NOT NULL;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_140000_drop_redundant_columns', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
