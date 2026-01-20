-- ========================================================================
-- COMPLETE DATABASE SETUP FOR EVENTEASE BOOKING SYSTEM
-- Run this SQL script in phpMyAdmin to set up the booking system
-- ========================================================================
-- Database: eventease
-- This script will create/update all tables needed for the booking system
-- ========================================================================

-- First, create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS eventease;
USE eventease;

-- ========================================================================
-- STEP 1: CREATE USERS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255) DEFAULT NULL,
    fcm_token VARCHAR(500) DEFAULT NULL,
    otp VARCHAR(10) DEFAULT NULL,
    otp_expiry DATETIME DEFAULT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================================================
-- STEP 2: CREATE EQUIPMENT TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    type VARCHAR(50),
    description TEXT,
    price_per_day DECIMAL(10,2) DEFAULT 0.00,
    quantity INT DEFAULT 10,
    image_url VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 4.0,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================================================
-- STEP 3: INSERT ALL 22 EQUIPMENT ITEMS
-- Clear existing data first (optional - comment out if you want to keep existing data)
-- ========================================================================
DELETE FROM equipment WHERE id <= 22;

-- Speakers (IDs 1-3)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(1, 'Large Event Speakers', 'Speakers', 4000.00, 10, 'available'),
(2, 'Bluetooth Speakers', 'Speakers', 1500.00, 15, 'available'),
(3, 'Stage Speakers', 'Speakers', 3000.00, 8, 'available'),

-- Carpets (IDs 4-6)
(4, 'Red Carpet', 'Carpets', 800.00, 20, 'available'),
(5, 'Event Carpet', 'Carpets', 600.00, 25, 'available'),
(6, 'Mandap Carpet', 'Carpets', 1200.00, 10, 'available'),

-- Tents (IDs 7-9)
(7, 'Wedding Tent', 'Tents', 1500.00, 5, 'available'),
(8, 'Canopy Tent', 'Tents', 2000.00, 8, 'available'),
(9, 'Small Shade Tent', 'Tents', 1000.00, 12, 'available'),

-- Cooking Vessels (IDs 10-12)
(10, 'Large Cooking Vessels', 'Cooking Vessels', 1500.00, 20, 'available'),
(11, 'Gas Stoves', 'Cooking Vessels', 2000.00, 15, 'available'),
(12, 'Serving Pots', 'Cooking Vessels', 3000.00, 25, 'available'),

-- Chairs (IDs 13-15)
(13, 'Plastic Chairs', 'Chairs', 1500.00, 100, 'available'),
(14, 'Wedding Chairs', 'Chairs', 2000.00, 50, 'available'),
(15, 'VIP Cushioned Chairs', 'Chairs', 5000.00, 30, 'available'),

-- Hospitality Items (IDs 16-18)
(16, 'Banquet Tables', 'Hospitality Items', 1500.00, 30, 'available'),
(17, 'Stage Platform', 'Hospitality Items', 2000.00, 5, 'available'),
(18, 'Event Curtains', 'Hospitality Items', 1200.00, 20, 'available'),

-- Decoration Items (IDs 19-22)
(19, 'LED Lighting System', 'Decoration Items', 4500.00, 10, 'available'),
(20, 'Flower Stands', 'Decoration Items', 2000.00, 30, 'available'),
(21, 'Backdrop Decor', 'Decoration Items', 3000.00, 8, 'available'),
(22, 'Floral Arches', 'Decoration Items', 1500.00, 12, 'available');

-- ========================================================================
-- STEP 4: CREATE BOOKINGS TABLE
-- This is the main table that stores all orders/bookings
-- ========================================================================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    rental_days INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'approved', 'accepted', 'rejected', 'preparing', 'out_for_delivery', 'delivered', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NULL,
    end_date DATE NULL,
    time_slot VARCHAR(50) DEFAULT 'Full Day',
    customer_name VARCHAR(100) DEFAULT NULL,
    customer_phone VARCHAR(20) DEFAULT NULL,
    delivery_address TEXT DEFAULT NULL,
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending',
    razorpay_payment_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- Add customer columns if they don't exist (for existing tables)
-- MySQL will ignore these if columns already exist

-- ========================================================================
-- STEP 5: CREATE NOTIFICATIONS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    type VARCHAR(50) DEFAULT 'admin',
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================================================
-- STEP 6: CREATE INDEXES FOR FASTER QUERIES
-- ========================================================================
-- Note: These will fail silently if indexes already exist

-- ========================================================================
-- STEP 7: CREATE ADMIN TABLE (for admin login)
-- ========================================================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT IGNORE INTO admins (id, username, email, password, name) VALUES
(1, 'admin', 'admin@eventease.com', 'admin123', 'Administrator');

-- ========================================================================
-- STEP 8: CREATE SUPPORT REQUESTS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ========================================================================
-- STEP 9: INSERT SAMPLE USER FOR TESTING (if needed)
-- ========================================================================
INSERT IGNORE INTO users (id, name, email, password, phone) VALUES
(1, 'Test User', 'test@example.com', 'password123', '9876543210');

-- ========================================================================
-- VERIFICATION QUERIES
-- Run these to verify the setup is correct
-- ========================================================================

-- Check equipment count (should be 22)
SELECT 'Equipment Count' as 'Table', COUNT(*) as 'Count' FROM equipment;

-- Check bookings table structure
DESCRIBE bookings;

-- View recent bookings (if any)
SELECT b.id, u.name as user_name, e.name as equipment_name, b.quantity, b.total_amount, b.status, b.booking_date
FROM bookings b
LEFT JOIN users u ON b.user_id = u.id
LEFT JOIN equipment e ON b.equipment_id = e.id
ORDER BY b.booking_date DESC
LIMIT 10;

-- View dashboard stats (same query as admin_dashboard_stats.php)
SELECT 
    (SELECT COUNT(*) FROM equipment) as total_items,
    (SELECT COUNT(*) FROM bookings) as total_bookings,
    (SELECT COUNT(*) FROM bookings WHERE status = 'pending') as pending_approvals,
    (SELECT COUNT(*) FROM equipment WHERE quantity = 0) as low_stock_alerts;

-- ========================================================================
-- END OF SETUP SCRIPT
-- 
-- After running this script:
-- 1. Your database will have all required tables
-- 2. All 22 equipment items will be inserted
-- 3. Bookings table will be ready to receive orders
-- 4. Admin dashboard will show correct counts
-- ========================================================================
