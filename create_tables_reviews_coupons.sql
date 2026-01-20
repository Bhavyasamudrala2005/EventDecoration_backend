-- ============================================
-- Event Decoration Items - New Features Tables
-- Run this SQL in your phpMyAdmin or MySQL
-- ============================================

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL,
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_equipment (equipment_id),
    INDEX idx_user (user_id),
    INDEX idx_booking (booking_id)
);

-- Coupons Table
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    valid_from DATE NOT NULL,
    valid_until DATE NOT NULL,
    usage_limit INT DEFAULT NULL,
    times_used INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Coupon Usage Tracking
CREATE TABLE IF NOT EXISTS coupon_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT DEFAULT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_coupon (coupon_id),
    INDEX idx_user (user_id)
);

-- Insert sample coupons for testing
INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, max_discount, valid_from, valid_until, is_active) VALUES
('SAVE10', 'percentage', 10, 500, 500, '2026-01-01', '2026-12-31', 1),
('SAVE20', 'percentage', 20, 1000, 1000, '2026-01-01', '2026-12-31', 1),
('FLAT500', 'fixed', 500, 2000, NULL, '2026-01-01', '2026-12-31', 1),
('WELCOME', 'percentage', 15, 0, 300, '2026-01-01', '2026-12-31', 1);
