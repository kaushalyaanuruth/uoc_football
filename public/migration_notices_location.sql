-- Add location support to notices (create flow)
-- Run this once if your existing notices table was created before the location column existed.
ALTER TABLE notices
ADD COLUMN location VARCHAR(255) NULL AFTER content;
