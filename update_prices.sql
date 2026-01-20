-- SQL Script to update ALL equipment prices
-- Run this in phpMyAdmin

-- =====================
-- SPEAKERS (IDs 1-3)
-- =====================
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Large Event Speakers';
UPDATE equipment SET price_per_day = 800.00 WHERE name = 'Bluetooth Speakers';
UPDATE equipment SET price_per_day = 2500.00 WHERE name = 'Stage Speakers';

-- =====================
-- CARPETS (IDs 4-6)
-- =====================
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Red Carpet';
UPDATE equipment SET price_per_day = 750.00 WHERE name = 'Event Carpet';
UPDATE equipment SET price_per_day = 1200.00 WHERE name = 'Mandap Carpet';

-- =====================
-- TENTS (IDs 7-9)
-- =====================
UPDATE equipment SET price_per_day = 5000.00 WHERE name = 'Wedding Tent';
UPDATE equipment SET price_per_day = 2000.00 WHERE name = 'Canopy Tent';
UPDATE equipment SET price_per_day = 800.00 WHERE name = 'Small Shade Tent';

-- =====================
-- COOKING VESSELS (IDs 10-12)
-- =====================
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Large Cooking Vessels';
UPDATE equipment SET price_per_day = 2000.00 WHERE name = 'Gas Stoves';
UPDATE equipment SET price_per_day = 3000.00 WHERE name = 'Serving Pots';

-- =====================
-- CHAIRS (IDs 13-15)
-- =====================
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Plastic Chairs';
UPDATE equipment SET price_per_day = 2000.00 WHERE name = 'Wedding Chairs';
UPDATE equipment SET price_per_day = 5000.00 WHERE name = 'VIP Cushioned Chairs';

-- =====================
-- HOSPITALITY ITEMS (IDs 16-18)
-- =====================
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Banquet Tables';
UPDATE equipment SET price_per_day = 2000.00 WHERE name = 'Stage Platform';
UPDATE equipment SET price_per_day = 1200.00 WHERE name = 'Event Curtains';

-- =====================
-- DECORATION ITEMS (IDs 19-22)
-- =====================
UPDATE equipment SET price_per_day = 4500.00 WHERE name = 'LED Lighting System';
UPDATE equipment SET price_per_day = 2000.00 WHERE name = 'Flower Stands';
UPDATE equipment SET price_per_day = 3000.00 WHERE name = 'Backdrop Decor';
UPDATE equipment SET price_per_day = 1500.00 WHERE name = 'Floral Arches';

-- Verify all prices updated
SELECT id, name, category, price_per_day FROM equipment ORDER BY category, name;
