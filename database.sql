CREATE DATABASE IF NOT EXISTS cybersecurity_db;
USE cybersecurity_db;

CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text TEXT NOT NULL,
    question_type ENUM('MCQ', 'TF', 'SCENARIO') DEFAULT 'MCQ',
    explanation TEXT,
    image_base64 LONGTEXT,
    topic_id INT,
    difficulty ENUM('EASY', 'MEDIUM', 'HARD') DEFAULT 'MEDIUM',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    text VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    is_password_changed BOOLEAN DEFAULT FALSE,
    first_name VARCHAR(150),
    last_name VARCHAR(150),
    is_staff BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    month DATE,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    duration_minutes INT DEFAULT 15,
    total_questions INT DEFAULT 10,
    pass_score INT DEFAULT 70,
    topic_id INT,
    difficulty ENUM('EASY', 'MEDIUM', 'HARD') DEFAULT 'MEDIUM',
    force_full_screen BOOLEAN DEFAULT TRUE,
    detect_tab_switch BOOLEAN DEFAULT TRUE,
    disable_copy_paste BOOLEAN DEFAULT TRUE,
    auto_submit_on_violation BOOLEAN DEFAULT FALSE,
    violation_limit INT DEFAULT 3,
    results_released BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE SET NULL
);

-- Default Admin User (Password: admin123)
-- In production, please change this immediately.
INSERT INTO users (email, password, first_name, last_name, is_staff, is_active, is_password_changed) 
VALUES ('admin@revseclabs.in', '$2y$10$CsBG6lCipRHj8ch269R0i.Tpw9GpQOO.13FIGj2wRxYXu9Oqn1NXm', 'Admin', 'User', 1, 1, 1);
-- Note: The above hash is for 'admin123'. It's better to use the Seeder for real deployment.

CREATE TABLE IF NOT EXISTS quiz_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    status ENUM('ASSIGNED', 'STARTED', 'COMPLETED') DEFAULT 'ASSIGNED',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME,
    score FLOAT,
    result_email_sent BOOLEAN DEFAULT FALSE,
    certificate_sent BOOLEAN DEFAULT FALSE,
    retest_requested BOOLEAN DEFAULT FALSE,
    UNIQUE KEY user_quiz (user_id, quiz_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id VARCHAR(36) PRIMARY KEY, -- UUID
    assignment_id INT UNIQUE NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME,
    score FLOAT DEFAULT 0.0,
    full_screen_violations INT DEFAULT 0,
    tab_switch_violations INT DEFAULT 0,
    FOREIGN KEY (assignment_id) REFERENCES quiz_assignments(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id VARCHAR(36) NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES options(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS profile_change_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    new_full_name VARCHAR(150),
    new_department VARCHAR(100),
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT FALSE,
    is_rejected BOOLEAN DEFAULT FALSE,
    admin_comment TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    details TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES 
('site_name', 'RevSecLabs', 'branding'),
('contact_email', 'revseclabs@gmail.com', 'branding'),
('email_sender_name', 'RevSecLabs Admin', 'email'),
('email_sender_email', 'revseclabs@gmail.com', 'email'),
('default_passing_score', '70', 'quiz'),
('default_violation_limit', '3', 'quiz'),
('time_format', '24h', 'general');
