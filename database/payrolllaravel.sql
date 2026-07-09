-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2026 at 08:30 AM
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
-- Database: `payrolllaravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','hr','manager','employee') NOT NULL DEFAULT 'employee',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `employee_id`, `email`, `email_verified_at`, `password`, `password_reset_token`, `password_reset_expires_at`, `role`, `is_active`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
('1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '22ff1b14-2301-4a27-a022-6736b3c9f318', 'jerson.cerezo.100@gmail.com', NULL, '$2y$12$sv9r3QP.MWvkTkH6vSamMenPRF5eBEd3po/W05ZAty3zlYcmx7oCa', NULL, NULL, 'admin', 1, '2026-07-08 06:20:52', NULL, '2025-10-05 07:33:26', '2026-07-08 06:20:52'),
('22470036-f3dc-45cb-b896-2f03dfd007b1', '4a189262-87df-48a6-8155-97fbe315e830', 'jersondev03@gmail.com', NULL, '$2y$12$UpzOqjgMBkCoXGrlmKGajOwXAy2b56Ms5YH8JRWIYOCuyn0.zJIBK', NULL, NULL, 'hr', 1, '2025-10-19 04:57:54', NULL, '2025-10-09 07:44:09', '2025-10-19 04:57:54'),
('25c3fd0a-81ba-49da-b8d1-5aa651526640', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', 'alex@gmail.com', NULL, '$2y$12$EW9uf2NI1Mu4fQK5aaDQy.3jCA0R5BlTSaT9YE.XoqGQH/2ojwUdy', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:37:41', '2025-10-05 07:37:41'),
('2a95e71a-c36d-49c9-b626-aad8784ff219', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', 'charlie@gmail.com', NULL, '$2y$12$EbuiItNU7/r5Tc.X3GW.6OB3NROGaSAXQTJ/axVnrd0E5.A.i12Wm', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:39:54', '2025-10-05 07:39:54'),
('384266c3-1888-47ed-b160-26db176eb639', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', 'Bulbasaur@gmail.com', NULL, '$2y$12$3ixECeCmeie9NuwfGGsxjuRx75wnkNK818RapTAqpDkK5ZEci748K', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 09:09:28', '2025-10-05 09:09:28'),
('634b2deb-8337-48b1-984e-099231e88a66', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', 'reece@gmail.com', NULL, '$2y$12$0FKRTtI5EftqVgQ9XHZwoOrPM2LkLksWdu4kxjiszO6PF2pRIoNCu', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:40:28', '2025-10-05 07:40:28'),
('847400e2-d2ef-4144-a0f2-d61bd030be11', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', 'lowegie@gmail.com', NULL, '$2y$12$itWmwPsYorGR1wqU1seaXeBlkHLBOiah2DDyTcE3R9Npa5kfJi4Gy', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:36:36', '2025-10-05 07:36:36'),
('9ef7954c-a2e0-4030-812f-898779f663ae', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', 'maria@gmail.com', NULL, '$2y$12$WSqBcovKm.cNsXBNY9wuUO6jJ6IhMTsKgPHyJnEH1oBkddaVum3by', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 09:08:31', '2025-10-05 09:08:31'),
('b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'curt@gmail.com', NULL, '$2y$12$xtoNBd2jfX/QZFbrou3HI.f/tMyeReOTs3T7o4f8EvARkHhc5V0um', NULL, NULL, 'employee', 1, '2026-07-08 06:17:34', NULL, '2025-10-05 07:39:11', '2026-07-08 06:17:34'),
('dd397e44-a108-4f0d-a808-659b5608c3da', '243e7606-9542-4c71-867c-05bea5e658d3', 'reyven@gmail.com', NULL, '$2y$12$jNoAoo03XPN7lwpj.EFK6OgTZGUR2tRPZHS2RvBwTyKny13XK5R26', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:41:03', '2025-10-05 07:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_exceptions`
--

CREATE TABLE `attendance_exceptions` (
  `id` char(36) NOT NULL,
  `date` date NOT NULL,
  `type` enum('holiday','company_event','emergency_closure','special_workday') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `is_working_day` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `action` enum('created','updated','deleted','approved','rejected','time_in','time_out','break_start','break_end') NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `performed_by` char(36) NOT NULL,
  `performed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_records`
--

CREATE TABLE `attendance_records` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `time_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `break_start` timestamp NULL DEFAULT NULL,
  `break_end` timestamp NULL DEFAULT NULL,
  `total_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `regular_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'absent',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `break_start`, `break_end`, `total_hours`, `regular_hours`, `overtime_hours`, `status`, `notes`, `created_at`, `updated_at`) VALUES
('dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-08', '2026-07-08 06:17:49', NULL, NULL, NULL, 0.00, 0.00, 0.00, 'present', NULL, '2026-07-08 06:17:49', '2026-07-08 06:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_settings`
--

CREATE TABLE `attendance_settings` (
  `id` char(36) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
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
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `tax_id` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `code`, `description`, `address`, `city`, `state`, `postal_code`, `country`, `phone`, `email`, `website`, `tax_id`, `registration_number`, `is_active`, `created_at`, `updated_at`) VALUES
('1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'Eternal Bright', 'EB1783420730', 'Eternal Bright Company', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-07 10:38:50', '2026-07-07 10:38:50');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `department_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `budget` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `company_id`, `department_id`, `name`, `description`, `location`, `budget`, `created_at`, `updated_at`) VALUES
('153a210b-1097-44e0-8287-e5311f2e722d', NULL, 'DEPT-0132', 'Finance', 'Manages financial planning, accounting, and budget oversight', 'Main Office - Floor 1', 3000000.00, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, 'DEPT-1355', 'Information Technology', 'Handles all IT infrastructure, software development, and technical support', 'Main Office - Floor 3', 3750000.00, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('2b1a7cb9-dcc3-44e8-8411-154290f7262f', NULL, 'DEPT-2847', 'Operations', 'Oversees daily business operations and process improvement', 'Main Office - Floor 1', 4000000.00, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('2bff599e-bbf1-4b39-9595-edaedb565299', NULL, 'DEPT-3472', 'Human Resources', 'Manages employee relations, recruitment, and HR policies', 'Main Office - Floor 2', 2500000.00, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('ac282586-cced-4d9e-b9a6-15609483eae7', NULL, 'DEPT-6117', 'Marketing', 'Responsible for brand management, advertising, and market research', 'Main Office - Floor 2', 2000000.00, '2026-07-07 12:33:49', '2026-07-07 12:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `employee_id` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `department_id` char(36) NOT NULL,
  `position_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL,
  `hire_date` date NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `linkedin_link` varchar(255) DEFAULT NULL,
  `ig_link` varchar(255) DEFAULT NULL,
  `other_link` varchar(255) DEFAULT NULL,
  `emergency_full_name` varchar(255) DEFAULT NULL,
  `emergency_relationship` varchar(255) DEFAULT NULL,
  `emergency_home_address` text DEFAULT NULL,
  `emergency_current_address` text DEFAULT NULL,
  `emergency_mobile_number` varchar(20) DEFAULT NULL,
  `emergency_email` varchar(255) DEFAULT NULL,
  `emergency_facebook_link` varchar(255) DEFAULT NULL,
  `loan_start_date` date DEFAULT NULL,
  `loan_end_date` date DEFAULT NULL,
  `loan_total_amount` decimal(12,2) DEFAULT NULL,
  `loan_monthly_amortization` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `company_id`, `employee_id`, `first_name`, `last_name`, `phone`, `department_id`, `position_id`, `created_by`, `salary`, `hire_date`, `date_of_birth`, `civil_status`, `home_address`, `current_address`, `mobile_number`, `facebook_link`, `linkedin_link`, `ig_link`, `other_link`, `emergency_full_name`, `emergency_relationship`, `emergency_home_address`, `emergency_current_address`, `emergency_mobile_number`, `emergency_email`, `emergency_facebook_link`, `loan_start_date`, `loan_end_date`, `loan_total_amount`, `loan_monthly_amortization`, `created_at`, `updated_at`) VALUES
('0c6fcadb-5d42-453e-b85e-04df54d4b42b', NULL, 'EMP-0005', 'Charlie', 'Cawile', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:39:54', '2025-10-05 07:39:54'),
('22ff1b14-2301-4a27-a022-6736b3c9f318', NULL, 'EMP-0001', 'Jerson Marg', 'Cerezo', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:33:26', '2025-10-05 07:33:26'),
('243e7606-9542-4c71-867c-05bea5e658d3', NULL, 'EMP-0007', 'Reyven', 'Plaza', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:41:02', '2025-10-05 07:41:02'),
('41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', NULL, 'EMP-0009', 'Bulbasaur', 'Poke', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 09:09:28', '2025-10-05 09:09:28'),
('4a189262-87df-48a6-8155-97fbe315e830', NULL, 'EMP-0010', 'Balingka', 'Kalat', '09', '2bff599e-bbf1-4b39-9595-edaedb565299', NULL, NULL, 20000.00, '2025-09-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-09 07:44:09', '2025-10-09 07:44:09'),
('75282d62-51f2-41a9-a5e3-6b4b6838a5f7', NULL, 'EMP-0003', 'Alexander', 'Estares', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:37:41', '2025-10-05 07:37:41'),
('b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', NULL, 'EMP-0002', 'Lowegie', 'Raga', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:36:36', '2025-10-05 07:36:36'),
('b5aecc58-69dc-4d2c-b435-d8f13a9416ef', NULL, 'EMP-0008', 'Maria', 'Sampalok', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 09:08:30', '2025-10-05 09:08:30'),
('b5b39e80-36cc-4f35-abaf-20272f5a461c', NULL, 'EMP-0004', 'Curt Vincent', 'Guiling', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:39:11', '2025-10-05 07:39:11'),
('c413f98e-d91b-4c61-b4d4-eb00e40a34b1', NULL, 'EMP-0006', 'Reece', 'Bibaro', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:40:28', '2025-10-05 07:40:28');

-- --------------------------------------------------------

--
-- Table structure for table `employee_breaks`
--

CREATE TABLE `employee_breaks` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `break_start` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `break_end` timestamp NULL DEFAULT NULL,
  `break_duration_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_other_infos`
--

CREATE TABLE `employee_other_infos` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `pov_address` varchar(255) DEFAULT NULL,
  `no_street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `town_district` varchar(255) DEFAULT NULL,
  `city_province` varchar(255) DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `blood_type` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `drivers_license` varchar(255) DEFAULT NULL,
  `prc_no` varchar(255) DEFAULT NULL,
  `father` varchar(255) DEFAULT NULL,
  `mother` varchar(255) DEFAULT NULL,
  `spouse` varchar(255) DEFAULT NULL,
  `spouse_employed` tinyint(1) NOT NULL DEFAULT 0,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_schedules`
--

CREATE TABLE `employee_schedules` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `department_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Working','Day Off','Leave','Holiday','Overtime') NOT NULL DEFAULT 'Working',
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
-- Table structure for table `hr_contacts`
--

CREATE TABLE `hr_contacts` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) DEFAULT NULL,
  `user_id` char(36) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `category` enum('attendance','leave','payroll','benefits','schedule','general','complaint','request') NOT NULL DEFAULT 'general',
  `status` enum('pending','in_progress','resolved','closed') NOT NULL DEFAULT 'pending',
  `response` longtext DEFAULT NULL,
  `responded_by` char(36) DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `year` int(11) NOT NULL,
  `vacation_days_total` int(11) NOT NULL DEFAULT 15,
  `vacation_days_used` int(11) NOT NULL DEFAULT 0,
  `sick_days_total` int(11) NOT NULL DEFAULT 10,
  `sick_days_used` int(11) NOT NULL DEFAULT 0,
  `personal_days_total` int(11) NOT NULL DEFAULT 5,
  `personal_days_used` int(11) NOT NULL DEFAULT 0,
  `emergency_days_total` int(11) NOT NULL DEFAULT 3,
  `emergency_days_used` int(11) NOT NULL DEFAULT 0,
  `maternity_days_total` int(11) NOT NULL DEFAULT 0,
  `maternity_days_used` int(11) NOT NULL DEFAULT 0,
  `paternity_days_total` int(11) NOT NULL DEFAULT 0,
  `paternity_days_used` int(11) NOT NULL DEFAULT 0,
  `bereavement_days_total` int(11) NOT NULL DEFAULT 0,
  `bereavement_days_used` int(11) NOT NULL DEFAULT 0,
  `study_days_total` int(11) NOT NULL DEFAULT 0,
  `study_days_used` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `leave_type` enum('vacation','sick','personal','emergency','maternity','paternity','bereavement','study') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `days_requested` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_09_18_145540_create_departments_table', 1),
(2, '2025_09_18_145542_create_accounts_table', 1),
(3, '2025_09_18_145543_create_positions_table', 1),
(4, '2025_09_18_145544_create_employees_table', 1),
(5, '2025_09_18_145545_add_employee_id_foreign_to_accounts_table', 1),
(6, '2025_09_18_145548_create_payrolls_table', 1),
(7, '2025_09_18_150533_create_sessions_table', 1),
(8, '2025_09_18_150601_create_cache_table', 1),
(9, '2025_09_18_150612_create_jobs_table', 1),
(10, '2025_09_18_150624_create_failed_jobs_table', 1),
(11, '2025_09_19_022316_create_attendance_records_table', 1),
(12, '2025_09_19_022330_create_overtime_requests_table', 1),
(13, '2025_09_19_022330_create_work_schedules_table', 1),
(14, '2025_09_19_022331_create_attendance_settings_table', 1),
(15, '2025_09_19_022331_create_leave_balances_table', 1),
(16, '2025_09_19_022331_create_leave_requests_table', 1),
(17, '2025_09_19_022346_create_attendance_exceptions_table', 1),
(18, '2025_09_19_022346_create_attendance_logs_table', 1),
(19, '2025_09_30_171709_create_employee_schedules_table', 1),
(20, '2025_10_18_014123_update_employee_schedules_holiday_status', 1),
(21, '2025_10_18_040316_add_night_shift_to_attendance_records_table', 1),
(22, '2025_10_19_221115_create_temp_timekeeping_table', 1),
(23, '2025_10_19_224709_create_companies_table', 1),
(24, '2025_10_19_225233_update_companies_table_use_uuid', 1),
(25, '2025_10_20_000939_add_approval_fields_to_payrolls_table', 1),
(26, '2025_10_20_011053_add_holiday_pay_fields_to_payrolls_table', 1),
(27, '2025_10_20_011838_add_special_holiday_premium_to_payrolls_table', 1),
(28, '2025_10_20_020910_add_scheduled_hours_to_payrolls_table', 1),
(29, '2025_10_20_034409_add_holiday_day_counts_to_payrolls_table', 1),
(30, '2025_10_20_074714_create_user_sessions_table', 1),
(31, '2025_10_20_083249_update_user_sessions_table_use_uuid', 1),
(32, '2025_10_24_005407_create_positions_table', 1),
(33, '2025_10_24_021817_create_periods_table', 1),
(34, '2025_10_24_023441_create_tax_brackets_table', 1),
(35, '2025_10_24_134745_update_attendance_records_status_enum', 1),
(36, '2025_10_24_135307_update_employee_schedules_status_enum_add_absent', 1),
(37, '2025_10_27_075937_add_company_id_to_departments_table', 1),
(38, '2025_10_27_075942_add_company_id_to_employees_table', 1),
(39, '2025_10_27_075950_add_company_id_to_positions_table', 1),
(40, '2025_10_27_080510_assign_existing_data_to_eternal_bright_company', 1),
(41, '2025_10_27_095544_add_password_reset_to_accounts_table', 1),
(42, '2025_11_29_010000_create_payments_table', 1),
(43, '2025_11_29_010001_add_employee_id_foreign_to_payments_table', 1),
(44, '2025_11_29_222408_update_payments_table_data_types', 1),
(45, '2025_12_01_142945_add_payment_method_to_payments_table', 1),
(46, '2025_12_01_143627_describe_payments_table', 1),
(47, '2025_12_02_154550_add_missing_payroll_columns', 1),
(48, '2025_12_04_170938_add_statutory_deduction_columns', 1),
(49, '2026_01_05_204451_create_breaks_table', 1),
(50, '2026_01_20_140700_update_payrolls_status_column', 1),
(51, '2026_01_20_141615_fix_payrolls_status_column_length', 1),
(52, '2026_01_20_142158_add_rejection_columns_to_payrolls_table', 1),
(53, '2026_01_21_130228_create_documents_table', 1),
(54, '2026_01_28_154754_create_login_logs_table', 1),
(55, '2026_02_02_100000_create_time_entries_table', 1),
(56, '2026_02_03_000000_create_hr_contacts_table', 1),
(57, '2026_02_20_000001_add_employee_detail_fields_to_employees_table', 1),
(58, '2026_02_25_000001_add_missing_columns_to_payments_table', 1),
(59, '2026_02_25_000002_add_foreign_key_processed_by_to_payments_table', 1),
(60, '2026_02_25_000003_convert_positions_id_to_uuid', 1),
(61, '2026_02_25_000004_add_missing_columns_to_payments_table', 1),
(62, '2026_02_25_000005_add_foreign_key_processed_by_to_payments_table', 1),
(63, '2026_03_02_000001_add_attendance_category_to_hr_contacts_enum', 1),
(64, '2026_03_03_000002_create_previous_employments_table', 1),
(65, '2026_03_04_000003_create_employee_other_infos_table', 1),
(66, '2026_05_08_161109_add_completed_to_attendance_records_status_enum', 1);

-- --------------------------------------------------------

--
-- Table structure for table `overtime_requests`
--

CREATE TABLE `overtime_requests` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `hours` decimal(8,2) NOT NULL,
  `rate_multiplier` decimal(3,2) NOT NULL DEFAULT 1.50,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` varchar(255) DEFAULT NULL,
  `employee_id` varchar(36) DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `pay_period_start` date NOT NULL,
  `pay_period_end` date NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `holiday_basic_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `holiday_premium` decimal(10,2) NOT NULL DEFAULT 0.00,
  `special_holiday_premium` decimal(10,2) NOT NULL DEFAULT 0.00,
  `regular_holiday_days` int(11) NOT NULL DEFAULT 0,
  `special_holiday_days` int(11) NOT NULL DEFAULT 0,
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overtime_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overtime_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `scheduled_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rest_day_premium_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sss` decimal(12,2) DEFAULT NULL,
  `phic` decimal(12,2) DEFAULT NULL,
  `hdmf` decimal(12,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `paid_by` varchar(36) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payslip_file` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_by` char(36) DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `department_id` char(36) DEFAULT NULL,
  `employee_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_ids`)),
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `department_id` char(36) NOT NULL,
  `min_salary` decimal(10,2) DEFAULT NULL,
  `max_salary` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requirements`)),
  `responsibilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`responsibilities`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `previous_employments`
--

CREATE TABLE `previous_employments` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `sequence` tinyint(3) UNSIGNED NOT NULL,
  `employment_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `start_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `start_year` smallint(5) UNSIGNED DEFAULT NULL,
  `end_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `end_year` smallint(5) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('5kVF21WW5kCWR5ErUqmMCFJqFpvt3765uW5vV7Bb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3N3VEh6OGRpMmxNckwwbnY1cnNkQzZSdkNyWnFFM25VUHdqQXhsdyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1783486079),
('iKauHfo01T54dkAHLQaKyyrDlxMMJy38WlB6fvc0', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoib3lMWExOWDRMMjh2U3NWVG1nbnJOWTY5Q0xYTE11QTgydERVZ0FlNiI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjM2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvaHIvaW5ib3gvcXVpY2siO3M6NToicm91dGUiO3M6MTQ6ImhyLmluYm94LnF1aWNrIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6IjFhMWQyYjEyLWQzYjUtNDM2ZC1hYTk0LTc2MmFiOGY1ZmJkNCI7czoxODoiY3VycmVudF9jb21wYW55X2lkIjtzOjM2OiJjNTc1MWIwNy0zNWM2LTRmMDYtOTQ0Mi01MWIzZmU4YjAzNDciO30=', 1783488094),
('OMWDELuwR7JeRfX63sREqOXlJ80ralkj8lyPnLiX', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNkMybjBSUjF6aVdGeURjRUNhM1FRSDBMcXZ5ekloSkY3N21BcFd1YiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oci9pbmJveC9xdWljayI7czo1OiJyb3V0ZSI7czoxNDoiaHIuaW5ib3gucXVpY2siO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czozNjoiMWExZDJiMTItZDNiNS00MzZkLWFhOTQtNzYyYWI4ZjVmYmQ0IjtzOjE4OiJjdXJyZW50X2NvbXBhbnlfaWQiO3M6MzY6ImM1NzUxYjA3LTM1YzYtNGYwNi05NDQyLTUxYjNmZThiMDM0NyI7fQ==', 1783492237),
('xhoqsplwnl9SxKKopmFyQygLuHQxTX9ry0OyTRSs', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVjRaUm80SXBKR3hReFZZZ0xoSEpCTkl2MnpRRGp0bWhCZjdXMHc1UyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO3M6MzY6ImI5MDE1YzhjLTM0OGYtNDc5My05OGIyLTVkM2FjM2RkMmFhOSI7czoxODoiY3VycmVudF9jb21wYW55X2lkIjtzOjM2OiJjNTc1MWIwNy0zNWM2LTRmMDYtOTQ0Mi01MWIzZmU4YjAzNDciO30=', 1783492063);

-- --------------------------------------------------------

--
-- Table structure for table `tax_brackets`
--

CREATE TABLE `tax_brackets` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `min_income` decimal(15,2) NOT NULL,
  `max_income` decimal(15,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  `base_tax` decimal(15,2) NOT NULL DEFAULT 0.00,
  `excess_over` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_from` date DEFAULT NULL,
  `effective_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_brackets`
--

INSERT INTO `tax_brackets` (`id`, `name`, `description`, `min_income`, `max_income`, `tax_rate`, `base_tax`, `excess_over`, `sort_order`, `is_active`, `effective_from`, `effective_until`, `created_at`, `updated_at`) VALUES
('497e0289-7ef0-4b3c-8c6a-db49086ebd47', '20% Bracket', '₱33,334 – ₱66,667 - ₱1,875 + 20% of the excess over ₱33,333', 33334.00, 66667.00, 20.00, 1875.00, 33333.00, 3, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23'),
('7e8745cc-59be-4c8d-a030-850f58a25ff4', 'Exempt', 'Up to ₱20,833 - Exempt from tax', 0.00, 20833.00, 0.00, 0.00, 0.00, 1, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23'),
('94dcbc14-3d77-4b4b-9f4a-991f396e5ac4', '15% Bracket', '₱20,834 – ₱33,333 - 15% of the excess over ₱20,833', 20834.00, 33333.00, 15.00, 0.00, 20833.00, 2, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23'),
('ae0c65e0-a81c-4a3f-b2b8-4b2bdf1fb869', '25% Bracket', '₱66,668 – ₱166,667 - ₱8,541.80 + 25% of the excess over ₱66,667', 66668.00, 166667.00, 25.00, 8541.80, 66667.00, 4, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23'),
('ba585b4b-f5e8-4e3d-8498-cbfa44667938', '30% Bracket', '₱166,668 – ₱666,667 - ₱33,541.80 + 30% of the excess over ₱166,667', 166668.00, 666667.00, 30.00, 33541.80, 166667.00, 5, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23'),
('c4228f8c-a3e5-47f1-98e7-3f535ec34b87', '35% Bracket', 'Over ₱666,667 - ₱183,541.80 + 35% of the excess over ₱666,667', 666668.00, NULL, 35.00, 183541.80, 666667.00, 6, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-07 11:14:23');

-- --------------------------------------------------------

--
-- Table structure for table `temp_timekeeping`
--

CREATE TABLE `temp_timekeeping` (
  `id` char(36) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `employee_name` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `time_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `break_start` timestamp NULL DEFAULT NULL,
  `break_end` timestamp NULL DEFAULT NULL,
  `total_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `regular_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overtime_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `status` enum('present','absent','late','half_day','on_leave','day_off','holiday','error') NOT NULL DEFAULT 'absent',
  `schedule_status` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `validation_errors` text DEFAULT NULL,
  `import_batch_id` varchar(255) DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_entries`
--

CREATE TABLE `time_entries` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `time_in` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `time_out` timestamp NULL DEFAULT NULL,
  `hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00,
  `entry_type` enum('regular','overtime','makeup') NOT NULL DEFAULT 'regular',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_entries`
--

INSERT INTO `time_entries` (`id`, `attendance_record_id`, `time_in`, `time_out`, `hours_worked`, `entry_type`, `notes`, `created_at`, `updated_at`) VALUES
('532afe2a-2417-4d35-beb3-0c65f52836ce', 'dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', '2026-07-08 06:17:49', NULL, 0.00, 'regular', NULL, '2026-07-08 06:17:49', '2026-07-08 06:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` char(36) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `device_type` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `last_activity` timestamp NULL DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `device_type`, `browser`, `os`, `location`, `is_current`, `last_activity`, `login_at`, `expires_at`, `created_at`, `updated_at`) VALUES
('111f668b-7297-4e88-bec1-53fbc8a9cf26', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Ef9p5grC2FsE7DsOO6aXHFUul3LFh4JRJUyuNSrA', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 21:32:13', NULL, '2026-07-07 23:32:13', '2026-07-07 21:31:46', '2026-07-07 21:32:13'),
('1b8e7f81-2743-4d61-86fa-6abd2c5bfb8b', '22470036-f3dc-45cb-b896-2f03dfd007b1', 'OvyoPUnVsnSjJJ85ROhZKabNDpP7OH9vIEiViNyt', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 11:44:19', NULL, '2026-07-07 13:44:19', '2026-07-07 11:07:55', '2026-07-07 11:44:19'),
('385aad5c-ca96-4813-8c2f-c7b0dd0015ce', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'UYbHVlfusYrsAPqxc8LoJsb38duz4I80U3wKKipD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 21:31:25', NULL, '2026-07-07 23:31:25', '2026-07-07 21:30:48', '2026-07-07 21:31:25'),
('571869b4-a730-456d-8534-a5d788a3d93a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'XpAXpYCclLt1J6pyxjUBNc0pfowYni8KspDhfkRM', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 12:51:05', NULL, '2026-07-07 14:51:05', '2026-07-07 12:50:34', '2026-07-07 12:51:05'),
('6af4f6b4-5a50-4e53-8779-19b4e88239db', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'LVeC3YAOa4mGeNLJNvS1BAQgJdI4nGai6eiXTWyh', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 03:25:20', NULL, '2026-07-08 05:25:20', '2026-07-08 02:43:15', '2026-07-08 03:25:20'),
('7d12fb63-6039-4741-81d6-6bb3dc260f51', '22470036-f3dc-45cb-b896-2f03dfd007b1', 'oPGhvOGInow9blb1sZKPgylLdTz4YzNqaAxWBFH9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 11:44:27', NULL, '2026-07-07 13:44:27', '2026-07-07 11:21:57', '2026-07-07 11:44:27'),
('8e7a8a5e-2638-4a53-ab02-1e492d3461c8', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'iKauHfo01T54dkAHLQaKyyrDlxMMJy38WlB6fvc0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 05:21:34', NULL, '2026-07-08 07:21:34', '2026-07-08 03:27:40', '2026-07-08 05:21:34'),
('a21fd02c-278c-4b97-bbb4-027a9d16266a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'heYcb3DJxVdeITtgAhDkDsHASKKR2lBoUANeZtRg', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 13:25:23', NULL, '2026-07-07 15:25:23', '2026-07-07 12:54:11', '2026-07-07 13:25:23'),
('a7245b27-9e9f-498b-9db1-bf2f466a8148', '22470036-f3dc-45cb-b896-2f03dfd007b1', 'cXJuC994CDnWruVRU8jUeBaEkQg6J7YQt6XP3ec6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 11:21:56', NULL, '2026-07-07 13:21:56', '2026-07-07 11:21:56', '2026-07-07 11:21:56'),
('b55e045a-432c-4dbf-a250-08083690d0c2', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'e0FSKv5GK05gQZznzRCfpE14nVumF6qHnhm0rNfo', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:07:55', NULL, '2026-07-08 08:07:55', '2026-07-08 05:54:00', '2026-07-08 06:07:55'),
('c2175868-462f-4585-bf08-7e41fd9f2e8a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '1MbLKMz88BTLRfPI9fDvLVuvhTzQkbONkzwdnMvB', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:08:45', NULL, '2026-07-08 08:08:45', '2026-07-08 06:08:43', '2026-07-08 06:08:45'),
('eccd81fe-0bf1-4910-a692-78909e832643', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'xhoqsplwnl9SxKKopmFyQygLuHQxTX9ry0OyTRSs', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:27:43', NULL, '2026-07-08 08:27:43', '2026-07-08 06:17:35', '2026-07-08 06:27:43'),
('f0439299-2715-4ba8-85a8-3cc360ede0f4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'OMWDELuwR7JeRfX63sREqOXlJ80ralkj8lyPnLiX', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:30:37', NULL, '2026-07-08 08:30:37', '2026-07-08 06:20:52', '2026-07-08 06:30:37');

-- --------------------------------------------------------

--
-- Table structure for table `work_schedules`
--

CREATE TABLE `work_schedules` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `schedule_name` varchar(255) NOT NULL,
  `monday_start` time DEFAULT NULL,
  `monday_end` time DEFAULT NULL,
  `tuesday_start` time DEFAULT NULL,
  `tuesday_end` time DEFAULT NULL,
  `wednesday_start` time DEFAULT NULL,
  `wednesday_end` time DEFAULT NULL,
  `thursday_start` time DEFAULT NULL,
  `thursday_end` time DEFAULT NULL,
  `friday_start` time DEFAULT NULL,
  `friday_end` time DEFAULT NULL,
  `saturday_start` time DEFAULT NULL,
  `saturday_end` time DEFAULT NULL,
  `sunday_start` time DEFAULT NULL,
  `sunday_end` time DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounts_email_unique` (`email`),
  ADD KEY `accounts_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `attendance_exceptions`
--
ALTER TABLE `attendance_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_exceptions_date_type_unique` (`date`,`type`),
  ADD KEY `attendance_exceptions_date_index` (`date`),
  ADD KEY `attendance_exceptions_type_index` (`type`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_logs_attendance_record_id_action_index` (`attendance_record_id`,`action`),
  ADD KEY `attendance_logs_performed_by_performed_at_index` (`performed_by`,`performed_at`);

--
-- Indexes for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_records_employee_id_date_unique` (`employee_id`,`date`),
  ADD KEY `attendance_records_date_status_index` (`date`,`status`);

--
-- Indexes for table `attendance_settings`
--
ALTER TABLE `attendance_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_settings_setting_key_unique` (`setting_key`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_code_unique` (`code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_department_id_unique` (`department_id`),
  ADD KEY `departments_company_id_index` (`company_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_employee_id_type_index` (`employee_id`,`type`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  ADD KEY `employees_department_id_foreign` (`department_id`),
  ADD KEY `employees_position_id_foreign` (`position_id`),
  ADD KEY `employees_created_by_foreign` (`created_by`),
  ADD KEY `employees_company_id_index` (`company_id`);

--
-- Indexes for table `employee_breaks`
--
ALTER TABLE `employee_breaks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_breaks_attendance_record_id_break_start_index` (`attendance_record_id`,`break_start`);

--
-- Indexes for table `employee_other_infos`
--
ALTER TABLE `employee_other_infos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_other_infos_employee_id_unique` (`employee_id`);

--
-- Indexes for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_schedules_employee_id_date_unique` (`employee_id`,`date`),
  ADD KEY `employee_schedules_created_by_foreign` (`created_by`),
  ADD KEY `employee_schedules_employee_id_date_index` (`employee_id`,`date`),
  ADD KEY `employee_schedules_department_id_date_index` (`department_id`,`date`),
  ADD KEY `employee_schedules_date_index` (`date`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hr_contacts`
--
ALTER TABLE `hr_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_contacts_responded_by_foreign` (`responded_by`),
  ADD KEY `hr_contacts_user_id_index` (`user_id`),
  ADD KEY `hr_contacts_employee_id_index` (`employee_id`),
  ADD KEY `hr_contacts_status_index` (`status`),
  ADD KEY `hr_contacts_category_index` (`category`),
  ADD KEY `hr_contacts_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `leave_balances_employee_id_year_unique` (`employee_id`,`year`),
  ADD KEY `leave_balances_year_index` (`year`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `leave_requests_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `leave_requests_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `leave_requests_leave_type_status_index` (`leave_type`,`status`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `overtime_requests_approved_by_foreign` (`approved_by`),
  ADD KEY `overtime_requests_employee_id_date_index` (`employee_id`,`date`),
  ADD KEY `overtime_requests_status_date_index` (`status`,`date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_payroll_id_index` (`payroll_id`),
  ADD KEY `payments_employee_id_index` (`employee_id`),
  ADD KEY `payments_processed_by_foreign` (`processed_by`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `payrolls_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `periods_department_id_foreign` (`department_id`),
  ADD KEY `periods_start_date_end_date_index` (`start_date`,`end_date`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `positions_name_unique` (`name`),
  ADD UNIQUE KEY `positions_code_unique` (`code`),
  ADD KEY `positions_department_id_foreign` (`department_id`),
  ADD KEY `positions_is_active_department_id_index` (`is_active`,`department_id`),
  ADD KEY `positions_level_index` (`level`),
  ADD KEY `positions_company_id_index` (`company_id`);

--
-- Indexes for table `previous_employments`
--
ALTER TABLE `previous_employments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `previous_employments_employee_id_sequence_unique` (`employee_id`,`sequence`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tax_brackets`
--
ALTER TABLE `tax_brackets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_brackets_is_active_sort_order_index` (`is_active`,`sort_order`),
  ADD KEY `tax_brackets_min_income_max_income_index` (`min_income`,`max_income`);

--
-- Indexes for table `temp_timekeeping`
--
ALTER TABLE `temp_timekeeping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `temp_timekeeping_employee_id_date_index` (`employee_id`,`date`),
  ADD KEY `temp_timekeeping_import_batch_id_index` (`import_batch_id`),
  ADD KEY `temp_timekeeping_is_processed_index` (`is_processed`);

--
-- Indexes for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_entries_attendance_record_id_time_in_index` (`attendance_record_id`,`time_in`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_sessions_session_id_unique` (`session_id`),
  ADD KEY `user_sessions_user_id_is_current_index` (`user_id`,`is_current`),
  ADD KEY `user_sessions_expires_at_index` (`expires_at`);

--
-- Indexes for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_schedules_employee_id_is_active_index` (`employee_id`,`is_active`),
  ADD KEY `work_schedules_effective_date_end_date_index` (`effective_date`,`end_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_records`
--
ALTER TABLE `attendance_records`
  ADD CONSTRAINT `attendance_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_breaks`
--
ALTER TABLE `employee_breaks`
  ADD CONSTRAINT `employee_breaks_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_other_infos`
--
ALTER TABLE `employee_other_infos`
  ADD CONSTRAINT `employee_other_infos_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_schedules`
--
ALTER TABLE `employee_schedules`
  ADD CONSTRAINT `employee_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_schedules_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_contacts`
--
ALTER TABLE `hr_contacts`
  ADD CONSTRAINT `hr_contacts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_contacts_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD CONSTRAINT `leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `overtime_requests`
--
ALTER TABLE `overtime_requests`
  ADD CONSTRAINT `overtime_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `overtime_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD CONSTRAINT `payrolls_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `previous_employments`
--
ALTER TABLE `previous_employments`
  ADD CONSTRAINT `previous_employments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_entries`
--
ALTER TABLE `time_entries`
  ADD CONSTRAINT `time_entries_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_schedules`
--
ALTER TABLE `work_schedules`
  ADD CONSTRAINT `work_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
