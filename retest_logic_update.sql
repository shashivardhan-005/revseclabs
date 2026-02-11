-- Add columns for retest logic
ALTER TABLE quiz_assignments
ADD COLUMN retest_count INT DEFAULT 0,
ADD COLUMN retest_rejected BOOLEAN DEFAULT FALSE;
