-- Migration: 2026_10_30_130000_create_salary_detail_logs_table
-- Import in phpMyAdmin AFTER 06_...

-- Audit trail of every salary revision added, edited or deleted.
CREATE TABLE `salary_detail_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `salary_detail_id` BIGINT UNSIGNED NULL,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `action` VARCHAR(20) NOT NULL,
    `changes` JSON NOT NULL,
    `created_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    INDEX `salary_detail_logs_employee_id_created_at_index` (`employee_id`, `created_at`),
    CONSTRAINT `salary_detail_logs_salary_detail_id_foreign`
        FOREIGN KEY (`salary_detail_id`) REFERENCES `salary_details` (`id`) ON DELETE SET NULL,
    CONSTRAINT `salary_detail_logs_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `salary_detail_logs_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_130000_create_salary_detail_logs_table', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
