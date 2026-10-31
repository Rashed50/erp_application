-- Migration: 2026_10_30_110000_add_change_reason_to_salary_details_table
-- Import in phpMyAdmin AFTER 04_...

-- Why the salary changed (Promotion, Demotion, Annual Increment...).
ALTER TABLE `salary_details`
    ADD `change_reason` VARCHAR(30) NULL AFTER `deduction_basis`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_110000_add_change_reason_to_salary_details_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
