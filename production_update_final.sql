-- Cumulative Update Script for RevSecLabs Production
-- Run this on your EC2 instance to bring the database schema up to date.

-- 1. Users Table Updates
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) DEFAULT NULL AFTER is_active;
ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires DATETIME DEFAULT NULL AFTER reset_token;

-- 2. Quizzes Table Updates
-- Ensure difficulty column is large enough for multiple values (e.g. "EASY,MEDIUM")
ALTER TABLE quizzes MODIFY difficulty VARCHAR(255) DEFAULT 'MEDIUM';

ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS results_released BOOLEAN DEFAULT FALSE;
ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS force_full_screen BOOLEAN DEFAULT TRUE;
ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS detect_tab_switch BOOLEAN DEFAULT TRUE;
ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS disable_copy_paste BOOLEAN DEFAULT TRUE;
ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS auto_submit_on_violation BOOLEAN DEFAULT FALSE;
ALTER TABLE quizzes ADD COLUMN IF NOT EXISTS violation_limit INT DEFAULT 3;

-- 3. Quiz Assignments Table Updates
-- Update status ENUM to include 'INCOMPLETE'
ALTER TABLE quiz_assignments MODIFY COLUMN status ENUM('ASSIGNED', 'STARTED', 'COMPLETED', 'INCOMPLETE') DEFAULT 'ASSIGNED';

ALTER TABLE quiz_assignments ADD COLUMN IF NOT EXISTS retest_requested BOOLEAN DEFAULT FALSE;

-- 4. Quiz Attempts Table Updates
ALTER TABLE quiz_attempts ADD COLUMN IF NOT EXISTS violation_auto_submitted BOOLEAN DEFAULT FALSE;

-- 5. Settings Table Updates
INSERT IGNORE INTO settings (setting_key, setting_value, setting_group) 
VALUES ('email_smtp_verify_ssl', '0', 'email');
