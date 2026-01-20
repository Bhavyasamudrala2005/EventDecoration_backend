-- ========================================================================
-- ADD CUSTOMER COLUMNS TO BOOKINGS TABLE
-- Run this if your bookings table is missing customer columns
-- ========================================================================

-- Add customer_name column if it doesn't exist
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS customer_name VARCHAR(100) DEFAULT NULL;

-- Add customer_phone column if it doesn't exist
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) DEFAULT NULL;

-- Add delivery_address column if it doesn't exist
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS delivery_address TEXT DEFAULT NULL;

-- Add payment columns if they don't exist
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending';
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS razorpay_payment_id VARCHAR(100) DEFAULT NULL;

-- Create indexes for faster queries
CREATE INDEX IF NOT EXISTS idx_bookings_user_id ON bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);

-- Verify the table structure
DESCRIBE bookings;

-- Check if there are any bookings
SELECT COUNT(*) as total_bookings FROM bookings;
SELECT * FROM bookings ORDER BY id DESC LIMIT 5;
