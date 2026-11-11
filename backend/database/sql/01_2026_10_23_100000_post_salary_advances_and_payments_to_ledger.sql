-- Migration: 2026_10_23_100000_post_salary_advances_and_payments_to_ledger
-- Import in phpMyAdmin (Import tab) BEFORE 02_... and 03_...
-- Each file runs once; it also records itself in the `migrations` table so
-- Laravel knows it has already been applied.

-- Cash/bank account an advance was paid from.
ALTER TABLE `employee_advances`
    ADD `payment_account_id` BIGINT UNSIGNED NULL AFTER `employee_id`;
ALTER TABLE `employee_advances`
    ADD CONSTRAINT `employee_advances_payment_account_id_foreign`
    FOREIGN KEY (`payment_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT;

-- Cash/bank account a cash repayment was received into.
ALTER TABLE `employee_advance_recoveries`
    ADD `payment_account_id` BIGINT UNSIGNED NULL AFTER `salary_history_id`;
ALTER TABLE `employee_advance_recoveries`
    ADD CONSTRAINT `employee_advance_recoveries_payment_account_id_foreign`
    FOREIGN KEY (`payment_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT;

-- (Moved onto salary_payments by 03_...; added here to match the migration.)
ALTER TABLE `salary_history`
    ADD `payment_account_id` BIGINT UNSIGNED NULL AFTER `paid_at`,
    ADD `payment_date` DATE NULL AFTER `payment_account_id`;
ALTER TABLE `salary_history`
    ADD CONSTRAINT `salary_history_payment_account_id_foreign`
    FOREIGN KEY (`payment_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT;

-- Predefined account 1080 "Advance to Employees" under the Assets group (1000),
-- only when the Assets group exists and 1080 does not.
INSERT INTO `chart_of_accounts`
    (`account_type_id`, `parent_id`, `name`, `account_number`, `sibling_level`, `opening_date`,
     `is_transaction`, `is_predefined`, `created_at`, `updated_at`)
SELECT 1, `assets`.`id`, 'Advance to Employees', '1080', `assets`.`sibling_level` + 1, CURDATE(),
       1, 1, NOW(), NOW()
FROM `chart_of_accounts` AS `assets`
WHERE `assets`.`account_number` = '1000'
  AND NOT EXISTS (SELECT 1 FROM (SELECT `account_number` FROM `chart_of_accounts`) AS `existing` WHERE `existing`.`account_number` = '1080')
LIMIT 1;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_23_100000_post_salary_advances_and_payments_to_ledger', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
