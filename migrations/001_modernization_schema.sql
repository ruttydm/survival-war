-- ============================================================================
-- Survival War - Modernization Schema Update
-- Migration: 001
-- Date: 2025-11-24
-- Description: Updates database schema for PHP 8.5 modernization
-- ============================================================================

-- Update password field length for modern password hashes (Argon2ID)
-- MD5 hashes are 32 characters, but modern hashes are up to 255 characters
ALTER TABLE km_users MODIFY password VARCHAR(255) NOT NULL;
ALTER TABLE km_admins MODIFY password VARCHAR(255) NOT NULL;

-- Add indices for improved query performance
-- These fields are frequently used in WHERE clauses
CREATE INDEX IF NOT EXISTS idx_playername ON km_users(playername);
CREATE INDEX IF NOT EXISTS idx_email ON km_users(email);
CREATE INDEX IF NOT EXISTS idx_validated ON km_users(validated);
CREATE INDEX IF NOT EXISTS idx_dead ON km_users(dead);
CREATE INDEX IF NOT EXISTS idx_lastaction ON km_users(lastaction);
CREATE INDEX IF NOT EXISTS idx_land ON km_users(land);
CREATE INDEX IF NOT EXISTS idx_honor ON km_users(honor);
CREATE INDEX IF NOT EXISTS idx_skillpts ON km_users(skillpts);

-- Add indices for battle records (logs page)
CREATE INDEX IF NOT EXISTS idx_attid ON km_battlerecords(attid);
CREATE INDEX IF NOT EXISTS idx_victimid ON km_battlerecords(victimid);

-- Add indices for forum system
CREATE INDEX IF NOT EXISTS idx_forumparent ON km_messages(forumparent);
CREATE INDEX IF NOT EXISTS idx_parentid ON km_messages(parentid);
CREATE INDEX IF NOT EXISTS idx_posterid ON km_messages(posterid);

-- Optional: Add new security fields for future enhancements
-- Uncomment these if you want additional security features:

-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS password_reset_token VARCHAR(64) NULL DEFAULT NULL;
-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS password_reset_expires INT(11) NULL DEFAULT NULL;
-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS failed_login_attempts INT(11) NOT NULL DEFAULT 0;
-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS locked_until INT(11) NULL DEFAULT NULL;
-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS last_login_ip VARCHAR(45) NULL DEFAULT NULL;
-- ALTER TABLE km_users ADD COLUMN IF NOT EXISTS last_login_time INT(11) NULL DEFAULT NULL;

-- Optional: Add audit trail for admin actions
-- Uncomment if you want admin action logging:

-- CREATE TABLE IF NOT EXISTS km_admin_log (
--     id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--     admin_name VARCHAR(50) NOT NULL,
--     action VARCHAR(100) NOT NULL,
--     target_type VARCHAR(50) NULL,
--     target_id INT(11) NULL,
--     details TEXT NULL,
--     ip_address VARCHAR(45) NULL,
--     timestamp INT(11) NOT NULL,
--     INDEX idx_admin_name (admin_name),
--     INDEX idx_timestamp (timestamp),
--     INDEX idx_action (action)
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- Migration Complete
-- ============================================================================

-- IMPORTANT: After running this migration, test the following:
-- 1. User login with existing MD5 passwords (should auto-migrate to Argon2ID)
-- 2. New user registration (should use Argon2ID from the start)
-- 3. Admin login (should auto-migrate passwords)
-- 4. Password reset/change functionality
-- 5. All ranking pages (should be faster with new indices)
-- 6. Forum functionality (should be faster with new indices)
