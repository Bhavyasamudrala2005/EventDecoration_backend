-- SQL Script to insert sample bookings for testing
-- Run this in phpMyAdmin to test Recent Bookings feature
-- IMPORTANT: Change user_id to YOUR actual user ID from the users table!

-- First, let's see what users exist
-- SELECT id, name, email FROM users;

-- Check your bookings table structure
-- DESCRIBE bookings;

-- STEP 1: Make sure the bookings table exists with correct structure
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `equipment_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL DEFAULT 1,
    `rental_days` int(11) NOT NULL DEFAULT 1,
    `total_amount` decimal(10,2) NOT NULL,
    `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending',
    `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `time_slot` varchar(50) DEFAULT 'Full Day',
    `customer_name` varchar(100) DEFAULT NULL,
    `customer_phone` varchar(20) DEFAULT NULL,
    `delivery_address` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `equipment_id` (`equipment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- STEP 2: Insert sample bookings
-- ⚠️ CHANGE user_id=1 to YOUR actual user_id! 
-- Run this query first to get your user_id: SELECT id, name FROM users;

INSERT INTO bookings (user_id, equipment_id, quantity, rental_days, total_amount, status, start_date, end_date, time_slot, customer_name, customer_phone, delivery_address) VALUES
(1, 1, 2, 3, 9000.00, 'pending', '2026-01-20', '2026-01-23', 'Full Day', 'Bhavya', '9876543210', 'Chennai, Tamil Nadu'),
(1, 7, 1, 2, 3000.00, 'approved', '2026-01-25', '2026-01-27', 'Morning', 'Bhavya', '9876543210', 'Chennai, Tamil Nadu'),
(1, 8, 1, 1, 2000.00, 'pending', '2026-02-01', '2026-02-02', 'Full Day', 'Bhavya', '9876543210', 'Anna Nagar, Chennai'),
(1, 13, 50, 1, 75000.00, 'completed', '2026-01-10', '2026-01-11', 'Full Day', 'Bhavya', '9876543210', 'T Nagar, Chennai'),
(1, 19, 1, 2, 9000.00, 'approved', '2026-01-28', '2026-01-30', 'Evening', 'Bhavya', '9876543210', 'Velachery, Chennai');

-- STEP 3: Verify bookings were inserted
SELECT 
    b.id,
    b.user_id,
    e.name as equipment_name,
    b.quantity,
    b.rental_days,
    b.total_amount,
    b.status,
    b.start_date,
    b.end_date
FROM bookings b
LEFT JOIN equipment e ON b.equipment_id = e.id
ORDER BY b.id DESC;

-- STEP 4: Check if your user exists
SELECT id, name, email FROM users WHERE id = 1;

-- If user_id=1 doesn't exist, use this to find your actual user_id:
-- SELECT id, name, email FROM users;
-- Then UPDATE the bookings: UPDATE bookings SET user_id = YOUR_ID WHERE user_id = 1;
