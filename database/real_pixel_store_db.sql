-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 26, 2025 at 06:13 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rps_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `unique_admin_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Admin1', '$2y$10$er/TcFc3zQlpUcGUcjcpcenJ9f8U5f.GyXGcrezhhm9n29g75LRcO', '2025-07-31 19:16:55', '2025-08-06 12:23:52'),
(6, 'Admin2', '$2y$10$Sz5DCjOqOAnmuvBVi/C0welqWfx/Tma/xcOkSqaUcta9.KUCYWe4m', '2025-08-06 09:36:29', '2025-08-06 12:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Computers and Laptops', '2025-08-06 10:05:09', '2025-08-08 09:46:23'),
(2, 'Audio', '2025-08-06 10:14:02', '2025-08-17 13:06:24'),
(5, 'Peripheral Devices and Accessories', '2025-08-06 12:21:26', '2025-08-08 09:45:50'),
(6, 'Components', '2025-08-06 12:21:33', '2025-08-06 12:21:33'),
(7, 'Video', '2025-08-06 12:21:45', '2025-08-06 12:21:45'),
(9, 'Mobile Devices', '2025-08-06 14:17:36', '2025-08-06 14:17:36');

-- --------------------------------------------------------

--
-- Table structure for table `category_images`
--

DROP TABLE IF EXISTS `category_images`;
CREATE TABLE IF NOT EXISTS `category_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `image_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`image_id`),
  KEY `fk_cat_img_image` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_subcategories`
--

DROP TABLE IF EXISTS `category_subcategories`;
CREATE TABLE IF NOT EXISTS `category_subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `subcategory_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`category_id`,`subcategory_id`),
  KEY `fk_cat_subcat_subcategory` (`subcategory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_subcategories`
--

INSERT INTO `category_subcategories` (`id`, `category_id`, `subcategory_id`) VALUES
(1, 1, 1),
(3, 1, 5),
(9, 2, 13),
(2, 5, 4),
(4, 5, 6),
(5, 5, 12),
(6, 6, 9),
(7, 7, 10),
(8, 7, 11),
(10, 9, 14),
(11, 9, 15);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `image_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `image_url`, `filename`, `alt_text`, `created_at`, `updated_at`) VALUES
(1, 'PRODUCTIMAGE_8025715fa59d129edf21.png', 'm_img.png', 'Razer DeathAdder V3 Pro - Product Image', '2025-08-25 14:52:43', '2025-08-25 14:52:43'),
(2, 'PRODUCTIMAGE_e9518cc408ce6889ff53.png', 'm_img2.png', 'Razer DeathAdder V3 Pro - Product Image', '2025-08-25 14:52:43', '2025-08-25 14:52:43'),
(3, 'PRODUCTIMAGE_9eecca1d252ce91d32be.png', 'sony2-removebg-preview.png', 'Sony WH-1000XM4 - Product Image', '2025-08-25 15:00:30', '2025-08-25 15:00:30');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` double(10,2) NOT NULL,
  `cover_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `cover_image`, `created_at`, `updated_at`) VALUES
(5, 'MSI Katana 15 B13V', 'MSI Katana 15 B13V Gaming Laptop with Intel Core i7-13620H, NVIDIA GeForce RTX 4060 8GB, 16GB DDR5 RAM, 1TB NVMe SSD, 15.6\" FHD 144Hz Display, Windows 11', 1299.99, 'COVERIMAGE_537187bacfbd91a69b95.png', '2025-08-06 11:30:31', '2025-08-18 06:27:41'),
(6, 'Logitech G502 HERO High Performance Gaming Mouse', 'Wired gaming mouse with HERO 25K sensor, customizable RGB lighting, 11 programmable buttons, adjustable weight system, and onboard memory for profiles. Built for precision and durability', 49.99, '', '2025-08-06 11:32:28', '2025-08-07 07:16:31'),
(8, 'Razer DeathAdder V2', 'Ergonomic gaming mouse with 20K DPI sensor', 69.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(9, 'Corsair Harpoon RGB', 'Lightweight wired gaming mouse with RGB lighting', 39.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(10, 'SteelSeries Rival 3', 'High-accuracy optical sensor mouse', 29.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(11, 'Dell XPS 13', 'Compact laptop with Intel i7 processor and 16GB RAM', 1199.99, 'COVERIMAGE_ce4e2b727419cee7a2d7.png', '2025-08-08 09:54:48', '2025-08-08 11:18:23'),
(12, 'HP Spectre x360', 'Convertible laptop with touchscreen and SSD', 1299.99, 'COVERIMAGE_c1a6647e41532879fc62.png', '2025-08-08 09:54:48', '2025-08-08 11:55:55'),
(13, 'Asus ZenBook 14', 'Slim laptop with AMD Ryzen 7 and long battery life', 999.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(14, 'Dell Inspiron Desktop', 'Affordable desktop with Intel i5 and 8GB RAM', 699.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(15, 'HP Pavilion Gaming Desktop', 'Gaming PC with AMD Ryzen 5 and GTX 1660', 899.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(16, 'Acer Aspire TC', 'Versatile desktop with Intel i7 and 16GB RAM', 799.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(17, 'ASUS VG248QG', '24-inch 165Hz gaming monitor with 1ms response time', 249.99, 'COVERIMAGE_5b67f9c2ad879bc17825.png', '2025-08-08 09:54:48', '2025-08-08 12:06:47'),
(18, 'LG 27UL500', '27-inch 4K UHD IPS monitor with HDR10 support', 349.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(19, 'Dell Ultrasharp U2419H', '24-inch IPS monitor with slim bezels', 299.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(20, 'Intel Core i7-12700K', '12th Gen Intel processor with 12 cores', 379.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(21, 'AMD Ryzen 7 5800X', '8-core AMD processor with high single-thread performance', 329.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(22, 'Intel Core i5-12600K', '10-core mid-range Intel CPU', 269.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(23, 'NVIDIA GeForce RTX 3070', 'High-performance graphics card for gaming and rendering', 499.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(24, 'AMD Radeon RX 6700 XT', 'Powerful GPU with 12GB VRAM', 479.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(25, 'NVIDIA GeForce GTX 1660 Super', 'Affordable 1080p gaming graphics card', 229.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(26, 'Corsair K70 RGB MK.2', 'Mechanical keyboard with Cherry MX switches and RGB', 159.99, 'COVERIMAGE_0d181a5fb8e80011daea.png', '2025-08-08 09:54:48', '2025-08-08 12:02:33'),
(27, 'Razer BlackWidow V3', 'Mechanical gaming keyboard with programmable keys', 139.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(28, 'Logitech G815', 'Low-profile mechanical keyboard with RGB lighting', 199.99, NULL, '2025-08-08 09:54:48', '2025-08-08 09:54:48'),
(29, 'Samsung Galaxy S21', 'Flagship smartphone with 6.2-inch display and 128GB storage', 799.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(30, 'Apple iPhone 13', 'Latest iPhone with A15 Bionic chip and dual cameras', 899.99, 'COVERIMAGE_1fbb3a84054e1faa0372.png', '2025-08-08 09:58:38', '2025-08-12 13:40:09'),
(31, 'Google Pixel 6', 'Google phone with Tensor chip and excellent camera', 599.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(32, 'Apple iPad Air', '10.9-inch tablet with A14 Bionic chip and Retina display', 599.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(33, 'Samsung Galaxy Tab S7', '11-inch Android tablet with S Pen support', 649.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(34, 'Amazon Fire HD 10', 'Affordable 10.1-inch tablet with Alexa integration', 149.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(35, 'Sony WH-1000XM4', 'Noise-cancelling over-ear headphones with long battery life', 349.99, 'COVERIMAGE_83d9ac7d45615ab5916e.png', '2025-08-08 09:58:38', '2025-08-08 11:56:47'),
(36, 'Bose QuietComfort 45', 'Comfortable wireless headphones with excellent noise cancellation', 329.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(37, 'Sennheiser HD 450BT', 'Wireless headphones with active noise cancellation', 199.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(38, 'SteelSeries QcK', 'High-quality cloth gaming mouse pad, 320x270mm', 14.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(39, 'Corsair MM300', 'Extended mouse pad with textile-weave surface', 29.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(40, 'Razer Goliathus', 'Speed edition gaming mouse pad with micro-textured surface', 19.99, NULL, '2025-08-08 09:58:38', '2025-08-08 09:58:38'),
(41, 'Lenovo ThinkPad X1 Carbon', '14-inch Ultrabook with high performance and lightweight design.', 1500.00, 'COVERIMAGE_b312e14727376cca277a.png', '2025-08-12 13:38:40', '2025-08-18 06:28:00'),
(42, 'Honor X50 Pro', 'Powerful smartphone with a 6.78-inch AMOLED display, 120Hz refresh rate, 256GB storage, and a versatile triple-camera setup for stunning photos and videos. Perfect for tech enthusiasts who demand speed and style.', 499.00, 'COVERIMAGE_c2186fe9b8a52aa995e0.jpg', '2025-08-18 06:20:22', '2025-08-18 06:20:22'),
(43, 'Razer DeathAdder V3 Pro', 'Wireless ergonomic gaming mouse with Focus Pro 30K sensor, 90-hour battery life, and HyperSpeed wireless technology', 149.99, 'COVERIMAGE_d0eba664a12e0e39afcb.png', '2025-08-25 14:52:43', '2025-08-25 14:52:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `image_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`image_id`),
  KEY `fk_prod_img_image` (`image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_id`) VALUES
(3, 35, 3),
(1, 43, 1),
(2, 43, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_subcategories`
--

DROP TABLE IF EXISTS `product_subcategories`;
CREATE TABLE IF NOT EXISTS `product_subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `subcategory_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`,`subcategory_id`),
  KEY `fk_prod_subcat_subcategory` (`subcategory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_subcategories`
--

INSERT INTO `product_subcategories` (`id`, `product_id`, `subcategory_id`) VALUES
(2, 5, 1),
(1, 6, 4),
(3, 8, 4),
(4, 9, 4),
(5, 10, 4),
(6, 11, 1),
(7, 12, 1),
(8, 13, 1),
(9, 14, 5),
(10, 15, 5),
(11, 16, 5),
(12, 17, 11),
(13, 18, 11),
(14, 19, 11),
(15, 20, 9),
(16, 21, 9),
(17, 22, 9),
(18, 23, 10),
(19, 24, 10),
(20, 25, 10),
(21, 26, 6),
(22, 27, 6),
(23, 28, 6),
(24, 29, 14),
(25, 30, 14),
(26, 31, 14),
(27, 32, 15),
(28, 33, 15),
(29, 34, 15),
(30, 35, 13),
(31, 36, 13),
(32, 37, 13),
(33, 38, 12),
(34, 39, 12),
(35, 40, 12),
(36, 41, 1),
(37, 42, 14),
(38, 43, 4);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Laptops', '2025-08-06 10:56:57', '2025-08-08 09:51:40'),
(4, 'Mice', '2025-08-06 12:20:52', '2025-08-08 09:51:41'),
(5, 'Computers', '2025-08-06 12:22:10', '2025-08-08 09:51:41'),
(6, 'Keyboards', '2025-08-06 12:22:21', '2025-08-08 09:51:41'),
(9, 'CPUs', '2025-08-06 14:18:09', '2025-08-06 14:18:09'),
(10, 'GPUs', '2025-08-06 14:18:15', '2025-08-06 14:18:20'),
(11, 'Monitors', '2025-08-07 06:00:28', '2025-08-07 06:00:28'),
(12, 'Mouse Pads', '2025-08-08 09:57:17', '2025-08-17 13:06:36'),
(13, 'Headphones', '2025-08-08 09:57:17', '2025-08-08 09:57:17'),
(14, 'Mobile Phones', '2025-08-08 09:57:17', '2025-08-08 09:57:17'),
(15, 'Tablets', '2025-08-08 09:57:17', '2025-08-08 09:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `subcategory_images`
--

DROP TABLE IF EXISTS `subcategory_images`;
CREATE TABLE IF NOT EXISTS `subcategory_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subcategory_id` int NOT NULL,
  `image_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subcategory_id` (`subcategory_id`,`image_id`),
  KEY `fk_subcat_img_image` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `unique_phone_number` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `dob`, `email`, `phone_number`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Taimour', 'Shmait', '2005-03-01', 'taimour@example.com', '70942690', '$2y$10$4ZgWIpvcG4dUXMe3pQYn9.0XyhWJNLw1pPQs.JnJAjzGUyRAC9b2m', '2025-08-01 17:01:14', '2025-08-01 17:01:14'),
(4, 'Aya', 'Jassem', '2025-08-06', 'aya@example.com', '80956789', '$2y$10$bKEHjdyrii4ll2lndvMi/u30c7SzPMcix05/HDX9XNYOdcbIQFbe.', '2025-08-06 06:08:22', '2025-08-06 15:10:00'),
(5, 'Rick', 'Grimes', '1976-01-01', 'rick@example.com', '34675120', '$2y$10$ZBjRG72ijgYn4J1xbcsBU.CpKVf2U2NGfISrjmVERy25EvWskGLge', '2025-08-06 06:11:42', '2025-08-06 06:11:42'),
(6, 'Jon', 'Snow', '1999-12-08', 'jon@example.com', '90545789', '$2y$10$5xWkkx6eEVotIW8modjcL.WBz/q.G85M/XVJJonyVibhipYnBd3hC', '2025-08-06 12:23:35', '2025-08-20 10:04:33'),
(7, 'Ramayana', 'El Deeb', '1966-09-03', 'ramayana@example.com', '56444349', '$2y$10$hTY.UqeD./Zs7cod4hFrh.dalDi017O1mkoBQRcUIsF9hEczPzb1W', '2025-08-06 14:19:48', '2025-08-06 14:19:48'),
(9, 'Fouad', 'El Kerdi', '1960-05-11', 'fouad@example.com', '67833899', '$2y$10$Sr3y22RybfuA7a4PLoEetOS1dVsv7vvB9dZ1Qpu7GScqETVyzxbdy', '2025-09-02 10:43:54', '2025-09-02 10:43:54');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category_images`
--
ALTER TABLE `category_images`
  ADD CONSTRAINT `fk_cat_img_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cat_img_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_subcategories`
--
ALTER TABLE `category_subcategories`
  ADD CONSTRAINT `fk_cat_subcat_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cat_subcat_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_prod_img_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prod_img_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_subcategories`
--
ALTER TABLE `product_subcategories`
  ADD CONSTRAINT `fk_prod_subcat_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prod_subcat_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategory_images`
--
ALTER TABLE `subcategory_images`
  ADD CONSTRAINT `fk_subcat_img_image` FOREIGN KEY (`image_id`) REFERENCES `images` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_subcat_img_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
