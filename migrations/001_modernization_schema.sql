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
-- Note: These will error if already exist, but migrate.sh handles errors gracefully
-- The migration script is designed to continue even if these fail

-- km_users indices
ALTER TABLE km_users ADD INDEX idx_playername (playername);
ALTER TABLE km_users ADD INDEX idx_email (email);
ALTER TABLE km_users ADD INDEX idx_validated (validated);
ALTER TABLE km_users ADD INDEX idx_dead (dead);
ALTER TABLE km_users ADD INDEX idx_lastaction (lastaction);
ALTER TABLE km_users ADD INDEX idx_land (land);
ALTER TABLE km_users ADD INDEX idx_honor (honor);
ALTER TABLE km_users ADD INDEX idx_skillpts (skillpts);

-- km_battlerecords indices
ALTER TABLE km_battlerecords ADD INDEX idx_attid (attid);
ALTER TABLE km_battlerecords ADD INDEX idx_victimid (victimid);

-- km_messages indices
ALTER TABLE km_messages ADD INDEX idx_forumparent (forumparent);
ALTER TABLE km_messages ADD INDEX idx_parentid (parentid);
ALTER TABLE km_messages ADD INDEX idx_posterid (posterid);

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
