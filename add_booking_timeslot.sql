-- SQL to add time_slot, start_date, and end_date columns to bookings table
-- Run this in phpMyAdmin or MySQL console

ALTER TABLE `bookings` 
ADD COLUMN `time_slot` VARCHAR(50) DEFAULT NULL AFTER `rental_days`,
ADD COLUMN `start_date` DATE DEFAULT NULL AFTER `time_slot`,
ADD COLUMN `end_date` DATE DEFAULT NULL AFTER `start_date`;

-- Update existing bookings with default values if needed
-- UPDATE bookings SET time_slot = 'Full Day', start_date = DATE(booking_date), end_date = DATE(booking_date + INTERVAL rental_days DAY) WHERE time_slot IS NULL;

