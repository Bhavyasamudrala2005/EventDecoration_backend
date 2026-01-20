-- ========================================================================
-- COMPLETE DATABASE SETUP - MATCHING ANDROID APP EXACTLY
-- Run ALL of this in phpMyAdmin to fix My Bookings
-- ========================================================================

-- Step 1: Create equipment table
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    type VARCHAR(50),
    description TEXT,
    price_per_day DECIMAL(10,2) DEFAULT 0.00,
    quantity INT DEFAULT 10,
    image_url VARCHAR(255),
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 2: Clear old equipment data and insert matching IDs
DELETE FROM equipment;

-- EXACT MAPPING FROM ANDROID APP:
-- equipmentId = 1  -> Large Event Speakers
-- equipmentId = 2  -> Bluetooth Speakers
-- equipmentId = 3  -> Stage Speakers
-- equipmentId = 4  -> Red Carpet
-- equipmentId = 5  -> Event Carpet
-- equipmentId = 6  -> Mandap Carpet
-- equipmentId = 7  -> Wedding Tent
-- equipmentId = 8  -> Canopy Tent
-- equipmentId = 9  -> Small Shade Tent
-- equipmentId = 10 -> Large Cooking Vessels
-- equipmentId = 11 -> Gas Stoves
-- equipmentId = 12 -> Serving Pots
-- equipmentId = 13 -> Plastic Chairs
-- equipmentId = 14 -> Wedding Chairs
-- equipmentId = 15 -> VIP Cushioned Chairs
-- equipmentId = 16 -> Banquet Tables
-- equipmentId = 17 -> Stage Platform
-- equipmentId = 18 -> Event Curtains
-- equipmentId = 19 -> LED Lighting System
-- equipmentId = 20 -> Flower Stands
-- equipmentId = 21 -> Backdrop Decor
-- equipmentId = 22 -> Floral Arches

INSERT INTO equipment (id, name, category, price_per_day, quantity) VALUES
(1, 'Large Event Speakers', 'Speakers', 4000.00, 10),
(2, 'Bluetooth Speakers', 'Speakers', 1500.00, 15),
(3, 'Stage Speakers', 'Speakers', 3000.00, 8),
(4, 'Red Carpet', 'Carpets', 800.00, 20),
(5, 'Event Carpet', 'Carpets', 600.00, 25),
(6, 'Mandap Carpet', 'Carpets', 1200.00, 10),
(7, 'Wedding Tent', 'Tents', 1500.00, 5),
(8, 'Canopy Tent', 'Tents', 2000.00, 8),
(9, 'Small Shade Tent', 'Tents', 1000.00, 12),
(10, 'Large Cooking Vessels', 'Cooking Vessels', 1500.00, 20),
(11, 'Gas Stoves', 'Cooking Vessels', 2000.00, 15),
(12, 'Serving Pots', 'Cooking Vessels', 3000.00, 25),
(13, 'Plastic Chairs', 'Chairs', 1500.00, 100),
(14, 'Wedding Chairs', 'Chairs', 2000.00, 50),
(15, 'VIP Cushioned Chairs', 'Chairs', 5000.00, 30),
(16, 'Banquet Tables', 'Hospitality Items', 1500.00, 30),
(17, 'Stage Platform', 'Hospitality Items', 2000.00, 5),
(18, 'Event Curtains', 'Hospitality Items', 1200.00, 20),
(19, 'LED Lighting System', 'Decoration Items', 4500.00, 10),
(20, 'Flower Stands', 'Decoration Items', 2000.00, 30),
(21, 'Backdrop Decor', 'Decoration Items', 3000.00, 8),
(22, 'Floral Arches', 'Decoration Items', 1500.00, 12);

-- Step 3: Create bookings table
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
    time_slot VARCHAR(50) DEFAULT 'Full Day'
);

-- Step 4: Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    fcm_token VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Step 5: Add fcm_token column if not exists
-- ALTER TABLE users ADD COLUMN IF NOT EXISTS fcm_token VARCHAR(500);

-- Step 6: Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    type VARCHAR(50) DEFAULT 'admin',
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================================================
-- VERIFY: Run these SELECTs to check data
-- ========================================================================
SELECT id, name, category, price_per_day FROM equipment ORDER BY id;
SELECT * FROM bookings ORDER BY id DESC LIMIT 10;
SELECT id, name, email FROM users;
