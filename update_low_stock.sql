-- Update Equipment Items for Low Stock Alerts
-- This script sets some items to "Limited" availability so they appear in Low Stock Alerts

-- Update Stage Speakers to Limited
UPDATE equipment SET availability = 'Limited', quantity = 3 WHERE name LIKE '%Stage Speaker%';

-- Update Small Shade Tent to Limited
UPDATE equipment SET availability = 'Limited', quantity = 2 WHERE name LIKE '%Small Shade Tent%';

-- Update VIP Cushioned Chairs to Limited
UPDATE equipment SET availability = 'Limited', quantity = 4 WHERE name LIKE '%VIP%';

-- Update Backdrop Decor to Limited
UPDATE equipment SET availability = 'Limited', quantity = 3 WHERE name LIKE '%Backdrop%';

-- Verify the updates
SELECT id, name, category, availability, quantity FROM equipment WHERE availability = 'Limited';

-- You can also run this to see all items:
-- SELECT id, name, category, availability, quantity FROM equipment;
