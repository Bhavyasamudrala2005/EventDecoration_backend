-- SQL script to add rating column and insert equipment for Compare Items feature
-- Run this in phpMyAdmin within the 'eventease' database

-- Step 1: Add rating column if not exists
ALTER TABLE `equipment` 
ADD COLUMN IF NOT EXISTS `rating` DECIMAL(2,1) DEFAULT 4.5,
ADD COLUMN IF NOT EXISTS `image_url` VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `description` TEXT DEFAULT NULL;

-- Step 2: Clear existing equipment (optional - remove this line if you want to keep existing data)
-- DELETE FROM equipment WHERE id > 0;

-- Step 3: Insert all 22 equipment items for comparison
-- Reset auto-increment (optional)
-- ALTER TABLE equipment AUTO_INCREMENT = 1;

-- Insert Speakers (Category: Speakers)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Large Event Speakers', 'Speakers', 'Professional Audio', 'High-power event speakers with subwoofer', 1500.00, 15, 'Available', 4.5, 'ic_large_event_speakers', 'Professional grade speakers for large events'),
('Bluetooth Speakers', 'Speakers', 'Portable Audio', 'Wireless Bluetooth speakers with 20hr battery', 1000.00, 25, 'Available', 4.2, 'ic_bluetooth_speaker', 'Portable Bluetooth speakers for small gatherings'),
('Stage Speakers', 'Speakers', 'Concert Grade', 'High-fidelity stage monitors and PA system', 2500.00, 8, 'Limited', 4.8, 'ic_stage_speakers', 'Concert quality stage speakers');

-- Insert Carpets (Category: Carpets)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Red Carpet', 'Carpets', 'Event Flooring', 'Premium red carpet 50ft x 4ft roll', 1000.00, 20, 'Available', 4.6, 'ic_red_carpet', 'Classic red carpet for VIP events'),
('Event Carpet', 'Carpets', 'Event Flooring', 'Multi-purpose event carpet various colors', 900.00, 30, 'Available', 4.3, 'ic_event_carpet', 'Versatile event carpet for any occasion'),
('Mandap Carpet', 'Carpets', 'Wedding Flooring', 'Decorative mandap carpet with traditional design', 1200.00, 12, 'Limited', 4.7, 'ic_mandap_carpet', 'Traditional wedding mandap carpet');

-- Insert Tents (Category: Tents)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Wedding Tent', 'Tents', 'Large Tent', '40x60 ft wedding shamiana with lining', 4500.00, 5, 'Limited', 4.9, 'ic_wedding_tent', 'Large decorated wedding tent'),
('Canopy Tent', 'Tents', 'Medium Tent', '20x20 ft canopy tent waterproof', 2000.00, 15, 'Available', 4.4, 'ic_canopy_tent', 'Waterproof canopy for outdoor events'),
('Small Shade Tent', 'Tents', 'Small Tent', '10x10 ft pop-up shade tent', 1550.00, 25, 'Available', 4.1, 'ic_small_shade_tent', 'Portable shade tent for small gatherings');

-- Insert Cooking Vessels (Category: Cooking Vessels)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Large Cooking Vessels', 'Cooking Vessels', 'Commercial Grade', 'Set of 100L stainless steel vessels', 6000.00, 10, 'Available', 4.7, 'ic_large_cooking_vessels', 'Commercial cooking vessels for large events'),
('Gas Stoves', 'Cooking Vessels', 'Cooking Equipment', 'Industrial 3-burner gas stoves', 1500.00, 20, 'Available', 4.5, 'ic_gas_stove', 'Heavy duty gas stoves for catering'),
('Serving Pots', 'Cooking Vessels', 'Serving Ware', 'Decorative stainless steel serving pots', 800.00, 40, 'Available', 4.0, 'ic_serving_pots', 'Elegant serving pots for buffet'),
('Boilers', 'Cooking Vessels', 'Cooking Equipment', 'Large water boilers 50L capacity', 570.00, 15, 'Available', 3.9, 'ic_large_cooking_vessels', 'Industrial water boilers');

-- Insert Chairs (Category: Chairs)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Plastic Chairs', 'Chairs', 'Standard Seating', 'Durable plastic chairs stackable', 1000.00, 200, 'Available', 4.2, 'ic_plastic_chair', 'Standard plastic chairs per 100 units'),
('Wedding Chairs', 'Chairs', 'Premium Seating', 'Decorated chairs with fabric cover', 750.00, 150, 'Available', 4.6, 'ic_wedding_chairs', 'Elegant wedding chairs with covers'),
('VIP Cushioned Chairs', 'Chairs', 'Luxury Seating', 'Premium cushioned VIP chairs', 800.00, 50, 'Limited', 4.8, 'ic_vip_chairs', 'Luxury VIP seating');

-- Insert Hospitality Items (Category: Hospitality Items)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('Banquet Tables', 'Hospitality Items', 'Furniture', '6ft rectangular banquet tables', 1120.00, 100, 'Available', 4.4, 'ic_banquet_tables', 'Sturdy banquet tables for events'),
('Stage Platform', 'Hospitality Items', 'Stage Equipment', '4x8 ft modular stage platform sections', 3300.00, 20, 'Limited', 4.7, 'ic_stage_platform', 'Modular stage platform'),
('Event Curtains', 'Hospitality Items', 'Decoration', 'Velvet event curtains various colors', 500.00, 50, 'Available', 4.1, 'ic_event_curtains', 'Decorative event curtains');

-- Insert Decoration Items (Category: Decoration Items)
INSERT INTO `equipment` (`name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `rating`, `image_url`, `description`) VALUES
('LED Lighting System', 'Decoration Items', 'Lighting', 'RGB LED party lights with controller', 2500.00, 30, 'Available', 4.9, 'ic_led_lighting', 'Dynamic LED lighting system'),
('Flower Stands', 'Decoration Items', 'Floral Decor', 'Decorative metal flower stands', 2500.00, 60, 'Available', 4.5, 'ic_flower_stands', 'Elegant flower display stands'),
('Backdrop Decor', 'Decoration Items', 'Stage Backdrop', 'Premium fabric backdrop with frame', 3000.00, 15, 'Limited', 4.8, 'ic_backdrop_decor', 'Beautiful event backdrop');

-- Verify the data
SELECT id, name, category, price_per_day, rating, availability FROM equipment ORDER BY category, name;
