-- ========================================================================
-- FIX MY BOOKINGS - COMPLETE DATABASE SETUP
-- Run this ENTIRE script in phpMyAdmin to fix the My Bookings issue
-- ========================================================================

-- Step 1: Make sure bookings table exists with all columns
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    rental_days INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) DEFAULT 0.00,
    status VARCHAR(50) DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NULL,
    end_date DATE NULL,
    time_slot VARCHAR(50) DEFAULT 'Full Day',
    customer_name VARCHAR(100) DEFAULT NULL,
    customer_phone VARCHAR(20) DEFAULT NULL,
    delivery_address TEXT DEFAULT NULL
);

-- Step 2: Add missing columns if table already exists
-- Try these one by one if ALTER fails

-- ALTER TABLE bookings ADD COLUMN customer_name VARCHAR(100) DEFAULT NULL;
-- ALTER TABLE bookings ADD COLUMN customer_phone VARCHAR(20) DEFAULT NULL;
-- ALTER TABLE bookings ADD COLUMN delivery_address TEXT DEFAULT NULL;
-- ALTER TABLE bookings ADD COLUMN time_slot VARCHAR(50) DEFAULT 'Full Day';
-- ALTER TABLE bookings ADD COLUMN start_date DATE NULL;
-- ALTER TABLE bookings ADD COLUMN end_date DATE NULL;

-- Step 3: Verify the structure
DESCRIBE bookings;

-- Step 4: Insert a test booking (optional - for testing)
-- INSERT INTO bookings (user_id, equipment_id, quantity, rental_days, total_amount, status, customer_name, customer_phone, delivery_address)
-- VALUES (1, 1, 2, 3, 12000.00, 'pending', 'Test User', '9876543210', '123 Test Street, Chennai - 600001');

-- Step 5: View all bookings
SELECT * FROM bookings;
