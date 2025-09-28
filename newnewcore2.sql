-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 28, 2025 at 09:12 AM
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
-- Database: `newcore2`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_service_provider`
--

DROP TABLE IF EXISTS `active_service_provider`;
CREATE TABLE IF NOT EXISTS `active_service_provider` (
  `provider_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_type` int NOT NULL DEFAULT '3',
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `services` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `iso_certified` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `business_permit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_approved` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `active_service_provider`
--

INSERT INTO `active_service_provider` (`provider_id`, `company_name`, `email`, `password`, `account_type`, `contact_person`, `contact_number`, `address`, `services`, `iso_certified`, `business_permit`, `company_profile`, `date_approved`, `status`) VALUES
(31, 'FLASH EXPRESS', 'flashexpressincorporated@gmail.com', 'flashexpress', 3, 'Kurt Cobain', '09315454613', 'Unit 304 Dona Julita Bldg., 112 Kamuning Road, Quezon City 1103', 'land', 'yes', 'businesspermitsample.png', 'flash.png', '2025-09-22 03:43:52', 'Active'),
(32, 'ABC Freight Express', 'abcfreightexpress@gmail.com', 'abc', 3, 'April Joy Consigna', '09107993740', 'asdasd', 'land', 'yes', 'businesspermitsample.png', 'companyprofle.jpg', '2025-09-22 08:30:05', 'Active'),
(33, 'AVRIL FREIGHT EXPRESS', 'apriljoyconsigna@gmail.com', 'aprik', 3, 'April Joy Consigna', '09275374767', 'jk', 'land', 'yes', 'Messenger_creation_A8DE8FC8-FDDF-4F9D-9DCB-030D795B7BEF.jpeg', 'metabase.png', '2025-09-22 08:35:17', 'Active'),
(34, 'Jazz Inc', 'jazznellevince.a@gmail.com', '', 3, 'Jazz', '09777323270', 'Phase 1F Ottawa St. B3 L4 Vista Verde Llano', 'Lahat', NULL, NULL, NULL, '2025-09-27 20:22:11', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `admin_list`
--

DROP TABLE IF EXISTS `admin_list`;
CREATE TABLE IF NOT EXISTS `admin_list` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_type` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_list`
--

INSERT INTO `admin_list` (`email`, `password`, `account_type`) VALUES
('admin', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `calculated_rates`
--

DROP TABLE IF EXISTS `calculated_rates`;
CREATE TABLE IF NOT EXISTS `calculated_rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `route_id` int NOT NULL,
  `provider_id` int NOT NULL,
  `carrier_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` decimal(10,2) DEFAULT '0.00',
  `total_rate` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calculated_rates`
--

INSERT INTO `calculated_rates` (`id`, `route_id`, `provider_id`, `carrier_type`, `unit`, `quantity`, `total_rate`, `created_at`, `status`) VALUES
(47, 59, 31, 'Land', 'per km', 0.00, 288.40, '2025-09-22 12:51:36', 'completed'),
(48, 60, 31, 'Land', 'per km', 0.00, 187.60, '2025-09-22 15:36:06', 'completed'),
(49, 61, 32, 'Air', 'per container', 500.00, 25000.00, '2025-09-22 17:35:50', 'completed'),
(50, 62, 31, 'Land', 'per km', 0.00, 7495.60, '2025-09-22 18:01:39', 'completed'),
(51, 63, 31, 'Land', 'per km', 0.00, 8839.60, '2025-09-22 18:07:25', 'Pending'),
(52, 63, 31, 'Land', 'per kg', 13.00, 650.00, '2025-09-22 18:11:33', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `freight_rates`
--

DROP TABLE IF EXISTS `freight_rates`;
CREATE TABLE IF NOT EXISTS `freight_rates` (
  `rate_id` int NOT NULL AUTO_INCREMENT,
  `provider_id` int NOT NULL,
  `mode` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `distance_range` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `weight_range` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `unit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Accepted','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  PRIMARY KEY (`rate_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freight_rates`
--

INSERT INTO `freight_rates` (`rate_id`, `provider_id`, `mode`, `distance_range`, `weight_range`, `rate`, `unit`, `created_at`, `status`) VALUES
(47, 31, 'land', '0-2000km', '0-2000kg', 5.00, 'per km', '2025-09-22 10:10:02', 'Accepted'),
(48, 31, 'land', '0-2000km', '0-2000kg', 50.00, 'per kg', '2025-09-22 10:10:53', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `network_points`
--

DROP TABLE IF EXISTS `network_points`;
CREATE TABLE IF NOT EXISTS `network_points` (
  `point_id` int NOT NULL AUTO_INCREMENT,
  `point_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `point_type` enum('Port','Airport','Warehouse','Terminal') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status` enum('Active','Inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`point_id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `network_points`
--

INSERT INTO `network_points` (`point_id`, `point_name`, `point_type`, `country`, `city`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(86, 'Batangas Port', 'Port', NULL, 'Batangas Port', 13.753794, 121.042717, 'Active', '2025-09-22 07:27:20', '2025-09-22 07:27:20'),
(87, 'Bestlink College of the Philippines', 'Port', NULL, 'Bestlink College of the Philippines', 14.728559, 121.041615, 'Active', '2025-09-22 07:27:51', '2025-09-22 07:30:08'),
(88, 'Warehouse A', 'Port', NULL, 'Rondalla Street QC', 14.7247, 121.067059, 'Active', '2025-09-22 07:57:57', '2025-09-22 07:57:57'),
(89, 'Warehouse A', 'Warehouse', NULL, 'Lagro High School', 14.727442, 121.066866, 'Active', '2025-09-22 12:38:43', '2025-09-22 12:38:43'),
(90, 'Bestlink College of the Philippines', 'Port', NULL, 'Bestlink College of the Philippines', 14.728559, 121.041615, 'Active', '2025-09-22 15:33:04', '2025-09-22 15:33:04'),
(91, 'Warehouse Z', 'Port', NULL, 'Pugong Ginto Street', 14.715214, 121.04625, 'Active', '2025-09-22 15:33:55', '2025-09-22 15:33:55'),
(92, 'General Santos Airport', 'Airport', NULL, 'General Santos Airport', 6.063702, 125.098489, 'Active', '2025-09-22 17:31:45', '2025-09-22 17:32:20'),
(93, 'Ninoy Aquino International Airport', 'Airport', NULL, 'Ninoy Aquino International Airport', 14.512302, 121.021886, 'Active', '2025-09-22 17:32:54', '2025-09-22 17:33:07'),
(94, 'Manila Port', 'Port', NULL, 'Manila Port', 14.583324, 120.973858, 'Active', '2025-09-22 18:00:10', '2025-09-22 18:00:10'),
(95, 'Asamba Hub', 'Warehouse', NULL, 'Asamba Bridge', 14.66463, 121.016121, 'Active', '2025-09-24 16:23:52', '2025-09-24 16:24:20'),
(96, 'Aghik Port', 'Port', NULL, 'Malabon', 14.662315, 120.939045, 'Active', '2025-09-25 19:23:45', '2025-09-25 19:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `newaccounts`
--

DROP TABLE IF EXISTS `newaccounts`;
CREATE TABLE IF NOT EXISTS `newaccounts` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_type` int DEFAULT '2',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newaccounts`
--

INSERT INTO `newaccounts` (`user_id`, `email`, `password`, `account_type`) VALUES
(17, 'leonardgaro@gmail.com', 'newpassword123', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'info',
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `message`, `type`, `link`, `is_read`, `created_at`) VALUES
(8, 'Your freight rate #10 has been Accepted.', 'service_provider', 'rates_management.php?rate_id=10', 1, '2025-09-13 11:06:13'),
(9, 'Freight rate #10 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-13 11:06:13'),
(10, 'Your freight rate #9 has been Rejected.', 'service_provider', 'rates_management.php?rate_id=9', 1, '2025-09-13 11:06:14'),
(11, 'Freight rate #9 has been Rejected.', 'admin', 'rates_management.php', 1, '2025-09-13 11:06:14'),
(12, 'Freight rate #11 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-13 11:09:36'),
(13, 'New Service Provider Registered: DOWNTOWN QC', 'info', 'pending_providers.php', 1, '2025-09-13 19:43:23'),
(14, 'Freight rate #12 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-13 20:27:40'),
(15, 'Schedule ID 13 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-13 21:23:14'),
(16, 'Schedule ID 13 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-13 21:23:28'),
(17, 'Service Provider approved: DOWNTOWN QC', 'service_provider', 'active_providers.php', 1, '2025-09-14 16:11:41'),
(18, 'Schedule ID 14 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-18 12:31:17'),
(19, 'Schedule ID 14 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-18 12:31:27'),
(20, 'Freight rate #14 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-19 07:12:32'),
(21, 'Schedule ID 15 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-19 07:23:31'),
(22, 'Schedule ID 15 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-19 07:23:33'),
(23, 'Schedule ID 16 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-19 07:26:00'),
(24, 'Schedule ID 16 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-19 07:26:00'),
(25, 'Schedule ID 17 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-19 07:37:03'),
(26, 'Schedule ID 17 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-19 07:37:05'),
(27, 'Schedule ID 18 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-19 07:42:11'),
(28, 'Schedule ID 18 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-19 07:42:19'),
(29, 'Service Provider approved: COCO PANDAN', 'service_provider', 'active_providers.php', 1, '2025-09-19 07:55:16'),
(30, 'Freight rate #20 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-19 08:15:07'),
(31, 'Freight rate #19 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-19 08:15:07'),
(32, 'Freight rate #15 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-19 08:15:08'),
(33, 'Schedule ID 20 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:08:08'),
(34, 'Schedule ID 21 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:11:09'),
(35, 'Schedule ID 22 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:13:18'),
(36, 'Schedule ID 22 has been completed. Route removed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:13:27'),
(37, 'Schedule ID 23 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:16:02'),
(38, 'Schedule ID 23 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:16:02'),
(39, 'Schedule ID 24 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:23:32'),
(40, 'Schedule ID 24 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:23:33'),
(41, 'Schedule ID 25 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:24:35'),
(42, 'Schedule ID 24 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 11:24:38'),
(43, 'Schedule ID 26 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 13:21:22'),
(44, 'New Service Provider Registered: JOLO & COCO Freight Services', 'info', 'pending_providers.php', 1, '2025-09-21 13:28:59'),
(45, 'Service Provider approved: JOLO & COCO Freight Services', 'service_provider', 'active_providers.php', 1, '2025-09-21 13:51:11'),
(46, 'New Service Provider Registered: COCO PANDAN', 'info', 'pending_providers.php', 1, '2025-09-21 13:52:38'),
(47, 'Service Provider rejected: COCO PANDAN', 'service_provider', 'pending_providers.php', 1, '2025-09-21 13:53:19'),
(48, 'New Service Provider Registered: GARO FREIGHT SERVICES', 'info', 'pending_providers.php', 1, '2025-09-21 17:06:14'),
(49, 'Service Provider approved: GARO FREIGHT SERVICES', 'service_provider', 'active_providers.php', 1, '2025-09-21 18:16:21'),
(50, 'Freight rate #22 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 18:43:50'),
(51, 'Schedule ID 27 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-21 18:47:08'),
(52, 'New Service Provider Registered: FLASH EXPRESS', 'info', 'pending_providers.php', 1, '2025-09-21 19:42:46'),
(53, 'Service Provider approved: FLASH EXPRESS', 'service_provider', 'active_providers.php', 1, '2025-09-21 19:43:52'),
(54, 'Freight rate #23 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 19:45:03'),
(55, 'Schedule ID 29 has been delayed.', 'info', 'schedule_routes.php', 1, '2025-09-21 19:48:06'),
(56, 'Schedule ID 29 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 19:48:25'),
(57, 'Freight rate #24 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 20:26:17'),
(58, 'Freight rate #25 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 20:37:27'),
(59, 'Freight rate #26 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 20:49:18'),
(60, 'Freight rate #27 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 20:59:49'),
(61, 'Freight rate #28 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 21:03:08'),
(62, 'Freight rate #29 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 21:04:11'),
(63, 'Schedule ID 32 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 23:14:20'),
(64, 'Schedule ID 31 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 23:14:20'),
(65, 'Schedule ID 30 has been completed.', 'info', 'schedule_routes.php', 1, '2025-09-21 23:14:21'),
(66, 'Freight rate #30 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 23:24:30'),
(67, 'Freight rate #31 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 23:32:12'),
(68, 'Freight rate #32 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-21 23:41:28'),
(69, 'Freight rate #34 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-22 00:14:02'),
(70, 'Freight rate #35 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-22 00:16:06'),
(71, 'New Service Provider Registered: ABC Freight Express', 'info', 'pending_providers.php', 1, '2025-09-22 00:29:04'),
(72, 'Service Provider approved: ABC Freight Express', 'service_provider', 'active_providers.php', 1, '2025-09-22 00:30:05'),
(73, 'Freight rate #39 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-22 00:30:32'),
(74, 'New Service Provider Registered: AVRIL FREIGHT EXPRESS', 'info', 'pending_providers.php', 1, '2025-09-22 00:34:50'),
(75, 'Service Provider approved: AVRIL FREIGHT EXPRESS', 'service_provider', 'active_providers.php', 1, '2025-09-22 00:35:17'),
(76, 'Schedule ID 33 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-22 04:36:58'),
(77, 'Schedule ID 33 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 04:36:59'),
(78, 'Schedule ID 35 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 04:47:49'),
(79, 'Schedule ID 34 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 04:47:50'),
(80, 'Schedule ID 36 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 04:55:25'),
(81, 'Freight rate #44 has been Accepted.', 'admin', 'rates_management.php', 1, '2025-09-22 07:38:19'),
(82, 'Schedule ID 38 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-22 09:37:19'),
(83, 'Schedule ID 38 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 09:37:21'),
(84, 'Schedule ID 37 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 09:37:46'),
(85, 'Schedule ID 39 has been delayed.', 'info', 'provider_schedules.php', 1, '2025-09-22 10:03:00'),
(86, 'Schedule ID 39 has been completed.', 'info', 'provider_schedules.php', 1, '2025-09-22 10:03:12'),
(87, 'New User Registered: bathanjc@gmail.com', 'info', 'user_management.php', 1, '2025-09-22 10:54:15'),
(88, 'New network point added: \'Aghik Port\' in Malabon (Port)', 'success', 'network_manage.php', 1, '2025-09-25 11:23:45'),
(89, 'Service Provider approved: Jazz Inc', 'service_provider', 'active_providers.php', 0, '2025-09-27 12:22:11'),
(90, 'New schedule created for 2025-09-27 at 09:35 (Rate: $650)', 'success', 'confirmed_timetables.php', 0, '2025-09-27 12:34:22');

-- --------------------------------------------------------

--
-- Table structure for table `pending_service_provider`
--

DROP TABLE IF EXISTS `pending_service_provider`;
CREATE TABLE IF NOT EXISTS `pending_service_provider` (
  `registration_id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `account_type` int NOT NULL DEFAULT '3',
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `services` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Provider website URL',
  `iso_certified` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `business_permit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_profile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_submitted` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `imported_from` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Source system (logistic1, manual, etc.)',
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `rejection_reason` text COLLATE utf8mb4_general_ci COMMENT 'Reason for rejection',
  `rejected_at` datetime DEFAULT NULL COMMENT 'Rejection timestamp',
  `approved_at` datetime DEFAULT NULL COMMENT 'Approval timestamp',
  `external_id` int DEFAULT NULL COMMENT 'External API provider ID if imported',
  PRIMARY KEY (`registration_id`),
  KEY `idx_external_id` (`external_id`),
  KEY `idx_imported_from` (`imported_from`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_service_provider`
--

INSERT INTO `pending_service_provider` (`registration_id`, `company_name`, `email`, `password`, `account_type`, `contact_person`, `contact_number`, `address`, `services`, `website`, `iso_certified`, `business_permit`, `company_profile`, `date_submitted`, `created_at`, `imported_from`, `status`, `rejection_reason`, `rejected_at`, `approved_at`, `external_id`) VALUES
(42, 'jnntt', 'rovic.castrodes@gmail.com', '', 3, 'Christian Rovic Ocop Castrodes', '09945948152', '55 Morning Star Culiat Quezon City Quezon City', 'transportaion', '', NULL, NULL, NULL, '2025-09-27 11:08:34', '2025-09-27 11:08:34', NULL, 'Pending', NULL, NULL, NULL, 0),
(43, 'Jazz Inc', 'jazznellevince.a@gmail.com', '', 3, 'Jazz', '09777323270', 'Phase 1F Ottawa St. B3 L4 Vista Verde Llano', 'Lahat', '', NULL, NULL, NULL, '2025-09-27 20:22:28', '2025-09-27 20:22:28', NULL, 'Pending', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
CREATE TABLE IF NOT EXISTS `routes` (
  `route_id` int NOT NULL AUTO_INCREMENT,
  `origin_id` int NOT NULL,
  `destination_id` int NOT NULL,
  `carrier_type` enum('land','air','sea') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `provider_id` int NOT NULL,
  `distance_km` decimal(10,2) NOT NULL,
  `eta_min` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`route_id`),
  KEY `fk_routes_origin` (`origin_id`),
  KEY `fk_routes_destination` (`destination_id`),
  KEY `fk_routes_provider` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `origin_id`, `destination_id`, `carrier_type`, `provider_id`, `distance_km`, `eta_min`, `created_at`, `status`) VALUES
(55, 88, 87, 'land', 31, 4.24, 14, '2025-09-22 07:58:09', 'completed'),
(57, 87, 86, 'land', 33, 126.23, 218, '2025-09-22 08:36:03', 'completed'),
(58, 86, 87, 'land', 31, 126.28, 220, '2025-09-22 08:42:54', 'completed'),
(59, 87, 89, 'land', 31, 4.12, 13, '2025-09-22 12:51:20', 'completed'),
(60, 91, 90, 'land', 31, 2.68, 9, '2025-09-22 15:35:28', 'completed'),
(61, 93, 92, 'air', 32, 1039.75, 78, '2025-09-22 17:33:37', 'completed'),
(62, 94, 86, 'land', 31, 107.08, 187, '2025-09-22 18:01:04', 'completed'),
(63, 86, 87, 'land', 31, 126.28, 220, '2025-09-22 18:07:12', 'pending'),
(64, 87, 94, 'land', 31, 19.49, 48, '2025-09-22 18:09:01', 'pending'),
(65, 87, 88, 'land', 31, 4.47, 16, '2025-09-22 19:40:36', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `rate_id` int NOT NULL,
  `route_id` int NOT NULL,
  `provider_id` int DEFAULT NULL,
  `sop_id` int NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'scheduled',
  `total_rate` decimal(12,2) NOT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `route_id` (`route_id`),
  KEY `sop_id` (`sop_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `rate_id`, `route_id`, `provider_id`, `sop_id`, `schedule_date`, `schedule_time`, `created_at`, `status`, `total_rate`) VALUES
(33, 44, 57, 33, 4, '2025-09-22', '00:35:00', '2025-09-22 04:35:18', 'completed', 6311.50),
(34, 46, 58, 31, 6, '2025-09-24', '00:45:00', '2025-09-22 04:45:56', 'completed', 8839.60),
(35, 45, 55, 31, 7, '2025-09-30', '12:46:00', '2025-09-22 04:47:01', 'completed', 296.80),
(36, 47, 59, 31, 4, '2025-09-22', '12:54:00', '2025-09-22 04:52:52', 'completed', 288.40),
(37, 48, 60, 31, 4, '2025-09-25', '03:37:00', '2025-09-22 07:36:39', 'completed', 187.60),
(38, 49, 61, 32, 7, '2025-09-22', '17:39:00', '2025-09-22 09:36:08', 'completed', 25000.00),
(39, 50, 62, 31, 4, '2025-09-22', '18:01:00', '2025-09-22 10:01:58', 'completed', 7495.60),
(40, 52, 63, 31, 4, '2025-09-27', '09:35:00', '2025-09-27 12:34:22', 'scheduled', 650.00);

-- --------------------------------------------------------

--
-- Table structure for table `sop_documents`
--

DROP TABLE IF EXISTS `sop_documents`;
CREATE TABLE IF NOT EXISTS `sop_documents` (
  `sop_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Active','Draft','Archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sop_documents`
--

INSERT INTO `sop_documents` (`sop_id`, `title`, `category`, `content`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(4, 'Fragile Cargo Handling', 'Safety', '1. Identify fragile cargo upon receipt and ensure it is properly labeled with “FRAGILE” stickers.  \r\n2. Inspect packaging to confirm it meets protective standards (bubble wrap, cushioning, wooden crate if required).  \r\n3. Use specialized equipment (trolleys, forklifts with padding) for loading and unloading.  \r\n4. Ensure cargo is always handled manually with two or more staff if weight exceeds 20kg.  \r\n5. Secure fragile cargo in transport vehicles using straps and separators to avoid shifting during transit.  \r\n6. Avoid stacking heavy items on top of fragile cargo during storage and transport.  \r\n7. Document handling steps in the shipment record for accountability.  \r\n8. In case of damage, immediately report, photograph, and file an incident log with operations management.', NULL, 'Active', '2025-09-12 20:52:38', '2025-09-21 10:51:15'),
(5, 'Hazardous Cargo', 'Safety', '1. asdsaad', NULL, 'Active', '2025-09-12 22:37:07', '2025-09-13 14:55:55'),
(6, 'Handling and Shipment of Live Animals', 'Customs', '1. Obtain and verify veterinary health certificates, import/export permits, vaccination records, and other required documents.  \r\n2. Submit documents to customs and quarantine authorities for clearance before shipment.  \r\n3. Prepare IATA-approved containers with proper ventilation, bedding, labels, and handling instructions.  \r\n4. Have a veterinary officer inspect animals and issue a \"Fit to Transport\" certificate.  \r\n5. Load animals under supervision of customs and quarantine officers, ensuring segregation from incompatible cargo.  \r\n6. Monitor animals during transit with adequate ventilation, food, and water.  \r\n7. Present documents at the destination for customs and veterinary inspection.  \r\n8. Deliver animals to the consignee once clearance is granted.  \r\n9. Record shipment details, customs references, and animal health condition in the system.', NULL, 'Active', '2025-09-19 07:15:55', '2025-09-19 07:21:16'),
(7, 'Handling and Shipment of Perishable Goods via Air Transport', 'Logistics', '1. Verify that the shipper provides valid documents including health certificates, invoices, and export permits if required.  \r\n2. Ensure all perishable goods are properly packed in insulated or refrigerated containers suitable for air transport.  \r\n3. Check temperature control devices and labeling such as “Perishable – Keep Refrigerated” before acceptance.  \r\n4. Submit documents to customs and quarantine authorities for clearance prior to flight loading.  \r\n5. Load perishable goods last and unload first to minimize exposure to non-controlled environments.  \r\n6. Monitor storage conditions in the aircraft hold and ensure appropriate ventilation or refrigeration is active.  \r\n7. Notify ground handling teams at the destination of special handling requirements for perishable cargo.  \r\n8. On arrival, present documents for customs and health inspections before releasing cargo.  \r\n9. Deliver goods immediately to the consignee or transfer to a cold storage facility to prevent spoilage.  \r\n10. Record shipment details, temperature logs, and customs references in the system for traceability.', NULL, 'Archived', '2025-09-19 07:36:10', '2025-09-21 10:51:21');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `freight_rates`
--
ALTER TABLE `freight_rates`
  ADD CONSTRAINT `freight_rates_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `active_service_provider` (`provider_id`) ON DELETE CASCADE;

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `fk_routes_destination` FOREIGN KEY (`destination_id`) REFERENCES `network_points` (`point_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_routes_origin` FOREIGN KEY (`origin_id`) REFERENCES `network_points` (`point_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_routes_provider` FOREIGN KEY (`provider_id`) REFERENCES `active_service_provider` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`sop_id`) REFERENCES `sop_documents` (`sop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`provider_id`) REFERENCES `active_service_provider` (`provider_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
