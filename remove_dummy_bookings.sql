-- ============================================
-- Remove Dummy Bookings from Database
-- Run this SQL in phpMyAdmin to clear dummy data
-- ============================================

-- View current bookings before deleting (optional - just to see what will be removed)
-- SELECT id, user_id, equipment_id, customer_name, status, booking_date FROM bookings;

-- Option 1: Delete all bookings with customer_name = 'John Doe' (dummy data)
DELETE FROM bookings WHERE customer_name = 'John Doe';

-- Option 2: Delete ALL bookings and start fresh (use with caution!)
-- TRUNCATE TABLE bookings;

-- Verify bookings after deletion
SELECT COUNT(*) as remaining_bookings FROM bookings;

-- If you also want to reset the auto-increment ID counter after truncating:
-- ALTER TABLE bookings AUTO_INCREMENT = 1;

-- ============================================
-- After running this, the Admin Dashboard will show:
-- - Total Bookings: 0 (or only your real bookings)
-- - Pending Approvals: 0 (or only real pending ones)
--
-- When users book items, they will appear with
-- their real names from their account profile.
-- ============================================
