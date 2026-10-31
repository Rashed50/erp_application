-- Migration: 2026_10_30_100000_drop_employee_snapshot_columns_from_salary_history_table
-- Import in phpMyAdmin AFTER 01_..., 02_... and 03_...

-- Employee code, name, department and designation are read from employee_info
-- through employee_id instead of being copied into every salary.
ALTER TABLE `salary_history`
    DROP INDEX `salary_history_department_index`,
    DROP COLUMN `employee_code`,
    DROP COLUMN `employee_name`,
    DROP COLUMN `department`,
    DROP COLUMN `designation`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_100000_drop_employee_snapshot_columns_from_salary_history_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
