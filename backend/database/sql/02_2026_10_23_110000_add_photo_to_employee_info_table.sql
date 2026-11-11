-- Migration: 2026_10_23_110000_add_photo_to_employee_info_table
-- Import in phpMyAdmin AFTER 01_... and BEFORE 03_...

-- Employee photo path on the public upload disk.
ALTER TABLE `employee_info`
    ADD `photo` VARCHAR(255) NULL AFTER `name`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_23_110000_add_photo_to_employee_info_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
