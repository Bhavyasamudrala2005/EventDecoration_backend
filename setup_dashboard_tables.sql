-- =====================================================
-- COMPLETE DATABASE SETUP FOR ADMIN DASHBOARD
-- Run this in phpMyAdmin to ensure all tables exist
-- =====================================================

-- Use the eventease database
USE eventease;

-- 1. Ensure equipment table exists with correct structure
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  `specifications` text NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `availability` enum('Available','Limited','Unavailable') NOT NULL DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Ensure users table exists
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ensure bookings table exists with status column
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rental_days` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Ensure notifications table exists
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('booking','admin','new_equipment') NOT NULL DEFAULT 'booking',
  `message` varchar(255) NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Ensure admins table exists
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Insert default admin if not exists
INSERT IGNORE INTO `admins` (`id`, `username`, `password`, `name`) VALUES
(1, 'admin', 'admin123', 'Administrator');

-- 7. Insert sample equipment if table is empty
INSERT INTO equipment (name, category, type, specifications, price_per_day, quantity, availability) 
SELECT 'Large Event Speakers', 'Speakers', 'Professional Audio', '2000W, Bluetooth enabled', 150.00, 10, 'Available'
WHERE NOT EXISTS (SELECT 1 FROM equipment LIMIT 1);

-- 8. Insert a sample test user if users table is empty
INSERT INTO users (user_id, name, email, phone, password)
SELECT 'TEST001', 'Test User', 'test@example.com', '1234567890', 'password123'
WHERE NOT EXISTS (SELECT 1 FROM users LIMIT 1);

-- 9. Insert a sample pending booking for testing
INSERT INTO bookings (user_id, equipment_id, quantity, rental_days, total_amount, status)
SELECT 1, 1, 2, 3, 900.00, 'pending'
WHERE NOT EXISTS (SELECT 1 FROM bookings WHERE status = 'pending' LIMIT 1);

-- =====================================================
-- VERIFY DATA - Run this to check your setup
-- =====================================================
SELECT 'Equipment Count' as metric, COUNT(*) as value FROM equipment
UNION ALL
SELECT 'Total Bookings', COUNT(*) FROM bookings
UNION ALL
SELECT 'Pending Bookings', COUNT(*) FROM bookings WHERE status = 'pending'
UNION ALL
SELECT 'Low Stock Items (qty < 10)', COUNT(*) FROM equipment WHERE quantity < 10;
