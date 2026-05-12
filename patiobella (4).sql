-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 05:03 PM
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
-- Database: `patiobella`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BEV-SODA', 'Soft Drinks', 'Coke, Fanta, Sprite, Pepsi, etc.', 1, 1, '2026-04-14 17:33:06', NULL),
(2, 'BEV-WATER', 'Water', 'Still and sparkling water', 2, 1, '2026-04-14 17:33:06', NULL),
(3, 'BEV-ENERGY', 'Energy Drinks', 'Red Bull, etc.', 3, 1, '2026-04-14 17:33:06', NULL),
(4, 'BEER', 'Beer', 'All beer brands', 4, 1, '2026-04-14 17:33:06', NULL),
(5, 'SPIRITS', 'Spirits', 'Vodka, Whisky, Gin, Rum, Tequila', 5, 1, '2026-04-14 17:33:06', NULL),
(6, 'WINE', 'Wine', 'Red, white, rose, sparkling', 6, 1, '2026-04-14 17:33:06', NULL),
(7, 'MIXERS', 'Mixers & Syrups', 'Tonic, soda water, syrups, purees', 7, 1, '2026-04-14 17:33:06', NULL),
(8, 'DAIRY', 'Dairy', 'Milk, cream, yoghurt, cheese, butter', 8, 1, '2026-04-14 17:33:06', NULL),
(9, 'MEAT', 'Meat & Poultry', 'Chicken, beef, pork, goat, fish', 9, 1, '2026-04-14 17:33:06', NULL),
(10, 'PRODUCE', 'Fresh Produce', 'Vegetables, fruits, herbs', 10, 1, '2026-04-14 17:33:06', NULL),
(11, 'DRY-GOODS', 'Dry Goods', 'Rice, flour, pasta, beans, sugar', 11, 1, '2026-04-14 17:33:06', NULL),
(12, 'OIL-FAT', 'Oils & Fats', 'Cooking oil, butter, ghee', 12, 1, '2026-04-14 17:33:06', NULL),
(13, 'SAUCES', 'Sauces & Condiments', 'Ketchup, mayo, soy sauce, vinegar', 13, 1, '2026-04-14 17:33:06', NULL),
(14, 'SPICES', 'Spices & Seasonings', 'Salt, pepper, paprika, curry, herbs', 14, 1, '2026-04-14 17:33:06', NULL),
(15, 'BAKERY', 'Bakery', 'Bread, buns, cakes, pastries', 15, 1, '2026-04-14 17:33:06', NULL),
(16, 'ICE-CREAM', 'Ice Cream', 'All ice cream flavors', 16, 1, '2026-04-14 17:33:06', NULL),
(17, 'CLEANING', 'Cleaning Supplies', 'Soap, disinfectant, garbage bags, gloves', 17, 1, '2026-04-14 17:33:06', NULL),
(18, 'PACKAGING', 'Packaging', 'Takeaway boxes, cups, lids, bags, foil', 18, 1, '2026-04-14 17:33:06', NULL),
(19, 'OFFICE', 'Office Supplies', 'Paper, pens, printer supplies', 19, 1, '2026-04-14 17:33:06', NULL),
(20, 'EQUIPMENT', 'Equipment & Utensils', 'Glasses, plates, kitchen tools', 20, 1, '2026-04-14 17:33:06', NULL),
(21, 'BEEF01', 'Beef', 'Cows meat', 0, 1, '2026-05-08 11:06:09', '2026-05-08 11:06:09'),
(22, 'SF001', 'Soft Drink', 'This category covers all types of sodas ie pepsi, fanta, coco-cola, etc', 0, 1, '2026-05-11 02:58:15', '2026-05-11 02:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `department_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `monthly_budget` decimal(15,2) DEFAULT NULL,
  `yearly_budget` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `code`, `name`, `Location`, `Description`, `department_type_id`, `manager_id`, `default_store_id`, `is_active`, `notes`, `monthly_budget`, `yearly_budget`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'DEP109FN', 'FINANCE DEPARTMENT', 'RM03', 'This is finance department', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-14 19:30:25', '2026-04-14 19:30:25', NULL, 1, NULL),
(2, 'DEP_PR001', 'PROCUREMENT', 'ROOM2 SECTION C', 'THis is department of procurement and suplies', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-14 19:32:23', '2026-04-14 19:32:23', NULL, 1, NULL),
(3, 'STR001', 'STORE', 'RMS12', 'This is store management department', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-14 22:28:45', '2026-04-14 22:44:28', NULL, 1, 1),
(4, 'BAR001', 'BAR', 'ROOM1 SECTION2', 'This is bar', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-17 03:58:55', '2026-04-17 03:58:55', NULL, 1, NULL),
(5, 'CAF001', 'CAFE', 'ROOM3', 'This is cafe', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-17 04:00:04', '2026-04-17 04:00:04', NULL, 1, NULL),
(6, 'GD001', 'GENERAL MANAGEMENT', 'ROOM3C', 'This is for general operations', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-28 08:14:53', '2026-04-28 09:00:06', NULL, 1, 1),
(7, 'DR001', 'DIRECTORS', 'ROOM2C', 'This is dirctors department', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-29 11:05:23', '2026-04-29 11:17:39', NULL, 1, 1),
(8, 'KIT001', 'KITCHEN', 'ROOM2C', 'This is kitchech', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-05-11 06:45:47', '2026-05-11 06:45:47', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_requisitions`
--

CREATE TABLE `department_requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_number` varchar(50) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Requesting department (Kitchen, Bar, Cafe)',
  `requested_by` bigint(20) UNSIGNED NOT NULL COMMENT 'User who created request',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Store manager who approved',
  `date_needed` date DEFAULT NULL,
  `status` enum('pending','approved','partially_issued','issued','partially_returned','returned','rejected','cancelled') DEFAULT 'pending',
  `store_notes` text DEFAULT NULL COMMENT 'Notes from store manager',
  `taken_by` varchar(255) DEFAULT NULL,
  `returned_by` varchar(255) DEFAULT NULL,
  `department_notes` text DEFAULT NULL COMMENT 'Notes from requesting department',
  `rejection_reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_requisitions`
--

INSERT INTO `department_requisitions` (`id`, `requisition_number`, `department_id`, `requested_by`, `approved_by`, `date_needed`, `status`, `store_notes`, `taken_by`, `returned_by`, `department_notes`, `rejection_reason`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'KIT-REQ-20260511-7208', 8, 8, 2, NULL, 'partially_returned', 'Taken by Ivan', 'Barigye', NULL, NULL, NULL, '2026-05-11 08:52:45', '2026-05-11 08:05:12', '2026-05-11 10:13:18', NULL),
(2, 'KIT-REQ-20260511-0390', 8, 8, 2, NULL, 'partially_returned', NULL, 'Barigye', 'James', NULL, NULL, '2026-05-11 11:25:04', '2026-05-11 11:22:23', '2026-05-11 14:12:29', NULL),
(3, 'KIT-REQ-20260511-3177', 8, 8, 2, '2026-05-11', 'partially_returned', NULL, 'Barigye', 'James', NULL, NULL, '2026-05-11 11:56:24', '2026-05-11 11:55:21', '2026-05-11 13:12:09', NULL),
(4, 'KIT-REQ-20260511-6256', 8, 8, 2, '2026-05-12', 'partially_issued', NULL, NULL, NULL, 'Please its urgent', NULL, '2026-05-11 14:09:23', '2026-05-11 14:05:03', '2026-05-11 14:10:04', NULL),
(5, 'KIT-REQ-20260512-0093', 8, 8, 2, '2026-05-12', 'partially_issued', NULL, NULL, NULL, 'we need thes items , today', NULL, '2026-05-12 06:38:33', '2026-05-12 06:34:31', '2026-05-12 06:39:31', NULL),
(6, 'KIT-REQ-20260512-1915', 8, 8, 2, '2026-05-12', 'issued', NULL, NULL, NULL, NULL, NULL, '2026-05-12 06:52:55', '2026-05-12 06:52:40', '2026-05-12 06:54:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_requisition_items`
--

CREATE TABLE `department_requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_requisition_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_requested` decimal(15,2) NOT NULL,
  `requested_pack_type` varchar(50) DEFAULT NULL COMMENT 'carton, crate, dozen, box',
  `requested_pack_size` int(11) DEFAULT NULL COMMENT 'pieces per pack (e.g., 24)',
  `quantity_issued` decimal(15,2) DEFAULT 0.00,
  `issued_pack_type` varchar(50) DEFAULT NULL,
  `issued_pack_size` int(11) DEFAULT NULL,
  `issued_total_pieces` decimal(15,2) DEFAULT 0.00,
  `quantity_returned` decimal(15,2) DEFAULT 0.00,
  `returned_pack_type` varchar(50) DEFAULT NULL,
  `returned_pack_size` int(11) DEFAULT NULL,
  `returned_total_pieces` decimal(15,2) DEFAULT 0.00,
  `return_reason` text DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `quantity_consumed` decimal(15,2) DEFAULT 0.00,
  `metrics` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_requisition_items`
--

INSERT INTO `department_requisition_items` (`id`, `department_requisition_id`, `inventory_item_id`, `quantity_requested`, `requested_pack_type`, `requested_pack_size`, `quantity_issued`, `issued_pack_type`, `issued_pack_size`, `issued_total_pieces`, `quantity_returned`, `returned_pack_type`, `returned_pack_size`, `returned_total_pieces`, `return_reason`, `returned_at`, `quantity_consumed`, `metrics`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 20.00, NULL, NULL, 20.00, NULL, NULL, 20.00, 10.00, NULL, NULL, 10.00, NULL, '2026-05-11 10:53:46', 10.00, 'kg', NULL, '2026-05-11 08:05:12', '2026-05-11 10:53:46'),
(2, 1, 21, 15.00, NULL, NULL, 15.00, NULL, NULL, 15.00, 5.00, NULL, NULL, 5.00, 'not used', '2026-05-11 10:13:18', 10.00, 'kg', NULL, '2026-05-11 08:05:12', '2026-05-11 10:13:18'),
(3, 2, 18, 10.00, 'dozen', 12, 10.00, NULL, 12, 10.00, 0.00, NULL, NULL, 3.00, NULL, '2026-05-11 14:12:29', 7.00, 'pcs', NULL, '2026-05-11 11:22:23', '2026-05-11 14:12:29'),
(4, 3, 38, 5.00, 'carton', 12, 5.00, 'carton', 12, 60.00, 0.00, NULL, NULL, 30.00, NULL, '2026-05-11 13:12:08', 30.00, 'bottles', NULL, '2026-05-11 11:55:21', '2026-05-11 13:12:08'),
(5, 4, 21, 20.00, NULL, NULL, 2.00, NULL, NULL, 2.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, 'kg', NULL, '2026-05-11 14:05:03', '2026-05-11 14:14:42'),
(6, 4, 7, 20.00, NULL, NULL, 4.00, NULL, NULL, 4.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, 'kg', NULL, '2026-05-11 14:05:03', '2026-05-11 14:14:42'),
(7, 5, 7, 20.00, 'pack', NULL, 20.00, NULL, NULL, 20.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, 'kg', 'i want 20 kgs of black beans', '2026-05-12 06:34:31', '2026-05-12 06:39:31'),
(8, 5, 1, 20.00, NULL, NULL, 15.00, NULL, NULL, 15.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, 'kg', NULL, '2026-05-12 06:34:31', '2026-05-12 06:39:31'),
(9, 6, 24, 20.00, 'bottle', NULL, 20.00, NULL, NULL, 20.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, 'bottles', NULL, '2026-05-12 06:52:40', '2026-05-12 06:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `department_types`
--

CREATE TABLE `department_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_types`
--

INSERT INTO `department_types` (`id`, `code`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'bar', 'Bar', 'Alcoholic and non-alcoholic beverages', 1, 1, '2026-04-14 17:33:00', NULL),
(2, 'cafe', 'Cafe', 'Coffee, tea, and light snacks', 2, 1, '2026-04-14 17:33:00', NULL),
(3, 'kitchen', 'Kitchen', 'Food preparation and cooking', 3, 1, '2026-04-14 17:33:00', NULL),
(4, 'pastry', 'Pastry', 'Bakery and dessert items', 4, 1, '2026-04-14 17:33:00', NULL),
(5, 'service', 'Service', 'Front of house and dining area', 5, 1, '2026-04-14 17:33:00', NULL),
(6, 'staff', 'Staff Kitchen', 'Staff meals and canteen', 6, 1, '2026-04-14 17:33:00', NULL),
(7, 'cleaning', 'Cleaning', 'Cleaning supplies and equipment', 7, 1, '2026-04-14 17:33:00', NULL),
(8, 'maintenance', 'Maintenance', 'Repair and maintenance items', 8, 1, '2026-04-14 17:33:00', NULL),
(9, 'office', 'Office', 'Administrative and office supplies', 9, 1, '2026-04-14 17:33:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_items`
--

CREATE TABLE `goods_received_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `goods_received_note_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_ordered` decimal(12,2) NOT NULL,
  `quantity_received` decimal(12,2) NOT NULL,
  `quantity_accepted` decimal(12,2) NOT NULL,
  `pack_type` varchar(50) DEFAULT NULL COMMENT 'e.g. carton, crate, box, dozen',
  `pack_size` int(11) DEFAULT NULL COMMENT 'How many base units inside one pack e.g. 24 bottles per carton',
  `number_of_packs` int(11) DEFAULT NULL COMMENT 'How many packs received e.g. 3 cartons',
  `quantity_in_base_unit` decimal(15,6) DEFAULT NULL COMMENT 'Total base units received = pack_size × number_of_packs',
  `base_unit` varchar(50) DEFAULT NULL COMMENT 'Base unit label at time of receipt e.g. bottle, piece',
  `quantity_rejected` decimal(12,2) NOT NULL DEFAULT 0.00,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `unit_cost` decimal(12,2) NOT NULL,
  `po_item_total_amount` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(12,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_received_items`
--

INSERT INTO `goods_received_items` (`id`, `goods_received_note_id`, `purchase_order_item_id`, `inventory_item_id`, `quantity_ordered`, `quantity_received`, `quantity_accepted`, `pack_type`, `pack_size`, `number_of_packs`, `quantity_in_base_unit`, `base_unit`, `quantity_rejected`, `rejection_reason`, `unit_cost`, `po_item_total_amount`, `total_cost`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 3, 51, 21, 20.00, 15.00, 15.00, NULL, NULL, NULL, 15.000000, 'bottle', 0.00, NULL, 10000.00, 200000.00, 150000.00, '5 not deleivered', 3, NULL, '2026-05-08 06:01:57', '2026-05-11 06:14:26'),
(2, 3, 52, 7, 30.00, 25.00, 25.00, NULL, NULL, NULL, 25.000000, 'bottle', 0.00, NULL, 10000.00, 300000.00, 250000.00, '5 not delivered', 3, NULL, '2026-05-08 06:01:57', '2026-05-11 06:14:26'),
(3, 4, 53, 7, 50.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 4000.00, 200000.00, 0.00, NULL, 3, NULL, '2026-05-11 06:23:19', '2026-05-11 06:23:19'),
(4, 5, 54, 7, 30.00, 30.00, 30.00, NULL, NULL, NULL, 30.000000, 'kg', 0.00, NULL, 3500.00, 105000.00, 105000.00, NULL, 3, NULL, '2026-05-11 06:29:59', '2026-05-11 06:30:42'),
(5, 5, 55, 1, 50.00, 20.00, 20.00, NULL, NULL, NULL, 20.000000, 'kg', 0.00, NULL, 350.00, 17500.00, 7000.00, NULL, 3, NULL, '2026-05-11 06:29:59', '2026-05-11 06:30:42'),
(6, 6, 56, 1, 50.00, 45.00, 45.00, NULL, NULL, NULL, 45.000000, 'bottle', 0.00, NULL, 4000.00, 200000.00, 180000.00, NULL, 3, NULL, '2026-05-11 13:59:46', '2026-05-11 14:01:06'),
(7, 6, 57, 23, 20.00, 20.00, 20.00, NULL, NULL, NULL, 20.000000, 'bottle', 0.00, NULL, 10000.00, 200000.00, 200000.00, NULL, 3, NULL, '2026-05-11 13:59:46', '2026-05-11 14:01:06'),
(8, 7, 58, 24, 40.00, 40.00, 40.00, 'carton', 12, 40, 480.000000, 'bottle', 0.00, NULL, 9999.99, 399999.60, 399999.60, NULL, 3, 2, '2026-05-12 03:58:27', '2026-05-12 04:04:07'),
(9, 7, 59, 9, 30.00, 25.00, 25.00, NULL, NULL, NULL, 25.000000, 'litre', 0.00, NULL, 25000.00, 750000.00, 625000.00, NULL, 3, 2, '2026-05-12 03:58:27', '2026-05-12 04:04:07'),
(10, 7, 60, 28, 20.00, 20.00, 20.00, 'crate', 12, 20, 240.000000, 'piece', 0.00, NULL, 49999.99, 999999.80, 999999.80, NULL, 3, 2, '2026-05-12 03:58:27', '2026-05-12 04:04:07'),
(12, 9, 61, 42, 30.00, 30.00, 30.00, 'box', 12, 30, 360.000000, 'bottle', 0.00, NULL, 20000.00, 600000.00, 600000.00, '5 not deleivered', 3, 2, '2026-05-12 05:27:02', '2026-05-12 05:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_notes`
--

CREATE TABLE `goods_received_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grn_number` varchar(255) NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `received_date` date NOT NULL,
  `delivery_note_number` varchar(255) DEFAULT NULL,
  `po_total_amount` decimal(15,2) DEFAULT 0.00,
  `grn_total_amount` decimal(15,2) DEFAULT 0.00,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('draft','completed','cancelled','inventory_updated') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goods_received_notes`
--

INSERT INTO `goods_received_notes` (`id`, `grn_number`, `purchase_order_id`, `vendor_id`, `received_date`, `delivery_note_number`, `po_total_amount`, `grn_total_amount`, `subtotal`, `tax_amount`, `notes`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'GRN-20260508-3441', 24, 1, '2026-05-08', NULL, 500000.00, 400000.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, NULL, '2026-05-08 06:01:57', '2026-05-11 06:14:26', NULL),
(4, 'GRN-20260511-0696', 25, 1, '2026-05-11', NULL, 200000.00, 0.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'completed', 3, NULL, '2026-05-11 06:23:19', '2026-05-11 06:23:19', NULL),
(5, 'GRN-20260511-8836', 26, 1, '2026-05-11', NULL, 122500.00, 112000.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, NULL, '2026-05-11 06:29:59', '2026-05-11 06:30:42', NULL),
(6, 'GRN-20260511-2585', 27, 1, '2026-05-11', NULL, 400000.00, 380000.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, NULL, '2026-05-11 13:59:46', '2026-05-11 14:01:06', NULL),
(7, 'GRN-20260512-7996', 28, 1, '2026-05-12', NULL, 2149999.40, 2024999.40, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, 2, '2026-05-12 03:58:27', '2026-05-12 04:04:07', NULL),
(9, 'GRN-20260512-6437', 29, 2, '2026-05-12', NULL, 600000.00, 600000.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, 2, '2026-05-12 05:27:02', '2026-05-12 05:28:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `item_code` varchar(255) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `default_unit_of_measure_id` varchar(50) DEFAULT NULL,
  `base_unit` varchar(50) NOT NULL DEFAULT 'pcs' COMMENT 'The smallest selling/consumption unit e.g. bottle, piece, kg',
  `minimum_stock` decimal(15,6) DEFAULT 0.000000,
  `maximum_stock` decimal(15,6) DEFAULT 0.000000,
  `reorder_quantity` decimal(15,6) DEFAULT 0.000000,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `last_purchase_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `is_perishable` tinyint(1) DEFAULT 0,
  `is_taxable` tinyint(1) DEFAULT 1,
  `shelf_life_days` int(11) DEFAULT NULL,
  `storage_conditions` varchar(255) DEFAULT NULL,
  `manufacturer` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `current_stock` decimal(15,6) DEFAULT 0.000000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`id`, `created_at`, `updated_at`, `is_active`, `deleted_at`, `item_code`, `barcode`, `name`, `description`, `category_id`, `sub_category_id`, `default_unit_of_measure_id`, `base_unit`, `minimum_stock`, `maximum_stock`, `reorder_quantity`, `unit_cost`, `last_purchase_price`, `selling_price`, `is_perishable`, `is_taxable`, `shelf_life_days`, `storage_conditions`, `manufacturer`, `brand`, `notes`, `created_by`, `updated_by`, `current_stock`) VALUES
(1, '2026-04-16 13:06:21', '2026-05-12 06:39:31', 1, NULL, 'RICE-001', '8901234567890', 'Basmati Rice', 'Premium long grain basmati rice', 11, NULL, NULL, 'pcs', 50.000000, 500.000000, 100.000000, 4000.00, 85.00, 120.00, 0, 1, 365, 'Cool dry place', 'India Gate', 'India Gate', 'Best for biryani', 1, NULL, 255.000000),
(2, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'RICE-002', '8901234567891', 'Super Rice', 'Local super quality rice', 11, NULL, NULL, 'pcs', 100.000000, 1000.000000, 200.000000, 65.00, 65.00, 90.00, 0, 1, 365, 'Cool dry place', 'Tilda', 'Tilda', 'Daily use', 1, NULL, 500.000000),
(3, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'FLOUR-001', '8901234567892', 'Wheat Flour', 'Fine wheat flour for chapati and bread', 11, NULL, NULL, 'pcs', 30.000000, 300.000000, 50.000000, 45.00, 45.00, 65.00, 0, 1, 180, 'Cool dry place', 'Pembe', 'Pembe', 'All purpose flour', 1, NULL, 150.000000),
(4, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'FLOUR-002', '8901234567893', 'Maize Flour', 'Fine maize flour for ugali/posho', 11, NULL, NULL, 'pcs', 40.000000, 400.000000, 80.000000, 40.00, 40.00, 60.00, 0, 1, 180, 'Cool dry place', 'Jogoo', 'Jogoo', 'Local favorite', 1, NULL, 200.000000),
(5, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SUGAR-001', '8901234567894', 'White Sugar', 'Fine granulated white sugar', 11, NULL, NULL, 'pcs', 50.000000, 500.000000, 100.000000, 55.00, 55.00, 80.00, 0, 1, 365, 'Cool dry place', 'Kinyara', 'Kinyara', 'Standard sugar', 1, NULL, 300.000000),
(6, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'BEANS-001', '8901234567895', 'Kidney Beans', 'Red kidney beans', 11, NULL, NULL, 'pcs', 30.000000, 300.000000, 60.000000, 45.00, 45.00, 70.00, 0, 1, 365, 'Cool dry place', 'Local', 'Local', 'Dried beans', 1, NULL, 120.000000),
(7, '2026-04-16 13:06:21', '2026-05-12 06:39:31', 1, NULL, 'BEANS-002', '8901234567896', 'Black Beans', 'Black turtle beans', 11, NULL, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 3500.00, 50.00, 75.00, 0, 1, 365, 'Cool dry place', 'Local', 'Local', 'Dried black beans', 1, NULL, 95.000000),
(8, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'PASTA-001', '8901234567897', 'Spaghetti', 'Italian spaghetti pasta', 11, NULL, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 35.00, 35.00, 55.00, 0, 1, 365, 'Cool dry place', 'Barilla', 'Barilla', 'No.5 pasta', 1, NULL, 100.000000),
(9, '2026-04-16 13:06:21', '2026-05-12 09:23:36', 1, NULL, 'OIL-001', '8901234567898', 'Cooking Oil', 'Vegetable cooking oil', 12, NULL, 'pcs', 'litre', 40.000000, 400.000000, 60.000000, 25000.00, 25000.00, 180.00, 0, 1, 365, 'Cool dark place', 'Mukwano', 'Mukwano', 'Sunflower oil', 1, 2, 185.000000),
(10, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'OIL-002', '8901234567899', 'Olive Oil', 'Extra virgin olive oil', 12, NULL, NULL, 'pcs', 10.000000, 100.000000, 20.000000, 350.00, 350.00, 500.00, 0, 1, 365, 'Cool dark place', 'Bertolli', 'Bertolli', 'Premium quality', 1, NULL, 50.000000),
(11, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SAUCE-001', '8901234567900', 'Tomato Sauce', 'Tomato ketchup', 13, NULL, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 25.00, 25.00, 40.00, 0, 1, 180, 'Cool place', 'Heinz', 'Heinz', 'Tomato ketchup', 1, NULL, 150.000000),
(12, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SAUCE-002', '8901234567901', 'Soy Sauce', 'Dark soy sauce', 13, NULL, NULL, 'pcs', 10.000000, 100.000000, 20.000000, 30.00, 30.00, 50.00, 0, 1, 365, 'Cool place', 'Kikkoman', 'Kikkoman', 'Japanese soy sauce', 1, NULL, 60.000000),
(13, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SALT-001', '8901234567902', 'Table Salt', 'Iodized table salt', 14, NULL, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 5.00, 5.00, 10.00, 0, 1, 730, 'Cool dry place', 'Unga', 'Unga', 'Fine salt', 1, NULL, 100.000000),
(14, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SPICES-001', '8901234567903', 'Black Pepper', 'Whole black pepper corns', 14, NULL, NULL, 'pcs', 5.000000, 50.000000, 10.000000, 80.00, 80.00, 120.00, 0, 1, 365, 'Cool dry place', 'East African', 'East African', 'Premium', 1, NULL, 30.000000),
(15, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SPICES-002', '8901234567904', 'Paprika', 'Ground paprika powder', 14, NULL, NULL, 'pcs', 5.000000, 50.000000, 10.000000, 65.00, 65.00, 100.00, 0, 1, 365, 'Cool dry place', 'East African', 'East African', 'Sweet paprika', 1, NULL, 25.000000),
(16, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'MILK-001', '8901234567905', 'UHT Milk', 'Long life whole milk', 8, NULL, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 35.00, 35.00, 55.00, 1, 1, 90, 'Cool place', 'Brookside', 'Brookside', '1 liter pack', 1, NULL, 80.000000),
(17, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'BUTTER-001', '8901234567906', 'Salted Butter', 'Pure salted butter', 8, NULL, NULL, 'pcs', 10.000000, 100.000000, 20.000000, 45.00, 45.00, 70.00, 1, 1, 60, 'Refrigerated', 'Blue Band', 'Blue Band', '250g pack', 1, NULL, 50.000000),
(18, '2026-04-16 13:06:21', '2026-05-11 14:12:29', 1, NULL, 'EGGS-001', '8901234567907', 'Chicken Eggs', 'Fresh grade A eggs', 8, NULL, NULL, 'pcs', 30.000000, 300.000000, 60.000000, 5.00, 5.00, 8.00, 1, 0, 21, 'Refrigerated', 'Local', 'Local', 'Per egg', 1, NULL, 193.000000),
(19, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'CHICKEN-001', '8901234567908', 'Chicken Breast', 'Boneless chicken breast', 9, 11, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 120.00, 120.00, 180.00, 1, 1, 7, 'Frozen', 'Local', 'Local', 'Per kg', 1, NULL, 60.000000),
(20, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'CHICKEN-002', '8901234567909', 'Chicken Thighs', 'Bone-in chicken thighs', 9, 11, NULL, 'pcs', 15.000000, 150.000000, 30.000000, 100.00, 100.00, 150.00, 1, 1, 7, 'Frozen', 'Local', 'Local', 'Per kg', 1, NULL, 50.000000),
(21, '2026-04-16 13:06:21', '2026-05-11 14:14:42', 1, NULL, 'BEEF-001', '8901234567910', 'Beef Mince', 'Ground beef mince', 9, 12, NULL, 'pcs', 15.000000, 150.000000, 30.000000, 10000.00, 150.00, 220.00, 1, 1, 5, 'Frozen', 'Local', 'Local', 'Per kg', 1, NULL, 25.000000),
(22, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'BEEF-002', '8901234567911', 'Beef Steak', 'Tender beef steak cuts', 9, 12, NULL, 'pcs', 10.000000, 100.000000, 20.000000, 250.00, 250.00, 350.00, 1, 1, 5, 'Frozen', 'Local', 'Local', 'Premium cuts', 1, NULL, 30.000000),
(23, '2026-04-16 13:06:21', '2026-05-11 14:01:06', 1, NULL, 'COLA-001', '8901234567912', 'Coca Cola', 'Original taste Coca Cola', 1, 1, NULL, 'pcs', 50.000000, 500.000000, 100.000000, 10000.00, 15.00, 25.00, 0, 1, 180, 'Cool place', 'Coca Cola', 'Coca Cola', '330ml can', 1, NULL, 320.000000),
(24, '2026-04-16 13:06:21', '2026-05-12 06:54:49', 1, NULL, 'FANTA-001', '8901234567913', 'Fanta Orange', 'Orange flavored soda', 1, 2, 'carton', 'bottle', 40.000000, 400.000000, 80.000000, 9999.99, 9999.99, 25.00, 0, 1, 180, 'Cool place', 'Coca Cola', 'Fanta', '330ml can', 1, 2, 710.000000),
(25, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SPRITE-001', '8901234567914', 'Sprite', 'Lemon-lime flavored soda', 1, 3, NULL, 'pcs', 40.000000, 400.000000, 80.000000, 15.00, 15.00, 25.00, 0, 1, 180, 'Cool place', 'Coca Cola', 'Sprite', '330ml can', 1, NULL, 250.000000),
(26, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WATER-001', '8901234567915', 'Mineral Water', 'Still mineral water', 2, NULL, NULL, 'pcs', 100.000000, 1000.000000, 200.000000, 10.00, 10.00, 20.00, 0, 1, 365, 'Cool place', 'Rwenzori', 'Rwenzori', '500ml bottle', 1, NULL, 500.000000),
(27, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WATER-002', '8901234567916', 'Sparkling Water', 'Carbonated mineral water', 2, NULL, NULL, 'pcs', 30.000000, 300.000000, 60.000000, 15.00, 15.00, 30.00, 0, 1, 365, 'Cool place', 'Rwenzori', 'Rwenzori', '500ml bottle', 1, NULL, 150.000000),
(28, '2026-04-16 13:06:21', '2026-05-12 04:04:07', 1, NULL, 'BEER-001', '8901234567917', 'Club Beer', 'Premium lager beer', 4, NULL, 'crate', 'piece', 50.000000, 500.000000, 100.000000, 49999.99, 49999.99, 35.00, 0, 1, 180, 'Cool place', 'Uganda Breweries', 'Club', '500ml bottle', 1, 2, 440.000000),
(29, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'BEER-002', '8901234567918', 'Tusker Lager', 'Premium lager beer', 4, NULL, NULL, 'pcs', 50.000000, 500.000000, 100.000000, 22.00, 22.00, 38.00, 0, 1, 180, 'Cool place', 'EABL', 'Tusker', '500ml bottle', 1, NULL, 180.000000),
(30, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'VODKA-001', '8901234567919', 'Smirnoff Vodka', 'Premium vodka', 5, 5, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 150.00, 150.00, 250.00, 0, 1, 365, 'Cool place', 'Diageo', 'Smirnoff', '750ml bottle', 1, NULL, 80.000000),
(31, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WHISKY-001', '8901234567920', 'Johnnie Walker', 'Black label whisky', 5, 6, NULL, 'pcs', 15.000000, 150.000000, 30.000000, 300.00, 300.00, 500.00, 0, 1, 365, 'Cool place', 'Diageo', 'Johnnie Walker', '750ml bottle', 1, NULL, 40.000000),
(32, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'GIN-001', '8901234567921', 'Bombay Sapphire', 'Premium gin', 5, 7, NULL, 'pcs', 15.000000, 150.000000, 30.000000, 280.00, 280.00, 450.00, 0, 1, 365, 'Cool place', 'Bombay', 'Bombay Sapphire', '750ml bottle', 1, NULL, 35.000000),
(33, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'RUM-001', '8901234567922', 'Bacardi Rum', 'White rum', 5, 8, NULL, 'pcs', 20.000000, 200.000000, 40.000000, 180.00, 180.00, 300.00, 0, 1, 365, 'Cool place', 'Bacardi', 'Bacardi', '750ml bottle', 1, NULL, 60.000000),
(37, '2026-05-08 12:11:38', '2026-05-08 12:11:38', 1, NULL, 'ITEM-69FDFD2A3AEE3', NULL, 'Rockboom 300mls', NULL, 3, NULL, 'pieces', 'pcs', 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, 'the following items were added to stock', 2, NULL, 220.000000),
(38, '2026-05-11 03:05:08', '2026-05-11 13:12:09', 1, NULL, 'ITEM-6A0171945AC3D', NULL, 'Mountain dew', NULL, 1, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 206.000000),
(39, '2026-05-11 03:14:55', '2026-05-11 03:14:55', 1, NULL, 'ITEM-6A0173DF19192', NULL, 'Cow meat', NULL, 21, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, 'expiring on 15/05/2026', 2, NULL, 99.990000),
(40, '2026-05-11 06:35:10', '2026-05-11 06:35:10', 1, NULL, 'ITEM-6A01A2CEABFFE', NULL, 'Sting', NULL, 3, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 120.000000),
(41, '2026-05-12 03:27:21', '2026-05-12 03:27:21', 1, NULL, 'ITEM-6A02C849E805F', NULL, 'Rockboom 300mls', NULL, 3, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 180.000000),
(42, '2026-05-12 05:13:56', '2026-05-12 05:28:15', 1, NULL, 'ITEM-6A02E1447F436', NULL, 'Rwenzori mineral water 300mls', NULL, 2, NULL, 'box', 'bottle', 0.000000, 0.000000, 0.000000, 20000.00, 20000.00, 0.00, 0, 1, NULL, NULL, NULL, NULL, 'We received 20 boxes of Rwenzori mineral water of 300mls', 2, 2, 580.000000);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_update_notes`
--

CREATE TABLE `inventory_update_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `note_number` varchar(50) NOT NULL,
  `entry_type` enum('manual','grn') NOT NULL,
  `grn_id` bigint(20) UNSIGNED DEFAULT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_update_note_items`
--

CREATE TABLE `inventory_update_note_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_update_note_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `receiving_unit` varchar(50) NOT NULL,
  `pack_size` int(11) DEFAULT NULL,
  `number_of_packs` int(11) DEFAULT NULL,
  `base_unit` varchar(50) NOT NULL,
  `quantity_in_base_unit` decimal(15,6) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_units`
--

CREATE TABLE `item_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `unit_of_measure_id` bigint(20) UNSIGNED NOT NULL,
  `is_base_unit` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Smallest unit, e.g., piece, bottle, kg',
  `quantity_in_base_unit` decimal(15,6) NOT NULL DEFAULT 1.000000 COMMENT 'e.g., 1 crate = 24 pieces, so quantity = 24',
  `last_purchase_price` decimal(15,2) DEFAULT NULL COMMENT 'Last price paid for this unit',
  `average_purchase_price` decimal(15,2) DEFAULT NULL COMMENT 'Weighted average cost for this unit',
  `selling_price` decimal(15,2) DEFAULT NULL COMMENT 'Menu/sales price for this unit',
  `barcode` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lpos`
--

CREATE TABLE `lpos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lpo_number` varchar(255) NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `lpo_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_instructions` varchar(255) DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending_director','director_approved','director_rejected','converted_to_epo') NOT NULL DEFAULT 'pending_director',
  `external_po_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `director_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lpos`
--

INSERT INTO `lpos` (`id`, `lpo_number`, `requisition_id`, `vendor_id`, `created_by`, `approved_by`, `lpo_date`, `expected_delivery_date`, `delivery_address`, `delivery_instructions`, `subtotal`, `tax_amount`, `total_amount`, `status`, `external_po_id`, `notes`, `director_notes`, `rejection_reason`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'LPO-20260429-5939', 4, 1, 3, 7, '2026-04-29', NULL, 'Arena Mall, Middle east Resturant East wing', NULL, 2149999.40, 0.00, 2149999.40, 'converted_to_epo', 28, NULL, NULL, NULL, '2026-05-05 04:38:16', '2026-04-29 10:32:30', '2026-05-12 03:56:45', NULL),
(3, 'LPO-20260506-3660', 8, 1, 3, 7, '2026-05-06', '2026-05-08', 'main offices', NULL, 500000.00, 0.00, 500000.00, 'converted_to_epo', 24, 'This LPO is drafted based on the requistion approved by General manager', 'please increase quantity by 3 @ each item', NULL, '2026-05-06 20:13:04', '2026-05-06 19:04:55', '2026-05-08 04:44:53', NULL),
(4, 'LPO-20260506-5347', 9, 2, 3, 7, '2026-05-06', '2026-05-10', NULL, NULL, 500000.00, 0.00, 500000.00, 'director_approved', NULL, NULL, NULL, NULL, '2026-05-06 23:46:34', '2026-05-06 23:44:47', '2026-05-06 23:46:34', NULL),
(5, 'LPO-20260511-5492', 10, 1, 3, 7, '2026-05-11', '2026-05-12', NULL, NULL, 200000.00, 0.00, 200000.00, 'converted_to_epo', 25, NULL, NULL, NULL, '2026-05-11 06:21:27', '2026-05-11 06:21:04', '2026-05-11 06:21:47', NULL),
(6, 'LPO-20260511-2596', 11, 1, 3, 7, '2026-05-11', '2026-05-13', NULL, NULL, 122500.00, 0.00, 122500.00, 'converted_to_epo', 26, NULL, NULL, NULL, '2026-05-11 06:28:32', '2026-05-11 06:28:19', '2026-05-11 06:28:57', NULL),
(7, 'LPO-20260511-7501', 12, 1, 3, 7, '2026-05-11', '2026-05-13', NULL, NULL, 400000.00, 0.00, 400000.00, 'converted_to_epo', 27, NULL, NULL, NULL, '2026-05-11 13:56:42', '2026-05-11 13:56:03', '2026-05-11 13:57:22', NULL),
(8, 'LPO-20260512-2558', 13, 2, 3, 7, '2026-05-12', '2026-05-14', 'main offices', NULL, 600000.00, 0.00, 600000.00, 'converted_to_epo', 29, NULL, 'please purchase these items as soon as possible', NULL, '2026-05-12 05:19:14', '2026-05-12 05:18:19', '2026-05-12 05:19:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lpo_items`
--

CREATE TABLE `lpo_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lpo_id` bigint(20) UNSIGNED NOT NULL,
  `requisition_item_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_approved` decimal(12,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `metrics` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lpo_items`
--

INSERT INTO `lpo_items` (`id`, `lpo_id`, `requisition_item_id`, `inventory_item_id`, `quantity_approved`, `unit_cost`, `total_cost`, `metrics`, `notes`, `created_at`, `updated_at`) VALUES
(4, 2, 36, 24, 40.00, 9999.99, 399999.60, 'cartons', NULL, '2026-04-29 10:32:30', '2026-04-29 10:32:30'),
(5, 2, 37, 9, 30.00, 25000.00, 750000.00, 'cartons', NULL, '2026-04-29 10:32:30', '2026-04-29 10:32:30'),
(6, 2, 38, 28, 20.00, 49999.99, 999999.80, 'sets', NULL, '2026-04-29 10:32:30', '2026-04-29 10:32:30'),
(7, 3, 43, 21, 20.00, 10000.00, 200000.00, 'kg', NULL, '2026-05-06 19:04:55', '2026-05-06 19:04:55'),
(8, 3, 44, 7, 30.00, 10000.00, 300000.00, 'kg', NULL, '2026-05-06 19:04:55', '2026-05-06 19:04:55'),
(9, 4, 49, 26, 30.00, 10000.00, 300000.00, 'boxes', NULL, '2026-05-06 23:44:47', '2026-05-06 23:44:47'),
(10, 4, 50, 26, 20.00, 10000.00, 200000.00, 'boxes', NULL, '2026-05-06 23:44:47', '2026-05-06 23:44:47'),
(11, 5, 51, 7, 50.00, 4000.00, 200000.00, 'kg', NULL, '2026-05-11 06:21:04', '2026-05-11 06:21:04'),
(12, 6, 52, 7, 30.00, 3500.00, 105000.00, 'kg', NULL, '2026-05-11 06:28:19', '2026-05-11 06:28:19'),
(13, 6, 53, 1, 50.00, 350.00, 17500.00, 'kg', NULL, '2026-05-11 06:28:19', '2026-05-11 06:28:19'),
(14, 7, 54, 1, 50.00, 4000.00, 200000.00, 'kg', NULL, '2026-05-11 13:56:03', '2026-05-11 13:56:03'),
(15, 7, 55, 23, 20.00, 10000.00, 200000.00, 'cartons', NULL, '2026-05-11 13:56:03', '2026-05-11 13:56:03'),
(16, 8, 56, 42, 30.00, 20000.00, 600000.00, 'boxes', NULL, '2026-05-12 05:18:19', '2026-05-12 05:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_14_120027_create_vendors_table', 1),
(5, '2026_04_14_120808_create_stores_table', 1),
(6, '2026_04_14_121319_create_departments_table', 1),
(7, '2026_04_14_121918_create_categories_table', 1),
(8, '2026_04_14_123417_create_units_of_measure_table', 1),
(9, '2026_04_14_124518_create_inventory_items_table', 1),
(10, '2026_04_14_124648_create_item_units_table', 1),
(11, '2026_04_14_124753_create_tock_movements_table', 1),
(12, '2026_04_14_131146_create_purchase_orders_table', 1),
(13, '2026_04_14_131241_create_purchase_order_items_table', 1),
(14, '2026_04_14_131339_create_goods_received_notes_table', 1),
(15, '2026_04_14_131524_create_goods_received_note_items_table', 1),
(16, '2026_04_14_142038_create_add_is_super_admin_to_users_table', 1),
(17, '2026_04_14_151340_add_soft_deletes_to_users_table', 1),
(18, '2026_04_14_201644_create_roles_table', 1),
(19, '2026_04_16_110542_create_requisitions_table', 2),
(20, '2026_04_16_110631_create_requisition_items_table', 2),
(21, '2026_04_16_113133_add_is_active_to_inventory_items_table', 3),
(22, '2026_04_16_124022_add_item_name_to_requisition_items_table', 4),
(23, '2026_04_16_124611_make_inventory_item_id_nullable_in_requisition_items', 5),
(24, '2026_04_16_170659_add_rejection_reason_to_requisitions_table', 6),
(25, '2026_04_27_071306_create_goods_received_notes_table', 7),
(26, '2026_04_29_071956_add_metrics_to_requisition_items_table', 8),
(27, '2026_04_29_125113_create_lpos_table', 9),
(28, '2026_04_29_125244_create_lpo_items_table', 9),
(29, '2026_05_11_141826_create_add_taken_by_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`, `group`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'view_users', 'View Users', 'users', NULL, 1, 1, '2026-04-14 17:34:15', '2026-04-14 17:34:15', NULL),
(2, 'create_users', 'Create Users', 'users', NULL, 2, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(3, 'edit_users', 'Edit Users', 'users', NULL, 3, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(4, 'delete_users', 'Delete Users', 'users', NULL, 4, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(5, 'assign_roles', 'Assign Roles to Users', 'users', NULL, 5, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(6, 'view_roles', 'View Roles', 'roles', NULL, 6, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(7, 'create_roles', 'Create Roles', 'roles', NULL, 7, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(8, 'edit_roles', 'Edit Roles', 'roles', NULL, 8, 1, '2026-04-14 17:34:16', '2026-04-14 17:34:16', NULL),
(9, 'delete_roles', 'Delete Roles', 'roles', NULL, 9, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(10, 'view_permissions', 'View Permissions', 'permissions', NULL, 10, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(11, 'create_permissions', 'Create Permissions', 'permissions', NULL, 11, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(12, 'edit_permissions', 'Edit Permissions', 'permissions', NULL, 12, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(13, 'delete_permissions', 'Delete Permissions', 'permissions', NULL, 13, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(14, 'view_inventory', 'View Inventory', 'inventory', NULL, 20, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(15, 'create_inventory', 'Add Inventory Items', 'inventory', NULL, 21, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(16, 'edit_inventory', 'Edit Inventory Items', 'inventory', NULL, 22, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(17, 'delete_inventory', 'Delete Inventory Items', 'inventory', NULL, 23, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(18, 'adjust_inventory', 'Adjust Stock (Counts)', 'inventory', NULL, 24, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(19, 'transfer_inventory', 'Transfer Stock Between Stores', 'inventory', NULL, 25, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(20, 'view_purchase_orders', 'View Purchase Orders', 'purchasing', NULL, 30, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(21, 'create_purchase_orders', 'Create Purchase Orders', 'purchasing', NULL, 31, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(22, 'edit_purchase_orders', 'Edit Purchase Orders', 'purchasing', NULL, 32, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(23, 'approve_purchase_orders', 'Approve Purchase Orders', 'purchasing', NULL, 33, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(24, 'receive_goods', 'Receive Goods (GRN)', 'purchasing', NULL, 34, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(25, 'view_vendors', 'View Vendors', 'vendors', NULL, 40, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(26, 'create_vendors', 'Create Vendors', 'vendors', NULL, 41, 1, '2026-04-14 17:34:17', '2026-04-14 17:34:17', NULL),
(27, 'edit_vendors', 'Edit Vendors', 'vendors', NULL, 42, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(28, 'delete_vendors', 'Delete Vendors', 'vendors', NULL, 43, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(29, 'view_departments', 'View Departments', 'departments', NULL, 50, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(30, 'manage_departments', 'Manage Departments (CRUD)', 'departments', NULL, 51, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(31, 'view_stores', 'View Stores', 'stores', NULL, 52, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(32, 'manage_stores', 'Manage Stores (CRUD)', 'stores', NULL, 53, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(33, 'view_categories', 'View Categories', 'categories', NULL, 60, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(34, 'manage_categories', 'Manage Categories (CRUD)', 'categories', NULL, 61, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(35, 'view_reports', 'View Reports Dashboard', 'reports', NULL, 70, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(36, 'view_stock_reports', 'View Stock Reports', 'reports', NULL, 71, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(37, 'view_financial_reports', 'View Financial Reports', 'reports', NULL, 72, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(38, 'export_reports', 'Export Reports', 'reports', NULL, 73, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(39, 'access_pos', 'Access POS System', 'pos', NULL, 80, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(40, 'create_sales', 'Create Sales Orders', 'pos', NULL, 81, 1, '2026-04-14 17:34:18', '2026-04-14 17:34:18', NULL),
(41, 'view_sales_history', 'View Sales History', 'pos', NULL, 82, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(42, 'refund_sales', 'Refund/Cancel Sales', 'pos', NULL, 83, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(43, 'view_kitchen', 'View Kitchen Orders', 'kitchen', NULL, 90, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(44, 'update_kitchen_status', 'Update Order Status', 'kitchen', NULL, 91, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(45, 'view_settings', 'View System Settings', 'settings', NULL, 100, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(46, 'edit_settings', 'Edit System Settings', 'settings', NULL, 101, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(47, 'view_audit_logs', 'View Audit Logs', 'audit', NULL, 110, 1, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(48, 'clear_system_logs', 'Clear system logs', 'audit', NULL, 111, 1, '2026-04-14 19:11:28', '2026-04-14 19:11:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `lpo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `delivery_terms` varchar(255) DEFAULT NULL,
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ordered_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `po_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','approved','lpo_created','sent','epo_created','partially_received','fully_received','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `vendor_id`, `lpo_id`, `notes`, `delivery_terms`, `store_id`, `ordered_by`, `approved_by`, `approved_at`, `po_date`, `expected_delivery_date`, `subtotal`, `tax_amount`, `total_amount`, `status`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `delivery_address`) VALUES
(3, 'PO-20260417-5283', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 2070000.00, 0.00, 2070000.00, 'draft', '2026-04-17 07:55:31', '2026-04-17 07:55:31', NULL, 3, NULL, 'AT our  main store'),
(6, 'PO-20260417-9125', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 09:48:59', '2026-04-17 09:48:59', NULL, 3, NULL, 'AT our  main store'),
(7, 'PO-20260417-7192', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 09:50:17', '2026-04-17 09:50:18', NULL, 3, NULL, 'AT our  main store'),
(8, 'PO-20260417-1948', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 09:57:33', '2026-04-17 09:57:33', NULL, 3, NULL, 'AT our  main store'),
(9, 'PO-20260417-5145', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 09:59:49', '2026-04-17 09:59:49', NULL, 3, NULL, 'AT our  main store'),
(10, 'PO-20260417-3492', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:02:20', '2026-04-17 10:02:21', NULL, 3, NULL, 'AT our  main store'),
(11, 'PO-20260417-4080', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:07:27', '2026-04-17 10:07:27', NULL, 3, NULL, 'AT our  main store'),
(12, 'PO-20260417-4885', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:08:09', '2026-04-17 10:08:09', NULL, 3, NULL, 'AT our  main store'),
(13, 'PO-20260417-2742', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:18:12', '2026-04-17 10:18:13', NULL, 3, NULL, 'AT our  main store'),
(14, 'PO-20260417-3253', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:19:22', '2026-04-17 10:19:23', NULL, 3, NULL, 'AT our  main store'),
(15, 'PO-20260417-4245', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:26:32', '2026-04-17 10:26:32', NULL, 3, NULL, 'AT our  main store'),
(17, 'PO-20260417-3257', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-20', 775000.00, 0.00, 775000.00, 'sent', '2026-04-17 10:49:21', '2026-04-17 10:49:21', NULL, 3, NULL, 'AT our main office'),
(22, 'LPO-20260429-0382', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-04-29', NULL, 760000.25, 0.00, 760000.25, 'lpo_created', '2026-04-29 09:07:44', '2026-04-29 09:07:45', NULL, 3, NULL, NULL),
(24, 'PO-20260508-1411', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-08', '2026-05-08', 500000.00, 0.00, 500000.00, 'partially_received', '2026-05-08 04:44:53', '2026-05-08 06:01:57', NULL, 3, NULL, 'main offices'),
(25, 'PO-20260511-0414', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-11', '2026-05-12', 200000.00, 0.00, 200000.00, 'partially_received', '2026-05-11 06:21:47', '2026-05-11 06:23:19', NULL, 3, NULL, NULL),
(26, 'PO-20260511-8792', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-11', '2026-05-13', 122500.00, 0.00, 122500.00, 'partially_received', '2026-05-11 06:28:57', '2026-05-11 06:29:59', NULL, 3, NULL, NULL),
(27, 'PO-20260511-9981', 1, NULL, NULL, 'Deliver to our promises', NULL, 3, NULL, NULL, '2026-05-11', '2026-05-13', 400000.00, 0.00, 400000.00, 'partially_received', '2026-05-11 13:57:22', '2026-05-11 13:59:46', NULL, 3, NULL, 'main offices'),
(28, 'PO-20260512-3148', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-12', '2026-05-13', 2149999.40, 0.00, 2149999.40, 'partially_received', '2026-05-12 03:56:45', '2026-05-12 03:58:27', NULL, 3, NULL, 'Arena Mall, Middle east Resturant East wing'),
(29, 'PO-20260512-2658', 2, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-12', '2026-05-14', 600000.00, 0.00, 600000.00, 'fully_received', '2026-05-12 05:19:54', '2026-05-12 05:27:02', NULL, 3, NULL, 'main offices');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity_ordered` decimal(15,6) NOT NULL,
  `quantity_received` decimal(15,6) NOT NULL DEFAULT 0.000000,
  `unit_cost` decimal(15,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `inventory_item_id`, `notes`, `unit_id`, `quantity_ordered`, `quantity_received`, `unit_cost`, `total_cost`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 3, 9, 'litres', NULL, 15.000000, 0.000000, 5500.00, 82500.00, '2026-04-17 07:55:31', '2026-04-17 07:55:31', 3, NULL),
(2, 3, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 07:55:31', '2026-04-17 07:55:31', 3, NULL),
(3, 3, 1, '3 bags', NULL, 15.000000, 0.000000, 125000.00, 1875000.00, '2026-04-17 07:55:31', '2026-04-17 07:55:31', 3, NULL),
(4, 6, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 09:48:59', '2026-04-17 09:48:59', 3, NULL),
(5, 6, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 09:48:59', '2026-04-17 09:48:59', 3, NULL),
(6, 6, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 09:48:59', '2026-04-17 09:48:59', 3, NULL),
(7, 7, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 09:50:18', '2026-04-17 09:50:18', 3, NULL),
(8, 7, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 09:50:18', '2026-04-17 09:50:18', 3, NULL),
(9, 7, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 09:50:18', '2026-04-17 09:50:18', 3, NULL),
(10, 8, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 09:57:33', '2026-04-17 09:57:33', 3, NULL),
(11, 8, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 09:57:33', '2026-04-17 09:57:33', 3, NULL),
(12, 8, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 09:57:33', '2026-04-17 09:57:33', 3, NULL),
(13, 9, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 09:59:49', '2026-04-17 09:59:49', 3, NULL),
(14, 9, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 09:59:49', '2026-04-17 09:59:49', 3, NULL),
(15, 9, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 09:59:49', '2026-04-17 09:59:49', 3, NULL),
(16, 10, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:02:21', '2026-04-17 10:02:21', 3, NULL),
(17, 10, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:02:21', '2026-04-17 10:02:21', 3, NULL),
(18, 10, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:02:21', '2026-04-17 10:02:21', 3, NULL),
(19, 11, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:07:27', '2026-04-17 10:07:27', 3, NULL),
(20, 11, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:07:27', '2026-04-17 10:07:27', 3, NULL),
(21, 11, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:07:27', '2026-04-17 10:07:27', 3, NULL),
(22, 12, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:08:09', '2026-04-17 10:08:09', 3, NULL),
(23, 12, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:08:09', '2026-04-17 10:08:09', 3, NULL),
(24, 12, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:08:09', '2026-04-17 10:08:09', 3, NULL),
(25, 13, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:18:13', '2026-04-17 10:18:13', 3, NULL),
(26, 13, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:18:13', '2026-04-17 10:18:13', 3, NULL),
(27, 13, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:18:13', '2026-04-17 10:18:13', 3, NULL),
(28, 14, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:19:22', '2026-04-17 10:19:22', 3, NULL),
(29, 14, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:19:22', '2026-04-17 10:19:22', 3, NULL),
(30, 14, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:19:23', '2026-04-17 10:19:23', 3, NULL),
(31, 15, 9, 'litres', NULL, 15.000000, 0.000000, 2000.00, 30000.00, '2026-04-17 10:26:32', '2026-04-17 10:26:32', 3, NULL),
(32, 15, 18, 'trays', NULL, 15.000000, 0.000000, 7500.00, 112500.00, '2026-04-17 10:26:32', '2026-04-17 10:26:32', 3, NULL),
(33, 15, 1, '3 bags', NULL, 15.000000, 0.000000, 120000.00, 1800000.00, '2026-04-17 10:26:32', '2026-04-17 10:26:32', 3, NULL),
(34, 17, NULL, 'boxes', NULL, 10.000000, 0.000000, 65000.00, 650000.00, '2026-04-17 10:49:21', '2026-04-17 10:49:21', 3, NULL),
(35, 17, NULL, 'kilograms', NULL, 50.000000, 0.000000, 1300.00, 65000.00, '2026-04-17 10:49:21', '2026-04-17 10:49:21', 3, NULL),
(36, 17, 4, 'kilograms', NULL, 50.000000, 0.000000, 1200.00, 60000.00, '2026-04-17 10:49:21', '2026-04-17 10:49:21', 3, NULL),
(45, 22, 1, NULL, NULL, 45.000000, 0.000000, 3000.00, 135000.00, '2026-04-29 09:07:44', '2026-04-29 09:07:44', 3, NULL),
(46, 22, 25, NULL, NULL, 25.000000, 0.000000, 10000.01, 250000.25, '2026-04-29 09:07:45', '2026-04-29 09:07:45', 3, NULL),
(47, 22, 9, NULL, NULL, 20.000000, 0.000000, 7500.00, 150000.00, '2026-04-29 09:07:45', '2026-04-29 09:07:45', 3, NULL),
(48, 22, 3, NULL, NULL, 30.000000, 0.000000, 7500.00, 225000.00, '2026-04-29 09:07:45', '2026-04-29 09:07:45', 3, NULL),
(51, 24, 21, NULL, NULL, 20.000000, 15.000000, 10000.00, 200000.00, '2026-05-08 04:44:53', '2026-05-08 06:01:57', 3, NULL),
(52, 24, 7, NULL, NULL, 30.000000, 25.000000, 10000.00, 300000.00, '2026-05-08 04:44:53', '2026-05-08 06:01:57', 3, NULL),
(53, 25, 7, NULL, NULL, 50.000000, 0.000000, 4000.00, 200000.00, '2026-05-11 06:21:47', '2026-05-11 06:21:47', 3, NULL),
(54, 26, 7, NULL, NULL, 30.000000, 30.000000, 3500.00, 105000.00, '2026-05-11 06:28:57', '2026-05-11 06:29:59', 3, NULL),
(55, 26, 1, NULL, NULL, 50.000000, 20.000000, 350.00, 17500.00, '2026-05-11 06:28:57', '2026-05-11 06:29:59', 3, NULL),
(56, 27, 1, NULL, NULL, 50.000000, 45.000000, 4000.00, 200000.00, '2026-05-11 13:57:22', '2026-05-11 13:59:46', 3, NULL),
(57, 27, 23, NULL, NULL, 20.000000, 20.000000, 10000.00, 200000.00, '2026-05-11 13:57:22', '2026-05-11 13:59:46', 3, NULL),
(58, 28, 24, NULL, NULL, 40.000000, 40.000000, 9999.99, 399999.60, '2026-05-12 03:56:45', '2026-05-12 03:58:27', 3, NULL),
(59, 28, 9, NULL, NULL, 30.000000, 25.000000, 25000.00, 750000.00, '2026-05-12 03:56:45', '2026-05-12 03:58:27', 3, NULL),
(60, 28, 28, NULL, NULL, 20.000000, 20.000000, 49999.99, 999999.80, '2026-05-12 03:56:45', '2026-05-12 03:58:27', 3, NULL),
(61, 29, 42, NULL, NULL, 30.000000, 30.000000, 20000.00, 600000.00, '2026-05-12 05:19:54', '2026-05-12 05:27:02', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

CREATE TABLE `requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_number` varchar(50) NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `date_needed` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','ordered','fulfilled','lpo_created') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `gm_notes` text DEFAULT NULL,
  `gm_edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `gm_edited_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisitions`
--

INSERT INTO `requisitions` (`id`, `requisition_number`, `store_id`, `requested_by`, `approved_by`, `date_needed`, `status`, `notes`, `gm_notes`, `gm_edited_by`, `gm_edited_at`, `rejection_reason`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'REQ-20260416-0001', 3, 2, 3, '2026-04-18', 'ordered', 'please we the following items in the store', NULL, NULL, NULL, NULL, '2026-04-17 10:41:08', '2026-04-16 09:47:39', '2026-04-17 10:49:21', NULL),
(3, 'REQ-20260416-0002', 3, 2, 3, '2026-04-24', 'ordered', 'please we urgently need the following items\r\n\r\nREJECTION REASON: reduce on qauntities please', NULL, NULL, NULL, NULL, '2026-04-17 04:57:29', '2026-04-16 10:09:59', '2026-04-17 09:48:59', NULL),
(4, 'REQ-20260429-0001', 3, 2, 4, '2026-05-02', 'lpo_created', 'please the following items will be needed\n\n--- GM Approval Notes ---\nThis requisition is carefully reviewed and approved for purchase, please kindly make a purchase order and send it to director for approval', NULL, NULL, NULL, NULL, '2026-04-29 06:32:50', '2026-04-29 03:19:41', '2026-04-29 10:32:30', NULL),
(5, 'REQ-20260429-0002', 3, 2, 4, '2026-05-02', 'ordered', NULL, NULL, NULL, NULL, NULL, '2026-04-29 06:19:08', '2026-04-29 04:52:17', '2026-04-29 09:07:45', NULL),
(6, 'REQ-20260505-0001', 3, 2, 4, '2026-05-09', 'approved', NULL, 'please create the LPO for these items', NULL, NULL, NULL, '2026-05-06 19:11:04', '2026-05-05 04:41:45', '2026-05-06 19:11:04', NULL),
(7, 'REQ-20260505-0002', 3, 2, 4, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, '2026-05-05 04:44:15', '2026-05-05 04:41:48', '2026-05-05 04:44:15', NULL),
(8, 'REQ-20260506-0001', 3, 2, 4, '2026-05-08', 'lpo_created', 'Please we need the following items', NULL, NULL, NULL, NULL, '2026-05-06 18:55:50', '2026-05-06 16:42:16', '2026-05-06 19:04:55', NULL),
(9, 'REQ-20260506-0002', 3, 2, 4, '2026-05-15', 'lpo_created', 'we shall need the following items', 'the please draft the lpo for the following items', NULL, NULL, NULL, '2026-05-06 23:40:45', '2026-05-06 23:38:27', '2026-05-06 23:44:47', NULL),
(10, 'REQ-20260511-0001', 3, 2, 4, '2026-05-13', 'lpo_created', NULL, NULL, NULL, NULL, NULL, '2026-05-11 06:20:18', '2026-05-11 06:19:57', '2026-05-11 06:21:04', NULL),
(11, 'REQ-20260511-0002', 3, 2, 4, '2026-05-13', 'lpo_created', NULL, NULL, NULL, NULL, NULL, '2026-05-11 06:26:36', '2026-05-11 06:26:10', '2026-05-11 06:28:19', NULL),
(12, 'REQ-20260511-0003', 3, 2, 4, '2026-05-14', 'lpo_created', NULL, NULL, NULL, NULL, NULL, '2026-05-11 13:55:03', '2026-05-11 13:54:10', '2026-05-11 13:56:03', NULL),
(13, 'REQ-20260512-0001', 3, 2, 4, NULL, 'lpo_created', NULL, 'please , procurement officer draft the lpo for these items', NULL, NULL, NULL, '2026-05-12 05:16:56', '2026-05-12 05:15:57', '2026-05-12 05:18:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity_requested` decimal(15,2) NOT NULL,
  `metrics` varchar(255) DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `quantity_approved` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisition_items`
--

INSERT INTO `requisition_items` (`id`, `requisition_id`, `inventory_item_id`, `item_name`, `quantity_requested`, `metrics`, `category_name`, `quantity_approved`, `notes`, `created_at`, `updated_at`) VALUES
(8, 3, 9, NULL, 15.00, NULL, NULL, 15.00, 'litres', '2026-04-16 14:25:44', '2026-04-17 04:57:29'),
(9, 3, 18, NULL, 15.00, NULL, NULL, 15.00, 'trays', '2026-04-16 14:25:44', '2026-04-17 04:57:29'),
(10, 3, 1, NULL, 15.00, NULL, NULL, 15.00, '3 bags', '2026-04-16 14:25:44', '2026-04-17 04:57:29'),
(15, 2, NULL, 'tomatoes', 10.00, NULL, NULL, 10.00, 'boxes', '2026-04-16 15:04:11', '2026-04-17 10:41:08'),
(16, 2, NULL, 'white salt', 50.00, NULL, NULL, 50.00, 'kilograms', '2026-04-16 15:04:11', '2026-04-17 10:41:08'),
(17, 2, 4, NULL, 50.00, NULL, NULL, 50.00, 'kilograms', '2026-04-16 15:04:11', '2026-04-17 10:41:08'),
(32, 5, 1, NULL, 50.00, 'kg', NULL, 45.00, NULL, '2026-04-29 05:28:29', '2026-04-29 06:19:07'),
(33, 5, 25, NULL, 30.00, 'cartons', NULL, 25.00, NULL, '2026-04-29 05:28:29', '2026-04-29 06:19:08'),
(34, 5, 9, NULL, 20.00, 'litres', NULL, 20.00, NULL, '2026-04-29 05:28:29', '2026-04-29 06:19:08'),
(35, 5, 3, NULL, 30.00, 'cartons', NULL, 30.00, NULL, '2026-04-29 05:28:29', '2026-04-29 06:19:08'),
(36, 4, 24, NULL, 40.00, 'cartons', NULL, 40.00, 'cartons', '2026-04-29 06:30:50', '2026-04-29 06:32:50'),
(37, 4, 9, NULL, 30.00, 'cartons', NULL, 30.00, 'for one letter each', '2026-04-29 06:30:50', '2026-04-29 06:32:50'),
(38, 4, 28, NULL, 20.00, 'sets', NULL, 20.00, 'crates', '2026-04-29 06:30:50', '2026-04-29 06:32:50'),
(41, 7, 21, NULL, 50.00, 'packs', NULL, 49.99, NULL, '2026-05-05 04:41:48', '2026-05-05 04:44:15'),
(42, 7, 7, NULL, 50.00, 'meters', NULL, 50.00, NULL, '2026-05-05 04:41:48', '2026-05-05 04:44:15'),
(43, 8, 21, NULL, 20.00, 'kg', 'Meat & Poultry', 20.00, 'aurgently needed', '2026-05-06 16:42:16', '2026-05-06 18:55:50'),
(44, 8, 7, NULL, 30.00, 'kg', 'Dry Goods', 30.00, NULL, '2026-05-06 16:42:16', '2026-05-06 18:55:50'),
(47, 6, 21, NULL, 50.00, 'packs', 'Meat & Poultry', 50.00, NULL, '2026-05-06 18:48:16', '2026-05-06 19:11:04'),
(48, 6, 7, NULL, 50.00, 'meters', 'Dry Goods', 50.00, NULL, '2026-05-06 18:48:16', '2026-05-06 19:11:04'),
(49, 9, 26, NULL, 30.00, 'boxes', 'Water', 30.00, '50omls', '2026-05-06 23:38:27', '2026-05-06 23:40:45'),
(50, 9, 26, NULL, 20.00, 'boxes', 'Water', 20.00, '1.5litres', '2026-05-06 23:38:27', '2026-05-06 23:40:45'),
(51, 10, 7, NULL, 50.00, 'kg', 'Dry Goods', 50.00, NULL, '2026-05-11 06:19:57', '2026-05-11 06:20:18'),
(52, 11, 7, NULL, 30.00, 'kg', 'Dry Goods', 30.00, NULL, '2026-05-11 06:26:10', '2026-05-11 06:26:36'),
(53, 11, 1, NULL, 50.00, 'kg', 'Dry Goods', 50.00, NULL, '2026-05-11 06:26:10', '2026-05-11 06:26:36'),
(54, 12, 1, NULL, 50.00, 'kg', 'Dry Goods', 50.00, NULL, '2026-05-11 13:54:10', '2026-05-11 13:55:03'),
(55, 12, 23, NULL, 20.00, 'cartons', 'Soft Drinks', 20.00, NULL, '2026-05-11 13:54:10', '2026-05-11 13:55:03'),
(56, 13, 42, NULL, 30.00, 'boxes', 'Water', 30.00, 'its urgently neede', '2026-05-12 05:15:57', '2026-05-12 05:16:56');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_system_role` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `is_active`, `is_system_role`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'super_admin', 'Super Administrator', 'Full system access with all permissions', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(2, 'owner', 'Owner', 'Restaurant owner with all business permissions', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(3, 'general_manager', 'General Manager', 'Manages overall restaurant operations', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(4, 'kitchen_manager', 'Kitchen Manager', 'Manages kitchen operations and inventory', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(5, 'bar_manager', 'Bar Manager', 'Manages bar operations and beverage inventory', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(6, 'procurement_officer', 'Procurement Officer', 'Handles purchasing and vendor management', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(7, 'store_keeper', 'Store Keeper', 'Manages inventory and stock movements', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(8, 'cashier', 'Cashier', 'Handles POS and sales transactions', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(9, 'waiter', 'Waiter/Waitress', 'Takes orders and serves customers', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(10, 'chef', 'Chef', 'Prepares food and updates kitchen status', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(11, 'accountant', 'Accountant', 'Handles financial reports and transactions', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(12, 'viewer', 'Viewer (Read Only)', 'Can only view data, no modifications', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(13, 'DR001', 'DIRECTOR', 'This is director of the business', 1, 0, 1, NULL, '2026-04-29 11:07:55', '2026-04-29 11:07:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(2, 1, 2, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(3, 1, 3, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(4, 1, 4, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(5, 1, 5, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(6, 1, 6, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(7, 1, 7, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(8, 1, 8, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(9, 1, 9, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(10, 1, 10, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(11, 1, 11, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(12, 1, 12, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(13, 1, 13, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(14, 1, 14, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(15, 1, 15, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(16, 1, 16, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(17, 1, 17, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(18, 1, 18, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(19, 1, 19, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(20, 1, 20, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(21, 1, 21, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(22, 1, 22, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(23, 1, 23, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(24, 1, 24, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(25, 1, 25, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(26, 1, 26, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(27, 1, 27, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(28, 1, 28, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(29, 1, 29, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(30, 1, 30, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(31, 1, 31, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(32, 1, 32, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(33, 1, 33, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(34, 1, 34, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(35, 1, 35, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(36, 1, 36, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(37, 1, 37, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(38, 1, 38, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(39, 1, 39, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(40, 1, 40, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(41, 1, 41, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(42, 1, 42, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(43, 1, 43, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(44, 1, 44, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(45, 1, 45, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(46, 1, 46, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(47, 1, 47, '2026-04-14 17:45:48', '2026-04-14 17:45:48'),
(48, 2, 1, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(49, 2, 2, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(50, 2, 3, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(51, 2, 4, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(52, 2, 5, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(53, 2, 14, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(54, 2, 15, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(55, 2, 16, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(56, 2, 17, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(57, 2, 18, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(58, 2, 19, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(59, 2, 20, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(60, 2, 21, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(61, 2, 22, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(62, 2, 23, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(63, 2, 24, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(64, 2, 25, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(65, 2, 26, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(66, 2, 27, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(67, 2, 28, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(68, 2, 29, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(69, 2, 30, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(70, 2, 31, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(71, 2, 32, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(72, 2, 33, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(73, 2, 34, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(74, 2, 35, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(75, 2, 36, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(76, 2, 37, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(77, 2, 38, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(78, 2, 39, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(79, 2, 40, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(80, 2, 41, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(81, 2, 42, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(82, 2, 43, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(83, 2, 44, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(84, 2, 45, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(85, 2, 46, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(86, 2, 47, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(87, 3, 14, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(88, 3, 15, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(89, 3, 16, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(90, 3, 17, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(91, 3, 18, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(92, 3, 19, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(93, 3, 20, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(94, 3, 21, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(95, 3, 22, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(96, 3, 23, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(97, 3, 24, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(98, 3, 25, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(99, 3, 26, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(100, 3, 27, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(101, 3, 28, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(102, 3, 29, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(103, 3, 30, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(104, 3, 31, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(105, 3, 32, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(106, 3, 33, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(107, 3, 34, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(108, 3, 35, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(109, 3, 36, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(110, 3, 37, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(111, 3, 38, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(112, 3, 39, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(113, 3, 40, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(114, 3, 41, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(115, 3, 42, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(116, 3, 43, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(117, 3, 44, '2026-04-14 17:45:49', '2026-04-14 17:45:49'),
(118, 4, 14, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(119, 4, 18, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(120, 4, 20, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(121, 4, 29, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(122, 4, 35, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(123, 4, 36, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(124, 4, 43, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(125, 4, 44, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(126, 5, 14, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(127, 5, 18, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(128, 5, 20, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(129, 5, 25, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(130, 5, 29, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(131, 5, 35, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(132, 5, 36, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(133, 5, 39, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(134, 6, 20, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(135, 6, 21, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(136, 6, 22, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(137, 6, 23, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(138, 6, 24, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(139, 6, 25, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(140, 6, 26, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(141, 6, 27, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(142, 6, 28, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(143, 7, 14, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(144, 7, 15, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(145, 7, 16, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(146, 7, 18, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(147, 7, 19, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(148, 7, 20, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(149, 7, 24, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(150, 7, 25, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(151, 7, 29, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(152, 7, 31, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(153, 7, 35, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(154, 7, 36, '2026-04-14 17:45:50', '2026-04-14 17:45:50'),
(155, 8, 39, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(156, 8, 40, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(157, 8, 41, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(158, 9, 39, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(159, 9, 40, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(160, 10, 44, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(161, 10, 43, '2026-04-14 17:45:51', '2026-04-14 17:45:51'),
(162, 11, 20, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(163, 11, 25, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(164, 11, 35, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(165, 11, 37, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(166, 11, 38, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(167, 11, 47, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(168, 12, 1, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(169, 12, 6, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(170, 12, 10, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(171, 12, 14, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(172, 12, 20, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(173, 12, 25, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(174, 12, 29, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(175, 12, 31, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(176, 12, 33, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(177, 12, 35, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(178, 12, 36, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(179, 12, 37, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(180, 12, 41, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(181, 12, 43, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(182, 12, 45, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(183, 12, 47, '2026-04-14 17:45:52', '2026-04-14 17:45:52'),
(184, 13, 2, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(185, 13, 3, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(186, 13, 4, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(187, 13, 5, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(188, 13, 7, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(189, 13, 11, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(190, 13, 12, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(191, 13, 31, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(192, 13, 32, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(193, 13, 33, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(194, 13, 35, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(195, 13, 36, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(196, 13, 37, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(197, 13, 38, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(198, 13, 46, '2026-04-29 11:07:56', '2026-04-29 11:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('124hiOBP5ZfzaFAchtrJ2WmWLxRXDQEHRbk7pinF', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYkpsSmlRVDl1MU1FNnAzeGlXd3JNUzBtelJMUDM3Y0hLZjBQOTNkcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9jdXJlbWVudC9ub3RpZmljYXRpb25zL2NoZWNrIjtzOjU6InJvdXRlIjtzOjMxOiJwcm9jdXJlbWVudC5ub3RpZmljYXRpb25zLmNoZWNrIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1778598207),
('28pUVabnBmidYpedkkzRnPKdaiNu2ox0OMvUUy81', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidEpQaEE5cVU2ekphREt4WEtJMVZlcFk5SklhRm95UU93V1NhTkpGSyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3VzZXJzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yb2xlcyI7czo1OiJyb3V0ZSI7czoxMToicm9sZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1778591962),
('ogZXMjLSHamfSkY6YSSuAvaDDWYZNyPe7rtj76Oe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiano4VjZjRTVDeEhKNUFKM2hLZDNZY2dnVFFEWEpOQ3JINFE1eHRJRCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czoxMDoibG9naW4uZm9ybSI7fX0=', 1778597978);

-- --------------------------------------------------------

--
-- Table structure for table `stock_balances`
--

CREATE TABLE `stock_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,6) NOT NULL DEFAULT 0.000000 COMMENT 'Always in base unit',
  `average_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `movement_number` varchar(100) NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `store_id` bigint(20) UNSIGNED NOT NULL,
  `movement_type_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(15,6) NOT NULL,
  `pack_type` varchar(50) DEFAULT NULL COMMENT 'Box, Carton, Crate, Dozen, etc.',
  `base_unit` varchar(50) DEFAULT NULL COMMENT 'Base unit label at time of movement e.g. bottle, piece, kg',
  `pack_size` int(11) DEFAULT NULL COMMENT 'Pieces per pack (e.g., 24, 12, 6)',
  `number_of_packs` int(11) DEFAULT NULL COMMENT 'Number of packs received (e.g., 10 cartons)',
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity_in_base_unit` decimal(15,6) NOT NULL DEFAULT 0.000000 COMMENT 'Calculate in model: quantity × unit.quantity_in_base_unit',
  `stock_before` decimal(10,2) DEFAULT 0.00,
  `stock_after` decimal(10,2) DEFAULT 0.00,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `total_value` decimal(15,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `taken_by` varchar(255) DEFAULT NULL COMMENT 'Person who took/received the items (STOCK OUT - issued to department)',
  `returned_by` varchar(255) DEFAULT NULL COMMENT 'Person who returned the items (STOCK IN - returned from department)',
  `movement_date` date NOT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `goods_received_note_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_reversed` tinyint(1) NOT NULL DEFAULT 0,
  `reversed_by_movement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `movement_number`, `inventory_item_id`, `store_id`, `movement_type_id`, `department_id`, `quantity`, `pack_type`, `base_unit`, `pack_size`, `number_of_packs`, `unit_id`, `quantity_in_base_unit`, `stock_before`, `stock_after`, `unit_cost`, `total_value`, `reason`, `taken_by`, `returned_by`, `movement_date`, `approved_at`, `approved_by`, `purchase_order_id`, `goods_received_note_id`, `is_reversed`, `reversed_by_movement_id`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(3, 'STK-IN-20260508-0414', 37, 1, 2, NULL, 20.000000, NULL, NULL, NULL, NULL, NULL, 220.000000, 0.00, 220.00, 0.00, 0.00, 'the following items were added to stock', NULL, NULL, '2026-05-08', '2026-05-08 12:11:38', 2, NULL, NULL, 0, NULL, '2026-05-08 12:11:38', '2026-05-08 12:11:38', NULL, 2, NULL),
(4, 'STK-IN-20260511-2511', 38, 1, 2, NULL, 21.000000, NULL, NULL, NULL, NULL, NULL, 231.000000, 0.00, 231.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-11', '2026-05-11 03:05:08', 2, NULL, NULL, 0, NULL, '2026-05-11 03:05:08', '2026-05-11 03:05:08', NULL, 2, NULL),
(5, 'STK-IN-20260511-9051', 39, 1, 2, NULL, 99.990000, NULL, NULL, NULL, NULL, NULL, 99.990000, 0.00, 99.99, 0.00, 0.00, 'expiring on 15/05/2026', NULL, NULL, '2026-05-11', '2026-05-11 03:14:55', 2, NULL, NULL, 0, NULL, '2026-05-11 03:14:55', '2026-05-11 03:14:55', NULL, 2, NULL),
(6, 'STK-ADJ-20260511-9995', 38, 1, 2, NULL, 5.000000, NULL, NULL, NULL, NULL, NULL, 5.000000, 231.00, 236.00, 0.00, 0.00, 'recount (Manual adjustment)', NULL, NULL, '2026-05-11', '2026-05-11 04:04:29', 2, NULL, NULL, 0, NULL, '2026-05-11 04:04:29', '2026-05-11 04:04:29', NULL, 2, NULL),
(7, 'STK-GRN-20260511-9935', 21, 1, 1, NULL, 15.000000, NULL, 'bottle', NULL, NULL, NULL, 15.000000, 0.00, 15.00, 10000.00, 150000.00, 'Received from GRN: GRN-20260508-3441', NULL, NULL, '2026-05-11', '2026-05-11 06:14:26', 2, NULL, 3, 0, NULL, '2026-05-11 06:14:26', '2026-05-11 06:14:26', NULL, 2, NULL),
(8, 'STK-GRN-20260511-0723', 7, 1, 1, NULL, 25.000000, NULL, 'bottle', NULL, NULL, NULL, 25.000000, 0.00, 25.00, 10000.00, 250000.00, 'Received from GRN: GRN-20260508-3441', NULL, NULL, '2026-05-11', '2026-05-11 06:14:26', 2, NULL, 3, 0, NULL, '2026-05-11 06:14:26', '2026-05-11 06:14:26', NULL, 2, NULL),
(9, 'STK-GRN-20260511-5050', 7, 1, 1, NULL, 30.000000, NULL, 'kg', NULL, NULL, NULL, 30.000000, 25.00, 55.00, 3500.00, 105000.00, 'Received from GRN: GRN-20260511-8836', NULL, NULL, '2026-05-11', '2026-05-11 06:30:42', 2, NULL, 5, 0, NULL, '2026-05-11 06:30:42', '2026-05-11 06:30:42', NULL, 2, NULL),
(10, 'STK-GRN-20260511-4580', 1, 1, 1, NULL, 20.000000, NULL, 'kg', NULL, NULL, NULL, 20.000000, 0.00, 20.00, 350.00, 7000.00, 'Received from GRN: GRN-20260511-8836', NULL, NULL, '2026-05-11', '2026-05-11 06:30:42', 2, NULL, 5, 0, NULL, '2026-05-11 06:30:42', '2026-05-11 06:30:42', NULL, 2, NULL),
(11, 'STK-IN-20260511-1245', 40, 1, 2, NULL, 10.000000, 'carton', 'bottle', 12, 10, NULL, 120.000000, 0.00, 120.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-11', '2026-05-11 06:35:10', 2, NULL, NULL, 0, NULL, '2026-05-11 06:35:10', '2026-05-11 06:35:10', NULL, 2, NULL),
(12, 'ISS-20260511-0993', 1, 1, 4, NULL, 20.000000, NULL, 'kg', NULL, 20, NULL, 20.000000, 20.00, 40.00, 350.00, 7000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-7208', NULL, NULL, '2026-05-11', '2026-05-11 08:56:05', 2, NULL, NULL, 0, NULL, '2026-05-11 08:56:05', '2026-05-11 08:56:05', NULL, 2, NULL),
(13, 'ISS-20260511-9602', 21, 1, 4, NULL, 15.000000, NULL, 'kg', NULL, 15, NULL, 15.000000, 15.00, 30.00, 10000.00, 150000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-7208', NULL, NULL, '2026-05-11', '2026-05-11 08:56:05', 2, NULL, NULL, 0, NULL, '2026-05-11 08:56:05', '2026-05-11 08:56:05', NULL, 2, NULL),
(14, 'RET-20260511-8045', 1, 1, 5, NULL, 10.000000, NULL, 'kg', NULL, NULL, NULL, 10.000000, 40.00, 30.00, 350.00, 3500.00, 'Return from KITCHEN - Req: KIT-REQ-20260511-7208 - not used', NULL, NULL, '2026-05-11', '2026-05-11 10:13:18', 2, NULL, NULL, 0, NULL, '2026-05-11 10:13:18', '2026-05-11 10:13:18', NULL, 2, NULL),
(15, 'RET-20260511-2430', 21, 1, 5, NULL, 5.000000, NULL, 'kg', NULL, NULL, NULL, 5.000000, 30.00, 25.00, 10000.00, 50000.00, 'Return from KITCHEN - Req: KIT-REQ-20260511-7208 - not used', NULL, NULL, '2026-05-11', '2026-05-11 10:13:18', 2, NULL, NULL, 0, NULL, '2026-05-11 10:13:18', '2026-05-11 10:13:18', NULL, 2, NULL),
(16, 'RET-20260511-4547', 1, 1, 2, NULL, 5.000000, NULL, 'kg', NULL, NULL, NULL, 5.000000, 30.00, 35.00, 350.00, 1750.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260511-7208', NULL, NULL, '2026-05-11', '2026-05-11 10:21:34', 2, NULL, NULL, 0, NULL, '2026-05-11 10:21:34', '2026-05-11 10:21:34', NULL, 2, NULL),
(17, 'RET-20260511-0306', 1, 1, 2, NULL, 5.000000, NULL, 'kg', NULL, NULL, NULL, 5.000000, 35.00, 40.00, 350.00, 1750.00, 'RETURN from KITCHEN', NULL, NULL, '2026-05-11', '2026-05-11 10:26:06', 2, NULL, NULL, 0, NULL, '2026-05-11 10:26:06', '2026-05-11 10:26:06', NULL, 2, NULL),
(18, 'RET-20260511-1946', 1, 1, 2, NULL, 5.000000, NULL, 'pcs', 1, 5, NULL, 5.000000, 40.00, 45.00, 350.00, 1750.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260511-7208', NULL, NULL, '2026-05-11', '2026-05-11 10:53:46', 2, NULL, NULL, 0, NULL, '2026-05-11 10:53:46', '2026-05-11 10:53:46', NULL, 2, NULL),
(19, 'ISS-20260511-3700', 18, 1, 4, NULL, 10.000000, NULL, 'pcs', 12, 10, NULL, 10.000000, 0.00, 10.00, 5.00, 50.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-0390', 'BARIGYE DAVIS', NULL, '2026-05-11', '2026-05-11 11:48:48', 2, NULL, NULL, 0, NULL, '2026-05-11 11:48:48', '2026-05-11 11:48:48', NULL, 2, NULL),
(20, 'ISS-20260511-9954', 38, 1, 4, NULL, 5.000000, 'carton', 'bottles', 12, 5, NULL, 60.000000, 236.00, 296.00, 0.00, 0.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-3177', 'James', NULL, '2026-05-11', '2026-05-11 11:57:42', 2, NULL, NULL, 0, NULL, '2026-05-11 11:57:42', '2026-05-11 11:57:42', NULL, 2, NULL),
(21, 'RET-20260511-6141', 38, 1, 2, NULL, 30.000000, NULL, 'bottle', 12, NULL, NULL, 30.000000, 296.00, 326.00, 0.00, 0.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260511-3177 - Returned: 30 individual pieces', NULL, 'James', '2026-05-11', '2026-05-11 13:12:09', 2, NULL, NULL, 0, NULL, '2026-05-11 13:12:09', '2026-05-11 13:12:09', NULL, 2, NULL),
(22, 'STK-GRN-20260511-8064', 1, 1, 1, NULL, 45.000000, NULL, 'bottle', NULL, NULL, NULL, 45.000000, 45.00, 90.00, 4000.00, 180000.00, 'Received from GRN: GRN-20260511-2585', NULL, NULL, '2026-05-11', '2026-05-11 14:01:06', 2, NULL, 6, 0, NULL, '2026-05-11 14:01:06', '2026-05-11 14:01:06', NULL, 2, NULL),
(23, 'STK-GRN-20260511-8960', 23, 1, 1, NULL, 20.000000, NULL, 'bottle', NULL, NULL, NULL, 20.000000, 0.00, 20.00, 10000.00, 200000.00, 'Received from GRN: GRN-20260511-2585', NULL, NULL, '2026-05-11', '2026-05-11 14:01:06', 2, NULL, 6, 0, NULL, '2026-05-11 14:01:06', '2026-05-11 14:01:06', NULL, 2, NULL),
(24, 'ISS-20260511-5615', 21, 1, 4, NULL, 18.000000, NULL, 'kg', NULL, 18, NULL, 18.000000, 25.00, 43.00, 10000.00, 180000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-6256', 'BARIGYE DAVIS', NULL, '2026-05-11', '2026-05-11 14:10:04', 2, NULL, NULL, 0, NULL, '2026-05-11 14:10:04', '2026-05-11 14:10:04', NULL, 2, NULL),
(25, 'ISS-20260511-4989', 7, 1, 4, NULL, 16.000000, NULL, 'kg', NULL, 16, NULL, 16.000000, 55.00, 71.00, 3500.00, 56000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-6256', 'BARIGYE DAVIS', NULL, '2026-05-11', '2026-05-11 14:10:04', 2, NULL, NULL, 0, NULL, '2026-05-11 14:10:04', '2026-05-11 14:10:04', NULL, 2, NULL),
(26, 'RET-20260511-5385', 18, 1, 2, NULL, 3.000000, NULL, 'pcs', 12, NULL, NULL, 3.000000, 10.00, 13.00, 5.00, 15.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260511-0390 - Returned: 3 individual pieces', NULL, 'James', '2026-05-11', '2026-05-11 14:12:29', 2, NULL, NULL, 0, NULL, '2026-05-11 14:12:29', '2026-05-11 14:12:29', NULL, 2, NULL),
(27, 'ISS-20260511-1867', 21, 1, 4, NULL, 2.000000, NULL, 'kg', NULL, 2, NULL, 2.000000, 43.00, 45.00, 10000.00, 20000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-6256', 'BARIGYE DAVIS', NULL, '2026-05-11', '2026-05-11 14:14:42', 2, NULL, NULL, 0, NULL, '2026-05-11 14:14:42', '2026-05-11 14:14:42', NULL, 2, NULL),
(28, 'ISS-20260511-9036', 7, 1, 4, NULL, 4.000000, NULL, 'kg', NULL, 4, NULL, 4.000000, 71.00, 75.00, 3500.00, 14000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260511-6256', 'BARIGYE DAVIS', NULL, '2026-05-11', '2026-05-11 14:14:42', 2, NULL, NULL, 0, NULL, '2026-05-11 14:14:42', '2026-05-11 14:14:42', NULL, 2, NULL),
(29, 'STK-IN-20260512-1806', 41, 1, 2, NULL, 15.000000, 'carton', 'bottle', 12, 15, NULL, 180.000000, 0.00, 180.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-12', '2026-05-12 03:27:22', 2, NULL, NULL, 0, NULL, '2026-05-12 03:27:22', '2026-05-12 03:27:22', NULL, 2, NULL),
(30, 'STK-GRN-20260512-3077', 24, 1, 1, NULL, 40.000000, 'carton', 'bottle', 12, 40, NULL, 480.000000, 0.00, 480.00, 9999.99, 4799995.20, 'Received from GRN: GRN-20260512-7996', NULL, NULL, '2026-05-12', '2026-05-12 04:04:07', 2, NULL, 7, 0, NULL, '2026-05-12 04:04:07', '2026-05-12 04:04:07', NULL, 2, NULL),
(31, 'STK-GRN-20260512-5043', 9, 1, 1, NULL, 25.000000, NULL, 'litre', NULL, NULL, NULL, 25.000000, 0.00, 25.00, 25000.00, 625000.00, 'Received from GRN: GRN-20260512-7996', NULL, NULL, '2026-05-12', '2026-05-12 04:04:07', 2, NULL, 7, 0, NULL, '2026-05-12 04:04:07', '2026-05-12 04:04:07', NULL, 2, NULL),
(32, 'STK-GRN-20260512-9019', 28, 1, 1, NULL, 20.000000, 'crate', 'piece', 12, 20, NULL, 240.000000, 0.00, 240.00, 49999.99, 11999997.60, 'Received from GRN: GRN-20260512-7996', NULL, NULL, '2026-05-12', '2026-05-12 04:04:07', 2, NULL, 7, 0, NULL, '2026-05-12 04:04:07', '2026-05-12 04:04:07', NULL, 2, NULL),
(33, 'STK-IN-20260512-4591', 42, 1, 2, NULL, 20.000000, 'box', 'bottle', 11, 20, NULL, 220.000000, 0.00, 220.00, 0.00, 0.00, 'We received 20 boxes of Rwenzori mineral water of 300mls', NULL, NULL, '2026-05-12', '2026-05-12 05:13:56', 2, NULL, NULL, 0, NULL, '2026-05-12 05:13:56', '2026-05-12 05:13:56', NULL, 2, NULL),
(34, 'STK-GRN-20260512-0180', 42, 1, 1, NULL, 30.000000, 'box', 'bottle', 12, 30, NULL, 360.000000, 220.00, 580.00, 20000.00, 7200000.00, 'Received from GRN: GRN-20260512-6437', NULL, NULL, '2026-05-12', '2026-05-12 05:28:15', 2, NULL, 9, 0, NULL, '2026-05-12 05:28:15', '2026-05-12 05:28:15', NULL, 2, NULL),
(35, 'ISS-20260512-0078', 7, 1, 5, NULL, 20.000000, NULL, 'pcs', NULL, NULL, NULL, 20.000000, 115.00, 95.00, 3500.00, 70000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260512-0093', 'BARIGYE DAVIS', NULL, '2026-05-12', '2026-05-12 06:39:31', 2, NULL, NULL, 0, NULL, '2026-05-12 06:39:31', '2026-05-12 06:39:31', NULL, 2, NULL),
(36, 'ISS-20260512-2727', 1, 1, 5, NULL, 15.000000, NULL, 'pcs', NULL, NULL, NULL, 15.000000, 270.00, 255.00, 4000.00, 60000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260512-0093', 'BARIGYE DAVIS', NULL, '2026-05-12', '2026-05-12 06:39:31', 2, NULL, NULL, 0, NULL, '2026-05-12 06:39:31', '2026-05-12 06:39:31', NULL, 2, NULL),
(37, 'ISS-20260512-1434', 24, 1, 5, NULL, 20.000000, NULL, 'bottle', NULL, NULL, NULL, 20.000000, 730.00, 710.00, 9999.99, 199999.80, 'Issued to KITCHEN - Req: KIT-REQ-20260512-1915', 'BARIGYE DAVIS', NULL, '2026-05-12', '2026-05-12 06:54:49', 2, NULL, NULL, 0, NULL, '2026-05-12 06:54:49', '2026-05-12 06:54:49', NULL, 2, NULL),
(38, 'STK-ADJ-20260512-7830', 9, 1, 2, NULL, 10.000000, NULL, 'litre', NULL, NULL, NULL, 10.000000, 175.00, 185.00, 25000.00, 250000.00, 'recount (Manual adjustment)', NULL, NULL, '2026-05-12', '2026-05-12 09:23:36', 2, NULL, NULL, 0, NULL, '2026-05-12 09:23:36', '2026-05-12 09:23:36', NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_movement_types`
--

CREATE TABLE `stock_movement_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sign` enum('+','-') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_movement_types`
--

INSERT INTO `stock_movement_types` (`id`, `code`, `name`, `sign`, `description`, `requires_approval`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PURCHASE', 'Purchase Receipt', '+', 'Stock received from purchase order', 0, 1, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(2, 'MANUAL_IN', 'Manual Stock In', '+', 'Manual stock addition', 0, 2, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(3, 'MANUAL_OUT', 'Manual Stock Out', '-', 'Manual stock reduction', 0, 3, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(4, 'ADJUSTMENT_IN', 'Stock Adjustment In', '+', 'Stock count adjustment', 0, 4, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(5, 'SALE', 'Sales', '-', 'Stock sold to customer', 0, 5, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(6, 'GRN', 'Goods Received Note', '+', 'Stock received via GRN', 0, 6, 1, '2026-05-08 15:11:33', '2026-05-08 15:11:33'),
(7, 'ADJUSTMENT_OUT', 'Stock Adjustment Out', '-', NULL, 0, 0, 1, '2026-05-12 07:46:08', '2026-05-12 07:46:08');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `store_type_id` bigint(20) UNSIGNED NOT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `code`, `name`, `location`, `store_type_id`, `manager_id`, `is_active`, `notes`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'MAIN', 'Main Store', 'Main Location', 1, NULL, 1, NULL, '2026-05-08 15:09:57', '2026-05-08 15:09:57', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `store_types`
--

CREATE TABLE `store_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `store_types`
--

INSERT INTO `store_types` (`id`, `code`, `name`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MAIN', 'Main Store Type', 'Primary store type', 1, 1, '2026-05-08 15:09:57', '2026-05-08 15:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `code`, `name`, `category_id`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'COLA', 'Cola', 1, NULL, 1, 1, '2026-04-14 17:33:07', NULL),
(2, 'ORANGE', 'Orange', 1, NULL, 2, 1, '2026-04-14 17:33:07', NULL),
(3, 'LEMON-LIME', 'Lemon-Lime', 1, NULL, 3, 1, '2026-04-14 17:33:07', NULL),
(4, 'GINGER', 'Ginger Beer/Ale', 1, NULL, 4, 1, '2026-04-14 17:33:07', NULL),
(5, 'VODKA', 'Vodka', 5, NULL, 1, 1, '2026-04-14 17:33:07', NULL),
(6, 'WHISKY', 'Whisky', 5, NULL, 2, 1, '2026-04-14 17:33:07', NULL),
(7, 'GIN', 'Gin', 5, NULL, 3, 1, '2026-04-14 17:33:07', NULL),
(8, 'RUM', 'Rum', 5, NULL, 4, 1, '2026-04-14 17:33:07', NULL),
(9, 'TEQUILA', 'Tequila', 5, NULL, 5, 1, '2026-04-14 17:33:07', NULL),
(10, 'LIQUEUR', 'Liqueur', 5, NULL, 6, 1, '2026-04-14 17:33:07', NULL),
(11, 'CHICKEN', 'Chicken', 9, NULL, 1, 1, '2026-04-14 17:33:07', NULL),
(12, 'BEEF', 'Beef', 9, NULL, 2, 1, '2026-04-14 17:33:07', NULL),
(13, 'PORK', 'Pork', 9, NULL, 3, 1, '2026-04-14 17:33:07', NULL),
(14, 'GOAT', 'Goat', 9, NULL, 4, 1, '2026-04-14 17:33:07', NULL),
(15, 'FISH', 'Fish', 9, NULL, 5, 1, '2026-04-14 17:33:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `units_of_measure`
--

CREATE TABLE `units_of_measure` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(20) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_base_unit` tinyint(1) NOT NULL DEFAULT 0,
  `base_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conversion_factor` decimal(15,6) DEFAULT NULL COMMENT 'Factor to convert to base unit',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units_of_measure`
--

INSERT INTO `units_of_measure` (`id`, `code`, `name`, `symbol`, `description`, `is_base_unit`, `base_unit_id`, `conversion_factor`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PCS', 'Pieces', 'pcs', NULL, 1, NULL, NULL, 1, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(2, 'BOTTLE', 'Bottle', 'btl', NULL, 1, NULL, NULL, 2, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(3, 'KG', 'Kilogram', 'kg', NULL, 1, NULL, NULL, 3, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(4, 'LITRE', 'Litre', 'L', NULL, 1, NULL, NULL, 4, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(5, 'GRAM', 'Gram', 'g', NULL, 1, NULL, NULL, 5, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(6, 'ML', 'Millilitre', 'ml', NULL, 1, NULL, NULL, 6, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(7, 'GLASS', 'Glass', 'gl', NULL, 1, NULL, NULL, 7, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(8, 'PLATE', 'Plate', 'plt', NULL, 1, NULL, NULL, 8, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(9, 'DOZEN', 'Dozen', 'dz', NULL, 0, NULL, NULL, 9, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(10, 'BOX', 'Box', 'box', NULL, 0, NULL, NULL, 10, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(11, 'CARTON', 'Carton', 'ctn', NULL, 0, NULL, NULL, 11, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(12, 'CRATE', 'Crate', 'crt', NULL, 0, NULL, NULL, 12, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(13, 'PACK', 'Pack', 'pk', NULL, 0, NULL, NULL, 13, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(14, 'SACK', 'Sack', 'sck', NULL, 0, NULL, NULL, 14, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(15, 'SET', 'Set', 'set', NULL, 0, NULL, NULL, 15, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` bigint(20) UNSIGNED DEFAULT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `can_create_users` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `role`, `is_super_admin`, `can_create_users`, `is_active`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `created_by`, `updated_by`, `deleted_at`, `role_id`, `department_id`) VALUES
(1, 'Barigye', 'Davis', 'barigyedavis6@gmail.com', NULL, '$2y$12$fwe8Dh49jYpyk8u.IzRYeO52v7mDATIHnbbUWs6.0bvaEj1otAwdq', 1, 1, 1, 1, '2026-05-12 10:19:14', NULL, '2026-04-14 17:37:47', '2026-05-12 10:19:14', NULL, NULL, NULL, NULL, NULL),
(2, 'BARIGYE', 'DAVIS', 'davisbarigye04@gmail.com', NULL, '$2y$12$Aie5KoiojnqluKnTg/vp3.Tdyf5TZYJVZEjHCp5KX7AbrXdZjJtvS', 7, 0, 0, 1, '2026-05-12 09:16:56', NULL, '2026-04-14 19:42:34', '2026-05-12 09:16:56', 1, 1, NULL, 11, 3),
(3, 'Kasibante', 'Julius', 'julius@gmail.com', NULL, '$2y$12$N60AXXenZWa4kDaRzaxSwud2ijFg.Q2M0jc965Bev3KdozC6mJsIq', 6, 0, 0, 1, '2026-05-12 11:57:17', NULL, '2026-04-14 20:44:17', '2026-05-12 11:57:17', 1, 1, NULL, NULL, 2),
(4, 'GENERAL', 'MANAGER', 'generalmanager@gmail.com', NULL, '$2y$12$59HDzVl1ntwJ4mSQw.2ts.gX6JaWOAxoSSYUvHFeqNvXwakZYbLyy', 3, 0, 1, 1, '2026-05-12 07:40:11', NULL, '2026-04-28 08:16:42', '2026-05-12 07:40:11', 1, NULL, NULL, NULL, 6),
(5, 'INNOCENT', 'MANAGER', 'innocentmanager@gmail.com', NULL, '$2y$12$vCIspZc7PzgloQ4V8ezeT.zheVO83i813UcqFRPAoui/c.A8bdP9u', 3, 0, 0, 1, '2026-04-28 10:24:06', NULL, '2026-04-28 10:22:24', '2026-04-28 10:24:06', 1, NULL, NULL, NULL, 6),
(6, 'SAMPLE', 'DATA', 'barigye@gmail.com', NULL, '$2y$12$3FaIgEZYg3QnhswkEMlzcex84a4sExr3QDJUmDrANp9T9SNheScI.', 6, 0, 0, 1, '2026-04-28 10:31:12', NULL, '2026-04-28 10:30:58', '2026-04-28 10:31:12', 1, NULL, NULL, NULL, 2),
(7, 'MANAGING', 'DIRECTOR', 'managingdirector@gmail.com', NULL, '$2y$12$4JhvUp5IsMMgcbUN80t1muWiLolVH6y9DbwJbL6eZD72xiW7ugMe.', 13, 0, 0, 1, '2026-05-12 03:55:09', NULL, '2026-04-29 11:10:16', '2026-05-12 03:55:09', 1, NULL, NULL, NULL, 7),
(8, 'kitchen', 'Manager', 'kitchen@gmail.com', NULL, '$2y$12$MY3R2I9VHGFiDymVvI8/YeScrKZFHZl03XUF/z7e8QEouQRHxRmz.', 4, 0, 0, 1, '2026-05-12 06:32:04', NULL, '2026-05-11 06:47:09', '2026-05-12 06:32:04', 1, 1, NULL, NULL, 8);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `alternative_phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Uganda',
  `tax_id` varchar(255) DEFAULT NULL COMMENT 'TIN / VAT registration number',
  `payment_method` enum('cash','bank','mobile') NOT NULL DEFAULT 'cash',
  `credit_limit` int(11) DEFAULT NULL COMMENT 'Maximum credit allowed',
  `status` enum('active','inactive','blacklisted') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_code`, `name`, `contact_person`, `email`, `phone`, `alternative_phone`, `address`, `city`, `country`, `tax_id`, `payment_method`, `credit_limit`, `status`, `notes`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'VEND-001', 'GREEN FARM LTD', 'Barigye Davis', 'barigyedavis6@gmail.com', '0777142030', '+256777142031', 'kasanga -Gabga Road', 'kampala', 'Uganda', '123445544265', 'cash', 5000000, 'active', 'will be supplying us with  vegetables forexample, tomatoes, cabbagges,apples', '2026-04-16 13:37:12', '2026-04-16 13:40:42', NULL, 3, 3),
(2, 'R001', 'Rwenzori', 'Rwenzori Companies', 'rwenzori2@gmail.com', '0777143020', NULL, NULL, 'Kampala', 'Uganda', NULL, 'cash', 0, 'active', NULL, '2026-05-06 23:35:23', '2026-05-06 23:35:23', NULL, 3, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_code_unique` (`code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`),
  ADD KEY `departments_default_store_id_foreign` (`default_store_id`),
  ADD KEY `departments_created_by_foreign` (`created_by`),
  ADD KEY `departments_updated_by_foreign` (`updated_by`),
  ADD KEY `departments_code_index` (`code`),
  ADD KEY `departments_department_type_id_index` (`department_type_id`),
  ADD KEY `departments_manager_id_index` (`manager_id`),
  ADD KEY `departments_is_active_index` (`is_active`);

--
-- Indexes for table `department_requisitions`
--
ALTER TABLE `department_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requisition_number` (`requisition_number`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `department_requisition_items`
--
ALTER TABLE `department_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_requisition_id` (`department_requisition_id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`);

--
-- Indexes for table `department_types`
--
ALTER TABLE `department_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_types_code_unique` (`code`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `goods_received_items`
--
ALTER TABLE `goods_received_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_received_items_goods_received_note_id_foreign` (`goods_received_note_id`),
  ADD KEY `goods_received_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  ADD KEY `goods_received_items_inventory_item_id_foreign` (`inventory_item_id`);

--
-- Indexes for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `goods_received_notes_grn_number_unique` (`grn_number`),
  ADD KEY `goods_received_notes_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `goods_received_notes_vendor_id_foreign` (`vendor_id`),
  ADD KEY `goods_received_notes_created_by_foreign` (`created_by`),
  ADD KEY `goods_received_notes_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_items_default_unit_of_measure_id_foreign` (`default_unit_of_measure_id`),
  ADD KEY `inventory_items_created_by_foreign` (`created_by`),
  ADD KEY `inventory_items_updated_by_foreign` (`updated_by`),
  ADD KEY `inventory_items_item_code_index` (`item_code`),
  ADD KEY `inventory_items_barcode_index` (`barcode`),
  ADD KEY `inventory_items_name_index` (`name`),
  ADD KEY `inventory_items_category_id_index` (`category_id`),
  ADD KEY `inventory_items_sub_category_id_index` (`sub_category_id`);

--
-- Indexes for table `inventory_update_notes`
--
ALTER TABLE `inventory_update_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_update_note_items`
--
ALTER TABLE `inventory_update_note_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_units`
--
ALTER TABLE `item_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_unit_unique` (`inventory_item_id`,`unit_of_measure_id`),
  ADD UNIQUE KEY `item_units_barcode_unique` (`barcode`),
  ADD KEY `item_units_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `item_units_unit_of_measure_id_index` (`unit_of_measure_id`),
  ADD KEY `item_units_is_base_unit_index` (`is_base_unit`),
  ADD KEY `item_units_barcode_index` (`barcode`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lpos`
--
ALTER TABLE `lpos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lpos_lpo_number_unique` (`lpo_number`),
  ADD KEY `lpos_requisition_id_foreign` (`requisition_id`),
  ADD KEY `lpos_vendor_id_foreign` (`vendor_id`),
  ADD KEY `lpos_created_by_foreign` (`created_by`),
  ADD KEY `lpos_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `lpo_items`
--
ALTER TABLE `lpo_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lpo_items_lpo_id_foreign` (`lpo_id`),
  ADD KEY `lpo_items_requisition_item_id_foreign` (`requisition_item_id`),
  ADD KEY `lpo_items_inventory_item_id_foreign` (`inventory_item_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_code_unique` (`code`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_ordered_by_foreign` (`ordered_by`),
  ADD KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  ADD KEY `purchase_orders_created_by_foreign` (`created_by`),
  ADD KEY `purchase_orders_updated_by_foreign` (`updated_by`),
  ADD KEY `purchase_orders_po_number_index` (`po_number`),
  ADD KEY `purchase_orders_vendor_id_index` (`vendor_id`),
  ADD KEY `purchase_orders_store_id_index` (`store_id`),
  ADD KEY `purchase_orders_status_index` (`status`),
  ADD KEY `purchase_orders_po_date_index` (`po_date`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_created_by_foreign` (`created_by`),
  ADD KEY `purchase_order_items_updated_by_foreign` (`updated_by`),
  ADD KEY `purchase_order_items_purchase_order_id_index` (`purchase_order_id`),
  ADD KEY `purchase_order_items_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `purchase_order_items_unit_id_index` (`unit_id`);

--
-- Indexes for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requisitions_requisition_number_unique` (`requisition_number`),
  ADD KEY `requisitions_store_id_foreign` (`store_id`),
  ADD KEY `requisitions_requested_by_foreign` (`requested_by`),
  ADD KEY `requisitions_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_items_requisition_id_foreign` (`requisition_id`),
  ADD KEY `requisition_items_inventory_item_id_foreign` (`inventory_item_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_code_unique` (`code`),
  ADD KEY `roles_created_by_foreign` (`created_by`),
  ADD KEY `roles_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_balances_unique` (`inventory_item_id`,`store_id`,`balance_date`),
  ADD KEY `stock_balances_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `stock_balances_store_id_index` (`store_id`),
  ADD KEY `stock_balances_balance_date_index` (`balance_date`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_movements_movement_number_unique` (`movement_number`),
  ADD KEY `stock_movements_approved_by_foreign` (`approved_by`),
  ADD KEY `stock_movements_reversed_by_movement_id_foreign` (`reversed_by_movement_id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`),
  ADD KEY `stock_movements_updated_by_foreign` (`updated_by`),
  ADD KEY `stock_movements_movement_number_index` (`movement_number`),
  ADD KEY `stock_movements_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `stock_movements_store_id_index` (`store_id`),
  ADD KEY `stock_movements_movement_type_id_index` (`movement_type_id`),
  ADD KEY `stock_movements_department_id_index` (`department_id`),
  ADD KEY `stock_movements_movement_date_index` (`movement_date`),
  ADD KEY `stock_movements_unit_id_index` (`unit_id`),
  ADD KEY `stock_movements_purchase_order_id_index` (`purchase_order_id`),
  ADD KEY `stock_movements_goods_received_note_id_index` (`goods_received_note_id`),
  ADD KEY `stock_movements_is_reversed_index` (`is_reversed`);

--
-- Indexes for table `stock_movement_types`
--
ALTER TABLE `stock_movement_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_movement_types_code_unique` (`code`),
  ADD KEY `stock_movement_types_code_index` (`code`),
  ADD KEY `stock_movement_types_sign_index` (`sign`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stores_code_unique` (`code`),
  ADD KEY `stores_created_by_foreign` (`created_by`),
  ADD KEY `stores_updated_by_foreign` (`updated_by`),
  ADD KEY `stores_code_index` (`code`),
  ADD KEY `stores_store_type_id_index` (`store_type_id`),
  ADD KEY `stores_manager_id_index` (`manager_id`),
  ADD KEY `stores_is_active_index` (`is_active`);

--
-- Indexes for table `store_types`
--
ALTER TABLE `store_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_types_code_unique` (`code`),
  ADD KEY `store_types_code_index` (`code`),
  ADD KEY `store_types_is_active_index` (`is_active`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_categories_code_unique` (`code`),
  ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `units_of_measure_code_unique` (`code`),
  ADD KEY `units_of_measure_code_index` (`code`),
  ADD KEY `units_of_measure_is_active_index` (`is_active`),
  ADD KEY `units_of_measure_is_base_unit_index` (`is_base_unit`),
  ADD KEY `units_of_measure_base_unit_id_index` (`base_unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_created_by_foreign` (`created_by`),
  ADD KEY `users_updated_by_foreign` (`updated_by`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `fk_users_role` (`role`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_vendor_code_unique` (`vendor_code`),
  ADD KEY `vendors_created_by_foreign` (`created_by`),
  ADD KEY `vendors_updated_by_foreign` (`updated_by`),
  ADD KEY `vendors_vendor_code_index` (`vendor_code`),
  ADD KEY `vendors_name_index` (`name`),
  ADD KEY `vendors_status_index` (`status`),
  ADD KEY `vendors_email_index` (`email`),
  ADD KEY `vendors_phone_index` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `department_requisitions`
--
ALTER TABLE `department_requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `department_requisition_items`
--
ALTER TABLE `department_requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `department_types`
--
ALTER TABLE `department_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_items`
--
ALTER TABLE `goods_received_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `inventory_update_notes`
--
ALTER TABLE `inventory_update_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_update_note_items`
--
ALTER TABLE `inventory_update_note_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_units`
--
ALTER TABLE `item_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lpos`
--
ALTER TABLE `lpos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lpo_items`
--
ALTER TABLE `lpo_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `stock_balances`
--
ALTER TABLE `stock_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `stock_movement_types`
--
ALTER TABLE `stock_movement_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `store_types`
--
ALTER TABLE `store_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_default_store_id_foreign` FOREIGN KEY (`default_store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_department_type_id_foreign` FOREIGN KEY (`department_type_id`) REFERENCES `department_types` (`id`),
  ADD CONSTRAINT `departments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `departments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `department_requisitions`
--
ALTER TABLE `department_requisitions`
  ADD CONSTRAINT `department_requisitions_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `department_requisitions_ibfk_2` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `department_requisitions_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `department_requisition_items`
--
ALTER TABLE `department_requisition_items`
  ADD CONSTRAINT `department_requisition_items_ibfk_1` FOREIGN KEY (`department_requisition_id`) REFERENCES `department_requisitions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `department_requisition_items_ibfk_2` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`);

--
-- Constraints for table `goods_received_items`
--
ALTER TABLE `goods_received_items`
  ADD CONSTRAINT `goods_received_items_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goods_received_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `goods_received_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`);

--
-- Constraints for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD CONSTRAINT `goods_received_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `goods_received_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goods_received_notes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `goods_received_notes_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD CONSTRAINT `inventory_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_items_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `item_units`
--
ALTER TABLE `item_units`
  ADD CONSTRAINT `item_units_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_units_unit_of_measure_id_foreign` FOREIGN KEY (`unit_of_measure_id`) REFERENCES `units_of_measure` (`id`);

--
-- Constraints for table `lpos`
--
ALTER TABLE `lpos`
  ADD CONSTRAINT `lpos_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lpos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `lpos_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`),
  ADD CONSTRAINT `lpos_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `lpo_items`
--
ALTER TABLE `lpo_items`
  ADD CONSTRAINT `lpo_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `lpo_items_lpo_id_foreign` FOREIGN KEY (`lpo_id`) REFERENCES `lpos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lpo_items_requisition_item_id_foreign` FOREIGN KEY (`requisition_item_id`) REFERENCES `requisition_items` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_ordered_by_foreign` FOREIGN KEY (`ordered_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `purchase_orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `purchase_orders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_orders_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_order_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `item_units` (`id`),
  ADD CONSTRAINT `purchase_order_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD CONSTRAINT `requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requisitions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requisitions_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD CONSTRAINT `requisition_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `requisition_items_requisition_id_foreign` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `roles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD CONSTRAINT `stock_balances_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `stock_balances_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`);

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `stock_movements_movement_type_id_foreign` FOREIGN KEY (`movement_type_id`) REFERENCES `stock_movement_types` (`id`),
  ADD CONSTRAINT `stock_movements_reversed_by_movement_id_foreign` FOREIGN KEY (`reversed_by_movement_id`) REFERENCES `stock_movements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`),
  ADD CONSTRAINT `stock_movements_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `item_units` (`id`),
  ADD CONSTRAINT `stock_movements_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stores_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stores_store_type_id_foreign` FOREIGN KEY (`store_type_id`) REFERENCES `store_types` (`id`),
  ADD CONSTRAINT `stores_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  ADD CONSTRAINT `units_of_measure_base_unit_id_foreign` FOREIGN KEY (`base_unit_id`) REFERENCES `units_of_measure` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendors_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
