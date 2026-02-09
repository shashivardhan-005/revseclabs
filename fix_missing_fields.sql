-- Add missing fields to users table
ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL AFTER is_active,
ADD COLUMN reset_expires DATETIME DEFAULT NULL AFTER reset_token;

-- Add missing field to quiz_attempts table
ALTER TABLE quiz_attempts
ADD COLUMN violation_auto_submitted TINYINT(1) DEFAULT 0 AFTER tab_switch_violations;

-- 3. Add SSL Verification setting (Defaults to 0/Disabled for local compatibility)
INSERT INTO settings (setting_key, setting_value, setting_group) 
VALUES ('email_smtp_verify_ssl', '0', 'email')
ON DUPLICATE KEY UPDATE setting_value = '0';
