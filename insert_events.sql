-- SQL Script to create events table and insert sample event data
-- Run this in phpMyAdmin

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Create the events table if it doesn't exist
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(300) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Clear existing events data
DELETE FROM events;

-- Reset auto increment
ALTER TABLE events AUTO_INCREMENT = 1;

-- Insert Sample Events
INSERT INTO events (id, name, description, event_date, event_time, location) VALUES
(1, 'Grand Wedding Ceremony', 'Traditional Indian wedding with all decorations and catering services', '2026-02-15', '10:00:00', 'Taj Convention Center, Chennai'),
(2, 'Corporate Annual Meet', 'Company annual meeting with stage setup and audio equipment', '2026-02-20', '09:00:00', 'ITC Grand Chola, Chennai'),
(3, 'Birthday Celebration', 'Kids birthday party with balloon decorations and entertainment', '2026-01-25', '16:00:00', 'Party Hall, Anna Nagar'),
(4, 'Engagement Ceremony', 'Ring ceremony with floral decorations and photography', '2026-03-05', '11:00:00', 'Golden Palace, Velachery'),
(5, 'College Cultural Fest', 'Annual cultural program with stage and sound setup', '2026-03-15', '14:00:00', 'SIMATS University Campus'),
(6, 'Baby Shower', 'Traditional baby shower celebration with decorations', '2026-02-10', '10:30:00', 'Community Hall, T Nagar'),
(7, 'Housewarming Ceremony', 'Griha Pravesh puja with tent and seating arrangements', '2026-02-28', '08:00:00', 'Residential Villa, ECR'),
(8, 'Product Launch Event', 'New product launch with LED displays and stage setup', '2026-03-10', '15:00:00', 'Hotel Marriott, OMR'),
(9, 'Wedding Reception', 'Grand reception dinner with premium decorations', '2026-02-16', '18:00:00', 'Taj Convention Center, Chennai'),
(10, 'Temple Festival', 'Annual temple festival with tent and lighting arrangements', '2026-04-01', '06:00:00', 'Kapaleeshwarar Temple, Mylapore'),
(11, 'Retirement Party', 'Farewell event for senior executives with catering', '2026-02-25', '13:00:00', 'The Park Hotel, Nungambakkam'),
(12, 'Music Concert', 'Live music performance with professional sound system', '2026-03-20', '19:00:00', 'Phoenix MarketCity, Velachery'),
(13, 'Fashion Show', 'College fashion show with runway and lighting', '2026-03-25', '17:00:00', 'VGN Convention Center, Porur'),
(14, 'Food Festival', 'Street food festival with stalls and seating', '2026-04-10', '12:00:00', 'Island Grounds, Chennai'),
(15, 'Award Ceremony', 'Corporate awards night with stage and decor', '2026-04-15', '18:30:00', 'Leela Palace, Adyar');

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- Verify all events were inserted
SELECT * FROM events ORDER BY event_date;
