-- ---------------------------------------------------------------------------
-- ERP database update: 2026-10-18
--
-- Same changes as these three Laravel migrations, for importing through
-- cPanel / phpMyAdmin instead of running `php artisan migrate`:
--   2026_10_18_160223_add_unit_and_overall_discount_vat_to_sales_and_purchases
--   2026_10_18_160224_add_purchased_by_and_expense_by_employee_columns
--   2026_10_18_161537_add_created_by_to_users_and_roles_tables
--
-- Import ONCE, after the earlier migrations are already on the server
-- (employee_info, users.image, purchases/income_expense_transactions.work_order_id).
-- The last part records the three migrations in the `migrations` table, so a
-- later `php artisan migrate` does not try to run them again.
--
-- Back up the database before importing.
-- ---------------------------------------------------------------------------

-- 1) Unit on sale/purchase lines; overall (invoice-level) discount and VAT
ALTER TABLE `sale_items`
    ADD COLUMN `unit` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `purchase_items`
    ADD COLUMN `unit` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `sales`
    ADD COLUMN `overall_discount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
    ADD COLUMN `overall_vat` DECIMAL(15,2) NOT NULL DEFAULT '0.00';

ALTER TABLE `purchases`
    ADD COLUMN `overall_discount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
    ADD COLUMN `overall_vat` DECIMAL(15,2) NOT NULL DEFAULT '0.00';

-- 2) Purchase By (employee) on purchases, Expense / Received By (employee) on income-expense entries
ALTER TABLE `purchases`
    ADD COLUMN `purchased_by_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    ADD CONSTRAINT `purchases_purchased_by_id_foreign`
        FOREIGN KEY (`purchased_by_id`) REFERENCES `employee_info` (`id`) ON DELETE SET NULL;

ALTER TABLE `income_expense_transactions`
    ADD COLUMN `employee_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    ADD CONSTRAINT `income_expense_transactions_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE SET NULL;

-- 3) Who created each user and role (the "Inserted By" column)
ALTER TABLE `users`
    ADD COLUMN `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    ADD CONSTRAINT `users_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `roles`
    ADD COLUMN `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    ADD CONSTRAINT `roles_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- 4) Mark the three migrations as run, in one new batch
SET @next_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`) VALUES
    ('2026_10_18_160223_add_unit_and_overall_discount_vat_to_sales_and_purchases', @next_batch),
    ('2026_10_18_160224_add_purchased_by_and_expense_by_employee_columns', @next_batch),
    ('2026_10_18_161537_add_created_by_to_users_and_roles_tables', @next_batch);
