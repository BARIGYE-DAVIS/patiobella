-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2026 at 08:20 PM
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
(21, 'BEEF01', 'Beef', 'Cows meat', 0, 1, '2026-05-08 11:06:09', '2026-05-08 11:06:09');

-- --------------------------------------------------------

--
-- Table structure for table `cost_price_history`
--

CREATE TABLE `cost_price_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `old_unit_cost` decimal(15,2) DEFAULT NULL,
  `new_unit_cost` decimal(15,2) NOT NULL,
  `pack_type` varchar(50) DEFAULT NULL COMMENT 'carton, crate, box, etc.',
  `pack_size` int(11) DEFAULT NULL,
  `number_of_packs` int(11) DEFAULT NULL,
  `total_base_units` decimal(15,6) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cost_price_history`
--

INSERT INTO `cost_price_history` (`id`, `inventory_item_id`, `old_unit_cost`, `new_unit_cost`, `pack_type`, `pack_size`, `number_of_packs`, `total_base_units`, `reason`, `changed_by`, `created_at`, `updated_at`) VALUES
(1, 28, 49999.99, 55000.00, NULL, NULL, NULL, NULL, 'price change from suplier', 3, '2026-05-14 10:44:54', '2026-05-14 10:44:54'),
(2, 18, 5.00, 300.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-14 10:45:46', '2026-05-14 10:45:46'),
(3, 44, 0.00, 35000.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-18 09:06:09', '2026-05-18 09:06:09'),
(4, 28, 55000.00, 2500.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-18 09:08:15', '2026-05-18 09:08:15'),
(5, 45, 0.00, 350.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-19 06:46:31', '2026-05-19 06:46:31'),
(6, 24, 9999.99, 999.99, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-27 07:37:00', '2026-05-27 07:37:00'),
(7, 24, 999.99, 1000.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-27 07:37:23', '2026-05-27 07:37:23'),
(8, 38, 10000.00, 1000.01, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-27 09:13:12', '2026-05-27 09:13:12'),
(9, 38, 1000.01, 1000.00, NULL, NULL, NULL, NULL, 'Manual cost update', 3, '2026-05-27 09:13:22', '2026-05-27 09:13:22');

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
(5, 'CAF001', 'CAFE', 'ROOM3', 'This is cafe', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-17 04:00:04', '2026-05-26 04:43:48', '2026-05-26 04:43:48', 1, NULL),
(6, 'GD001', 'GENERAL MANAGEMENT', 'ROOM3C', 'This is for general operations', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-28 08:14:53', '2026-04-28 09:00:06', NULL, 1, 1),
(7, 'DR001', 'DIRECTORS', 'ROOM2C', 'This is dirctors department', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-04-29 11:05:23', '2026-04-29 11:17:39', NULL, 1, 1),
(8, 'KIT001', 'KITCHEN', 'ROOM2C', 'This is kitchech', NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-05-11 06:45:47', '2026-05-11 06:45:47', NULL, 1, NULL),
(9, 'R001', 'CAFE', 'ROOM2C', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2026-05-13 12:14:45', '2026-05-26 04:44:04', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `department_requisitions`
--

CREATE TABLE `department_requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_number` varchar(50) NOT NULL,
  `requisition_type` enum('daily','weekly','monthly') NOT NULL DEFAULT 'daily',
  `department_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Requesting department (Kitchen, Bar, Cafe)',
  `requested_by` bigint(20) UNSIGNED NOT NULL COMMENT 'User who created request',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Store manager who approved',
  `date_needed` date DEFAULT NULL,
  `status` enum('pending','approved','partially_issued','issued','partially_consumed','fully_consumed','completed','partially_returned','returned','rejected','cancelled') DEFAULT 'pending',
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

INSERT INTO `department_requisitions` (`id`, `requisition_number`, `requisition_type`, `department_id`, `requested_by`, `approved_by`, `date_needed`, `status`, `store_notes`, `taken_by`, `returned_by`, `department_notes`, `rejection_reason`, `approved_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'KIT-REQ-20260511-7208', 'daily', 8, 8, 2, NULL, 'partially_returned', 'Taken by Ivan', 'Barigye', NULL, NULL, NULL, '2026-05-11 08:52:45', '2026-05-11 08:05:12', '2026-05-11 10:13:18', NULL),
(2, 'KIT-REQ-20260511-0390', 'daily', 8, 8, 2, NULL, 'partially_returned', NULL, 'Barigye', 'James', NULL, NULL, '2026-05-11 11:25:04', '2026-05-11 11:22:23', '2026-05-11 14:12:29', NULL),
(3, 'KIT-REQ-20260511-3177', 'daily', 8, 8, 2, '2026-05-11', 'partially_returned', NULL, 'Barigye', 'James', NULL, NULL, '2026-05-11 11:56:24', '2026-05-11 11:55:21', '2026-05-11 13:12:09', NULL),
(4, 'KIT-REQ-20260511-6256', 'daily', 8, 8, 2, '2026-05-12', 'partially_issued', NULL, NULL, NULL, 'Please its urgent', NULL, '2026-05-11 14:09:23', '2026-05-11 14:05:03', '2026-05-11 14:10:04', NULL),
(5, 'KIT-REQ-20260512-0093', 'daily', 8, 8, 2, '2026-05-12', 'completed', NULL, NULL, 'BARIGYE DAVIS', 'we need thes items , today', NULL, '2026-05-12 06:38:33', '2026-05-12 06:34:31', '2026-05-14 13:19:11', NULL),
(6, 'KIT-REQ-20260512-1915', 'daily', 8, 8, 2, '2026-05-12', 'partially_returned', NULL, NULL, 'James', NULL, NULL, '2026-05-12 06:52:55', '2026-05-12 06:52:40', '2026-05-13 12:08:23', NULL),
(7, 'KIT-REQ-20260513-4130', 'daily', 8, 8, 2, '2026-05-14', 'partially_returned', NULL, NULL, 'BARIGYE DAVIS', NULL, NULL, '2026-05-13 12:19:36', '2026-05-13 12:19:07', '2026-05-13 12:31:05', NULL),
(8, 'REST-REQ-20260513-7586', 'daily', 9, 9, 2, '2026-05-13', 'partially_returned', NULL, NULL, 'James', 'please we need the followin items in the resturant', NULL, '2026-05-13 14:18:59', '2026-05-13 14:18:29', '2026-05-14 04:03:16', NULL),
(9, 'REST-REQ-20260515-5414', 'daily', 9, 9, 2, '2026-05-15', 'completed', NULL, NULL, 'BARIGYE DAVIS', NULL, NULL, '2026-05-15 04:14:05', '2026-05-15 04:13:43', '2026-05-15 04:27:13', NULL),
(10, 'REST-REQ-20260515-4325', 'daily', 9, 9, 2, '2026-05-15', 'completed', 'please make sure you return unsold items back to store', NULL, 'BARIGYE DAVIS', 'please we are out of the following items', NULL, '2026-05-15 05:06:10', '2026-05-15 05:05:49', '2026-05-15 06:01:48', NULL),
(11, 'REST-REQ-20260515-9891', 'daily', 9, 9, 2, '2026-05-15', 'partially_issued', NULL, 'DANIEL', NULL, NULL, NULL, '2026-05-15 06:18:13', '2026-05-15 06:17:56', '2026-05-15 06:18:46', NULL),
(12, 'REST-REQ-20260515-8720', 'daily', 9, 9, 2, '2026-05-15', 'issued', NULL, 'DANIEL', NULL, NULL, NULL, '2026-05-15 07:06:19', '2026-05-15 07:06:07', '2026-05-15 07:06:42', NULL),
(13, 'BAR-REQ-20260518-4151', 'daily', 4, 12, 2, NULL, 'issued', NULL, 'DANIEL', NULL, NULL, NULL, '2026-05-18 06:46:16', '2026-05-18 03:49:23', '2026-05-18 06:47:09', NULL),
(14, 'REST-REQ-20260519-7060', 'daily', 9, 9, 2, '2026-05-19', 'partially_consumed', NULL, 'DANIEL', NULL, NULL, NULL, '2026-05-19 10:35:34', '2026-05-19 10:33:59', '2026-05-19 10:41:37', NULL),
(15, 'REST-REQ-20260520-2645', 'daily', 9, 9, 2, '2026-05-21', 'partially_issued', NULL, 'BARIGYE DAVIS', NULL, NULL, NULL, '2026-05-20 11:54:58', '2026-05-20 11:49:43', '2026-05-20 11:55:59', NULL),
(16, 'REST-REQ-20260521-3811', 'daily', 9, 9, 2, '2026-05-22', 'partially_issued', NULL, 'BARIGYE DAVIS', NULL, NULL, NULL, '2026-05-21 15:15:51', '2026-05-21 15:15:07', '2026-05-21 15:23:33', NULL),
(17, 'KIT-REQ-20260521-8604', 'weekly', 8, 8, 2, '2026-05-22', 'partially_issued', NULL, 'DANIEL', NULL, NULL, NULL, '2026-05-21 15:30:09', '2026-05-21 15:26:26', '2026-05-21 15:31:19', NULL),
(18, 'KIT-REQ-20260522-5887', 'daily', 8, 8, 4, '2026-05-22', 'issued', NULL, 'James', NULL, NULL, NULL, '2026-05-22 05:18:24', '2026-05-22 04:07:33', '2026-05-22 05:43:54', NULL),
(19, 'REST-REQ-20260522-3481', 'weekly', 9, 9, NULL, '2026-05-23', 'rejected', NULL, NULL, NULL, NULL, 'please visit my office', NULL, '2026-05-22 06:09:25', '2026-05-22 06:16:57', NULL),
(20, 'KIT-REQ-20260522-1278', 'weekly', 8, 8, 4, '2026-05-23', 'approved', 'Beef mince is not availble at the moment please', NULL, NULL, NULL, NULL, '2026-05-22 06:27:44', '2026-05-22 06:26:07', '2026-05-22 06:27:44', NULL),
(21, 'KIT-REQ-20260522-6953', 'weekly', 8, 8, 4, '2026-05-22', 'partially_issued', 'These are the items we have at  the moment', 'James', NULL, NULL, NULL, '2026-05-22 12:40:11', '2026-05-22 12:37:12', '2026-05-22 12:41:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department_requisition_items`
--

CREATE TABLE `department_requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_requisition_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_requested` decimal(15,2) NOT NULL,
  `quantity_approved` decimal(15,2) DEFAULT 0.00 COMMENT 'Quantity approved by management',
  `approved_pack_type` varchar(50) DEFAULT NULL COMMENT 'Pack type approved by management',
  `approved_pack_size` int(11) DEFAULT NULL COMMENT 'Pack size approved by management',
  `approved_metrics` varchar(50) DEFAULT NULL COMMENT 'Metrics approved by management',
  `approval_notes` text DEFAULT NULL COMMENT 'Notes from management approval',
  `requested_pack_type` varchar(50) DEFAULT NULL COMMENT 'carton, crate, dozen, box',
  `requested_pack_size` int(11) DEFAULT NULL COMMENT 'pieces per pack (e.g., 24)',
  `quantity_issued` decimal(15,2) DEFAULT 0.00,
  `quantity_sold` decimal(15,2) NOT NULL DEFAULT 0.00,
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
  `last_consumed_at` timestamp NULL DEFAULT NULL,
  `last_sold_at` timestamp NULL DEFAULT NULL,
  `metrics` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `empty_bottle_weight` decimal(15,6) DEFAULT 0.000000 COMMENT 'Weight of empty bottle/container for this issuance',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_requisition_items`
--

INSERT INTO `department_requisition_items` (`id`, `department_requisition_id`, `inventory_item_id`, `quantity_requested`, `quantity_approved`, `approved_pack_type`, `approved_pack_size`, `approved_metrics`, `approval_notes`, `requested_pack_type`, `requested_pack_size`, `quantity_issued`, `quantity_sold`, `issued_pack_type`, `issued_pack_size`, `issued_total_pieces`, `quantity_returned`, `returned_pack_type`, `returned_pack_size`, `returned_total_pieces`, `return_reason`, `returned_at`, `quantity_consumed`, `last_consumed_at`, `last_sold_at`, `metrics`, `notes`, `empty_bottle_weight`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 20.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, 0.00, NULL, NULL, 20.00, 10.00, NULL, NULL, 10.00, NULL, '2026-05-11 10:53:46', 10.00, NULL, NULL, 'kg', NULL, 0.000000, '2026-05-11 08:05:12', '2026-05-11 10:53:46'),
(2, 1, 21, 15.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 15.00, 0.00, NULL, NULL, 15.00, 5.00, NULL, NULL, 5.00, 'not used', '2026-05-11 10:13:18', 10.00, NULL, NULL, 'kg', NULL, 0.000000, '2026-05-11 08:05:12', '2026-05-11 10:13:18'),
(3, 2, 18, 10.00, 0.00, NULL, NULL, NULL, NULL, 'dozen', 12, 10.00, 0.00, NULL, 12, 10.00, 0.00, NULL, NULL, 3.00, NULL, '2026-05-11 14:12:29', 7.00, NULL, NULL, 'pcs', NULL, 0.000000, '2026-05-11 11:22:23', '2026-05-11 14:12:29'),
(4, 3, 38, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 0.00, 'carton', 12, 60.00, 0.00, NULL, NULL, 30.00, NULL, '2026-05-11 13:12:08', 30.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-11 11:55:21', '2026-05-11 13:12:08'),
(5, 4, 21, 20.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 2.00, 0.00, NULL, NULL, 2.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, 'kg', NULL, 0.000000, '2026-05-11 14:05:03', '2026-05-11 14:14:42'),
(6, 4, 7, 20.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 4.00, 0.00, NULL, NULL, 4.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, 'kg', NULL, 0.000000, '2026-05-11 14:05:03', '2026-05-11 14:14:42'),
(7, 5, 7, 20.00, 0.00, NULL, NULL, NULL, NULL, 'pack', NULL, 20.00, 0.00, NULL, NULL, 20.00, 0.00, NULL, NULL, 10.00, 'not  used', '2026-05-14 13:19:11', 10.00, NULL, NULL, 'kg', 'i want 20 kgs of black beans\n2026-05-14 15:01 - Kitchen consumed: 10 pieces (10 pieces)', 0.000000, '2026-05-12 06:34:31', '2026-05-14 13:19:11'),
(8, 5, 1, 20.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 15.00, 0.00, NULL, NULL, 15.00, 0.00, NULL, NULL, 10.00, 'not  used', '2026-05-14 13:19:11', 5.00, NULL, NULL, 'kg', '2026-05-14 15:01 - Kitchen consumed: 5 pieces (5 pieces)', 0.000000, '2026-05-12 06:34:31', '2026-05-14 13:19:11'),
(9, 6, 24, 20.00, 0.00, NULL, NULL, NULL, NULL, 'bottle', NULL, 20.00, 0.00, NULL, NULL, 20.00, 10.00, 'unit', 1, 10.00, 'not sold out', '2026-05-13 12:08:22', 10.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-12 06:52:40', '2026-05-13 12:08:22'),
(10, 7, 21, 20.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, 0.00, NULL, NULL, 20.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, 'kg', NULL, 0.000000, '2026-05-13 12:19:07', '2026-05-13 12:20:26'),
(11, 7, 38, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 0.00, 'carton', 12, 60.00, 2.00, 'carton', 12, 24.00, 'Not sold out', '2026-05-13 12:31:05', 36.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-13 12:19:07', '2026-05-13 12:31:05'),
(12, 8, 38, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 0.00, 'carton', 12, 60.00, 1.00, 'carton', 12, 12.00, 'not sold out', '2026-05-14 04:03:16', 48.00, NULL, NULL, 'bottles', 'urgently needed', 0.000000, '2026-05-13 14:18:29', '2026-05-14 04:03:16'),
(13, 9, 24, 4.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 4.00, 0.00, 'carton', 12, 48.00, 0.00, NULL, NULL, 3.00, 'Not sold out', '2026-05-15 04:27:13', 45.00, '2026-05-15 04:25:34', NULL, 'bottles', '2026-05-15 07:25 - Restaurant consumed: 24 bottles', 0.000000, '2026-05-15 04:13:43', '2026-05-15 04:27:13'),
(14, 9, 38, 6.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 6.00, 0.00, 'carton', 12, 72.00, 0.00, NULL, NULL, 11.00, 'Not sold out', '2026-05-15 04:27:13', 61.00, '2026-05-15 04:25:34', NULL, 'bottles', '2026-05-15 07:25 - Restaurant consumed: 42 bottles', 0.000000, '2026-05-15 04:13:43', '2026-05-15 04:27:13'),
(15, 10, 38, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 0.00, 'carton', 12, 60.00, 5.00, 'carton', 12, 60.00, 'Not sold out', '2026-05-15 06:01:48', 0.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-15 05:05:49', '2026-05-15 06:01:48'),
(16, 10, 42, 5.00, 0.00, NULL, NULL, NULL, NULL, 'box', 24, 5.00, 0.00, 'carton', 24, 120.00, 5.00, 'carton', 24, 120.00, 'Not sold out', '2026-05-15 06:01:48', 0.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-15 05:05:49', '2026-05-15 06:01:48'),
(17, 10, 24, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 0.00, 'carton', 11, 55.00, 5.00, 'carton', 11, 55.00, 'Not sold out', '2026-05-15 06:01:48', 0.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-15 05:05:49', '2026-05-15 06:01:48'),
(18, 11, 38, 2.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 1.00, 2.00, 'carton', 12, 12.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, '2026-05-18 01:58:01', 'bottles', NULL, 0.000000, '2026-05-15 06:17:56', '2026-05-18 01:58:01'),
(19, 12, 24, 5.00, 0.00, NULL, NULL, NULL, NULL, 'carton', 12, 5.00, 4.00, 'carton', 12, 60.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, '2026-05-15 14:09:56', 'bottles', NULL, 0.000000, '2026-05-15 07:06:07', '2026-05-15 14:09:56'),
(20, 13, 28, 5.00, 0.00, NULL, NULL, NULL, NULL, 'crate', 12, 5.00, 9.00, 'crate', 12, 60.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, '2026-05-18 12:44:34', 'bottles', NULL, 0.000000, '2026-05-18 03:49:23', '2026-05-18 12:44:34'),
(21, 13, 44, 10.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, 1.00, NULL, NULL, 10.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 3.00, '2026-05-27 12:33:53', '2026-05-18 12:44:34', 'bottles', NULL, 0.000000, '2026-05-18 03:49:23', '2026-05-27 12:33:53'),
(22, 14, 43, 2.00, 0.00, NULL, NULL, NULL, NULL, 'crate', 12, 2.00, 0.00, NULL, NULL, 2.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 1.00, '2026-05-19 10:41:37', NULL, 'bottles', '2026-05-19 13:41 - Restaurant consumed: 1 bottles', 0.000000, '2026-05-19 10:33:59', '2026-05-19 10:41:37'),
(23, 15, 24, 30.00, 0.00, NULL, NULL, NULL, NULL, 'bottle', NULL, 25.00, 0.00, NULL, NULL, 25.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, 'bottles', NULL, 0.000000, '2026-05-20 11:49:43', '2026-05-20 11:55:59'),
(24, 16, 24, 50.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 45.00, 0.00, NULL, NULL, 45.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-21 15:15:07', '2026-05-21 15:23:33'),
(25, 16, 38, 60.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 50.00, 0.00, NULL, NULL, 50.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-21 15:15:07', '2026-05-21 15:23:33'),
(26, 17, 99, 60.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 60.00, 0.00, NULL, NULL, 60.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-21 15:26:27', '2026-05-21 15:31:19'),
(27, 17, 212, 60.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 50.00, 0.00, NULL, NULL, 50.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-21 15:26:27', '2026-05-21 15:31:19'),
(28, 17, 229, 50.02, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 50.02, 0.00, NULL, NULL, 50.02, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-21 15:26:27', '2026-05-21 15:31:19'),
(29, 18, 95, 10.00, 10.00, NULL, NULL, 'portion', NULL, NULL, NULL, 10.00, 0.00, NULL, NULL, 10.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 04:07:33', '2026-05-22 05:43:54'),
(30, 18, 229, 25.00, 25.00, NULL, NULL, 'kg', NULL, NULL, NULL, 25.00, 0.00, NULL, NULL, 25.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 04:07:33', '2026-05-22 05:43:54'),
(31, 19, 24, 100.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 06:09:26', '2026-05-22 06:09:26'),
(32, 19, 38, 150.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 06:09:26', '2026-05-22 06:09:26'),
(33, 20, 157, 100.00, 100.00, NULL, NULL, 'portion', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 06:26:07', '2026-05-22 06:27:44'),
(34, 20, 21, 150.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 06:26:07', '2026-05-22 06:26:07'),
(35, 20, 53, 30.00, 30.00, NULL, NULL, 'kg', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 06:26:07', '2026-05-22 06:27:44'),
(36, 21, 89, 300.00, 50.00, NULL, NULL, 'kg', NULL, NULL, NULL, 50.00, 0.00, NULL, NULL, 50.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 12:37:12', '2026-05-22 12:41:11'),
(37, 21, 229, 35.00, 10.00, NULL, NULL, 'kg', NULL, NULL, NULL, 10.00, 0.00, NULL, NULL, 10.00, 0.00, NULL, NULL, 0.00, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, 0.000000, '2026-05-22 12:37:12', '2026-05-22 12:41:11');

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
(12, 9, 61, 42, 30.00, 30.00, 30.00, 'box', 12, 30, 360.000000, 'bottle', 0.00, NULL, 20000.00, 600000.00, 600000.00, '5 not deleivered', 3, 2, '2026-05-12 05:27:02', '2026-05-12 05:28:15'),
(13, 10, 62, 38, 15.00, 10.00, 10.00, 'carton', 12, 10, 120.000000, 'bottle', 0.00, NULL, 10000.00, 150000.00, 100000.00, NULL, 3, 2, '2026-05-19 10:28:53', '2026-05-19 10:30:23'),
(14, 10, 63, 43, 10.00, 10.00, 10.00, 'crate', 12, 10, 120.000000, 'bottle', 0.00, NULL, 11000.00, 110000.00, 110000.00, NULL, 3, 2, '2026-05-19 10:28:53', '2026-05-19 10:30:23'),
(15, 11, 28, 9, 15.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 2000.00, 30000.00, 0.00, NULL, 3, NULL, '2026-05-22 11:47:16', '2026-05-22 11:47:16'),
(16, 11, 29, 18, 15.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 7500.00, 112500.00, 0.00, NULL, 3, NULL, '2026-05-22 11:47:16', '2026-05-22 11:47:16'),
(17, 11, 30, 1, 15.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 120000.00, 1800000.00, 0.00, NULL, 3, NULL, '2026-05-22 11:47:16', '2026-05-22 11:47:16'),
(18, 12, 13, 9, 15.00, 15.00, 15.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 2000.00, 30000.00, 30000.00, NULL, 3, NULL, '2026-05-22 11:48:33', '2026-05-22 11:48:33'),
(19, 12, 14, 18, 15.00, 15.00, 15.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 7500.00, 112500.00, 112500.00, NULL, 3, NULL, '2026-05-22 11:48:33', '2026-05-22 11:48:33'),
(20, 12, 15, 1, 15.00, 15.00, 15.00, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 120000.00, 1800000.00, 1800000.00, NULL, 3, NULL, '2026-05-22 11:48:33', '2026-05-22 11:48:33');

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
(9, 'GRN-20260512-6437', 29, 2, '2026-05-12', NULL, 600000.00, 600000.00, 0.00, 0.00, '10 items were not delivered, as according to the purchorder placed', 'inventory_updated', 3, 2, '2026-05-12 05:27:02', '2026-05-12 05:28:15', NULL),
(10, 'GRN-20260519-7336', 30, 1, '2026-05-19', NULL, 260000.00, 210000.00, 0.00, 0.00, NULL, 'inventory_updated', 3, 2, '2026-05-19 10:28:53', '2026-05-19 10:30:23', NULL),
(11, 'GRN-20260522-9878', 14, 1, '2026-05-22', NULL, 1942500.00, 0.00, 0.00, 0.00, NULL, 'completed', 3, NULL, '2026-05-22 11:47:16', '2026-05-22 11:47:16', NULL),
(12, 'GRN-20260522-7435', 9, 1, '2026-05-22', NULL, 1942500.00, 1942500.00, 0.00, 0.00, NULL, 'completed', 3, NULL, '2026-05-22 11:48:33', '2026-05-22 11:48:33', NULL);

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
  `empty_bottle_weight` decimal(15,6) DEFAULT 0.000000 COMMENT 'Weight of empty bottle/container in kg',
  `minimum_stock` decimal(15,6) DEFAULT 0.000000,
  `maximum_stock` decimal(15,6) DEFAULT 0.000000,
  `reorder_quantity` decimal(15,6) DEFAULT 0.000000,
  `unit_cost` decimal(15,2) DEFAULT 0.00,
  `last_purchase_price` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `is_sellable` tinyint(1) NOT NULL DEFAULT 0,
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

INSERT INTO `inventory_items` (`id`, `created_at`, `updated_at`, `is_active`, `deleted_at`, `item_code`, `barcode`, `name`, `description`, `category_id`, `sub_category_id`, `default_unit_of_measure_id`, `base_unit`, `empty_bottle_weight`, `minimum_stock`, `maximum_stock`, `reorder_quantity`, `unit_cost`, `last_purchase_price`, `selling_price`, `is_sellable`, `is_perishable`, `is_taxable`, `shelf_life_days`, `storage_conditions`, `manufacturer`, `brand`, `notes`, `created_by`, `updated_by`, `current_stock`) VALUES
(1, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'RICE-001', '8901234567890', 'Basmati Rice', 'Premium long grain basmati rice', 11, NULL, NULL, 'pcs', 0.000000, 50.000000, 500.000000, 100.000000, 4000.00, 85.00, 120.00, 0, 0, 1, 365, 'Cool dry place', 'India Gate', 'India Gate', 'Best for biryani', 1, 4, 265.000000),
(2, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'RICE-002', '8901234567891', 'Super Rice', 'Local super quality rice', 11, NULL, NULL, 'pcs', 0.000000, 100.000000, 1000.000000, 200.000000, 65.00, 65.00, 90.00, 0, 0, 1, 365, 'Cool dry place', 'Tilda', 'Tilda', 'Daily use', 1, 4, 500.000000),
(3, '2026-04-16 13:06:21', '2026-05-15 09:35:52', 1, NULL, 'FLOUR-001', '8901234567892', 'Wheat Flour', 'Fine wheat flour for chapati and bread', 11, NULL, NULL, 'pcs', 0.000000, 30.000000, 300.000000, 50.000000, 45.00, 45.00, 65.00, 0, 0, 1, 180, 'Cool dry place', 'Pembe', 'Pembe', 'All purpose flour', 1, 4, 150.000000),
(4, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'FLOUR-002', '8901234567893', 'Maize Flour', 'Fine maize flour for ugali/posho', 11, NULL, NULL, 'pcs', 0.000000, 40.000000, 400.000000, 80.000000, 40.00, 40.00, 60.00, 0, 0, 1, 180, 'Cool dry place', 'Jogoo', 'Jogoo', 'Local favorite', 1, 4, 200.000000),
(5, '2026-04-16 13:06:21', '2026-05-15 09:35:43', 1, NULL, 'SUGAR-001', '8901234567894', 'White Sugar', 'Fine granulated white sugar', 11, NULL, NULL, 'pcs', 0.000000, 50.000000, 500.000000, 100.000000, 55.00, 55.00, 80.00, 0, 0, 1, 365, 'Cool dry place', 'Kinyara', 'Kinyara', 'Standard sugar', 1, 4, 300.000000),
(6, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'BEANS-001', '8901234567895', 'Kidney Beans', 'Red kidney beans', 11, NULL, NULL, 'pcs', 0.000000, 30.000000, 300.000000, 60.000000, 45.00, 45.00, 70.00, 0, 0, 1, 365, 'Cool dry place', 'Local', 'Local', 'Dried beans', 1, 4, 120.000000),
(7, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'BEANS-002', '8901234567896', 'Black Beans', 'Black turtle beans', 11, NULL, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 3500.00, 50.00, 75.00, 0, 0, 1, 365, 'Cool dry place', 'Local', 'Local', 'Dried black beans', 1, 4, 105.000000),
(8, '2026-04-16 13:06:21', '2026-05-15 09:36:04', 1, NULL, 'PASTA-001', '8901234567897', 'Spaghetti', 'Italian spaghetti pasta', 11, NULL, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 35.00, 35.00, 55.00, 0, 0, 1, 365, 'Cool dry place', 'Barilla', 'Barilla', 'No.5 pasta', 1, 4, 100.000000),
(9, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'OIL-001', '8901234567898', 'Cooking Oil', 'Vegetable cooking oil', 12, NULL, 'pcs', 'litre', 0.000000, 40.000000, 400.000000, 60.000000, 25000.00, 25000.00, 180.00, 0, 0, 1, 365, 'Cool dark place', 'Mukwano', 'Mukwano', 'Sunflower oil', 1, 4, 185.000000),
(10, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'OIL-002', '8901234567899', 'Olive Oil', 'Extra virgin olive oil', 12, NULL, NULL, 'pcs', 0.000000, 10.000000, 100.000000, 20.000000, 350.00, 350.00, 500.00, 0, 0, 1, 365, 'Cool dark place', 'Bertolli', 'Bertolli', 'Premium quality', 1, 4, 50.000000),
(11, '2026-04-16 13:06:21', '2026-05-15 09:35:58', 1, NULL, 'SAUCE-001', '8901234567900', 'Tomato Sauce', 'Tomato ketchup', 13, NULL, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 25.00, 25.00, 40.00, 0, 0, 1, 180, 'Cool place', 'Heinz', 'Heinz', 'Tomato ketchup', 1, 4, 150.000000),
(12, '2026-04-16 13:06:21', '2026-05-15 09:36:15', 1, NULL, 'SAUCE-002', '8901234567901', 'Soy Sauce', 'Dark soy sauce', 13, NULL, NULL, 'pcs', 0.000000, 10.000000, 100.000000, 20.000000, 30.00, 30.00, 50.00, 0, 0, 1, 365, 'Cool place', 'Kikkoman', 'Kikkoman', 'Japanese soy sauce', 1, 4, 60.000000),
(13, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'SALT-001', '8901234567902', 'Table Salt', 'Iodized table salt', 14, NULL, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 5.00, 5.00, 10.00, 1, 0, 1, 730, 'Cool dry place', 'Unga', 'Unga', 'Fine salt', 1, NULL, 100.000000),
(14, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'SPICES-001', '8901234567903', 'Black Pepper', 'Whole black pepper corns', 14, NULL, NULL, 'pcs', 0.000000, 5.000000, 50.000000, 10.000000, 80.00, 80.00, 120.00, 0, 0, 1, 365, 'Cool dry place', 'East African', 'East African', 'Premium', 1, 4, 30.000000),
(15, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'SPICES-002', '8901234567904', 'Paprika', 'Ground paprika powder', 14, NULL, NULL, 'pcs', 0.000000, 5.000000, 50.000000, 10.000000, 65.00, 65.00, 100.00, 0, 0, 1, 365, 'Cool dry place', 'East African', 'East African', 'Sweet paprika', 1, 4, 25.000000),
(16, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'MILK-001', '8901234567905', 'UHT Milk', 'Long life whole milk', 8, NULL, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 35.00, 35.00, 55.00, 1, 1, 1, 90, 'Cool place', 'Brookside', 'Brookside', '1 liter pack', 1, NULL, 80.000000),
(17, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'BUTTER-001', '8901234567906', 'Salted Butter', 'Pure salted butter', 8, NULL, NULL, 'pcs', 0.000000, 10.000000, 100.000000, 20.000000, 45.00, 45.00, 70.00, 1, 1, 1, 60, 'Refrigerated', 'Blue Band', 'Blue Band', '250g pack', 1, NULL, 50.000000),
(18, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'EGGS-001', '8901234567907', 'Chicken Eggs', 'Fresh grade A eggs', 8, NULL, NULL, 'pcs', 0.000000, 30.000000, 300.000000, 60.000000, 300.00, 300.00, 8.00, 0, 1, 0, 21, 'Refrigerated', 'Local', 'Local', 'Per egg', 1, 4, 193.000000),
(19, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'CHICKEN-001', '8901234567908', 'Chicken Breast', 'Boneless chicken breast', 9, 11, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 120.00, 120.00, 180.00, 0, 1, 1, 7, 'Frozen', 'Local', 'Local', 'Per kg', 1, 4, 60.000000),
(20, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'CHICKEN-002', '8901234567909', 'Chicken Thighs', 'Bone-in chicken thighs', 9, 11, NULL, 'pcs', 0.000000, 15.000000, 150.000000, 30.000000, 100.00, 100.00, 150.00, 0, 1, 1, 7, 'Frozen', 'Local', 'Local', 'Per kg', 1, 4, 50.000000),
(21, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'BEEF-001', '8901234567910', 'Beef Mince', 'Ground beef mince', 9, 12, NULL, 'pcs', 0.000000, 15.000000, 150.000000, 30.000000, 10000.00, 150.00, 220.00, 0, 1, 1, 5, 'Frozen', 'Local', 'Local', 'Per kg', 1, 4, 5.000000),
(22, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'BEEF-002', '8901234567911', 'Beef Steak', 'Tender beef steak cuts', 9, 12, NULL, 'pcs', 0.000000, 10.000000, 100.000000, 20.000000, 250.00, 250.00, 350.00, 0, 1, 1, 5, 'Frozen', 'Local', 'Local', 'Premium cuts', 1, 4, 30.000000),
(23, '2026-04-16 13:06:21', '2026-05-15 09:37:28', 1, NULL, 'COLA-001', '8901234567912', 'Coca Cola', 'Original taste Coca Cola', 1, 1, NULL, 'pcs', 0.000000, 50.000000, 500.000000, 100.000000, 10000.00, 15.00, 25.00, 0, 0, 1, 180, 'Cool place', 'Coca Cola', 'Coca Cola', '330ml can', 1, 4, 320.000000),
(24, '2026-04-16 13:06:21', '2026-05-27 07:37:23', 1, NULL, 'FANTA-001', '8901234567913', 'Fanta Orange', 'Orange flavored soda', 1, 2, 'carton', 'bottle', 0.000000, 40.000000, 400.000000, 80.000000, 1000.00, 1000.00, 3000.00, 1, 0, 1, 180, 'Cool place', 'Coca Cola', 'Fanta', '330ml can', 1, 3, 545.000000),
(25, '2026-04-16 13:06:21', '2026-05-14 09:43:29', 1, NULL, 'SPRITE-001', '8901234567914', 'Sprite', 'Lemon-lime flavored soda', 1, 3, NULL, 'pcs', 0.000000, 40.000000, 400.000000, 80.000000, 15.00, 15.00, 3000.00, 1, 0, 1, 180, 'Cool place', 'Coca Cola', 'Sprite', '330ml can', 1, 4, 250.000000),
(26, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WATER-001', '8901234567915', 'Mineral Water', 'Still mineral water', 2, NULL, NULL, 'pcs', 0.000000, 100.000000, 1000.000000, 200.000000, 10.00, 10.00, 20.00, 1, 0, 1, 365, 'Cool place', 'Rwenzori', 'Rwenzori', '500ml bottle', 1, NULL, 500.000000),
(27, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WATER-002', '8901234567916', 'Sparkling Water', 'Carbonated mineral water', 2, NULL, NULL, 'pcs', 0.000000, 30.000000, 300.000000, 60.000000, 15.00, 15.00, 30.00, 1, 0, 1, 365, 'Cool place', 'Rwenzori', 'Rwenzori', '500ml bottle', 1, NULL, 150.000000),
(28, '2026-04-16 13:06:21', '2026-05-18 09:09:18', 1, NULL, 'BEER-001', '8901234567917', 'Club Beer', 'Premium lager beer', 4, NULL, 'crate', 'piece', 0.000000, 50.000000, 500.000000, 100.000000, 2500.00, 2500.00, 5000.00, 1, 0, 1, 180, 'Cool place', 'Uganda Breweries', 'Club', '500ml bottle', 1, 4, 380.000000),
(29, '2026-04-16 13:06:21', '2026-05-14 10:05:45', 1, NULL, 'BEER-002', '8901234567918', 'Tusker Lager', 'Premium lager beer', 4, NULL, NULL, 'pcs', 0.000000, 50.000000, 500.000000, 100.000000, 22.00, 22.00, 38000.00, 1, 0, 1, 180, 'Cool place', 'EABL', 'Tusker', '500ml bottle', 1, 4, 180.000000),
(30, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'VODKA-001', '8901234567919', 'Smirnoff Vodka', 'Premium vodka', 5, 5, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 150.00, 150.00, 250.00, 1, 0, 1, 365, 'Cool place', 'Diageo', 'Smirnoff', '750ml bottle', 1, NULL, 80.000000),
(31, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'WHISKY-001', '8901234567920', 'Johnnie Walker', 'Black label whisky', 5, 6, NULL, 'pcs', 0.000000, 15.000000, 150.000000, 30.000000, 300.00, 300.00, 500.00, 1, 0, 1, 365, 'Cool place', 'Diageo', 'Johnnie Walker', '750ml bottle', 1, NULL, 40.000000),
(32, '2026-04-16 13:06:21', '2026-04-16 13:06:21', 1, NULL, 'GIN-001', '8901234567921', 'Bombay Sapphire', 'Premium gin', 5, 7, NULL, 'pcs', 0.000000, 15.000000, 150.000000, 30.000000, 280.00, 280.00, 450.00, 1, 0, 1, 365, 'Cool place', 'Bombay', 'Bombay Sapphire', '750ml bottle', 1, NULL, 35.000000),
(33, '2026-04-16 13:06:21', '2026-05-14 09:54:09', 1, NULL, 'RUM-001', '8901234567922', 'Bacardi Rum', 'White rum', 5, 8, NULL, 'pcs', 0.000000, 20.000000, 200.000000, 40.000000, 180.00, 180.00, 300.00, 0, 0, 1, 365, 'Cool place', 'Bacardi', 'Bacardi', '750ml bottle', 1, 4, 60.000000),
(37, '2026-05-08 12:11:38', '2026-05-08 12:11:38', 1, NULL, 'ITEM-69FDFD2A3AEE3', NULL, 'Rockboom 300mls', NULL, 3, NULL, 'pieces', 'pcs', 0.000000, 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, 'the following items were added to stock', 2, NULL, 220.000000),
(38, '2026-05-11 03:05:08', '2026-05-27 09:13:22', 1, NULL, 'ITEM-6A0171945AC3D', NULL, 'Mountain dew', NULL, 1, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 1000.00, 3000.00, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, 3, 125.000000),
(39, '2026-05-11 03:14:55', '2026-05-11 03:14:55', 1, NULL, 'ITEM-6A0173DF19192', NULL, 'Cow meat', NULL, 21, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, 'expiring on 15/05/2026', 2, NULL, 99.990000),
(40, '2026-05-11 06:35:10', '2026-05-11 06:35:10', 1, NULL, 'ITEM-6A01A2CEABFFE', NULL, 'Sting', NULL, 3, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 120.000000),
(41, '2026-05-12 03:27:21', '2026-05-12 03:27:21', 1, NULL, 'ITEM-6A02C849E805F', NULL, 'Rockboom 300mls', NULL, 3, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 0.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, NULL, 180.000000),
(42, '2026-05-12 05:13:56', '2026-05-15 09:27:01', 1, NULL, 'ITEM-6A02E1447F436', NULL, 'Rwenzori mineral water 300mls', NULL, 2, NULL, 'box', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 20000.00, 1000.00, 1, 0, 1, NULL, NULL, NULL, NULL, 'We received 20 boxes of Rwenzori mineral water of 300mls', 2, 4, 580.000000),
(43, '2026-05-16 05:41:27', '2026-05-19 10:38:54', 1, NULL, 'ITEM-6A082DB7AF99D', '5449000195777', 'Novida 500mls', NULL, 1, NULL, 'crate', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 11000.00, 11000.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, 2, 378.000000),
(44, '2026-05-16 06:11:29', '2026-05-18 09:06:09', 1, NULL, 'ITEM-6A0834C1A043A', NULL, 'Four cousins', NULL, 6, NULL, 'bottles', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 35000.00, 35000.00, 70000.00, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, 3, 20.000000),
(45, '2026-05-19 06:44:45', '2026-05-19 06:55:04', 1, NULL, 'ITEM-6A0C310D36CC3', NULL, 'HEMA 300MLS', NULL, 2, NULL, 'carton', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 350.00, 350.00, 1000.00, 1, 0, 1, NULL, NULL, NULL, NULL, NULL, 2, 4, 220.000000),
(46, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-3C9BE443DC5C8', NULL, 'Beef sausage', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 56000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(47, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-A49E2E28B8381', NULL, 'Whole chicken', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 14000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(48, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-DF8A20466806F', NULL, 'Chicken filet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(49, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-1045BBCB704CF', NULL, 'Mixed meat', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 18000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(50, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-4D6A7C769D61A', NULL, 'Chicken wings', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 14000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(51, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-516E608D3CA35', NULL, 'Tilapia filet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 39000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(52, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-8E20B8D5944AA', NULL, 'Minced meat', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 10000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(53, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-B7B19C931987D', NULL, 'Beef fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 28000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(54, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-6B4AF60B75C9A', NULL, 'Fish fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(55, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-15F1CEFE7A488', NULL, 'Pork fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 21000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(56, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-4F6684B04584F', NULL, 'Pork ribs', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(57, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-58D5505F78387', NULL, 'Bacon', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 35000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(58, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-1951B3690A4AA', NULL, 'Goat ribs', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(59, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-80BA5329EE811', NULL, 'Ham', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(60, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-0F6E8FD2FB1DB', NULL, 'Salmon', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 310000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(61, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-9A0D57CC4C2E3', NULL, 'Prawns', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 99300.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(62, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-7BF9BEFC5A375', NULL, 'Lobster tails', NULL, 9, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 5762.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(63, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-64177B08344CA', NULL, 'Whole fish', NULL, 9, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(64, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-243A57EC8AC6F', NULL, 'Whole pig', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(65, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-49C4BC4BD01C5', NULL, 'Goat whole leg', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 27000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(66, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-8B9CC29EB3677', NULL, 'Whole local chicken', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 40000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(67, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-375F47E26EDE9', NULL, 'Top side', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(68, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-6BD18942966FB', NULL, 'Cow pease', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 10000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(69, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-7F6F6FD561E49', NULL, 'Rib eye steak', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 23000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(71, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-19B4CC205317D', NULL, 'Mozzarella cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(72, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-EAD9912833D70', NULL, 'Cheddar cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(73, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-1BE7590FB543F', NULL, 'Parmesan cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 79000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(74, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-AC6095289E898', NULL, 'Unsalted butter', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 204000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(75, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-5308F6B2CDBEC', NULL, 'Milk', NULL, 8, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(76, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-61FA7AEEC1767', NULL, 'Cooking cream', NULL, 8, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(77, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-9217E792F0459', NULL, 'Croma butter', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 105000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(78, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-21EAACA50738F', NULL, 'Chips/Potato wedges', NULL, 10, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1335.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(79, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-8691261B47B16', NULL, 'Tomatoes', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2700.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(80, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-04A94E40922F3', NULL, 'Lettuce', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(81, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-75AD81AF6DE48', NULL, 'Onions', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(82, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-DC917AFB35C64', NULL, 'Pickles', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(83, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-F395485F6CB5B', NULL, 'Spring onions', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(84, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-17E7F2B5D6806', NULL, 'Yellow pepper', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(85, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-49B0011460BCF', NULL, 'Pineapple', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(86, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-DAA52A1C8D198', NULL, 'Coriander', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(87, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-0EAB0287A7610', NULL, 'Chinese cabbage', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(88, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-A49373E78C3AE', NULL, 'Cabbage', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(89, '2026-05-20 13:37:21', '2026-05-22 12:41:11', 1, NULL, 'ITEM-15562B8A13E60', NULL, 'Avocado', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(90, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-9CA4F41F12217', NULL, 'Cucumber', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(91, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-F235D8DBE2546', NULL, 'Broccoli', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(92, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-78F481A8625CB', NULL, 'Cauliflower', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(93, '2026-05-20 13:37:21', '2026-05-20 13:37:21', 1, NULL, 'ITEM-0241DE6199387', NULL, 'French beans', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(94, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-FAB39175A8001', NULL, 'Irish potatoes', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(95, '2026-05-20 13:37:22', '2026-05-22 05:43:54', 1, NULL, 'ITEM-6F17673E1A511', NULL, 'Baby potatoes', NULL, 10, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 2876.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 490.000000),
(96, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-0E481A2731AC9', NULL, 'Zucchini', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 4000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(97, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-4E1F6D9B552B6', NULL, 'Leafy lettuce', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(98, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-DC8B9855BB25D', NULL, 'Bell peppers', NULL, 10, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 11.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(99, '2026-05-20 13:37:22', '2026-05-25 10:45:40', 1, NULL, 'ITEM-7309E78BB13FA', NULL, '1000 island sauce', NULL, 13, NULL, '17', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 831.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 440.000000),
(100, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-623A1B3EACBFF', NULL, 'Dark soy sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 8000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(101, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-6B6A2DC4A4C19', NULL, 'Bbq sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(102, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-ACD11A2813E18', NULL, 'Concasse sauce', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1264.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(103, '2026-05-20 13:37:22', '2026-05-20 13:37:22', 1, NULL, 'ITEM-A6212C2CB47C2', NULL, 'Oyster sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(104, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-A7473355440C6', NULL, 'Beef sausage', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 56000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(105, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-06A8AEFAF2598', NULL, 'Whole chicken', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 14000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(106, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-DEF86C046BCBC', NULL, 'Chicken filet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(107, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-2E4BC7A52CAD7', NULL, 'Mixed meat', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 18000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(108, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-652C546B4C088', NULL, 'Chicken wings', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 14000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(109, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-11F3836DF9B3F', NULL, 'Tilapia filet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 39000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(110, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-4251226A5E13D', NULL, 'Minced meat', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 10000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(111, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-54BF388AB4308', NULL, 'Beef fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 28000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(112, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-2217F3FD2BA22', NULL, 'Fish fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(113, '2026-05-20 13:44:41', '2026-05-20 13:44:41', 1, NULL, 'ITEM-E357483C12DF1', NULL, 'Pork fillet', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 21000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(114, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-6EDF40594E787', NULL, 'Pork ribs', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(115, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-14885454F0A96', NULL, 'Bacon', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 35000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(116, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-E7DDE3BE5D4F5', NULL, 'Goat ribs', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(117, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-CA15959AEE29D', NULL, 'Ham', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(118, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-A312096FDCF1D', NULL, 'Salmon', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 310000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(119, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-2176C8E52015C', NULL, 'Prawns', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 99300.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(120, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-152B31319FFAA', NULL, 'Lobster tails', NULL, 9, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 5762.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(121, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-AA4C1D5B8D070', NULL, 'Whole fish', NULL, 9, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(122, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-EDD669086694A', NULL, 'Whole pig', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(123, '2026-05-20 13:49:39', '2026-05-20 13:49:39', 1, NULL, 'ITEM-915B95512E9E6', NULL, 'Goat whole leg', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 27000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(124, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-3F9A9C32BFA72', NULL, 'Whole local chicken', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 40000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(125, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-F835238D12CBB', NULL, 'Top side', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 20000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(126, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-988EE0D442217', NULL, 'Cow pease', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 10000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(127, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-F027826FC36BC', NULL, 'Rib eye steak', NULL, 9, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 23000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(129, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-26AF365130C26', NULL, 'Mozzarella cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(130, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-CC579DE724811', NULL, 'Cheddar cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(131, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-C1B1C0A87CD8C', NULL, 'Parmesan cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 79000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(132, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-3857DE7FB9274', NULL, 'Unsalted butter', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 204000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(133, '2026-05-20 13:51:22', '2026-05-20 13:51:22', 1, NULL, 'ITEM-D46E9AD2FD34D', NULL, 'Milk', NULL, 8, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(134, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-3E911177077AC', NULL, 'Cooking cream', NULL, 8, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(135, '2026-05-20 13:54:13', '2026-05-25 10:46:51', 1, NULL, 'ITEM-3A88ABA38D64C', NULL, 'Croma butter', NULL, 8, NULL, '3', 'kg', 0.050000, 0.000000, 0.000000, 0.000000, 105000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 50.000000),
(136, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-23FA36BC00729', NULL, 'Chips/Potato wedges', NULL, 10, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1335.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(137, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-2982106210BF9', NULL, 'Tomatoes', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2700.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(138, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-90A3D501524B6', NULL, 'Lettuce', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(139, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-43908BA33266F', NULL, 'Onions', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(140, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-BF6AD305D06E4', NULL, 'Pickles', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(141, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-FD9BC34094949', NULL, 'Spring onions', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(142, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-7CB2228797A73', NULL, 'Yellow pepper', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(143, '2026-05-20 13:54:13', '2026-05-20 13:54:13', 1, NULL, 'ITEM-38CC2A0C7F188', NULL, 'Pineapple', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(144, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-B66989D2831DF', NULL, 'Coriander', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(145, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-A1308A1E79EE1', NULL, 'Chinese cabbage', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(146, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-C758B124DD333', NULL, 'Cabbage', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(147, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-1D54E069ACA1E', NULL, 'Avocado', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(148, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-0C4594C6A4245', NULL, 'Cucumber', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(149, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-56C4E8AF6C717', NULL, 'Broccoli', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(150, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-5202F658C218E', NULL, 'Cauliflower', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(151, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-0CB737398719A', NULL, 'French beans', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(152, '2026-05-20 13:57:12', '2026-05-20 13:57:12', 1, NULL, 'ITEM-266C9514A4740', NULL, 'Irish potatoes', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(153, '2026-05-20 13:57:12', '2026-05-25 11:52:58', 1, NULL, 'ITEM-F493F1E369B51', NULL, 'Baby potatoes', NULL, 10, NULL, '17', 'portion', 0.050000, 0.000000, 0.000000, 0.000000, 2876.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 500.000000),
(154, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-4A00C1250A922', NULL, 'Zucchini', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 4000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(155, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-7B0E370720C67', NULL, 'Leafy lettuce', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(156, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-1F58E06EFE6B6', NULL, 'Bell peppers', NULL, 10, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 11.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(157, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-4465D29E32942', NULL, '1000 island sauce', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 831.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(158, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-83A06C8D7C656', NULL, 'Dark soy sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 8000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(159, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-6E0745D0A027A', NULL, 'Bbq sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(160, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-204B71FB7DC49', NULL, 'Concasse sauce', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1264.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(161, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-D6C0DB516E397', NULL, 'Oyster sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(162, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-8110D930978A3', NULL, 'Bechamel sauce', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(163, '2026-05-20 14:03:16', '2026-05-20 14:03:16', 1, NULL, 'ITEM-30FB3512CADEF', NULL, 'Vinaigrette dressing', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 5435.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(164, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-9B10BB683A6B4', NULL, 'Sweet chilli sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 17000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(165, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-EAEE880B74F76', NULL, 'Mayonnaise', NULL, 13, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(166, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-DFCAFA815074F', NULL, 'Tomato sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 8000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(167, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-7E0E307AA325C', NULL, 'Worcestershire sauce', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 12000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(168, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-22660ADDBD014', NULL, 'Light soy sauce', NULL, 13, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 8500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(169, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-6805BDB575E5C', NULL, 'Salt', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(170, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-ECF2498F07945', NULL, 'Brown sugar', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(171, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-216EFF3453B9C', NULL, 'White pepper', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(172, '2026-05-20 14:06:07', '2026-05-20 14:06:07', 1, NULL, 'ITEM-F7CC697AC2B63', NULL, 'Black pepper', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 35000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(173, '2026-05-20 14:06:08', '2026-05-20 14:06:08', 1, NULL, 'ITEM-22EEDF0C17D90', NULL, 'Paprika', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(174, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-6CD68D5A55389', NULL, 'Rosemary', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 2000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(175, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-1545AB90C197C', NULL, 'Garlic and ginger paste', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 36343.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(176, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-43278AB35D504', NULL, 'Meat tenderizer', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(177, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-541242A4554AF', NULL, 'Cumin', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 35000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(178, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-880F3AF942DD6', NULL, 'Oregano', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(179, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-E17ABFD52E966', NULL, 'Thyme', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(180, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-8A35EA9D1F94B', NULL, 'Honey', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 18000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(181, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-1CBB26BFE9057', NULL, 'Cinnamon', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(182, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-D63F2F0C780C1', NULL, 'Nutmeg', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(183, '2026-05-20 14:07:59', '2026-05-20 14:07:59', 1, NULL, 'ITEM-53F8F038A454B', NULL, 'Chilli flex', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(184, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-00B59E3DAC291', NULL, 'Tikka masala', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 33000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(185, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-8D4283A0FCAD9', NULL, 'Pilau masala', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(186, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-C687151C83266', NULL, 'Dry thyme', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(187, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-9A6D0DF2C3231', NULL, 'Sage', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(188, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-5AE02097E1B3E', NULL, 'Curry powder', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(189, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-CC710463B62AC', NULL, 'Turmeric', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 28000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(190, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-BA7ED15DD17C3', NULL, 'Cardamom', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 30000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(191, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-E2A75F94AB92F', NULL, 'Ginger', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 25000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(192, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-9677A9EF6BECE', NULL, 'Dough', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1831.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(193, '2026-05-20 14:09:54', '2026-05-20 14:09:54', 1, NULL, 'ITEM-41132E3668FC9', NULL, 'Pasta', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 4500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000);
INSERT INTO `inventory_items` (`id`, `created_at`, `updated_at`, `is_active`, `deleted_at`, `item_code`, `barcode`, `name`, `description`, `category_id`, `sub_category_id`, `default_unit_of_measure_id`, `base_unit`, `empty_bottle_weight`, `minimum_stock`, `maximum_stock`, `reorder_quantity`, `unit_cost`, `last_purchase_price`, `selling_price`, `is_sellable`, `is_perishable`, `is_taxable`, `shelf_life_days`, `storage_conditions`, `manufacturer`, `brand`, `notes`, `created_by`, `updated_by`, `current_stock`) VALUES
(194, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-AA7983DB9FB2B', NULL, 'Rice', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 9000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(195, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-14D584EE12124', NULL, 'Steamed rice', NULL, 11, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 2127.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(196, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-93207D82BD832', NULL, 'Vegetable rice', NULL, 11, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 2460.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(197, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-4921AE88C9EF2', NULL, 'Burger bun', NULL, 15, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(198, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-D101E004DBBD7', NULL, 'Bread', NULL, 15, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(199, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-1024820C651F8', NULL, 'Slider bars', NULL, 15, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 512.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(200, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-B4C4AAEC5ABA1', NULL, 'Pizza box', NULL, 15, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(201, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-8581B4E192A31', NULL, 'Springroll sheet', NULL, 15, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 288.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(202, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-2D25BF0769B30', NULL, 'Cooking oil', NULL, 12, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 7250.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(203, '2026-05-20 14:12:53', '2026-05-20 14:12:53', 1, NULL, 'ITEM-2B5D388EA6634', NULL, 'Soft drink', NULL, 1, NULL, 'bottle', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 813.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(204, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-8888C8BC1CE58', NULL, 'Castle Lite', NULL, 1, NULL, 'bottle', 'bottle', 0.000000, 0.000000, 0.000000, 0.000000, 2400.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(205, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-B76161ADB62E7', NULL, 'Four cousins dry white', NULL, 6, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 55000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(206, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-EE01A7192721A', NULL, 'Fragolino', NULL, 6, NULL, 'glass', 'glass', 0.000000, 0.000000, 0.000000, 0.000000, 9000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(207, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-1B48C0A5C5874', NULL, 'Juice', NULL, 1, NULL, 'glass', 'glass', 0.000000, 0.000000, 0.000000, 0.000000, 9015.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(208, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-FA6D3A35E9C3F', NULL, 'Nojitos', NULL, 1, NULL, 'glass', 'glass', 0.000000, 0.000000, 0.000000, 0.000000, 854.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(209, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-8FC121F99F8A7', NULL, 'Lemon juice', NULL, 1, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(210, '2026-05-20 14:16:39', '2026-05-27 08:34:18', 1, NULL, 'ITEM-9A3A5541CB229', NULL, 'Oranges', NULL, 10, NULL, 'kg', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 100.000000),
(211, '2026-05-20 14:16:39', '2026-05-20 14:16:39', 1, NULL, 'ITEM-79EDBBDE9C31D', NULL, 'Watermelon', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 7000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(212, '2026-05-20 14:16:40', '2026-05-21 15:31:19', 1, NULL, 'ITEM-4FFC78A7704E6', NULL, 'Apples', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 175000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0.000000),
(213, '2026-05-20 14:16:40', '2026-05-20 14:16:40', 1, NULL, 'ITEM-BF3DD77880652', NULL, 'Pineapple natural tenderizer', NULL, 14, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 30.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(214, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-379D28A9A6792', NULL, 'Balsamic vinegar', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 13000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(215, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-C5F4BB8949505', NULL, 'Chicken quarters', NULL, 9, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 16.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(216, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-E2F2FC36AFB10', NULL, 'Chicken for pizza', NULL, 9, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 3014.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(217, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-4E99151B3A16D', NULL, 'Curry sauce', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(218, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-F0EE7299C7C2E', NULL, 'Coconut milk', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 18000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(219, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-61A69AFA1566D', NULL, 'Red food color', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 10.000000),
(220, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-C64A3978EE943', NULL, 'White vinegar', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 26000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(221, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-A10D85CE602BD', NULL, 'Vanilla essence', NULL, 14, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 10.000000),
(222, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-56F50FED7725F', NULL, 'Sugar', NULL, 14, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 210000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 200.000000),
(223, '2026-05-20 14:19:16', '2026-05-20 14:19:16', 1, NULL, 'ITEM-F7A3EAB1AE5E2', NULL, 'Prestige', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 105000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(224, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-099081DA4C3F1', NULL, 'Yeast', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 16500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(225, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-943CD447F544C', NULL, 'Bread improver', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 20.000000),
(226, '2026-05-20 14:21:05', '2026-05-25 02:56:33', 1, NULL, 'ITEM-B9635D44CDFBD', NULL, 'Anchovies', NULL, 9, NULL, 'kg', 'kg', 5.000000, 0.000000, 0.000000, 0.000000, 19000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 20.000000),
(227, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-39E10854A290C', NULL, 'Parsley', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 10.000000),
(228, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-B340C8FE5069D', NULL, 'Celery', NULL, 10, NULL, 'bundle', 'bundle', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(229, '2026-05-20 14:21:05', '2026-05-22 12:41:11', 1, NULL, 'ITEM-6BE8563DD289E', NULL, 'Baking flour', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 6600.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 14.980000),
(230, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-E91CC6E3132D7', NULL, 'Clear chicken vegetable soup', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 4801.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(231, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-D0744D077F187', NULL, 'Beef Pilau', NULL, 13, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 12068.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(232, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-74CF58C99A270', NULL, 'Maze mixed green salad', NULL, 10, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 1586.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(233, '2026-05-20 14:21:05', '2026-05-20 14:21:05', 1, NULL, 'ITEM-5F9BAA5FE0802', NULL, 'Pork ribs portioned', NULL, 9, NULL, 'portion', 'portion', 0.000000, 0.000000, 0.000000, 0.000000, 28902.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(234, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-E0D7576394A29', NULL, 'Matoke', NULL, 10, NULL, 'bunch', 'bunch', 0.000000, 0.000000, 0.000000, 0.000000, 40000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(235, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-43453722C9AC1', NULL, 'Matoke fingers', NULL, 10, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 300.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(236, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-E59ADD464343C', NULL, 'Plantain finger', NULL, 10, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 700.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(237, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-F58445197F2E2', NULL, 'Yams', NULL, 10, NULL, 'heap', 'heap', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(238, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-98F2CCFCB2BD7', NULL, 'Cassava', NULL, 10, NULL, 'heap', 'heap', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(239, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-1D9FA2E1D04C3', NULL, 'G nut paste', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 8000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(240, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-218A274582FBC', NULL, 'Cashew nut', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 28000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(241, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-F25918A7F085C', NULL, 'Dates', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 4000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(242, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-B60E57FCD2624', NULL, 'Top up', NULL, 13, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 15000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(243, '2026-05-20 14:22:43', '2026-05-20 14:22:43', 1, NULL, 'ITEM-9F688E28D8974', NULL, 'Pene pasta', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 4500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(244, '2026-05-20 14:23:44', '2026-05-20 14:23:44', 1, NULL, 'ITEM-61E4BBEDD8C17', NULL, 'Fresh beans', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 3000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(245, '2026-05-20 14:23:44', '2026-05-20 14:23:44', 1, NULL, 'ITEM-9F9E54B77907D', NULL, 'English cucumber', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 5000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(246, '2026-05-20 14:23:44', '2026-05-25 02:52:13', 1, NULL, 'ITEM-6DC87E89FAD8C', NULL, 'Baby marrow', NULL, 10, NULL, 'kg', 'kg', 0.004999, 0.000000, 0.000000, 0.000000, 3500.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, 2, 100.000000),
(247, '2026-05-20 14:23:44', '2026-05-20 14:23:44', 1, NULL, 'ITEM-19DE7BD9BD831', NULL, 'Mushroom', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 9000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(248, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-5BE888115A57D', NULL, 'Pizza Dough', NULL, 11, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1831.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(249, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-4F1F379442D9D', NULL, 'Mozzarella Cheese', NULL, 8, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 22000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 100.000000),
(250, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-DCEA6CF2CEA82', NULL, 'Concasse Sauce', NULL, 13, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 1264.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000),
(251, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-CBE56B3E95CF7', NULL, 'Fresh Basil', NULL, 10, NULL, 'kg', 'kg', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 10.000000),
(252, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-DEBFD6C8978E9', NULL, 'Olive Oil', NULL, 12, NULL, 'litre', 'litre', 0.000000, 0.000000, 0.000000, 0.000000, 350.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 50.000000),
(253, '2026-05-20 17:40:15', '2026-05-20 17:40:15', 1, NULL, 'ITEM-FC4623BA6D214', NULL, 'Pizza Box', NULL, 18, NULL, 'piece', 'piece', 0.000000, 0.000000, 0.000000, 0.000000, 1000.00, 0.00, 0.00, 0, 0, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, 500.000000);

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
(8, 'LPO-20260512-2558', 13, 2, 3, 7, '2026-05-12', '2026-05-14', 'main offices', NULL, 600000.00, 0.00, 600000.00, 'converted_to_epo', 29, NULL, 'please purchase these items as soon as possible', NULL, '2026-05-12 05:19:14', '2026-05-12 05:18:19', '2026-05-12 05:19:54', NULL),
(9, 'LPO-20260519-8939', 14, 1, 3, 7, '2026-05-19', '2026-05-21', NULL, NULL, 260000.10, 0.00, 260000.10, 'converted_to_epo', 30, NULL, 'Lpo approved go a head and purchase the items', NULL, '2026-05-19 10:21:20', '2026-05-19 10:19:05', '2026-05-19 10:24:14', NULL);

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
(16, 8, 56, 42, 30.00, 20000.00, 600000.00, 'boxes', NULL, '2026-05-12 05:18:19', '2026-05-12 05:18:19'),
(17, 9, 57, 38, 15.00, 10000.00, 150000.00, 'cartons', 'its urgently neede', '2026-05-19 10:19:05', '2026-05-19 10:19:05'),
(18, 9, 58, 43, 10.00, 11000.01, 110000.10, 'cartons', 'its over', '2026-05-19 10:19:05', '2026-05-19 10:19:05');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `department_id`, `description`, `sort_order`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'BREAK FAST', 9, 'This menu serves as break fast menu', 1, 1, NULL, 4, '2026-05-19 12:42:45', '2026-05-27 08:37:19', NULL),
(2, 'Restaurant Menu', 9, 'Main menu for restaurant department', 1, 1, NULL, NULL, '2026-05-19 14:44:36', '2026-05-21 11:02:27', '2026-05-21 11:02:27'),
(3, 'BAR MENU', 4, 'This is menu for the bars', 0, 1, 4, 4, '2026-05-19 12:07:34', '2026-05-21 12:21:49', NULL),
(6, 'LUNCH MENU', 8, 'This menu will be sued for kicthen department', 1, 1, 4, 4, '2026-05-21 09:45:27', '2026-05-21 10:55:54', NULL),
(7, 'BEVERAGES', 4, 'This menu will manage all beverages ie non alocholic and alcoholic drinks', 0, 1, 4, NULL, '2026-05-27 06:20:18', '2026-05-27 06:20:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `menu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `menu_item_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `selling_price` decimal(12,2) NOT NULL,
  `preparation_time` int(11) DEFAULT NULL COMMENT 'Minutes',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `inventory_item_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Optional link to inventory',
  `allergen_info` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `m_cost` decimal(15,2) DEFAULT 0.00 COMMENT 'M/cost from PDF',
  `vat` decimal(15,2) DEFAULT 0.00 COMMENT 'VAT from PDF',
  `mark_up` decimal(15,2) DEFAULT 0.00 COMMENT 'Mark up from PDF',
  `age_margins` decimal(5,2) DEFAULT 0.00 COMMENT '%age Margins from PDF',
  `age_cost` decimal(5,2) DEFAULT 0.00 COMMENT '%age Cost from PDF',
  `discount` decimal(15,2) DEFAULT 0.00 COMMENT 'Discount from PDF',
  `glovo_selling_price` decimal(15,2) DEFAULT 0.00 COMMENT 'Glovo Selling Price from PDF',
  `glovo_commission` decimal(15,2) DEFAULT 0.00 COMMENT 'Glovo Commission from PDF',
  `final_margin` decimal(15,2) DEFAULT 0.00 COMMENT 'Final margin from PDF',
  `vat_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `vat_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `vat_inclusive` tinyint(1) NOT NULL DEFAULT 1,
  `net_price` decimal(12,2) DEFAULT NULL,
  `last_costed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `category`, `menu_id`, `menu_item_category_id`, `selling_price`, `preparation_time`, `is_active`, `inventory_item_id`, `allergen_info`, `image_url`, `sort_order`, `notes`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`, `m_cost`, `vat`, `mark_up`, `age_margins`, `age_cost`, `discount`, `glovo_selling_price`, `glovo_commission`, `final_margin`, `vat_rate`, `vat_amount`, `vat_inclusive`, `net_price`, `last_costed_at`) VALUES
(1, 'Grilled Chicken Breast', 'Tender grilled chicken breast served with garlic        \r\n   mashed potatoes, roasted vegetables, herb sauce', 'Main', 6, 4, 40031.43, 20, 0, NULL, 'Contains dairy', NULL, 0, NULL, 9, 4, '2026-05-13 14:02:25', '2026-05-27 09:10:00', NULL, 14011.00, 0.00, 19913.94, 58.70, 41.30, 0.00, 48037.71, 9607.54, 24419.17, 18.00, 6106.49, 1, 33924.94, NULL),
(2, 'Grilled Chicken Breast', 'Tender grilled chicken breast served with garlic mashed potatoes, roasted seasonal vegetables, and a light herb sauce. Garnished with fresh rosemary.', 'Main', 6, 4, 25000.00, 20, 1, 1, 'Contains: Dairy (butter, cream), Garlic.', NULL, 1, NULL, 1, 4, '2026-05-13 17:05:43', '2026-05-21 13:34:57', NULL, 100.00, 0.00, 24900.00, 99.60, 0.40, 0.00, 30000.00, 6000.00, 23900.00, 18.00, 0.00, 1, NULL, NULL),
(3, 'Margherita Pizza', 'Traditional Italian pizza topped with fresh San Marzano tomatoes, mozzarella cheese, fresh basil leaves, extra virgin olive oil, and a pinch of sea salt on our signature thin crust.', 'Main', 1, NULL, 22000.00, 15, 1, 5, 'Contains: Gluten (wheat flour), Dairy (mozzarella).', NULL, 2, NULL, 1, 4, '2026-05-13 17:05:43', '2026-05-14 09:54:14', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 18.00, 0.00, 1, NULL, NULL),
(4, 'Fresh Orange Juice', 'Freshly squeezed seasonal oranges, served chilled with ice. No added sugar or preservatives. 100% natural.', 'Beverage', 1, 16, 5000.00, 5, 1, NULL, 'None.', NULL, 1, NULL, 1, 4, '2026-05-13 17:05:43', '2026-05-27 12:14:12', NULL, 2000.00, 0.00, 2237.29, 52.80, 47.20, 0.00, 6000.00, 1200.00, 2800.00, 18.00, 762.71, 1, 4237.29, NULL),
(5, 'OLUWOMBO', 'African Traditional food served on Kwanjura parties', 'Main', 1, NULL, 50000.00, 5, 1, NULL, 'contains chicken, G.nuts, matooke, rice', NULL, 0, NULL, 9, NULL, '2026-05-15 15:27:52', '2026-05-15 15:27:52', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 18.00, 0.00, 1, NULL, NULL),
(6, 'Margherita Pizza', NULL, '4', 6, 4, 30000.00, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-21 09:45:27', '2026-05-21 09:45:27', NULL, 1751.00, 0.00, 28249.00, 94.16, 5.84, 0.00, 36000.00, 7200.00, 27049.00, 18.00, 0.00, 1, NULL, NULL),
(7, 'pizza small', 'Please mix the items well', NULL, 6, 4, 10000.00, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-21 13:09:04', '2026-05-21 13:09:04', NULL, 831.00, 0.00, 9169.00, 91.69, 8.31, 0.00, 12000.00, 2400.00, 8769.00, 18.00, 0.00, 1, NULL, NULL),
(8, 'Organic Beef Steak', NULL, NULL, 6, 4, 12536.67, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-22 11:54:52', '2026-05-22 11:54:52', NULL, 3761.00, 0.00, 8775.67, 70.00, 30.00, 0.00, 15420.10, 3546.62, 8112.48, 18.00, 0.00, 1, NULL, NULL),
(9, 'Black Rum', NULL, NULL, 3, 9, 20.00, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-22 11:57:45', '2026-05-22 11:57:45', NULL, 5.00, 0.00, 15.00, 75.00, 25.00, 0.00, 24.60, 5.66, 13.94, 18.00, 0.00, 1, NULL, NULL),
(10, 'Roasted chicked', NULL, NULL, 6, 4, 5000.00, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-26 07:55:05', '2026-05-26 07:55:05', NULL, 200.00, 0.00, 4037.29, 95.28, 4.72, 0.00, 5750.00, 862.50, 4687.50, 18.00, 762.71, 1, 4237.29, NULL),
(11, 'COCOTAIL BAR MENU', NULL, NULL, 3, 4, 5085.68, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, 4, '2026-05-26 09:01:48', '2026-05-26 09:18:33', NULL, 831.00, 0.00, 3478.90, 80.72, 19.28, 0.00, 6102.82, 1220.56, 4051.25, 18.00, 775.78, 1, 4309.90, NULL),
(12, 'Chicken Pilau', NULL, NULL, 6, 4, 9996.04, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL, 471.00, 0.00, 8000.22, 94.44, 5.56, 0.00, 11995.25, 2399.05, 9125.20, 18.00, 1524.82, 1, 8471.22, NULL),
(13, 'Fanta medium', NULL, NULL, 7, 12, 5000.00, NULL, 1, 24, NULL, NULL, 0, NULL, 4, 4, '2026-05-27 07:22:06', '2026-05-27 08:22:31', NULL, 1000.00, 0.00, 3237.29, 76.40, 23.60, 0.00, 0.00, 0.00, 0.00, 18.00, 762.71, 1, 4237.29, NULL),
(14, 'Mountain dew small', NULL, NULL, 3, 12, 5000.00, NULL, 1, 38, NULL, NULL, 0, NULL, 4, 4, '2026-05-27 07:24:20', '2026-05-27 09:15:09', NULL, 1000.00, 0.00, 3237.29, 76.40, 23.60, 0.00, 0.00, 0.00, 0.00, 18.00, 762.71, 1, 4237.29, NULL),
(15, 'The Ultimate Breakfast Platter', NULL, NULL, 1, 4, 24998.27, NULL, 1, NULL, NULL, NULL, 0, NULL, 4, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL, 4398.00, 0.00, 16786.97, 79.24, 20.76, 0.00, 29997.92, 5999.58, 19600.34, 18.00, 3813.30, 1, 21184.97, NULL),
(16, 'Four cusins', NULL, NULL, 3, 15, 70000.00, NULL, 1, 44, NULL, NULL, 0, NULL, 4, NULL, '2026-05-27 12:21:50', '2026-05-27 12:21:50', NULL, 35000.00, 0.00, 24322.03, 41.00, 59.00, 0.00, 0.00, 0.00, 0.00, 18.00, 10677.97, 1, 59322.03, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_categories`
--

CREATE TABLE `menu_item_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item_categories`
--

INSERT INTO `menu_item_categories` (`id`, `name`, `code`, `description`, `sort_order`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Appetizers', 'APP001', 'Starters and small plates', 1, 1, NULL, NULL, '2026-05-19 15:21:34', '2026-05-21 11:28:47', NULL),
(2, 'Soups', NULL, 'Hot and cold soups', 2, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(3, 'Salads', NULL, 'Fresh garden salads', 3, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(4, 'Main Course', NULL, 'Main dishes', 4, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(5, 'Grill', NULL, 'Grilled specialties', 5, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(6, 'Pasta', NULL, 'Italian pasta dishes', 6, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(7, 'Seafood', NULL, 'Fresh seafood', 7, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(8, 'Desserts', NULL, 'Sweet treats', 8, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(9, 'Beverages', NULL, 'Drinks and refreshments', 9, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(10, 'Side Dishes', NULL, 'Accompaniments', 10, 1, NULL, NULL, '2026-05-19 15:21:34', NULL, NULL),
(11, 'Break fast', NULL, 'This is for break fas', 0, 1, NULL, NULL, '2026-05-21 11:25:05', '2026-05-21 11:25:05', NULL),
(12, 'Soft Drinks', 'SOFTDRINKS', 'This category covers all types of soda', 0, 1, NULL, NULL, '2026-05-27 06:37:36', '2026-05-27 06:37:36', NULL),
(13, 'Beer', 'BEER', 'This category covers all types of beer', 0, 1, NULL, NULL, '2026-05-27 06:38:04', '2026-05-27 06:38:04', NULL),
(14, 'Lucas', 'LUCAS', NULL, 0, 1, NULL, NULL, '2026-05-27 06:41:05', '2026-05-27 06:41:05', NULL),
(15, 'Wine', 'WINE', 'This category covers all types of wine', 0, 1, NULL, NULL, '2026-05-27 06:41:56', '2026-05-27 06:41:56', NULL),
(16, 'FRESH JUICE', 'JC001', NULL, 0, 1, NULL, NULL, '2026-05-27 12:12:54', '2026-05-27 12:12:54', NULL);

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
(29, '2026_05_11_141826_create_add_taken_by_table', 10),
(30, '2026_05_13_164028_create_menu_table', 11),
(31, '2026_05_14_104617_create__order_table', 12),
(32, '2026_05_14_104739_create__order_item_table', 12),
(33, '2026_05_15_132936_create_add_inventory_item_id_to_sales_order_items', 13),
(34, '2026_05_15_162938_add_payment_amounts_to_sales_orders', 14),
(35, '2026_05_26_101158_add_vat_fields_to_menu_items_table', 15);

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
(48, 'clear_system_logs', 'Clear system logs', 'audit', NULL, 111, 1, '2026-04-14 19:11:28', '2026-04-14 19:11:28', NULL),
(49, 'view_stock_movements', 'view_stock_movements', 'stock', 'For mornitoring stock movements', 1, 1, '2026-05-26 03:21:49', '2026-05-26 03:40:52', NULL),
(50, 'view_requisitions', 'View Requisitions', 'requisitions', 'Can view requisitions', 20, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(51, 'create_requisitions', 'Create Requisitions', 'requisitions', 'Can create requisitions', 21, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(52, 'edit_requisitions', 'Edit Requisitions', 'requisitions', 'Can edit requisitions', 22, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(53, 'delete_requisitions', 'Delete Requisitions', 'requisitions', 'Can delete requisitions', 23, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(54, 'approve_requisitions', 'Approve Requisitions', 'requisitions', 'Can approve requisitions', 24, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(55, 'issue_items', 'Issue Items', 'requisitions', 'Can issue items from store', 25, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(56, 'return_items', 'Return Items', 'requisitions', 'Can return items to store', 26, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(57, 'create_stock_movements', 'Create Stock Movements', 'stock', 'Can create stock movements', 30, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(58, 'view_procurement', 'View Procurement', 'procurement', 'Can access procurement module', 32, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(59, 'rate_vendors', 'Rate Vendors', 'vendors', 'Can rate vendors', 33, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(60, 'delete_vendor_ratings', 'Delete Vendor Ratings', 'vendors', 'Can delete vendor ratings', 38, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(61, 'delete_purchase_orders', 'Delete Purchase Orders', 'purchasing', 'Can delete purchase orders', 42, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(62, 'send_purchase_orders', 'Send Purchase Orders', 'purchasing', 'Can send purchase orders to vendors', 43, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(63, 'view_goods_received', 'View Goods Received', 'purchasing', 'Can view goods received notes', 44, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(64, 'view_stock_counts', 'View Stock Counts', 'stock', 'Can view stock counts', 47, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(65, 'create_stock_counts', 'Create Stock Counts', 'stock', 'Can create stock counts', 48, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(66, 'edit_stock_counts', 'Edit Stock Counts', 'stock', 'Can edit stock counts', 49, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(67, 'approve_stock_counts', 'Approve Stock Counts', 'stock', 'Can approve stock counts', 50, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(68, 'manage_stock_variance_reasons', 'Manage Variance Reasons', 'stock', 'Can manage stock variance reasons', 51, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(69, 'view_menus', 'View Menus', 'menu', 'Can view menus', 52, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(70, 'create_menus', 'Create Menus', 'menu', 'Can create menus', 53, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(71, 'edit_menus', 'Edit Menus', 'menu', 'Can edit menus', 54, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(72, 'delete_menus', 'Delete Menus', 'menu', 'Can delete menus', 55, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(73, 'view_menu_items', 'View Menu Items', 'menu', 'Can view menu items', 56, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(74, 'create_menu_items', 'Create Menu Items', 'menu', 'Can create menu items', 57, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(75, 'edit_menu_items', 'Edit Menu Items', 'menu', 'Can edit menu items', 58, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(76, 'delete_menu_items', 'Delete Menu Items', 'menu', 'Can delete menu items', 59, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(77, 'view_recipes', 'View Recipes', 'menu', 'Can view recipes', 60, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(78, 'manage_recipes', 'Manage Recipes', 'menu', 'Can manage recipes', 61, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(79, 'view_menu_item_categories', 'View Menu Item Categories', 'menu', 'Can view menu item categories', 62, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(80, 'manage_menu_item_categories', 'Manage Menu Item Categories', 'menu', 'Can manage menu item categories', 63, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(81, 'view_prices', 'View Prices', 'pricing', 'Can view prices', 64, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(82, 'edit_prices', 'Edit Prices', 'pricing', 'Can edit prices', 65, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(83, 'view_lpos', 'View LPOs', 'purchasing', 'Can view Local Purchase Orders', 66, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(84, 'approve_lpos', 'Approve LPOs', 'purchasing', 'Can approve Local Purchase Orders', 67, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(85, 'view_consumption', 'View Consumption', 'consumption', 'Can view consumption records', 68, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(86, 'record_consumption', 'Record Consumption', 'consumption', 'Can record consumption', 69, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(87, 'view_restaurant', 'View Restaurant', 'restaurant', 'Can access restaurant module', 70, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(88, 'view_sales', 'View Sales', 'sales', 'Can view sales', 71, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(89, 'process_payments', 'Process Payments', 'sales', 'Can process payments', 74, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(90, 'view_sales_reports', 'View Sales Reports', 'reports', 'Can view sales reports', 75, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(91, 'view_own_sales', 'View Own Sales', 'sales', 'Can view own sales only', 76, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(92, 'view_stock', 'View Stock', 'stock', 'Can view stock', 77, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(93, 'view_returns', 'View Returns', 'returns', 'Can view returns', 78, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(94, 'create_returns', 'Create Returns', 'returns', 'Can create returns', 79, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(95, 'view_profile', 'View Profile', 'profile', 'Can view own profile', 80, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(96, 'edit_profile', 'Edit Profile', 'profile', 'Can edit own profile', 81, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(97, 'view_bar', 'View Bar', 'bar', 'Can access bar module', 83, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(98, 'view_cashiers', 'View Cashiers', 'cashiers', 'Can view cashiers', 84, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(99, 'view_notifications', 'View Notifications', 'notifications', 'Can view notifications', 85, 1, '2026-05-26 06:56:10', '2026-05-26 06:56:10', NULL),
(135, 'view_tables', 'View Restaurant Tables', 'tables', 'Can view restaurant tables', 86, 1, '2026-05-26 07:51:20', '2026-05-26 07:51:20', NULL),
(136, 'create_tables', 'Create Restaurant Tables', 'tables', 'Can create restaurant tables', 87, 1, '2026-05-26 07:51:20', '2026-05-26 07:51:20', NULL),
(137, 'edit_tables', 'Edit Restaurant Tables', 'tables', 'Can edit restaurant tables', 88, 1, '2026-05-26 07:51:20', '2026-05-26 07:51:20', NULL),
(138, 'delete_tables', 'Delete Restaurant Tables', 'tables', 'Can delete restaurant tables', 89, 1, '2026-05-26 07:51:20', '2026-05-26 07:51:20', NULL),
(139, 'view_reservations', 'View Reservations', 'reservations', 'Can view table reservations', 90, 1, '2026-05-26 08:48:00', '2026-05-26 08:48:00', NULL),
(140, 'create_reservations', 'Create Reservations', 'reservations', 'Can create table reservations', 91, 1, '2026-05-26 08:48:00', '2026-05-26 08:48:00', NULL),
(141, 'edit_reservations', 'Edit Reservations', 'reservations', 'Can edit table reservations', 92, 1, '2026-05-26 08:48:00', '2026-05-26 08:48:00', NULL),
(142, 'delete_reservations', 'Delete Reservations', 'reservations', 'Can delete table reservations', 93, 1, '2026-05-26 08:48:00', '2026-05-26 08:48:00', NULL),
(143, 'access_waiter_module', 'Access Waiter Module', 'waiter', 'Can access waiter portal', 100, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(144, 'view_waiter_dashboard', 'View Waiter Dashboard', 'waiter', 'Can view waiter dashboard', 101, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(145, 'create_orders', 'Create Orders', 'waiter', 'Can create customer orders', 102, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(146, 'view_orders', 'View Orders', 'waiter', 'Can view orders', 103, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(147, 'print_bills', 'Print Bills', 'waiter', 'Can print customer bills', 104, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(148, 'view_menu', 'View Menu', 'waiter', 'Can view menu items', 106, 1, '2026-05-26 17:06:09', '2026-05-26 17:06:09', NULL),
(152, 'view_cashier_dashboard', 'View Cashier Dashboard', 'cashier', NULL, 200, 1, '2026-05-27 17:05:37', '2026-05-27 17:05:37', NULL),
(153, 'print_receipts', 'Print Receipts', 'cashier', NULL, 202, 1, '2026-05-27 17:05:37', '2026-05-27 17:05:37', NULL);

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
(9, 'PO-20260417-5145', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'fully_received', '2026-04-17 09:59:49', '2026-05-22 11:48:33', NULL, 3, NULL, 'AT our  main store'),
(10, 'PO-20260417-3492', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:02:20', '2026-04-17 10:02:21', NULL, 3, NULL, 'AT our  main store'),
(11, 'PO-20260417-4080', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:07:27', '2026-04-17 10:07:27', NULL, 3, NULL, 'AT our  main store'),
(12, 'PO-20260417-4885', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:08:09', '2026-04-17 10:08:09', NULL, 3, NULL, 'AT our  main store'),
(13, 'PO-20260417-2742', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:18:12', '2026-04-17 10:18:13', NULL, 3, NULL, 'AT our  main store'),
(14, 'PO-20260417-3253', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'partially_received', '2026-04-17 10:19:22', '2026-05-22 11:47:16', NULL, 3, NULL, 'AT our  main store'),
(15, 'PO-20260417-4245', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-19', 1942500.00, 0.00, 1942500.00, 'sent', '2026-04-17 10:26:32', '2026-04-17 10:26:32', NULL, 3, NULL, 'AT our  main store'),
(17, 'PO-20260417-3257', 1, NULL, NULL, 'Your are expected to delivery requested items at on the delivery date at 2pm', NULL, 3, NULL, NULL, '2026-04-17', '2026-04-20', 775000.00, 0.00, 775000.00, 'sent', '2026-04-17 10:49:21', '2026-04-17 10:49:21', NULL, 3, NULL, 'AT our main office'),
(22, 'LPO-20260429-0382', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-04-29', NULL, 760000.25, 0.00, 760000.25, 'lpo_created', '2026-04-29 09:07:44', '2026-04-29 09:07:45', NULL, 3, NULL, NULL),
(24, 'PO-20260508-1411', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-08', '2026-05-08', 500000.00, 0.00, 500000.00, 'partially_received', '2026-05-08 04:44:53', '2026-05-08 06:01:57', NULL, 3, NULL, 'main offices'),
(25, 'PO-20260511-0414', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-11', '2026-05-12', 200000.00, 0.00, 200000.00, 'partially_received', '2026-05-11 06:21:47', '2026-05-11 06:23:19', NULL, 3, NULL, NULL),
(26, 'PO-20260511-8792', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-11', '2026-05-13', 122500.00, 0.00, 122500.00, 'partially_received', '2026-05-11 06:28:57', '2026-05-11 06:29:59', NULL, 3, NULL, NULL),
(27, 'PO-20260511-9981', 1, NULL, NULL, 'Deliver to our promises', NULL, 3, NULL, NULL, '2026-05-11', '2026-05-13', 400000.00, 0.00, 400000.00, 'partially_received', '2026-05-11 13:57:22', '2026-05-11 13:59:46', NULL, 3, NULL, 'main offices'),
(28, 'PO-20260512-3148', 1, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-12', '2026-05-13', 2149999.40, 0.00, 2149999.40, 'partially_received', '2026-05-12 03:56:45', '2026-05-12 03:58:27', NULL, 3, NULL, 'Arena Mall, Middle east Resturant East wing'),
(29, 'PO-20260512-2658', 2, NULL, NULL, NULL, NULL, 3, NULL, NULL, '2026-05-12', '2026-05-14', 600000.00, 0.00, 600000.00, 'fully_received', '2026-05-12 05:19:54', '2026-05-12 05:27:02', NULL, 3, NULL, 'main offices'),
(30, 'PO-20260519-0417', 1, NULL, NULL, '48 hours to deliver the items', NULL, 3, NULL, NULL, '2026-05-19', '2026-05-21', 260000.00, 0.00, 260000.00, 'partially_received', '2026-05-19 10:24:14', '2026-05-19 10:28:53', NULL, 3, NULL, 'main offices');

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
(13, 9, 9, 'litres', NULL, 15.000000, 15.000000, 2000.00, 30000.00, '2026-04-17 09:59:49', '2026-05-22 11:48:33', 3, NULL),
(14, 9, 18, 'trays', NULL, 15.000000, 15.000000, 7500.00, 112500.00, '2026-04-17 09:59:49', '2026-05-22 11:48:33', 3, NULL),
(15, 9, 1, '3 bags', NULL, 15.000000, 15.000000, 120000.00, 1800000.00, '2026-04-17 09:59:49', '2026-05-22 11:48:33', 3, NULL),
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
(61, 29, 42, NULL, NULL, 30.000000, 30.000000, 20000.00, 600000.00, '2026-05-12 05:19:54', '2026-05-12 05:27:02', 3, NULL),
(62, 30, 38, NULL, NULL, 15.000000, 10.000000, 10000.00, 150000.00, '2026-05-19 10:24:14', '2026-05-19 10:28:53', 3, NULL),
(63, 30, 43, NULL, NULL, 10.000000, 10.000000, 11000.00, 110000.00, '2026-05-19 10:24:14', '2026-05-19 10:28:53', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_items`
--

CREATE TABLE `recipe_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Links to menu_items.id',
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Links to inventory_items.id (the ingredient)',
  `quantity_required` decimal(12,4) NOT NULL COMMENT 'How much of the ingredient (e.g., 160 grams)',
  `unit_of_measure_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Unit: grams, kg, pieces, ml, litre',
  `wastage_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Estimated waste during prep',
  `unit_cost_at_creation` decimal(15,2) DEFAULT 0.00 COMMENT 'Snapshot of ingredient cost when recipe was made',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Order of ingredients in display',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipe_items`
--

INSERT INTO `recipe_items` (`id`, `menu_item_id`, `inventory_item_id`, `quantity_required`, `unit_of_measure_id`, `wastage_percentage`, `unit_cost_at_creation`, `sort_order`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 157, 1.0000, 16, 0.00, 831.00, 0, NULL, '2026-05-21 09:45:27', '2026-05-21 09:45:27', NULL),
(2, 6, 248, 0.5000, 3, 0.00, 1831.00, 1, NULL, '2026-05-21 09:45:27', '2026-05-21 09:45:27', NULL),
(3, 6, 252, 0.0100, 6, 0.00, 350.00, 2, NULL, '2026-05-21 09:45:27', '2026-05-21 09:45:27', NULL),
(4, 7, 157, 1.0000, 5, 0.00, 831.00, 0, NULL, '2026-05-21 13:09:04', '2026-05-21 13:09:04', NULL),
(5, 2, 20, 1.0000, 16, 0.00, 100.00, 0, NULL, '2026-05-21 13:28:12', '2026-05-21 13:28:12', NULL),
(6, 1, 48, 0.0005, 5, 0.00, 22000.00, 0, NULL, '2026-05-21 13:36:56', '2026-05-21 13:36:56', NULL),
(7, 1, 108, 1.0000, 16, 0.00, 14000.00, 0, NULL, '2026-05-21 13:36:56', '2026-05-21 13:36:56', NULL),
(8, 8, 157, 1.0000, 16, 0.00, 831.00, 0, NULL, '2026-05-22 11:54:52', '2026-05-22 11:54:52', NULL),
(9, 8, 89, 0.0270, 3, 0.00, 2000.00, 1, NULL, '2026-05-22 11:54:52', '2026-05-22 11:54:52', NULL),
(10, 8, 95, 1.0000, 16, 0.00, 2876.00, 2, NULL, '2026-05-22 11:54:52', '2026-05-22 11:54:52', NULL),
(11, 9, 33, 0.0250, 4, 0.00, 180.00, 0, NULL, '2026-05-22 11:57:45', '2026-05-22 11:57:45', NULL),
(12, 10, 20, 1.0000, 16, 0.00, 100.00, 0, NULL, '2026-05-26 07:55:05', '2026-05-26 07:55:05', NULL),
(13, 10, 1, 0.0250, 3, 0.00, 4000.00, 1, NULL, '2026-05-26 07:55:05', '2026-05-26 07:55:05', NULL),
(14, 11, 157, 1.0000, 19, 0.00, 831.00, 0, NULL, '2026-05-26 09:01:48', '2026-05-26 09:01:48', NULL),
(15, 12, 194, 0.0500, 3, 0.00, 9000.00, 0, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL),
(16, 12, 139, 0.0005, 5, 0.00, 3000.00, 1, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL),
(17, 12, 79, 0.0005, 5, 0.00, 2700.00, 2, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL),
(18, 12, 202, 0.0020, 6, 0.00, 7250.00, 3, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL),
(19, 12, 169, 0.0050, 5, 0.00, 500.00, 4, NULL, '2026-05-26 09:10:08', '2026-05-26 09:10:08', NULL),
(20, 15, 18, 3.0000, 5, 0.00, 300.00, 0, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL),
(21, 15, 79, 0.1500, 5, 0.00, 2700.00, 1, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL),
(22, 15, 198, 2.0000, 16, 0.00, 1000.00, 2, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL),
(23, 15, 74, 0.0050, 5, 0.00, 204000.00, 3, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL),
(24, 15, 202, 0.0100, 6, 0.00, 7250.00, 4, NULL, '2026-05-27 09:04:40', '2026-05-27 09:04:40', NULL),
(25, 4, 210, 2.0000, 5, 0.00, 1000.00, 0, NULL, '2026-05-27 12:14:12', '2026-05-27 12:14:12', NULL);

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
(13, 'REQ-20260512-0001', 3, 2, 4, NULL, 'lpo_created', NULL, 'please , procurement officer draft the lpo for these items', NULL, NULL, NULL, '2026-05-12 05:16:56', '2026-05-12 05:15:57', '2026-05-12 05:18:19', NULL),
(14, 'REQ-20260519-0001', 3, 2, 4, '2026-05-23', 'lpo_created', NULL, NULL, NULL, NULL, NULL, '2026-05-19 10:17:04', '2026-05-19 10:15:40', '2026-05-19 10:19:05', NULL);

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
(56, 13, 42, NULL, 30.00, 'boxes', 'Water', 30.00, 'its urgently neede', '2026-05-12 05:15:57', '2026-05-12 05:16:56'),
(57, 14, 38, NULL, 20.00, 'cartons', 'Soft Drinks', 15.00, 'aurgently needed', '2026-05-19 10:15:40', '2026-05-19 10:17:04'),
(58, 14, 43, NULL, 10.00, 'cartons', 'Soft Drinks', 10.00, 'its over', '2026-05-19 10:15:40', '2026-05-19 10:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(50) NOT NULL COMMENT 'Table number/identifier (e.g., T01, T02, 1, 2)',
  `capacity` int(11) NOT NULL DEFAULT 2 COMMENT 'Number of seats/people the table can accommodate',
  `size` varchar(50) DEFAULT NULL COMMENT 'Table size (e.g., Small, Medium, Large, 2-seater, 4-seater, 6-seater)',
  `location` varchar(100) DEFAULT NULL COMMENT 'Table location (e.g., Indoor, Outdoor, Terrace, VIP, Smoking Area)',
  `description` text DEFAULT NULL COMMENT 'Additional description or notes about the table',
  `is_reserved` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Available, 1 = Reserved',
  `is_occupied` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = Inactive/Removed, 1 = Active',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Order for display',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`id`, `table_number`, `capacity`, `size`, `location`, `description`, `is_reserved`, `is_occupied`, `is_active`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TB001', 6, 'Large', 'Indoor', NULL, 1, 1, 1, 0, 4, 4, '2026-05-26 05:23:04', '2026-05-27 10:03:37', NULL),
(2, 'TB002', 4, 'Medium', 'Indoor', NULL, 0, 1, 1, 0, 4, NULL, '2026-05-26 05:29:12', '2026-05-27 11:00:52', NULL),
(3, 'TB003', 4, 'Medium', 'Indoor', NULL, 0, 1, 1, 0, 4, NULL, '2026-05-26 09:33:25', '2026-05-27 12:01:28', NULL),
(4, 'TB004', 6, 'Large', 'Outdoor', NULL, 1, 1, 1, 0, 4, NULL, '2026-05-26 09:34:02', '2026-05-27 12:17:28', NULL),
(5, 'TB005', 12, 'Extra Large', 'Outdoor', NULL, 0, 1, 1, 0, 4, NULL, '2026-05-27 04:30:52', '2026-05-27 12:32:17', NULL),
(6, 'TB006', 10, 'Large', 'Bar Area', NULL, 0, 1, 1, 0, 4, NULL, '2026-05-27 04:31:16', '2026-05-27 11:10:06', NULL),
(7, 'TB007', 12, 'VIP', 'VIP Room', NULL, 0, 1, 1, 0, 4, NULL, '2026-05-27 04:31:38', '2026-05-27 12:33:53', NULL),
(8, 'TB008', 6, 'Large', 'Bar Area', NULL, 0, 0, 1, 0, 4, NULL, '2026-05-27 05:31:47', '2026-05-27 05:31:47', NULL),
(9, 'TB009', 6, 'Medium', 'Indoor', NULL, 0, 0, 1, 0, 4, NULL, '2026-05-27 05:46:15', '2026-05-27 05:46:15', NULL),
(10, 'TB010', 8, 'Large', 'Garden', NULL, 0, 0, 1, 0, 4, NULL, '2026-05-27 05:46:42', '2026-05-27 05:46:42', NULL);

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
(5, 'bar_manager', 'Bar Manager', 'Manages bar operations and beverage inventory', 1, 0, NULL, 1, '2026-04-14 17:34:19', '2026-05-26 02:27:21', NULL),
(6, 'procurement_officer', 'Procurement Officer', 'Handles purchasing and vendor management', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(7, 'store_keeper', 'Store Keeper', 'Manages inventory and stock movements', 1, 1, NULL, NULL, '2026-04-14 17:34:19', '2026-04-14 17:34:19', NULL),
(8, 'cashier', 'Cashier', 'Handles POS and sales transactions', 1, 0, NULL, 1, '2026-04-14 17:34:19', '2026-05-26 02:27:47', NULL),
(9, 'waiter', 'Waiter', 'Takes orders and serves customers', 1, 1, NULL, 1, '2026-04-14 17:34:20', '2026-05-26 14:10:15', NULL),
(10, 'chef', 'Chef', 'Prepares food and updates kitchen status', 1, 0, NULL, 1, '2026-04-14 17:34:20', '2026-05-26 02:27:01', NULL),
(11, 'accountant', 'Accountant', 'Handles financial reports and transactions', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(12, 'viewer', 'Viewer (Read Only)', 'Can only view data, no modifications', 1, 1, NULL, NULL, '2026-04-14 17:34:20', '2026-04-14 17:34:20', NULL),
(13, 'DR001', 'DIRECTOR', 'This is director of the business', 1, 0, 1, NULL, '2026-04-29 11:07:55', '2026-04-29 11:07:55', NULL),
(14, 'BAR001', 'BAR MANAGER', NULL, 1, 0, 1, NULL, '2026-05-16 06:16:05', '2026-05-16 06:16:05', NULL),
(15, 'BC001', 'Bar Cashier', 'This bar cashier', 1, 0, 1, 1, '2026-05-18 02:32:58', '2026-05-26 02:06:49', NULL),
(16, 'SR001', 'STOCK CONTROLLER', 'Responsible for controlling stock, approving department requistions, and conducting stock counts', 1, 0, 1, NULL, '2026-05-26 01:58:35', '2026-05-26 01:58:35', NULL);

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
(198, 13, 46, '2026-04-29 11:07:56', '2026-04-29 11:07:56'),
(199, 14, 26, '2026-05-16 06:16:05', '2026-05-16 06:16:05'),
(200, 14, 36, '2026-05-16 06:16:05', '2026-05-16 06:16:05'),
(201, 14, 39, '2026-05-16 06:16:05', '2026-05-16 06:16:05'),
(202, 14, 40, '2026-05-16 06:16:05', '2026-05-16 06:16:05'),
(203, 14, 41, '2026-05-16 06:16:05', '2026-05-16 06:16:05'),
(204, 15, 38, '2026-05-18 02:32:58', '2026-05-18 02:32:58'),
(205, 15, 40, '2026-05-18 02:32:58', '2026-05-18 02:32:58'),
(206, 15, 41, '2026-05-18 02:32:58', '2026-05-18 02:32:58');

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `waiter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `cashier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_type` varchar(50) NOT NULL DEFAULT 'dine_in',
  `table_number` varchar(50) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(12,2) DEFAULT NULL,
  `change_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','mobile_money') DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'completed',
  `payment_status` enum('pending','paid','cancelled','unpaid') DEFAULT 'unpaid',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `order_number`, `table_id`, `waiter_id`, `notes`, `cashier_id`, `department_id`, `customer_type`, `table_number`, `subtotal`, `tax_amount`, `total_amount`, `amount_paid`, `change_amount`, `payment_method`, `status`, `payment_status`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'INV-20260515-7362', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 25000.00, 0.00, 25000.00, 30000.00, 5000.00, 'cash', 'completed', 'paid', 10, NULL, '2026-05-15 10:48:01', '2026-05-15 14:03:02', NULL),
(7, 'INV-20260515-9549', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 75000.00, 0.00, 75000.00, 100000.00, 25000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-15 13:11:34', '2026-05-15 13:35:59', NULL),
(8, 'INV-20260515-1130', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 5000.00, 0.00, 5000.00, 5000.00, 0.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-15 13:56:08', '2026-05-15 13:56:19', NULL),
(9, 'INV-20260515-6363', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 6000.00, 0.00, 6000.00, 10000.00, 4000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-15 13:57:56', '2026-05-15 13:58:05', NULL),
(10, 'INV-20260515-6781', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 28000.00, 0.00, 28000.00, 30000.00, 2000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-15 14:09:56', '2026-05-15 14:10:18', NULL),
(11, 'INV-20260515-9557', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 60000.00, 0.00, 60000.00, 60000.00, 0.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-15 15:19:44', '2026-05-15 15:21:59', NULL),
(12, 'INV-20260518-1707', NULL, NULL, NULL, 10, NULL, 'dine_in', NULL, 30000.00, 0.00, 30000.00, 30000.00, 0.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 01:58:01', '2026-05-18 02:18:01', NULL),
(13, 'INV-20260518-5632', NULL, NULL, NULL, 10, 9, 'dine_in', NULL, 25000.00, 0.00, 25000.00, 25000.00, 0.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 02:24:59', '2026-05-18 02:26:01', NULL),
(14, 'BAR-INV-20260518-4031', NULL, NULL, NULL, 13, 4, 'dine_in', NULL, 5000.00, 0.00, 5000.00, 10000.00, 5000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 11:26:16', '2026-05-18 11:26:16', NULL),
(15, 'BAR-INV-20260518-6245', NULL, NULL, NULL, 13, 4, 'dine_in', NULL, 20000.00, 0.00, 20000.00, 20000.00, 0.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 11:43:47', '2026-05-18 12:28:08', NULL),
(16, 'BAR-INV-20260518-5372', NULL, NULL, NULL, 13, 4, 'dine_in', NULL, 15000.00, 0.00, 15000.00, 20000.00, 5000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 12:28:41', '2026-05-18 12:29:11', NULL),
(17, 'BAR-INV-20260518-6388', NULL, NULL, NULL, 13, 4, 'dine_in', NULL, 75000.00, 0.00, 75000.00, 80000.00, 5000.00, 'cash', 'completed', 'paid', NULL, NULL, '2026-05-18 12:44:34', '2026-05-18 12:44:34', NULL),
(21, 'ORD202605270001', NULL, NULL, NULL, NULL, NULL, 'dine_in', 'TB001', 30000.00, 0.00, 30000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 10:03:37', '2026-05-27 10:03:37', NULL),
(22, 'ORD202605270002', 2, 15, 'Customer wants chill in this order', NULL, NULL, 'dine_in', 'TB002', 25000.00, 0.00, 25000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 11:00:52', '2026-05-27 11:00:52', NULL),
(23, 'ORD202605270003', 6, 15, 'the customer wants it cold', NULL, NULL, 'dine_in', 'TB006', 5085.68, 0.00, 5085.68, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 11:10:06', '2026-05-27 11:10:06', NULL),
(24, 'ORD202605270004', 3, 15, NULL, NULL, NULL, 'dine_in', 'TB003', 5000.00, 0.00, 5000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 12:01:28', '2026-05-27 12:01:28', NULL),
(25, 'ORD202605270005', 4, 15, NULL, NULL, NULL, 'dine_in', 'TB004', 5000.00, 0.00, 5000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 12:17:28', '2026-05-27 12:17:28', NULL),
(26, 'ORD202605270006', 5, 15, NULL, NULL, NULL, 'dine_in', 'TB005', 140000.00, 0.00, 140000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 12:32:17', '2026-05-27 12:32:17', NULL),
(27, 'ORD202605270007', 7, 15, 'bring two glasses', NULL, NULL, 'dine_in', 'TB007', 70000.00, 0.00, 70000.00, NULL, 0.00, NULL, 'pending', 'unpaid', 15, NULL, '2026-05-27 12:33:53', '2026-05-27 12:33:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sales_order_id` bigint(20) UNSIGNED NOT NULL,
  `menu_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inventory_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `sales_order_id`, `menu_item_id`, `inventory_item_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 6, NULL, 24, 'Fanta Orange', 1, 3000.00, 3000.00, NULL, '2026-05-15 10:48:01', '2026-05-15 10:48:01', NULL),
(5, 6, 3, NULL, 'Margherita Pizza', 1, 22000.00, 22000.00, NULL, '2026-05-15 10:48:01', '2026-05-15 10:48:01', NULL),
(6, 7, 2, NULL, 'Grilled Chicken Breast', 3, 25000.00, 75000.00, NULL, '2026-05-15 13:11:34', '2026-05-15 13:11:34', NULL),
(7, 8, 4, NULL, 'Fresh Orange Juice', 1, 5000.00, 5000.00, NULL, '2026-05-15 13:56:08', '2026-05-15 13:56:08', NULL),
(8, 9, NULL, 38, 'Mountain dew', 1, 3000.00, 3000.00, NULL, '2026-05-15 13:57:56', '2026-05-15 13:57:56', NULL),
(9, 9, NULL, 24, 'Fanta Orange', 1, 3000.00, 3000.00, NULL, '2026-05-15 13:57:56', '2026-05-15 13:57:56', NULL),
(10, 10, 3, NULL, 'Margherita Pizza', 1, 22000.00, 22000.00, NULL, '2026-05-15 14:09:56', '2026-05-15 14:09:56', NULL),
(11, 10, NULL, 24, 'Fanta Orange', 2, 3000.00, 6000.00, NULL, '2026-05-15 14:09:56', '2026-05-15 14:09:56', NULL),
(12, 11, 1, NULL, 'Grilled Chicken Breast', 2, 25000.00, 50000.00, NULL, '2026-05-15 15:19:44', '2026-05-15 15:19:44', NULL),
(13, 11, 4, NULL, 'Fresh Orange Juice', 2, 5000.00, 10000.00, NULL, '2026-05-15 15:19:44', '2026-05-15 15:19:44', NULL),
(14, 12, 4, NULL, 'Fresh Orange Juice', 1, 5000.00, 5000.00, NULL, '2026-05-18 01:58:01', '2026-05-18 01:58:01', NULL),
(15, 12, 3, NULL, 'Margherita Pizza', 1, 22000.00, 22000.00, NULL, '2026-05-18 01:58:01', '2026-05-18 01:58:01', NULL),
(16, 12, NULL, 38, 'Mountain dew', 1, 3000.00, 3000.00, NULL, '2026-05-18 01:58:01', '2026-05-18 01:58:01', NULL),
(17, 13, 2, NULL, 'Grilled Chicken Breast', 1, 25000.00, 25000.00, NULL, '2026-05-18 02:24:59', '2026-05-18 02:24:59', NULL),
(18, 14, NULL, 28, 'Club Beer', 1, 5000.00, 5000.00, NULL, '2026-05-18 11:26:16', '2026-05-18 11:26:16', NULL),
(19, 15, NULL, 28, 'Club Beer', 4, 5000.00, 20000.00, NULL, '2026-05-18 11:43:47', '2026-05-18 11:43:47', NULL),
(20, 16, NULL, 28, 'Club Beer', 3, 5000.00, 15000.00, NULL, '2026-05-18 12:28:41', '2026-05-18 12:28:41', NULL),
(21, 17, NULL, 44, 'Four cousins', 1, 70000.00, 70000.00, NULL, '2026-05-18 12:44:34', '2026-05-18 12:44:34', NULL),
(22, 17, NULL, 28, 'Club Beer', 1, 5000.00, 5000.00, NULL, '2026-05-18 12:44:34', '2026-05-18 12:44:34', NULL),
(26, 21, 6, NULL, 'Margherita Pizza', 1, 30000.00, 30000.00, NULL, '2026-05-27 10:03:37', '2026-05-27 10:03:37', NULL),
(27, 22, 2, NULL, 'Grilled Chicken Breast', 1, 25000.00, 25000.00, NULL, '2026-05-27 11:00:52', '2026-05-27 11:00:52', NULL),
(28, 23, 11, NULL, 'COCOTAIL BAR MENU', 1, 5085.68, 5085.68, NULL, '2026-05-27 11:10:06', '2026-05-27 11:10:06', NULL),
(29, 24, 14, NULL, 'Mountain dew small', 1, 5000.00, 5000.00, NULL, '2026-05-27 12:01:28', '2026-05-27 12:01:28', NULL),
(30, 25, 4, NULL, 'Fresh Orange Juice', 1, 5000.00, 5000.00, NULL, '2026-05-27 12:17:28', '2026-05-27 12:17:28', NULL),
(31, 26, 16, NULL, 'Four cusins', 2, 70000.00, 140000.00, NULL, '2026-05-27 12:32:17', '2026-05-27 12:32:17', NULL),
(32, 27, 16, NULL, 'Four cusins', 1, 70000.00, 70000.00, NULL, '2026-05-27 12:33:53', '2026-05-27 12:33:53', NULL);

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
('etr0keF5f7E2c3xNfFyZyMgdsmFETfP4Ru8Uwryq', 15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoienNkZjZNa0lhUHlhN09BMFZwN0Y5ek14M3ptaktNM0xJak9PNTFwWCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC93YWl0ZXIvb3JkZXJzL2FjdGl2ZSI7czo1OiJyb3V0ZSI7czoyMDoid2FpdGVyLmFjdGl2ZS1vcmRlcnMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNTt9', 1779906033),
('fAgjfU8xZXIHCDJACARaV4Umxj5syJr0Dtfb0LIH', 10, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieXJTbmVpNXdHbmx4UkY0TWljSFZGdkdFQ3BHd2l1QVBScGN6YVBhQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYXNoaWVyL3RhYmxlLzIvb3JkZXIiO3M6NToicm91dGUiO3M6Mjc6ImNhc2hpZXIuY2FzaGllci50YWJsZS5vcmRlciI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjEwO30=', 1779905990),
('ZBJfzIK9evJpq8Gx4JIMLX4Vw2zClDctZyADEPaX', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiazFHTXJzS3p2amliQU9HZVh4SVI5a2VtVklobTQwQnlUZVA4SDc2MyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3VzZXJzLzEwIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1779905907);

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
-- Table structure for table `stock_counts`
--

CREATE TABLE `stock_counts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `count_number` varchar(50) NOT NULL,
  `location_type` enum('store','department') NOT NULL COMMENT 'Where the count is being done',
  `location_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID of store OR department',
  `count_date` date NOT NULL,
  `status` enum('draft','in_progress','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_counts`
--

INSERT INTO `stock_counts` (`id`, `count_number`, `location_type`, `location_id`, `count_date`, `status`, `created_by`, `completed_by`, `completed_at`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'CNT-20260522-0001', 'store', 1, '2026-05-22', 'cancelled', 4, NULL, NULL, NULL, '2026-05-22 08:16:50', '2026-05-22 08:17:16', NULL),
(3, 'CNT-20260522-0002', 'store', 1, '2026-05-22', 'completed', 4, 4, '2026-05-22 09:21:09', NULL, '2026-05-22 08:20:29', '2026-05-22 09:21:09', NULL),
(4, 'CNT-20260522-0003', 'department', 4, '2026-05-22', 'completed', 4, 4, '2026-05-22 09:47:37', NULL, '2026-05-22 09:44:29', '2026-05-22 09:47:37', NULL),
(5, 'CNT-20260522-0004', 'department', 8, '2026-05-22', 'in_progress', 4, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:50:06', NULL),
(6, 'CNT-20260522-0005', 'store', 1, '2026-05-22', 'in_progress', 4, NULL, NULL, NULL, '2026-05-22 12:05:21', '2026-05-22 12:05:28', NULL),
(7, 'CNT-20260525-0001', 'store', 1, '2026-05-25', 'draft', 4, NULL, NULL, NULL, '2026-05-25 11:14:48', '2026-05-25 11:14:48', NULL),
(8, 'CNT-20260525-0002', 'department', 4, '2026-05-25', 'draft', 4, NULL, NULL, NULL, '2026-05-25 13:10:06', '2026-05-25 13:10:06', NULL),
(9, 'CNT-20260525-0003', 'department', 8, '2026-05-25', 'draft', 4, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_count_items`
--

CREATE TABLE `stock_count_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_count_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `system_quantity` decimal(15,6) NOT NULL DEFAULT 0.000000 COMMENT 'Expected quantity from system',
  `physical_quantity` decimal(15,6) NOT NULL DEFAULT 0.000000 COMMENT 'Actual counted quantity',
  `physical_quantity_is_gross` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if physical_quantity is gross weight (with container), 0 if net weight',
  `variance` decimal(15,6) NOT NULL DEFAULT 0.000000 COMMENT 'physical - system (negative or positive)',
  `unit_cost` decimal(15,2) DEFAULT NULL COMMENT 'Current unit cost for value calculation',
  `variance_value` decimal(15,2) DEFAULT NULL COMMENT 'variance × unit_cost',
  `reason_code` varchar(100) DEFAULT NULL COMMENT 'e.g., THEFT, DAMAGE, EXPIRY, MISCOUNT',
  `reason_notes` text DEFAULT NULL COMMENT 'Detailed explanation from management',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Management user who approved variance',
  `approved_at` timestamp NULL DEFAULT NULL,
  `adjustment_movement_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Links to stock_movements if adjustment made',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock_count_items`
--

INSERT INTO `stock_count_items` (`id`, `stock_count_id`, `inventory_item_id`, `system_quantity`, `physical_quantity`, `physical_quantity_is_gross`, `variance`, `unit_cost`, `variance_value`, `reason_code`, `reason_notes`, `approved_by`, `approved_at`, `adjustment_movement_id`, `created_at`, `updated_at`) VALUES
(1, 2, 99, 440.000000, 400.000000, 0, -40.000000, 831.00, -33240.00, NULL, 'Damaged', NULL, NULL, NULL, '2026-05-22 08:16:50', '2026-05-22 08:16:50'),
(2, 3, 157, 500.000000, 500.000000, 0, 0.000000, 831.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 08:20:29', '2026-05-22 08:20:29'),
(3, 3, 212, 0.000000, 1.000000, 0, 1.000000, 175000.00, 175000.00, NULL, 'Miscount', NULL, NULL, NULL, '2026-05-22 08:20:29', '2026-05-22 08:20:29'),
(4, 3, 1, 265.000000, 263.000000, 0, -2.000000, 4000.00, -8000.00, NULL, 'Expiry', NULL, NULL, NULL, '2026-05-22 08:20:29', '2026-05-22 08:20:29'),
(5, 4, 28, 51.000000, 51.000000, 0, 0.000000, 2500.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:44:29', '2026-05-22 09:44:29'),
(6, 4, 44, 9.000000, 9.000000, 0, 0.000000, 35000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:44:29', '2026-05-22 09:44:29'),
(7, 5, 1, 0.000000, 2.000000, 0, 2.000000, 4000.00, 8000.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(8, 5, 7, 4.000000, 4.000000, 0, 0.000000, 3500.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(9, 5, 18, 0.000000, 0.000000, 0, 0.000000, 300.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(10, 5, 21, 22.000000, 21.000000, 0, -1.000000, 10000.00, -10000.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(11, 5, 24, 0.000000, 0.000000, 0, 0.000000, 9999.99, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(12, 5, 38, 0.000000, 0.000000, 0, 0.000000, 10000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(13, 5, 53, 0.000000, 0.000000, 0, 0.000000, 28000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(14, 5, 95, 10.000000, 10.000000, 0, 0.000000, 2876.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(15, 5, 99, 60.000000, 55.000000, 0, -5.000000, 831.00, -4155.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(16, 5, 157, 0.000000, 0.000000, 0, 0.000000, 831.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(17, 5, 212, 50.000000, 45.000000, 0, -5.000000, 175000.00, -875000.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(18, 5, 229, 75.020000, 75.000000, 0, -0.020000, 6600.00, -132.00, NULL, NULL, NULL, NULL, NULL, '2026-05-22 09:49:44', '2026-05-22 09:49:44'),
(19, 6, 229, 24.980000, 24.000000, 0, -0.980000, 6600.00, -6468.00, NULL, 'Damaged', NULL, NULL, NULL, '2026-05-22 12:05:21', '2026-05-22 12:05:21'),
(20, 6, 104, 100.000000, 95.000000, 0, -5.000000, 56000.00, -280000.00, NULL, 'Spillage', NULL, NULL, NULL, '2026-05-22 12:05:21', '2026-05-22 12:05:21'),
(21, 7, 190, 50.000000, 50.000000, 0, 0.000000, 30000.00, 0.00, NULL, 'Damaged', NULL, NULL, NULL, '2026-05-25 11:14:48', '2026-05-25 12:58:18'),
(22, 8, 28, 51.000000, 52.000000, 0, 1.000000, 2500.00, 2500.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:10:06', '2026-05-25 13:10:06'),
(23, 8, 44, 9.000000, 8.000000, 0, -1.000000, 35000.00, -35000.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:10:06', '2026-05-25 13:10:06'),
(24, 9, 1, 0.000000, 0.000000, 0, 0.000000, 4000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(25, 9, 7, 4.000000, 4.000000, 0, 0.000000, 3500.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(26, 9, 18, 0.000000, 0.000000, 0, 0.000000, 300.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(27, 9, 21, 22.000000, 21.000000, 0, -1.000000, 10000.00, -10000.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(28, 9, 24, 0.000000, 0.000000, 0, 0.000000, 9999.99, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(29, 9, 38, 0.000000, 0.000000, 0, 0.000000, 10000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(30, 9, 53, 0.000000, 0.000000, 0, 0.000000, 28000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(31, 9, 89, 50.000000, 50.000000, 0, 0.000000, 2000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(32, 9, 95, 10.000000, 10.000000, 0, 0.000000, 2876.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(33, 9, 99, 60.000000, 60.000000, 0, 0.000000, 831.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(34, 9, 157, 0.000000, 0.000000, 0, 0.000000, 831.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(35, 9, 212, 50.000000, 50.000000, 0, 0.000000, 175000.00, 0.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07'),
(36, 9, 229, 85.020000, 85.000000, 0, -0.020000, 6600.00, -132.00, NULL, NULL, NULL, NULL, NULL, '2026-05-25 13:11:07', '2026-05-25 13:11:07');

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
(38, 'STK-ADJ-20260512-7830', 9, 1, 2, NULL, 10.000000, NULL, 'litre', NULL, NULL, NULL, 10.000000, 175.00, 185.00, 25000.00, 250000.00, 'recount (Manual adjustment)', NULL, NULL, '2026-05-12', '2026-05-12 09:23:36', 2, NULL, NULL, 0, NULL, '2026-05-12 09:23:36', '2026-05-12 09:23:36', NULL, 2, NULL),
(39, 'RET-20260513-5118', 24, 1, 2, NULL, 10.000000, 'unit', 'bottle', 1, 10, NULL, 10.000000, 710.00, 720.00, 9999.99, 99999.90, 'RETURN from KITCHEN - Req: KIT-REQ-20260512-1915 - 10 unit(s) (10 pieces) - Not sold out', NULL, 'James', '2026-05-13', '2026-05-13 12:08:23', 2, NULL, NULL, 0, NULL, '2026-05-13 12:08:23', '2026-05-13 12:08:23', NULL, 2, NULL),
(40, 'ISS-20260513-8085', 21, 1, 5, NULL, 20.000000, NULL, 'pcs', NULL, NULL, NULL, 20.000000, 25.00, 5.00, 10000.00, 200000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260513-4130', 'BARIGYE DAVIS', NULL, '2026-05-13', '2026-05-13 12:20:26', 2, NULL, NULL, 0, NULL, '2026-05-13 12:20:26', '2026-05-13 12:20:26', NULL, 2, NULL),
(41, 'ISS-20260513-1004', 38, 1, 5, NULL, 5.000000, 'carton', 'bottle', 12, 5, NULL, 60.000000, 206.00, 146.00, 0.00, 0.00, 'Issued to KITCHEN - Req: KIT-REQ-20260513-4130', 'BARIGYE DAVIS', NULL, '2026-05-13', '2026-05-13 12:20:26', 2, NULL, NULL, 0, NULL, '2026-05-13 12:20:26', '2026-05-13 12:20:26', NULL, 2, NULL),
(42, 'RET-20260513-5517', 38, 1, 2, NULL, 30.000000, 'carton', 'bottle', 12, 2, NULL, 30.000000, 146.00, 176.00, 0.00, 0.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260513-4130 - 2 carton(s) (24 pieces) + 6 individual pieces - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-13', '2026-05-13 12:31:05', 2, NULL, NULL, 0, NULL, '2026-05-13 12:31:05', '2026-05-13 12:31:05', NULL, 2, NULL),
(43, 'ISS-20260513-4147', 38, 1, 5, NULL, 5.000000, 'carton', 'bottle', 12, 5, NULL, 60.000000, 176.00, 116.00, 0.00, 0.00, 'Issued to RESTAURANT - Req: REST-REQ-20260513-7586', 'David', NULL, '2026-05-13', '2026-05-13 14:19:36', 2, NULL, NULL, 0, NULL, '2026-05-13 14:19:36', '2026-05-13 14:19:36', NULL, 2, NULL),
(44, 'RET-20260514-4293', 38, 1, 2, NULL, 12.000000, 'carton', 'bottle', 12, 1, NULL, 12.000000, 116.00, 128.00, 0.00, 0.00, 'RETURN from RESTAURANT - Req: REST-REQ-20260513-7586 - 1 carton(s) (12 pieces) - Not sold out', NULL, 'James', '2026-05-14', '2026-05-14 04:03:16', 2, NULL, NULL, 0, NULL, '2026-05-14 04:03:16', '2026-05-14 04:03:16', NULL, 2, NULL),
(45, 'RET-20260514-8627', 7, 1, 2, NULL, 10.000000, NULL, 'pcs', 1, NULL, NULL, 10.000000, 95.00, 105.00, 3500.00, 35000.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260512-0093 - 10 individual pieces - not  used', NULL, 'BARIGYE DAVIS', '2026-05-14', '2026-05-14 13:19:11', 2, NULL, NULL, 0, NULL, '2026-05-14 13:19:11', '2026-05-14 13:19:11', NULL, 2, NULL),
(46, 'RET-20260514-4438', 1, 1, 2, NULL, 10.000000, NULL, 'pcs', 1, NULL, NULL, 10.000000, 255.00, 265.00, 4000.00, 40000.00, 'RETURN from KITCHEN - Req: KIT-REQ-20260512-0093 - 10 individual pieces - not  used', NULL, 'BARIGYE DAVIS', '2026-05-14', '2026-05-14 13:19:11', 2, NULL, NULL, 0, NULL, '2026-05-14 13:19:11', '2026-05-14 13:19:11', NULL, 2, NULL),
(47, 'ISS-20260515-3857', 24, 1, 5, NULL, 4.000000, 'carton', 'bottle', 12, 4, NULL, 48.000000, 720.00, 672.00, 9999.99, 479999.52, 'Issued to RESTAURANT - Req: REST-REQ-20260515-5414', 'BARIGYE DAVIS', NULL, '2026-05-15', '2026-05-15 04:14:46', 2, NULL, NULL, 0, NULL, '2026-05-15 04:14:46', '2026-05-15 04:14:46', NULL, 2, NULL),
(48, 'ISS-20260515-6670', 38, 1, 5, NULL, 6.000000, 'carton', 'bottle', 12, 6, NULL, 72.000000, 128.00, 56.00, 0.00, 0.00, 'Issued to RESTAURANT - Req: REST-REQ-20260515-5414', 'BARIGYE DAVIS', NULL, '2026-05-15', '2026-05-15 04:14:46', 2, NULL, NULL, 0, NULL, '2026-05-15 04:14:46', '2026-05-15 04:14:46', NULL, 2, NULL),
(49, 'RET-20260515-1458', 24, 1, 2, NULL, 3.000000, NULL, 'bottle', 12, NULL, NULL, 3.000000, 672.00, 675.00, 9999.99, 29999.97, 'RETURN from RESTAURANT - Req: REST-REQ-20260515-5414 - 3 individual pieces - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-15', '2026-05-15 04:27:13', 2, NULL, NULL, 0, NULL, '2026-05-15 04:27:13', '2026-05-15 04:27:13', NULL, 2, NULL),
(50, 'RET-20260515-4980', 38, 1, 2, NULL, 11.000000, NULL, 'bottle', 12, NULL, NULL, 11.000000, 56.00, 67.00, 0.00, 0.00, 'RETURN from RESTAURANT - Req: REST-REQ-20260515-5414 - 11 individual pieces - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-15', '2026-05-15 04:27:13', 2, NULL, NULL, 0, NULL, '2026-05-15 04:27:13', '2026-05-15 04:27:13', NULL, 2, NULL),
(51, 'ISS-20260515-6637', 38, 1, 5, NULL, 5.000000, 'carton', 'bottle', 12, 5, NULL, 60.000000, 67.00, 7.00, 0.00, 0.00, 'Issued to RESTAURANT - Req: REST-REQ-20260515-4325', 'DANIEL', NULL, '2026-05-15', '2026-05-15 05:18:35', 2, NULL, NULL, 0, NULL, '2026-05-15 05:18:35', '2026-05-15 05:18:35', NULL, 2, NULL),
(52, 'ISS-20260515-2839', 42, 1, 5, NULL, 5.000000, 'carton', 'bottle', 24, 5, NULL, 120.000000, 580.00, 460.00, 20000.00, 2400000.00, 'Issued to RESTAURANT - Req: REST-REQ-20260515-4325', 'DANIEL', NULL, '2026-05-15', '2026-05-15 05:18:35', 2, NULL, NULL, 0, NULL, '2026-05-15 05:18:35', '2026-05-15 05:18:35', NULL, 2, NULL),
(53, 'ISS-20260515-0727', 24, 1, 5, NULL, 5.000000, 'carton', 'bottle', 11, 5, NULL, 55.000000, 675.00, 620.00, 9999.99, 549999.45, 'Issued to RESTAURANT - Req: REST-REQ-20260515-4325', 'DANIEL', NULL, '2026-05-15', '2026-05-15 05:18:35', 2, NULL, NULL, 0, NULL, '2026-05-15 05:18:35', '2026-05-15 05:18:35', NULL, 2, NULL),
(54, 'RET-20260515-0449', 38, 1, 2, NULL, 60.000000, 'carton', 'bottle', 12, 5, NULL, 60.000000, 7.00, 67.00, 0.00, 0.00, 'RETURN from RESTAURANT - Req: REST-REQ-20260515-4325 - 5 carton(s) (60 pieces) - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-15', '2026-05-15 06:01:48', 2, NULL, NULL, 0, NULL, '2026-05-15 06:01:48', '2026-05-15 06:01:48', NULL, 2, NULL),
(55, 'RET-20260515-6932', 42, 1, 2, NULL, 120.000000, 'carton', 'bottle', 24, 5, NULL, 120.000000, 460.00, 580.00, 20000.00, 2400000.00, 'RETURN from RESTAURANT - Req: REST-REQ-20260515-4325 - 5 carton(s) (120 pieces) - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-15', '2026-05-15 06:01:48', 2, NULL, NULL, 0, NULL, '2026-05-15 06:01:48', '2026-05-15 06:01:48', NULL, 2, NULL),
(56, 'RET-20260515-6843', 24, 1, 2, NULL, 55.000000, 'carton', 'bottle', 11, 5, NULL, 55.000000, 620.00, 675.00, 9999.99, 549999.45, 'RETURN from RESTAURANT - Req: REST-REQ-20260515-4325 - 5 carton(s) (55 pieces) - Not sold out', NULL, 'BARIGYE DAVIS', '2026-05-15', '2026-05-15 06:01:48', 2, NULL, NULL, 0, NULL, '2026-05-15 06:01:48', '2026-05-15 06:01:48', NULL, 2, NULL),
(57, 'ISS-20260515-3868', 38, 1, 5, NULL, 1.000000, 'carton', 'bottle', 12, 1, NULL, 12.000000, 67.00, 55.00, 0.00, 0.00, 'Issued to RESTAURANT - Req: REST-REQ-20260515-9891', 'DANIEL', NULL, '2026-05-15', '2026-05-15 06:18:46', 2, NULL, NULL, 0, NULL, '2026-05-15 06:18:46', '2026-05-15 06:18:46', NULL, 2, NULL),
(58, 'ISS-20260515-5776', 24, 1, 5, NULL, 5.000000, 'carton', 'bottle', 12, 5, NULL, 60.000000, 675.00, 615.00, 9999.99, 599999.40, 'Issued to RESTAURANT - Req: REST-REQ-20260515-8720', 'DANIEL', NULL, '2026-05-15', '2026-05-15 07:06:42', 2, NULL, NULL, 0, NULL, '2026-05-15 07:06:42', '2026-05-15 07:06:42', NULL, 2, NULL),
(59, 'STK-IN-20260516-1716', 43, 1, 2, NULL, 20.000000, 'crate', 'bottle', 13, 20, NULL, 260.000000, 0.00, 260.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-16', '2026-05-16 05:41:27', 2, NULL, NULL, 0, NULL, '2026-05-16 05:41:27', '2026-05-16 05:41:27', NULL, 2, NULL),
(60, 'STK-IN-20260516-1939', 44, 1, 2, NULL, 30.000000, NULL, 'bottle', NULL, NULL, NULL, 30.000000, 0.00, 30.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-16', '2026-05-16 06:11:29', 2, NULL, NULL, 0, NULL, '2026-05-16 06:11:29', '2026-05-16 06:11:29', NULL, 2, NULL),
(61, 'ISS-20260518-9697', 28, 1, 5, NULL, 5.000000, 'crate', 'piece', 12, 5, NULL, 60.000000, 440.00, 380.00, 55000.00, 3300000.00, 'Issued to BAR - Req: BAR-REQ-20260518-4151', 'DANIEL', NULL, '2026-05-18', '2026-05-18 06:47:09', 2, NULL, NULL, 0, NULL, '2026-05-18 06:47:09', '2026-05-18 06:47:09', NULL, 2, NULL),
(62, 'ISS-20260518-3756', 44, 1, 5, NULL, 10.000000, NULL, 'bottle', NULL, NULL, NULL, 10.000000, 30.00, 20.00, 0.00, 0.00, 'Issued to BAR - Req: BAR-REQ-20260518-4151', 'DANIEL', NULL, '2026-05-18', '2026-05-18 06:47:09', 2, NULL, NULL, 0, NULL, '2026-05-18 06:47:09', '2026-05-18 06:47:09', NULL, 2, NULL),
(63, 'STK-IN-20260519-8566', 45, 1, 2, NULL, 20.000000, 'carton', 'bottle', 11, 20, NULL, 220.000000, 0.00, 220.00, 0.00, 0.00, 'Manual inventory entry', NULL, NULL, '2026-05-19', '2026-05-19 06:44:45', 2, NULL, NULL, 0, NULL, '2026-05-19 06:44:45', '2026-05-19 06:44:45', NULL, 2, NULL),
(64, 'STK-GRN-20260519-7080', 38, 1, 1, NULL, 10.000000, 'carton', 'bottle', 12, 10, NULL, 120.000000, 55.00, 175.00, 10000.00, 1200000.00, 'Received from GRN: GRN-20260519-7336', NULL, NULL, '2026-05-19', '2026-05-19 10:30:23', 2, NULL, 10, 0, NULL, '2026-05-19 10:30:23', '2026-05-19 10:30:23', NULL, 2, NULL),
(65, 'STK-GRN-20260519-6114', 43, 1, 1, NULL, 10.000000, 'crate', 'bottle', 12, 10, NULL, 120.000000, 260.00, 380.00, 11000.00, 1320000.00, 'Received from GRN: GRN-20260519-7336', NULL, NULL, '2026-05-19', '2026-05-19 10:30:23', 2, NULL, 10, 0, NULL, '2026-05-19 10:30:23', '2026-05-19 10:30:23', NULL, 2, NULL),
(66, 'ISS-20260519-4516', 43, 1, 5, NULL, 2.000000, NULL, 'bottle', NULL, NULL, NULL, 2.000000, 380.00, 378.00, 11000.00, 22000.00, 'Issued to RESTAURANT - Req: REST-REQ-20260519-7060', 'DANIEL', NULL, '2026-05-19', '2026-05-19 10:38:54', 2, NULL, NULL, 0, NULL, '2026-05-19 10:38:54', '2026-05-19 10:38:54', NULL, 2, NULL),
(67, 'STK-IN-20260520-0513', 46, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 56000.00, 5600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(68, 'STK-IN-20260520-8856', 47, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 14000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(69, 'STK-IN-20260520-3657', 48, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(70, 'STK-IN-20260520-9790', 49, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 18000.00, 1800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(71, 'STK-IN-20260520-3089', 50, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 14000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(72, 'STK-IN-20260520-0585', 51, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 39000.00, 3900000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(73, 'STK-IN-20260520-3327', 52, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 10000.00, 1000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(74, 'STK-IN-20260520-7947', 53, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 28000.00, 2800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(75, 'STK-IN-20260520-1060', 54, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 30000.00, 3000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(76, 'STK-IN-20260520-8619', 55, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 21000.00, 2100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(77, 'STK-IN-20260520-4224', 56, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 30000.00, 3000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(78, 'STK-IN-20260520-2691', 57, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 35000.00, 3500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(79, 'STK-IN-20260520-3116', 58, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(80, 'STK-IN-20260520-4995', 59, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(81, 'STK-IN-20260520-6869', 60, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 310000.00, 31000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(82, 'STK-IN-20260520-5559', 61, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 99300.00, 9930000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(83, 'STK-IN-20260520-3807', 62, 1, 2, NULL, 50.000000, NULL, 'piece', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5762.00, 288100.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(84, 'STK-IN-20260520-2313', 63, 1, 2, NULL, 50.000000, NULL, 'piece', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(85, 'STK-IN-20260520-9680', 64, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(86, 'STK-IN-20260520-3109', 65, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 27000.00, 2700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(87, 'STK-IN-20260520-1952', 66, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 40000.00, 4000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(88, 'STK-IN-20260520-9120', 67, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(89, 'STK-IN-20260520-5722', 68, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 10000.00, 1000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(90, 'STK-IN-20260520-6644', 69, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 23000.00, 2300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(91, 'STK-IN-20260520-4735', 70, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 428.00, 214000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(92, 'STK-IN-20260520-0173', 71, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(93, 'STK-IN-20260520-0679', 72, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(94, 'STK-IN-20260520-1348', 73, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 79000.00, 3950000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(95, 'STK-IN-20260520-9513', 74, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 204000.00, 10200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(96, 'STK-IN-20260520-8624', 75, 1, 2, NULL, 200.000000, NULL, 'litre', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 3000.00, 600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(97, 'STK-IN-20260520-8234', 76, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17500.00, 1750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(98, 'STK-IN-20260520-8835', 77, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 105000.00, 5250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(99, 'STK-IN-20260520-3771', 78, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1335.00, 667500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(100, 'STK-IN-20260520-2135', 79, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 2700.00, 540000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(101, 'STK-IN-20260520-6622', 80, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3500.00, 350000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(102, 'STK-IN-20260520-6603', 81, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 3000.00, 600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(103, 'STK-IN-20260520-5859', 82, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(104, 'STK-IN-20260520-1894', 83, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 2000.00, 100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(105, 'STK-IN-20260520-3263', 84, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 1500.00, 75000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(106, 'STK-IN-20260520-4959', 85, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 1500.00, 150000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(107, 'STK-IN-20260520-4861', 86, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 1000.00, 50000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(108, 'STK-IN-20260520-7737', 87, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5000.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(109, 'STK-IN-20260520-3258', 88, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2000.00, 200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(110, 'STK-IN-20260520-2277', 89, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2000.00, 200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(111, 'STK-IN-20260520-3948', 90, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(112, 'STK-IN-20260520-4515', 91, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 5000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(113, 'STK-IN-20260520-0252', 92, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2500.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(114, 'STK-IN-20260520-4164', 93, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:21', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(115, 'STK-IN-20260520-6860', 94, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 2500.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(116, 'STK-IN-20260520-2871', 95, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 2876.00, 1438000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(117, 'STK-IN-20260520-2811', 96, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 4000.00, 400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(118, 'STK-IN-20260520-7569', 97, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2500.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(119, 'STK-IN-20260520-8520', 98, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 11.00, 5500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(120, 'STK-IN-20260520-3764', 99, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 831.00, 415500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(121, 'STK-IN-20260520-4844', 100, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 8000.00, 800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(122, 'STK-IN-20260520-8162', 101, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17000.00, 1700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(123, 'STK-IN-20260520-1490', 102, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1264.00, 632000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:37:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(125, 'STK-IN-20260520-000001', 104, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 56000.00, 5600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(126, 'STK-IN-20260520-000002', 105, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 14000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(127, 'STK-IN-20260520-000003', 106, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(128, 'STK-IN-20260520-000004', 107, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 18000.00, 1800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(129, 'STK-IN-20260520-000005', 108, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 14000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(130, 'STK-IN-20260520-000006', 109, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 39000.00, 3900000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(131, 'STK-IN-20260520-000007', 110, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 10000.00, 1000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(132, 'STK-IN-20260520-000008', 111, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 28000.00, 2800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(133, 'STK-IN-20260520-000009', 112, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 30000.00, 3000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(134, 'STK-IN-20260520-000010', 113, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 21000.00, 2100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:44:41', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(135, 'STK-IN-20260520-000011', 114, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 30000.00, 3000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(136, 'STK-IN-20260520-000012', 115, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 35000.00, 3500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(137, 'STK-IN-20260520-000013', 116, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(138, 'STK-IN-20260520-000014', 117, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(139, 'STK-IN-20260520-000015', 118, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 310000.00, 31000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(140, 'STK-IN-20260520-000016', 119, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 99300.00, 9930000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(141, 'STK-IN-20260520-000017', 120, 1, 2, NULL, 50.000000, NULL, 'piece', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5762.00, 288100.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(142, 'STK-IN-20260520-000018', 121, 1, 2, NULL, 50.000000, NULL, 'piece', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(143, 'STK-IN-20260520-000019', 122, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(144, 'STK-IN-20260520-000020', 123, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 27000.00, 2700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:49:39', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(145, 'STK-IN-20260520-000021', 124, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 40000.00, 4000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(146, 'STK-IN-20260520-000022', 125, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 20000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(147, 'STK-IN-20260520-000023', 126, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 10000.00, 1000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(148, 'STK-IN-20260520-000024', 127, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 23000.00, 2300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(149, 'STK-IN-20260520-000025', 128, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 428.00, 214000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(150, 'STK-IN-20260520-000026', 129, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(151, 'STK-IN-20260520-000027', 130, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(152, 'STK-IN-20260520-000028', 131, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 79000.00, 3950000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(153, 'STK-IN-20260520-000029', 132, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 204000.00, 10200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(154, 'STK-IN-20260520-000030', 133, 1, 2, NULL, 200.000000, NULL, 'litre', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 3000.00, 600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:51:22', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(155, 'STK-IN-20260520-000031', 134, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17500.00, 1750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(156, 'STK-IN-20260520-000032', 135, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 105000.00, 5250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(157, 'STK-IN-20260520-000033', 136, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1335.00, 667500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(158, 'STK-IN-20260520-000034', 137, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 2700.00, 540000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(159, 'STK-IN-20260520-000035', 138, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3500.00, 350000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(160, 'STK-IN-20260520-000036', 139, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 3000.00, 600000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(161, 'STK-IN-20260520-000037', 140, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(162, 'STK-IN-20260520-000038', 141, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 2000.00, 100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL);
INSERT INTO `stock_movements` (`id`, `movement_number`, `inventory_item_id`, `store_id`, `movement_type_id`, `department_id`, `quantity`, `pack_type`, `base_unit`, `pack_size`, `number_of_packs`, `unit_id`, `quantity_in_base_unit`, `stock_before`, `stock_after`, `unit_cost`, `total_value`, `reason`, `taken_by`, `returned_by`, `movement_date`, `approved_at`, `approved_by`, `purchase_order_id`, `goods_received_note_id`, `is_reversed`, `reversed_by_movement_id`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(163, 'STK-IN-20260520-000039', 142, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 1500.00, 75000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(164, 'STK-IN-20260520-000040', 143, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 1500.00, 150000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:54:13', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(165, 'STK-IN-20260520-000041', 144, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 1000.00, 50000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(166, 'STK-IN-20260520-000042', 145, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5000.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(167, 'STK-IN-20260520-000043', 146, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2000.00, 200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(168, 'STK-IN-20260520-000044', 147, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2000.00, 200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(169, 'STK-IN-20260520-000045', 148, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(170, 'STK-IN-20260520-000046', 149, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 5000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(171, 'STK-IN-20260520-000047', 150, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2500.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(172, 'STK-IN-20260520-000048', 151, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(173, 'STK-IN-20260520-000049', 152, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 2500.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(174, 'STK-IN-20260520-000050', 153, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 2876.00, 1438000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 13:57:12', 1, NULL, NULL, 0, NULL, '2026-05-19 21:00:00', '2026-05-19 21:00:00', NULL, 1, NULL),
(175, 'STK-IN-20260520-000051', 154, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 4000.00, 400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(176, 'STK-IN-20260520-000052', 155, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 2500.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(177, 'STK-IN-20260520-000053', 156, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 11.00, 5500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(178, 'STK-IN-20260520-000054', 157, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 831.00, 415500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(179, 'STK-IN-20260520-000055', 158, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 8000.00, 800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(180, 'STK-IN-20260520-000056', 159, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17000.00, 1700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(181, 'STK-IN-20260520-000057', 160, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1264.00, 632000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(182, 'STK-IN-20260520-000058', 161, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17000.00, 1700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(183, 'STK-IN-20260520-000059', 162, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(184, 'STK-IN-20260520-000060', 163, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 5435.00, 2717500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:03:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:03:16', '2026-05-20 14:03:16', NULL, 1, NULL),
(185, 'STK-IN-20260520-000061', 164, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 17000.00, 1700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(186, 'STK-IN-20260520-000062', 165, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 30000.00, 3000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(187, 'STK-IN-20260520-000063', 166, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 8000.00, 800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(188, 'STK-IN-20260520-000064', 167, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 12000.00, 1200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(189, 'STK-IN-20260520-000065', 168, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 8500.00, 850000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(190, 'STK-IN-20260520-000066', 169, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 500.00, 50000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(191, 'STK-IN-20260520-000067', 170, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 15000.00, 1500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(192, 'STK-IN-20260520-000068', 171, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 30000.00, 1500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:07', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:07', '2026-05-20 14:06:07', NULL, 1, NULL),
(193, 'STK-IN-20260520-000069', 172, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 35000.00, 1750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:08', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:08', '2026-05-20 14:06:08', NULL, 1, NULL),
(194, 'STK-IN-20260520-000070', 173, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:06:08', 1, NULL, NULL, 0, NULL, '2026-05-20 14:06:08', '2026-05-20 14:06:08', NULL, 1, NULL),
(195, 'STK-IN-20260520-000071', 174, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 2000.00, 100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(196, 'STK-IN-20260520-000072', 175, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 36343.00, 3634300.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(197, 'STK-IN-20260520-000073', 176, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(198, 'STK-IN-20260520-000074', 177, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 35000.00, 1750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(199, 'STK-IN-20260520-000075', 178, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(200, 'STK-IN-20260520-000076', 179, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 1000.00, 50000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(201, 'STK-IN-20260520-000077', 180, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 18000.00, 900000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(202, 'STK-IN-20260520-000078', 181, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(203, 'STK-IN-20260520-000079', 182, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(204, 'STK-IN-20260520-000080', 183, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:07:59', 1, NULL, NULL, 0, NULL, '2026-05-20 14:07:59', '2026-05-20 14:07:59', NULL, 1, NULL),
(205, 'STK-IN-20260520-000081', 184, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 33000.00, 1650000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(206, 'STK-IN-20260520-000082', 185, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(207, 'STK-IN-20260520-000083', 186, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(208, 'STK-IN-20260520-000084', 187, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 30000.00, 1500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(209, 'STK-IN-20260520-000085', 188, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(210, 'STK-IN-20260520-000086', 189, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 28000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(211, 'STK-IN-20260520-000087', 190, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 30000.00, 1500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(212, 'STK-IN-20260520-000088', 191, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 25000.00, 1250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(213, 'STK-IN-20260520-000089', 192, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 1831.00, 183100.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(214, 'STK-IN-20260520-000090', 193, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 4500.00, 900000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:09:54', 1, NULL, NULL, 0, NULL, '2026-05-20 14:09:54', '2026-05-20 14:09:54', NULL, 1, NULL),
(215, 'STK-IN-20260520-000091', 194, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 9000.00, 1800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(216, 'STK-IN-20260520-000092', 195, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 2127.00, 1063500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(217, 'STK-IN-20260520-000093', 196, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 2460.00, 1230000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(218, 'STK-IN-20260520-000094', 197, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 500.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(219, 'STK-IN-20260520-000095', 198, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(220, 'STK-IN-20260520-000096', 199, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 512.00, 256000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(221, 'STK-IN-20260520-000097', 200, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(222, 'STK-IN-20260520-000098', 201, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 288.00, 144000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(223, 'STK-IN-20260520-000099', 202, 1, 2, NULL, 200.000000, NULL, 'litre', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 7250.00, 1450000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(224, 'STK-IN-20260520-000100', 203, 1, 2, NULL, 500.000000, NULL, 'bottle', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 813.00, 406500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:12:53', 1, NULL, NULL, 0, NULL, '2026-05-20 14:12:53', '2026-05-20 14:12:53', NULL, 1, NULL),
(225, 'STK-IN-20260520-000101', 204, 1, 2, NULL, 500.000000, NULL, 'bottle', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 2400.00, 1200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(226, 'STK-IN-20260520-000102', 205, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 55000.00, 5500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(227, 'STK-IN-20260520-000103', 206, 1, 2, NULL, 500.000000, NULL, 'glass', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 9000.00, 4500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(228, 'STK-IN-20260520-000104', 207, 1, 2, NULL, 500.000000, NULL, 'glass', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 9015.00, 4507500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(229, 'STK-IN-20260520-000105', 208, 1, 2, NULL, 500.000000, NULL, 'glass', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 854.00, 427000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(230, 'STK-IN-20260520-000106', 209, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(231, 'STK-IN-20260520-000107', 210, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 10000.00, 1000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(232, 'STK-IN-20260520-000108', 211, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 7000.00, 700000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:39', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:39', '2026-05-20 14:16:39', NULL, 1, NULL),
(233, 'STK-IN-20260520-000109', 212, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 175000.00, 8750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:40', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:40', '2026-05-20 14:16:40', NULL, 1, NULL),
(234, 'STK-IN-20260520-000110', 213, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 30.00, 15000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:16:40', 1, NULL, NULL, 0, NULL, '2026-05-20 14:16:40', '2026-05-20 14:16:40', NULL, 1, NULL),
(235, 'STK-IN-20260520-000111', 214, 1, 2, NULL, 50.000000, NULL, 'litre', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 13000.00, 650000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(236, 'STK-IN-20260520-000112', 215, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 16.00, 8000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(237, 'STK-IN-20260520-000113', 216, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 3014.00, 1507000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(238, 'STK-IN-20260520-000114', 217, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 5000.00, 2500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(239, 'STK-IN-20260520-000115', 218, 1, 2, NULL, 100.000000, NULL, 'litre', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 18000.00, 1800000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(240, 'STK-IN-20260520-000116', 219, 1, 2, NULL, 10.000000, NULL, 'kg', NULL, NULL, NULL, 10.000000, 0.00, 10.00, 1000.00, 10000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(241, 'STK-IN-20260520-000117', 220, 1, 2, NULL, 50.000000, NULL, 'litre', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 26000.00, 1300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(242, 'STK-IN-20260520-000118', 221, 1, 2, NULL, 10.000000, NULL, 'litre', NULL, NULL, NULL, 10.000000, 0.00, 10.00, 5000.00, 50000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(243, 'STK-IN-20260520-000119', 222, 1, 2, NULL, 200.000000, NULL, 'kg', NULL, NULL, NULL, 200.000000, 0.00, 200.00, 210000.00, 42000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(244, 'STK-IN-20260520-000120', 223, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 105000.00, 5250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:19:16', 1, NULL, NULL, 0, NULL, '2026-05-20 14:19:16', '2026-05-20 14:19:16', NULL, 1, NULL),
(245, 'STK-IN-20260520-000121', 224, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 16500.00, 825000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(246, 'STK-IN-20260520-000122', 225, 1, 2, NULL, 20.000000, NULL, 'kg', NULL, NULL, NULL, 20.000000, 0.00, 20.00, 15000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(247, 'STK-IN-20260520-000123', 226, 1, 2, NULL, 20.000000, NULL, 'kg', NULL, NULL, NULL, 20.000000, 0.00, 20.00, 19000.00, 380000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(248, 'STK-IN-20260520-000124', 227, 1, 2, NULL, 10.000000, NULL, 'kg', NULL, NULL, NULL, 10.000000, 0.00, 10.00, 1000.00, 10000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(249, 'STK-IN-20260520-000125', 228, 1, 2, NULL, 100.000000, NULL, 'bundle', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 1000.00, 100000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(250, 'STK-IN-20260520-000126', 229, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 6600.00, 660000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(251, 'STK-IN-20260520-000127', 230, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 4801.00, 2400500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(252, 'STK-IN-20260520-000128', 231, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 12068.00, 6034000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(253, 'STK-IN-20260520-000129', 232, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1586.00, 793000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(254, 'STK-IN-20260520-000130', 233, 1, 2, NULL, 500.000000, NULL, 'portion', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 28902.00, 14451000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:21:05', 1, NULL, NULL, 0, NULL, '2026-05-20 14:21:05', '2026-05-20 14:21:05', NULL, 1, NULL),
(255, 'STK-IN-20260520-000131', 234, 1, 2, NULL, 50.000000, NULL, 'bunch', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 40000.00, 2000000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(256, 'STK-IN-20260520-000132', 235, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 300.00, 150000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(257, 'STK-IN-20260520-000133', 236, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 700.00, 350000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(258, 'STK-IN-20260520-000134', 237, 1, 2, NULL, 50.000000, NULL, 'heap', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5000.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(259, 'STK-IN-20260520-000135', 238, 1, 2, NULL, 50.000000, NULL, 'heap', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 5000.00, 250000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(260, 'STK-IN-20260520-000136', 239, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 8000.00, 400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(261, 'STK-IN-20260520-000137', 240, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 28000.00, 1400000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(262, 'STK-IN-20260520-000138', 241, 1, 2, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 4000.00, 200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(263, 'STK-IN-20260520-000139', 242, 1, 2, NULL, 50.000000, NULL, 'litre', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 15000.00, 750000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(264, 'STK-IN-20260520-000140', 243, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 4500.00, 450000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:22:43', 1, NULL, NULL, 0, NULL, '2026-05-20 14:22:43', '2026-05-20 14:22:43', NULL, 1, NULL),
(265, 'STK-IN-20260520-000141', 244, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3000.00, 300000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:23:44', 1, NULL, NULL, 0, NULL, '2026-05-20 14:23:44', '2026-05-20 14:23:44', NULL, 1, NULL),
(266, 'STK-IN-20260520-000142', 245, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 5000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:23:44', 1, NULL, NULL, 0, NULL, '2026-05-20 14:23:44', '2026-05-20 14:23:44', NULL, 1, NULL),
(267, 'STK-IN-20260520-000143', 246, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 3500.00, 350000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:23:44', 1, NULL, NULL, 0, NULL, '2026-05-20 14:23:44', '2026-05-20 14:23:44', NULL, 1, NULL),
(268, 'STK-IN-20260520-000144', 247, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 9000.00, 900000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 14:23:44', 1, NULL, NULL, 0, NULL, '2026-05-20 14:23:44', '2026-05-20 14:23:44', NULL, 1, NULL),
(269, 'ISS-20260520-8056', 24, 1, 5, NULL, 25.000000, NULL, 'bottle', NULL, NULL, NULL, 25.000000, 615.00, 590.00, 9999.99, 249999.75, 'Issued to RESTAURANT - Req: REST-REQ-20260520-2645', 'BARIGYE DAVIS', NULL, '2026-05-20', '2026-05-20 11:55:59', 2, NULL, NULL, 0, NULL, '2026-05-20 11:55:59', '2026-05-20 11:55:59', NULL, 2, NULL),
(270, 'STK-IN-20260520-000145', 248, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 1831.00, 183100.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(271, 'STK-IN-20260520-000146', 249, 1, 2, NULL, 100.000000, NULL, 'kg', NULL, NULL, NULL, 100.000000, 0.00, 100.00, 22000.00, 2200000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(272, 'STK-IN-20260520-000147', 250, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1264.00, 632000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(273, 'STK-IN-20260520-000148', 251, 1, 2, NULL, 10.000000, NULL, 'kg', NULL, NULL, NULL, 10.000000, 0.00, 10.00, 1000.00, 10000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(274, 'STK-IN-20260520-000149', 252, 1, 2, NULL, 50.000000, NULL, 'litre', NULL, NULL, NULL, 50.000000, 0.00, 50.00, 350.00, 17500.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(275, 'STK-IN-20260520-000150', 253, 1, 2, NULL, 500.000000, NULL, 'piece', NULL, NULL, NULL, 500.000000, 0.00, 500.00, 1000.00, 500000.00, 'Manual inventory entry from PDF', NULL, NULL, '2026-05-20', '2026-05-20 17:40:15', 1, NULL, NULL, 0, NULL, '2026-05-20 17:40:15', '2026-05-20 17:40:15', NULL, 1, NULL),
(276, 'ISS-20260521-2497', 24, 1, 5, NULL, 45.000000, NULL, 'bottle', NULL, NULL, NULL, 45.000000, 590.00, 545.00, 9999.99, 449999.55, 'Issued to RESTAURANT - Req: REST-REQ-20260521-3811', 'BARIGYE DAVIS', NULL, '2026-05-21', '2026-05-21 15:23:33', 2, NULL, NULL, 0, NULL, '2026-05-21 15:23:33', '2026-05-21 15:23:33', NULL, 2, NULL),
(277, 'ISS-20260521-2427', 38, 1, 5, NULL, 50.000000, NULL, 'bottle', NULL, NULL, NULL, 50.000000, 175.00, 125.00, 10000.00, 500000.00, 'Issued to RESTAURANT - Req: REST-REQ-20260521-3811', 'BARIGYE DAVIS', NULL, '2026-05-21', '2026-05-21 15:23:33', 2, NULL, NULL, 0, NULL, '2026-05-21 15:23:33', '2026-05-21 15:23:33', NULL, 2, NULL),
(278, 'ISS-20260521-7951', 99, 1, 5, NULL, 60.000000, NULL, 'portion', NULL, NULL, NULL, 60.000000, 500.00, 440.00, 831.00, 49860.00, 'Issued to KITCHEN - Req: KIT-REQ-20260521-8604', 'DANIEL', NULL, '2026-05-21', '2026-05-21 15:31:19', 2, NULL, NULL, 0, NULL, '2026-05-21 15:31:19', '2026-05-21 15:31:19', NULL, 2, NULL),
(279, 'ISS-20260521-0701', 212, 1, 5, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 50.00, 0.00, 175000.00, 8750000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260521-8604', 'DANIEL', NULL, '2026-05-21', '2026-05-21 15:31:19', 2, NULL, NULL, 0, NULL, '2026-05-21 15:31:19', '2026-05-21 15:31:19', NULL, 2, NULL),
(280, 'ISS-20260521-4446', 229, 1, 5, NULL, 50.020000, NULL, 'kg', NULL, NULL, NULL, 50.020000, 100.00, 49.98, 6600.00, 330132.00, 'Issued to KITCHEN - Req: KIT-REQ-20260521-8604', 'DANIEL', NULL, '2026-05-21', '2026-05-21 15:31:19', 2, NULL, NULL, 0, NULL, '2026-05-21 15:31:19', '2026-05-21 15:31:19', NULL, 2, NULL),
(281, 'ISS-20260522-8555', 95, 1, 5, NULL, 10.000000, NULL, 'portion', NULL, NULL, NULL, 10.000000, 500.00, 490.00, 2876.00, 28760.00, 'Issued to KITCHEN - Req: KIT-REQ-20260522-5887', 'James', NULL, '2026-05-22', '2026-05-22 05:43:54', 2, NULL, NULL, 0, NULL, '2026-05-22 05:43:54', '2026-05-22 05:43:54', NULL, 2, NULL),
(282, 'ISS-20260522-5492', 229, 1, 5, NULL, 25.000000, NULL, 'kg', NULL, NULL, NULL, 25.000000, 49.98, 24.98, 6600.00, 165000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260522-5887', 'James', NULL, '2026-05-22', '2026-05-22 05:43:54', 2, NULL, NULL, 0, NULL, '2026-05-22 05:43:54', '2026-05-22 05:43:54', NULL, 2, NULL),
(283, 'ISS-20260522-7489', 89, 1, 5, NULL, 50.000000, NULL, 'kg', NULL, NULL, NULL, 50.000000, 100.00, 50.00, 2000.00, 100000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260522-6953', 'James', NULL, '2026-05-22', '2026-05-22 12:41:11', 2, NULL, NULL, 0, NULL, '2026-05-22 12:41:11', '2026-05-22 12:41:11', NULL, 2, NULL),
(284, 'ISS-20260522-7162', 229, 1, 5, NULL, 10.000000, NULL, 'kg', NULL, NULL, NULL, 10.000000, 24.98, 14.98, 6600.00, 66000.00, 'Issued to KITCHEN - Req: KIT-REQ-20260522-6953', 'James', NULL, '2026-05-22', '2026-05-22 12:41:11', 2, NULL, NULL, 0, NULL, '2026-05-22 12:41:11', '2026-05-22 12:41:11', NULL, 2, NULL);

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
-- Table structure for table `stock_variance_reasons`
--

CREATE TABLE `stock_variance_reasons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=needs management approval, 0=auto-approved',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `table_reservations`
--

CREATE TABLE `table_reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_table_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `duration_hours` int(11) NOT NULL DEFAULT 2 COMMENT 'Duration of reservation in hours',
  `number_of_guests` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','seated','completed','cancelled','no_show') NOT NULL DEFAULT 'pending',
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `table_reservations`
--

INSERT INTO `table_reservations` (`id`, `restaurant_table_id`, `customer_name`, `customer_phone`, `customer_email`, `reservation_date`, `reservation_time`, `duration_hours`, `number_of_guests`, `notes`, `status`, `cancelled_by`, `cancelled_at`, `cancellation_reason`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'BARIGYE DAVIS', '0777143020', 'customer@gmail.com', '2026-05-27', '19:00:00', 1, 3, NULL, 'confirmed', NULL, NULL, NULL, 4, 4, '2026-05-26 05:56:22', '2026-05-26 06:10:24', NULL),
(2, 4, 'IVAN BANDA', '0777143020', 'banda@gmail.com', '2026-05-31', '19:00:00', 1, 5, NULL, 'pending', NULL, NULL, NULL, 4, 4, '2026-05-26 09:36:14', '2026-05-26 09:45:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `ticket_type` enum('kitchen','bar','cafe') NOT NULL,
  `sales_order_id` bigint(20) UNSIGNED NOT NULL,
  `table_number` varchar(50) DEFAULT NULL,
  `waiter_name` varchar(255) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `supplement` text DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `is_printed` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Not printed, 1 = Printed',
  `printed_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `ticket_type`, `sales_order_id`, `table_number`, `waiter_name`, `comments`, `supplement`, `items`, `is_printed`, `printed_at`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 'KOT202605270001', 'kitchen', 21, 'TB001', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":6,\\\"item_name\\\":\\\"Margherita Pizza\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":\\\"medium\\\"}]\"', 0, NULL, 15, '2026-05-27 10:03:37', '2026-05-27 10:03:37', NULL),
(4, 'KOT202605270002', 'kitchen', 22, 'TB002', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":2,\\\"item_name\\\":\\\"Grilled Chicken Breast\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":\\\"put some chill\\\"}]\"', 0, NULL, 15, '2026-05-27 11:00:52', '2026-05-27 11:00:52', NULL),
(5, 'KOT202605270003', 'kitchen', 23, 'TB006', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":11,\\\"item_name\\\":\\\"COCOTAIL BAR MENU\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":\\\"cold\\\"}]\"', 0, NULL, 15, '2026-05-27 11:10:06', '2026-05-27 11:10:06', NULL),
(6, 'BOT202605270001', 'bar', 24, 'TB003', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":14,\\\"item_name\\\":\\\"Mountain dew small\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":\\\"cold\\\"}]\"', 1, '2026-05-27 13:17:58', 15, '2026-05-27 12:01:28', '2026-05-27 13:17:58', NULL),
(7, 'COT202605270001', 'cafe', 25, 'TB004', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":4,\\\"item_name\\\":\\\"Fresh Orange Juice\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":\\\"spiced\\\"}]\"', 0, NULL, 15, '2026-05-27 12:17:28', '2026-05-27 12:17:28', NULL),
(8, 'BOT202605270002', 'bar', 26, 'TB005', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":16,\\\"item_name\\\":\\\"Four cusins\\\",\\\"quantity\\\":2,\\\"comments\\\":null,\\\"supplement\\\":\\\"cold\\\"}]\"', 0, NULL, 15, '2026-05-27 12:32:17', '2026-05-27 12:32:17', NULL),
(9, 'BOT202605270003', 'bar', 27, 'TB007', 'WAITER WAITERSS', NULL, NULL, '\"[{\\\"menu_item_id\\\":16,\\\"item_name\\\":\\\"Four cusins\\\",\\\"quantity\\\":1,\\\"comments\\\":null,\\\"supplement\\\":null}]\"', 0, NULL, 15, '2026-05-27 12:33:53', '2026-05-27 12:33:53', NULL);

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
(15, 'SET', 'Set', 'set', NULL, 0, NULL, NULL, 15, 1, '2026-05-11 05:17:40', '2026-05-11 05:17:40'),
(16, 'piece', 'piece', 'piece', NULL, 0, NULL, NULL, 0, 1, '2026-05-21 09:45:27', '2026-05-21 09:45:27'),
(17, '', 'Portion', 'Pt', NULL, 0, NULL, NULL, 0, 1, NULL, NULL),
(19, 'portion', 'portion', 'portion', NULL, 0, NULL, NULL, 0, 1, '2026-05-26 09:01:48', '2026-05-26 09:01:48');

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
(1, 'Barigye', 'Davis', 'barigyedavis6@gmail.com', NULL, '$2y$12$fwe8Dh49jYpyk8u.IzRYeO52v7mDATIHnbbUWs6.0bvaEj1otAwdq', 1, 1, 1, 1, '2026-05-27 14:15:13', NULL, '2026-04-14 17:37:47', '2026-05-27 14:15:13', NULL, 1, NULL, 1, NULL),
(2, 'BARIGYE', 'DAVIS', 'davisbarigye04@gmail.com', NULL, '$2y$12$Aie5KoiojnqluKnTg/vp3.Tdyf5TZYJVZEjHCp5KX7AbrXdZjJtvS', 16, 0, 0, 1, '2026-05-27 14:31:58', NULL, '2026-04-14 19:42:34', '2026-05-27 14:31:58', 1, 1, NULL, 16, 3),
(3, 'Kasibante', 'Julius', 'julius@gmail.com', NULL, '$2y$12$N60AXXenZWa4kDaRzaxSwud2ijFg.Q2M0jc965Bev3KdozC6mJsIq', 6, 0, 0, 1, '2026-05-27 09:12:37', NULL, '2026-04-14 20:44:17', '2026-05-27 09:12:37', 1, 1, NULL, 6, 2),
(4, 'GENERAL', 'MANAGER', 'generalmanager@gmail.com', NULL, '$2y$12$59HDzVl1ntwJ4mSQw.2ts.gX6JaWOAxoSSYUvHFeqNvXwakZYbLyy', 3, 0, 1, 1, '2026-05-27 12:10:02', NULL, '2026-04-28 08:16:42', '2026-05-27 12:10:02', 1, 1, NULL, 3, 6),
(5, 'INNOCENT', 'MANAGER', 'innocentmanager@gmail.com', NULL, '$2y$12$vCIspZc7PzgloQ4V8ezeT.zheVO83i813UcqFRPAoui/c.A8bdP9u', 3, 0, 0, 1, '2026-05-27 03:41:36', NULL, '2026-04-28 10:22:24', '2026-05-27 03:41:36', 1, NULL, NULL, 3, 6),
(6, 'SAMPLE', 'DATA', 'barigye@gmail.com', NULL, '$2y$12$3FaIgEZYg3QnhswkEMlzcex84a4sExr3QDJUmDrANp9T9SNheScI.', 6, 0, 0, 1, '2026-04-28 10:31:12', NULL, '2026-04-28 10:30:58', '2026-04-28 10:31:12', 1, NULL, NULL, 6, 2),
(7, 'MANAGING', 'DIRECTOR', 'managingdirector@gmail.com', NULL, '$2y$12$4JhvUp5IsMMgcbUN80t1muWiLolVH6y9DbwJbL6eZD72xiW7ugMe.', 13, 0, 0, 1, '2026-05-19 10:19:21', NULL, '2026-04-29 11:10:16', '2026-05-19 10:19:21', 1, NULL, NULL, 13, 7),
(8, 'kitchen', 'Manager', 'kitchen@gmail.com', NULL, '$2y$12$MY3R2I9VHGFiDymVvI8/YeScrKZFHZl03XUF/z7e8QEouQRHxRmz.', 4, 0, 0, 1, '2026-05-27 13:25:53', NULL, '2026-05-11 06:47:09', '2026-05-27 13:25:53', 1, 1, NULL, 4, 8),
(9, 'RESTURANT', 'MANAGER', 'resturant@gmail.com', NULL, '$2y$12$XIj54QTC5pmv2ZkrkC1bO.pvl0M627h1H5H2XzeKSIuUEP/lH73J6', 11, 0, 0, 1, '2026-05-27 13:37:43', NULL, '2026-05-13 12:17:09', '2026-05-27 13:37:43', 1, 1, NULL, 11, 9),
(10, 'CASHIER', 'ATTENDENTANT', 'cashier@gmail.com', NULL, '$2y$12$wdcBCfESL5GNAs5f4JSTHOjUJN5Ad9rZ4LiVsWZJUG0I5ijvolNFK', 8, 0, 0, 1, '2026-05-27 14:45:46', NULL, '2026-05-14 04:10:24', '2026-05-27 14:45:46', 1, 1, NULL, 8, NULL),
(11, 'BARIGYE', 'DAVIS', 'wycliffemwalye83@gmail.com', NULL, '$2y$12$LK6g1DXFGkWWusJm2AikV.grjAioyidwLMVKfjpG7RgMwYIF72fkq', NULL, 0, 0, 1, NULL, NULL, '2026-05-16 06:33:04', '2026-05-16 06:33:04', 1, NULL, NULL, 5, 8),
(12, 'BARIGYE', 'DAVIS', 'barmanager@gmail.com', NULL, '$2y$12$vi8Cc7.yLzYh9OFgH8FQN.aGrelypGkeRMWemp4yDyHVED.dkYjuS', 14, 0, 0, 1, '2026-05-27 12:32:43', NULL, '2026-05-16 06:34:03', '2026-05-27 12:32:43', 1, 1, NULL, 14, 4),
(13, 'CASHIER', 'BAR', 'barcashier@gmail.com', NULL, '$2y$12$VcwuhNMPYm8GFFh9z/0GAuqTsoHzHel8mATmRXb6DV.S3Mm5.YhSa', 8, 0, 0, 1, '2026-05-18 09:33:30', NULL, '2026-05-18 02:31:34', '2026-05-27 14:19:53', 1, 1, NULL, 8, 4),
(14, 'NAMARA', 'JUNIOR', 'junior@gmail.com', NULL, '$2y$12$OQtzmoJx1ljcftG/XSQlHeXd54n0TlCIdpHTJih8/WXyg6ZZ3Cw3W', 8, 0, 0, 1, NULL, NULL, '2026-05-25 14:59:20', '2026-05-25 14:59:20', 1, NULL, NULL, 8, 2),
(15, 'WAITER', 'WAITERSS', 'waiter@gmail.com', NULL, '$2y$12$kvgopCnuFlVOVvrRWQNVh.WZ0SyWf1AOBdFOGF2khTNwlGDHV65ey', 9, 0, 0, 1, '2026-05-27 14:33:29', NULL, '2026-05-26 14:02:01', '2026-05-27 14:33:29', 1, 1, NULL, 9, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`, `is_allowed`, `created_at`, `updated_at`) VALUES
(1, 9, 43, 1, NULL, '2026-05-27 03:43:46'),
(2, 9, 44, 1, NULL, '2026-05-27 03:43:46'),
(3, 9, 39, 1, NULL, '2026-05-27 03:43:46'),
(4, 9, 40, 1, NULL, '2026-05-27 03:43:46'),
(5, 1, 47, 1, NULL, NULL),
(6, 1, 48, 1, NULL, NULL),
(7, 1, 29, 1, NULL, NULL),
(8, 1, 30, 1, NULL, NULL),
(9, 1, 10, 1, NULL, NULL),
(10, 1, 11, 1, NULL, NULL),
(11, 1, 12, 1, NULL, NULL),
(12, 14, 43, 1, NULL, NULL),
(13, 14, 44, 1, NULL, NULL),
(14, 14, 39, 1, NULL, NULL),
(15, 14, 40, 1, NULL, NULL),
(16, 14, 41, 1, NULL, NULL),
(19, 2, 49, 1, '2026-05-26 03:48:17', '2026-05-27 08:29:06'),
(20, 4, 49, 1, '2026-05-26 03:58:43', '2026-05-27 06:36:14'),
(21, 4, 64, 1, '2026-05-26 03:58:43', '2026-05-27 06:36:14'),
(22, 4, 72, 1, '2026-05-26 04:00:35', '2026-05-27 06:36:14'),
(23, 4, 73, 1, '2026-05-26 04:00:35', '2026-05-27 06:36:14'),
(24, 4, 74, 1, '2026-05-26 04:00:35', '2026-05-27 06:36:14'),
(25, 4, 75, 1, '2026-05-26 04:02:03', '2026-05-27 06:36:14'),
(26, 4, 135, 1, '2026-05-26 05:17:38', '2026-05-27 06:36:14'),
(27, 4, 136, 1, '2026-05-26 05:20:51', '2026-05-27 06:36:14'),
(28, 4, 137, 1, '2026-05-26 05:33:14', '2026-05-27 06:36:14'),
(29, 4, 139, 1, '2026-05-26 05:53:05', '2026-05-27 06:36:14'),
(30, 4, 140, 1, '2026-05-26 05:54:07', '2026-05-27 06:36:14'),
(31, 4, 141, 1, '2026-05-26 06:09:45', '2026-05-27 06:36:14'),
(32, 2, 14, 1, '2026-05-26 06:53:06', '2026-05-27 08:29:06'),
(33, 2, 16, 1, '2026-05-26 06:53:06', '2026-05-27 08:29:06'),
(34, 4, 77, 1, '2026-05-26 09:14:04', '2026-05-27 06:36:14'),
(35, 15, 143, 1, '2026-05-26 14:12:18', '2026-05-27 11:04:05'),
(36, 15, 144, 1, '2026-05-26 14:12:18', '2026-05-27 11:04:05'),
(37, 15, 145, 1, '2026-05-27 05:55:53', '2026-05-27 11:04:05'),
(38, 15, 148, 1, '2026-05-27 05:55:53', '2026-05-27 11:04:05'),
(39, 4, 69, 1, '2026-05-27 06:17:50', '2026-05-27 06:36:14'),
(40, 4, 70, 1, '2026-05-27 06:17:50', '2026-05-27 06:36:14'),
(41, 4, 148, 1, '2026-05-27 06:17:50', '2026-05-27 06:36:14'),
(42, 4, 71, 1, '2026-05-27 06:20:58', '2026-05-27 06:36:14'),
(43, 4, 79, 1, '2026-05-27 06:36:14', '2026-05-27 06:36:14'),
(44, 4, 80, 1, '2026-05-27 06:36:14', '2026-05-27 06:36:14'),
(45, 2, 33, 1, '2026-05-27 06:51:46', '2026-05-27 08:29:06'),
(46, 2, 34, 1, '2026-05-27 06:51:46', '2026-05-27 08:29:06'),
(47, 3, 14, 1, '2026-05-27 07:09:20', '2026-05-27 07:09:20'),
(48, 3, 16, 1, '2026-05-27 07:09:20', '2026-05-27 07:09:20'),
(49, 3, 81, 1, '2026-05-27 07:09:20', '2026-05-27 07:09:20'),
(50, 3, 82, 1, '2026-05-27 07:09:20', '2026-05-27 07:09:20'),
(51, 2, 17, 1, '2026-05-27 08:29:06', '2026-05-27 08:29:06'),
(52, 15, 146, 1, '2026-05-27 10:36:58', '2026-05-27 11:04:05'),
(53, 15, 147, 1, '2026-05-27 11:04:05', '2026-05-27 11:04:05'),
(54, 12, 92, 1, '2026-05-27 11:42:04', '2026-05-27 11:42:04'),
(55, 10, 152, 1, '2026-05-27 14:34:34', '2026-05-27 15:09:19'),
(56, 10, 146, 1, '2026-05-27 15:09:19', '2026-05-27 15:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`) VALUES
(3, 13, 8, NULL, '2026-05-25 17:19:35', NULL, NULL),
(4, 9, 11, NULL, '2026-05-25 17:46:58', NULL, NULL),
(7, 1, 1, NULL, '2026-05-25 17:48:35', NULL, NULL),
(8, 14, 8, NULL, '2026-05-25 17:59:20', NULL, NULL),
(9, 2, 16, NULL, '2026-05-26 05:59:32', '2026-05-26 02:59:32', '2026-05-26 02:59:32'),
(10, 2, 7, NULL, '2026-05-26 05:59:32', '2026-05-26 02:59:32', '2026-05-26 02:59:32'),
(11, 4, 3, NULL, '2026-05-26 06:58:43', '2026-05-26 03:58:43', '2026-05-26 03:58:43'),
(12, 15, 9, NULL, '2026-05-26 17:02:01', '2026-05-26 14:02:01', '2026-05-26 14:02:01'),
(13, 3, 6, NULL, '2026-05-27 10:08:22', '2026-05-27 07:08:22', '2026-05-27 07:08:22'),
(14, 12, 14, NULL, '2026-05-27 14:42:04', '2026-05-27 11:42:04', '2026-05-27 11:42:04'),
(15, 10, 8, NULL, '2026-05-27 17:16:27', '2026-05-27 14:16:27', '2026-05-27 14:16:27');

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
  `average_rating` decimal(2,1) DEFAULT 0.0 COMMENT 'Average rating from 1 to 5',
  `total_ratings` int(11) NOT NULL DEFAULT 0 COMMENT 'Total number of ratings received',
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

INSERT INTO `vendors` (`id`, `vendor_code`, `name`, `contact_person`, `email`, `phone`, `alternative_phone`, `address`, `city`, `country`, `tax_id`, `payment_method`, `credit_limit`, `status`, `average_rating`, `total_ratings`, `notes`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'VEND-001', 'GREEN FARM LTD', 'Barigye Davis', 'barigyedavis6@gmail.com', '0777142030', '+256777142031', 'kasanga -Gabga Road', 'kampala', 'Uganda', '123445544265', 'cash', 5000000, 'active', 5.0, 4, 'will be supplying us with  vegetables forexample, tomatoes, cabbagges,apples', '2026-04-16 13:37:12', '2026-05-22 11:48:53', NULL, 3, 3),
(2, 'R001', 'Rwenzori', 'Rwenzori Companies', 'rwenzori2@gmail.com', '0777143020', NULL, NULL, 'Kampala', 'Uganda', NULL, 'cash', 0, 'active', 0.0, 0, NULL, '2026-05-06 23:35:23', '2026-05-06 23:35:23', NULL, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ratings`
--

CREATE TABLE `vendor_ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `goods_received_note_id` bigint(20) UNSIGNED NOT NULL COMMENT 'GRN being rated',
  `rating` tinyint(1) NOT NULL COMMENT '1 to 5 stars (1=worst, 5=best)',
  `comment` text DEFAULT NULL COMMENT 'Optional feedback comment',
  `rated_by` bigint(20) UNSIGNED NOT NULL COMMENT 'User who gave rating',
  `rated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_ratings`
--

INSERT INTO `vendor_ratings` (`id`, `vendor_id`, `goods_received_note_id`, `rating`, `comment`, `rated_by`, `rated_at`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 5, 'He delivered in Time', 3, '2026-05-22 11:18:23', '2026-05-22 11:18:23', '2026-05-22 11:18:23'),
(2, 1, 7, 5, 'All items were in good conditions', 3, '2026-05-22 11:20:02', '2026-05-22 11:20:02', '2026-05-22 11:20:02'),
(3, 1, 3, 5, 'Good qaulity products', 3, '2026-05-22 11:46:10', '2026-05-22 11:46:10', '2026-05-22 11:46:10'),
(4, 1, 12, 5, 'Good timing', 3, '2026-05-22 11:48:53', '2026-05-22 11:48:53', '2026-05-22 11:48:53');

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
-- Indexes for table `cost_price_history`
--
ALTER TABLE `cost_price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_item_id` (`inventory_item_id`),
  ADD KEY `changed_by` (`changed_by`);

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
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_department_id_index` (`department_id`),
  ADD KEY `menus_is_active_index` (`is_active`),
  ADD KEY `menus_created_by_foreign` (`created_by`),
  ADD KEY `menus_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_inventory_item_id_foreign` (`inventory_item_id`),
  ADD KEY `menu_items_created_by_foreign` (`created_by`),
  ADD KEY `menu_items_updated_by_foreign` (`updated_by`),
  ADD KEY `menu_items_category_index` (`category`),
  ADD KEY `menu_items_is_active_index` (`is_active`),
  ADD KEY `menu_items_menu_id_index` (`menu_id`),
  ADD KEY `menu_items_menu_item_category_id_index` (`menu_item_category_id`);

--
-- Indexes for table `menu_item_categories`
--
ALTER TABLE `menu_item_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_item_categories_code_unique` (`code`);

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
-- Indexes for table `recipe_items`
--
ALTER TABLE `recipe_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_items_menu_item_id_index` (`menu_item_id`),
  ADD KEY `recipe_items_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `recipe_items_unit_of_measure_id_index` (`unit_of_measure_id`);

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
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurant_tables_table_number_unique` (`table_number`),
  ADD KEY `restaurant_tables_capacity_index` (`capacity`),
  ADD KEY `restaurant_tables_is_reserved_index` (`is_reserved`),
  ADD KEY `restaurant_tables_is_active_index` (`is_active`),
  ADD KEY `restaurant_tables_location_index` (`location`),
  ADD KEY `restaurant_tables_created_by_foreign` (`created_by`),
  ADD KEY `restaurant_tables_updated_by_foreign` (`updated_by`);

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
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_orders_order_number_unique` (`order_number`),
  ADD KEY `sales_orders_cashier_id_foreign` (`cashier_id`),
  ADD KEY `sales_orders_created_by_foreign` (`created_by`),
  ADD KEY `sales_orders_updated_by_foreign` (`updated_by`),
  ADD KEY `sales_orders_order_number_index` (`order_number`),
  ADD KEY `sales_orders_status_index` (`status`),
  ADD KEY `sales_orders_created_at_index` (`created_at`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `waiter_id` (`waiter_id`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_order_items_sales_order_id_index` (`sales_order_id`),
  ADD KEY `sales_order_items_menu_item_id_index` (`menu_item_id`),
  ADD KEY `sales_order_items_inventory_item_id_foreign` (`inventory_item_id`);

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
-- Indexes for table `stock_counts`
--
ALTER TABLE `stock_counts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_counts_count_number_unique` (`count_number`),
  ADD KEY `stock_counts_location_type_location_id_index` (`location_type`,`location_id`),
  ADD KEY `stock_counts_status_index` (`status`),
  ADD KEY `stock_counts_created_by_foreign` (`created_by`),
  ADD KEY `stock_counts_completed_by_foreign` (`completed_by`);

--
-- Indexes for table `stock_count_items`
--
ALTER TABLE `stock_count_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_count_items_stock_count_id_index` (`stock_count_id`),
  ADD KEY `stock_count_items_inventory_item_id_index` (`inventory_item_id`),
  ADD KEY `stock_count_items_approved_by_foreign` (`approved_by`),
  ADD KEY `stock_count_items_adjustment_movement_id_foreign` (`adjustment_movement_id`);

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
-- Indexes for table `stock_variance_reasons`
--
ALTER TABLE `stock_variance_reasons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_variance_reasons_code_unique` (`code`);

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
-- Indexes for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_reservations_restaurant_table_id_foreign` (`restaurant_table_id`),
  ADD KEY `table_reservations_reservation_date_index` (`reservation_date`),
  ADD KEY `table_reservations_status_index` (`status`),
  ADD KEY `table_reservations_customer_name_index` (`customer_name`),
  ADD KEY `table_reservations_customer_phone_index` (`customer_phone`),
  ADD KEY `table_reservations_created_by_foreign` (`created_by`),
  ADD KEY `table_reservations_updated_by_foreign` (`updated_by`),
  ADD KEY `table_reservations_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tickets_sales_order_id_foreign` (`sales_order_id`);

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
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

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
-- Indexes for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_ratings_vendor_id_index` (`vendor_id`),
  ADD KEY `vendor_ratings_goods_received_note_id_index` (`goods_received_note_id`),
  ADD KEY `vendor_ratings_rated_by_index` (`rated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `cost_price_history`
--
ALTER TABLE `cost_price_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `department_requisitions`
--
ALTER TABLE `department_requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `department_requisition_items`
--
ALTER TABLE `department_requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lpo_items`
--
ALTER TABLE `lpo_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `menu_item_categories`
--
ALTER TABLE `menu_item_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `recipe_items`
--
ALTER TABLE `recipe_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `stock_balances`
--
ALTER TABLE `stock_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_counts`
--
ALTER TABLE `stock_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_count_items`
--
ALTER TABLE `stock_count_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT for table `stock_movement_types`
--
ALTER TABLE `stock_movement_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stock_variance_reasons`
--
ALTER TABLE `stock_variance_reasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cost_price_history`
--
ALTER TABLE `cost_price_history`
  ADD CONSTRAINT `cost_price_history_ibfk_1` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cost_price_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menus_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menus_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_menu_item_category_id_foreign` FOREIGN KEY (`menu_item_category_id`) REFERENCES `menu_item_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `recipe_items`
--
ALTER TABLE `recipe_items`
  ADD CONSTRAINT `recipe_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `recipe_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipe_items_unit_of_measure_id_foreign` FOREIGN KEY (`unit_of_measure_id`) REFERENCES `units_of_measure` (`id`);

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
-- Constraints for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD CONSTRAINT `restaurant_tables_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `restaurant_tables_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`id`),
  ADD CONSTRAINT `sales_orders_ibfk_3` FOREIGN KEY (`waiter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_orders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  ADD CONSTRAINT `sales_order_items_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD CONSTRAINT `stock_balances_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `stock_balances_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`);

--
-- Constraints for table `stock_counts`
--
ALTER TABLE `stock_counts`
  ADD CONSTRAINT `stock_counts_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_counts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `stock_count_items`
--
ALTER TABLE `stock_count_items`
  ADD CONSTRAINT `stock_count_items_adjustment_movement_id_foreign` FOREIGN KEY (`adjustment_movement_id`) REFERENCES `stock_movements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_count_items_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_count_items_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `stock_count_items_stock_count_id_foreign` FOREIGN KEY (`stock_count_id`) REFERENCES `stock_counts` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD CONSTRAINT `table_reservations_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `table_reservations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `table_reservations_restaurant_table_id_foreign` FOREIGN KEY (`restaurant_table_id`) REFERENCES `restaurant_tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `table_reservations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendors_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  ADD CONSTRAINT `vendor_ratings_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_ratings_rated_by_foreign` FOREIGN KEY (`rated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_ratings_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
