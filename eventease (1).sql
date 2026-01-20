-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 08:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventease`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin', '2025-12-24 08:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `rental_days` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `equipment_id`, `quantity`, `rental_days`, `total_amount`, `booking_date`, `event_id`, `status`, `start_date`, `end_date`, `time_slot`, `customer_name`, `customer_phone`, `delivery_address`) VALUES
(21, 2, 1, 1, 2, 2000.00, '2026-01-16 09:44:33', NULL, 'pending', '2026-01-16', '2026-01-18', 'Full Day', 'Test User', '9876543210', 'Test Address'),
(22, 2, 8, 1, 2, 4000.00, '2026-01-16 10:17:46', NULL, 'pending', '0000-00-00', '2026-01-18', 'Morning (6AM-12PM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615'),
(23, 2, 31, 2, 2, 0.00, '2026-01-16 17:30:16', NULL, '', '0000-00-00', '2026-01-18', 'Afternoon (12PM-6PM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615'),
(24, 2, 32, 2, 2, 0.00, '2026-01-17 02:58:46', NULL, 'pending', '0000-00-00', '2026-01-19', 'Afternoon (12PM-6PM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615'),
(25, 2, 10, 1, 1, 0.00, '2026-01-17 03:03:10', NULL, 'pending', '0000-00-00', '2026-01-18', 'Evening (6PM-12AM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615'),
(26, 2, 40, 1, 1, 450.00, '2026-01-17 03:12:50', NULL, 'pending', '0000-00-00', '2026-01-18', 'Afternoon (12PM-6PM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615'),
(27, 2, 37, 1, 1, 1500.00, '2026-01-20 04:49:39', NULL, 'pending', '0000-00-00', '2026-01-21', 'Afternoon (12PM-6PM)', 'John Doe', '1234567890', '8XP2+2MF, Challagundla, Andhra Pradesh 522615, India, Challagundla - 522615');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_uses` int(11) DEFAULT NULL,
  `times_used` int(11) DEFAULT 0,
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `max_uses`, `times_used`, `valid_from`, `valid_until`, `is_active`) VALUES
(1, 'SAVE10', 'percentage', 10.00, 500.00, NULL, 0, NULL, NULL, 1),
(2, 'SAVE20', 'percentage', 20.00, 1000.00, NULL, 0, NULL, NULL, 1),
(3, 'FLAT500', 'fixed', 500.00, 2000.00, NULL, 0, NULL, NULL, 1),
(4, 'WELCOME', 'percentage', 15.00, 0.00, NULL, 0, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  `specifications` text NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `availability` enum('Available','Limited','Unavailable') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` decimal(2,1) DEFAULT 4.5,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `category`, `type`, `specifications`, `price_per_day`, `quantity`, `availability`, `created_at`, `rating`, `image_url`, `description`) VALUES
(1, 'Large Event Speakers', 'Speakers', 'Professional Audio', '2000W, Bluetooth enabled, Weather resistant', 4000.00, 10, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(2, 'Bluetooth Speakers', 'Speakers', 'Portable', '500W, Rechargeable battery, 10hr playback', 1500.00, 12, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(3, 'Stage Speakers', 'Speakers', 'Concert Grade', '3000W, Line array system, Professional grade', 3000.00, 5, 'Limited', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(4, 'Red Carpet', 'Carpets', 'Event Carpet', '50ft x 4ft, Velvet finish, Premium quality', 800.00, 8, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(5, 'Event Carpet', 'Carpets', 'Floor Covering', '100 sq ft, Durable material, Easy to clean', 600.00, 12, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(6, 'Mandap Carpet', 'Carpets', 'Wedding Decor', '200 sq ft, Traditional design, Luxury finish', 1200.00, 6, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(7, 'Wedding Tent', 'Tents', 'Large Event', '40x60ft, Waterproof, Seats 200 people', 1500.00, 4, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(8, 'Canopy Tent', 'Tents', 'Medium Event', '20x20ft, UV protection, Pop-up design', 2000.00, 10, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(9, 'Small Shade Tent', 'Tents', 'Small Event', '10x10ft, Portable, Easy setup', 1000.00, 15, 'Limited', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(10, 'Large Cooking Vessels', 'Cooking Vessels', 'Commercial Grade', '50L capacity, Stainless steel, Heavy duty', 1500.00, 20, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(11, 'Gas Stoves', 'Cooking Vessels', 'Cooking Equipment', '4 burner, LPG compatible, Commercial use', 2000.00, 25, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(12, 'Serving Pots', 'Cooking Vessels', 'Serving Ware', 'Set of 10, Insulated, Buffet style', 3000.00, 30, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(13, 'Plastic Chairs', 'Chairs', 'Standard Seating', 'Stackable, Lightweight, Set of 50', 1500.00, 500, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(14, 'Wedding Chairs', 'Chairs', 'Premium Seating', 'Chiavari style, Gold finish, Cushioned', 2000.00, 200, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(15, 'VIP Cushioned Chairs', 'Chairs', 'Luxury Seating', 'Leather finish, Extra padding, Premium quality', 5000.00, 50, 'Limited', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(16, 'Banquet Tables', 'Hospitality Items', 'Tables', '6ft x 3ft, Folding, Set of 20', 1500.00, 100, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(17, 'Stage Platform', 'Hospitality Items', 'Stage Items', '20x15ft, Modular, Height adjustable', 2000.00, 10, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(18, 'Event Curtains', 'Hospitality Items', 'Draping', '30ft height, Velvet, Multiple colors', 1200.00, 20, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(19, 'LED Lighting System', 'Decoration Items', 'Lighting', 'RGB, DMX control, 50 lights', 4500.00, 15, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(20, 'Flower Stands', 'Decoration Items', 'Floral Decor', 'Metal, Adjustable height, Set of 10', 2000.00, 40, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(21, 'Backdrop Decor', 'Decoration Items', 'Background', '15x10ft, Customizable, Premium fabric', 3000.00, 8, 'Limited', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(22, 'Floral Arches', 'Decoration Items', 'Arches', '8ft tall, Metal frame, Floral arrangements', 1500.00, 10, 'Available', '2026-01-02 04:19:33', 4.5, NULL, NULL),
(23, 'small dj speaker', 'Audio', 'professional speaker, wedding', 'wedding anniversary', 200.00, 6, 'Available', '2026-01-03 06:40:46', 4.5, NULL, NULL),
(24, 'Large Event Speakers', 'Speakers', 'Professional Audio', 'High-power event speakers with subwoofer', 1500.00, 14, 'Available', '2026-01-03 13:15:05', 4.5, 'ic_large_event_speakers', 'Professional grade speakers for large events'),
(25, 'Bluetooth Speakers', 'Speakers', 'Portable Audio', 'Wireless Bluetooth speakers with 20hr battery', 1000.00, 22, 'Available', '2026-01-03 13:15:05', 4.2, 'ic_bluetooth_speaker', 'Portable Bluetooth speakers for small gatherings'),
(26, 'Stage Speakers', 'Speakers', 'Concert Grade', 'High-fidelity stage monitors and PA system', 2500.00, 7, 'Limited', '2026-01-03 13:15:05', 4.8, 'ic_stage_speakers', 'Concert quality stage speakers'),
(27, 'Red Carpet', 'Carpets', 'Event Flooring', 'Premium red carpet 50ft x 4ft roll', 1000.00, 20, 'Available', '2026-01-03 13:15:05', 4.6, 'ic_red_carpet', 'Classic red carpet for VIP events'),
(28, 'Event Carpet', 'Carpets', 'Event Flooring', 'Multi-purpose event carpet various colors', 900.00, 30, 'Available', '2026-01-03 13:15:05', 4.3, 'ic_event_carpet', 'Versatile event carpet for any occasion'),
(29, 'Mandap Carpet', 'Carpets', 'Wedding Flooring', 'Decorative mandap carpet with traditional design', 1200.00, 12, 'Limited', '2026-01-03 13:15:05', 4.7, 'ic_mandap_carpet', 'Traditional wedding mandap carpet'),
(30, 'Wedding Tent', 'Tents', 'Large Tent', '40x60 ft wedding shamiana with lining', 4500.00, 5, 'Limited', '2026-01-03 13:15:05', 4.9, 'ic_wedding_tent', 'Large decorated wedding tent'),
(31, 'Canopy Tent', 'Tents', 'Medium Tent', '20x20 ft canopy tent waterproof', 2000.00, 15, 'Available', '2026-01-03 13:15:05', 4.4, 'ic_canopy_tent', 'Waterproof canopy for outdoor events'),
(32, 'Small Shade Tent', 'Tents', 'Small Tent', '10x10 ft pop-up shade tent', 1550.00, 25, 'Available', '2026-01-03 13:15:05', 4.1, 'ic_small_shade_tent', 'Portable shade tent for small gatherings'),
(33, 'Large Cooking Vessels', 'Cooking Vessels', 'Commercial Grade', 'Set of 100L stainless steel vessels', 1500.00, 10, 'Available', '2026-01-03 13:15:05', 4.7, 'ic_large_cooking_vessels', 'Commercial cooking vessels for large events'),
(34, 'Gas Stoves', 'Cooking Vessels', 'Cooking Equipment', 'Industrial 3-burner gas stoves', 2000.00, 20, 'Available', '2026-01-03 13:15:05', 4.5, 'ic_gas_stove', 'Heavy duty gas stoves for catering'),
(35, 'Serving Pots', 'Cooking Vessels', 'Serving Ware', 'Decorative stainless steel serving pots', 3000.00, 40, 'Available', '2026-01-03 13:15:05', 4.0, 'ic_serving_pots', 'Elegant serving pots for buffet'),
(36, 'Boilers', 'Cooking Vessels', 'Cooking Equipment', 'Large water boilers 50L capacity', 570.00, 15, 'Available', '2026-01-03 13:15:05', 3.9, 'ic_large_cooking_vessels', 'Industrial water boilers'),
(37, 'Plastic Chairs', 'Chairs', 'Standard Seating', 'Durable plastic chairs stackable', 1500.00, 200, 'Available', '2026-01-03 13:15:05', 4.2, 'ic_plastic_chair', 'Standard plastic chairs per 100 units'),
(38, 'Wedding Chairs', 'Chairs', 'Premium Seating', 'Decorated chairs with fabric cover', 2000.00, 150, 'Available', '2026-01-03 13:15:05', 4.6, 'ic_wedding_chairs', 'Elegant wedding chairs with covers'),
(39, 'VIP Cushioned Chairs', 'Chairs', 'Luxury Seating', 'Premium cushioned VIP chairs', 5000.00, 50, 'Limited', '2026-01-03 13:15:05', 4.8, 'ic_vip_chairs', 'Luxury VIP seating'),
(40, 'Banquet Tables', 'Hospitality Items', 'Furniture', '6ft rectangular banquet tables', 1500.00, 100, 'Available', '2026-01-03 13:15:05', 4.4, 'ic_banquet_tables', 'Sturdy banquet tables for events'),
(41, 'Stage Platform', 'Hospitality Items', 'Stage Equipment', '4x8 ft modular stage platform sections', 2000.00, 20, 'Limited', '2026-01-03 13:15:05', 4.7, 'ic_stage_platform', 'Modular stage platform'),
(42, 'Event Curtains', 'Hospitality Items', 'Decoration', 'Velvet event curtains various colors', 1200.00, 50, 'Available', '2026-01-03 13:15:05', 4.1, 'ic_event_curtains', 'Decorative event curtains'),
(43, 'LED Lighting System', 'Decoration Items', 'Lighting', 'RGB LED party lights with controller', 4500.00, 30, 'Available', '2026-01-03 13:15:05', 4.9, 'ic_led_lighting', 'Dynamic LED lighting system'),
(44, 'Flower Stands', 'Decoration Items', 'Floral Decor', 'Decorative metal flower stands', 2000.00, 60, 'Available', '2026-01-03 13:15:05', 4.5, 'ic_flower_stands', 'Elegant flower display stands'),
(45, 'Backdrop Decor', 'Decoration Items', 'Stage Backdrop', 'Premium fabric backdrop with frame', 3000.00, 15, 'Limited', '2026-01-03 13:15:05', 4.8, 'ic_backdrop_decor', 'Beautiful event backdrop');

-- --------------------------------------------------------

--
-- Table structure for table `event_equipment`
--

CREATE TABLE `event_equipment` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_equipment`
--

INSERT INTO `event_equipment` (`id`, `event_id`, `equipment_id`, `quantity`, `price_per_day`, `total_price`, `created_at`) VALUES
(2, 1, 2, 5, 200.00, 1000.00, '2025-12-14 11:27:46'),
(3, 1, 1, 20, 50.00, 1000.00, '2025-12-14 11:40:35'),
(4, 1, 5, 2, 300.00, 600.00, '2025-12-29 16:12:35'),
(5, 1, 5, 2, 300.00, 600.00, '2025-12-29 16:18:18'),
(6, 1, 5, 2, 300.00, 600.00, '2025-12-29 16:18:18'),
(7, 1, 5, 2, 300.00, 600.00, '2025-12-29 16:18:18'),
(8, 1, 5, 2, 300.00, 600.00, '2025-12-29 16:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `event_operators`
--

CREATE TABLE `event_operators` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_operators`
--

INSERT INTO `event_operators` (`id`, `event_id`, `operator_id`) VALUES
(1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` enum('booking','admin','new_equipment') NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `status`, `created_at`) VALUES
(1, 1, 'booking', 'Your booking for Plastic Chair has been accepted', 'unread', '2025-12-14 12:21:11'),
(2, 1, 'booking', 'Your booking for Round Table has been rejected', 'unread', '2025-12-14 12:21:11'),
(3, NULL, 'admin', 'System maintenance scheduled for 15th Dec', 'unread', '2025-12-14 12:21:11'),
(4, NULL, 'new_equipment', 'New equipment LED Lights added to the catalog', 'unread', '2025-12-14 12:21:11'),
(5, NULL, 'admin', 'gjkthn: bmm', 'unread', '2026-01-02 08:27:13'),
(6, NULL, 'admin', 'b nht: gvbbh', 'unread', '2026-01-02 08:36:55'),
(7, NULL, 'admin', 'kaveri: kaveri', 'unread', '2026-01-02 08:42:58'),
(8, 1, 'booking', 'Your booking #1 has been ACCEPTED', 'unread', '2026-01-02 12:45:46'),
(9, NULL, 'admin', 'your order is delivered: take your order safety', 'unread', '2026-01-03 11:13:51'),
(10, NULL, 'admin', 'your order will come soon: get your product is ready', 'unread', '2026-01-03 11:46:07'),
(11, 1, 'booking', 'Your booking #11 has been REJECTED', 'unread', '2026-01-04 06:33:43'),
(12, 1, 'booking', 'Your booking #10 has been ACCEPTED', 'unread', '2026-01-04 06:41:08'),
(13, 1, 'booking', 'Your booking #9 has been ACCEPTED', 'unread', '2026-01-04 11:54:30'),
(14, 1, 'booking', 'Your booking #8 has been ACCEPTED', 'unread', '2026-01-04 11:54:34'),
(15, 1, 'booking', 'Your order #7 is being prepared.', 'unread', '2026-01-04 12:15:45'),
(16, 1, 'booking', 'Your order #5 is out for delivery!', 'unread', '2026-01-04 12:23:34'),
(17, 1, 'booking', 'Your booking #4 has been approved!', 'unread', '2026-01-04 12:23:40'),
(18, 1, 'booking', '???? Great news! Your booking #6 has been APPROVED. We will start preparing your items.', 'unread', '2026-01-05 04:46:03'),
(19, NULL, 'admin', 'vvh: hhj', 'unread', '2026-01-05 08:25:04'),
(20, NULL, 'admin', 'Bobby reddy', 'unread', '2026-01-06 16:39:48'),
(21, 2, 'booking', 'Your booking for Bluetooth Speakers has been submitted. Waiting for approval.', 'unread', '2026-01-09 13:12:05'),
(22, 2, 'booking', 'Your booking for Red Carpet has been submitted. Waiting for approval.', 'unread', '2026-01-09 14:39:41'),
(23, 2, 'booking', 'Your booking for Bluetooth Speakers has been submitted. Waiting for approval.', 'unread', '2026-01-09 15:35:02'),
(24, 2, 'booking', 'Your booking for Wedding Tent has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:01:04'),
(25, 2, 'booking', 'Your booking for Small Shade Tent has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:22:34'),
(26, 2, 'booking', 'Your booking for Stage Platform has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:43:21'),
(27, 2, 'booking', 'Your booking for Red Carpet has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:46:46'),
(28, 2, 'booking', 'Your booking for Red Carpet has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:47:08'),
(29, 2, 'booking', 'Your booking for Large Cooking Vessels has been submitted. Waiting for approval.', 'unread', '2026-01-12 04:50:00'),
(30, 1, 'booking', 'Your booking #2 has been cancelled.', 'unread', '2026-01-12 04:58:53'),
(31, 1, 'booking', 'Your booking #2 has been approved!', 'unread', '2026-01-12 04:58:56'),
(32, 1, 'booking', 'Your order #2 is out for delivery!', 'unread', '2026-01-12 04:58:58'),
(33, 1, 'booking', 'Your booking #3 has been approved!', 'unread', '2026-01-12 04:58:59'),
(34, NULL, 'admin', 'Bobby: Bobby', 'unread', '2026-01-12 12:19:22'),
(35, 1, 'booking', 'Item for booking #1 is currently out of stock. We\'ll notify you when available.', 'unread', '2026-01-16 08:38:09'),
(36, 1, 'booking', 'Item for booking #3 is currently out of stock. We\'ll notify you when available.', 'unread', '2026-01-16 08:38:13'),
(37, 2, 'booking', 'Your order #23 is out for delivery!', 'unread', '2026-01-17 03:00:54');

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operators`
--

INSERT INTO `operators` (`id`, `name`, `phone`) VALUES
(2, 'Ravi', '9876543210');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verification`
--

CREATE TABLE `otp_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verification`
--

INSERT INTO `otp_verification` (`id`, `email`, `otp`, `otp_expiry`, `created_at`) VALUES
(1, 'samudralabhavya60@gmail.com', '194215', '2026-01-05 13:01:04', '2026-01-03 03:21:06'),
(9, 'inuvasareddys48@gmail.com', '931150', '2026-01-05 10:41:03', '2026-01-05 04:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `feedback` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `equipment_id`, `rating`, `feedback`, `review_date`) VALUES
(1, 1, 2, 5, 'Excellent quality and service!', '2025-12-14 12:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `support_requests`
--

CREATE TABLE `support_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','in_progress','resolved','closed') DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_requests`
--

INSERT INTO `support_requests` (`id`, `user_id`, `user_name`, `user_email`, `category`, `message`, `status`, `admin_response`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Doe', 'john@example.com', 'Booking Issue', 'I need help with my booking #123', 'pending', NULL, '2026-01-04 06:10:41', '2026-01-04 06:10:41'),
(2, 1, 'John Doe', 'john@example.com', 'Payment Problem', 'Payment failed but amount deducted', 'in_progress', NULL, '2026-01-04 06:10:41', '2026-01-04 06:10:41'),
(3, 2, 'Jane Smith', 'jane@example.com', 'Equipment Issue', 'Received damaged equipment', 'pending', NULL, '2026-01-04 06:10:41', '2026-01-04 06:10:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fcm_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `name`, `email`, `phone`, `password`, `status`, `created_at`, `fcm_token`) VALUES
(1, 'EE85677', 'Admin User', 'ravi.k@gmail.com', '9999999999', '$2y$10$kvWlgTFC2EobV5/lpGyVSeSAdEJyvBuooiEaPSS9jiwtZzQid3yKi', 0, '2025-12-14 05:27:40', NULL),
(2, 'EE76440', 'John Doe', 'd@gmail.com', '1234567890', '123', 1, '2025-12-14 05:29:46', 'd8163JWiS6Stt3E42uALmG:APA91bEBo8XHYc_dfXTiIapGPKsriCNSPCIPY257-c0rDs-tFcisGtWDmSwhWYNzG37IiGo5vsl5rRB3ebounkU3M2vDn1Wwu_S4DSD-xuMjFtio3NcK-gc'),
(3, 'EE49716', 'sri S', 'bobby@gmail.com', '9876543210', '123456', 1, '2025-12-28 12:29:26', NULL),
(4, 'EE12994', 'sri S', 'chinnu@gmail.com', '8978201651', '123456', 1, '2025-12-28 12:52:31', NULL),
(5, 'EE67987', 'sri S', 'chinnu12@gmail.com', '8978201681', '123456', 1, '2025-12-28 13:42:50', NULL),
(6, 'EE47300', 'sri S', 'chinnu122@gmail.com', '8978221681', '123456', 1, '2025-12-28 13:47:32', NULL),
(7, 'EE92744', 'sri S', 'chinnu2@gmail.com', '8978291681', '123456', 1, '2025-12-28 14:15:51', NULL),
(8, 'EE63273', 'sri S', 'h2@gmail.com', '8978291691', '123456', 1, '2025-12-29 03:50:14', NULL),
(9, 'EE86463', 'Bhavya Samudrala', 'bhavy@example.com', '9876543010', '123456', 1, '2025-12-29 07:39:56', NULL),
(10, 'EE28487', 'Bobby Samudrala', 'b@example.com', '987654321', '123456', 1, '2025-12-29 07:53:58', NULL),
(11, 'EE40218', 'Bobby Samudrala', 'h@example.com', '987654320', '123456', 1, '2025-12-29 07:54:45', NULL),
(12, 'EE20183', 'Bobby Samudrala', 'g@example.com', '787654320', '123456', 1, '2025-12-30 11:49:21', NULL),
(13, 'EE93007', 'moksha', 'mokshi13@gmail.com', '7896543218', 'mokshi', 1, '2026-01-04 11:46:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_equipment`
--
ALTER TABLE `event_equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_operators`
--
ALTER TABLE `event_operators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verification`
--
ALTER TABLE `otp_verification`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `support_requests`
--
ALTER TABLE `support_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `event_equipment`
--
ALTER TABLE `event_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `event_operators`
--
ALTER TABLE `event_operators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_verification`
--
ALTER TABLE `otp_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_requests`
--
ALTER TABLE `support_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
