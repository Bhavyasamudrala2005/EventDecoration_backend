-- Create bookings table for storing user equipment bookings
-- Run this SQL in your phpMyAdmin or MySQL client

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    equipment_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    rental_days INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'rejected', 'preparing', 'out_for_delivery', 'delivered', 'completed', 'cancelled') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NULL,
    end_date DATE NULL,
    time_slot VARCHAR(50) DEFAULT 'Full Day',
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('pending', 'partial', 'completed') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- Create indexes for faster queries
CREATE INDEX IF NOT EXISTS idx_bookings_user_id ON bookings(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_status ON bookings(status);
CREATE INDEX IF NOT EXISTS idx_bookings_date ON bookings(booking_date DESC);

-- Add time_slot column if it doesn't exist (for existing tables)
-- Run this if you already have bookings table but missing time_slot
-- ALTER TABLE bookings ADD COLUMN IF NOT EXISTS time_slot VARCHAR(50) DEFAULT 'Full Day';
-- ALTER TABLE bookings ADD COLUMN IF NOT EXISTS start_date DATE NULL;
-- ALTER TABLE bookings ADD COLUMN IF NOT EXISTS end_date DATE NULL;
