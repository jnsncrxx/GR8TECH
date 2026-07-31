-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 09:30 AM
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
  `google_id` varchar(255) DEFAULT NULL,
  `microsoft_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
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

INSERT INTO `accounts` (`id`, `employee_id`, `email`, `google_id`, `microsoft_id`, `avatar`, `email_verified_at`, `password`, `password_reset_token`, `password_reset_expires_at`, `role`, `is_active`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
('134402ed-fc04-4be7-a2c0-df8395f03a24', '62e12895-9295-4a40-a298-5b9903867cd7', 'taylor@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$U1OHO4BAJcXPk.Zng2tPC.gtocj/jVkEqwxem1eBkwOTIYSSISY/6', NULL, NULL, 'hr', 1, '2026-07-22 08:13:24', NULL, '2026-07-17 22:59:13', '2026-07-22 08:13:24'),
('1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '22ff1b14-2301-4a27-a022-6736b3c9f318', 'jerson.cerezo.100@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$sv9r3QP.MWvkTkH6vSamMenPRF5eBEd3po/W05ZAty3zlYcmx7oCa', NULL, NULL, 'admin', 1, '2026-07-23 05:51:42', NULL, '2025-10-05 07:33:26', '2026-07-23 05:51:42'),
('25c3fd0a-81ba-49da-b8d1-5aa651526640', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', 'alex@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$lszC00m2T1rnDQK3tuVbWenXVx2whNELKtms41o6/eM8yysOEwcm6', NULL, NULL, 'manager', 1, '2026-07-22 08:20:20', NULL, '2025-10-05 07:37:41', '2026-07-22 08:20:20'),
('2a95e71a-c36d-49c9-b626-aad8784ff219', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', 'charlie@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$EbuiItNU7/r5Tc.X3GW.6OB3NROGaSAXQTJ/axVnrd0E5.A.i12Wm', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:39:54', '2025-10-05 07:39:54'),
('384266c3-1888-47ed-b160-26db176eb639', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', 'Bulbasaur@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$3ixECeCmeie9NuwfGGsxjuRx75wnkNK818RapTAqpDkK5ZEci748K', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 09:09:28', '2025-10-05 09:09:28'),
('634b2deb-8337-48b1-984e-099231e88a66', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', 'reece@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$0FKRTtI5EftqVgQ9XHZwoOrPM2LkLksWdu4kxjiszO6PF2pRIoNCu', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:40:28', '2025-10-05 07:40:28'),
('7d966088-f3b6-4411-8034-7abe3ca89851', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 'test0001@example.com', NULL, NULL, NULL, '2026-07-18 19:06:08', '$2y$12$yQBkMfHXso3p/i5NrgpkCuSvRF1L21hPU.1HlWIczgDt4Vxq4/Gba', NULL, NULL, 'employee', 1, '2026-07-22 07:54:36', NULL, '2026-07-18 19:06:08', '2026-07-22 07:54:36'),
('847400e2-d2ef-4144-a0f2-d61bd030be11', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', 'lowegie@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$itWmwPsYorGR1wqU1seaXeBlkHLBOiah2DDyTcE3R9Npa5kfJi4Gy', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:36:36', '2025-10-05 07:36:36'),
('9ef7954c-a2e0-4030-812f-898779f663ae', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', 'maria@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$WSqBcovKm.cNsXBNY9wuUO6jJ6IhMTsKgPHyJnEH1oBkddaVum3by', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 09:08:31', '2025-10-05 09:08:31'),
('b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'curt@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$xtoNBd2jfX/QZFbrou3HI.f/tMyeReOTs3T7o4f8EvARkHhc5V0um', NULL, NULL, 'employee', 1, '2026-07-23 06:07:04', NULL, '2025-10-05 07:39:11', '2026-07-23 06:07:04'),
('dd397e44-a108-4f0d-a808-659b5608c3da', '243e7606-9542-4c71-867c-05bea5e658d3', 'reyven@gmail.com', NULL, NULL, NULL, NULL, '$2y$12$jNoAoo03XPN7lwpj.EFK6OgTZGUR2tRPZHS2RvBwTyKny13XK5R26', NULL, NULL, 'employee', 1, NULL, NULL, '2025-10-05 07:41:03', '2025-10-05 07:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_corrections`
--

CREATE TABLE `attendance_corrections` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `corrected_by` char(36) DEFAULT NULL,
  `reason` text NOT NULL,
  `original_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_values`)),
  `corrected_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`corrected_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_corrections`
--

INSERT INTO `attendance_corrections` (`id`, `attendance_record_id`, `corrected_by`, `reason`, `original_values`, `corrected_values`, `created_at`, `updated_at`) VALUES
('1c7a419c-e593-46e4-8f4e-b5fdee31dd7d', '8eb6cbcd-5247-455f-92a5-cd0ae483dc45', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'sample', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-18T19:07:34.000000Z\",\"time_out\":null,\"break_start\":null,\"break_end\":null,\"notes\":null}', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-19T00:07:00.000000Z\",\"time_out\":\"2026-07-19T09:00:00.000000Z\",\"break_start\":\"2026-07-19T04:00:00.000000Z\",\"break_end\":\"2026-07-19T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 08:43:34', '2026-07-22 08:43:34'),
('1cb24e5f-a056-4066-98c5-b19e713bb713', 'f83f2e84-95c4-409c-ae29-b15b7bc0531b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'EMPLOYEE PROVIDED PROOF DURING HIS SHIFT', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":null}', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 12:44:14', '2026-07-22 12:44:14'),
('5e997b28-734a-47cb-8d8c-5fcf27ee6fe5', 'dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'report time corrected', '{\"employee_id\":\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"date\":\"2026-07-07T16:00:00.000000Z\",\"status\":\"completed\",\"time_in\":\"2026-07-08T06:17:49.000000Z\",\"time_out\":\"2026-07-08T10:08:51.000000Z\",\"break_start\":null,\"break_end\":null,\"notes\":null}', '{\"employee_id\":\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"date\":\"2026-07-07T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-08T06:17:00.000000Z\",\"time_out\":\"2026-07-08T10:08:00.000000Z\",\"break_start\":\"2026-07-08T04:00:00.000000Z\",\"break_end\":\"2026-07-08T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 23:04:45', '2026-07-22 23:04:45'),
('86eaeceb-4a0f-4b16-807b-bf7ca552a707', '8eb6cbcd-5247-455f-92a5-cd0ae483dc45', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'test', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-19T00:07:00.000000Z\",\"time_out\":\"2026-07-19T09:00:00.000000Z\",\"break_start\":\"2026-07-19T04:00:00.000000Z\",\"break_end\":\"2026-07-19T05:00:00.000000Z\",\"notes\":null}', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"absent\",\"time_in\":\"2026-07-18T16:00:00.000000Z\",\"time_out\":\"2026-07-19T04:00:00.000000Z\",\"break_start\":\"2026-07-19T04:00:00.000000Z\",\"break_end\":\"2026-07-19T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 08:46:02', '2026-07-22 08:46:02'),
('9339c00b-3596-4e74-bb0e-e738e1db0654', 'd705b7b3-b9cd-43b9-8103-e308cd62f78c', NULL, 'Approved vacation leave retained for 2026-07-09; biometric attendance disregarded by user instruction. Raw time entries preserved.', '{\"status\":\"completed\",\"time_in\":\"2026-07-09T07:14:56.000000Z\",\"time_out\":\"2026-07-09T11:15:00.000000Z\",\"break_start\":null,\"break_end\":null,\"total_hours\":\"0.00\",\"regular_hours\":\"0.00\",\"overtime_hours\":\"0.00\",\"notes\":null}', '{\"status\":\"on_leave\",\"time_in\":null,\"time_out\":null,\"break_start\":null,\"break_end\":null,\"total_hours\":\"0.00\",\"regular_hours\":\"0.00\",\"overtime_hours\":\"0.00\",\"notes\":\"Approved vacation leave retained for 2026-07-09; biometric attendance disregarded by user instruction. Raw time entries preserved.\"}', '2026-07-22 03:43:04', '2026-07-22 03:43:04'),
('b61279e1-f99f-40d6-a47f-31cf8c3323bd', '8eb6cbcd-5247-455f-92a5-cd0ae483dc45', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'test', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-19T00:07:00.000000Z\",\"time_out\":\"2026-07-19T09:00:00.000000Z\",\"break_start\":\"2026-07-19T04:00:00.000000Z\",\"break_end\":\"2026-07-19T05:00:00.000000Z\",\"notes\":null}', '{\"employee_id\":\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\",\"date\":\"2026-07-18T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-07-19T00:07:00.000000Z\",\"time_out\":\"2026-07-19T09:00:00.000000Z\",\"break_start\":\"2026-07-19T04:00:00.000000Z\",\"break_end\":\"2026-07-19T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 08:44:24', '2026-07-22 08:44:24'),
('bbc2dbc2-280c-4e43-b164-a4db5d12f6da', 'f83f2e84-95c4-409c-ae29-b15b7bc0531b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Employee reported at during day-off, he submitted a photo as a proof.', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":null}', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 09:15:21', '2026-07-22 09:15:21'),
('fd71be83-1997-429c-a519-466b966aa981', 'f83f2e84-95c4-409c-ae29-b15b7bc0531b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Employee actually reported during his day off.', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":\"Cutoff mock attendance for E2E payroll testing\"}', '{\"employee_id\":\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"date\":\"2026-08-01T16:00:00.000000Z\",\"status\":\"present\",\"time_in\":\"2026-08-02T00:00:00.000000Z\",\"time_out\":\"2026-08-02T09:00:00.000000Z\",\"break_start\":\"2026-08-02T04:00:00.000000Z\",\"break_end\":\"2026-08-02T05:00:00.000000Z\",\"notes\":null}', '2026-07-22 09:11:31', '2026-07-22 09:11:31');

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
  `corrected_by` char(36) DEFAULT NULL,
  `correction_reason` text DEFAULT NULL,
  `corrected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_records`
--

INSERT INTO `attendance_records` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `break_start`, `break_end`, `total_hours`, `regular_hours`, `overtime_hours`, `status`, `notes`, `corrected_by`, `correction_reason`, `corrected_at`, `created_at`, `updated_at`) VALUES
('006530c8-efef-48f4-b198-6785443c8493', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 11:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 10.00, 8.00, 2.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('00c4627a-c40c-4ebc-b265-61d760b397eb', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0205c2a0-a098-4ce3-89f1-3934ba1a8f0a', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('038ed48b-6e87-41f6-800d-9997e39e2e06', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('057106fb-7e45-4b27-b5fd-c634123e36dd', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('084c9407-a0d9-470c-927a-2c69723e6052', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-12', '2026-07-12 00:00:00', '2026-07-12 09:00:00', '2026-07-12 04:00:00', '2026-07-12 05:00:00', 8.00, 8.00, 0.00, 'official_business', 'test', NULL, NULL, NULL, '2026-07-12 06:22:10', '2026-07-12 06:22:10'),
('0944ce71-a100-45b9-8394-831ae0cfe46a', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0976041e-3fba-4ac7-87a6-4321f08972e7', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('09891e8d-84ce-4cca-98d2-3e863ed38b47', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0d4f8d1c-0149-4b8a-aec3-43dab9ed2ded', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0fece521-1120-4e59-b33a-0855f1c45b7d', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('10719efa-4e92-46f3-923e-eaaaef11428f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('122d3f82-05c6-4d58-8992-85a4ccb14cf9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-18', '2026-07-17 20:31:21', NULL, NULL, NULL, 2.77, 2.77, 0.00, 'completed', NULL, NULL, NULL, NULL, '2026-07-17 20:31:21', '2026-07-18 00:08:15'),
('13c82375-1d69-4ef4-8302-eca5ce7803f0', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1434b406-8013-450e-a3c5-3c9cac276da0', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1521afc9-84c8-42d7-a48b-58bdf3f7aa15', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('15d7fddc-2050-487c-b86e-071df3dc2b38', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 08:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 7.00, 7.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('16025aa7-5fe5-42e2-8905-ec90e6fce2fb', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('189d2122-03cf-488e-961a-7c3247a4b586', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1a1e8d99-f56f-4c37-83de-97a777087e99', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-10-02', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: test', NULL, NULL, NULL, '2026-07-17 01:31:01', '2026-07-17 01:31:01'),
('1b47ca33-fb98-41a3-8e02-f0064982f400', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1da24a20-6cf3-4336-bfa6-e1d49ab555c9', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1dd3cd25-7782-4cfe-8d9b-5369aa93778e', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-16', '2026-07-14 00:00:00', '2026-07-16 14:23:55', NULL, NULL, 14.38, 8.00, 6.38, 'completed', 'sample', NULL, NULL, NULL, '2026-07-14 07:08:35', '2026-07-16 14:23:55'),
('1e8101db-ab60-40c7-9757-bcf461891730', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1efa770f-7b51-4d98-b750-ed8574d450b6', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1f815b93-eef1-423e-a5f4-4d94d55637f3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2130956e-c9a5-43c6-91fc-a1b21fd20ae5', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-12', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Vacation Leave: sample', NULL, NULL, NULL, '2026-07-19 14:09:50', '2026-07-19 14:09:50'),
('22e07ea6-82b9-44fb-8c7b-d82bc2e5a757', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('297c69c6-8e64-4165-a1b9-3d2cf56f829c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2a97b2ce-ca76-4a7f-a18e-37dc0925c7e2', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2b655850-9401-4c3c-bb7f-be2fc0821891', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2c883aa3-b8d9-4825-915a-e788f710b5df', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2ced9314-f163-4c26-a7bc-2e2218d6d098', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2d6c014a-78d2-4474-b6e9-c52ad2c13afa', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2d7fe04a-f607-4568-a9b2-4fd6736b7ead', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('30ea7678-01cc-40f5-a6a4-373a37e7314c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-25', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Sick Leave: sample', NULL, NULL, NULL, '2026-07-17 01:49:52', '2026-07-17 01:49:52'),
('3250af21-a446-45df-839c-fb32e485e7a9', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('32a5ffc8-41cb-407f-8c44-4665d3e04a45', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3429d7fb-86a5-44d6-a86d-26f594bc8839', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('34ae15cf-79f1-4e2c-9f12-5d36f4f40e10', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3539bb32-587d-4d77-9ec5-54d6d653b32b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('392bc219-b0de-4805-ac25-158d3460e1b4', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3b71c751-ca49-42be-bc45-a1740df14aa6', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3e194fc5-fa9d-4451-a27b-4141d7db7270', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-14', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: test unpaid leave', NULL, NULL, NULL, '2026-07-19 22:57:28', '2026-07-19 22:57:28'),
('41262b2a-28de-4622-9b5a-5142259d0e6b', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4359e014-5a11-4e32-b3af-6374252cb309', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-20', '2026-07-20 00:00:00', '2026-07-20 10:54:37', '2026-07-20 04:00:00', '2026-07-20 05:00:00', 10.90, 8.00, 2.90, 'completed', 'TEST DAY 1', NULL, NULL, NULL, '2026-07-18 18:48:26', '2026-07-20 10:54:37'),
('43a3d0ce-1786-4557-a934-af5392ba1563', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4405730e-68fc-447c-8277-f0792a405670', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'official_business', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('44dfdfcb-e932-4c50-b3ac-d61e87b15094', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4537b616-4730-49b0-a8cf-ef4e5f73560d', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('46274cf1-5466-4f5d-b8ec-fa5916c9ba77', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-17', '2026-07-11 23:00:00', NULL, NULL, NULL, 9.33, 8.00, 0.00, 'completed', 'testing overtime split', NULL, NULL, NULL, '2026-07-12 00:41:57', '2026-07-17 04:31:00'),
('46d7fa3b-7183-4411-a237-0d2d1691f61b', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-22', NULL, NULL, NULL, NULL, 8.00, 8.00, 0.00, 'on_leave', 'Approved Vacation Leave: TEST\r\nApproved OB: testing notif (8.00 hrs)', NULL, NULL, NULL, '2026-07-19 15:32:27', '2026-07-19 20:20:06'),
('486f0d5e-6dfd-444c-ac78-b1c21eb6af6a', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('48e89d21-94c5-4119-a7e1-d9b0f5744cfe', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-11', '2026-07-11 00:20:00', '2026-07-11 08:30:00', NULL, NULL, 9.00, 8.00, 1.00, 'official_business', 'Retroactive and advance filing validation. TESTA', NULL, NULL, NULL, '2026-07-14 09:27:13', '2026-07-15 09:21:03'),
('49869c59-c834-4db1-a4f3-0f1f18330d56', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4b453ab0-0428-45ce-a604-097623154644', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4d30f5f3-0a08-4885-a2da-4a535db22bbd', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4fa01ec0-766d-4c72-8de6-23e1e80b5098', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('535d48ce-08ab-4f05-bc13-7bafd47ccefe', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('548c96ff-9cc2-43be-a78a-c2396a1f5a8e', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('55721982-8798-4f5a-aabf-4d1300eea0e2', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('561d33ee-464a-47be-b370-3f8fa7f94267', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-25', '2026-07-25 00:00:00', '2026-07-25 01:00:00', '2026-07-25 04:00:00', '2026-07-25 05:00:00', 0.00, 0.00, 0.00, 'present', 'TEST', NULL, NULL, NULL, '2026-07-18 21:38:23', '2026-07-18 21:38:23'),
('564f8127-306f-4292-a207-a2d810f19e7d', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5682a6ac-8458-4fa0-90e1-5e417f97d84e', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('57280f87-baec-406f-a298-491db0d99446', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5918a938-911b-4840-91b0-aeb64524f140', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5ad49372-fef4-4ab9-974b-d7ffacbbbce7', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5b1cea61-ec48-4d79-94c8-3aae85de2a12', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5d318960-f963-47be-84c0-96969877f523', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5e13fb1b-6e1f-465e-a383-ddf834a438c6', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5ffef67a-12ef-4d62-91ed-a729ec034a33', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('60e57548-f363-4b76-959c-59643f9533ee', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-08', NULL, NULL, NULL, NULL, 8.00, 8.00, 0.00, 'official_business', 'Out of office meeting\r\nLegacy manual OB attendance repaired and linked during payroll integrity audit.', NULL, NULL, NULL, '2026-07-08 08:21:12', '2026-07-22 03:36:23'),
('6294840f-64c3-4396-acad-a099368e9cd2', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-10', '2026-07-10 05:27:25', NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'test2\r\nAutomatically rejected during payroll integrity correction: date is covered by approved vacation leave (2026-07-09 to 2026-07-12).', NULL, NULL, NULL, '2026-07-08 12:50:29', '2026-07-22 03:33:17'),
('655d42dd-28c6-4936-9eaf-69aa83080e1c', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-29', '2026-07-29 00:20:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 7.67, 7.67, 0.00, 'late', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('658c1496-3497-4faa-990c-0e198b28e8e8', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('65954bad-c4f4-4427-9b0b-ed50fb37c2c7', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('65a7d966-5628-4246-a0c2-ae31887517f2', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6874379d-8a39-4bfd-b9e1-a654cc934fcc', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-10-05', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: test', NULL, NULL, NULL, '2026-07-17 01:31:01', '2026-07-17 01:31:01'),
('6b832702-4e4e-461a-ad53-fd05f808b6b3', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6c1a7ea5-6a6d-4dd1-9a9f-c8b532f8bd43', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6ffe4584-a111-444f-96df-8ecf79ff3617', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7278f587-534c-4133-add4-18190dac0e49', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('755e2ed5-ba29-4b67-ae5a-5c2910d4407b', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('77ece1e5-51c9-4bc6-9573-86d92ef4a48d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7c302c22-6d72-47e0-9bc1-b0dd5658525e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7c9acd1f-ff8e-422c-8f2d-8154f947f7fe', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7f7d5567-200f-4f9f-a954-f30e092114ee', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-12', '2026-07-11 21:31:25', '2026-07-11 23:37:34', NULL, NULL, 0.00, 0.00, 0.00, 'completed', NULL, NULL, NULL, NULL, '2026-07-11 21:31:25', '2026-07-11 23:37:35'),
('7fb8b968-c5bb-49f1-a6da-ffef16e06550', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8160aaaf-1914-497e-b5d0-520c75f1e70b', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('84c865b6-4c4f-42f4-bdcd-87fe89a3ebc6', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8588c943-2d21-4803-b143-fd4877fda125', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('86129c29-679c-4680-a9f2-d898f69147c8', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8889f2ca-9559-4193-b15c-cba691cd5aae', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('88bee926-ba63-41f7-9862-7ddcb7784460', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8b6908b0-21c5-43b8-9abc-f670b8db57f1', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8bba5526-3025-4ad3-b504-c055c548a3d0', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8d1577a2-2ec4-405c-b769-89d83d273c6f', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8e3cd314-4e58-45e4-b0d1-9eeb5c31fec8', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-16', '2026-07-16 02:33:00', '2026-07-16 11:33:00', NULL, NULL, 9.00, 8.00, 1.00, 'official_business', 'TEST', NULL, NULL, NULL, '2026-07-12 02:34:14', '2026-07-12 02:34:14'),
('8eb6cbcd-5247-455f-92a5-cd0ae483dc45', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-19', '2026-07-18 16:00:00', '2026-07-19 04:00:00', '2026-07-19 04:00:00', '2026-07-19 05:00:00', 11.00, 8.00, 3.00, 'absent', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'test', '2026-07-22 08:46:02', '2026-07-18 19:07:34', '2026-07-22 08:46:02'),
('8ee93749-6b11-471a-8ef7-3bbecbd0826f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-15', '2026-07-12 00:04:00', '2026-07-12 09:52:00', NULL, NULL, 9.80, 9.80, 0.00, 'official_business', 'end to end test', NULL, NULL, NULL, '2026-07-11 21:54:27', '2026-07-11 21:54:27'),
('8f21e40b-c4cf-4b9d-8377-9612deb2aca0', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-20', '2026-07-14 00:00:00', '2026-07-20 10:15:02', NULL, NULL, 10.25, 8.00, 2.25, 'completed', 'Retroactive and advance filing validation. TEST B', NULL, NULL, NULL, '2026-07-14 09:23:17', '2026-07-20 10:15:02'),
('9104afa6-5b5e-4935-aff6-5f2cd90dc5a1', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('93f4226c-9d2b-4d05-b8e5-18e74a526d76', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('95208808-ff8f-4233-bfc9-ea5de130c1f2', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-17', '2026-07-17 00:00:00', '2026-07-17 09:00:00', '2026-07-17 04:00:00', '2026-07-17 05:00:00', 8.00, 8.00, 0.00, 'official_business', 'sample', NULL, NULL, NULL, '2026-07-17 04:19:52', '2026-07-17 04:19:52'),
('95ee8878-d542-4c71-9d9f-4e739648e529', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('97706adc-6ae2-4a07-b1e5-09b00ad71dd4', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('98c1cb1a-f794-4fcb-bb41-cf8e5ae766a9', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('98c2239b-86a9-4148-96ac-b5b4cd3e170e', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('98e5d76c-b5b2-4aa8-ad70-27816b371a61', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('99261c13-86c4-4b49-a145-6c5c8bc60863', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('99638638-7f6b-441a-a3ab-6476c58c9414', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9a880f25-335a-47a5-a54e-9ac5d20588cc', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9a91f296-ab2d-4620-bd73-722b503fc7fd', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9b1345f2-18b4-4004-8900-76af8808ac33', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-03', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9c6c588b-0790-4640-867d-a90a711b6a4b', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9c89fe38-3eb2-45b5-807e-e79a27479b04', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9d8aa79b-20dc-4ffd-808c-8855f07e57ca', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9fb8efc5-c24b-4e80-8029-412f76d43fb5', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a39e40c4-28ff-4400-8cf8-c8d0cdeb3e0d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a448a34b-844d-48c6-911b-f6b7a2e66832', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a527398b-083b-4b69-92d8-a7fccafaa667', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-13', '2026-07-12 19:20:02', '2026-07-13 05:34:46', NULL, NULL, 13.65, 8.00, 2.23, 'completed', 'Approved OB: sample (8.00 hrs)', NULL, NULL, NULL, '2026-07-12 19:20:01', '2026-07-16 02:12:23'),
('a6bfaec4-686f-4e4b-9090-ea69d8f47643', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a7bd863f-1468-463c-917c-7364b95f2183', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-21', '2026-07-21 05:02:27', NULL, NULL, NULL, 8.00, 8.00, 0.00, 'present', 'Approved OB: a (8.00 hrs)', NULL, NULL, NULL, '2026-07-16 01:57:41', '2026-07-21 05:02:27'),
('a81dcca0-6b73-4530-bc24-2d5db41d0c8e', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-08', '2026-08-08 00:00:00', '2026-08-08 09:00:00', '2026-08-08 04:00:00', '2026-08-08 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a825926b-9def-4529-8311-0fa4d14d5f24', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('aa6e4b0f-4779-44e9-898b-b4003d002934', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ae1149ee-4f68-46e5-95e4-1bd236ca77fe', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ae464fa5-a2ff-4a06-a01e-85bde4b2206f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('af36527c-a4f0-47b4-802f-a0274421b581', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('af3ea14d-70b2-413f-a48a-954e4ff707b7', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b0f00028-9bd4-4fbb-9af9-12c42e575b86', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b23714f5-27e2-47f6-b03c-d4efa280ad07', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b2899356-09aa-4d17-a229-31042b8bc6b9', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b369d1ec-f73c-45d7-a71d-fc8c4950aec4', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-18', '2026-07-18 00:30:00', '2026-07-18 09:00:00', '2026-07-18 04:00:00', '2026-07-18 05:00:00', 7.50, 7.50, 0.00, 'late', NULL, NULL, NULL, NULL, '2026-07-18 01:37:51', '2026-07-18 01:37:52'),
('b5f4b313-8271-4e84-9bda-15720c63f2fd', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-10-03', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: test', NULL, NULL, NULL, '2026-07-17 01:31:01', '2026-07-17 01:31:01'),
('b84157d0-5d49-4880-bef5-a629ed4bf4d3', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b946e64a-521d-40e0-92a3-7ebdced29fd1', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-22', '2026-07-22 06:53:39', NULL, NULL, NULL, 13.68, 8.00, 0.00, 'completed', 'Approved OB: sample (8.00 hrs)', NULL, NULL, NULL, '2026-07-18 13:44:22', '2026-07-22 14:42:45'),
('b97e5c9e-8992-49c5-8c85-cdab145f5ddb', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c0042857-ffef-41ab-a9e3-bdd7c9973d36', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c2455c47-b11f-477c-8323-28fcc2417b4b', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c6ce80fa-7d1f-4e8b-83f3-cc66e5fe6733', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-15', '2026-07-15 00:00:00', '2026-07-15 09:00:00', '2026-07-15 04:00:00', '2026-07-15 05:00:00', 8.00, 8.00, 0.00, 'present', NULL, NULL, NULL, NULL, '2026-07-15 09:37:10', '2026-07-15 09:37:10'),
('c8bc090f-3a8a-4716-9ce7-1d565aee707b', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c8cda86e-166b-480f-b77d-87ac85aee520', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cb8bba4a-537a-4620-991d-32f119f2007f', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cd2ae045-0bee-458f-b3ee-d839be610bdb', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-21', '2026-07-21 00:30:00', '2026-07-21 09:00:00', '2026-07-21 04:00:00', '2026-07-21 05:00:00', 9.00, 8.00, 0.50, 'late', 'TEST DAY 2\r\nApproved OB: test (8.00 hrs)', NULL, NULL, NULL, '2026-07-18 18:50:09', '2026-07-19 19:00:03'),
('ce61c00b-94f4-4aeb-bc84-2c7608b2c1b5', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ce6346be-7a68-4c0c-bf34-ae144c13f3d8', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cfc3fb14-ca6d-4f7e-a19f-21b4633310b1', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('d0745e72-918c-453e-9e07-03117966a0a9', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('d2ce2a0b-a750-4fa3-924a-02997c924057', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-24', '2026-07-24 00:00:00', '2026-07-24 09:00:00', '2026-07-24 04:00:00', '2026-07-24 05:00:00', 8.00, 8.00, 0.00, 'official_business', 'TEST OB', NULL, NULL, NULL, '2026-07-18 19:20:32', '2026-07-18 19:20:32'),
('d323eb80-f14e-43df-80db-1949fda93282', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-19', '2026-07-18 18:51:58', '2026-07-19 15:26:58', NULL, NULL, 20.58, 8.00, 12.58, 'completed', NULL, NULL, NULL, NULL, '2026-07-18 18:51:58', '2026-07-19 15:26:58'),
('d705b7b3-b9cd-43b9-8103-e308cd62f78c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-09', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved vacation leave retained for 2026-07-09; biometric attendance disregarded by user instruction. Raw time entries preserved.', NULL, 'Approved vacation leave retained for 2026-07-09; biometric attendance disregarded by user instruction. Raw time entries preserved.', '2026-07-22 03:43:04', '2026-07-09 07:14:56', '2026-07-22 03:43:04'),
('daa0d310-e351-4b35-a4d8-03ca037bbcb5', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('db1fcd14-f24b-4780-b2e3-9eb731f3ab84', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: test unpaid leave', NULL, NULL, NULL, '2026-07-19 22:57:27', '2026-07-19 22:57:27'),
('dc403d56-afaf-4a9f-bf56-e16ee181affe', '62e12895-9295-4a40-a298-5b9903867cd7', '2026-08-05', '2026-08-05 00:00:00', '2026-08-05 09:00:00', '2026-08-05 04:00:00', '2026-08-05 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30');
INSERT INTO `attendance_records` (`id`, `employee_id`, `date`, `time_in`, `time_out`, `break_start`, `break_end`, `total_hours`, `regular_hours`, `overtime_hours`, `status`, `notes`, `corrected_by`, `correction_reason`, `corrected_at`, `created_at`, `updated_at`) VALUES
('de9085f4-3773-4ddc-9a66-1467be4dd630', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-23', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Personal Leave: TEST LWOP DEDUCTION', NULL, NULL, NULL, '2026-07-19 15:46:19', '2026-07-19 15:46:19'),
('df58aae3-53e3-4e52-9e79-6268ccb232ab', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('df6ff3d2-538b-4151-a8bc-7a5314284429', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-08', '2026-07-08 06:17:00', '2026-07-08 10:08:00', '2026-07-08 04:00:00', '2026-07-08 05:00:00', 2.85, 2.85, 0.00, 'present', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'report time corrected', '2026-07-22 23:04:45', '2026-07-08 06:17:49', '2026-07-22 23:04:45'),
('e08b14cf-33eb-47d4-aa10-f359c3eeac13', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-07', '2026-08-07 00:00:00', '2026-08-07 09:00:00', '2026-08-07 04:00:00', '2026-08-07 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e0bb7b95-65c6-481f-b94c-2870947f3a42', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-07-27', '2026-07-27 00:00:00', '2026-07-27 09:00:00', '2026-07-27 04:00:00', '2026-07-27 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e346c5e9-6efa-4e41-83f4-3a36d5ec268e', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e62c87e8-07e3-4e48-b113-e2837550fd6b', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e66afe0b-b4ff-45b8-b2c8-dca5f29830d5', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e6acee4b-f313-46e8-9175-0de1ab2798f6', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-07-28', '2026-07-28 00:00:00', '2026-07-28 09:00:00', '2026-07-28 04:00:00', '2026-07-28 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ee20aeb5-d2aa-4a2d-b840-243a4f051a13', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ef539960-c61a-40fe-a42b-5b827206aa23', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f188cc6d-e6f7-418e-aee6-b337c68cd9d0', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-08-04', '2026-08-04 00:00:00', '2026-08-04 09:00:00', '2026-08-04 04:00:00', '2026-08-04 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f1dc2661-687c-46c2-85df-7efd7fbb89e1', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-18', '2026-07-18 00:00:00', '2026-07-18 09:00:00', '2026-07-18 04:00:00', '2026-07-18 05:00:00', 8.00, 8.00, 0.00, 'official_business', 'sa', NULL, NULL, NULL, '2026-07-18 01:29:28', '2026-07-18 01:29:28'),
('f2dde9da-5753-4fe4-a9b9-da27280a570c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f3c22656-8157-47d5-96dd-fe6b58207597', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '2026-08-01', '2026-08-01 00:00:00', '2026-08-01 09:00:00', '2026-08-01 04:00:00', '2026-08-01 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f4fdf445-6d99-4ca1-86dd-af9ea5231a37', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-30', '2026-07-30 00:00:00', '2026-07-30 09:00:00', '2026-07-30 04:00:00', '2026-07-30 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f6849ef6-9219-4ff5-aa94-08b488f7ea81', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-08-03', '2026-08-03 00:00:00', '2026-08-03 09:00:00', '2026-08-03 04:00:00', '2026-08-03 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f6e9e8c8-b682-41bf-911d-aaa26390d060', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-07-29', '2026-07-29 00:00:00', '2026-07-29 09:00:00', '2026-07-29 04:00:00', '2026-07-29 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f83f2e84-95c4-409c-ae29-b15b7bc0531b', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-02', '2026-08-02 00:00:00', '2026-08-02 09:00:00', '2026-08-02 04:00:00', '2026-08-02 05:00:00', 8.00, 8.00, 0.00, 'present', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'EMPLOYEE PROVIDED PROOF DURING HIS SHIFT', '2026-07-22 12:44:14', '2026-07-22 08:58:30', '2026-07-22 12:44:14'),
('f906065e-8b00-4ede-ae9b-ccf14c8fe733', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-23', '2026-07-22 22:56:55', NULL, NULL, NULL, 0.00, 0.00, 0.00, 'present', NULL, NULL, NULL, NULL, '2026-07-22 22:56:55', '2026-07-22 22:56:55'),
('fa87740a-45bc-4440-b5cf-51303b6f6cb0', '243e7606-9542-4c71-867c-05bea5e658d3', '2026-08-06', '2026-08-06 00:00:00', '2026-08-06 09:00:00', '2026-08-06 04:00:00', '2026-08-06 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('fb3b3c12-2a3f-4665-a7de-3723c072c077', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-11', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'on_leave', 'Approved Vacation Leave: sample', NULL, NULL, NULL, '2026-07-19 14:09:50', '2026-07-19 14:09:50'),
('fb700dd7-2ac5-4cc2-8c51-42730ea77de1', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-14', '2026-07-14 05:33:52', NULL, NULL, NULL, 0.00, 0.00, 0.00, 'present', NULL, NULL, NULL, NULL, '2026-07-14 05:33:52', '2026-07-14 05:33:52'),
('fb900bed-2099-4117-aa13-78a0c1928315', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-08-10', '2026-08-10 00:00:00', '2026-08-10 09:00:00', '2026-08-10 04:00:00', '2026-08-10 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('fde61766-1106-4f4b-a401-08673790e340', '22ff1b14-2301-4a27-a022-6736b3c9f318', '2026-07-31', '2026-07-31 00:00:00', '2026-07-31 09:00:00', '2026-07-31 04:00:00', '2026-07-31 05:00:00', 8.00, 8.00, 0.00, 'present', 'Cutoff mock attendance for E2E payroll testing', NULL, NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30');

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

--
-- Dumping data for table `attendance_settings`
--

INSERT INTO `attendance_settings` (`id`, `setting_key`, `setting_value`, `description`, `created_at`, `updated_at`) VALUES
('07bcc906-1d93-4398-bf3c-460f40d728a7', 'max_overtime_hours', '4', 'Maximum overtime hours allowed per day', '2026-07-18 20:45:44', '2026-07-18 20:45:44'),
('0f56ee3a-fa26-4360-b3d8-69f4bbb19aa8', 'overtime_rate_multiplier', '1.2', 'Rate multiplier for overtime hours (e.g., 1.5 = 150%)', '2026-07-18 20:45:44', '2026-07-21 05:14:53'),
('aeb25815-440f-4258-9cd2-10b5fc6aa9a7', 'auto_calculate_overtime', '0', 'Automatically calculate overtime hours', '2026-07-18 20:45:44', '2026-07-18 20:45:44'),
('f3881479-a4c7-43e9-98ca-613b3e2734c8', 'require_overtime_approval', '0', 'Overtime must be approved by supervisor', '2026-07-18 20:45:44', '2026-07-18 20:45:44');

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
  `cutoff_day_1` tinyint(3) UNSIGNED NOT NULL DEFAULT 10,
  `cutoff_day_2` tinyint(3) UNSIGNED NOT NULL DEFAULT 25,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `code`, `description`, `address`, `city`, `state`, `postal_code`, `country`, `phone`, `email`, `website`, `tax_id`, `registration_number`, `is_active`, `cutoff_day_1`, `cutoff_day_2`, `created_at`, `updated_at`) VALUES
('1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'GR8 TECH ENTERPRISE INC.', 'GR8TECH', 'Primary company for the GR8TECH HRIS and Payroll System', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 10, 25, '2026-07-07 10:38:50', '2026-07-22 00:04:18');

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
  `archived_at` timestamp NULL DEFAULT NULL,
  `supervisor_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `company_id`, `department_id`, `name`, `description`, `location`, `budget`, `archived_at`, `supervisor_id`, `created_at`, `updated_at`) VALUES
('153a210b-1097-44e0-8287-e5311f2e722d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-0132', 'Finance', 'Manages financial planning, accounting, and budget oversight', 'Main Office - Floor 1', 3000000.00, NULL, NULL, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('1e263b81-13ee-45be-8a66-54cc1afa344e', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-1355', 'Information Technology', 'Handles all IT infrastructure, software development, and technical support', 'Main Office - Floor 3', 3750000.00, NULL, NULL, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('21b6bdd2-0e9b-4176-9d65-452d7779f77f', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-8524', 'Human Resource', NULL, 'Main Office', 1000000.00, NULL, NULL, '2026-07-17 04:42:18', '2026-07-17 04:42:18'),
('2b1a7cb9-dcc3-44e8-8411-154290f7262f', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-2847', 'Operations', 'Oversees daily business operations and process improvement', 'Main Office - Floor 1', 4000000.00, NULL, NULL, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('ac282586-cced-4d9e-b9a6-15609483eae7', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-6117', 'Marketing', 'Responsible for brand management, advertising, and market research', 'Main Office - Floor 2', 2000000.00, NULL, NULL, '2026-07-07 12:33:49', '2026-07-07 12:33:49'),
('c835966c-ea0d-4935-9d82-8b39b8976c17', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'DEPT-2367', 'Legal and Compliance', NULL, 'Main Office', 1000000.00, '2026-07-17 22:20:01', NULL, '2026-07-17 07:07:49', '2026-07-17 22:20:01');

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `payroll_template_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `company_id`, `employee_id`, `first_name`, `last_name`, `phone`, `department_id`, `position_id`, `created_by`, `salary`, `hire_date`, `date_of_birth`, `civil_status`, `home_address`, `current_address`, `mobile_number`, `facebook_link`, `linkedin_link`, `ig_link`, `other_link`, `emergency_full_name`, `emergency_relationship`, `emergency_home_address`, `emergency_current_address`, `emergency_mobile_number`, `emergency_email`, `emergency_facebook_link`, `loan_start_date`, `loan_end_date`, `loan_total_amount`, `loan_monthly_amortization`, `created_at`, `updated_at`, `payroll_template_id`) VALUES
('0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0005', 'Charlie', 'Cawile', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', '019f6f4e-7b6c-7278-b96a-fe60417a2c02', NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:39:54', '2026-07-17 11:49:39', NULL),
('22ff1b14-2301-4a27-a022-6736b3c9f318', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0001', 'Jerson Marg', 'Cerezo', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:33:26', '2025-10-05 07:33:26', NULL),
('243e7606-9542-4c71-867c-05bea5e658d3', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0007', 'Reyven', 'Plaza', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', '019f6f4e-7b6c-7278-b96a-fe60417a2c02', NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:41:02', '2026-07-17 09:01:21', NULL),
('3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEST-0001', 'Test', 'Employee', NULL, '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', 20800.00, '2024-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 18:36:54', '2026-07-18 18:36:54', NULL),
('41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0009', 'Bulbasaur', 'Poke', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', '019f6e6f-f33e-721f-8d13-8acefe0a683a', NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 09:09:28', '2026-07-17 09:08:17', 'f32ef3fb-c182-438c-bfa6-e2dfb44b9734'),
('62e12895-9295-4a40-a298-5b9903867cd7', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-1356', 'Taylor', 'Swift', '09123456789', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '019f6f65-6d5d-71d3-aff3-e0821ea1e862', NULL, 26000.00, '2025-01-16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-17 22:59:12', '2026-07-22 04:40:22', 'f32ef3fb-c182-438c-bfa6-e2dfb44b9734'),
('75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0003', 'Alexander', 'Estares', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:37:41', '2025-10-05 07:37:41', NULL),
('b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0002', 'Lowegie', 'Raga', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 18000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:36:36', '2025-10-05 07:36:36', NULL),
('b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0008', 'Maria', 'Sampalok', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 09:08:30', '2025-10-05 09:08:30', NULL),
('b5b39e80-36cc-4f35-abaf-20272f5a461c', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0004', 'Curt Vincent', 'Guiling', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', '019f6f65-6d5d-71d3-aff3-e0821ea1e862', NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:39:11', '2026-07-18 13:15:56', NULL),
('c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'EMP-0006', 'Reece', 'Bibaro', '09', '1e263b81-13ee-45be-8a66-54cc1afa344e', '019f6f4e-7b6c-7278-b96a-fe60417a2c02', NULL, 15000.00, '2025-09-08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-05 07:40:28', '2026-07-17 21:59:03', NULL);

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

--
-- Dumping data for table `employee_breaks`
--

INSERT INTO `employee_breaks` (`id`, `attendance_record_id`, `break_start`, `break_end`, `break_duration_minutes`, `created_at`, `updated_at`) VALUES
('d8e94af9-c80a-4985-a3ea-af2be3dc8c35', '122d3f82-05c6-4d58-8992-85a4ccb14cf9', '2026-07-17 22:00:01', '2026-07-17 22:00:01', 21, '2026-07-17 21:38:36', '2026-07-17 22:00:01');

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
  `status` enum('Working','Day Off','Leave','Holiday','Overtime','Regular Holiday','Special Holiday','Absent') NOT NULL DEFAULT 'Working',
  `schedule_type` enum('fixed','flexible') NOT NULL DEFAULT 'fixed',
  `required_hours` decimal(4,2) NOT NULL DEFAULT 8.00,
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_schedules`
--

INSERT INTO `employee_schedules` (`id`, `employee_id`, `department_id`, `date`, `time_in`, `time_out`, `status`, `schedule_type`, `required_hours`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('005f2e1a-8806-4af4-a245-dc094ba0889e', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('00a750d9-0343-4e32-adb7-32aa667925e4', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('00c4a2ca-216a-4184-90d8-ae26a852a7c8', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('01702e93-0f66-4f21-a3fe-605d14b388ab', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('01f3321b-89aa-4b7e-9639-28e018e441df', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('02066edf-eb81-4f5a-9cbb-165415b09ee5', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 18:36:54', '2026-07-18 18:36:54'),
('02b01083-d67d-4b88-bad8-9e584e5902d6', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('03313002-0436-49a6-ae99-f48566e8758b', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('03bdc6c9-d911-412b-990e-58e5e0ef5a37', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('04472583-0192-4b44-9df9-e1d5caac5d48', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('04525d7f-349a-42df-92e3-9d3081f4cace', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('05502685-26df-4135-b411-48be372ef6c8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('06072de4-4824-4750-b4d5-8d531b18cdb1', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('066b3a66-1d2d-46e3-a837-120ce7702c97', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('0693ee76-5d4c-4b08-9ff0-e7c811608a1c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('06e57caa-3c01-4e59-9226-c72ea2638758', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('070ae9ab-6862-4ba0-adc2-267660c221c1', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('070efb01-4b57-4cd6-9816-dbdad68f4c64', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('07f23fed-313a-4c9e-a4da-50e08dec2f96', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('081611ce-d27a-45f4-b224-d3ecf8966869', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('088badb6-a5d4-43c7-b8f1-8ed1e70db257', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('0893acea-157a-45dc-bd0a-22cc0f5e07c0', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('090d97ff-8ead-49e1-82e9-9a6eeab644ee', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('091fe9ce-0151-49b9-9ecc-97c96a3528cd', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('09460da6-3af4-4abc-907d-47b510dda6a7', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('09df26a9-409e-4a1a-b4c9-4c0c5447733f', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0ab84b6b-db0e-4fec-aca2-aaebce3ead25', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('0bfe230e-4635-4aa0-b7fd-c03b99fb672b', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('0ca0e033-7924-41ab-ac6c-218868be6318', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0d1282ea-4329-43e5-9a0a-1a3d6f309af3', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('0d9c8790-cff7-4feb-8679-a028e501d271', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('0e50e558-2fa2-43db-bbea-d26e2611c248', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('0e5e9159-65ea-4dce-b655-c706e88cc4ff', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('0ea5faac-0c54-4c1a-9b89-facca6c3469c', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('0ec84346-6728-41e8-8723-0d1e08631b12', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('0ed37297-a98d-4ea8-ad0d-1830a380aa2b', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('0f08010e-2cb9-493e-afbf-ea104a9e4fa1', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('1028f5a7-227b-4741-a4fd-d578ab53377e', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('105fa923-ec38-4a2f-834b-22c2ea85ad79', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('10862aaf-6c93-41fb-9ffa-ee875e238577', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('10b17b93-a393-4774-82f1-262ba6b6aa5e', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('114e9336-dafb-4a77-b763-302fcaf65344', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1158ff2a-3674-4e6f-9b49-2297a9e5df46', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('117cedaa-4565-4ea7-b06e-987e2b14209c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('11dd6e75-a08c-41f7-8f83-c2e0054711c6', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1259b0a9-f417-4332-a519-ea8fae4d0df9', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('12b45ea5-fc80-457a-85c6-6a539c994fd0', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('137c16d3-6e02-410d-9b79-1ef45475c8d7', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('137c36ea-6e35-45ec-8b9e-852c49338596', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1430fbcf-cb50-4787-84dc-2e8158ad9b4f', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('148bd8a9-7790-4200-b025-94e16243630c', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('14b7848f-f4e1-4de1-a476-eb730420b5f4', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('14c3dec0-c9d0-4c34-828f-0ac5a5ee8762', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('14e77f5b-5b48-42fa-be2e-acd34c7f4669', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('14ef40ce-b34f-4698-a770-1880b70559bc', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('152aa037-ece9-4c36-9445-94bd093c57a3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('1556b98b-8758-4816-accb-5b36f8ad4221', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('16ae08f5-bb35-4a97-ae6e-5db261e0e6f5', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('16b52e01-7018-4fe7-a50c-82e25f3b96b4', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('16dc78ea-4603-408f-8a6a-71bc9bf244f7', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('17247074-e5e9-46e0-bac3-bb3acf11ce00', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('17cbd55c-db5a-4f49-a742-cda494601c50', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('17cda627-c51f-4530-94de-0b20831fb69d', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('18e35e14-03a0-49aa-ad8e-3322e53f3a7a', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('18f93ade-fa10-4478-98c3-6c86f5dc06f9', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('190ebc97-8685-450d-a705-d1a0aa687989', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('19240579-9cc4-41ee-91fe-3f93de685718', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('1969f2ff-153f-46a1-8600-2abd1bc4d08c', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('198d296d-2eb1-4e91-856f-8c0119597d0d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('1aebe894-93b4-4ae5-9385-de6375ae2a7c', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1b56a78b-aee8-4d26-9a0a-97654017bd90', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('1be6487f-b9c2-4893-b79b-c2b068900428', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('1c2a3d73-63ae-4f9d-98cb-0a2c4baa4540', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1c52799d-2513-44c9-88cc-9dd0a6b7f614', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('1cc241c5-6c4a-4392-aab1-e53723e5b60a', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1d1eb000-fe3a-4fc4-aa80-99859313707a', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('1dca86dd-67d4-4f33-8652-68925e21f3ce', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('1e3117e3-3139-48a6-961c-5a1660258183', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1ee3b512-9ae1-4476-baf4-49c3a58510ab', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('1efc92a7-89c7-489b-a84f-72a2b167cc22', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('1f03c59b-d984-418b-a7a8-89b00438b33c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('1f585a5d-8386-472d-bafe-fb363af5317e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1f82fe20-ae26-4b28-b2d7-7366ee756da9', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1fb1de04-ef5a-4035-b4e6-1cdf384535d5', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('1fb3cde6-3688-4573-989f-d1e0af0075a7', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('20155122-3316-41a6-9cca-adaf8af2e2a5', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('20d0d87e-a8e4-45d0-a328-028dc9efcb0a', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('20e14fe1-1dd5-464a-af4a-4560e6ac094a', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('21545d9b-352f-4a70-abdc-d962d5f4bef7', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2156e0be-af87-4534-967e-c503986b4950', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('21ef4380-8351-45b2-b304-e8199f3c24f0', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('2201d965-cc24-495b-8df7-a50217b8f8da', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('229e2ec0-6fc2-4bfc-8752-9748e700a2e5', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('235c2305-b52e-4e04-95fe-e3e2b01fa93f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('23889a5e-3019-49dd-ab65-8b5432acadf8', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('23c0fc2c-356b-42c8-a5ca-2e621a79a0f5', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2577d507-aeef-4e11-8b22-e403493d626c', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('276a3d7f-2994-4fe4-b9be-f49c3e1a2ce1', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('278aca7e-6d14-4436-9fd6-83e1792705f0', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('27a0a426-aed2-41c5-a06f-66b9a69f2ca8', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('27c53af2-f0a5-45ee-a549-1d8c29034188', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('28010964-dae6-4160-a444-7f0e9cd283ec', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2807aa69-6119-49e8-9035-b279b278e119', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('28424b86-c3f4-4b5d-bd25-7b95c68238f6', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('28fb9c13-55ac-483a-8e1e-a7a7368b662b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('296638cc-cc11-4d39-986b-cfc84d2cff58', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('29ca0a64-9dce-4134-91f1-0571a40162a7', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('2a5c97ce-824e-479c-9721-a3807b4a1687', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('2a931d4d-b170-4376-9754-fbefd4f1db07', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('2be4d63d-6781-4114-8661-3e42fd3195d1', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2c094093-7b31-43fc-b98b-2ab01aec8b83', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2c4de1bb-1cb5-43e5-b8ec-2b8f118d9acf', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2c61c4f4-a8dd-4959-aa8e-adc908b46520', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('2d4cf39d-b43a-483a-a1b1-f24a4055a068', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('2d878105-e418-4078-9664-544ce5461390', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('2dfb1432-f0df-4e5d-8733-6a43520986e3', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2e7068f5-14d1-4634-a1c5-1e43a5dd5b33', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('2f1b2aaa-edde-4ceb-a084-e47f185fb9b9', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', NULL, NULL, 'Absent', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:14:00', '2026-07-18 13:40:30'),
('2f1fa0c7-2262-42ab-a201-1b16e4254fb3', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('2f45a3a2-345f-4b68-9500-21575fd74fd7', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2f57070b-a1fa-448d-a680-7558918141eb', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2f600226-ab2d-47d0-a19b-b405304e160a', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('2f7dedf5-6e34-45cd-afb8-e73151fdd8ab', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2f8e9c52-1762-4f72-ba94-6d7bf97646eb', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('2f953b30-b674-4bf3-bd23-6ac1ba8820c8', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('302d82de-83b5-497b-8095-b8c92cfebdb9', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('304ce3ed-50b9-4b27-bbc9-5ce175b1bcd3', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('30964b46-2134-4753-8e9f-5c0d27f20bab', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('309e831e-5241-4a82-9c17-9852a7e9fc1b', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('30fed6b3-2474-45ab-a960-034d9c43ff85', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 18:36:54', '2026-07-18 18:36:54'),
('3232dce1-6f38-4e59-b143-09562b8c4ee6', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('32b230a1-bc7a-45d5-9ad1-e026ca08478b', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('336dff76-e64c-4257-8e7b-fe25393b0395', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('33ff4d3c-094b-4895-aa3d-bf6c36079ce2', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('3401c7a8-f819-47ca-b628-f7575e39e7a1', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('3424818a-97b4-4f12-8b3c-15f7e1fd409f', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('3535cfd7-2c55-4d1f-a892-4ba6e803c6cf', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('35432c1a-fc26-4387-b6ef-6ce24f5086cc', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('35b2ca8e-6114-4722-acc9-58e27045d171', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('35ec67cc-276d-4e1c-b149-10d50d595ad9', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('361a987b-c712-453c-9983-d3f2d9b520ab', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('36da1c3f-5b43-43ef-b503-c15969e333c4', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Regular Holiday', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:47:35', '2026-07-17 07:47:35'),
('37429c17-f677-4ae5-841a-b42a369e4207', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('3795e22a-d83f-4e00-a4ed-204f386823a5', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('37a45170-45bb-45ca-b59a-f9524d1a80c0', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('387d47e6-9b45-4f03-8c0d-b2ce75e2e7a1', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('38edc937-1cdb-4628-953c-7dcdd2865836', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('39bfd636-954a-4e0b-b543-30fd7f0dd154', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3a62870e-710b-41bf-997c-2ddc75744198', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3aad2db9-fb3b-4745-b86e-ce15986e2f0f', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('3aafd745-09d7-4ca7-9b33-642760c96bba', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('3ac5612b-ff76-44d9-af9a-2765b09431cc', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('3bba3dc8-f6f1-4ea0-9e1b-8db9993f9bfa', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3c0962dd-445e-4d23-b054-81d3516e8ae3', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('3c412906-45da-4c3a-a967-e206b56c34f7', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('3cd15719-63ab-44eb-9456-9aacf078818f', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('3dda408a-1ae1-4ae8-997b-82daa9cccd0a', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3f487209-4bd5-4c0b-b6f9-2ef11a887516', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30');
INSERT INTO `employee_schedules` (`id`, `employee_id`, `department_id`, `date`, `time_in`, `time_out`, `status`, `schedule_type`, `required_hours`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('3fcba571-9f30-4fd2-9ba1-2431a09b4ec8', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('3fea73de-3110-41a1-8dbc-bdb5dd71a1af', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('40b4d096-6c51-415c-8d3e-8b72af182c5e', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('426f1cc4-4a2d-4c71-98e5-b633abacc49d', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('430bda12-f032-4ee8-8d0a-9046ccc67535', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('435448fe-a2be-4abb-9642-87e18a0b0e3c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('436b90e5-4ff8-466c-b2ce-256b9bc0965a', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('43a3a84c-25bb-47aa-a0c9-92eae74bc7f5', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('43cbdc56-8e0b-4e92-9204-0859dfe68ec7', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4427268d-67ae-4f31-839c-9ed49c4fe279', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('44ef9454-7962-48f9-a633-7f1fd7e3784e', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('452ba003-65c4-4108-b63f-7b3e956bc3a3', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('45779c3c-5d44-45ff-9d9a-5bb6de9a6812', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('45a38b45-4ab1-4616-8672-15db5c9fca3f', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('45a41a6f-3cd9-4de9-844c-d29eb295715b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('45a4e734-2d00-4bb2-b5fd-18f50aff841b', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('468280ba-81b4-40b2-9e87-7e56fba352c7', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('469fc5fb-1f5c-472b-9cca-5800e9e78cc3', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('47181795-c1ac-43c6-845d-84f64da56528', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('474c7b73-f809-47f5-9c8e-93800bde4f21', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('47bddddc-00bb-4cfe-96f0-11e963b8d2aa', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('482c4d15-12b6-43df-ba01-490398edcd2c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4845d3d7-2833-4a33-8250-2f3cc417d12e', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('489065e0-d8ef-4856-a5bb-2b124b9b6fcd', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('48da2a91-2cf1-4fda-9a84-0132f6a5ddf3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('49187f21-d7cd-48d3-8c8a-be05e586b878', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:16:15', '2026-07-18 06:16:15'),
('4987fc58-2d4c-48d6-8a4b-4d620681b5aa', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('4b17776a-868c-460e-9f82-716e803f2f34', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', NULL, NULL, 'Absent', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:43:18', '2026-07-17 07:43:18'),
('4b2d2982-cac3-4689-83ca-a668430507ea', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('4b5a8a6e-dd59-4047-adf9-a94af47d25b9', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('4b7901bc-e975-4b53-a359-75a3ef7a217d', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4ca4b407-e7bf-4e0f-acf4-4516bf96824f', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('4cdcdf79-88f6-4c91-9385-203f60ce9e20', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('4cf5ebed-6bc6-4c3e-b34d-7cc5313d4b4b', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('4d1a0665-e564-44d7-8bdb-213621739b14', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4de53050-6594-4cb5-bde4-1e7d3592cbfc', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('4eb48f48-78f2-4a95-ad7d-1dc1f5e41921', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('4ec57a57-698b-488e-877b-2c3805187344', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('4f09f5cb-f301-41cb-855f-4690d2985054', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('4f56a34a-c2fa-43d6-8ade-16bfff146283', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', NULL, NULL, 'Working', 'flexible', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 14:42:20'),
('4fc55458-dc71-4586-b16d-59aa2489e0f8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('511b5bb4-60bb-4c84-9484-94451e2fc8b2', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('51429ce4-77fb-4e3f-a739-4c2b7bcd9502', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('51616648-8a6a-4a3d-ab45-b246b46dfb0e', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('51762716-9c97-459f-b16a-e524338d30eb', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('5189a38e-3c0d-410c-9d9c-6cc55601f94d', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('51a05d17-e7b5-41aa-9920-08ed8f010d2c', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('53410c2a-4307-4720-9723-80dca7291d72', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Leave', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:49:01', '2026-07-17 07:49:01'),
('53d05410-6f46-40be-96f6-6fbc833214db', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('546bcfcb-880c-4422-96c2-0a56e43f853d', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('54a63781-b9d5-4f83-96f2-5037552e3f64', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('54baf661-cc10-41bb-94c2-e22e37985649', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('5511116c-7f66-4654-a58c-b160437b0abb', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('55209da7-0c90-4635-9e17-9c07fc8c9ca5', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('5539064d-3ecc-46f3-bd36-7abced4ad4c0', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('55ac89d9-03ee-4340-a3f4-0820c2a55b8f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('56b5af02-2010-460b-a288-e59244460dff', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('56d5b67d-593a-4921-bb0c-d708e3fdcd2c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('5705fd7d-53b4-4e75-bf58-1b3df4001531', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('58182646-6faa-4185-b1cc-f5bca16ff5f9', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('5860144d-494c-4ea4-ad79-3da63e66f319', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('58892b37-a8c6-4013-bcf7-5ae76431edea', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('58a7edd1-a73d-406e-aa74-054ef239faf5', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('58bf661b-f923-466f-97d3-801dc5511d1e', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('58d2636a-125f-46df-bf3e-1e80cbabe528', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('5918116a-1a30-4258-987d-c665a4e50693', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('5953601f-a8ba-4f79-9943-2dde9ecacc7c', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', NULL, NULL, 'Leave', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 06:15:07', '2026-07-18 13:40:51'),
('59dd7523-4591-4b3a-b9c2-33dce1c6fe8b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('5a7954f4-67a3-4fb9-a43d-0ffba52c1832', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5b249093-4035-4623-a5e5-19f0d1dc527b', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('5b79fadf-94a5-442f-8ccb-7fb3f0291791', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('5b825dd8-0404-46be-883a-8983471c80e9', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('5b93faae-b3df-466e-ab79-4279eef9011d', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5b968ec9-9ad1-45d6-b30f-6e8db07ab918', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 18:36:54', '2026-07-18 18:36:54'),
('5beb4b63-acf1-4cf1-a6a5-5e422eee2d18', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('5d11a271-9895-48ea-81c5-71223e2cec1a', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5d3b59a1-e54f-41ee-8c1f-16cb156afce3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5d7d2adf-97bc-4514-8e35-4a4488aff571', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5de92b07-a84a-43ce-8c85-5dd381c20e6e', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('5e0e44b9-f4f1-4e33-abe4-b416e29eee3b', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('5edd78c4-453a-4cdd-9e72-aaa7c3c6b73d', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('5f4703ce-c4dd-4dda-8709-c754aabaa737', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('5fdcd9ba-a7f6-439d-b9bc-99a81a6756ef', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('6009383d-f02a-47a2-bca6-6908ce5f229f', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('605b6e09-489e-4223-bdef-b9a556b0da9f', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('60de7d5d-d666-4b05-b351-04bead7f546b', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 18:36:54', '2026-07-18 18:36:54'),
('6186dbac-eab5-490e-9ac0-7d8ae94e0896', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('619e03b4-7299-438e-8203-2a9f43dc8309', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('61dc1f1b-ab61-48d7-8e98-44c14cd02954', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('632222a5-1a34-4075-9d1f-8d40630a8e2f', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('636a3b2d-035a-4d1d-8a15-26b672a76008', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('6388988c-ca5a-4280-be59-5e27e5756deb', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('638f3e62-c764-4d07-9b46-c2c51c4fb6d4', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('63e25efd-045b-4bee-888d-87219034e7d9', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('64497205-64e4-42f1-badf-bb12ded43596', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('64c1f2ea-0e32-4bd0-88a6-969927c928f2', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('656aa96e-d36b-46f0-be7b-a5978a6ea139', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('65906633-f2ed-48fd-90d5-3a36ccc27b40', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('65ac9734-a77b-461c-b2aa-d4b626d4fe85', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('65f56bda-abcf-4985-b6b2-9c3cf835ee32', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('660ca50d-1f13-414b-850e-790c42a4f57a', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('6630ded6-6a23-44ce-944a-abccd5658d4f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('663f05dc-d04e-4832-a427-ea9009cc1c86', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:44:04', '2026-07-17 07:44:04'),
('66db4816-cee0-4356-9514-b717548ed80e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('6729cdd1-1730-454f-885d-4af36d054c4d', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('677f45d1-1a6c-47ff-ba50-479e90980aa1', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('67f3d329-797a-4130-857c-2a13c64e261f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('68048714-6b66-4109-87c5-92a681c68b68', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('68aa5a20-c57c-4a68-9e7f-39ec0ae5c239', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('69175292-b30d-4455-9704-0ffaca6f8cb2', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('69498495-e231-465d-abe9-05c31e3473c6', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('69791ded-1b02-417a-8424-674e941ec727', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('699da52d-7a67-4aea-9678-65902a6cbb9a', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6a808f9c-acaa-4cc4-a60c-6bdb42c5be13', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('6ac541c5-76e2-436f-8ecf-e39d18e33e78', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', NULL, NULL, 'Absent', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:33:31'),
('6af416a9-98df-4797-8b58-58fbb5fd7fa4', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('6b07d998-72ae-4972-a8ce-507e84b4adfc', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6bcf04f7-d58a-4426-b839-4aebe6d77b70', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6be52f9e-88e6-464f-a2ce-473d5cf60dba', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6bf9f140-80b5-499c-8ce0-8cc1f33f4f11', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('6cc2dad0-a951-48b8-a5e5-d3459e9374d0', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('6ce42cc4-368f-4eba-8baa-41cb903ea754', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('6d7448c4-40c8-47c4-9a45-31fc3b4b42f0', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('6d9aad58-9739-4523-abd2-92338457ded5', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('6e2252d6-a6f2-4618-9d85-19ca1673f8be', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('6ecf02d1-c33b-40d6-a58b-37918bb1ecaa', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('6fa91ace-33b5-4248-b024-5f74eeccdd53', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('70532f40-c9aa-43a9-8c2e-368b7ad8a7b0', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 18:36:54', '2026-07-18 18:36:54'),
('7107b03c-d9d6-4b75-b353-0baccd557750', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('71c6d2dc-b290-4441-b1d8-fd8b34fa7b1d', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('71e39a65-8b59-4ec9-be9a-71ea9a5a5ab8', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('7303c554-8eb6-49eb-9411-ad3dbd8422d7', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('731ed5e6-bb00-47d3-84f1-fa6c10b9499c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7420216f-a1bc-428b-8423-19c609a73a4a', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7479f60d-1057-4a9a-9d31-727be76b1e83', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('7489a60f-af34-4443-8f1c-f4839143c5c3', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('74a061ab-4d27-4f00-9bd3-e5fe119cacfa', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('74d69fa7-de9d-4d5e-8804-88c0cc14e0e7', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('76a7eb07-5202-44df-8a94-ceab184cccca', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('77154860-7fb4-482f-bf58-f81ce1922065', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('773ee3c6-81e2-4322-afbf-5e8d944ffe6d', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7767f389-5023-4cef-bb6e-640332dbdfae', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('776a71f8-1178-4b63-8a1f-91deefbb83ba', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('779d1aac-6687-4842-9150-aeebf61a3117', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('77a61458-30f4-4b4c-9965-23eb646d7d2d', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('78ae67de-b35b-4d2e-9b85-0ee7a6b76fd0', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('78d12ed2-ec43-4504-9073-332eb252b49a', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('7965e475-b56e-4f16-b47d-fe612626f4e8', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('798c531b-7b15-4250-a221-9bbc5d175f97', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('7aae89be-3003-4bcc-9545-68bd91cb19c1', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7ae0675a-f72e-4f00-90c9-2468affba7cd', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:47:50', '2026-07-22 02:59:55'),
('7b2e0640-479f-4eeb-b64a-351003ca668c', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('7b320bb0-1195-404a-a75c-bee3651f696c', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7ba7b3ba-e30f-4b43-b00e-e64ddcbc4b54', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('7baa66e3-c64b-44eb-bbb1-8248608027b8', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7c9fe017-df58-4789-9872-b099beb8e14c', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7cd48f77-934a-4867-9a63-75d971fb3959', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('7d1488a0-ad3a-4c97-bd0f-44632dc1a62b', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('7d1855e8-e0ba-49ea-988e-3fc62588b963', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('7dd10a7f-f42e-4eea-a20a-4d8144127d71', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('7df1abdd-6443-4f85-9ffd-54d6184bee26', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10');
INSERT INTO `employee_schedules` (`id`, `employee_id`, `department_id`, `date`, `time_in`, `time_out`, `status`, `schedule_type`, `required_hours`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('7ebc6c87-0450-417a-9c6e-972133d70fe1', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('7f1c5706-8bed-41b5-a101-01437a1ee79f', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('808da83d-4d03-45ae-ad2a-7661b02baa98', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('80dc83a5-8adf-43a1-9a56-be58692ed8cf', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('80e537c4-1baa-4adc-93b0-912f093f54a6', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('816ba7f7-1bdf-47e5-82e3-d0202dd15a86', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('81951d57-3d58-49c2-a366-bea7801dee50', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('81c577d1-fff0-4727-8a13-f515353731b9', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('81f667bf-c405-4ac6-8339-942226b281e7', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('82163332-44d0-47bc-902c-e5b35722a07a', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('82850f92-031e-439a-bbdc-de0961c77282', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('829e4796-4aff-4c96-80bf-cea929272621', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('82bcff85-176b-4fdb-bdf1-8dad6540a36b', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('834b0eb3-3148-4766-8f9b-9fec74600b82', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('835f3da3-c874-4358-bb12-4308940fcc3c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('844807a6-8753-4c38-a90f-fb05dcb70e23', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('8451fe0b-12b4-4024-9c16-58197257e98d', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('84614f70-15df-4291-b375-079bb98050d7', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('86d2552a-04c6-42e6-a5bf-ae86f553e422', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('87051ff2-8be6-458c-938e-34ff98b86fba', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('87754d79-32cf-4e35-984c-510d357847eb', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('88e201a9-e525-424e-9a56-eca208e657a2', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('88f8d4ab-b414-49b8-9667-a4744dab7d54', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('8969d1e2-86a5-43eb-ad25-ab0e7a186cff', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('8977f449-80ce-451d-a73c-55ab613d61e0', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('89c4dd41-5e2e-4ea3-b1b8-b7a9eb98bfe8', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('89d38184-e109-41e6-bece-9a194f34ac7b', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8a161995-d9ae-4b87-9516-8220227ea98e', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('8a1aec23-f1f9-473a-ba8d-421b7d324050', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('8af6f62e-ad86-4202-9e77-ad067d341714', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('8b19c800-e1be-4a4b-8006-af3792deff39', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('8b3662e1-51d0-4ee1-a632-413401ac765e', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('8b7c0756-b9c3-406d-bef4-1c9067a2390a', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('8bf5deac-3e3a-4811-94f5-271665a57bbd', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('8c4bfa5c-834e-405e-9d90-32a12b7da0e6', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8d58a0ca-d454-4d92-8963-f35ebeaedb65', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8db7b738-ee1b-4a3d-b1ea-169b9aedc927', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('8ddd156b-80cb-412c-8569-df4015324b8e', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('8e14e49c-9cbe-46df-8ccf-5a1a4ee16cd0', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('8e22226e-52e3-443d-9254-e14ab93b8b94', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('8ef96a2c-f7f2-4e30-bb90-e94d3a4762f3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('8f4ac167-7aec-427f-a153-dc32b0068293', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('8f71fc8f-fc16-45ce-9aa5-533e9011b34d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('8f873cf9-1ba6-4126-b805-97a294571307', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8fb116d5-b813-415b-9b9a-278e0427538d', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8fc6286b-6801-4e3e-b7e8-a970e57a5c15', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('8fdb6c59-ebec-43bb-89c9-0a22eb6f6af6', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9014832f-bafc-4a32-ba5b-9bf5f0fad690', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('903835ba-713f-4ef3-a86b-6ce72175ae4e', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9080ec4a-a61a-4c7b-b495-255f1ea5abb0', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('9087d9c8-cbaa-41ef-8df2-07b1e9000f07', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('9114470a-c99a-4862-8ef9-aeacc1376c44', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('91306b88-7cbc-40b5-b9d3-9758c8a49799', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('91b86b97-f581-49bb-ba7e-4af4fe9a67e7', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('91c21136-450d-4fed-9a96-eb59707e774f', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('924140b3-001a-4dc1-b5d6-e3785e63b9cc', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9243997a-48fe-4356-9712-efd0f7e5cbac', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('926f4375-eefc-4ca9-a234-a2552184a21e', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('92916183-a390-4411-a904-27f7846deb1d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('930438db-4875-44d4-8df1-8acc7406bbfc', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('930466ef-2319-45e2-aa8c-a976512faf7b', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('931f6087-8136-474d-b022-4e845a800d1e', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9399dc91-764d-4785-9b30-08a27d6c740f', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('93b84138-9056-4b2d-aa42-76fc80bcdb08', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('93be760c-1175-437f-91c2-3c764b508281', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('93d8b401-f576-492b-b4a8-47f364e86ac6', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('943b29aa-dfef-49eb-aded-fd17465f4728', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('943f514c-8518-4584-a072-bd70b3f666eb', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('949fed6b-41eb-46d4-93f6-233691dde221', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9524321e-39b8-49de-81bd-3c2a3f300846', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:45:25', '2026-07-17 07:45:25'),
('953716d4-dcdd-48fb-988a-4ebe24bc2950', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('95702e58-3ef8-4c84-938a-7db42fd7ef97', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('963b12b2-81aa-472c-ad86-fc2a6a4472fd', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:16:15', '2026-07-18 06:16:15'),
('96967f81-baa0-4f7a-9eed-465864d249f0', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('96d235d1-f839-4128-8ceb-d4c2e8cb7c7f', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('97b66480-6fd9-4c96-98b7-d9008b016034', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('97f76c3a-2149-472a-8821-4b481ed06de7', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('99c2781a-f6d8-411b-aaef-f52fd30369e3', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-09', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9a5919d5-286f-4a9f-ae7d-e59ba142c88c', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('9bf751f5-fc50-471e-9328-5a86eb505133', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9c0f1930-4deb-4944-b3f1-cb6efb4abe26', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('9c945f36-80fa-4978-b434-5a46d1d58954', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('9d21969e-05c5-47fa-a961-c20e2d6a7ae8', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:16:15', '2026-07-18 06:16:15'),
('9da78d08-9654-4342-939f-a4306f66ba6c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9dab5e4b-4095-49c3-b4f9-eb6f833318fe', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('9f0039c2-6e04-4825-869b-bcafa85c7281', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('9f802fb2-fbae-4a78-8e74-8e9765d67509', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-26', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9f8a8ddc-360e-4b03-b237-7ebc40731a76', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('9fbe261e-727c-40b0-a1e1-ab11d7cbd6ce', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a012bca5-63cb-48d6-ba48-46871ab61356', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('a058a9e5-8c9f-4c3e-9571-05a64c696d09', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a0f63037-f8fb-418f-bda5-0aa5cba06371', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:45:55', '2026-07-17 07:45:55'),
('a144072f-ff21-4e38-87fc-23a0c1222cd9', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a178ca8b-55bd-4309-b191-b45c305d4928', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('a18247f2-584f-4df3-8422-02b083e4f23f', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a3006b54-b523-476a-bf2c-49c0eab7ac8d', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a3674288-ba9d-405f-b0a8-d242e8e70949', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a3b38981-a0f6-4ac3-9101-91cd9bc80bd2', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('a3fe35f3-0f4c-41b2-a07f-71fd8825a4b8', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a46a52bb-a121-4806-ac26-03e81df6b6ce', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a47a4682-99d2-41ef-afb4-b469bd588e62', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('a4aa263d-c1c7-4c58-b4f5-cae673ac900c', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a4c6d063-b530-48a9-9cca-77022a263005', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('a51e8c50-3e53-469f-a146-612d02421efd', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a5368a6b-fe53-4b94-923d-ddadb2e5e3d3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('a565ffb9-fa96-41f8-9e19-6a081d062bf7', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('a59706ee-fcdf-47ba-b2b5-ea287f2f1881', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a5f282ca-4588-474e-809f-852190fbbec9', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a5f8e053-36d9-449e-a245-b4b44481ed3b', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('a64d75ab-a126-47c0-b13e-39b49e8e4292', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-01', '08:00:00', '17:00:00', 'Regular Holiday', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 13:41:32', '2026-07-18 13:41:32'),
('a651d4e0-9c13-4522-95eb-bff4f2b41a7b', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a6b58790-9d5b-4350-98e3-6681498e6004', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('a6fd74e9-b3e0-4a66-bb4c-76d4b99043ca', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('a77cacd6-e960-4cf9-8e61-f0485b61e547', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('a77ddb7b-eba0-4d88-b8dc-c7eacaea316e', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a781f61e-3857-47ea-b7e1-f25e09c6c938', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('a7c3ba8b-63d3-47d1-93ff-79b15ed8e4e9', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('a8080108-e5fb-42e9-b367-d3fee91f162b', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('a84ac39b-b3b3-4add-b24b-e052472d6a52', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('a8f01f79-08ef-4d67-9e9a-30ee7b09b35f', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('aa18ee49-dcb3-4776-b3ab-29981a5bdf12', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('aa55b081-0bff-453e-a7cf-70207cd0d45f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('aa84f350-8e53-4f8a-ae06-290486ecbfd1', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('ab19dc70-bf59-4382-9f69-1f53276d3d19', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', NULL, NULL, 'Working', 'flexible', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 01:18:10', '2026-07-22 14:20:16'),
('ab28c952-1886-4407-a603-c8c9658da9dd', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('abd4353c-3ce6-462c-a8c8-67c6327fcb22', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('ac2a9b99-a666-4c0b-b7ac-c4ff80346dc1', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ac9a31ac-6165-439b-8721-ec85b0f6bcac', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('acb5c597-f9a8-4faa-a5f4-ae95107c90f2', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('acba8005-4cde-4020-b2b1-2b6a623001ab', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('aee9f645-d93b-42ae-8198-7d92c7ccaa41', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('af672be3-102c-4caf-a6e2-fb41aee344cc', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('af7015ec-3cdc-466c-9a00-c167359dcaf1', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b00bfe2d-705e-4e8e-90d9-67723b715ac9', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-12', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('b0185929-eb2a-4d5b-994c-08085eca816e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('b07633f1-64e0-4ee3-b8c6-db7c55cae628', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('b0aec18e-ae6e-45ae-b55f-39cfa95b7577', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b14f61a1-e0cd-4140-b05e-feab5eb329d2', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('b16ba18a-257c-4c1a-8eb7-bf94b6cc71f9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b188612d-8b00-4dd5-90b1-55bf35c610d3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('b232ff1a-41fb-4ba3-8baf-39fc58113ed4', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b250fe58-c29a-4333-8134-407e2c111d5a', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('b26f83cc-0530-4890-b173-d1664807810c', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b2cf23fb-7366-416e-9e1e-885b55937fa1', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('b2f88c3e-c720-4ba9-9970-a377e5ccd846', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b353ea57-a03a-4baa-8655-4cb18b58bac5', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b4116ea6-0de8-441e-b22d-bbd50473c10d', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('b45c772b-4a75-44f0-9b96-845675bf2c5a', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b4a88b81-ef1b-4909-9856-15ca1aa53036', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b4d50016-34a0-4731-b730-0a0540452e74', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('b5f8ec0b-9864-4e48-a18d-12d53fe39f90', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('b65066a7-1d84-4f13-900c-47f35fff1847', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('b6b46916-6679-4c75-b62b-dede2effdc2e', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('b6cc173c-4223-49a0-9348-922614fc12dd', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b7355fe4-3330-4030-bb80-bfd02754f038', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('b7f698d1-d2f1-4041-bd2f-1e6811309320', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', NULL, NULL, 'Leave', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 06:16:15', '2026-07-18 13:40:51'),
('b8739982-0bdf-443c-a8b1-148ec7a04da1', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('b8d863cf-9cbc-41b2-bdad-9c57e7386ac6', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('b9cc53a5-39ed-44aa-9b78-ebef20d4e10e', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ba0473fa-ba82-4996-aecf-d45124650618', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:16:15', '2026-07-18 06:16:15'),
('bace7504-c990-410f-873a-52d7793b078c', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30');
INSERT INTO `employee_schedules` (`id`, `employee_id`, `department_id`, `date`, `time_in`, `time_out`, `status`, `schedule_type`, `required_hours`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('bacfd7f3-5c56-4e95-92f5-7ba47e295b4a', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('bb0ff3fe-0f8d-4c04-9467-4fc96a2f14b3', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('bb1d07ec-fb0e-4a01-97fb-cce6e1258186', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('bb2efb63-0cb0-4dee-9e01-5fb999a9f978', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('bb828e2f-2a3e-4599-8bf7-9b78276eab72', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('bc599dd2-8656-4f66-a841-6acb41274dc5', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('bc6b7fc7-0479-404e-ac0d-1ecd0d81120c', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('bc6e0ec2-b42f-493a-90c1-5085e390a559', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('bd31ea6c-6cef-4479-82b1-19f5c9392e92', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('bd363cd7-de19-4ccc-b14e-13823a6d5aab', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('bdb5aaab-325c-4e81-90e4-eb50d0eca21d', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('bdbd57a2-fcf6-496f-b900-0ae366a9ac6d', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('be3bb3ad-6fee-4e37-ab0d-d18b335b0d06', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('beac9a21-c31c-4106-9f56-e9f95665e645', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('bec2c853-d22b-49b7-943c-71b38f0f2c44', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('bf0fe192-4322-47b7-8fd3-d29dd70c42c4', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('bf7f6034-2e1b-4f89-b680-33ee5608f4bd', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('bfcf5f8a-0f3f-4c8f-b6a0-56aee9189049', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('c03a67dd-238d-4bdb-95ed-cb599d7c141a', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('c0d128e1-e70f-4809-b0f2-a62088ccb4e2', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('c1bf79ae-5846-4693-af18-198f77fb69d1', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('c20b710d-6dfb-4a7e-b198-28cc9524b1f1', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c225fe50-2d22-47fc-a85e-9f46accbee41', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('c2c727e2-a8dd-47bf-9172-46dced5743db', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('c3146ba4-aa95-4ec1-90a6-b2f2d60efd41', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c334de6c-3665-4fe3-bddf-f349915c0292', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c34850b7-a096-438c-b1af-14f434edf492', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c3fec47e-270d-4684-83e2-7154d2cbf160', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('c432c773-f322-466d-a2e0-083ee69ae3e3', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('c45ffc4c-64d2-4076-8430-b5301f4459c8', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('c5565bfb-7b1c-40ee-8068-5a3ab5119b6f', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('c572b82d-8a60-42d7-a509-38883a40f7ec', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c594be1b-4291-4d80-b78a-1b44f69c4a63', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c61bf5ca-5904-44c3-ac3b-4d1dacf86ee4', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c6c61557-b1ce-4bbc-bc15-ac8c32b4c86e', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('c84264c7-ab56-46f8-ade9-efd9d126adcb', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c8d3731b-25ac-4b53-831c-d4b99601833c', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('c96724a1-d434-484f-ac0e-d2da4dfaf206', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('c98ae906-0c74-4f2b-bbf1-996a538ddf17', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('c9e65a87-179d-4f08-b24a-3f465ff28c85', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('ca16f0bb-4127-40ad-b951-78894540e0bd', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ca85448a-8aa9-4142-b980-3c8d21068831', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-02', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ca85c45d-7833-4c8b-8795-e9fb34a53faa', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('cadc48e7-849c-4204-9e8c-82b56a475bec', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('cb09e599-04e7-4851-899c-840d94035403', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('cb178122-feb5-49f1-a0ef-b7e79903fbfe', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('cb337a14-a95e-4db8-ac47-7e651ff0dd3b', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('cb926207-4f9c-4093-a0ab-b3f42401cdea', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cbc7b059-3000-4de8-a9d8-117e8f91e3d3', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('cbf22194-c7fc-44d7-aae4-320ec3a5a7a6', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('cc0a1e25-b12c-4cc5-a4e6-a9be18a22b80', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('cc5a4496-1074-41c4-a2ae-f6f5e67ce0b0', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('cc5a463e-2170-4176-b316-32c25fa5a3ad', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('cc60ef6d-1112-4092-8018-263248724732', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('ccf05ee3-8d7f-4ba8-84a4-faddf12a87c5', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('cd85b203-98c7-4679-bff9-8d3ec06ea131', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ce20fbd2-a7c2-4d8e-9abf-2a2d504bc2e9', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-12', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ce455f36-170b-401f-aaba-fbe328652051', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ce51b05d-8942-4511-9f20-38a7850a41b3', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('ce5cd25d-9682-46ee-9f3c-c42a04b1d619', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('ceda3a4e-9ca5-4c85-b6b0-94ded88f1e19', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cf0807f7-dc94-4ee5-aca7-83a60e9b67e9', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cf370f55-724f-4923-a3ff-e697c3e8bd79', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cf3fb752-27df-4999-af68-595abe846a37', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('cf7dc425-bbcc-47b1-9154-7d4edda259e9', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('cf86ea3c-c0ab-4509-ad01-d54baae7593b', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('cfd5df05-287d-441d-beb5-a05fdbe39643', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('d03b7bdd-50fc-4194-8476-0836cc23b227', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('d0a21e03-0b7a-45d1-884e-6870b5bd40ff', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('d0f587a5-1a02-4143-984f-1aa115ed4028', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('d1035eab-9589-46a7-a7ef-7f5e71e37088', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('d1c8dda5-4cb6-4ec0-b365-04cdd8ce8be2', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('d23b8c04-7afe-4627-8c45-5fd3033cb62a', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d2583590-4c31-4824-a1ca-f5cc19df63e8', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-20 10:32:50', '2026-07-20 10:32:50'),
('d2e19b84-03c4-4ce5-b47f-0665d2921a02', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('d39cb615-216d-420a-b4c0-4ef8889479d3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d40654ba-4e46-4b05-8a1f-6c5daa4be132', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('d41eacd0-23b6-4779-a9c3-9458bc4d8455', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d4f7b1e9-22cd-4ed0-92b4-39b46dce0db1', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('d58689cb-9b50-4ab8-8e58-e55f82a9bf4f', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('d5d27cd5-c108-4a2d-b201-37abcbf1e14f', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('d6c74eb7-2529-428b-99f8-20d7bd390a85', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d742194a-f58b-4f09-8428-e675026bb2b3', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('d7658310-7a23-4a4a-83cb-0b60988bee04', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d8359ccc-75dc-41b7-90c3-1307e1d0ec02', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-31', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('d8cc3501-a5a8-4240-8092-0bd87a6fd5f5', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('d8e65d8c-2022-47fc-9935-8d3292a9fab8', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('d968e8e7-3259-4c2a-add7-47594b056e73', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('da0e0102-cdba-4928-9838-6c0c1a4b9943', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('da51ba15-0441-4413-968e-012995f5e5af', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('dac79104-1acc-4e6f-87a5-5209ec9f947a', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('db05af1c-0cec-4d84-b17f-77773d484c32', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('db5f5cb3-f4c2-4b26-ad4b-dc7cc5da1e44', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('dcb78aab-576b-495a-8581-c6117bd42610', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('dd2a8357-284c-4b17-90ed-3b0147fe8475', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('de05daaf-a07d-4b19-94db-c883e233eea7', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('de4b0aea-7fa7-48ac-af78-41971fe73e75', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('dec193cf-4ed1-4e84-91a5-e18e53c659ab', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('df858402-7272-4c95-8014-a58cc9c14321', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e02aa481-66c9-44d7-acd9-7b9fef4cec45', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e03320aa-a552-498a-80c9-40672cffca91', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('e0a6e551-d4c8-43a8-9ca9-9cc1c6baddf9', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e114bf48-37aa-4d3c-a22c-1cc948341d8b', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e141f738-d5d5-4db6-8492-bb4d431d7d53', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e254c6a6-a61b-4629-94f9-988b44127447', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '14:00:00', '18:00:00', 'Working', 'fixed', 3.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 23:03:18'),
('e25a5c9e-9500-4062-9ee2-4b29a56b0271', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e268565b-8bc4-43bc-aecc-8a968e2c9610', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e2d5e9e8-c3a0-4ba8-afdb-ec40e99e5eaf', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e2e3cb5b-1e57-40c2-a91b-6c770d607c2c', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e3788e71-5908-4351-a032-145fd127f73a', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e37ce565-db8d-4877-abf4-55c6f622ea59', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e3b7aad6-7890-4436-b850-1e800f6cec45', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e3c87b9b-2b42-471b-b81f-9630b281aec1', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'none', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 01:35:18', '2026-07-18 01:35:18'),
('e3e0beee-89dd-4d3c-8020-ed8eadcba678', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e3e30575-7bd0-4e0b-b4c6-a824f9c8ed4b', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e45cbbb1-5003-4c10-877e-a930df0f4d12', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e4a7d820-f985-4215-846d-9bb71c7fbc4d', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e57fa5e1-4777-40c8-a559-3d168406aa08', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e5e870c6-df7d-410a-bf2a-98bb0e4903c7', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e6f79173-8504-4035-8306-f5e836e3c2b9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e708ec46-17d2-4807-a3f0-ef50c6f2b031', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-25', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('e749dd19-4e76-409a-9040-bc78ad6b8895', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e74f1a7d-ed92-4c28-8407-14fd66e7edd6', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e750f0c6-b8ec-4ba1-9ba2-b4395f841b3d', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e754c3e8-bd2c-48b6-8213-58d20d815d2e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e791e068-a274-424a-8972-f3506d4c5be9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('e81b0ed1-a392-4507-bb16-0daf424266b8', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-23', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e91e26aa-4a76-4f3e-8160-3d7f71928a1c', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('e9b32e82-6e3e-46ca-8626-b8e30971a64e', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('e9d29ee4-1c14-457d-b993-fe5b1265d1bc', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('ea33f8d1-13b1-401c-8b78-7735b7be9263', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('ea40534b-1ca7-44cd-929b-dff47e9fc468', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ea748cfd-85a8-47b4-87c5-b7838f6c4fcc', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('eaa469ea-1e1f-4711-84e1-2f602f3a1436', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('eadb259d-a946-481e-9faf-d810c0f25974', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('ebeb8e46-8a0a-4889-846d-2766a613c1dd', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('ec189a19-2a32-41ac-977e-0d9afcef84ad', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ec6c95f4-d05c-4347-997f-b696bbfe2d1e', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('eced09a1-9356-409a-a6c0-8bf3b5c20c4e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('ee0cae68-6b31-4950-b086-5ade062a62e8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-06', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ee3a3f2d-8f94-48ba-a837-d8780351c1f7', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('ee6ef429-6089-4e90-8b33-4796eab48ad0', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('eeac200b-88f8-4051-8ce1-794c0b23cf0c', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-20', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('eeb9aa5b-384a-4241-8b16-cc5dbd33df98', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-21', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('eeec5f69-5e36-4d8d-86cb-e2892cd66acd', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-27', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('efc6cd1f-6048-4cd0-9aa8-e188e0e1c06e', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-05', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('efd00303-b5ae-47be-a218-f615534cbc47', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-06-28', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('f0011bb8-62a8-4e7b-9677-4605e92c0e41', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('f169a439-3fc1-499b-94a6-dc52b7a127d6', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('f292da8c-f666-4b42-b3fd-74df11f0fa10', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-17', '09:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 07:42:19', '2026-07-17 07:42:19'),
('f2ce703b-8fe9-4787-87af-9b134324206e', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('f2d04b80-258d-43a5-9c3f-46d040100ef6', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', NULL, NULL, 'Leave', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 06:14:17', '2026-07-18 13:40:51'),
('f2d8bb11-367b-45ae-ae15-ddbc5cb2bf91', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-26', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f2e3100f-e33f-4a17-8e74-00c03679fd5f', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-30', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('f34226c4-8953-4e09-afab-28f6da411129', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f3647c81-ff2a-4b14-9778-a12ccfe404a0', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('f3c91f53-01c4-4781-8d3e-bf6f5f3b5336', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('f3e963f8-31e9-4ad9-9205-64ab3c4b6644', '22ff1b14-2301-4a27-a022-6736b3c9f318', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-16', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('f3ec27a6-eb18-434c-b5e2-0d6be7225500', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09');
INSERT INTO `employee_schedules` (`id`, `employee_id`, `department_id`, `date`, `time_in`, `time_out`, `status`, `schedule_type`, `required_hours`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('f3fee0f3-283d-4a17-b768-1f4febf3fed2', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-08', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('f485d728-ac18-45eb-8fbd-69a062647809', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:21'),
('f4c20236-8d77-4fbd-afb1-dc766eed392e', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-18', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f541068e-ef95-478b-b1f5-b8bde0a3fb8d', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-15', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('f54f9781-8449-49ac-b31c-0e90da4e54b4', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f6c995b3-baa9-4da0-b8be-1b4706c5461b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f6e71bc6-5e2a-4abb-b81f-11bf80d46d83', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-24', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f7e0cf2a-988c-466f-9c6b-105e90a3c2f2', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-01', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f8057a91-33a2-42bd-81ca-c7109f7ef411', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('f81b8a5e-2b4f-40d9-b419-c280bfb5c5f7', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f85be46d-2262-4f75-953a-c36f6d34b059', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('f881b1c6-1fb2-4c8a-9e32-b6f7bb0cb16f', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-29', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('f8f00927-8701-41a2-b524-15e235c6de33', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-19', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f9ae9d80-96ea-4b2e-8cd5-f74ef7cba815', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-16', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('f9fbebee-6b77-4055-921b-5ce594746560', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-02', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('fa07e5f9-9a09-407b-bc45-707b5275a599', '62e12895-9295-4a40-a298-5b9903867cd7', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', '2026-08-04', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('fb3c5ce7-e8a3-45a8-8092-0971241cd641', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-13', '08:00:00', '17:00:00', 'Leave', 'fixed', 8.00, NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-18 06:17:08', '2026-07-18 06:17:08'),
('fb8965b5-ae94-4440-83bb-effa5a5a77ed', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-22', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('fbbfe523-e8da-4390-aab0-ffc3b58cdf50', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('fbce04ea-02f4-471f-b2b7-cf0ebd537f15', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-13', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('fc51798e-ac20-416b-966d-11f4b2e3aa40', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-28', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 08:58:30', '2026-07-23 06:28:22'),
('fcb91c3a-3288-4b44-a99d-8a6e89b25db0', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-17', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:43', '2026-07-23 01:53:43'),
('fcc07a49-0550-4e22-9b0f-71389a59b341', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-19', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('fce54649-2986-45a4-beff-8f26187b8544', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-14', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:53:42', '2026-07-23 01:53:42'),
('fcf5ebd2-72b6-4830-8610-0a4542c058c6', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-23', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:10', '2026-07-22 01:18:10'),
('fd81be35-3c1e-448d-9855-c3f7af54cc1a', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('fd898e4c-85d7-46aa-8f93-09670e6e0cd2', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-05', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('fe3443d0-19ca-4919-945c-d53f4f0715a3', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-08-09', NULL, NULL, 'Day Off', 'fixed', 0.00, 'Cutoff mock data: July 26-August 10 E2E test', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('fe4dbc06-fcf4-4bcb-aaa5-63236d1ce77a', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-11', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('feb3af96-f4fa-4a70-ab87-e27f62c99597', '243e7606-9542-4c71-867c-05bea5e658d3', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-07', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ff2221cb-8e80-4832-9432-06201fae1e30', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-03', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09'),
('ff2c3dd4-50d8-4dbc-9313-1aca2a92e724', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '1e263b81-13ee-45be-8a66-54cc1afa344e', '2026-07-10', '08:00:00', '17:00:00', 'Working', 'fixed', 8.00, 'Default company schedule: Mon-Sat 8:00 AM-5:00 PM; Sunday day off.', NULL, '2026-07-22 01:18:09', '2026-07-22 01:18:09');

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
  `sil_days_total` int(11) NOT NULL DEFAULT 5,
  `sil_days_used` int(11) NOT NULL DEFAULT 0,
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

--
-- Dumping data for table `leave_balances`
--

INSERT INTO `leave_balances` (`id`, `employee_id`, `year`, `vacation_days_total`, `vacation_days_used`, `sick_days_total`, `sick_days_used`, `sil_days_total`, `sil_days_used`, `personal_days_total`, `personal_days_used`, `emergency_days_total`, `emergency_days_used`, `maternity_days_total`, `maternity_days_used`, `paternity_days_total`, `paternity_days_used`, `bereavement_days_total`, `bereavement_days_used`, `study_days_total`, `study_days_used`, `created_at`, `updated_at`) VALUES
('355e6b0a-76eb-4475-badc-18cde84f58d0', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 2026, 15, 6, 10, 0, 5, 0, 5, 2, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-07-19 15:24:32', '2026-07-19 20:31:30'),
('910ee0ae-ef29-44e3-b125-1e860e8ea0e1', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 2026, 15, 14, 10, 10, 5, 0, 5, 11, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-07-09 11:56:40', '2026-07-19 22:57:27');

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
  `status` enum('pending','approved','rejected','cancelled','expired') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `days_requested`, `reason`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `expires_at`, `created_at`, `updated_at`) VALUES
('2d351d39-3515-4fc1-ab34-e9ac08789102', '62e12895-9295-4a40-a298-5b9903867cd7', 'vacation', '2026-08-03', '2026-08-03', 1, 'Approved mock vacation leave for cutoff E2E testing', 'approved', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('403ee551-479d-4609-b762-7f44e27ac9ca', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'vacation', '2026-08-11', '2026-08-12', 2, 'sample', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-19 14:09:50', NULL, NULL, '2026-07-19 14:09:39', '2026-07-19 14:09:50'),
('674cb166-814c-4eb3-a2d2-e0cc86503cf5', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 'vacation', '2026-07-20', '2026-07-22', 3, 'TEST', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-19 15:32:27', NULL, NULL, '2026-07-19 15:30:27', '2026-07-19 15:32:27'),
('7fba9098-e85d-4be3-bb59-37461d5ea7d8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'personal', '2026-08-13', '2026-08-14', 2, 'test unpaid leave', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-19 22:57:27', NULL, NULL, '2026-07-19 22:55:56', '2026-07-19 22:57:27'),
('89951b76-cb29-4b53-9e1a-5529f7a6484c', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 'personal', '2026-07-23', '2026-07-24', 2, 'TEST LWOP DEDUCTION', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-19 15:46:19', NULL, NULL, '2026-07-19 15:45:16', '2026-07-19 15:46:19'),
('affab39f-8cbb-4b0c-b83f-542ddaf670b0', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'personal', '2026-10-02', '2026-10-05', 3, 'test', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-17 01:31:01', NULL, NULL, '2026-07-17 01:30:41', '2026-07-17 01:31:01'),
('bdd155ca-2a5c-4c46-8e47-99d86bc329e0', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'vacation', '2026-07-13', '2026-07-17', 5, 'sample', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-13 05:38:28', NULL, NULL, '2026-07-13 05:38:02', '2026-07-13 05:38:28'),
('d88a1c0f-e05b-4f4e-8e95-d3a9deacfa05', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'sick', '2026-07-16', '2026-07-23', 8, 'sample', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-14 05:35:46', NULL, NULL, '2026-07-14 05:35:13', '2026-07-14 05:35:46'),
('db26b62d-60a6-4c21-998b-61ca8e3cab67', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'personal', '2026-07-25', '2026-07-25', 1, 'sample', 'expired', NULL, NULL, NULL, NULL, '2026-05-31 16:00:00', '2026-07-14 06:19:20'),
('e0b30dda-9d1d-49cf-ac0b-7df45f6f9fe1', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'vacation', '2026-07-09', '2026-07-12', 4, 'sample', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-09 11:57:44', NULL, NULL, '2026-07-09 11:57:11', '2026-07-09 11:57:44'),
('e4ade2d7-0dbd-49ce-800a-3481e0750fb8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'personal', '2026-07-21', '2026-07-24', 4, 'sample', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-10 05:29:00', NULL, NULL, '2026-07-10 05:28:39', '2026-07-10 05:29:00'),
('e87b2c70-38d0-4553-bd5c-d3fe0e761b2f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'sick', '2026-07-09', '2026-07-10', 2, 'sakit batok', 'rejected', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-09 12:01:28', 'deserve', NULL, '2026-07-09 11:58:43', '2026-07-09 12:01:28'),
('fbc58e52-7d72-43a3-ad7d-ed3fc1f959c5', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'sick', '2026-07-16', '2026-07-23', 8, 'sample', 'pending', NULL, NULL, NULL, NULL, '2026-07-14 05:35:12', '2026-07-14 05:35:12');

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
(66, '2026_05_08_161109_add_completed_to_attendance_records_status_enum', 1),
(67, '2026_07_08_000000_create_official_business_requests_table', 2),
(68, '2026_07_08_000001_fix_official_business_requests_id_columns', 3),
(69, '2026_07_09_000000_add_leave_pay_columns_to_payrolls_table', 4),
(70, '2026_07_10_000000_add_ob_times_to_official_business_requests_table', 4),
(71, '2026_07_12_000000_add_ob_workflow_fields_to_official_business_requests_table', 4),
(72, '2025_07_13_134312_add_google_fields_to_accounts_table', 5),
(73, '2025_07_13_134935_add_microsoft_id_to_accounts_table', 5),
(74, '2026_07_14_000000_add_expires_at_to_leave_requests_table', 5),
(75, '2026_07_14_010000_add_expired_status_to_leave_requests_table', 5),
(76, '2026_07_14_112142_add_expires_at_to_overtime_requests_table', 5),
(77, '2026_07_14_202325_add_expiry_columns_to_overtime_requests_table', 5),
(78, '2026_07_14_192608_add_schedule_type_to_employee_schedules_table', 6),
(79, '2026_07_16_180038_create_payroll_templates_table', 7),
(80, '2026_07_16_180047_add_payroll_template_id_to_employees_and_positions', 7),
(81, '2026_07_16_184913_add_deleted_at_to_payroll_templates_table', 7),
(82, '2026_07_16_135216_add_holiday_types_to_employee_schedule_status_enum', 8),
(83, '2026_07_17_094107_add_unpaid_leave_deduction_to_payrolls_table', 9),
(84, '2026_07_16_000000_add_archived_at_to_departments_table', 10),
(85, '2026_07_16_000001_add_supervisor_id_to_departments_table', 10),
(86, '2026_07_18_121318_add_unpaid_leave_columns_to_payrolls_table', 11),
(87, '2026_07_18_000001_add_cutoff_days_to_companies_table', 12),
(88, '2026_07_18_000000_create_notifications_table', 13),
(89, '2026_07_19_000000_add_unique_period_constraint_to_payrolls_table', 14),
(90, '2026_07_19_060000_add_sil_days_to_leave_balances_table', 15),
(91, '2026_07_21_140000_upgrade_periods_for_payroll_workflow', 16),
(92, '2026_07_21_152742_add_validation_fields_to_periods_table', 17),
(93, '2026_07_21_180000_add_phase2_validation_fields_to_periods_table', 17),
(94, '2026_07_21_190000_add_review_and_finalize_fields_to_periods_table', 18),
(95, '2026_07_22_010000_set_gr8_tech_as_system_company', 19),
(96, '2026_07_22_020000_restore_multi_company_switching', 20),
(97, '2026_07_22_030000_add_period_id_to_payrolls_table', 21),
(98, '2026_07_22_040000_assign_default_employee_schedules', 22),
(99, '2026_07_22_050000_add_payroll_snapshot_breakdown_columns', 23),
(100, '2026_07_22_060000_add_loan_deduction_to_payrolls_table', 24),
(101, '2026_07_22_061000_add_attendance_correction_audit', 25);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` char(36) NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('456a4629-5130-41ee-8264-d473d5c5d485', 'App\\Notifications\\RequestStatusChanged', 'App\\Models\\Account', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '{\"request_type\":\"official_business\",\"request_type_label\":\"Official Business Request\",\"request_id\":\"8874f255-3d77-47d1-82b5-75a673a8a0a4\",\"status\":\"rejected\",\"status_label\":\"Rejected\",\"date\":\"Jul 23, 2026\",\"title\":\"Official Business Request Rejected\",\"message\":\"Your Official Business Request for Jul 23, 2026 was Rejected. Reason: sample\",\"icon\":\"fa-times-circle\",\"color\":\"red\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/attendance\\/official-business\"}', '2026-07-18 13:46:25', '2026-07-18 13:46:04', '2026-07-18 13:46:25'),
('781f9fc2-322e-460c-a888-0d74f7011d6f', 'App\\Notifications\\RequestStatusChanged', 'App\\Models\\Account', '7d966088-f3b6-4411-8034-7abe3ca89851', '{\"request_type\":\"leave\",\"request_type_label\":\"Leave Request\",\"request_id\":\"4c0b5374-d043-4367-aed9-4659ea4bd208\",\"status\":\"approved\",\"status_label\":\"Approved\",\"date\":\"Jul 25, 2026 - Jul 28, 2026\",\"title\":\"Leave Request Approved\",\"message\":\"Your Leave Request for Jul 25, 2026 - Jul 28, 2026 was Approved.\",\"icon\":\"fa-check-circle\",\"color\":\"green\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/attendance\\/leave-management\"}', '2026-07-19 20:42:43', '2026-07-19 20:31:30', '2026-07-19 20:42:43'),
('878f623f-821d-4387-9df3-b003c2445737', 'App\\Notifications\\RequestStatusChanged', 'App\\Models\\Account', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '{\"request_type\":\"leave\",\"request_type_label\":\"Leave Request\",\"request_id\":\"7fba9098-e85d-4be3-bb59-37461d5ea7d8\",\"status\":\"approved\",\"status_label\":\"Approved\",\"date\":\"Aug 13, 2026 - Aug 14, 2026\",\"title\":\"Leave Request Approved\",\"message\":\"Your Leave Request for Aug 13, 2026 - Aug 14, 2026 was Approved.\",\"icon\":\"fa-check-circle\",\"color\":\"green\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/attendance\\/leave-management\"}', '2026-07-20 10:14:41', '2026-07-19 22:57:28', '2026-07-20 10:14:41'),
('c15fdafd-0f7c-44d0-b887-fdb100016cd0', 'App\\Notifications\\RequestStatusChanged', 'App\\Models\\Account', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '{\"request_type\":\"official_business\",\"request_type_label\":\"Official Business Request\",\"request_id\":\"c0859fdf-ecfe-4c85-836a-b1fcdf04c7b9\",\"status\":\"approved\",\"status_label\":\"Approved\",\"date\":\"Jul 22, 2026\",\"title\":\"Official Business Request Approved\",\"message\":\"Your Official Business Request for Jul 22, 2026 was Approved.\",\"icon\":\"fa-check-circle\",\"color\":\"green\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/attendance\\/official-business\"}', '2026-07-18 13:44:33', '2026-07-18 13:44:22', '2026-07-18 13:44:33'),
('fb57b7a0-bc75-4774-aa67-1a4fe18bba56', 'App\\Notifications\\RequestStatusChanged', 'App\\Models\\Account', '7d966088-f3b6-4411-8034-7abe3ca89851', '{\"request_type\":\"official_business\",\"request_type_label\":\"Official Business Request\",\"request_id\":\"a8fa71ec-15fc-4cda-b119-869b47db759f\",\"status\":\"approved\",\"status_label\":\"Approved\",\"date\":\"Jul 22, 2026\",\"title\":\"Official Business Request Approved\",\"message\":\"Your Official Business Request for Jul 22, 2026 was Approved.\",\"icon\":\"fa-check-circle\",\"color\":\"green\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/attendance\\/official-business\"}', '2026-07-22 08:05:46', '2026-07-19 20:20:07', '2026-07-22 08:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `official_business_requests`
--

CREATE TABLE `official_business_requests` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `cutoff_period_key` varchar(40) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `reason` text NOT NULL,
  `is_full_day` tinyint(1) NOT NULL DEFAULT 1,
  `ob_start_time` time DEFAULT NULL,
  `ob_end_time` time DEFAULT NULL,
  `credited_hours` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `reviewed_by` char(36) DEFAULT NULL,
  `approved_by_role` varchar(20) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `attendance_record_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `official_business_requests`
--

INSERT INTO `official_business_requests` (`id`, `employee_id`, `date`, `cutoff_period_key`, `expires_at`, `reason`, `is_full_day`, `ob_start_time`, `ob_end_time`, `credited_hours`, `status`, `reviewed_at`, `rejection_reason`, `reviewed_by`, `approved_by_role`, `created_by`, `attendance_record_id`, `created_at`, `updated_at`) VALUES
('03dbf4a7-a06f-48d5-960f-4ac7f3346613', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-21', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'test', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-19 19:00:03', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '7d966088-f3b6-4411-8034-7abe3ca89851', 'cd2ae045-0bee-458f-b3ee-d839be610bdb', '2026-07-19 18:23:15', '2026-07-19 19:00:03'),
('1f2534db-95bd-4577-9152-ed1c671e52b1', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-07-16', NULL, NULL, 'TEST', 0, '10:33:00', '19:33:00', 9.00, 'approved', '2026-07-12 02:34:14', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '8e3cd314-4e58-45e4-b0d1-9eeb5c31fec8', '2026-07-12 02:34:14', '2026-07-12 02:34:14'),
('2bbcf159-aa6e-48e8-8a96-5ab0eb09f999', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-17', NULL, NULL, 'sample', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-17 04:19:52', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '95208808-ff8f-4233-bfc9-ea5de130c1f2', '2026-07-17 04:19:52', '2026-07-17 04:19:52'),
('2e3f624a-0545-4334-abb5-635797a257cf', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-24', NULL, NULL, 'TEST OB', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-18 19:20:32', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'd2ce2a0b-a750-4fa3-924a-02997c924057', '2026-07-18 19:20:32', '2026-07-18 19:20:32'),
('3c5bb48e-f4cc-46cf-8e91-8f728fdc3699', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-22', '2026-07-11_2026-07-25', '2026-07-12 00:44:58', 'TESTING Expired requests cannot be approved/rejected', 0, '09:33:00', '21:33:00', NULL, 'expired', NULL, NULL, NULL, NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-12 01:33:11', '2026-07-12 01:46:16'),
('443ff65c-5861-4a82-81b5-3fab7cf12f4b', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-18', '2026-07-11_2026-07-25', '2026-07-12 00:17:30', 'TEST', 0, '09:15:00', '17:15:00', NULL, 'expired', NULL, NULL, NULL, NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-12 01:15:54', '2026-07-12 01:20:53'),
('56a7b28f-0b7a-4d6d-8aa3-59a8dd046288', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-21', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'a', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-16 01:57:41', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'a7bd863f-1468-463c-917c-7364b95f2183', '2026-07-16 01:57:23', '2026-07-16 01:57:41'),
('6218c415-a878-45cb-8773-e70349871661', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-14', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'sample', 0, '08:39:00', '17:39:00', NULL, 'rejected', '2026-07-13 05:39:58', 'sample', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-13 05:39:37', '2026-07-13 05:39:58'),
('6865e5c1-137b-4edd-aa02-9a4576cb7d74', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-09', NULL, NULL, 'test', 1, NULL, NULL, NULL, 'rejected', '2026-07-08 12:47:49', 'test', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-08 12:46:45', '2026-07-08 12:47:49'),
('6c690525-a99f-47e4-bd48-ac086b21e94f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-17', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'testing overtime split', 0, '07:00:00', '17:00:00', 10.00, 'approved', '2026-07-12 00:41:57', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '46274cf1-5466-4f5d-b8ec-fa5916c9ba77', '2026-07-12 00:40:44', '2026-07-12 00:41:57'),
('6d2a0355-dbd3-4ba1-a413-4904ee16765e', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-15', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'end to end test', 0, '08:04:00', '17:52:00', 9.80, 'approved', '2026-07-11 21:54:27', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '8ee93749-6b11-471a-8ef7-3bbecbd0826f', '2026-07-11 21:52:40', '2026-07-11 21:54:27'),
('6e2958f0-1a21-4560-b861-5d4038c21350', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-13', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'sample', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-16 02:12:23', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'a527398b-083b-4b69-92d8-a7fccafaa667', '2026-07-16 02:12:02', '2026-07-16 02:12:23'),
('6e465a21-2c40-4bc1-aeb0-e3db7b890e2c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-10', NULL, NULL, 'test2', 1, NULL, NULL, NULL, 'rejected', '2026-07-22 03:33:17', 'Automatically rejected during payroll integrity correction: date is covered by approved vacation leave (2026-07-09 to 2026-07-12).', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '6294840f-64c3-4396-acad-a099368e9cd2', '2026-07-08 12:50:21', '2026-07-22 03:33:17'),
('77b5fce0-a59e-4e95-8959-8ecf255045ae', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-14', NULL, NULL, 'birthday ni rechelle', 1, NULL, NULL, NULL, 'rejected', '2026-07-09 11:26:43', 'bawal', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-09 07:19:14', '2026-07-09 11:26:43'),
('8874f255-3d77-47d1-82b5-75a673a8a0a4', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-23', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'fvff', 0, '09:45:00', '21:45:00', NULL, 'rejected', '2026-07-18 13:46:04', 'sample', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'hr', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-18 13:45:29', '2026-07-18 13:46:04'),
('967f4a3d-f533-4a50-9907-205de47e3369', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '2026-07-12', NULL, NULL, 'test', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-12 06:22:10', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '084c9407-a0d9-470c-927a-2c69723e6052', '2026-07-12 06:22:10', '2026-07-12 06:22:10'),
('a050289e-7260-411b-9b7d-5634e2756748', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-18', '2026-07-11_2026-07-25', '2026-07-12 00:26:35', 'SS', 0, '09:24:00', '21:25:00', NULL, 'expired', NULL, NULL, NULL, NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-12 01:22:17', '2026-07-12 01:27:02'),
('a35ce7d0-8610-4358-89d4-65c32960d885', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-09', NULL, NULL, 'sample', 1, NULL, NULL, NULL, 'rejected', '2026-07-08 12:27:28', 'test', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-08 12:23:25', '2026-07-08 12:27:28'),
('a8fa71ec-15fc-4cda-b119-869b47db759f', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-22', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'testing notif', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-19 20:20:06', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', '7d966088-f3b6-4411-8034-7abe3ca89851', '46d7fa3b-7183-4411-a237-0d2d1691f61b', '2026-07-19 20:19:43', '2026-07-19 20:20:06'),
('b8c4fbd8-7c18-45bd-86af-7fe8af17ac95', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-04', '2026-07-26_2026-08-10', NULL, 'Approved mock client visit for cutoff E2E testing', 1, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-22 08:58:30', NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', 'hr', '134402ed-fc04-4be7-a2c0-df8395f03a24', '4405730e-68fc-447c-8277-f0792a405670', '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('bdd735b6-41b1-44f7-b8bb-3f837f9efa4f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-23', '2026-07-11_2026-07-25', '2026-07-12 00:58:24', 'testing if scheduled expiry command executes correctly', 0, '09:56:00', '21:56:00', NULL, 'expired', NULL, NULL, NULL, NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-12 01:57:09', '2026-07-12 01:58:42'),
('c0859fdf-ecfe-4c85-836a-b1fcdf04c7b9', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-22', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'sample', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-18 13:44:22', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'b946e64a-521d-40e0-92a3-7ebdced29fd1', '2026-07-18 13:43:05', '2026-07-18 13:44:22'),
('d6b1028b-d659-48d5-93f0-7a9796adcf46', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-18', NULL, NULL, 'sa', 0, '08:00:00', '17:00:00', 8.00, 'approved', '2026-07-18 01:29:28', NULL, '134402ed-fc04-4be7-a2c0-df8395f03a24', 'hr', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'f1dc2661-687c-46c2-85df-7efd7fbb89e1', '2026-07-18 01:29:28', '2026-07-18 01:29:28'),
('e4e25be0-667e-4018-a978-cf2c295db4eb', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-11', NULL, NULL, 'tes3 - for reject', 1, NULL, NULL, NULL, 'rejected', '2026-07-08 12:52:11', 'test', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-08 12:51:41', '2026-07-08 12:52:11'),
('e536b9ca-8cb4-415e-82fb-00ab66f2cafc', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-09-05', '2026-08-26_2026-09-10', '2026-07-15 18:54:20', 'sample', 0, '08:00:00', '18:00:00', NULL, 'expired', NULL, NULL, NULL, NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-15 18:51:39', '2026-07-15 18:55:32'),
('e79e6ea6-2dc2-4515-bbaf-0cd58e6729c4', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '2026-07-08', NULL, NULL, 'Legacy manual OB attendance repaired and linked during payroll integrity audit.', 1, NULL, NULL, 8.00, 'approved', '2026-07-22 03:35:43', NULL, NULL, 'admin', NULL, '60e57548-f363-4b76-959c-59643f9533ee', '2026-07-22 03:35:44', '2026-07-22 03:36:23'),
('e8455b1b-1972-40fc-b04b-abdcdacc57ed', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-20', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'Retroactive and advance filing validation. TEST B', 0, '08:00:00', '17:00:00', 9.00, 'approved', '2026-07-14 09:23:17', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '8f21e40b-c4cf-4b9d-8377-9612deb2aca0', '2026-07-12 00:56:40', '2026-07-14 09:23:17'),
('ee9dcd2a-4a8d-48b6-aeef-98768a18fa8c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-11', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'Retroactive and advance filing validation. TESTA', 0, '08:00:00', '17:00:00', 9.00, 'approved', '2026-07-14 09:27:13', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '48e89d21-94c5-4119-a7e1-d9b0f5744cfe', '2026-07-12 00:53:31', '2026-07-14 09:27:13'),
('eedf564f-8afd-4256-b981-87c5a0d9d4ba', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-08-13', NULL, NULL, 'requwst', 1, NULL, NULL, NULL, 'rejected', '2026-07-08 15:27:28', 'test', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', NULL, '2026-07-08 15:26:45', '2026-07-08 15:27:28'),
('f5d7feff-c7c9-473e-9d61-6d3ebbb89f3c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-16', '2026-07-11_2026-07-25', '2026-07-26 15:59:59', 'sample', 0, '08:00:00', '12:00:00', 4.00, 'approved', '2026-07-14 07:08:35', NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'admin', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '1dd3cd25-7782-4cfe-8d9b-5369aa93778e', '2026-07-14 07:06:32', '2026-07-14 07:08:35');

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
  `status` enum('pending','approved','rejected','expired') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `overtime_requests`
--

INSERT INTO `overtime_requests` (`id`, `employee_id`, `date`, `start_time`, `end_time`, `hours`, `rate_multiplier`, `reason`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `expires_at`, `created_at`, `updated_at`) VALUES
('006c6b4b-990f-4c06-9809-46e5cb2f331c', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-22', '2026-07-22 09:00:00', '2026-07-22 12:16:00', 3.27, 1.25, 'test', 'expired', NULL, NULL, NULL, '2026-07-22 20:16:00', '2026-07-15 19:16:41', '2026-07-22 12:27:32'),
('19ca02d1-5ada-4ce0-84ce-fee8a35c3cea', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-20', '2026-07-20 09:00:00', '2026-07-20 12:14:00', 3.23, 1.25, 'reject test', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-15 19:14:44', NULL, '2026-07-20 20:14:00', '2026-07-15 19:14:16', '2026-07-15 19:14:44'),
('1caf6047-8072-46b7-b0ca-fd512cb762ed', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-14', '2026-07-13 17:35:00', '2026-07-14 05:35:00', 12.00, 1.25, 'sample', 'expired', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-13 05:36:42', NULL, NULL, '2026-07-13 05:36:15', '2026-07-13 05:36:42'),
('21dfd0cf-91d5-4031-8839-5c0c15453701', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '2026-08-02', '2026-08-02 00:00:00', '2026-08-02 09:00:00', 8.00, 1.25, 'Approved mock rest-day OT for cutoff E2E testing', 'approved', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('2743d7b5-11f5-4e82-a5e7-1827c65cc2cb', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-15', '2026-07-15 09:00:00', '2026-07-15 12:52:00', 3.87, 1.25, 's', 'expired', NULL, NULL, NULL, '2026-07-16 03:04:36', '2026-07-15 03:52:52', '2026-07-15 19:06:15'),
('2b42e608-e29c-47db-bf76-2895923d78b1', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-25', '2026-07-25 09:00:00', '2026-07-25 13:00:00', 4.00, 1.70, 'TEST', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 21:44:08', NULL, '2026-07-25 21:00:00', '2026-07-18 21:39:50', '2026-07-18 21:44:08'),
('4081567a-c61c-4b49-a25d-85fc773712ac', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-19', '2026-07-19 09:00:00', '2026-07-19 12:11:00', 3.18, 1.25, 'testt', 'expired', NULL, NULL, NULL, '2026-07-16 03:10:42', '2026-07-15 19:11:08', '2026-07-15 19:11:53'),
('47fb9082-0b68-4fa9-8433-35c05c110886', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-20', '2026-07-20 09:00:00', '2026-07-20 12:47:00', 3.78, 1.70, 'test rate multiplier', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 20:53:20', NULL, '2026-07-20 20:47:00', '2026-07-18 20:47:24', '2026-07-18 20:53:20'),
('4a31ccdc-4e4b-426e-b1ab-b26880ac6639', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-09', '2026-07-09 11:25:00', '2026-07-09 12:25:00', 1.00, 1.25, 'test', 'rejected', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-15 19:07:48', 'Automatically rejected during payroll integrity correction: date is covered by approved vacation leave (2026-07-09 to 2026-07-12).', NULL, '2026-07-09 11:25:43', '2026-07-22 03:33:17'),
('6efb3027-8b71-44eb-83e4-e1eb48544a05', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-23', '2026-07-23 09:00:00', '2026-07-23 12:00:00', 3.00, 1.25, 'TEST OT', 'approved', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-18 19:12:59', NULL, '2026-07-23 20:00:00', '2026-07-18 19:09:55', '2026-07-18 19:12:59'),
('83eac1f5-f919-4232-b56d-da559eda98ef', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '2026-07-30', '2026-07-30 09:00:00', '2026-07-30 11:00:00', 2.00, 1.25, 'Approved mock weekday OT for cutoff E2E testing', 'approved', '134402ed-fc04-4be7-a2c0-df8395f03a24', '2026-07-22 08:58:30', NULL, NULL, '2026-07-22 08:58:30', '2026-07-22 08:58:30'),
('8b525208-f5d9-4d2e-a9b0-5f70ca605bf5', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-21', '2026-07-21 09:00:00', '2026-07-21 13:00:00', 4.00, 1.25, 'reject test', 'rejected', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-15 19:15:35', 'rejecting request', '2026-07-21 21:00:00', '2026-07-15 19:15:21', '2026-07-15 19:15:35'),
('9cc84eb7-277e-4fa2-bcba-92dc549665c7', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-16', '2026-07-16 09:00:00', '2026-07-16 12:09:00', 3.15, 1.25, 'test', 'expired', NULL, NULL, NULL, '2026-07-16 03:09:03', '2026-07-15 19:09:31', '2026-07-15 19:10:25'),
('e4214312-3ae5-4aac-a5f6-7cba2f64f889', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-09', '2026-07-09 07:15:00', '2026-07-09 11:15:00', 4.00, 1.25, 'sample', 'rejected', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-15 19:07:53', 'Automatically rejected during payroll integrity correction: date is covered by approved vacation leave (2026-07-09 to 2026-07-12).', NULL, '2026-07-09 07:15:36', '2026-07-22 03:33:17'),
('f79b5ce6-7430-4ba1-b76c-d729f41d224f', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '2026-07-23', '2026-07-23 00:00:00', '2026-07-23 09:00:00', 9.00, 1.25, 'sample', 'pending', NULL, NULL, NULL, '2026-07-26 23:59:59', '2026-07-21 05:10:03', '2026-07-21 05:10:03');

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `payroll_id`, `employee_id`, `amount`, `status`, `payment_method`, `payment_reference`, `notes`, `processed_by`, `transaction_id`, `paid_at`, `meta`, `created_at`, `updated_at`) VALUES
(23, '09f05260-aa6c-45c0-8af1-e6b80665f67b', '62e12895-9295-4a40-a298-5b9903867cd7', 27818.95, 'completed', NULL, NULL, NULL, NULL, 'PAY-U9CC49KZMV', '2026-07-22 06:24:58', NULL, '2026-07-22 06:24:58', '2026-07-22 06:24:58'),
(24, '1d18aedd-97e0-4e95-887a-af4568ac7c01', '22ff1b14-2301-4a27-a022-6736b3c9f318', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-POQMWQOSEV', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(25, '6a35240b-2e27-4e4b-9044-efc2b23cb848', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-MEQHJYDVO5', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(26, '72785e6b-92a9-4654-ac25-07875671bc19', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-9EJSCB9QBN', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(27, '75480a86-0bbe-4ee0-9ec0-8321d86514a0', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-HJ5TBN2QAS', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(28, '9d3946f4-dbbd-4f52-ae52-0f459d2ececb', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-R64KFGDQ1Z', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(29, 'b5cd9427-cfb1-4d04-a675-68e84e056c19', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-GHISJEFHGF', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(30, 'c880595e-48e8-4409-89af-8a6813f9ad45', '243e7606-9542-4c71-867c-05bea5e658d3', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-BDF1EEEORR', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(31, 'd77d8e18-4685-4583-8b4b-bd2683417a52', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', 26934.95, 'completed', NULL, NULL, NULL, NULL, 'PAY-ATWQNBHJKX', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(32, 'e0636abf-bac0-438d-9946-a521940a8c7a', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', 1911.52, 'completed', NULL, NULL, NULL, NULL, 'PAY-TAX7LPJ6AC', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(33, 'f635b4d1-67ea-4649-a13a-ff3667cd236b', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', 0.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-GRMYAVZZGV', '2026-07-22 06:24:59', NULL, '2026-07-22 06:24:59', '2026-07-22 06:24:59'),
(34, '76438fef-2ecb-4403-b44c-0012ce37f417', '243e7606-9542-4c71-867c-05bea5e658d3', 10425.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-3GSFZEMJUK', '2026-07-22 13:10:48', NULL, '2026-07-22 13:10:48', '2026-07-22 13:10:48'),
(35, '246c97af-607e-4406-966a-841a36854054', '62e12895-9295-4a40-a298-5b9903867cd7', 35441.60, 'completed', NULL, NULL, NULL, NULL, 'PAY-ETXXXEUPRP', '2026-07-22 13:11:07', NULL, '2026-07-22 13:11:07', '2026-07-22 13:11:07'),
(36, '5cd08004-4e13-490c-8963-ff0dcacb0db8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 10425.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-N1CCB4DSWH', '2026-07-22 13:11:07', NULL, '2026-07-22 13:11:07', '2026-07-22 13:11:07'),
(37, '5dff0877-036c-4a6b-acf0-cf657f6e9266', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', 7363.46, 'completed', NULL, NULL, NULL, NULL, 'PAY-7CUT9NNMTT', '2026-07-22 13:11:07', NULL, '2026-07-22 13:11:07', '2026-07-22 13:11:07'),
(38, '73244976-9a83-48e5-86a9-a4ab1eb6ca21', '22ff1b14-2301-4a27-a022-6736b3c9f318', 7450.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-USIBQLRGMN', '2026-07-22 13:11:07', NULL, '2026-07-22 13:11:07', '2026-07-22 13:11:07'),
(39, '900da906-959e-4ce7-942a-71b8babcfaea', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', 10425.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-CSA8FP3SFH', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08'),
(40, '94a57e79-9dfc-4d24-b324-d384bc01fea3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', 10425.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-M0O8PLY6QF', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08'),
(41, '9ecf90d9-3c94-43d8-bc3f-567e68d621c3', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', 5598.08, 'completed', NULL, NULL, NULL, NULL, 'PAY-PCDCG2FR10', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08'),
(42, 'a38a8239-46e3-4862-bf25-31213ae21e59', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', 7421.15, 'completed', NULL, NULL, NULL, NULL, 'PAY-3RLMDDXIX6', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08'),
(43, 'c81b2f5e-df12-4d32-852f-9dda0785e689', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', 36273.60, 'completed', NULL, NULL, NULL, NULL, 'PAY-98FTT3RLML', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08'),
(44, 'f97d6d0c-cc95-459f-8d6c-b87f694e9427', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 8880.00, 'completed', NULL, NULL, NULL, NULL, 'PAY-EWGUCPIN8Q', '2026-07-22 13:11:08', NULL, '2026-07-22 13:11:08', '2026-07-22 13:11:08');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `period_id` char(36) DEFAULT NULL,
  `company_id` char(36) DEFAULT NULL,
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
  `worked_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `late_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `undertime_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `late_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `undertime_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `absence_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `night_differential_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rest_day_premium_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sick_leave_days` int(11) NOT NULL DEFAULT 0,
  `sick_leave_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unpaid_leave_days` int(11) NOT NULL DEFAULT 0,
  `unpaid_leave_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `loan_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sss` decimal(12,2) DEFAULT NULL,
  `phic` decimal(12,2) DEFAULT NULL,
  `hdmf` decimal(12,2) DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unpaid_leave_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
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

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `employee_id`, `period_id`, `company_id`, `pay_period_start`, `pay_period_end`, `basic_salary`, `holiday_basic_pay`, `holiday_premium`, `special_holiday_premium`, `regular_holiday_days`, `special_holiday_days`, `overtime_hours`, `overtime_rate`, `overtime_pay`, `scheduled_hours`, `worked_hours`, `late_minutes`, `undertime_minutes`, `late_deduction`, `undertime_deduction`, `absence_deduction`, `night_differential_hours`, `night_differential_rate`, `night_differential_pay`, `rest_day_premium_pay`, `bonuses`, `other_earnings`, `sick_leave_days`, `sick_leave_pay`, `unpaid_leave_days`, `unpaid_leave_pay`, `allowances`, `deductions`, `other_deductions`, `loan_deduction`, `sss`, `phic`, `hdmf`, `tax_amount`, `unpaid_leave_deduction`, `gross_pay`, `net_pay`, `status`, `rejection_reason`, `processed_at`, `paid_by`, `payment_reference`, `payslip_file`, `paid_at`, `rejected_at`, `rejected_by`, `approved_by`, `approved_at`, `notes`, `created_at`, `updated_at`) VALUES
('03c40549-5995-467a-a735-062b9385a01d', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 12500.00, 0.00, 0.00, 0.00, 0, 0, 3.23, 177.56, 573.53, 77.67, 0.00, 0, 0, 0.00, 0.00, 0.00, 72.07, 14.21, 1023.75, 0.00, 0.00, 0.00, 14, 8076.88, 4, 0.00, 13758.68, 8928.12, 0.00, 0.00, 1250.00, 625.00, 200.00, 1053.44, 2307.68, 27855.96, 17874.40, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 22:59:49', NULL, '2026-07-18 22:39:34', '2026-07-21 08:45:49'),
('09f05260-aa6c-45c0-8af1-e6b80665f67b', '62e12895-9295-4a40-a298-5b9903867cd7', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 96.00, 0.00, 0, 0, 0.00, 0.00, 9600.00, 0.00, 9.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 20000.00, 10950.00, 0.00, 0.00, 450.00, 450.00, 450.00, 1471.05, 0.00, 40240.00, 27818.95, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:58', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:58'),
('14b1ef71-b615-46a9-b108-a9b9330d21af', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 7500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 90.14, 0.00, 8.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 7.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 2884.60, 1325.00, 0.00, 0.00, 750.00, 375.00, 200.00, 0.00, 0.00, 10384.60, 9059.60, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('17ca2a34-9dd7-434c-967c-84b257adc2b1', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 8.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 10911.55, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('1ca9ec62-6b5d-4fa6-afa6-0252fbaf11c9', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 16.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 10911.55, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('1d18aedd-97e0-4e95-887a-af4568ac7c01', '22ff1b14-2301-4a27-a022-6736b3c9f318', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 9000.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('246c97af-607e-4406-966a-841a36854054', '62e12895-9295-4a40-a298-5b9903867cd7', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 104.00, 96.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 9.00, 0.00, 0.00, 0.00, 0.00, 1, 1000.00, 0, 0.00, 20000.00, 1350.00, 0.00, 0.00, 450.00, 450.00, 450.00, 3208.40, 0.00, 40000.00, 35441.60, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:07', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:07'),
('45612c22-3bb2-4ce0-bc51-87c0c994cdfb', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', NULL, NULL, '2026-07-01', '2026-07-15', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 64.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 4319.24, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 8142.31, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('552667fb-6c93-40d8-9343-2af0cfccb500', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 9.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 9.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 20000.00, 1350.00, 0.00, 0.00, 450.00, 450.00, 450.00, 3208.40, 0.00, 40000.00, 35441.60, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('5ad5f9f3-9c67-45b7-92d2-891a3ea8876c', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', NULL, NULL, '2026-07-01', '2026-07-15', 7500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 90.14, 0.00, 8.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 7.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 2884.60, 1325.00, 0.00, 0.00, 750.00, 375.00, 200.00, 0.00, 0.00, 10384.60, 9059.60, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('5b8a776e-43df-4eea-8c42-6602d38e0ba5', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', NULL, NULL, '2026-07-01', '2026-07-15', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 8.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 10911.55, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('5cd08004-4e13-490c-8963-ff0dcacb0db8', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 104.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 12500.00, 10425.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:07', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:07'),
('5dff0877-036c-4a6b-acf0-cf657f6e9266', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 104.00, 103.00, 0, 60, 0.00, 86.54, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 1636.54, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 9000.00, 7363.46, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:07', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:07'),
('6a35240b-2e27-4e4b-9044-efc2b23cb848', 'b19c96de-91d9-4bcd-a0b6-2010bc7dae8c', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 9000.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 9000.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('72785e6b-92a9-4654-ac25-07875671bc19', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 3.85, 377, 0, 892.55, 0.00, 11363.60, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 2, 1153.84, 0, 0.00, 1153.84, 13653.84, 0.00, 0.00, 1250.00, 147.69, 0.00, 0.00, 0.00, 13653.84, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('73244976-9a83-48e5-86a9-a4ab1eb6ca21', '22ff1b14-2301-4a27-a022-6736b3c9f318', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 104.00, 104.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 9000.00, 7450.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:07', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:07'),
('74ab1146-82e3-43ee-9571-11e5ca597845', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', NULL, NULL, '2026-07-01', '2026-07-15', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 9.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 20000.00, 1350.00, 0.00, 0.00, 450.00, 450.00, 450.00, 3208.40, 0.00, 40000.00, 35441.60, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('75480a86-0bbe-4ee0-9ec0-8321d86514a0', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 80.00, 0.00, 0, 0, 0.00, 0.00, 11363.60, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 12840.91, 0.00, 0.00, 1250.00, 227.31, 0.00, 0.00, 0.00, 12840.91, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('76438fef-2ecb-4403-b44c-0012ce37f417', '243e7606-9542-4c71-867c-05bea5e658d3', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 104.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 12500.00, 10425.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:10:49', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:10:49'),
('856f3e2c-7065-4d81-a48b-8055ec931823', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', NULL, NULL, '2026-07-01', '2026-07-15', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16106.80, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('8f114348-d277-4317-9ba6-3385fee16328', '62e12895-9295-4a40-a298-5b9903867cd7', NULL, NULL, '2026-07-01', '2026-07-15', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 96.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18522.71, 16447.71, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('900da906-959e-4ce7-942a-71b8babcfaea', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 104.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 12500.00, 10425.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08'),
('94a57e79-9dfc-4d24-b324-d384bc01fea3', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 104.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 12500.00, 10425.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08'),
('9d3946f4-dbbd-4f52-ae52-0f459d2ececb', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 12500.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 12500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('9ecf90d9-3c94-43d8-bc3f-567e68d621c3', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 7500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 90.15, 0.00, 104.00, 96.00, 0, 0, 0.00, 0.00, 576.92, 0.00, 7.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 1901.92, 0.00, 0.00, 750.00, 375.00, 200.00, 0.00, 0.00, 7500.00, 5598.08, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08'),
('a38a8239-46e3-4862-bf25-31213ae21e59', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 104.00, 103.67, 20, 0, 28.85, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 1578.85, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 9000.00, 7421.15, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08'),
('a906b92c-533e-4649-9515-c7f48a7344e3', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16106.80, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:33', '2026-07-18 22:39:33'),
('aa84e6b2-3f43-4cf3-83a7-7fa50fd044d9', '243e7606-9542-4c71-867c-05bea5e658d3', NULL, NULL, '2026-07-01', '2026-07-15', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16106.80, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('b359e10c-f7eb-426e-8d29-63b88eef0207', '22ff1b14-2301-4a27-a022-6736b3c9f318', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 10911.55, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:33', '2026-07-18 22:39:33'),
('b5cd9427-cfb1-4d04-a675-68e84e056c19', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 10400.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 125.00, 0.00, 96.00, 0.00, 0, 0, 0.00, 0.00, 9600.00, 0.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 10400.00, 0.00, 0.00, 800.00, 0.00, 0.00, 0.00, 0.00, 10400.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('b6011ec7-a2a6-4879-b21e-0122b1a79669', 'b5b39e80-36cc-4f35-abaf-20272f5a461c', NULL, NULL, '2026-07-01', '2026-07-15', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 32.45, 0.00, 0, 0, 0.00, 0.00, 0.00, 3.12, 14.21, 44.32, 0.00, 0.00, 0.00, 6, 3461.52, 0, 0.00, 9143.32, 8893.16, 0.00, 0.00, 1250.00, 625.00, 200.00, 128.20, 0.00, 21687.64, 12666.28, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('ba3fb2d5-6e90-4805-bcb3-3c931e05a62a', 'c413f98e-d91b-4c61-b4d4-eb00e40a34b1', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16106.80, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('c81b2f5e-df12-4d32-852f-9dda0785e689', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 104.00, 112.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 9.00, 0.00, 1040.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 20000.00, 1350.00, 0.00, 0.00, 450.00, 450.00, 450.00, 3416.40, 0.00, 41040.00, 36273.60, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08'),
('c880595e-48e8-4409-89af-8a6813f9ad45', '243e7606-9542-4c71-867c-05bea5e658d3', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 12500.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 12500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 12500.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('cc9748d1-5ba9-4b32-82b2-fa488abdd8c1', '243e7606-9542-4c71-867c-05bea5e658d3', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16106.80, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('d5280804-d240-4300-b476-e41371b69c90', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 10400.00, 0.00, 0.00, 0.00, 0, 0, 7.78, 170.00, 1322.60, 27.90, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 10.00, 0.00, 0.00, 0.00, 0.00, 4, 3200.00, 2, 0.00, 7200.00, 6595.00, 0.00, 0.00, 1050.00, 520.00, 200.00, 0.00, 1600.00, 18922.60, 12327.60, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-21 08:45:49'),
('d77d8e18-4685-4583-8b4b-bd2683417a52', '41c930c9-40d9-4e8b-9575-87e1ae3c8ab7', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 20000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 112.50, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 10400.00, 0.00, 9.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 20000.00, 11750.00, 0.00, 0.00, 450.00, 450.00, 450.00, 1315.05, 0.00, 40000.00, 26934.95, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('de6fea9b-9e9d-4753-b2a5-6ed651a6af7f', '0c6fcadb-5d42-453e-b85e-04df54d4b42b', NULL, NULL, '2026-07-01', '2026-07-15', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 80.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2075.00, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18522.71, 16447.71, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('e0636abf-bac0-438d-9946-a521940a8c7a', '75282d62-51f2-41a9-a5e3-6b4b6838a5f7', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.18, 0.00, 72.00, 8.00, 0, 0, 0.00, 0.00, 5538.48, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 7088.48, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 9000.00, 1911.52, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('ea2771b7-d752-4b2a-94ea-2ece4fd9b905', '62e12895-9295-4a40-a298-5b9903867cd7', '7038c856-c975-4ce2-b896-44ef7db776e7', NULL, '2026-07-11', '2026-07-25', 12500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 177.56, 0.00, 7.50, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 14.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 5681.80, 2110.51, 0.00, 0.00, 1250.00, 625.00, 200.00, 0.00, 0.00, 18181.80, 16071.29, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-18 22:39:34', '2026-07-18 22:39:34'),
('f635b4d1-67ea-4649-a13a-ff3667cd236b', 'b5aecc58-69dc-4d2c-b435-d8f13a9416ef', 'bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-06-26', '2026-07-10', 7500.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 90.15, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 7499.96, 0.00, 7.21, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 7500.00, 0.00, 0.00, 0.04, 0.00, 0.00, 0.00, 0.00, 7500.00, 0.00, 'paid', NULL, '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 06:24:59', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', NULL, '2026-07-22 04:45:30', '2026-07-22 06:24:59'),
('f65b3f65-e1a0-4305-ac27-fd08c2cfd9f3', '22ff1b14-2301-4a27-a022-6736b3c9f318', NULL, NULL, '2026-07-01', '2026-07-15', 9000.00, 0.00, 0.00, 0.00, 0, 0, 0.00, 108.17, 0.00, 104.00, 0.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 8.65, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 3461.55, 1550.00, 0.00, 0.00, 900.00, 450.00, 200.00, 0.00, 0.00, 12461.55, 10911.55, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-07-21 06:11:38', '2026-07-21 06:11:38'),
('f97d6d0c-cc95-459f-8d6c-b87f694e9427', '3470aa7e-87e2-4ca0-a161-bc0403b7e936', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '2026-07-26', '2026-08-10', 10400.00, 0.00, 0.00, 0.00, 0, 0, 2.00, 125.00, 250.00, 104.00, 106.00, 0, 0, 0.00, 0.00, 0.00, 0.00, 10.00, 0.00, 0.00, 0.00, 0.00, 0, 0.00, 0, 0.00, 0.00, 1770.00, 0.00, 0.00, 1050.00, 520.00, 200.00, 0.00, 0.00, 10650.00, 8880.00, 'paid', NULL, '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-22 13:11:08', NULL, NULL, '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', NULL, '2026-07-22 12:56:16', '2026-07-22 13:11:08');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_templates`
--

CREATE TABLE `payroll_templates` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `monthly_rate` decimal(10,2) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `overtime_rate` decimal(10,2) DEFAULT NULL,
  `night_differential_rate` decimal(10,2) DEFAULT NULL,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `sss` decimal(10,2) DEFAULT NULL,
  `phic` decimal(10,2) DEFAULT NULL,
  `hdmf` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payroll_templates`
--

INSERT INTO `payroll_templates` (`id`, `company_id`, `name`, `description`, `monthly_rate`, `daily_rate`, `hourly_rate`, `overtime_rate`, `night_differential_rate`, `allowances`, `deductions`, `sss`, `phic`, `hdmf`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
('3224e231-8e29-426e-9741-9c404c4f7d30', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEMPLATE B', 'assigned directly to Employee', NULL, NULL, NULL, NULL, NULL, 700.00, NULL, 10.00, NULL, NULL, 1, '2026-07-16 12:28:14', '2026-07-17 04:56:18', '2026-07-17 04:56:18'),
('7dc459b8-c106-4258-ab48-b0ed18b63305', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEMPLATE C', 'selected manually during payroll generation', NULL, NULL, NULL, NULL, NULL, 1000.00, 100.00, NULL, NULL, NULL, 1, '2026-07-16 12:30:32', '2026-07-17 04:58:04', '2026-07-17 04:58:04'),
('89ac4b5c-d460-45a5-9ee6-4f60fe487fae', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'SAMPLE TESTING EDIT', 'SAMPLE TESTING EDIT', 18000.00, 750.00, 85.00, 50.00, 50.00, 1000.00, NULL, 450.00, 200.00, 200.00, 1, '2026-07-16 12:23:58', '2026-07-16 12:26:42', NULL),
('8ee5023e-a175-4c19-a1b7-b7f6569e589f', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'template d', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-16 12:30:46', '2026-07-16 12:30:46', NULL),
('a6b968a2-ada1-4912-b2d8-623cb3e15f1f', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'Regular Monthly Employee', 'Test payroll template for monthly employees', 25000.00, 1136.36, 142.05, 177.56, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-07-17 10:35:28', '2026-07-17 10:35:28', NULL),
('edaee21d-7e7f-45b2-bd26-7a7345bb70d3', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'REGULAR SALARY', NULL, 40000.00, 800.00, 100.00, 120.00, NULL, 1000.00, NULL, 450.00, 260.00, 200.00, 1, '2026-07-17 09:14:23', '2026-07-17 09:14:23', NULL),
('f32ef3fb-c182-438c-bfa6-e2dfb44b9734', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEMPLATE A', 'assigned to Position', 40000.00, 800.00, 90.00, 120.00, 0.00, 20000.00, NULL, 450.00, 450.00, 450.00, 1, '2026-07-16 12:27:35', '2026-07-17 09:09:46', NULL),
('f8785372-96be-4bc3-a3f4-22ce58ad101a', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEMPLATE B', 'TEMPLATE B', 1000.00, 100.00, 100.00, 100.00, 100.00, 100.00, NULL, 100.00, 100.00, 100.00, 1, '2026-07-16 12:28:15', '2026-07-16 12:32:47', '2026-07-16 12:32:47'),
('fda50283-0ebd-4e45-b67e-1750183ada9d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'TEMPLATE C', 'selected manually during payroll generation', 1000.00, 1000.00, 1000.00, 1000.00, 1000.00, 1000.00, NULL, 100.00, 100.00, 100.00, 1, '2026-07-16 12:30:31', '2026-07-16 12:32:43', '2026-07-16 12:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `previous_period_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `period_month` tinyint(3) UNSIGNED DEFAULT NULL,
  `period_year` smallint(5) UNSIGNED DEFAULT NULL,
  `period_no` tinyint(3) UNSIGNED DEFAULT NULL,
  `period_type` varchar(30) NOT NULL DEFAULT 'regular',
  `processing_type` varchar(30) NOT NULL DEFAULT 'regular',
  `payroll_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `working_days` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `attendance_validated_at` timestamp NULL DEFAULT NULL,
  `attendance_validated_by` char(36) DEFAULT NULL,
  `leave_validated_at` timestamp NULL DEFAULT NULL,
  `leave_validated_by` char(36) DEFAULT NULL,
  `ob_validated_at` timestamp NULL DEFAULT NULL,
  `ob_validated_by` char(36) DEFAULT NULL,
  `overtime_validated_at` timestamp NULL DEFAULT NULL,
  `overtime_validated_by` char(36) DEFAULT NULL,
  `validation_notes` text DEFAULT NULL,
  `ready_at` timestamp NULL DEFAULT NULL,
  `ready_by` char(36) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` char(36) DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `finalized_by` char(36) DEFAULT NULL,
  `department_id` char(36) DEFAULT NULL,
  `employee_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_ids`)),
  `created_by` varchar(255) NOT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`id`, `company_id`, `previous_period_id`, `name`, `description`, `period_month`, `period_year`, `period_no`, `period_type`, `processing_type`, `payroll_date`, `start_date`, `end_date`, `working_days`, `status`, `attendance_validated_at`, `attendance_validated_by`, `leave_validated_at`, `leave_validated_by`, `ob_validated_at`, `ob_validated_by`, `overtime_validated_at`, `overtime_validated_by`, `validation_notes`, `ready_at`, `ready_by`, `reviewed_at`, `reviewed_by`, `finalized_at`, `finalized_by`, `department_id`, `employee_ids`, `created_by`, `locked_at`, `locked_by`, `created_at`, `updated_at`) VALUES
('0e8e9ca9-895b-44dd-9a26-15800ec70f05', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '7038c856-c975-4ce2-b896-44ef7db776e7', 'August 2026 - Period 1', NULL, 8, 2026, 1, 'regular', 'regular', '2026-08-15', '2026-07-26', '2026-08-10', 13, 'locked', '2026-07-22 12:47:02', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 12:47:15', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 12:47:25', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 12:47:38', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, '2026-07-22 12:47:38', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, '[\"75282d62-51f2-41a9-a5e3-6b4b6838a5f7\",\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"0c6fcadb-5d42-453e-b85e-04df54d4b42b\",\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"22ff1b14-2301-4a27-a022-6736b3c9f318\",\"b19c96de-91d9-4bcd-a0b6-2010bc7dae8c\",\"b5aecc58-69dc-4d2c-b435-d8f13a9416ef\",\"c413f98e-d91b-4c61-b4d4-eb00e40a34b1\",\"243e7606-9542-4c71-867c-05bea5e658d3\",\"62e12895-9295-4a40-a298-5b9903867cd7\",\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\"]', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 13:00:35', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 12:40:40', '2026-07-22 13:00:35'),
('7038c856-c975-4ce2-b896-44ef7db776e7', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', NULL, 'July 2026 - Period 2', 'sample', 7, 2026, 2, 'regular', 'regular', '2026-07-31', '2026-07-11', '2026-07-25', 10, 'for_validation', '2026-07-21 16:07:13', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-21 16:07:17', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-21 16:07:24', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-21 16:07:28', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, '2026-07-21 16:07:28', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, NULL, NULL, NULL, '[\"75282d62-51f2-41a9-a5e3-6b4b6838a5f7\",\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"0c6fcadb-5d42-453e-b85e-04df54d4b42b\",\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"22ff1b14-2301-4a27-a022-6736b3c9f318\",\"b19c96de-91d9-4bcd-a0b6-2010bc7dae8c\",\"b5aecc58-69dc-4d2c-b435-d8f13a9416ef\",\"c413f98e-d91b-4c61-b4d4-eb00e40a34b1\",\"243e7606-9542-4c71-867c-05bea5e658d3\",\"62e12895-9295-4a40-a298-5b9903867cd7\",\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\"]', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-21 15:45:33', '2026-07-22 00:44:59'),
('b64ebf8e-a990-4d9e-a6d7-6442cf06f2de', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', '0e8e9ca9-895b-44dd-9a26-15800ec70f05', 'August 2026 - Period 2', NULL, 8, 2026, 2, 'regular', 'regular', '2026-08-30', '2026-08-11', '2026-08-25', 13, 'for_validation', NULL, NULL, '2026-07-23 01:54:10', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:54:14', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-23 01:54:19', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"75282d62-51f2-41a9-a5e3-6b4b6838a5f7\",\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"0c6fcadb-5d42-453e-b85e-04df54d4b42b\",\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"22ff1b14-2301-4a27-a022-6736b3c9f318\",\"b19c96de-91d9-4bcd-a0b6-2010bc7dae8c\",\"b5aecc58-69dc-4d2c-b435-d8f13a9416ef\",\"c413f98e-d91b-4c61-b4d4-eb00e40a34b1\",\"243e7606-9542-4c71-867c-05bea5e658d3\",\"62e12895-9295-4a40-a298-5b9903867cd7\",\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\"]', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, NULL, '2026-07-23 01:53:08', '2026-07-23 06:26:49'),
('bafdd200-ee1d-44b9-95cd-5a3437df0c1d', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', NULL, 'July 2026 - Period 1', 'test', 7, 2026, 1, 'regular', 'regular', '2026-07-15', '2026-06-26', '2026-07-10', 11, 'locked', '2026-07-22 03:46:35', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 03:46:48', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 03:47:02', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 03:47:16', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Returned for source correction and regeneration: fixing records\r\nAutomatically returned for correction on 2026-07-22: payroll audit found missing attendance and conflicting leave/OB/overtime source records.', '2026-07-22 03:47:16', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 04:48:43', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', NULL, '[\"75282d62-51f2-41a9-a5e3-6b4b6838a5f7\",\"41c930c9-40d9-4e8b-9575-87e1ae3c8ab7\",\"0c6fcadb-5d42-453e-b85e-04df54d4b42b\",\"b5b39e80-36cc-4f35-abaf-20272f5a461c\",\"22ff1b14-2301-4a27-a022-6736b3c9f318\",\"b19c96de-91d9-4bcd-a0b6-2010bc7dae8c\",\"b5aecc58-69dc-4d2c-b435-d8f13a9416ef\",\"c413f98e-d91b-4c61-b4d4-eb00e40a34b1\",\"243e7606-9542-4c71-867c-05bea5e658d3\",\"62e12895-9295-4a40-a298-5b9903867cd7\",\"3470aa7e-87e2-4ca0-a161-bc0403b7e936\"]', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 05:54:44', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '2026-07-22 00:25:43', '2026-07-22 05:54:44');

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `payroll_template_id` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `company_id`, `name`, `code`, `description`, `level`, `department_id`, `min_salary`, `max_salary`, `is_active`, `requirements`, `responsibilities`, `created_at`, `updated_at`, `payroll_template_id`) VALUES
('019f6e6f-f33e-721f-8d13-8acefe0a683a', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'NA', 'NA', NULL, NULL, '1e263b81-13ee-45be-8a66-54cc1afa344e', NULL, NULL, 0, NULL, NULL, '2026-07-17 04:57:38', '2026-07-17 21:41:23', NULL),
('019f6f4e-7b6c-7278-b96a-fe60417a2c02', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'Executive Manager', 'EXM', NULL, 'Senior', '1e263b81-13ee-45be-8a66-54cc1afa344e', 40000.00, 50000.00, 1, '[]', '[]', '2026-07-17 09:00:42', '2026-07-17 11:48:43', 'a6b968a2-ada1-4912-b2d8-623cb3e15f1f'),
('019f6f65-6d5d-71d3-aff3-e0821ea1e862', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'Hiring Manager', 'HRM', NULL, 'Mid', '21b6bdd2-0e9b-4176-9d65-452d7779f77f', 15000.00, 20000.00, 1, '[]', '[]', '2026-07-17 09:25:45', '2026-07-17 11:22:30', 'a6b968a2-ada1-4912-b2d8-623cb3e15f1f'),
('019f721a-66b5-716c-85a2-9695927a64d1', '1c7860cf-53f1-4852-a2a6-d9b02030f1ae', 'Software Engineer', 'SE', NULL, 'Mid', '1e263b81-13ee-45be-8a66-54cc1afa344e', 30000.00, 40000.00, 1, '[]', '[]', '2026-07-17 22:02:40', '2026-07-17 22:02:40', NULL);

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
('9ZqvoodaVeUoJ4POtQy9OArwXQPSWHCKz68VYEhy', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiOW1ZT0lRN0F1d2RsUHh3MjVPa1o3VXhad21JZlE5TzZHZlFtT0IxRSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oci9pbmJveC9xdWljayI7czo1OiJyb3V0ZSI7czoxNDoiaHIuaW5ib3gucXVpY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czozNjoiMWExZDJiMTItZDNiNS00MzZkLWFhOTQtNzYyYWI4ZjVmYmQ0IjtzOjE4OiJjdXJyZW50X2NvbXBhbnlfaWQiO3M6MzY6ImM1NzUxYjA3LTM1YzYtNGYwNi05NDQyLTUxYjNmZThiMDM0NyI7fQ==', 1784784928),
('nqBNvobedAPy9Xw8CKg7xj1P4EWQVIW10Gc9BxyM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSEFjWFpBYjloZFN3VUl0cTh4TkNUMDVOOTBydTlxQkZubDJZS1lydSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hyL2luYm94L3F1aWNrIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1784791846),
('ozJRdRsibGAWCynZ8H1uNdd54Exr9qOanVcDHJtM', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoibUp5MG9oQkxEeFc0MGpmVHBsd2VoaE9YMlNhUjRDMXUwTlUzZGE2ZyI7czozOiJ1cmwiO2E6MDp7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjM2OiJiOTAxNWM4Yy0zNDhmLTQ3OTMtOThiMi01ZDNhYzNkZDJhYTkiO3M6MTg6ImN1cnJlbnRfY29tcGFueV9pZCI7czozNjoiYzU3NTFiMDctMzVjNi00ZjA2LTk0NDItNTFiM2ZlOGIwMzQ3Ijt9', 1784791799),
('RM6ozo7BhU6j0Z4ebxEU0WATQvwpgAToIZEn78jS', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoid3c1TU9jYklaSU1WNFNMSVpBVk1vYjBnODVuQ1doZG9vUGpSUFo2ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9oci9pbmJveC9xdWljayI7czo1OiJyb3V0ZSI7czoxNDoiaHIuaW5ib3gucXVpY2siO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7czozNjoiMWExZDJiMTItZDNiNS00MzZkLWFhOTQtNzYyYWI4ZjVmYmQ0IjtzOjE4OiJjdXJyZW50X2NvbXBhbnlfaWQiO3M6MzY6IjFjNzg2MGNmLTUzZjEtNDg1Mi1hMmE2LWQ5YjAyMDMwZjFhZSI7fQ==', 1784791842),
('TN0B4c3aOvILWUD9uD9ejP2eDcxCJQQne6Kike0u', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRjV5QUdvazRtclMyZXZHRXJuSjJNbHVXWFV5MTZadGlDSFJkdFVkTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtzOjM2OiIxYTFkMmIxMi1kM2I1LTQzNmQtYWE5NC03NjJhYjhmNWZiZDQiO3M6MTg6ImN1cnJlbnRfY29tcGFueV9pZCI7czozNjoiYzU3NTFiMDctMzVjNi00ZjA2LTk0NDItNTFiM2ZlOGIwMzQ3Ijt9', 1784785901),
('yxbOsvBFkJ2MfNUsd7g5Rz3Tn0NhTrpUVOrRRxUM', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoibHEzZVBuYlphMFZIR3E2cXU4TzkwZUdENmJCSFZCUkpFVkJPa1NyUyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784786799);

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
('497e0289-7ef0-4b3c-8c6a-db49086ebd47', '20% Bracket', '₱33,334 – ₱66,667 - ₱1,875 + 20% of the excess over ₱33,333', 33334.00, 66667.00, 20.00, 1875.00, 33333.00, 3, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12'),
('7e8745cc-59be-4c8d-a030-850f58a25ff4', 'Exempt', 'Up to ₱20,833 - Exempt from tax', 0.00, 20833.00, 0.00, 0.00, 0.00, 1, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12'),
('94dcbc14-3d77-4b4b-9f4a-991f396e5ac4', '15% Bracket', '₱20,834 – ₱33,333 - 15% of the excess over ₱20,833', 20834.00, 33333.00, 15.00, 0.00, 20833.00, 2, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12'),
('ae0c65e0-a81c-4a3f-b2b8-4b2bdf1fb869', '25% Bracket', '₱66,668 – ₱166,667 - ₱8,541.80 + 25% of the excess over ₱66,667', 66668.00, 166667.00, 25.00, 8541.80, 66667.00, 4, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12'),
('ba585b4b-f5e8-4e3d-8498-cbfa44667938', '30% Bracket', '₱166,668 – ₱666,667 - ₱33,541.80 + 30% of the excess over ₱166,667', 166668.00, 666667.00, 30.00, 33541.80, 166667.00, 5, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12'),
('c4228f8c-a3e5-47f1-98e7-3f535ec34b87', '35% Bracket', 'Over ₱666,667 - ₱183,541.80 + 35% of the excess over ₱666,667', 666668.00, NULL, 35.00, 183541.80, 666667.00, 6, 1, '2026-01-01', NULL, '2026-07-07 11:14:23', '2026-07-23 01:48:12');

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
('104a5803-7341-477d-b225-42fe30cff28d', 'dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', '2026-07-08 10:08:51', '2026-07-08 10:08:51', 0.13, 'regular', NULL, '2026-07-08 10:00:51', '2026-07-08 10:08:51'),
('1be8c3f5-c84c-489b-b475-543077a21c19', '122d3f82-05c6-4d58-8992-85a4ccb14cf9', '2026-07-18 00:08:15', NULL, 0.00, 'regular', NULL, '2026-07-18 00:08:15', '2026-07-18 00:08:15'),
('1e355583-3056-42ed-92f9-f76f70d85371', '46274cf1-5466-4f5d-b8ec-fa5916c9ba77', '2026-07-17 04:31:00', NULL, 0.00, 'regular', NULL, '2026-07-17 04:31:00', '2026-07-17 04:31:00'),
('26c55d28-3393-413d-bf24-46783e7e9753', '4359e014-5a11-4e32-b3af-6374252cb309', '2026-07-20 10:54:37', '2026-07-20 10:54:37', 16.52, 'regular', NULL, '2026-07-19 18:22:47', '2026-07-20 10:54:37'),
('33ea8a1f-e8f3-490b-832d-ed34283d9aef', 'a7bd863f-1468-463c-917c-7364b95f2183', '2026-07-21 05:02:27', NULL, 0.00, 'regular', NULL, '2026-07-21 05:02:27', '2026-07-21 05:02:27'),
('483ba3dd-5224-4e45-af3c-a2cb7214915f', '46274cf1-5466-4f5d-b8ec-fa5916c9ba77', '2026-07-17 04:20:10', '2026-07-17 04:20:10', 2.85, 'regular', NULL, '2026-07-17 01:28:11', '2026-07-17 04:20:10'),
('4a9c35a0-e950-44f2-962e-8cba75d4ed09', '7f7d5567-200f-4f9f-a954-f30e092114ee', '2026-07-11 23:37:35', '2026-07-11 23:37:34', 2.10, 'regular', NULL, '2026-07-11 21:31:25', '2026-07-11 23:37:34'),
('532afe2a-2417-4d35-beb3-0c65f52836ce', 'dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', '2026-07-08 08:38:38', '2026-07-08 08:38:38', 2.33, 'regular', NULL, '2026-07-08 06:17:49', '2026-07-08 08:38:38'),
('688896bd-9bb7-45c9-ac98-93e50cc00735', 'fb700dd7-2ac5-4cc2-8c51-42730ea77de1', '2026-07-14 05:33:52', NULL, 0.00, 'regular', NULL, '2026-07-14 05:33:52', '2026-07-14 05:33:52'),
('7ab6045c-b298-4199-b3a1-ea07f9b14574', 'd705b7b3-b9cd-43b9-8103-e308cd62f78c', '2026-07-09 07:14:56', NULL, 0.00, 'regular', NULL, '2026-07-09 07:14:56', '2026-07-09 07:14:56'),
('82a52c03-cfc6-47fe-861c-9d9130ee92e2', '46274cf1-5466-4f5d-b8ec-fa5916c9ba77', '2026-07-17 01:27:37', '2026-07-17 01:27:37', 0.00, 'regular', NULL, '2026-07-17 01:26:49', '2026-07-17 01:27:37'),
('8596bd3d-9e04-4519-a045-2e4e7d0b3626', '6294840f-64c3-4396-acad-a099368e9cd2', '2026-07-10 05:27:25', NULL, 0.00, 'regular', NULL, '2026-07-10 05:27:25', '2026-07-10 05:27:25'),
('889a8073-5b83-46cd-ad64-c441c8cd051e', 'f906065e-8b00-4ede-ae9b-ccf14c8fe733', '2026-07-22 22:56:55', NULL, 0.00, 'regular', NULL, '2026-07-22 22:56:55', '2026-07-22 22:56:55'),
('b8b0b91b-f750-40f5-a0b0-53f30e10a3ba', '8f21e40b-c4cf-4b9d-8377-9612deb2aca0', '2026-07-20 10:15:02', '2026-07-20 10:15:02', 13.67, 'regular', NULL, '2026-07-19 20:34:26', '2026-07-20 10:15:02'),
('c85981c1-b2aa-46a1-bea0-b821a70d1419', 'b946e64a-521d-40e0-92a3-7ebdced29fd1', '2026-07-22 14:42:44', NULL, 0.00, 'regular', NULL, '2026-07-22 14:42:45', '2026-07-22 14:42:45'),
('ccb52bf0-29f3-4bfd-9249-28920075beec', '1dd3cd25-7782-4cfe-8d9b-5369aa93778e', '2026-07-16 14:23:55', '2026-07-16 14:23:55', 20.37, 'regular', NULL, '2026-07-15 18:01:17', '2026-07-16 14:23:55'),
('d936fb4e-75ee-44d3-84f5-06bab051b0a3', '122d3f82-05c6-4d58-8992-85a4ccb14cf9', '2026-07-17 23:17:02', '2026-07-17 23:17:02', 2.75, 'regular', NULL, '2026-07-17 20:31:21', '2026-07-17 23:17:02'),
('e59cbe36-18ab-4ec2-9212-e398d4815f86', '8ee93749-6b11-471a-8ef7-3bbecbd0826f', '2026-07-15 03:51:38', NULL, 0.00, 'regular', NULL, '2026-07-15 03:51:38', '2026-07-15 03:51:38'),
('e81ad29e-ffd2-41a9-84c5-1c24d2eb590a', 'a527398b-083b-4b69-92d8-a7fccafaa667', '2026-07-13 05:35:35', NULL, 0.00, 'regular', NULL, '2026-07-13 05:35:35', '2026-07-13 05:35:35'),
('e8847997-c0fe-47f6-a013-ec1b0c58304b', 'dfb7b91a-fc90-4bf6-8f11-ca4797c99d85', '2026-07-08 11:44:47', NULL, 0.00, 'regular', NULL, '2026-07-08 11:44:47', '2026-07-08 11:44:47'),
('eb4b8731-2780-4108-9f73-d145d7444f18', '122d3f82-05c6-4d58-8992-85a4ccb14cf9', '2026-07-17 23:17:28', '2026-07-17 23:17:28', 0.00, 'regular', NULL, '2026-07-17 23:17:22', '2026-07-17 23:17:28'),
('ee623d42-1321-4e61-8ba3-570e855f2dcb', 'b946e64a-521d-40e0-92a3-7ebdced29fd1', '2026-07-22 14:41:53', '2026-07-22 14:41:53', 7.80, 'regular', NULL, '2026-07-22 06:53:39', '2026-07-22 14:41:53'),
('f2bc6751-0655-42a6-8400-737763dc5cca', 'd323eb80-f14e-43df-80db-1949fda93282', '2026-07-19 15:26:58', '2026-07-19 15:26:58', 20.58, 'regular', NULL, '2026-07-18 18:51:58', '2026-07-19 15:26:58'),
('f4b0cbfd-367a-4ee8-a545-c88fd5a2ea2f', '8eb6cbcd-5247-455f-92a5-cd0ae483dc45', '2026-07-18 19:07:34', NULL, 0.00, 'regular', NULL, '2026-07-18 19:07:34', '2026-07-18 19:07:34'),
('f783c7cc-6b9c-49db-a727-fe23982350c1', 'a527398b-083b-4b69-92d8-a7fccafaa667', '2026-07-13 05:34:46', '2026-07-13 05:34:46', 10.23, 'regular', NULL, '2026-07-12 19:20:02', '2026-07-13 05:34:46');

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
('03cf79c6-6d08-4ea5-9269-d7136caab7e5', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'YEIDlINAgJcyeZE0okoj33TvOMvDexeffeDoHLKl', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 11:59:26', NULL, '2026-07-09 13:59:26', '2026-07-09 11:59:26', '2026-07-09 11:59:26'),
('05325c79-6567-4034-a0b7-8ae0d4ec8fa4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'c5T3V52y1aFgW9VK2W3SDRUmKOjHPayFM3GhJwP7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 12:14:41', NULL, '2026-07-08 14:14:41', '2026-07-08 12:12:00', '2026-07-08 12:14:41'),
('059ea13b-ccc5-4753-8937-1506748c2b48', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '73wd0Nnlh8wb0tz7flqk9910dTpghqN8ecK8zLT7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:55:03', NULL, '2026-07-20 12:55:03', '2026-07-20 10:54:52', '2026-07-20 10:55:03'),
('05abdc78-caef-42e3-8179-9360e80104b4', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'i44oCGDdVrcbuZPkM3Gmw5LxJf1Mqzl8gj6PEtzF', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 04:32:12', NULL, '2026-07-17 06:32:12', '2026-07-17 04:18:21', '2026-07-17 04:32:12'),
('05d34059-b447-42e1-b620-6ea467a554d3', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'O73BvzkD0j4xB34em5KMPR7VrscqIS4FlGnr7Ox5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.1 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 03:54:25', NULL, '2026-07-15 05:54:25', '2026-07-15 03:51:29', '2026-07-15 03:54:25'),
('08534fb8-aad5-45a6-95ad-10c26cada8f3', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'qjpFiOZNWyFWUByh8fdvwTNsQLcNSLySlC29hXYR', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 10:44:01', NULL, '2026-07-15 12:44:01', '2026-07-15 09:22:57', '2026-07-15 10:44:01'),
('0974b6ab-5ed7-413d-b96a-c9ff1962b93b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'O1pxm5tt9tXFgXNWzK1HXU0maRxpkyDwO2qSDp27', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 09:32:36', NULL, '2026-07-21 11:32:36', '2026-07-21 06:59:11', '2026-07-21 09:32:36'),
('0bc9e954-af4b-45c4-bdbf-e199eb269e94', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'c9CB0Cf6P8vQdjRczE6CUe5APMMykuZsRFbJWQzi', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 10:41:06', NULL, '2026-07-14 12:41:06', '2026-07-14 10:41:05', '2026-07-14 10:41:06'),
('0c4f58c4-8346-41f3-aff6-565c1beac8c1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '5TKAyhREPODOkSCbsBenElrKgU2kselQJiu66Auo', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:19:48', NULL, '2026-07-22 10:19:48', '2026-07-22 08:18:46', '2026-07-22 08:19:48'),
('0c9ef03c-e151-47dd-b8a5-021d4ccc2f20', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'cjMS1dvcDJh3ucKYsVwDHwonPd2pKNwwhMd0L58P', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 09:26:34', NULL, '2026-07-09 11:26:34', '2026-07-09 09:26:34', '2026-07-09 09:26:34'),
('0e8b565c-4714-4a69-97de-02378ae026a1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'K9JFEijeNygoB4mFT9CqKHpt2db7oAsWS0ccc3Gy', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 06:58:37', NULL, '2026-07-21 08:58:37', '2026-07-21 05:01:53', '2026-07-21 06:58:37'),
('111f668b-7297-4e88-bec1-53fbc8a9cf26', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Ef9p5grC2FsE7DsOO6aXHFUul3LFh4JRJUyuNSrA', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 21:32:13', NULL, '2026-07-07 23:32:13', '2026-07-07 21:31:46', '2026-07-07 21:32:13'),
('139e713b-d67f-44ce-a8ba-64233ea19c13', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'RPdOmoL3fJhOVBpY83zMTKPF0DXbVxGWjaZKbw8v', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:53:53', NULL, '2026-07-20 12:53:53', '2026-07-20 10:14:57', '2026-07-20 10:53:53'),
('14ec16d8-ed26-4610-8a3d-0fe8af27ed63', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'TTJW3Sg66U7avMoHyu5DUZhUCIkRNNZeKSUgLn7l', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.1 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 18:00:35', NULL, '2026-07-15 20:00:35', '2026-07-15 15:23:54', '2026-07-15 18:00:35'),
('1a72e90b-ed46-4926-ae56-9ddb381044f8', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'CblUFL5ZeCDYAHIzVbbkeD1VAKmzZ1CyRGBU7Ou8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 23:35:30', NULL, '2026-07-23 01:35:30', '2026-07-22 22:56:14', '2026-07-22 23:35:30'),
('1b123fac-e6ae-4d6b-b749-35919b710bde', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'pK22kxBfLUUSaWMpbLeFrrPxgqyCO1vaXup00za9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 12:01:59', NULL, '2026-07-09 14:01:59', '2026-07-09 11:23:19', '2026-07-09 12:01:59'),
('1c5e402b-5d88-4674-aa73-7f483167c56d', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'tyoqdErxO1xfbTD7fm2RtPEe54sZd5DwMAwEQgZi', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 07:26:29', NULL, '2026-07-09 09:26:29', '2026-07-09 07:19:44', '2026-07-09 07:26:29'),
('1ef344d7-ce51-4c56-962e-2744ecdea0e8', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'oEN7tr3e4Id63ZU4UfqHqQ6JgqCFdTpWHxHv0vye', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 23:54:11', NULL, '2026-07-20 01:54:11', '2026-07-19 19:46:04', '2026-07-19 23:54:11'),
('1fe328d5-07b3-4991-b793-c5f81080d26f', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 't4lW5QAibS4UQOn0QRJd2Pf41GEjVrYk3EtSUjnl', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 09:42:37', NULL, '2026-07-09 11:42:37', '2026-07-09 09:26:36', '2026-07-09 09:42:37'),
('20577455-1626-490c-9395-329664a14619', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'QHPbGlmoE0N2iqMpBuhiWb7IccMDgLYkqNXMPpN2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 16:43:09', NULL, '2026-07-19 18:43:09', '2026-07-19 16:43:11', '2026-07-19 16:43:11'),
('2226e3ab-9e06-4234-9b56-c3c51c175359', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '7LIyoAsVIizhQMAMbYmS1dMcJ6MplT4m4EPn2mwW', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:13:00', NULL, '2026-07-22 10:13:00', '2026-07-22 06:53:30', '2026-07-22 08:13:00'),
('242e6d6f-e79d-4205-bac6-ab6e010072c6', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'E7JZgkTQTJzFUw2b7lFYqgy8A2eTWEEKuwLL5F3y', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 01:17:19', NULL, '2026-07-18 03:17:19', '2026-07-18 00:08:09', '2026-07-18 01:17:19'),
('24a117b5-e1a2-4786-87a5-09ab6e01d9df', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'kGTzo9buzsCVhQzuxEg4BDdDpe8sQ34YdpQ9Yy9h', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 15:26:59', NULL, '2026-07-19 17:26:59', '2026-07-19 14:08:39', '2026-07-19 15:26:59'),
('2999b870-7ba7-4077-b741-d4faa3ca3375', '7d966088-f3b6-4411-8034-7abe3ca89851', 'MeRg3Uk4KEo9cwxFysCQajLoirx3dYUNNWoxs0na', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:55:40', NULL, '2026-07-20 12:55:40', '2026-07-20 10:54:19', '2026-07-20 10:55:40'),
('2a5c7004-ed18-4ca2-9905-3001a01d647e', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'jcfVSFWbXCB5QYYWXSPPBRTZTqFKCSq3pSV7lolV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 02:01:45', NULL, '2026-07-17 04:01:45', '2026-07-17 01:26:13', '2026-07-17 02:01:45'),
('2e32ffd3-30ae-417e-81f7-ff10358198ba', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'ClH06iGLQYzXs1Gp1hTD0nOUhbCh3p38VwIWnH22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 18:52:23', NULL, '2026-07-18 20:52:23', '2026-07-18 18:51:52', '2026-07-18 18:52:23'),
('2e664492-c4a9-4479-89e9-ca872af2eaa2', '25c3fd0a-81ba-49da-b8d1-5aa651526640', '5yUFCxzDsYypsVNItKfhVDCmfPPzti2SP9GHqGj6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 11:02:46', NULL, '2026-07-14 13:02:46', '2026-07-14 11:00:15', '2026-07-14 11:02:46'),
('339b09df-cf52-4637-9c73-381bc7a9dbc6', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'yj2lAonMgPB1dBopF6uiTevbyITNERw9bAcSbOxw', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.1 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 04:36:11', NULL, '2026-07-16 06:36:11', '2026-07-15 21:25:23', '2026-07-16 04:36:11'),
('35bed312-fe9a-4082-9390-aabf679746b1', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'rrENhURAh6V1W3K06akohrHebEfhbyjQPgWEdevR', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 14:25:24', NULL, '2026-07-16 16:25:24', '2026-07-16 12:21:51', '2026-07-16 14:25:24'),
('385aad5c-ca96-4813-8c2f-c7b0dd0015ce', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'UYbHVlfusYrsAPqxc8LoJsb38duz4I80U3wKKipD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 21:31:25', NULL, '2026-07-07 23:31:25', '2026-07-07 21:30:48', '2026-07-07 21:31:25'),
('3ed63ec6-7e60-4c02-9ff1-556363ad11a4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'SGjVqLdRLHztIi2z5hjfUCfSnzm5VM27wsEDwisK', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 03:41:34', NULL, '2026-07-22 05:41:34', '2026-07-21 23:06:08', '2026-07-22 03:41:34'),
('3f404f4a-e36a-44c2-b269-9a1166b71b99', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'mjsoeGGBWheFVCqmrB0E1YfJmEedLtZn3bZHkOcs', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 05:03:25', NULL, '2026-07-16 07:03:25', '2026-07-15 21:51:19', '2026-07-16 05:03:25'),
('405c6d46-4b42-4f3b-a4e1-a2429b2e1da3', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'v4UFOhCUVFxx09pygmiRUn0a1QCW7FzVPwrscJ8G', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 10:34:48', NULL, '2026-07-14 12:34:48', '2026-07-14 10:31:20', '2026-07-14 10:34:48'),
('406c5d79-e9c0-40be-a3f5-6b1c274dbe8e', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'Sicb2YfGL7KaxLsVzyslACF6K9crmICQSZpY0DnH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 23:17:29', NULL, '2026-07-18 01:17:29', '2026-07-17 20:31:15', '2026-07-17 23:17:29'),
('41b2baeb-79ca-4c57-ae34-b7be3611f9c7', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'h1kPZ4Z0zPsef9R4rb5EK4PHGYUFAjgK37q6ST3z', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 10:31:45', NULL, '2026-07-09 12:31:45', '2026-07-09 09:23:06', '2026-07-09 10:31:45'),
('45cd5332-f3d4-4de2-8546-72c9e42d6d80', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'BYrJEKdPYhzoqMIUb9xeOIKVoNMXlMjTPFdu3E46', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:53:10', NULL, '2026-07-20 12:53:10', '2026-07-20 10:28:38', '2026-07-20 10:53:10'),
('476d765b-505d-4b7d-85be-b21627e58718', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '68YVjWV2noxI1ZJsznS7CzmYkxts2jobnvOBIRl0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 02:44:48', NULL, '2026-07-22 04:44:48', '2026-07-21 23:52:26', '2026-07-22 02:44:48'),
('480bb4e6-6969-4783-994f-5b34370c921c', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'CXnrSBZPrJcAv7sWE1Rls5nkZvTyskRi45IyfwWe', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 14:03:53', NULL, '2026-07-19 16:03:53', '2026-07-19 13:45:09', '2026-07-19 14:03:53'),
('488a61f0-65b9-4767-9ac2-79ab72092d0f', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'yQfrCeWXrgE2evKRfClkhtWLgoWPEWt94DmR1ilW', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 13:35:13', NULL, '2026-07-19 15:35:13', '2026-07-19 11:55:17', '2026-07-19 13:35:13'),
('4ac36364-fa19-4ff0-8d6a-8ea3491e2e62', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '5uur0vElqIdfpL9fF0uI0LJQ5DGikLs66LEP7WSN', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 18:21:21', NULL, '2026-07-18 20:21:21', '2026-07-18 18:21:16', '2026-07-18 18:21:21'),
('4b079ac5-ae1d-43ba-a2d3-a0c71d885454', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'TN0B4c3aOvILWUD9uD9ejP2eDcxCJQQne6Kike0u', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-23 05:51:40', NULL, '2026-07-23 07:51:40', '2026-07-23 05:51:41', '2026-07-23 05:51:41'),
('4fc4f8fe-df11-473a-8202-dbe88de4972d', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'BDvTam1AC8wl75ZI6Xedy9R4hyRAYns7aSXybs48', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-23 06:06:28', NULL, '2026-07-23 08:06:28', '2026-07-23 05:51:42', '2026-07-23 06:06:28'),
('50879f1e-4dd0-4276-bb60-626298899e20', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'wAnU6UIso6eu1pH05WkOXyEKs8QJuzMeJRdgCVLO', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 07:18:22', NULL, '2026-07-09 09:18:22', '2026-07-09 07:17:50', '2026-07-09 07:18:22'),
('56e0e7fc-7105-4bf2-a380-5c35f472c09a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '9ouo4xv5muMgabImAWKIJoUpvVodb20QFqf0dfCE', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 10:40:07', NULL, '2026-07-14 12:40:07', '2026-07-14 10:37:15', '2026-07-14 10:40:07'),
('5704b38f-ecd7-47be-ac42-39c0315ac764', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '88Ybi60tukzA1NMauD248Fgv1TBYl3djJaKdRV94', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 04:46:07', NULL, '2026-07-09 06:46:07', '2026-07-09 04:30:25', '2026-07-09 04:46:07'),
('571869b4-a730-456d-8534-a5d788a3d93a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'XpAXpYCclLt1J6pyxjUBNc0pfowYni8KspDhfkRM', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 12:51:05', NULL, '2026-07-07 14:51:05', '2026-07-07 12:50:34', '2026-07-07 12:51:05'),
('57bed90c-5820-4fa0-82c7-2877779657d2', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'OEkiZfDEn6v4TS8AxwxjCyRDA3Enfm6z5ByhsCve', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 07:08:36', NULL, '2026-07-21 09:08:36', '2026-07-21 06:59:19', '2026-07-21 07:08:36'),
('57f57d6f-0304-4391-b370-8426b52b9ff1', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '9HtVpxHc0pFpK45nJ6AYYENWOkSaaQI9KOP8XbF2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 15:56:28', NULL, '2026-07-22 17:56:28', '2026-07-22 15:43:41', '2026-07-22 15:56:28'),
('58c5328a-ba42-4b6c-b880-44067d962b0f', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'NJ0FOp6p4RYdNuNEPRC6bBDejxpRfRYNapXt19b7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 09:32:37', NULL, '2026-07-21 11:32:37', '2026-07-21 05:10:49', '2026-07-21 09:32:37'),
('5b386622-b2ca-4ff3-b00b-72014064fa72', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'rCRlciueFBaw7efPmta7z8JV9tKkr1G8PEflvcNX', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.1 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 19:16:43', NULL, '2026-07-15 21:16:43', '2026-07-15 18:01:12', '2026-07-15 19:16:43'),
('5cc737ab-bef7-46e9-ba3b-14d72e09072f', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'HVVY2yE8JkSuU3cielp8YsoaTUnQFNb9BJaulR0D', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 06:58:58', NULL, '2026-07-21 08:58:58', '2026-07-21 05:55:13', '2026-07-21 06:58:58'),
('5df7de93-46be-4bdc-9a92-ae9e1172e4e0', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'cXSNnZX5NNf81hJ0lfVpA6i7Xd9plx7qLY36ZoWR', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 08:38:42', NULL, '2026-07-08 10:38:42', '2026-07-08 07:15:54', '2026-07-08 08:38:42'),
('5e0d080f-c439-45aa-965a-ff4f2de7bd2b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'tYbWnsT7dGhNMYOzo0ZiYlTixDb2oXj61Ii2lXwE', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:13:54', NULL, '2026-07-20 12:13:54', '2026-07-20 10:13:33', '2026-07-20 10:13:54'),
('5ea7a232-a15f-4ce4-b5c6-2d7fdf63d731', '7d966088-f3b6-4411-8034-7abe3ca89851', 'W00JvHNC3jDPe8g1WvlcauQs3r9mHpN7Q0NqVPUQ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 13:47:55', NULL, '2026-07-19 15:47:55', '2026-07-19 13:40:53', '2026-07-19 13:47:55'),
('61e0d3da-6721-4346-a757-72c9726dd614', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'EkYqzhKROjgUNeAQtmN61VGLcg20278nODhxqBwL', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 14:18:38', NULL, '2026-07-17 16:18:38', '2026-07-17 04:17:41', '2026-07-17 14:18:38'),
('622e14cd-2305-4396-8f29-fa373a5dc0ae', '25c3fd0a-81ba-49da-b8d1-5aa651526640', '9tcgZB6rxvieLiyK4e5cmnlZqw9NjPbSy22v8dFH', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:20:24', NULL, '2026-07-22 10:20:24', '2026-07-22 08:20:20', '2026-07-22 08:20:24'),
('65183fb2-3a4f-4b1e-bc95-1aa950aa7865', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'ccZGIsOQZsrFCrnBHddOV2qgDvzc3i1nExKnihAL', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 20:30:31', NULL, '2026-07-19 22:30:31', '2026-07-19 16:44:53', '2026-07-19 20:30:31'),
('66036058-8cb9-48e2-bcd4-cf64506e42a5', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'zX2apNhoqKbdSLofNnKniRlcT2Y2pgahk9epSMlI', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 23:18:36', NULL, '2026-07-18 01:18:36', '2026-07-17 22:59:44', '2026-07-17 23:18:36'),
('69de2180-69e6-40c0-9ff3-5f8d303d18b2', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'mWhLsG0LLTDNS9hoHuat2aFWruV7dWbvnpGUcpix', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 06:24:35', NULL, '2026-07-12 08:24:35', '2026-07-12 06:17:39', '2026-07-12 06:24:35'),
('6af4f6b4-5a50-4e53-8779-19b4e88239db', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'LVeC3YAOa4mGeNLJNvS1BAQgJdI4nGai6eiXTWyh', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 03:25:20', NULL, '2026-07-08 05:25:20', '2026-07-08 02:43:15', '2026-07-08 03:25:20'),
('6f88fa9c-5139-4270-a1fc-4f8113abbbda', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'ky2gZJ4IF6pgJEaFZosVz2umEibVxqzwBhsfXlXv', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-21 16:26:13', NULL, '2026-07-21 18:26:13', '2026-07-21 15:42:05', '2026-07-21 16:26:13'),
('728a1f3a-37ff-4017-8780-71a14a16f7c3', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'qJ9PSFVFc7Xz5Ividt8vNmmPmMiXcWg0372iYVBl', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 22:59:20', NULL, '2026-07-18 00:59:20', '2026-07-17 22:44:40', '2026-07-17 22:59:20'),
('766ee762-cec4-4ffa-9a48-26799accac86', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '8cHNUNXtn00xgYFWaF1EzcpNdrfidFHCaGJ02csF', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-11 19:28:31', NULL, '2026-07-11 21:28:31', '2026-07-11 19:21:36', '2026-07-11 19:28:31'),
('766efab1-0d43-43e1-bb64-28312e7e4b49', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'VkrgGChJJjq9m6hANhJkdsZjBlHpUMh6Qj0WG1e1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 13:08:55', NULL, '2026-07-22 15:08:55', '2026-07-22 10:57:27', '2026-07-22 13:08:55'),
('78e50615-f55f-4462-87d6-cd74309da367', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'vYMT5sINH5NQMybwOq84tqyUmQOGMHv2Fq9YLll0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 10:38:02', NULL, '2026-07-17 12:38:02', '2026-07-17 10:37:55', '2026-07-17 10:38:02'),
('79de2f1c-b78b-40de-9c06-f9227b933e59', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'Vm6IW1aMrL52PffncQFT65u47xtpGzRsVn9ov9Iu', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 06:18:39', NULL, '2026-07-18 08:18:39', '2026-07-18 00:35:36', '2026-07-18 06:18:39'),
('7e4b6760-7a9b-42c8-a55b-bab0c3415a54', '7d966088-f3b6-4411-8034-7abe3ca89851', 'zZGXju7F3b3W42SHfjzmJZzqrQGMLweAYMa98cae', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 15:47:33', NULL, '2026-07-19 17:47:33', '2026-07-19 15:27:15', '2026-07-19 15:47:33'),
('7ee8ad32-b473-44b2-a0f9-8e5d8f4f4e6e', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'gi0zdIewdc5YqpkWpoDoxCcVCHM2qaQoIKOaNqRl', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 04:29:42', NULL, '2026-07-09 06:29:42', '2026-07-09 04:29:09', '2026-07-09 04:29:42'),
('8245187f-5dc2-462e-9eb2-bc4c795a8862', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'htUt1eqW1fYpuEQje9uDejB5Ti6uRc75keU0h8m4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 01:51:23', NULL, '2026-07-12 03:51:23', '2026-07-12 01:34:57', '2026-07-12 01:51:23'),
('8886c3f9-2269-405c-aea8-9f2d4c8c8df8', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '3T99vHH9HEYe1BwlnEbZ1v3OV2qYF8b5LPqEt2Hj', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 12:53:26', NULL, '2026-07-08 14:53:26', '2026-07-08 12:16:48', '2026-07-08 12:53:26'),
('889f6d02-6551-4d9e-9035-e1160b0b744a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'MziGxFSo6SqbH96ZCfPaZ3c7QTuPbRutzXylkUHU', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 04:25:31', NULL, '2026-07-12 06:25:31', '2026-07-12 04:21:02', '2026-07-12 04:25:31'),
('896c7911-573a-4276-881a-27354c67c0f9', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '0WlKuQVUcUt8kZw2OUHbNx9wxQzW1m08nLULExz2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 21:40:48', NULL, '2026-07-15 23:40:48', '2026-07-15 18:00:19', '2026-07-15 21:40:48'),
('8a90217e-1fef-4f73-837c-bcb033488431', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '6ImCxE668uvohl83Qj9Zs7GbShvqp6m5dEsszk0C', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 14:48:26', NULL, '2026-07-22 16:48:26', '2026-07-22 13:11:28', '2026-07-22 14:48:26'),
('8c19e13f-d29c-472f-9499-b6244923cecf', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'VZMg2PRaCsVVhrZ32fCART5uInqrjGnpxsT8S0lD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 14:06:59', NULL, '2026-07-08 16:06:59', '2026-07-08 12:15:55', '2026-07-08 14:06:59'),
('8e7a8a5e-2638-4a53-ab02-1e492d3461c8', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'iKauHfo01T54dkAHLQaKyyrDlxMMJy38WlB6fvc0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 05:21:34', NULL, '2026-07-08 07:21:34', '2026-07-08 03:27:40', '2026-07-08 05:21:34'),
('8e7b09d3-7f13-4ca7-89fd-70de6a4f73de', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'raliht50OUfRzeL94aKApsf62sztYvekX5mtcfx5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 13:45:25', NULL, '2026-07-18 15:45:25', '2026-07-18 13:11:44', '2026-07-18 13:45:25'),
('907c7ae0-abcc-4ee2-81b4-1b23cc96f955', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '483InYJLqP8qub5vSDkzUTJSDnt6V0lAanU01gIp', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 00:35:09', NULL, '2026-07-18 02:35:09', '2026-07-17 23:55:11', '2026-07-18 00:35:09'),
('910bfd1f-d55c-4fa4-8c36-26fa5fa8790d', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'WQMo7RjmCvh72XFeUQOQvYuudPIvMwMV8IuHOeKV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 19:23:06', NULL, '2026-07-12 21:23:06', '2026-07-12 19:19:26', '2026-07-12 19:23:06'),
('911d32fb-2aa3-4a14-ab65-ab9a0d8caf0d', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'TpnSBfxfK3JUQPlP04KlnkfD9LRDCMB8pb5lbYuS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:15:34', NULL, '2026-07-22 10:15:34', '2026-07-22 08:13:24', '2026-07-22 08:15:34'),
('92f13567-a0f1-47cb-9a7d-6cd3c39b6daf', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'ZfbWrdW9KpW8ouVCFdpnRPYt4IVIvve2s5hhc4ev', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-13 06:47:39', NULL, '2026-07-13 08:47:39', '2026-07-13 05:34:29', '2026-07-13 06:47:39'),
('94699f47-9e5a-4a13-8e1e-707485389ec1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'D6GSybYgkPrIFrufi2qLu2oE4owbz8NKvqacyFGS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 13:56:16', NULL, '2026-07-18 15:56:16', '2026-07-18 13:12:04', '2026-07-18 13:56:16'),
('949a2b95-6113-4f12-8a9f-4b267e55ab3f', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '0G1Zi6aMfBi5hGS8xh4mj6r9P4x0ggF1Cq60kWjQ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 15:53:20', NULL, '2026-07-08 17:53:20', '2026-07-08 12:13:30', '2026-07-08 15:53:20'),
('99664d27-d7a7-4036-a555-16369db25b88', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '9ZqvoodaVeUoJ4POtQy9OArwXQPSWHCKz68VYEhy', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-23 05:35:28', NULL, '2026-07-23 07:35:28', '2026-07-23 02:53:57', '2026-07-23 05:35:28'),
('9a262835-64ca-413a-8d19-5d7721ec0728', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'LJ2nEVP0M8gKXt3CwPAh7R544fLTS8owFV2tbsP6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 12:12:59', NULL, '2026-07-08 14:12:59', '2026-07-08 10:00:38', '2026-07-08 12:12:59'),
('9b18196e-756b-4faa-b6fc-cfd43ab13f9d', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'q13YEigFvWg6PfZPSw3PQeFH4UYA7TdBbdmVYZxU', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 03:59:47', NULL, '2026-07-15 05:59:47', '2026-07-15 03:21:13', '2026-07-15 03:59:47'),
('9f75b158-73d4-4746-83e2-481d82572cf6', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '0F7fhp4UrnDSVYdQPAcy025emFBhsLln3sJuYbD2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 07:15:38', NULL, '2026-07-09 09:15:38', '2026-07-09 07:14:45', '2026-07-09 07:15:38'),
('a02dda96-482d-493d-b05d-5dfd11a2310b', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'fsZghVaxIaguZNjis1eGkEWtuxTXF2ItNPpAqy8h', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 13:48:31', NULL, '2026-07-18 15:48:31', '2026-07-18 13:45:50', '2026-07-18 13:48:31'),
('a03940bf-d135-4c41-8840-0deeab47a5f4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'n8XYQF65KuwSWSx7VMIMfPrvVWozYzndx9mQa6jD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-10 03:04:40', NULL, '2026-07-10 05:04:40', '2026-07-10 03:01:22', '2026-07-10 03:04:40'),
('a0f7e441-bd24-4ae5-994e-023655483603', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'JTGWxiyS2ckOqMsAZcd74hfYTUx0eX6ARhEwvQoc', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 03:17:15', NULL, '2026-07-12 05:17:15', '2026-07-12 03:15:42', '2026-07-12 03:17:15'),
('a1f6f384-1dfe-4cae-9013-1b1278096258', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'WrYWWhEPo8vCUo9gzINYFEzMsojstjADJuhwTAST', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 01:47:37', NULL, '2026-07-22 03:47:37', '2026-07-22 00:13:37', '2026-07-22 01:47:37'),
('a21fd02c-278c-4b97-bbb4-027a9d16266a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'heYcb3DJxVdeITtgAhDkDsHASKKR2lBoUANeZtRg', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-07 13:25:23', NULL, '2026-07-07 15:25:23', '2026-07-07 12:54:11', '2026-07-07 13:25:23'),
('a2291ebd-745b-4aa8-92ff-4cd280223ac8', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'QMOEZu0gDVep9ipN37Bk8jhAlFyzF87GPiOHOlTj', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 03:18:25', NULL, '2026-07-12 05:18:25', '2026-07-11 23:38:15', '2026-07-12 03:18:25'),
('a4d8ddfb-09f0-4cb0-ba7b-f1d8441dbaef', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'CfJcWgbngM4TArg9vF6c5U1Xg3xBjeYp8rGI7C2p', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 14:49:01', NULL, '2026-07-22 16:49:01', '2026-07-22 08:07:40', '2026-07-22 14:49:01'),
('a8a21bb1-9023-47cf-bf3f-2ea9298e6e25', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '1OFMKGqOnAesZNwGkdYTQyuZBa8EnSrIyTuU8JIm', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 13:12:03', NULL, '2026-07-18 15:12:03', '2026-07-18 13:12:03', '2026-07-18 13:12:03'),
('a9bc2b50-7a96-4a04-ab2b-a4752965b6ac', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '5TFt4Ru4FzlLoW1YwCiwRixYxQY8DCiQWtrlLJQQ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 07:26:48', NULL, '2026-07-09 09:26:48', '2026-07-09 07:16:38', '2026-07-09 07:26:48'),
('aa2b153c-cf2c-4e51-8d16-ec83ffd2c1f1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'OHT6BKBIyAzTqywzexImeUFheR1q6CY7G2Fv2PvD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 12:52:20', NULL, '2026-07-14 14:52:20', '2026-07-14 10:43:11', '2026-07-14 12:52:20'),
('ad04dd41-7ff7-4942-977e-3dba9dd32278', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'etEss8wAEZHRyi6MPDkqiehhBgqhtcD4xHkrDg05', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 15:53:11', NULL, '2026-07-08 17:53:11', '2026-07-08 15:19:01', '2026-07-08 15:53:11'),
('aec3365e-bcb9-4f8f-b416-d482ad4d794b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'SX9XbY96Vt1h43UIq8BZMwaMrQAhWYw4gt6qENya', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 10:00:09', NULL, '2026-07-08 12:00:09', '2026-07-08 06:52:08', '2026-07-08 10:00:09'),
('b1efed70-47f8-42fc-a1f3-6056235ad068', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'hTkQKvoRjduImrpG3GXURSdrigBys3agCuphrqc2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 12:21:35', NULL, '2026-07-16 14:21:35', '2026-07-16 12:21:24', '2026-07-16 12:21:35'),
('b3e4c497-f4f4-4c5f-9e16-efb81f47298a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'LXI6327rr7VYhrJ5bE1U5jHCbBnRsuIErfYvRioT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 23:36:11', NULL, '2026-07-23 01:36:11', '2026-07-22 22:54:49', '2026-07-22 23:36:11'),
('b55e045a-432c-4dbf-a250-08083690d0c2', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'e0FSKv5GK05gQZznzRCfpE14nVumF6qHnhm0rNfo', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:07:55', NULL, '2026-07-08 08:07:55', '2026-07-08 05:54:00', '2026-07-08 06:07:55'),
('b68c4ebc-7e00-4578-977a-6767e8adb068', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'zqT4P0PJT3CRFWxEtvQ2V8uKBjO3WfQBJa6HVrVX', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-10 05:51:15', NULL, '2026-07-10 07:51:15', '2026-07-10 05:27:20', '2026-07-10 05:51:15'),
('b6a7d7d1-c897-4cc4-950e-4e4b7e4ab5fe', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '3Q1tXFW5LLV0ld4jTduYGn3uHepcaRCJgw7WCw6d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 01:34:33', NULL, '2026-07-12 03:34:33', '2026-07-11 21:40:56', '2026-07-12 01:34:33'),
('b712bf3b-c85d-47ab-b711-ae1c5be8f507', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '1Whb7ijZZttEgfXnEtXeVSxYSMvxUjkyE6jmMh7F', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-13 08:01:51', NULL, '2026-07-13 10:01:51', '2026-07-13 05:10:57', '2026-07-13 08:01:51'),
('b73c0ff7-3aab-45cb-8176-165d6f4aa9b1', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'k5INBdEs3QEm4DsonK4Xdx0qmlzzvwl1fJNMoXRV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 16:44:06', NULL, '2026-07-19 18:44:06', '2026-07-19 16:43:12', '2026-07-19 16:44:06'),
('b9c54626-745b-4708-8144-54335e812c8e', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'ZK7zHsjVjW16XChY8tUZhI8IAqGJ7PThpParKuW4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 13:09:33', NULL, '2026-07-22 15:09:33', '2026-07-22 13:09:33', '2026-07-22 13:09:33'),
('b9ce4cff-2d04-4260-a2ec-f912b5244fad', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'MENphmTXMfZpjgDEJzl63x4ROYDU7DZpT9NpnECA', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 10:25:24', NULL, '2026-07-14 12:25:24', '2026-07-14 05:28:56', '2026-07-14 10:25:24'),
('bbcf7e70-769a-46e4-b9f9-a841e32e91ce', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'ft5PZVhEPkeSVFzrrztiGBRwwrrHZaDd2hFtxKwN', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 02:02:07', NULL, '2026-07-17 04:02:07', '2026-07-17 01:26:43', '2026-07-17 02:02:07'),
('be558d5e-0250-4ba5-8dbc-e4c82180c379', '7d966088-f3b6-4411-8034-7abe3ca89851', 'JVTcUY29LH3WDE8nn9sEdgRHjHhLvPWVkm8UPbWD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:06:41', NULL, '2026-07-22 10:06:41', '2026-07-22 07:54:36', '2026-07-22 08:06:41'),
('be8f78cd-42d3-43fc-a505-083132d04a48', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'hIrctS0FYSuhLH7gXQZQSogw28lDCT3I8zbhFqrD', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 12:21:50', NULL, '2026-07-16 14:21:50', '2026-07-16 12:21:50', '2026-07-16 12:21:50'),
('c01a84ac-e45a-412d-9b20-cbd4e0c2fda5', '7d966088-f3b6-4411-8034-7abe3ca89851', 'afHIQBv8ZzMC4k6akJKb6tYSjsutNXtYS7A5cNr0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 22:03:27', NULL, '2026-07-19 00:03:27', '2026-07-18 19:07:27', '2026-07-18 22:03:27'),
('c2175868-462f-4585-bf08-7e41fd9f2e8a', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '1MbLKMz88BTLRfPI9fDvLVuvhTzQkbONkzwdnMvB', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:08:45', NULL, '2026-07-08 08:08:45', '2026-07-08 06:08:43', '2026-07-08 06:08:45'),
('c4844638-34f3-4014-a236-9b3c29ab8f4b', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'FkEKbusl54n3HGAUdzgyyyFIoeyJgcH22tfhaOb1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 22:33:50', NULL, '2026-07-18 00:33:50', '2026-07-17 20:56:54', '2026-07-17 22:33:50'),
('cad8b34f-c2dc-4814-98c5-668b49a51c73', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'rgDCBfTfpUP15EmERb4RsElWTe1Yn2r0ybIxV78J', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 13:20:43', NULL, '2026-07-22 15:20:43', '2026-07-22 13:09:35', '2026-07-22 13:20:43'),
('cc69c645-6d9d-4945-8eb7-df947fc8bf80', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '9B2jmZCKye1Msul6O7lyBKWp0dpDxnoMBDzTFglQ', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-14 09:31:18', NULL, '2026-07-14 11:31:18', '2026-07-14 05:33:48', '2026-07-14 09:31:18'),
('ccb65518-28bd-4a47-b910-4fe8668600b1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'pzrDLwlkYX1NSXdHgdcvROYE7VmDkeGPFovd4J7D', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-17 12:47:22', NULL, '2026-07-17 14:47:22', '2026-07-17 12:20:18', '2026-07-17 12:47:22'),
('cdbbac9c-3ec8-4ecd-85e1-587de64c3831', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'KqzIGBmtoLdNv9SnmJL2012BnMrordW5kcbOT1If', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 18:03:54', NULL, '2026-07-08 20:03:54', '2026-07-08 18:03:48', '2026-07-08 18:03:54');
INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `device_type`, `browser`, `os`, `location`, `is_current`, `last_activity`, `login_at`, `expires_at`, `created_at`, `updated_at`) VALUES
('ce8f25bd-d9c4-4611-9f92-1511a479bcbf', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'I8eDXNHcREkhYyAX5N7gJOLfrRDb9p5IBcpGI9vs', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 18:06:31', NULL, '2026-07-08 20:06:31', '2026-07-08 18:04:06', '2026-07-08 18:06:31'),
('ceac2bdd-99f1-4a1c-b882-31689cc96679', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'XS8Wr8P4k5QXKWw0AC0oP76nEhFeW7E8TrqnEc2l', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-11 23:37:35', NULL, '2026-07-12 01:37:35', '2026-07-11 21:31:19', '2026-07-11 23:37:35'),
('cf183eb1-fd06-45e7-808d-b17e8667f409', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'j5FEwpFkRWtjUO5baRfP1iAQhw6HUg0DUjrxe2Od', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 19:21:42', NULL, '2026-07-12 21:21:42', '2026-07-12 19:19:54', '2026-07-12 19:21:42'),
('d3caed64-a568-4fe1-9210-2e8b6d741779', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'lL2gm2Tosq5UWxYitfrWJoCYgZ3gV06iwvgCSy5F', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 23:53:33', NULL, '2026-07-20 01:53:33', '2026-07-19 20:34:10', '2026-07-19 23:53:33'),
('d454cc0c-e550-48b2-a4de-9b2a806616c6', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'zbq81VUmO2taE3KyAAS1yal8sq9SZ50UudvGAdxT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 15:46:33', NULL, '2026-07-15 17:46:33', '2026-07-15 15:44:37', '2026-07-15 15:46:33'),
('d6e6aa44-0641-4b8e-aee4-7065889be2bb', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'NIV2hswPx4ge0pKozXAhDNHampAf7l0wXv6lB6TU', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 12:20:24', NULL, '2026-07-09 14:20:24', '2026-07-09 11:24:57', '2026-07-09 12:20:24'),
('dac16cbc-91a5-4506-8d1e-273195d2a1b1', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'pDrwo9jjGCjWEM94qPeJJ1AoUGL0NReMO9goNVle', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 04:13:09', NULL, '2026-07-12 06:13:09', '2026-07-12 04:09:39', '2026-07-12 04:13:09'),
('db47db0c-9b90-4630-b0f1-645f8950df23', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '83UIRnAighCrnGqzktSoGyZ3nWNWAEz6tVwE0ke6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 16:42:34', NULL, '2026-07-19 18:42:34', '2026-07-19 14:04:20', '2026-07-19 16:42:34'),
('dbc1fa56-a4c4-4a5e-97c6-97372662e82e', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'tPbU6ADHkukXhL2V9zSuovWwgm8zz3YgtyWjtOEE', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 01:35:40', NULL, '2026-07-12 03:35:40', '2026-07-12 01:34:22', '2026-07-12 01:35:40'),
('dc419fb7-7569-4629-8a0f-6b8834a1a062', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'XXVQ2kKK0ACIK5qBanE8zY3QKHRR6niSJRMTqzX1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 12:20:37', NULL, '2026-07-09 14:20:37', '2026-07-09 11:59:27', '2026-07-09 12:20:37'),
('de25767d-208e-4aa8-b92c-037e347226a2', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'RM6ozo7BhU6j0Z4ebxEU0WATQvwpgAToIZEn78jS', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-23 07:30:42', NULL, '2026-07-23 09:30:42', '2026-07-23 01:47:06', '2026-07-23 07:30:42'),
('df7dbdcf-f83c-43e1-a10f-289e4b8bf4bc', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'O7ytSNFem8AcC6jiRAQ4Y510u951yxkgAw31nGsy', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 07:53:33', NULL, '2026-07-22 09:53:33', '2026-07-22 02:10:57', '2026-07-22 07:53:33'),
('e29a028d-82db-4eec-bd4f-6650dd27cb11', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'I3PPETWNL857MbgvAlAp2STLKA7DP3XgB9gMinNT', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-15 21:51:18', NULL, '2026-07-15 23:51:18', '2026-07-15 21:51:18', '2026-07-15 21:51:18'),
('e39a3bb2-51f1-4b1e-b829-8409d3b7e4f1', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'ozJRdRsibGAWCynZ8H1uNdd54Exr9qOanVcDHJtM', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-23 07:29:59', NULL, '2026-07-23 09:29:59', '2026-07-23 06:07:04', '2026-07-23 07:29:59'),
('e4350abe-b5a7-493d-ba3c-ef2c8a3b0d89', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'X8XZ6HFe950OVnEve1kUKxQxeMy1DXU1sCQJUKmk', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.128.0 Chrome/148.0.7778.271 Electron/42.5.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-10 06:25:36', NULL, '2026-07-10 08:25:36', '2026-07-10 05:25:01', '2026-07-10 06:25:36'),
('e74cce15-4f4e-4788-a8b0-f2c06d1ddba4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'gUrdVrABf18Z19kZIinKTETB0bJBBqsDGjEEK8KU', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 05:10:19', NULL, '2026-07-16 07:10:19', '2026-07-15 21:50:29', '2026-07-16 05:10:19'),
('e79eed71-ce91-4e54-9035-f9e4402a55dd', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'UCEej8uip4kYyhCNqfX6uA0B8FJfCGXgUjr6tbIw', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-09 07:19:15', NULL, '2026-07-09 09:19:15', '2026-07-09 07:18:42', '2026-07-09 07:19:15'),
('e8beaa2e-d34e-443f-9097-89516d10a152', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '6rZq25Qe7cgz0dfrgSFXKX9MRzhiGXl2kAZDIp9m', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 13:07:33', NULL, '2026-07-22 15:07:33', '2026-07-22 08:22:54', '2026-07-22 13:07:33'),
('ea249be2-b81b-4a76-b94d-ea906a4881b7', '7d966088-f3b6-4411-8034-7abe3ca89851', 'RsnAFalZkD4x22OA74xaGIMgcAddC6aJpv7YCDjv', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-19 21:16:08', NULL, '2026-07-19 23:16:08', '2026-07-19 18:22:38', '2026-07-19 21:16:08'),
('eb0a1597-41d6-4150-9549-d01b7399e6f5', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', '32aO2n9NV10JezisiYWbYnYeHjauOoyRUNdAEWLV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-20 10:14:45', NULL, '2026-07-20 12:14:45', '2026-07-20 10:14:04', '2026-07-20 10:14:45'),
('ebfd4f22-cb48-4cbb-888b-f4095dbb4d64', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'WNMSceMSfH3Qwwtc8DJHk35bplRqN3wUnP0AZpA8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.0 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-16 14:25:10', NULL, '2026-07-16 16:25:10', '2026-07-16 14:21:08', '2026-07-16 14:25:10'),
('eccd81fe-0bf1-4910-a692-78909e832643', 'b9015c8c-348f-4793-98b2-5d3ac3dd2aa9', 'xhoqsplwnl9SxKKopmFyQygLuHQxTX9ry0OyTRSs', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.127.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 06:45:25', NULL, '2026-07-08 08:45:25', '2026-07-08 06:17:35', '2026-07-08 06:45:25'),
('f0439299-2715-4ba8-85a8-3cc360ede0f4', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'OMWDELuwR7JeRfX63sREqOXlJ80ralkj8lyPnLiX', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-08 07:15:27', NULL, '2026-07-08 09:15:27', '2026-07-08 06:20:52', '2026-07-08 07:15:27'),
('f344eb69-32d3-4f02-bcd2-b04971753932', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', '6yWHExXA6yhV7uhf2HAPGZaC3KjWTzDABlaKEJKc', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-12 03:13:09', NULL, '2026-07-12 05:13:09', '2026-07-12 01:36:09', '2026-07-12 03:13:09'),
('f7c04b99-3d58-4e21-8bb5-150986732210', '25c3fd0a-81ba-49da-b8d1-5aa651526640', 'DhvdH9uTfU3Xrw5aScaeOrxYY4INARLfjy446S0y', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-22 08:18:14', NULL, '2026-07-22 10:18:14', '2026-07-22 08:16:03', '2026-07-22 08:18:14'),
('fd95bf95-3267-4c52-b3d0-4691c2b10020', '1a1d2b12-d3b5-436d-aa94-762ab8f5fbd4', 'Rg917qINIEbql3nikJ45dSDqKvKrH73zVapLEfK4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 22:46:07', NULL, '2026-07-19 00:46:07', '2026-07-18 18:41:59', '2026-07-18 22:46:07'),
('ffa1a4d5-159f-435c-b557-63fd965558f3', '134402ed-fc04-4be7-a2c0-df8395f03a24', 'J2sTB5dIH4qaQaHSzsB2NmX68KEQd732qlM0gx41', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.129.1 Chrome/148.0.7778.280 Electron/42.6.0 Safari/537.36', 'desktop', 'Chrome', 'Windows', 'Local Development', 1, '2026-07-18 00:07:34', NULL, '2026-07-18 02:07:34', '2026-07-17 23:53:07', '2026-07-18 00:07:34');

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
-- Indexes for table `attendance_corrections`
--
ALTER TABLE `attendance_corrections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_corrections_attendance_record_id_index` (`attendance_record_id`),
  ADD KEY `attendance_corrections_corrected_by_index` (`corrected_by`);

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
  ADD KEY `departments_company_id_index` (`company_id`),
  ADD KEY `departments_supervisor_id_foreign` (`supervisor_id`);

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
  ADD KEY `employees_company_id_index` (`company_id`),
  ADD KEY `employees_payroll_template_id_foreign` (`payroll_template_id`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `official_business_requests`
--
ALTER TABLE `official_business_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `official_business_requests_attendance_record_id_foreign` (`attendance_record_id`),
  ADD KEY `official_business_requests_employee_id_date_index` (`employee_id`,`date`),
  ADD KEY `official_business_requests_status_index` (`status`),
  ADD KEY `official_business_requests_status_expires_at_index` (`status`,`expires_at`);

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
  ADD UNIQUE KEY `payrolls_employee_period_unique` (`employee_id`,`pay_period_start`,`pay_period_end`),
  ADD KEY `payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `payrolls_approved_by_foreign` (`approved_by`),
  ADD KEY `payrolls_period_id_index` (`period_id`),
  ADD KEY `payrolls_company_id_index` (`company_id`);

--
-- Indexes for table `payroll_templates`
--
ALTER TABLE `payroll_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `periods_company_cycle_unique` (`company_id`,`period_year`,`period_month`,`period_no`),
  ADD KEY `periods_department_id_foreign` (`department_id`),
  ADD KEY `periods_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `periods_previous_period_id_foreign` (`previous_period_id`),
  ADD KEY `periods_company_dates_index` (`company_id`,`start_date`,`end_date`),
  ADD KEY `periods_company_cycle_index` (`company_id`,`period_year`,`period_month`);

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
  ADD KEY `positions_company_id_index` (`company_id`),
  ADD KEY `positions_payroll_template_id_foreign` (`payroll_template_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  ADD CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `departments_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `employees_payroll_template_id_foreign` FOREIGN KEY (`payroll_template_id`) REFERENCES `payroll_templates` (`id`),
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
-- Constraints for table `official_business_requests`
--
ALTER TABLE `official_business_requests`
  ADD CONSTRAINT `official_business_requests_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `official_business_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `payrolls_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payrolls_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `periods_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `periods_previous_period_id_foreign` FOREIGN KEY (`previous_period_id`) REFERENCES `periods` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `positions_payroll_template_id_foreign` FOREIGN KEY (`payroll_template_id`) REFERENCES `payroll_templates` (`id`);

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
