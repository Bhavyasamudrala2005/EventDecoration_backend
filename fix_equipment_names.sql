-- ========================================================================
-- FIX EQUIPMENT TABLE AND VERIFY BOOKINGS
-- Run this in phpMyAdmin to ensure equipment names show in My Bookings
-- ========================================================================

-- First, check what equipment_ids are used in bookings
SELECT DISTINCT equipment_id FROM bookings ORDER BY equipment_id;

-- Check current equipment table
SELECT id, name, category, price_per_day FROM equipment ORDER BY id;

-- ========================================================================
-- INSERT/UPDATE ALL 22 EQUIPMENT ITEMS WITH CORRECT IDs
-- ========================================================================

-- SPEAKERS (IDs 1-3)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(1, 'Large Event Speakers', 'Speakers', 4000.00, 10, 'available'),
(2, 'Bluetooth Speakers', 'Speakers', 1500.00, 15, 'available'),
(3, 'Stage Speakers', 'Speakers', 3000.00, 8, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- CARPETS (IDs 4-6)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(4, 'Red Carpet', 'Carpets', 800.00, 20, 'available'),
(5, 'Event Carpet', 'Carpets', 600.00, 25, 'available'),
(6, 'Mandap Carpet', 'Carpets', 1200.00, 10, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- TENTS (IDs 7-9)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(7, 'Wedding Tent', 'Tents', 1500.00, 5, 'available'),
(8, 'Canopy Tent', 'Tents', 2000.00, 8, 'available'),
(9, 'Small Shade Tent', 'Tents', 1000.00, 12, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- COOKING VESSELS (IDs 10-12)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(10, 'Large Cooking Vessels', 'Cooking Vessels', 1500.00, 20, 'available'),
(11, 'Gas Stoves', 'Cooking Vessels', 2000.00, 15, 'available'),
(12, 'Serving Pots', 'Cooking Vessels', 3000.00, 25, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- CHAIRS (IDs 13-15)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(13, 'Plastic Chairs', 'Chairs', 1500.00, 100, 'available'),
(14, 'Wedding Chairs', 'Chairs', 2000.00, 50, 'available'),
(15, 'VIP Cushioned Chairs', 'Chairs', 5000.00, 30, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- HOSPITALITY ITEMS (IDs 16-18)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(16, 'Banquet Tables', 'Hospitality Items', 1500.00, 30, 'available'),
(17, 'Stage Platform', 'Hospitality Items', 2000.00, 5, 'available'),
(18, 'Event Curtains', 'Hospitality Items', 1200.00, 20, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- DECORATION ITEMS (IDs 19-22)
INSERT INTO equipment (id, name, category, price_per_day, quantity, status) VALUES
(19, 'LED Lighting System', 'Decoration Items', 4500.00, 10, 'available'),
(20, 'Flower Stands', 'Decoration Items', 2000.00, 30, 'available'),
(21, 'Backdrop Decor', 'Decoration Items', 3000.00, 8, 'available'),
(22, 'Floral Arches', 'Decoration Items', 1500.00, 12, 'available')
ON DUPLICATE KEY UPDATE name=VALUES(name), category=VALUES(category), price_per_day=VALUES(price_per_day);

-- ========================================================================
-- VERIFY: Check bookings with equipment names
-- This is what the My Bookings screen should show
-- ========================================================================
SELECT 
    b.id as booking_id,
    e.name as item_name,
    b.quantity,
    b.rental_days as days,
    b.total_amount,
    b.status,
    b.booking_date
FROM bookings b
LEFT JOIN equipment e ON b.equipment_id = e.id
ORDER BY b.id DESC;
