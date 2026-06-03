-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 08:39 PM
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
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `goods_received_note_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `initial_quantity` decimal(15,6) NOT NULL,
  `remaining_quantity` decimal(15,6) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL,
  `pack_cost` decimal(15,2) DEFAULT NULL COMMENT 'Cost per single pack (e.g., cost per crate)',
  `total_cost` decimal(15,2) NOT NULL,
  `unit_of_measurement` varchar(50) NOT NULL DEFAULT 'piece' COMMENT 'Unit: piece, bottle, kg, litre, gram, portion, plate, glass, box, carton, crate, dozen, pack, sack, set, roll, strip, sachet, can, bundle, heap, bunch, meter',
  `pack_type` varchar(50) DEFAULT NULL COMMENT 'How received: Carton, Crate, Box, Dozen, Pack',
  `pack_size` int(11) DEFAULT NULL COMMENT 'Number of base units per pack (e.g., 24 bottles per crate)',
  `number_of_packs` int(11) DEFAULT NULL COMMENT 'Number of packs received',
  `total_quantity` decimal(15,6) DEFAULT NULL COMMENT 'Total base units = pack_size × number_of_packs',
  `manufacture_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_status` varchar(255) NOT NULL DEFAULT 'active',
  `supplier_batch_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_settings`
--

CREATE TABLE `business_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `department_requisition_items`
--

CREATE TABLE `department_requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_requisition_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_issuances` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_issuances`)),
  `batch_returns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`batch_returns`)),
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

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grn_id` bigint(20) UNSIGNED DEFAULT NULL,
  `po_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_type` varchar(255) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `vat_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'VAT rate applied',
  `vat_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'VAT amount calculated',
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `received_by` varchar(255) DEFAULT NULL COMMENT 'Person who physically received the goods',
  `received_by_user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User ID who recorded the receipt',
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'User ID who verified the goods',
  `verified_at` timestamp NULL DEFAULT NULL,
  `delivered_by_name` varchar(255) DEFAULT NULL COMMENT 'Delivery person name',
  `delivered_by_phone` varchar(50) DEFAULT NULL COMMENT 'Delivery person phone',
  `delivered_by_email` varchar(255) DEFAULT NULL COMMENT 'Delivery person email',
  `status` enum('draft','completed','cancelled','inventory_updated','verified') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `unit_of_measurement` varchar(50) NOT NULL DEFAULT 'piece' COMMENT 'Unit: piece, bottle, kg, litre, gram, portion, plate, glass, box, carton, crate, dozen, pack, sack, set, roll, strip, sachet, can, bundle, heap, bunch, meter',
  `empty_bottle_weight` decimal(15,6) DEFAULT 0.000000 COMMENT 'Weight of empty bottle/container in kg',
  `minimum_stock` decimal(15,6) DEFAULT 0.000000,
  `maximum_stock` decimal(15,6) DEFAULT 0.000000,
  `reorder_quantity` decimal(15,6) DEFAULT 0.000000,
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
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `type` enum('normal','emergency') NOT NULL DEFAULT 'normal',
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `lpo_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_instructions` varchar(255) DEFAULT NULL,
  `payment_method` enum('cash','credit','bank_transfer','mobile_money','cheque') DEFAULT 'cash',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_rate` decimal(5,2) DEFAULT 0.00,
  `vat_amount` decimal(15,2) DEFAULT 0.00,
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

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `type` enum('normal','emergency') NOT NULL DEFAULT 'normal',
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `lpo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `delivery_terms` varchar(255) DEFAULT NULL,
  `payment_method` enum('cash','credit','bank_transfer','mobile_money','cheque') DEFAULT 'cash',
  `store_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ordered_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `po_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `vat_rate` decimal(5,2) DEFAULT 0.00,
  `vat_amount` decimal(15,2) DEFAULT 0.00,
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

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

CREATE TABLE `requisitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_number` varchar(50) NOT NULL,
  `requisition_type` enum('normal','emergency') NOT NULL DEFAULT 'normal',
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

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inventory_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity_requested` decimal(15,2) NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `batch_stock_at_request` decimal(15,6) DEFAULT NULL,
  `total_stock_at_request` decimal(15,6) DEFAULT NULL,
  `metrics` varchar(255) DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `quantity_approved` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `movement_number` varchar(100) NOT NULL,
  `inventory_item_id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
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
  `signature_path` varchar(255) DEFAULT NULL,
  `signature_updated_at` timestamp NULL DEFAULT NULL,
  `signature_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `batches_batch_number_unique` (`batch_number`),
  ADD KEY `batches_supplier_id_foreign` (`supplier_id`),
  ADD KEY `batches_inventory_item_id_batch_status_index` (`inventory_item_id`,`batch_status`),
  ADD KEY `batches_expiry_date_index` (`expiry_date`),
  ADD KEY `batches_remaining_quantity_index` (`remaining_quantity`),
  ADD KEY `batches_goods_received_note_id_foreign` (`goods_received_note_id`);

--
-- Indexes for table `business_settings`
--
ALTER TABLE `business_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_settings_key_unique` (`key`);

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
  ADD KEY `inventory_item_id` (`inventory_item_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `department_types`
--
ALTER TABLE `department_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_types_code_unique` (`code`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `documents_grn_id_foreign` (`grn_id`),
  ADD KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `documents_po_id_foreign` (`po_id`);

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
  ADD KEY `requisition_items_inventory_item_id_foreign` (`inventory_item_id`),
  ADD KEY `requisition_items_batch_id_index` (`batch_id`);

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
  ADD KEY `stock_movements_is_reversed_index` (`is_reversed`),
  ADD KEY `batch_id` (`batch_id`);

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
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_settings`
--
ALTER TABLE `business_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_price_history`
--
ALTER TABLE `cost_price_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_requisitions`
--
ALTER TABLE `department_requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_requisition_items`
--
ALTER TABLE `department_requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_types`
--
ALTER TABLE `department_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_items`
--
ALTER TABLE `goods_received_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lpo_items`
--
ALTER TABLE `lpo_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_item_categories`
--
ALTER TABLE `menu_item_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe_items`
--
ALTER TABLE `recipe_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_balances`
--
ALTER TABLE `stock_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_counts`
--
ALTER TABLE `stock_counts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_count_items`
--
ALTER TABLE `stock_count_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movement_types`
--
ALTER TABLE `stock_movement_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_variance_reasons`
--
ALTER TABLE `stock_variance_reasons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `store_types`
--
ALTER TABLE `store_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units_of_measure`
--
ALTER TABLE `units_of_measure`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_ratings`
--
ALTER TABLE `vendor_ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `batches_inventory_item_id_foreign` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `batches_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `department_requisition_items_ibfk_2` FOREIGN KEY (`inventory_item_id`) REFERENCES `inventory_items` (`id`),
  ADD CONSTRAINT `department_requisition_items_ibfk_3` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_grn_id_foreign` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

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
  ADD CONSTRAINT `requisition_items_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
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
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`),
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
