-- Migration: 2026_10_23_120000_create_salary_payments_table
-- Import in phpMyAdmin AFTER 01_... and 02_...

-- One payment (full or part) of an approved salary.
CREATE TABLE `salary_payments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `salary_history_id` BIGINT UNSIGNED NOT NULL,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `payment_account_id` BIGINT UNSIGNED NOT NULL,
    `payment_date` DATE NOT NULL,
    `amount` DECIMAL(15, 2) NOT NULL,
    `remarks` VARCHAR(255) NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';

ALTER TABLE `salary_payments`
    ADD CONSTRAINT `salary_payments_salary_history_id_foreign`
        FOREIGN KEY (`salary_history_id`) REFERENCES `salary_history` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `salary_payments_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `salary_payments_payment_account_id_foreign`
        FOREIGN KEY (`payment_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT,
    ADD CONSTRAINT `salary_payments_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `salary_payments` ADD INDEX `salary_payments_payment_date_index` (`payment_date`);

-- Paid total of each salary, so its due (net salary - paid) is quick to list.
ALTER TABLE `salary_history`
    ADD `paid_amount` DECIMAL(15, 2) NOT NULL DEFAULT '0.00' AFTER `net_salary`;

-- Salaries already marked Paid were paid in full.
UPDATE `salary_history` SET `paid_amount` = `net_salary` WHERE `status` = 'Paid';

-- The payment account and date now live on each payment (added by 01_...).
ALTER TABLE `salary_history` DROP FOREIGN KEY `salary_history_payment_account_id_foreign`;
ALTER TABLE `salary_history` DROP COLUMN `payment_account_id`, DROP COLUMN `payment_date`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_23_120000_create_salary_payments_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
