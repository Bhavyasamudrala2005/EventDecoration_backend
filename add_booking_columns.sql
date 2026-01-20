-- Add missing columns to bookings table for complete booking details
-- Run this SQL in your MySQL/phpMyAdmin

ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS start_date DATE NULL AFTER booking_date,
ADD COLUMN IF NOT EXISTS end_date DATE NULL AFTER start_date,
ADD COLUMN IF NOT EXISTS time_slot VARCHAR(50) NULL AFTER end_date,
ADD COLUMN IF NOT EXISTS customer_name VARCHAR(100) NULL AFTER time_slot,
ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20) NULL AFTER customer_name,
ADD COLUMN IF NOT EXISTS delivery_address TEXT NULL AFTER customer_phone;

-- If the above doesn't work (older MySQL), use these individual statements:
-- ALTER TABLE bookings ADD COLUMN start_date DATE NULL;
-- ALTER TABLE bookings ADD COLUMN end_date DATE NULL;
-- ALTER TABLE bookings ADD COLUMN time_slot VARCHAR(50) NULL;
-- ALTER TABLE bookings ADD COLUMN customer_name VARCHAR(100) NULL;
-- ALTER TABLE bookings ADD COLUMN customer_phone VARCHAR(20) NULL;
-- ALTER TABLE bookings ADD COLUMN delivery_address TEXT NULL;
