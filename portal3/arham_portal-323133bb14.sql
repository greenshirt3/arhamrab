-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: sdb-w.hosting.stackcp.net
-- Generation Time: Feb 11, 2026 at 07:37 PM
-- Server version: 10.6.18-MariaDB-log
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arham_portal-323133bb14`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_type` enum('cash','bank','mobile_wallet','device') DEFAULT 'cash',
  `current_balance` decimal(15,2) DEFAULT 0.00,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `account_name`, `account_type`, `current_balance`, `last_updated`) VALUES
(1, 'Shop Cash Drawer', 'cash', 50000.00, '2026-02-10 08:06:04'),
(2, 'HBL Konnect BVS', 'device', 33461.17, '2026-02-10 07:53:10'),
(3, 'Personal Wallet', 'cash', 0.00, '2026-02-09 15:52:56'),
(4, 'Bank Account 1', 'bank', 0.00, '2026-02-09 15:52:56');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `cnic` varchar(15) NOT NULL,
  `name` varchar(100) DEFAULT 'Unknown',
  `phone` varchar(20) DEFAULT NULL,
  `district` varchar(50) DEFAULT 'Gujrat',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_visit` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `cnic`, `name`, `phone`, `district`, `created_at`, `last_visit`) VALUES
(1, '3420189762733', 'Saif Ullah', '', 'Gujrat', '2026-02-03 04:06:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bill_queue`
--

CREATE TABLE `bill_queue` (
  `id` int(11) NOT NULL,
  `bill_type` varchar(100) DEFAULT NULL,
  `consumer_number` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `consumer_name` varchar(100) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL COMMENT 'Link to loans table',
  `mobile_no` varchar(20) DEFAULT NULL,
  `payment_status` enum('cash','credit') DEFAULT 'cash',
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'Proof of payment TID'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bill_queue`
--

INSERT INTO `bill_queue` (`id`, `bill_type`, `consumer_number`, `amount`, `status`, `created_at`, `consumer_name`, `customer_id`, `mobile_no`, `payment_status`, `transaction_id`) VALUES
(1, 'Electricity', '13123410556203', 893.00, 'paid', '2026-02-09 11:05:31', 'a', NULL, NULL, 'cash', NULL),
(2, 'Electricity', '13123410556201', 1136.00, 'paid', '2026-02-09 11:06:31', 'b', NULL, NULL, 'cash', NULL),
(3, 'Electricity', '14123410605201', 933.00, 'paid', '2026-02-09 11:06:57', 'c', NULL, NULL, 'cash', NULL),
(4, 'Electricity', '14123410583700', 2986.00, 'paid', '2026-02-09 11:07:49', 'd', NULL, NULL, 'cash', NULL),
(5, 'Electricity', '14123410593201', 2437.00, 'paid', '2026-02-09 11:08:56', 'e', NULL, NULL, 'cash', NULL),
(6, 'Electricity', '14123410596700', 1986.00, 'paid', '2026-02-09 11:09:42', 'f', NULL, NULL, 'cash', NULL),
(7, 'Electricity', '14123410571801', 609.00, 'paid', '2026-02-09 11:10:23', 'G', NULL, NULL, 'cash', NULL),
(8, 'Electricity', '14123410571800', 1815.00, 'paid', '2026-02-09 11:11:00', 'H', NULL, NULL, 'cash', NULL),
(9, 'Electricity', '14123410599701', 663.00, 'paid', '2026-02-09 11:11:43', 'I', NULL, NULL, 'cash', NULL),
(10, 'Electricity', '15123410618502', 676.00, 'paid', '2026-02-09 11:12:56', 'J', NULL, NULL, 'cash', NULL),
(11, 'Electricity', '14123410604000', 1333.00, 'paid', '2026-02-09 11:15:16', 'K', NULL, NULL, 'cash', NULL),
(12, 'Electricity', '14123410602608', 183.00, 'paid', '2026-02-09 11:15:56', 'L', NULL, NULL, 'cash', NULL),
(13, 'Electricity', '14123410572604', 15371.00, 'paid', '2026-02-09 11:16:29', 'M', NULL, NULL, 'cash', NULL),
(14, 'Electricity', '14123410572503', 2845.00, 'paid', '2026-02-09 11:17:04', 'N', NULL, NULL, 'cash', NULL),
(15, 'Electricity', '14123410572200', 854.00, 'paid', '2026-02-09 11:17:28', 'O', NULL, NULL, 'cash', NULL),
(16, 'Electricity', '14123410592208', 676.00, 'paid', '2026-02-09 11:17:56', 'P', NULL, NULL, 'cash', NULL),
(17, 'Electricity', '14123410601500', 1650.00, 'paid', '2026-02-09 11:18:19', 'Q', NULL, NULL, 'cash', NULL),
(18, 'Electricity', '14123410590300', 2195.00, 'paid', '2026-02-09 11:18:44', 'R', NULL, NULL, 'cash', NULL),
(19, 'Electricity', '14123410596701', 1535.00, 'paid', '2026-02-09 11:19:20', 'S', NULL, NULL, 'cash', NULL),
(20, 'Electricity', '14123410572603', 1133.00, 'paid', '2026-02-09 11:20:05', 'T', NULL, NULL, 'cash', NULL),
(21, 'Electricity', '14123410571300', 831.00, 'paid', '2026-02-09 11:20:39', 'U', NULL, NULL, 'cash', NULL),
(23, 'Electricity', '14123410591700', 1015.00, 'paid', '2026-02-09 11:21:57', 'W', NULL, NULL, 'cash', NULL),
(24, 'Electricity', '14123410588404', 1435.00, 'paid', '2026-02-09 11:24:58', 'X', NULL, NULL, 'cash', NULL),
(25, 'Electricity', '14123410588400', 1913.00, 'paid', '2026-02-09 11:25:52', 'Y', NULL, NULL, 'cash', NULL),
(26, 'Electricity', '14123410583201', 555.00, 'paid', '2026-02-09 11:26:43', 'Z', NULL, NULL, 'cash', NULL),
(27, 'Electricity', '14123410583203', 882.00, 'paid', '2026-02-09 11:31:10', 'AA', NULL, NULL, 'cash', NULL),
(29, 'Electricity', '14123410532500', 500.00, 'paid', '2026-02-09 11:32:10', 'AC', NULL, NULL, 'cash', NULL),
(30, 'Electricity', '14123410571400', 5965.00, 'paid', '2026-02-09 11:32:35', 'AD', NULL, NULL, 'cash', NULL),
(31, 'Electricity', '14123410593900', 1192.00, 'paid', '2026-02-09 11:33:07', 'AE', NULL, NULL, 'cash', NULL),
(32, 'Electricity', '14123410585501', 717.00, 'paid', '2026-02-09 11:33:32', 'AF', NULL, NULL, 'cash', NULL),
(33, 'Electricity', '14123410583200', 514.00, 'paid', '2026-02-09 11:34:00', 'AG', NULL, NULL, 'cash', NULL),
(34, 'Electricity', '14123410589401', 203.00, 'paid', '2026-02-09 11:34:42', 'AH', NULL, NULL, 'cash', NULL),
(35, 'Electricity', '12123410541901', 1958.00, 'paid', '2026-02-09 14:03:32', 'vvvvv', NULL, NULL, 'cash', NULL),
(36, 'Electricity', '13123410564504', 916.00, 'paid', '2026-02-09 14:04:14', 'dfg', NULL, NULL, 'cash', NULL),
(37, 'Electricity', '15123411662700', 425.00, 'paid', '2026-02-09 14:05:59', 'jjjjj', NULL, NULL, 'cash', NULL),
(38, 'Other', '40182602105656119', 965.00, 'paid', '2026-02-10 06:49:56', 'Saad Razzaq', NULL, NULL, 'cash', NULL),
(40, 'Electricity', '40182602105656119', 965.00, 'paid', '2026-02-10 07:38:53', 'Fard Chalan', 2, '', 'credit', '4556294532'),
(41, 'Electricity', '15123411850704', 2977.00, 'paid', '2026-02-10 07:36:44', 'AAMIR SADDIQUE', NULL, '03295230615', 'cash', '4556465428'),
(42, 'Electricity', '265895', 252.00, 'paid', '2026-02-10 07:52:54', 'Walk-in', NULL, '03006238233', 'cash', '2656');

-- --------------------------------------------------------

--
-- Table structure for table `daily_openings`
--

CREATE TABLE `daily_openings` (
  `id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `opening_balance` decimal(15,2) DEFAULT NULL,
  `closing_balance` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finance_ledger`
--

CREATE TABLE `finance_ledger` (
  `id` int(11) NOT NULL,
  `trans_date` date DEFAULT NULL,
  `type` enum('income','expense','transfer','sale','purchase','loan') NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `receipt_no` varchar(100) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `account_head` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `invoice_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `finance_ledger`
--

INSERT INTO `finance_ledger` (`id`, `trans_date`, `type`, `category`, `description`, `receipt_no`, `amount`, `payment_method`, `account_head`, `related_id`, `created_at`, `invoice_no`) VALUES
(1, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (13123410556203)', NULL, 893.00, 'Konnect', 'HBL Konnect BVS', 1, '2026-02-09 17:06:37', NULL),
(2, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (13123410556201)', NULL, 1136.00, 'Konnect', 'HBL Konnect BVS', 2, '2026-02-09 17:06:37', NULL),
(3, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410605201)', NULL, 933.00, 'Konnect', 'HBL Konnect BVS', 3, '2026-02-09 17:06:37', NULL),
(4, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410583700)', NULL, 2986.00, 'Konnect', 'HBL Konnect BVS', 4, '2026-02-09 17:06:37', NULL),
(5, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410593201)', NULL, 2437.00, 'Konnect', 'HBL Konnect BVS', 5, '2026-02-09 17:06:37', NULL),
(6, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410596700)', NULL, 1986.00, 'Konnect', 'HBL Konnect BVS', 6, '2026-02-09 17:06:37', NULL),
(7, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410571801)', NULL, 609.00, 'Konnect', 'HBL Konnect BVS', 7, '2026-02-09 17:06:37', NULL),
(8, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410571800)', NULL, 1815.00, 'Konnect', 'HBL Konnect BVS', 8, '2026-02-09 17:06:37', NULL),
(9, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410599701)', NULL, 663.00, 'Konnect', 'HBL Konnect BVS', 9, '2026-02-09 17:06:37', NULL),
(10, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (15123410618502)', NULL, 676.00, 'Konnect', 'HBL Konnect BVS', 10, '2026-02-09 17:06:37', NULL),
(11, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410604000)', NULL, 1333.00, 'Konnect', 'HBL Konnect BVS', 11, '2026-02-09 17:06:37', NULL),
(12, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410602608)', NULL, 183.00, 'Konnect', 'HBL Konnect BVS', 12, '2026-02-09 17:06:37', NULL),
(13, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410572604)', NULL, 15371.00, 'Konnect', 'HBL Konnect BVS', 13, '2026-02-09 17:06:37', NULL),
(14, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410572503)', NULL, 2845.00, 'Konnect', 'HBL Konnect BVS', 14, '2026-02-09 17:06:37', NULL),
(15, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410572200)', NULL, 854.00, 'Konnect', 'HBL Konnect BVS', 15, '2026-02-09 17:06:37', NULL),
(16, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410592208)', NULL, 676.00, 'Konnect', 'HBL Konnect BVS', 16, '2026-02-09 17:06:37', NULL),
(17, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410601500)', NULL, 1650.00, 'Konnect', 'HBL Konnect BVS', 17, '2026-02-09 17:06:37', NULL),
(18, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410590300)', NULL, 2195.00, 'Konnect', 'HBL Konnect BVS', 18, '2026-02-09 17:06:37', NULL),
(19, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410596701)', NULL, 1535.00, 'Konnect', 'HBL Konnect BVS', 19, '2026-02-09 17:06:37', NULL),
(20, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410572603)', NULL, 1133.00, 'Konnect', 'HBL Konnect BVS', 20, '2026-02-09 17:06:37', NULL),
(21, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410571300)', NULL, 831.00, 'Konnect', 'HBL Konnect BVS', 21, '2026-02-09 17:06:37', NULL),
(22, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410591700)', NULL, 1015.00, 'Konnect', 'HBL Konnect BVS', 23, '2026-02-09 17:06:37', NULL),
(23, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410588404)', NULL, 1435.00, 'Konnect', 'HBL Konnect BVS', 24, '2026-02-09 17:06:37', NULL),
(24, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410588400)', NULL, 1913.00, 'Konnect', 'HBL Konnect BVS', 25, '2026-02-09 17:06:37', NULL),
(25, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410583201)', NULL, 555.00, 'Konnect', 'HBL Konnect BVS', 26, '2026-02-09 17:06:37', NULL),
(26, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410583203)', NULL, 882.00, 'Konnect', 'HBL Konnect BVS', 27, '2026-02-09 17:06:37', NULL),
(27, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410532500)', NULL, 500.00, 'Konnect', 'HBL Konnect BVS', 29, '2026-02-09 17:06:37', NULL),
(28, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410571400)', NULL, 5965.00, 'Konnect', 'HBL Konnect BVS', 30, '2026-02-09 17:06:37', NULL),
(29, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410593900)', NULL, 1192.00, 'Konnect', 'HBL Konnect BVS', 31, '2026-02-09 17:06:37', NULL),
(30, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410585501)', NULL, 717.00, 'Konnect', 'HBL Konnect BVS', 32, '2026-02-09 17:06:37', NULL),
(31, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410583200)', NULL, 514.00, 'Konnect', 'HBL Konnect BVS', 33, '2026-02-09 17:06:37', NULL),
(32, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (14123410589401)', NULL, 203.00, 'Konnect', 'HBL Konnect BVS', 34, '2026-02-09 17:06:37', NULL),
(33, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (12123410541901)', NULL, 1958.00, 'Konnect', 'HBL Konnect BVS', 35, '2026-02-09 17:06:37', NULL),
(34, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (13123410564504)', NULL, 916.00, 'Konnect', 'HBL Konnect BVS', 36, '2026-02-09 17:06:37', NULL),
(35, '2026-02-09', 'expense', 'Utility Bill', 'Bill Paid: Electricity (15123411662700)', NULL, 425.00, 'Konnect', 'HBL Konnect BVS', 37, '2026-02-09 17:06:37', NULL),
(38, '2026-02-09', 'transfer', 'Internal', 'Load BVS Device', NULL, 105930.00, NULL, 'HBL Konnect BVS', NULL, '2026-02-10 06:37:16', NULL),
(42, '2026-02-10', '', 'Bill Credit', 'Credit Bill: Saad Razzaq Shakir (Electricity - 40182602105656119)', NULL, 965.00, 'Credit', 'Customer Ledger', NULL, '2026-02-10 07:30:28', NULL),
(43, '2026-02-10', 'expense', 'Refund', 'Bill Deleted/Refunded: 15123411850704 (mistake)', NULL, 2977.00, 'Cash', 'Shop Cash Drawer', NULL, '2026-02-10 07:35:11', NULL),
(44, '2026-02-10', 'income', 'Bill Collection', 'Collection: Electricity (15123411850704)', NULL, 2977.00, 'Cash', 'Shop Cash Drawer', NULL, '2026-02-10 07:36:27', NULL),
(45, '2026-02-10', 'expense', 'Utility Bill', 'Bill Paid: Electricity (15123411850704) TID: 4556465428', NULL, 2977.00, 'Konnect', 'HBL Konnect BVS', 41, '2026-02-10 07:36:44', NULL),
(46, '2026-02-10', 'expense', 'Utility Bill', 'Bill Paid: Electricity (40182602105656119) TID: 4556294532', NULL, 965.00, 'Konnect', 'HBL Konnect BVS', 40, '2026-02-10 07:38:53', NULL),
(47, '2026-02-10', '', 'Correction', 'Manual Correction: Aligned DB with Physical Device Balance', NULL, 0.00, NULL, 'HBL Konnect BVS', NULL, '2026-02-10 07:45:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) DEFAULT NULL,
  `type` enum('product','service') DEFAULT 'product',
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `sale_price` decimal(10,2) DEFAULT 0.00,
  `stock_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `item_name`, `type`, `purchase_price`, `sale_price`, `stock_qty`) VALUES
(1, 'Wallpapers', 'product', 20.00, 20.00, 49);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(20) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `type` enum('sale','purchase') DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL,
  `previous_balance` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `public_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT 0.00,
  `height` decimal(10,2) DEFAULT 0.00,
  `sq_ft` decimal(10,2) DEFAULT 0.00,
  `qty` int(11) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `person_name` varchar(100) DEFAULT NULL,
  `type` enum('given','taken') DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `status` enum('active','cleared') DEFAULT 'active',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `loan_category` enum('personal','installment','bank') DEFAULT 'personal',
  `item_name` varchar(100) DEFAULT NULL COMMENT 'For installments e.g. Solar Panel',
  `total_installments` int(11) DEFAULT 0,
  `paid_installments` int(11) DEFAULT 0,
  `installment_amount` decimal(15,2) DEFAULT 0.00 COMMENT 'Fixed monthly amount',
  `next_due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `person_name`, `type`, `total_amount`, `paid_amount`, `due_date`, `status`, `phone`, `address`, `loan_category`, `item_name`, `total_installments`, `paid_installments`, `installment_amount`, `next_due_date`) VALUES
(2, 'Saad Razzaq Shakir', 'given', 965.00, 0.00, NULL, 'active', '03114101053', 'Jalalpur Jattan', 'personal', NULL, 0, 0, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `invoice_no` varchar(20) DEFAULT NULL,
  `items_json` text DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queue_tokens`
--

CREATE TABLE `queue_tokens` (
  `id` int(11) NOT NULL,
  `token_number` varchar(20) NOT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('waiting','served','cancelled') DEFAULT 'waiting',
  `issued_by` int(11) NOT NULL,
  `issued_at` datetime DEFAULT current_timestamp(),
  `served_at` datetime DEFAULT NULL,
  `is_printed` tinyint(1) DEFAULT 0,
  `is_online` tinyint(1) DEFAULT 0,
  `service_type` enum('bisp','bills','other') DEFAULT 'bisp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `queue_tokens`
--

INSERT INTO `queue_tokens` (`id`, `token_number`, `cnic`, `name`, `status`, `issued_by`, `issued_at`, `served_at`, `is_printed`, `is_online`, `service_type`) VALUES
(1, '03-001', NULL, NULL, 'cancelled', 1, '2026-02-02 19:04:52', '2026-02-02 19:05:05', 1, 0, 'bisp'),
(2, '03-002', '3420189762733', '', 'served', 1, '2026-02-02 19:30:20', '2026-02-03 09:30:03', 1, 0, 'bisp'),
(3, '03-003', '3420189762733', 'saif ullah', 'cancelled', 1, '2026-02-02 19:46:47', NULL, 1, 0, 'bisp'),
(4, '03-001', '3420189762733', 'Saif Ullah', 'served', 1, '2026-02-03 03:47:03', '2026-02-03 03:48:39', 1, 0, 'bisp'),
(5, '03-002', '3420189762733', 'Saif Ullah', 'served', 1, '2026-02-03 03:47:24', '2026-02-03 03:49:33', 1, 0, 'bisp'),
(6, '03-003', '34201-8976273-3', 'Saif Ullah', 'served', 1, '2026-02-03 03:57:40', '2026-02-03 03:57:59', 1, 0, 'bisp'),
(7, '03-004', '34201-8976273-3', 'Saif Ullah', 'served', 1, '2026-02-03 12:36:21', '2026-02-03 12:37:00', 1, 0, 'bisp'),
(14, '06-001', '33333-3333333-3', '', 'served', 1, '2026-02-06 22:50:15', '2026-02-06 22:50:31', 1, 0, 'bisp'),
(15, '06-002', '22222222222222222222', 'Online User', 'cancelled', 999, '2026-02-06 22:51:11', '2026-02-06 22:51:43', 1, 1, 'bisp'),
(16, '06-003', '22222222222222222222', 'Online User', 'cancelled', 999, '2026-02-06 22:52:44', NULL, 1, 1, 'bisp'),
(17, '06-004', '22222222222222222222', 'Online User', 'cancelled', 999, '2026-02-06 22:52:59', NULL, 1, 1, 'bisp'),
(18, '06-005', '3420189762733', 'Online User', 'cancelled', 999, '2026-02-06 22:53:17', NULL, 1, 1, 'bisp'),
(19, '07-001', '3420119909268', 'Online User', 'cancelled', 999, '2026-02-07 02:53:43', '2026-02-07 17:17:17', 0, 1, 'bisp'),
(20, '07-002', '34201-8976273-3', '', 'served', 1, '2026-02-07 17:20:15', '2026-02-07 17:20:51', 1, 0, 'bisp');

-- --------------------------------------------------------

--
-- Table structure for table `saved_consumers`
--

CREATE TABLE `saved_consumers` (
  `id` int(11) NOT NULL,
  `consumer_number` varchar(100) DEFAULT NULL,
  `consumer_name` varchar(100) DEFAULT NULL,
  `bill_type` varchar(100) DEFAULT NULL,
  `last_paid_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mobile_no` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saved_consumers`
--

INSERT INTO `saved_consumers` (`id`, `consumer_number`, `consumer_name`, `bill_type`, `last_paid_date`, `mobile_no`) VALUES
(1, '13123410556203', 'a', 'Electricity', '2026-02-09 11:05:31', NULL),
(2, '13123410556201', 'b', 'Electricity', '2026-02-09 11:06:31', NULL),
(4, '14123410605201', 'c', 'Electricity', '2026-02-09 11:06:57', NULL),
(5, '14123410583700', 'd', 'Electricity', '2026-02-09 11:07:49', NULL),
(6, '14123410593201', 'e', 'Electricity', '2026-02-09 11:08:56', NULL),
(7, '14123410596700', 'f', 'Electricity', '2026-02-09 11:09:42', NULL),
(8, '14123410571801', 'G', 'Electricity', '2026-02-09 11:10:23', NULL),
(9, '14123410571800', 'H', 'Electricity', '2026-02-09 11:11:00', NULL),
(10, '14123410599701', 'I', 'Electricity', '2026-02-09 11:11:43', NULL),
(11, '15123410618502', 'J', 'Electricity', '2026-02-09 11:12:56', NULL),
(12, '14123410604000', 'K', 'Electricity', '2026-02-09 11:15:16', NULL),
(13, '14123410602608', 'L', 'Electricity', '2026-02-09 11:15:56', NULL),
(14, '14123410572604', 'M', 'Electricity', '2026-02-09 11:16:29', NULL),
(15, '14123410572503', 'N', 'Electricity', '2026-02-09 11:17:04', NULL),
(16, '14123410572200', 'O', 'Electricity', '2026-02-09 11:17:28', NULL),
(17, '14123410592208', 'P', 'Electricity', '2026-02-09 11:17:56', NULL),
(18, '14123410601500', 'Q', 'Electricity', '2026-02-09 11:18:19', NULL),
(19, '14123410590300', 'R', 'Electricity', '2026-02-09 11:18:44', NULL),
(20, '14123410596701', 'S', 'Electricity', '2026-02-09 11:19:20', NULL),
(21, '14123410572603', 'T', 'Electricity', '2026-02-09 11:20:05', NULL),
(22, '14123410571300', 'U', 'Electricity', '2026-02-09 11:20:39', NULL),
(23, '12123410541901', 'vvvvv', 'Electricity', '2026-02-09 14:03:32', NULL),
(24, '14123410591700', 'W', 'Electricity', '2026-02-09 11:21:57', NULL),
(25, '14123410588404', 'X', 'Electricity', '2026-02-09 11:24:58', NULL),
(26, '14123410588400', 'Y', 'Electricity', '2026-02-09 11:25:52', NULL),
(27, '14123410583201', 'Z', 'Electricity', '2026-02-09 11:26:43', NULL),
(28, '14123410583203', 'AA', 'Electricity', '2026-02-09 11:31:10', NULL),
(29, '14123410564504', 'AB', 'Electricity', '2026-02-09 11:31:45', NULL),
(30, '14123410532500', 'AC', 'Electricity', '2026-02-09 11:32:10', NULL),
(31, '14123410571400', 'AD', 'Electricity', '2026-02-09 11:32:35', NULL),
(32, '14123410593900', 'AE', 'Electricity', '2026-02-09 11:33:07', NULL),
(33, '14123410585501', 'AF', 'Electricity', '2026-02-09 11:33:32', NULL),
(34, '14123410583200', 'AG', 'Electricity', '2026-02-09 11:34:00', NULL),
(35, '14123410589401', 'AH', 'Electricity', '2026-02-09 11:34:42', NULL),
(37, '13123410564504', 'dfg', 'Electricity', '2026-02-09 14:04:14', NULL),
(38, '15123411662700', 'jjjjj', 'Electricity', '2026-02-09 14:05:59', NULL),
(39, '40182602105656119', 'Fard Chalan', 'Other', '2026-02-09 19:00:00', ''),
(40, '15123411850704', 'AAMIR SADDIQUE', 'Electricity', '2026-02-09 19:00:00', '03295230615'),
(41, '265895', 'Walk-in', 'Electricity', '2026-02-09 19:00:00', '03006238233');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'Call Token', 'Called token ID: 11', '119.156.37.73', '2026-02-06 09:31:26'),
(2, 1, 'Call Token', 'Called token ID: 12', '119.156.35.179', '2026-02-06 10:19:16'),
(3, 1, 'Call Token', 'Called token ID: 13', '119.156.35.179', '2026-02-06 10:19:21'),
(4, 1, 'Call Token', 'Called token ID: 14', '119.156.45.4', '2026-02-06 17:50:31'),
(5, 1, 'Call Token', 'Called token ID: 15', '119.156.45.4', '2026-02-06 17:51:43'),
(6, 1, 'Call Token', 'Called token ID: 19', '119.156.34.186', '2026-02-07 12:17:17'),
(7, 1, 'Call Token', 'Called token ID: 20', '119.156.34.186', '2026-02-07 12:20:51'),
(8, 14, 'Login', 'User Logged In successfully', '119.156.46.128', '2026-02-09 22:53:22'),
(9, 1, 'Login', 'User Logged In successfully', '119.156.46.128', '2026-02-09 22:59:45'),
(10, 1, 'Login', 'User Logged In successfully', '119.156.46.128', '2026-02-09 23:01:32'),
(11, 1, 'Login', 'User Logged In successfully', '119.156.46.128', '2026-02-09 23:04:42'),
(12, 1, 'Login', 'User Logged In successfully', '119.156.46.128', '2026-02-09 23:05:41'),
(13, 1, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:14:53'),
(14, 14, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:18:36'),
(15, 14, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:20:41'),
(16, 1, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:21:17'),
(17, 15, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:22:54'),
(18, 1, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:24:21'),
(19, 1, 'Login', 'User Logged In', '119.156.46.128', '2026-02-09 23:25:22'),
(20, 1, 'Login', 'User Logged In', '154.80.33.107', '2026-02-10 01:36:10'),
(21, 1, 'Delete Bill', 'Deleted Bill ID 39. Amount: 2977.00. Reason: mistake', NULL, '2026-02-10 07:35:11'),
(22, 1, 'Add User', 'Created user staff', '119.156.45.140', '2026-02-10 09:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'status_color', 'danger'),
(2, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(3, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(4, 'shop_open_time', '09:00'),
(5, 'shop_close_time', '20:00'),
(6, 'enable_auto_hours', '0'),
(7, 'daily_token_limit', '50'),
(8, 'service_speed_mins', '14'),
(9, 'break_mode', '0'),
(10, 'require_phone', '1'),
(11, 'status_color', 'danger'),
(12, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(13, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(14, 'shop_open_time', '09:00'),
(15, 'shop_close_time', '20:00'),
(16, 'enable_auto_hours', '0'),
(17, 'daily_token_limit', '50'),
(18, 'service_speed_mins', '14'),
(19, 'break_mode', '0'),
(20, 'require_phone', '1'),
(21, 'status_color', 'danger'),
(22, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(23, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(24, 'shop_open_time', '09:00'),
(25, 'shop_close_time', '20:00'),
(26, 'enable_auto_hours', '0'),
(27, 'daily_token_limit', '50'),
(28, 'service_speed_mins', '14'),
(29, 'break_mode', '0'),
(30, 'require_phone', '1'),
(31, 'status_color', 'danger'),
(32, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(33, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(34, 'shop_open_time', '09:00'),
(35, 'shop_close_time', '20:00'),
(36, 'enable_auto_hours', '0'),
(37, 'daily_token_limit', '50'),
(38, 'service_speed_mins', '14'),
(39, 'break_mode', '0'),
(40, 'require_phone', '1'),
(41, 'status_color', 'danger'),
(42, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(43, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(44, 'shop_open_time', '09:00'),
(45, 'shop_close_time', '20:00'),
(46, 'enable_auto_hours', '0'),
(47, 'daily_token_limit', '50'),
(48, 'service_speed_mins', '14'),
(49, 'break_mode', '0'),
(50, 'require_phone', '1'),
(51, 'status_color', 'danger'),
(52, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(53, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(54, 'shop_open_time', '09:00'),
(55, 'shop_close_time', '20:00'),
(56, 'enable_auto_hours', '0'),
(57, 'daily_token_limit', '50'),
(58, 'service_speed_mins', '14'),
(59, 'break_mode', '0'),
(60, 'require_phone', '1'),
(61, 'status_color', 'danger'),
(62, 'status_text', 'بینظیر انکم سپورٹ ادائیگی مرکز'),
(63, 'announcement', 'خوش آمدید! ہمارے ہاں BISP کی سہ ماہی قسط بائیو میٹرک تصدیق کے ساتھ ادا کی جاتی ہے،معزز صارف! اپنی رقم کی وصولی کے لیے ٹوکن حاصل کریں۔ ⚠️ نوٹ: اپنا پن کوڈ کسی کو نہ بتائیں۔ شکریہ'),
(64, 'shop_open_time', '09:00'),
(65, 'shop_close_time', '20:00'),
(66, 'enable_auto_hours', '0'),
(67, 'daily_token_limit', '50'),
(68, 'service_speed_mins', '14'),
(69, 'break_mode', '0'),
(70, 'require_phone', '1'),
(71, 'shop_name', 'ARHAM PRINTERS'),
(72, 'shop_address', 'Domela Chowk, Jalalpur Jattan'),
(73, 'shop_phone', '0300-6238233'),
(74, 'invoice_footer', 'Computer Generated Receipt - No Signature Required');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `token_id` int(11) DEFAULT NULL,
  `beneficiary_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `konnect_trx_id` varchar(50) DEFAULT NULL,
  `txn_type` enum('bisp_payout','bill_payment') DEFAULT 'bisp_payout',
  `agent_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `notes` mediumtext DEFAULT NULL,
  `status` enum('success','void') DEFAULT 'success',
  `void_reason` mediumtext DEFAULT NULL,
  `void_by` int(11) DEFAULT NULL,
  `income` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `token_id`, `beneficiary_id`, `amount`, `konnect_trx_id`, `txn_type`, `agent_id`, `created_at`, `notes`, `status`, `void_reason`, `void_by`, `income`) VALUES
(3, NULL, 1, 13500.00, NULL, 'bisp_payout', 1, '2026-02-03 12:37:25', NULL, 'success', NULL, NULL, 0.00),
(4, NULL, 1, 10500.00, NULL, 'bisp_payout', 1, '2026-02-03 19:51:18', NULL, 'success', NULL, NULL, 0.00),
(5, NULL, 1, 10500.00, '5552222', 'bisp_payout', 1, '2026-02-06 00:31:15', NULL, 'success', NULL, NULL, 0.00),
(6, NULL, 1, 10500.00, '222222', 'bisp_payout', 1, '2026-02-07 17:18:24', NULL, 'success', NULL, NULL, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','agent') DEFAULT 'agent',
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `role`, `last_login`, `status`, `permissions`) VALUES
(1, 'Saif Ullah', 'admin', '$2y$10$hdG/8I64545ls05v0wqKvu4llU7RQtQ/ALx7t2HbZ4uKPdE83ekYS', 'admin', '2026-02-12 00:17:39', 1, '{\"bisp\":\"1\",\"hbl\":\"1\",\"shop\":\"1\",\"loans\":\"1\"}'),
(16, 'staff', 'kaif', '$2y$10$FVOWyfB08kZQ7uS3qPAqK.7nxwixhY0UnMCqv6uT6crxXcYc4d97O', '', '2026-02-11 13:30:56', 1, '{\"admin\":0,\"bisp\":0,\"hbl\":1,\"loans\":0,\"shop\":1,\"closing\":0}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_name` (`account_name`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnic` (`cnic`),
  ADD KEY `idx_search` (`cnic`,`phone`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill_queue`
--
ALTER TABLE `bill_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_openings`
--
ALTER TABLE `daily_openings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `date` (`date`,`account_id`);

--
-- Indexes for table `finance_ledger`
--
ALTER TABLE `finance_ledger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_no` (`invoice_no`),
  ADD KEY `invoice_no_2` (`invoice_no`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD UNIQUE KEY `public_token` (`public_token`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `queue_tokens`
--
ALTER TABLE `queue_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_consumers`
--
ALTER TABLE `saved_consumers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `consumer_number` (`consumer_number`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_id` (`beneficiary_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill_queue`
--
ALTER TABLE `bill_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `daily_openings`
--
ALTER TABLE `daily_openings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finance_ledger`
--
ALTER TABLE `finance_ledger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue_tokens`
--
ALTER TABLE `queue_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `saved_consumers`
--
ALTER TABLE `saved_consumers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
