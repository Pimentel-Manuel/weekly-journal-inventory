-- Migration Script for Weekly Journal Inventory
-- This script updates the database schema to support the new file upload functionality

-- Check if the table exists and has the old structure
-- Then alter it to add the new columns

-- Add week_number column if it doesn't exist
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'journal_entries' 
    AND COLUMN_NAME = 'week_number'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE journal_entries ADD COLUMN week_number INT NOT NULL DEFAULT 1 AFTER id',
    'SELECT "week_number column already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add month column if it doesn't exist
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'journal_entries' 
    AND COLUMN_NAME = 'month'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE journal_entries ADD COLUMN month INT NOT NULL DEFAULT 1 AFTER week_number',
    'SELECT "month column already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add image_url column if it doesn't exist
SET @column_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'journal_entries' 
    AND COLUMN_NAME = 'image_url'
);

SET @sql = IF(
    @column_exists = 0,
    'ALTER TABLE journal_entries ADD COLUMN image_url VARCHAR(500) NULL AFTER content',
    'SELECT "image_url column already exists" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show final table structure
DESCRIBE journal_entries;

SELECT 'Migration completed successfully!' AS message;
