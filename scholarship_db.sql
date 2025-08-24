-- Create database and use it
CREATE DATABASE IF NOT EXISTS scholarship_db;
USE scholarship_db;

-- =============================
-- Users Table with Bank Details
-- =============================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,

    -- Bank details
    bank_name VARCHAR(100) DEFAULT NULL,
    account_number VARCHAR(30) DEFAULT NULL,
    ifsc_code VARCHAR(20) DEFAULT NULL,
    account_holder_name VARCHAR(100) DEFAULT NULL,

    user_type ENUM('admin', 'user') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    login_attempts INT DEFAULT 0,
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =============================
-- Scholarships Table
-- =============================
CREATE TABLE scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) NULL,
    deadline DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- =============================
-- Applications Table
-- =============================
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    scholarship_id INT,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    dob DATE NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    application_text TEXT NULL,
    community_cert_path VARCHAR(255) NOT NULL,
    income_cert_path VARCHAR(255) NOT NULL,
    marksheet_10th_path VARCHAR(255) NOT NULL,
    marksheet_12th_path VARCHAR(255) NOT NULL,
    photo_path VARCHAR(255);
    recent_marksheet_path VARCHAR(255) NOT NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    feedback TEXT NULL,
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (scholarship_id) REFERENCES scholarships(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- =============================
-- Admin Logs Table
-- =============================
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- =============================
-- Insert Default Admin User
-- =============================
-- ⚠️ Replace '12345' with a securely hashed password in production
INSERT INTO users (username, password, email, user_type) 
VALUES ('admin', '12345', 'admin@yourdomain.com', 'admin');

ALTER TABLE scholarships
ADD COLUMN IF NOT EXISTS eligibility_criteria TEXT,
ADD COLUMN IF NOT EXISTS degree_level VARCHAR(50),
ADD COLUMN IF NOT EXISTS eligible_caste VARCHAR(50),
ADD COLUMN IF NOT EXISTS eligible_gender VARCHAR(50),
ADD COLUMN IF NOT EXISTS scheme_type VARCHAR(50);





//new 23-04-2025
-- First check which columns already exist
SHOW COLUMNS FROM scholarships LIKE 'eligible_castes';
SHOW COLUMNS FROM scholarships LIKE 'eligible_degrees';
SHOW COLUMNS FROM scholarships LIKE 'eligible_genders';
SHOW COLUMNS FROM scholarships LIKE 'amount';

-- Then only add the columns that don't exist
ALTER TABLE scholarships
ADD COLUMN IF NOT EXISTS eligible_castes VARCHAR(255) NOT NULL DEFAULT 'All' COMMENT 'Comma-separated list of eligible castes (General,OBC,SC,ST) or "All"',
ADD COLUMN IF NOT EXISTS eligible_degrees VARCHAR(255) NOT NULL DEFAULT 'All' COMMENT 'Comma-separated list of eligible degrees (High School,Undergraduate,Postgraduate,PhD) or "All"',
ADD COLUMN IF NOT EXISTS eligible_genders VARCHAR(255) NOT NULL DEFAULT 'All' COMMENT 'Comma-separated list of eligible genders (Male,Female,Other) or "All"',
ADD COLUMN IF NOT EXISTS amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Scholarship amount in INR';

--13-05-2025
CREATE TABLE application_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);


ALTER TABLE application_documents ADD COLUMN document_name VARCHAR(100);
ALTER TABLE scholarships ADD COLUMN caste VARCHAR(50);
ALTER TABLE scholarships ADD COLUMN gender VARCHAR(20);


ALTER TABLE applications 
ADD COLUMN bank_name VARCHAR(100),
ADD COLUMN account_holder_name VARCHAR(100),
ADD COLUMN account_number VARCHAR(50),
ADD COLUMN ifsc_code VARCHAR(20),
ADD COLUMN branch VARCHAR(100),
ADD COLUMN bank_passbook_path VARCHAR(255);


ALTER TABLE `user_bank_details` 
ADD COLUMN `branch_name` VARCHAR(100) AFTER `account_number`,
ADD COLUMN `account_type` VARCHAR(50) AFTER `ifsc_code`;

ALTER TABLE `users` 
ADD COLUMN `bank_account_name` VARCHAR(100) AFTER `email`,
ADD COLUMN `bank_name` VARCHAR(100) AFTER `bank_account_name`,
ADD COLUMN `bank_account_number` VARCHAR(50) AFTER `bank_name`,
ADD COLUMN `bank_ifsc_code` VARCHAR(20) AFTER `bank_account_number`,
ADD COLUMN `bank_branch` VARCHAR(100) AFTER `bank_ifsc_code`;


CREATE TABLE admin_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);