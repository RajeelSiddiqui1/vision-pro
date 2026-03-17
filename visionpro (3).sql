-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 17, 2026 at 08:32 PM
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
-- Database: `visionpro`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'Canada',
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `device_model` varchar(100) DEFAULT NULL,
  `issue_description` text DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `description`, `is_active`, `created_at`) VALUES
(1, 'Apple', 'apple', 'assets/images/brands/apple.png', 'Premium electronics and software.', 1, '2026-03-17 18:42:36'),
(2, 'Samsung', 'samsung', 'assets/images/brands/samsung.png', 'Leading innovator in displays and mobiles.', 1, '2026-03-17 18:42:36'),
(3, 'Microsoft', 'microsoft', 'assets/images/brands/microsoft.png', 'Empowering every person and organization.', 1, '2026-03-17 18:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `brand_id`, `image_url`, `created_at`) VALUES
(1, 'iPhone', 'iphone', NULL, 1, 'assets/images/categories/iphone.png', '2026-03-17 18:42:36'),
(2, 'iPad', 'ipad', NULL, 1, 'assets/images/categories/ipad.png', '2026-03-17 18:42:36'),
(3, 'MacBook', 'macbook', NULL, 1, 'assets/images/categories/macbook.png', '2026-03-17 18:42:36'),
(4, 'Galaxy S', 'galaxy-s', NULL, 2, 'assets/images/categories/galaxy-s.png', '2026-03-17 18:42:36'),
(5, 'Galaxy Z', 'galaxy-z', NULL, 2, 'assets/images/categories/galaxy-z.png', '2026-03-17 18:42:36'),
(6, 'Surface Pro', 'surface-pro', NULL, 3, 'assets/images/categories/surface-pro.png', '2026-03-17 18:42:36'),
(7, 'Surface Laptop', 'surface-laptop', NULL, 3, 'assets/images/categories/surface-laptop.png', '2026-03-17 18:42:36'),
(8, 'iPhone 16 Pro Max', 'iphone-16-pro-max', 1, 1, 'assets/images/categories/sub-iphone-16-pro-max.png', '2026-03-17 18:42:36'),
(9, 'iPhone 15 Pro', 'iphone-15-pro', 1, 1, 'assets/images/categories/sub-iphone-15-pro.png', '2026-03-17 18:42:36'),
(10, 'iPhone 14', 'iphone-14', 1, 1, 'assets/images/categories/sub-iphone-14.png', '2026-03-17 18:42:36'),
(11, 'iPad Pro M4', 'ipad-pro-m4', 2, 1, 'assets/images/categories/sub-ipad-pro-m4.png', '2026-03-17 18:42:36'),
(12, 'MacBook Pro M3', 'macbook-pro-m3', 3, 1, 'assets/images/categories/sub-macbook-pro-m3.png', '2026-03-17 18:42:36'),
(13, 'Galaxy S24 Ultra', 'galaxy-s24-ultra', 4, 2, 'assets/images/categories/sub-galaxy-s24-ultra.png', '2026-03-17 18:42:36'),
(14, 'Galaxy S23 Ultra', 'galaxy-s23-ultra', 4, 2, 'assets/images/categories/sub-galaxy-s23-ultra.png', '2026-03-17 18:42:36'),
(15, 'Galaxy Z Fold 6', 'galaxy-z-fold-6', 5, 2, 'assets/images/categories/sub-galaxy-z-fold-6.png', '2026-03-17 18:42:36'),
(16, 'Surface Pro 9', 'surface-pro-9', 6, 3, 'assets/images/categories/sub-surface-pro-9.png', '2026-03-17 18:42:36'),
(17, 'Surface Laptop 5', 'surface-laptop-5', 7, 3, 'assets/images/categories/sub-surface-laptop-5.png', '2026-03-17 18:42:36'),
(18, 'OLED Screen', 'iphone-16-pro-max-oled-screen', 8, 1, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(19, 'Battery Pack', 'iphone-16-pro-max-battery-pack', 8, 1, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(20, 'Rear Housing', 'iphone-16-pro-max-rear-housing', 8, 1, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(21, 'Camera Module', 'iphone-16-pro-max-camera-module', 8, 1, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(22, 'Charging Port', 'iphone-16-pro-max-charging-port', 8, 1, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(23, 'OLED Screen', 'iphone-15-pro-oled-screen', 9, 1, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(24, 'Battery Pack', 'iphone-15-pro-battery-pack', 9, 1, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(25, 'Rear Housing', 'iphone-15-pro-rear-housing', 9, 1, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(26, 'Camera Module', 'iphone-15-pro-camera-module', 9, 1, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(27, 'Charging Port', 'iphone-15-pro-charging-port', 9, 1, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(28, 'OLED Screen', 'iphone-14-oled-screen', 10, 1, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(29, 'Battery Pack', 'iphone-14-battery-pack', 10, 1, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(30, 'Rear Housing', 'iphone-14-rear-housing', 10, 1, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(31, 'Camera Module', 'iphone-14-camera-module', 10, 1, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(32, 'Charging Port', 'iphone-14-charging-port', 10, 1, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(33, 'OLED Screen', 'ipad-pro-m4-oled-screen', 11, 1, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(34, 'Battery Pack', 'ipad-pro-m4-battery-pack', 11, 1, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(35, 'Rear Housing', 'ipad-pro-m4-rear-housing', 11, 1, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(36, 'Camera Module', 'ipad-pro-m4-camera-module', 11, 1, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(37, 'Charging Port', 'ipad-pro-m4-charging-port', 11, 1, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(38, 'OLED Screen', 'macbook-pro-m3-oled-screen', 12, 1, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(39, 'Battery Pack', 'macbook-pro-m3-battery-pack', 12, 1, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(40, 'Rear Housing', 'macbook-pro-m3-rear-housing', 12, 1, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(41, 'Camera Module', 'macbook-pro-m3-camera-module', 12, 1, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(42, 'Charging Port', 'macbook-pro-m3-charging-port', 12, 1, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(43, 'OLED Screen', 'galaxy-s24-ultra-oled-screen', 13, 2, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(44, 'Battery Pack', 'galaxy-s24-ultra-battery-pack', 13, 2, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(45, 'Rear Housing', 'galaxy-s24-ultra-rear-housing', 13, 2, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(46, 'Camera Module', 'galaxy-s24-ultra-camera-module', 13, 2, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(47, 'Charging Port', 'galaxy-s24-ultra-charging-port', 13, 2, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(48, 'OLED Screen', 'galaxy-s23-ultra-oled-screen', 14, 2, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(49, 'Battery Pack', 'galaxy-s23-ultra-battery-pack', 14, 2, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(50, 'Rear Housing', 'galaxy-s23-ultra-rear-housing', 14, 2, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(51, 'Camera Module', 'galaxy-s23-ultra-camera-module', 14, 2, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(52, 'Charging Port', 'galaxy-s23-ultra-charging-port', 14, 2, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(53, 'OLED Screen', 'galaxy-z-fold-6-oled-screen', 15, 2, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(54, 'Battery Pack', 'galaxy-z-fold-6-battery-pack', 15, 2, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(55, 'Rear Housing', 'galaxy-z-fold-6-rear-housing', 15, 2, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(56, 'Camera Module', 'galaxy-z-fold-6-camera-module', 15, 2, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(57, 'Charging Port', 'galaxy-z-fold-6-charging-port', 15, 2, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(58, 'OLED Screen', 'surface-pro-9-oled-screen', 16, 3, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(59, 'Battery Pack', 'surface-pro-9-battery-pack', 16, 3, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(60, 'Rear Housing', 'surface-pro-9-rear-housing', 16, 3, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(61, 'Camera Module', 'surface-pro-9-camera-module', 16, 3, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(62, 'Charging Port', 'surface-pro-9-charging-port', 16, 3, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36'),
(63, 'OLED Screen', 'surface-laptop-5-oled-screen', 17, 3, 'assets/images/categories/parts/screen.png', '2026-03-17 18:42:36'),
(64, 'Battery Pack', 'surface-laptop-5-battery-pack', 17, 3, 'assets/images/categories/parts/battery.png', '2026-03-17 18:42:36'),
(65, 'Rear Housing', 'surface-laptop-5-rear-housing', 17, 3, 'assets/images/categories/parts/housing.png', '2026-03-17 18:42:36'),
(66, 'Camera Module', 'surface-laptop-5-camera-module', 17, 3, 'assets/images/categories/parts/camera.png', '2026-03-17 18:42:36'),
(67, 'Charging Port', 'surface-laptop-5-charging-port', 17, 3, 'assets/images/categories/parts/charging.png', '2026-03-17 18:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `device_categories`
--

CREATE TABLE `device_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_subcategories`
--

CREATE TABLE `device_subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `shipping_address_id` int(11) DEFAULT NULL,
  `billing_address_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `tax_amount`, `shipping_amount`, `status`, `shipping_address`, `payment_status`, `shipping_address_id`, `billing_address_id`, `tracking_number`, `created_at`) VALUES
(1, 2, 101.69, 11.70, 0.00, 'pending', NULL, 'unpaid', NULL, NULL, NULL, '2026-02-06 00:35:01'),
(2, 2, 101.69, 11.70, 0.00, 'processing', '3001 Markham Rd Unit 20, Scarborough, ON, Scarborough, M1X 1L6', 'unpaid', NULL, NULL, NULL, '2026-02-06 00:52:20'),
(3, 10, 1108.53, 127.53, 0.00, 'processing', 'l116/1 nafees bunglows, jinnah square, malir, karachi, ontario 75050', 'paid', NULL, NULL, NULL, '2026-03-13 21:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 89.99),
(2, 2, 2, 1, 89.99),
(3, 3, 201, 1, 981.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quality_tier` varchar(100) DEFAULT 'Standard',
  `warranty` varchar(100) DEFAULT 'Lifetime Warranty',
  `compatibility` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `bulk_pricing` text DEFAULT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `main_image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `stock_deducted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `quality_tier`, `warranty`, `compatibility`, `price`, `bulk_pricing`, `discount_price`, `sku`, `part_number`, `stock_quantity`, `main_image`, `is_featured`, `created_at`, `is_active`, `stock_deducted`) VALUES
(1, 18, 1, 'iPhone 16 Pro Max OLED Screen - Premium Quality', 'iphone-16-pro-max-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for iPhone 16 Pro Max. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 16 Pro Max', 185.99, '[]', NULL, 'VP-IPH-709', 'PN-5974', 30, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(2, 19, 1, 'iPhone 16 Pro Max Battery Pack - Premium Quality', 'iphone-16-pro-max-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for iPhone 16 Pro Max. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 16 Pro Max', 50.99, '[]', NULL, 'VP-IPH-179', 'PN-5942', 39, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(3, 20, 1, 'iPhone 16 Pro Max Rear Housing - Premium Quality', 'iphone-16-pro-max-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for iPhone 16 Pro Max. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 16 Pro Max', 83.99, '[]', NULL, 'VP-IPH-185', 'PN-2726', 40, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(4, 21, 1, 'iPhone 16 Pro Max Camera Module - Premium Quality', 'iphone-16-pro-max-camera-module---premium-quality', 'Premium grade Camera Module replacement for iPhone 16 Pro Max. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 16 Pro Max', 136.99, '[]', NULL, 'VP-IPH-819', 'PN-1795', 14, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(5, 22, 1, 'iPhone 16 Pro Max Charging Port - Premium Quality', 'iphone-16-pro-max-charging-port---premium-quality', 'Premium grade Charging Port replacement for iPhone 16 Pro Max. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 16 Pro Max', 28.99, '[]', NULL, 'VP-IPH-198', 'PN-3546', 11, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(6, 23, 1, 'iPhone 15 Pro OLED Screen - Premium Quality', 'iphone-15-pro-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for iPhone 15 Pro. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 15 Pro', 183.99, '[]', NULL, 'VP-IPH-226', 'PN-6680', 44, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(7, 24, 1, 'iPhone 15 Pro Battery Pack - Premium Quality', 'iphone-15-pro-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for iPhone 15 Pro. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 15 Pro', 39.99, '[]', NULL, 'VP-IPH-874', 'PN-8722', 46, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(8, 25, 1, 'iPhone 15 Pro Rear Housing - Premium Quality', 'iphone-15-pro-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for iPhone 15 Pro. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 15 Pro', 72.99, '[]', NULL, 'VP-IPH-902', 'PN-6867', 33, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(9, 26, 1, 'iPhone 15 Pro Camera Module - Premium Quality', 'iphone-15-pro-camera-module---premium-quality', 'Premium grade Camera Module replacement for iPhone 15 Pro. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 15 Pro', 136.99, '[]', NULL, 'VP-IPH-276', 'PN-3329', 45, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(10, 27, 1, 'iPhone 15 Pro Charging Port - Premium Quality', 'iphone-15-pro-charging-port---premium-quality', 'Premium grade Charging Port replacement for iPhone 15 Pro. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 15 Pro', 32.99, '[]', NULL, 'VP-IPH-965', 'PN-6339', 18, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(11, 28, 1, 'iPhone 14 OLED Screen - Premium Quality', 'iphone-14-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for iPhone 14. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 14', 188.99, '[]', NULL, 'VP-IPH-412', 'PN-5184', 24, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(12, 29, 1, 'iPhone 14 Battery Pack - Premium Quality', 'iphone-14-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for iPhone 14. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 14', 42.99, '[]', NULL, 'VP-IPH-391', 'PN-2090', 23, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(13, 30, 1, 'iPhone 14 Rear Housing - Premium Quality', 'iphone-14-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for iPhone 14. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 14', 72.99, '[]', NULL, 'VP-IPH-806', 'PN-2947', 13, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(14, 31, 1, 'iPhone 14 Camera Module - Premium Quality', 'iphone-14-camera-module---premium-quality', 'Premium grade Camera Module replacement for iPhone 14. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 14', 137.99, '[]', NULL, 'VP-IPH-546', 'PN-3733', 24, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(15, 32, 1, 'iPhone 14 Charging Port - Premium Quality', 'iphone-14-charging-port---premium-quality', 'Premium grade Charging Port replacement for iPhone 14. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPhone 14', 25.99, '[]', NULL, 'VP-IPH-632', 'PN-8798', 32, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(16, 33, 1, 'iPad Pro M4 OLED Screen - Premium Quality', 'ipad-pro-m4-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for iPad Pro M4. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPad Pro M4', 198.99, '[]', NULL, 'VP-IPA-508', 'PN-3875', 47, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(17, 34, 1, 'iPad Pro M4 Battery Pack - Premium Quality', 'ipad-pro-m4-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for iPad Pro M4. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPad Pro M4', 41.99, '[]', NULL, 'VP-IPA-941', 'PN-6581', 41, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(18, 35, 1, 'iPad Pro M4 Rear Housing - Premium Quality', 'ipad-pro-m4-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for iPad Pro M4. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPad Pro M4', 84.99, '[]', NULL, 'VP-IPA-759', 'PN-7396', 39, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(19, 36, 1, 'iPad Pro M4 Camera Module - Premium Quality', 'ipad-pro-m4-camera-module---premium-quality', 'Premium grade Camera Module replacement for iPad Pro M4. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPad Pro M4', 133.99, '[]', NULL, 'VP-IPA-577', 'PN-7833', 21, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(20, 37, 1, 'iPad Pro M4 Charging Port - Premium Quality', 'ipad-pro-m4-charging-port---premium-quality', 'Premium grade Charging Port replacement for iPad Pro M4. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'iPad Pro M4', 32.99, '[]', NULL, 'VP-IPA-848', 'PN-1985', 34, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(21, 38, 1, 'MacBook Pro M3 OLED Screen - Premium Quality', 'macbook-pro-m3-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for MacBook Pro M3. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'MacBook Pro M3', 186.99, '[]', NULL, 'VP-MAC-490', 'PN-8756', 32, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(22, 39, 1, 'MacBook Pro M3 Battery Pack - Premium Quality', 'macbook-pro-m3-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for MacBook Pro M3. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'MacBook Pro M3', 48.99, '[]', NULL, 'VP-MAC-890', 'PN-8810', 10, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(23, 40, 1, 'MacBook Pro M3 Rear Housing - Premium Quality', 'macbook-pro-m3-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for MacBook Pro M3. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'MacBook Pro M3', 76.99, '[]', NULL, 'VP-MAC-393', 'PN-7818', 24, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(24, 41, 1, 'MacBook Pro M3 Camera Module - Premium Quality', 'macbook-pro-m3-camera-module---premium-quality', 'Premium grade Camera Module replacement for MacBook Pro M3. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'MacBook Pro M3', 134.99, '[]', NULL, 'VP-MAC-585', 'PN-8991', 19, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(25, 42, 1, 'MacBook Pro M3 Charging Port - Premium Quality', 'macbook-pro-m3-charging-port---premium-quality', 'Premium grade Charging Port replacement for MacBook Pro M3. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'MacBook Pro M3', 32.99, '[]', NULL, 'VP-MAC-508', 'PN-5359', 37, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(26, 43, 2, 'Galaxy S24 Ultra OLED Screen - Premium Quality', 'galaxy-s24-ultra-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for Galaxy S24 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S24 Ultra', 196.99, '[]', NULL, 'VP-GAL-944', 'PN-4360', 20, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(27, 44, 2, 'Galaxy S24 Ultra Battery Pack - Premium Quality', 'galaxy-s24-ultra-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for Galaxy S24 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S24 Ultra', 45.99, '[]', NULL, 'VP-GAL-343', 'PN-7704', 16, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(28, 45, 2, 'Galaxy S24 Ultra Rear Housing - Premium Quality', 'galaxy-s24-ultra-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for Galaxy S24 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S24 Ultra', 84.99, '[]', NULL, 'VP-GAL-382', 'PN-2942', 20, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(29, 46, 2, 'Galaxy S24 Ultra Camera Module - Premium Quality', 'galaxy-s24-ultra-camera-module---premium-quality', 'Premium grade Camera Module replacement for Galaxy S24 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S24 Ultra', 136.99, '[]', NULL, 'VP-GAL-427', 'PN-5943', 17, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(30, 47, 2, 'Galaxy S24 Ultra Charging Port - Premium Quality', 'galaxy-s24-ultra-charging-port---premium-quality', 'Premium grade Charging Port replacement for Galaxy S24 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S24 Ultra', 35.99, '[]', NULL, 'VP-GAL-397', 'PN-6415', 12, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(31, 48, 2, 'Galaxy S23 Ultra OLED Screen - Premium Quality', 'galaxy-s23-ultra-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for Galaxy S23 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S23 Ultra', 188.99, '[]', NULL, 'VP-GAL-688', 'PN-9167', 20, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(32, 49, 2, 'Galaxy S23 Ultra Battery Pack - Premium Quality', 'galaxy-s23-ultra-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for Galaxy S23 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S23 Ultra', 55.99, '[]', NULL, 'VP-GAL-986', 'PN-9686', 41, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(33, 50, 2, 'Galaxy S23 Ultra Rear Housing - Premium Quality', 'galaxy-s23-ultra-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for Galaxy S23 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S23 Ultra', 71.99, '[]', NULL, 'VP-GAL-656', 'PN-2783', 33, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(34, 51, 2, 'Galaxy S23 Ultra Camera Module - Premium Quality', 'galaxy-s23-ultra-camera-module---premium-quality', 'Premium grade Camera Module replacement for Galaxy S23 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S23 Ultra', 134.99, '[]', NULL, 'VP-GAL-919', 'PN-5024', 40, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(35, 52, 2, 'Galaxy S23 Ultra Charging Port - Premium Quality', 'galaxy-s23-ultra-charging-port---premium-quality', 'Premium grade Charging Port replacement for Galaxy S23 Ultra. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy S23 Ultra', 41.99, '[]', NULL, 'VP-GAL-345', 'PN-3039', 22, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(36, 53, 2, 'Galaxy Z Fold 6 OLED Screen - Premium Quality', 'galaxy-z-fold-6-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for Galaxy Z Fold 6. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy Z Fold 6', 190.99, '[]', NULL, 'VP-GAL-350', 'PN-7072', 24, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(37, 54, 2, 'Galaxy Z Fold 6 Battery Pack - Premium Quality', 'galaxy-z-fold-6-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for Galaxy Z Fold 6. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy Z Fold 6', 47.99, '[]', NULL, 'VP-GAL-139', 'PN-3144', 25, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(38, 55, 2, 'Galaxy Z Fold 6 Rear Housing - Premium Quality', 'galaxy-z-fold-6-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for Galaxy Z Fold 6. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy Z Fold 6', 82.99, '[]', NULL, 'VP-GAL-923', 'PN-5738', 14, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(39, 56, 2, 'Galaxy Z Fold 6 Camera Module - Premium Quality', 'galaxy-z-fold-6-camera-module---premium-quality', 'Premium grade Camera Module replacement for Galaxy Z Fold 6. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy Z Fold 6', 119.99, '[]', NULL, 'VP-GAL-822', 'PN-1753', 31, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(40, 57, 2, 'Galaxy Z Fold 6 Charging Port - Premium Quality', 'galaxy-z-fold-6-charging-port---premium-quality', 'Premium grade Charging Port replacement for Galaxy Z Fold 6. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Galaxy Z Fold 6', 32.99, '[]', NULL, 'VP-GAL-497', 'PN-6198', 16, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(41, 58, 3, 'Surface Pro 9 OLED Screen - Premium Quality', 'surface-pro-9-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for Surface Pro 9. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Pro 9', 182.99, '[]', NULL, 'VP-SUR-753', 'PN-5470', 43, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(42, 59, 3, 'Surface Pro 9 Battery Pack - Premium Quality', 'surface-pro-9-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for Surface Pro 9. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Pro 9', 51.99, '[]', NULL, 'VP-SUR-581', 'PN-8884', 16, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(43, 60, 3, 'Surface Pro 9 Rear Housing - Premium Quality', 'surface-pro-9-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for Surface Pro 9. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Pro 9', 89.99, '[]', NULL, 'VP-SUR-866', 'PN-6631', 35, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(44, 61, 3, 'Surface Pro 9 Camera Module - Premium Quality', 'surface-pro-9-camera-module---premium-quality', 'Premium grade Camera Module replacement for Surface Pro 9. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Pro 9', 121.99, '[]', NULL, 'VP-SUR-558', 'PN-4696', 28, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(45, 62, 3, 'Surface Pro 9 Charging Port - Premium Quality', 'surface-pro-9-charging-port---premium-quality', 'Premium grade Charging Port replacement for Surface Pro 9. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Pro 9', 28.99, '[]', NULL, 'VP-SUR-887', 'PN-7651', 47, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0),
(46, 63, 3, 'Surface Laptop 5 OLED Screen - Premium Quality', 'surface-laptop-5-oled-screen---premium-quality', 'Premium grade OLED Screen replacement for Surface Laptop 5. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Laptop 5', 194.99, '[]', NULL, 'VP-SUR-451', 'PN-5369', 22, 'assets/images/products/screen.webp', 0, '2026-03-17 18:42:36', 1, 0),
(47, 64, 3, 'Surface Laptop 5 Battery Pack - Premium Quality', 'surface-laptop-5-battery-pack---premium-quality', 'Premium grade Battery Pack replacement for Surface Laptop 5. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Laptop 5', 57.99, '[]', NULL, 'VP-SUR-770', 'PN-6269', 25, 'assets/images/products/battery.webp', 0, '2026-03-17 18:42:36', 1, 0),
(48, 65, 3, 'Surface Laptop 5 Rear Housing - Premium Quality', 'surface-laptop-5-rear-housing---premium-quality', 'Premium grade Rear Housing replacement for Surface Laptop 5. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Laptop 5', 89.99, '[]', NULL, 'VP-SUR-853', 'PN-6426', 26, 'assets/images/products/housing.webp', 0, '2026-03-17 18:42:36', 1, 0),
(49, 66, 3, 'Surface Laptop 5 Camera Module - Premium Quality', 'surface-laptop-5-camera-module---premium-quality', 'Premium grade Camera Module replacement for Surface Laptop 5. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Laptop 5', 119.99, '[]', NULL, 'VP-SUR-525', 'PN-1120', 13, 'assets/images/products/camera.webp', 0, '2026-03-17 18:42:36', 1, 0),
(50, 67, 3, 'Surface Laptop 5 Charging Port - Premium Quality', 'surface-laptop-5-charging-port---premium-quality', 'Premium grade Charging Port replacement for Surface Laptop 5. High durability and perfect fit.', 'Ultra Premium', '12 Months', 'Surface Laptop 5', 37.99, '[]', NULL, 'VP-SUR-711', 'PN-8611', 42, 'assets/images/products/charging.webp', 0, '2026-03-17 18:42:36', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repair_services`
--

CREATE TABLE `repair_services` (
  `id` int(11) NOT NULL,
  `device_category_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 60,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `business_name` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `order_otp` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `reset_otp` varchar(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `role`, `business_name`, `is_verified`, `remember_token`, `token_expiry`, `order_otp`, `created_at`, `verification_otp`, `otp_expiry`, `reset_otp`) VALUES
(2, 'User', 'user@v.com', '$2y$10$tbDB40KsL6uNzZLe9uNLNeo2uDA61g3dIzQqMBcimBa1JX6sMs39O', NULL, 'user', 'Shop', 0, NULL, NULL, NULL, '2026-02-05 23:50:07', NULL, NULL, NULL),
(10, 'Syed Muhammad Ali Farhan Raza Zaidi', 'farhanraza772@gmail.com', '$2y$10$cWi8eRl/X28up/yOdReYA.P256W..LG.ftZvzssvqdVp97oZGDKny', NULL, 'user', 'MN Enterprises', 1, NULL, NULL, NULL, '2026-03-13 21:54:39', NULL, NULL, NULL),
(11, 'Ali Farhan', 'alifarhan1531@gmail.com', '$2y$10$oDJclIkCzzsqbxU2FrLmO.1yALaccYSXzLTrw.w1zFwvyhvTPGwB2', NULL, 'admin', NULL, 1, NULL, NULL, NULL, '2026-03-14 20:01:40', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `device_categories`
--
ALTER TABLE `device_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `device_subcategories`
--
ALTER TABLE `device_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`),
  ADD KEY `billing_address_id` (`billing_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `repair_services`
--
ALTER TABLE `repair_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `device_categories`
--
ALTER TABLE `device_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_subcategories`
--
ALTER TABLE `device_subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repair_services`
--
ALTER TABLE `repair_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`billing_address_id`) REFERENCES `addresses` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
