-- Migration: 2026_10_30_120000_add_designation_id_to_salary_details_table
-- Import in phpMyAdmin AFTER 05_...

-- The designation given with a salary revision (promotion or demotion).
ALTER TABLE `salary_details`
    ADD `designation_id` BIGINT UNSIGNED NULL AFTER `change_reason`;

ALTER TABLE `salary_details`
    ADD CONSTRAINT `salary_details_designation_id_foreign`
    FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_120000_add_designation_id_to_salary_details_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
