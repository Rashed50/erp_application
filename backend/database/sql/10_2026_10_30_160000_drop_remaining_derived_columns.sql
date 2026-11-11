-- Migration: 2026_10_30_160000_drop_remaining_derived_columns
-- Import in phpMyAdmin AFTER 09_...

-- More totals the application now computes instead of storing.

-- The customer's "Payment Received" entries carrying the invoice number.
ALTER TABLE `sales`
    DROP COLUMN `paid_amount`;

-- The purchase's bill payments.
ALTER TABLE `purchases`
    DROP COLUMN `paid_amount`;

-- The order's lines.
ALTER TABLE `purchase_orders`
    DROP COLUMN `subtotal`,
    DROP COLUMN `discount`,
    DROP COLUMN `tax`,
    DROP COLUMN `grand_total`;

-- qty x unit price - discount + VAT (tax), per line.
ALTER TABLE `sale_items`
    DROP COLUMN `total_amount`;
ALTER TABLE `purchase_items`
    DROP COLUMN `total_amount`;
ALTER TABLE `purchase_order_items`
    DROP COLUMN `total`;

-- The entry's debit lines.
ALTER TABLE `account_transactions`
    DROP COLUMN `total_amount`;

-- The requested quantity once the transfer is dispatched / received.
ALTER TABLE `inventory_transfer_items`
    DROP COLUMN `sent_qty`,
    DROP COLUMN `received_qty`;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_30_160000_drop_remaining_derived_columns', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
