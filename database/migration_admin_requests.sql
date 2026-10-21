-- =====================================================================
--  MIGRATION: admin request/approval flow (replaces the invite key)
--  Run once in phpMyAdmin on an EXISTING database. Safe to re-run.
--  Fresh installs don't need this - schema.sql already includes it.
--
--  What it does:
--   1. Adds a `status` column to users (active | pending | rejected |
--      suspended) if it does not exist, defaulting to 'active' so no
--      existing data changes and all current admins/customers keep
--      working.
--   2. Extends the allowed values with 'rejected' (needed for the
--      admin request approval flow).
-- =====================================================================

-- 1. Add the status column if missing (keeps existing rows = 'active').
SET @col_exists := (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'status'
);
SET @sql := IF(@col_exists = 0,
  "ALTER TABLE `users` ADD COLUMN `status` ENUM('active','pending','rejected','suspended') NOT NULL DEFAULT 'active' AFTER `loyalty_points`",
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. If the column exists but cannot hold 'rejected', extend the ENUM.
--    (Modify is a no-op if the values are already correct.)
ALTER TABLE `users`
  MODIFY `status` ENUM('active','pending','rejected','suspended') NOT NULL DEFAULT 'active';

-- 3. Make sure every existing admin (and customer) is active.
UPDATE `users` SET `status` = 'active' WHERE `status` = '' OR `status` IS NULL;

-- 4. Remove the invite key setting, if it ever was stored in `settings`.
DELETE FROM `settings` WHERE `key_name` IN ('admin_registration_key', 'invite_key');

-- Done. Existing admins keep working; new admin registrations now land
-- as 'pending' and are approved under Admin -> Admin requests.
