-- SQL Script to create equipment table and insert all 22 items
-- Run this in phpMyAdmin

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Create the equipment table if it doesn't exist
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  `specifications` text NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `availability` enum('Available','Limited','Unavailable') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Clear existing equipment data
DELETE FROM equipment;

-- Reset auto increment
ALTER TABLE equipment AUTO_INCREMENT = 1;

-- Insert Speakers (3 items) - IDs 1, 2, 3
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(1, 'Large Event Speakers', 'Speakers', 'Professional Audio', '2000W, Bluetooth enabled, Weather resistant', 1500.00, 10, 'Available'),
(2, 'Bluetooth Speakers', 'Speakers', 'Portable', '500W, Rechargeable battery, 10hr playback', 500.00, 15, 'Available'),
(3, 'Stage Speakers', 'Speakers', 'Concert Grade', '3000W, Line array system, Professional grade', 2500.00, 5, 'Limited');

-- Insert Carpets (3 items) - IDs 4, 5, 6
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(4, 'Red Carpet', 'Carpets', 'Event Carpet', '50ft x 4ft, Velvet finish, Premium quality', 1500.00, 8, 'Available'),
(5, 'Event Carpet', 'Carpets', 'Floor Covering', '100 sq ft, Durable material, Easy to clean', 750.00, 12, 'Available'),
(6, 'Mandap Carpet', 'Carpets', 'Wedding Decor', '200 sq ft, Traditional design, Luxury finish', 1200.00, 6, 'Available');

-- Insert Tents (3 items) - IDs 7, 8, 9
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(7, 'Wedding Tent', 'Tents', 'Large Event', '40x60ft, Waterproof, Seats 200 people', 1500.00, 4, 'Available'),
(8, 'Canopy Tent', 'Tents', 'Medium Event', '20x20ft, UV protection, Pop-up design', 2000.00, 10, 'Available'),
(9, 'Small Shade Tent', 'Tents', 'Small Event', '10x10ft, Portable, Easy setup', 800.00, 15, 'Limited');

-- Insert Cooking Vessels (3 items) - IDs 10, 11, 12
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(10, 'Large Cooking Vessels', 'Cooking Vessels', 'Commercial Grade', '50L capacity, Stainless steel, Heavy duty', 1500.00, 20, 'Available'),
(11, 'Gas Stoves', 'Cooking Vessels', 'Cooking Equipment', '4 burner, LPG compatible, Commercial use', 2000, 25, 'Available'),
(12, 'Serving Pots', 'Cooking Vessels', 'Serving Ware', 'Set of 10, Insulated, Buffet style', 3000.00, 30, 'Available');

-- Insert Chairs (3 items) - IDs 13, 14, 15
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(13, 'Plastic Chairs', 'Chairs', 'Standard Seating', 'Stackable, Lightweight, Set of 50', 1500.00, 500, 'Available'),
(14, 'Wedding Chairs', 'Chairs', 'Premium Seating', 'Chiavari style, Gold finish, Cushioned', 2000.00, 200, 'Available'),
(15, 'VIP Cushioned Chairs', 'Chairs', 'Luxury Seating', 'Leather finish, Extra padding, Premium quality', 5000.00, 50, 'Limited');

-- Insert Hospitality Items (3 items) - IDs 16, 17, 18
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(16, 'Banquet Tables', 'Hospitality Items', 'Tables', '6ft x 3ft, Folding, Set of 20', 1500.00, 100, 'Available'),
(17, 'Stage Platform', 'Hospitality Items', 'Stage Items', '20x15ft, Modular, Height adjustable', 2000.00, 10, 'Available'),
(18, 'Event Curtains', 'Hospitality Items', 'Draping', '30ft height, Velvet, Multiple colors', 1200.00, 20, 'Available');

-- Insert Decoration Items (4 items) - IDs 19, 20, 21, 22
INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES
(19, 'LED Lighting System', 'Decoration Items', 'Lighting', 'RGB, DMX control, 50 lights', 4500.00, 15, 'Available'),
(20, 'Flower Stands', 'Decoration Items', 'Floral Decor', 'Metal, Adjustable height, Set of 10', 2000.00, 40, 'Available'),
(21, 'Backdrop Decor', 'Decoration Items', 'Background', '15x10ft, Customizable, Premium fabric', 3000.00, 8, 'Limited'),
(22, 'Floral Arches', 'Decoration Items', 'Arches', '8ft tall, Metal frame, Floral arrangements', 1500.00, 10, 'Available');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify all 22 items were inserted
SELECT * FROM equipment ORDER BY id;
