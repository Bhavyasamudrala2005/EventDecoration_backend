-- ========================================================================
-- REFERRAL PROGRAM - DATABASE TABLES
-- Run this SQL in phpMyAdmin to create referral tables
-- ========================================================================

-- 1. Add referral_code column to users table (if not exists)
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS referral_code VARCHAR(10) UNIQUE,
ADD COLUMN IF NOT EXISTS referred_by INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS credits DECIMAL(10,2) DEFAULT 0.00;

-- 2. Referral Codes Table - Store unique referral codes for each user
CREATE TABLE IF NOT EXISTS referral_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Referrals Table - Track who referred whom
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    referral_code VARCHAR(10) NOT NULL,
    status ENUM('pending', 'completed', 'credited') DEFAULT 'pending',
    reward_amount DECIMAL(10,2) DEFAULT 50.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    credited_at TIMESTAMP NULL,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Credit Transactions Table - Track all credit transactions
CREATE TABLE IF NOT EXISTS credit_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_type ENUM('referral_bonus', 'signup_bonus', 'booking_used', 'admin_credit', 'expired') NOT NULL,
    description VARCHAR(255),
    reference_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Referral Settings Table - Admin configurable settings
CREATE TABLE IF NOT EXISTS referral_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value VARCHAR(100) NOT NULL,
    description VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================================================
-- INSERT DEFAULT SETTINGS
-- ========================================================================
INSERT INTO referral_settings (setting_key, setting_value, description) VALUES
('referrer_reward', '50.00', 'Credits given to the person who refers'),
('referee_reward', '25.00', 'Credits given to the new user who signs up'),
('min_booking_for_credit', '1', 'Minimum bookings for referral credit'),
('max_referrals_per_user', '50', 'Maximum referrals a user can make'),
('credit_expiry_days', '365', 'Days until credits expire')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ========================================================================
-- GENERATE REFERRAL CODES FOR EXISTING USERS
-- ========================================================================
-- This will generate random 8-character codes for users without codes
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS GenerateReferralCodes()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_user_id INT;
    DECLARE v_code VARCHAR(10);
    DECLARE cur CURSOR FOR SELECT id FROM users WHERE referral_code IS NULL OR referral_code = '';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_user_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Generate random 8-character code
        SET v_code = UPPER(SUBSTRING(MD5(RAND()), 1, 8));
        
        -- Update user and insert into referral_codes
        UPDATE users SET referral_code = v_code WHERE id = v_user_id;
        INSERT IGNORE INTO referral_codes (user_id, code) VALUES (v_user_id, v_code);
    END LOOP;
    
    CLOSE cur;
END //
DELIMITER ;

-- Call the procedure
CALL GenerateReferralCodes();

-- ========================================================================
-- VERIFY TABLES CREATED
-- ========================================================================
SHOW TABLES LIKE 'referral%';
SHOW TABLES LIKE 'credit%';
SELECT * FROM referral_settings;
SELECT id, name, email, referral_code, credits FROM users LIMIT 5;
