
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `microsoft_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','hr','manager','employee') NOT NULL DEFAULT 'employee',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounts_email_unique` (`email`),
  UNIQUE KEY `accounts_employee_id_unique` (`employee_id`),
  CONSTRAINT `accounts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES ('037af7ca-b5f3-4dd2-8d04-2f8c5ec943a8','20000000-0000-4000-8000-000000000009','hr@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$w.w.YZCNVbNgd2v2qMTC9.UgCcASv0LEyD9EK.VxOXrgUA4NtluI.',NULL,NULL,'hr',1,NULL,NULL,'2026-08-14 16:35:04','2026-08-14 16:35:04',NULL),('16606d42-55b3-40e3-ba6b-9d9f36cf56f6','20000000-0000-4000-8000-000000000008','bulbasaur@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$zpHYe/Lvl3N95iNhbqJCV.4ztWm6ZYP/M.J178v0szabyMZT5P00.',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:10','2026-08-14 16:35:10',NULL),('40f0278d-ee63-43bc-b63d-a2c130aaf12e','20000000-0000-4000-8000-000000000001','admin@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$CgWXxSnsRWAEe3Jj66wB9.Rk05aIVDtk5Gv/EDpo0agJ8XtqMI/Oe',NULL,NULL,'admin',1,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('5e6db660-dae3-44a4-a381-b4a494dfa78e','20000000-0000-4000-8000-000000000005','alexander@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$aTrYGFkn.SUYQtpkwWyi9OsCzoxdHsxlqDyk/LEyz/U4FoW.xtLeu',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:08','2026-08-14 16:35:08',NULL),('79b0f387-b58d-41f2-ad42-9f3da235963e','20000000-0000-4000-8000-000000000002','employee@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$UC5vHZLUtwHsBxdikUcsIOBxfXgeEgvr5psVsZCTErcZ6Ux389Ili',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:06','2026-08-14 16:35:06',NULL),('96583c9b-a425-4be7-8588-41f29e3ae0ca','20000000-0000-4000-8000-000000000003','manager@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$83v5pSZo6A3LMz91OgbiP.bxC8nLRM8tpKElBB0tpt6JTwlKprCqa',NULL,NULL,'manager',1,NULL,NULL,'2026-08-14 16:35:04','2026-08-14 16:35:04',NULL),('b427bc79-3f59-4d8c-8b3c-26182f3e0fa7','20000000-0000-4000-8000-000000000006','maria@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$o8DJyaQs7Zu21x3ey5yhzOjVgr0ftR8e5tDF5ALAczhpRFvXhp/lm',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:08','2026-08-14 16:35:08',NULL),('e37a4f35-504c-4e9a-8b3e-0a39fc61d70b','20000000-0000-4000-8000-000000000004','reece@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$h/2KpZV5E9WkpV362IEwXOfYn3amYklesvUi6jeebrxFb9MpCRyo2',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:07','2026-08-14 16:35:07',NULL),('e85d0d66-cf0a-491c-b739-7054a9954fb5','20000000-0000-4000-8000-000000000010','employee@northstar.example',NULL,NULL,NULL,NULL,'$2y$12$DamYyDTfLs151GyIJu/2heyM3nNAFaTdsCbJmtPfkOCZiqj63lH6e',NULL,NULL,'employee',1,NULL,NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11',NULL),('ed764d34-4b67-4add-b5a5-82e622f24129','20000000-0000-4000-8000-000000000007','reyven@gr8tech.example',NULL,NULL,NULL,NULL,'$2y$12$/Erb3AO4ZnS4VEd4nKzFWOxrWHMUG4rPbTjFbxj29LZ2aLeWchG..',NULL,NULL,'manager',1,NULL,NULL,'2026-08-14 16:35:09','2026-08-14 16:35:09',NULL);
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` char(36) DEFAULT NULL,
  `actor_name` varchar(255) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_account_id_foreign` (`account_id`),
  KEY `activity_logs_action_index` (`action`),
  KEY `activity_logs_module_index` (`module`),
  KEY `activity_logs_created_at_index` (`created_at`),
  CONSTRAINT `activity_logs_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `attendance_corrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_corrections` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `corrected_by` char(36) DEFAULT NULL,
  `reason` text NOT NULL,
  `original_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_values`)),
  `corrected_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`corrected_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_corrections_attendance_record_id_index` (`attendance_record_id`),
  KEY `attendance_corrections_corrected_by_index` (`corrected_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `attendance_corrections` WRITE;
/*!40000 ALTER TABLE `attendance_corrections` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_corrections` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `attendance_exceptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_exceptions` (
  `id` char(36) NOT NULL,
  `date` date NOT NULL,
  `type` enum('holiday','company_event','emergency_closure','special_workday') NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `is_working_day` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_exceptions_date_type_unique` (`date`,`type`),
  KEY `attendance_exceptions_date_index` (`date`),
  KEY `attendance_exceptions_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `attendance_exceptions` WRITE;
/*!40000 ALTER TABLE `attendance_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_exceptions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `attendance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_logs_attendance_record_id_action_index` (`attendance_record_id`,`action`),
  KEY `attendance_logs_performed_by_performed_at_index` (`performed_by`,`performed_at`),
  CONSTRAINT `attendance_logs_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `attendance_logs` WRITE;
/*!40000 ALTER TABLE `attendance_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `validation_status` varchar(30) NOT NULL DEFAULT 'valid',
  `notes` text DEFAULT NULL,
  `corrected_by` char(36) DEFAULT NULL,
  `correction_reason` text DEFAULT NULL,
  `corrected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_employee_id_date_unique` (`employee_id`,`date`),
  KEY `attendance_records_date_status_index` (`date`,`status`),
  KEY `attendance_records_validation_status_index` (`validation_status`),
  CONSTRAINT `attendance_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `attendance_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_settings` (
  `id` char(36) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_settings_setting_key_unique` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `attendance_settings` WRITE;
/*!40000 ALTER TABLE `attendance_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_settings` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `cutoff_day_1` tinyint(3) unsigned NOT NULL DEFAULT 10,
  `cutoff_day_2` tinyint(3) unsigned NOT NULL DEFAULT 25,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES ('10000000-0000-4000-8000-000000000002','Northstar People Innovations Inc.','NPI','Secondary company used to validate company-scoped records.','Northstar Office',NULL,NULL,NULL,'Philippines',NULL,'hr@northstar.example',NULL,NULL,NULL,1,10,25,'2026-08-14 16:35:03','2026-08-14 16:35:03'),('22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8 TECH ENTERPRISE INC.','GR8TECH','Primary demonstration company for the GR8TECH HRIS.','Main Office',NULL,NULL,NULL,'Philippines',NULL,'hr@gr8tech.example',NULL,NULL,NULL,1,10,25,'2026-08-14 16:34:53','2026-08-14 16:35:03');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `department_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `budget` decimal(15,2) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `manager_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_department_id_unique` (`department_id`),
  KEY `departments_company_id_index` (`company_id`),
  KEY `departments_manager_id_foreign` (`manager_id`),
  CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES ('1c5856ce-c769-4fe4-9a36-b2dc8552c155','22d0ec1c-9100-45ed-9db9-3b08d2880940','DEPT-GR8-HR','Human Resources','Demo Human Resources department','Main Office - Floor 2',2500000.00,NULL,'20000000-0000-4000-8000-000000000001','2026-08-14 16:35:03','2026-08-14 16:35:03'),('4599a57b-fdb5-477b-a410-70ad0487e5fe','22d0ec1c-9100-45ed-9db9-3b08d2880940','DEPT-GR8-SM','Sales and Marketing','Demo Sales and Marketing department','Main Office - Floor 2',2000000.00,NULL,'20000000-0000-4000-8000-000000000003','2026-08-14 16:35:03','2026-08-14 16:35:03'),('48560b40-f3c5-4c90-adad-851d8105f56d','10000000-0000-4000-8000-000000000002','DEPT-NPI-IT','NPI Information Technology','Demo NPI Information Technology department','Northstar Office',2000000.00,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03'),('7175f562-6b5c-48bb-9c35-fb71233c7c2a','10000000-0000-4000-8000-000000000002','DEPT-NPI-HR','NPI Human Resources','Demo NPI Human Resources department','Northstar Office',1500000.00,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03'),('7c2e9daf-b6fc-4667-9792-b4b3f6b0c495','22d0ec1c-9100-45ed-9db9-3b08d2880940','DEPT-GR8-FIN','Finance','Demo Finance department','Main Office - Floor 1',3000000.00,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03'),('84014998-93d1-4b1f-9d31-efb5e5940e46','22d0ec1c-9100-45ed-9db9-3b08d2880940','DEPT-GR8-IT','Information Technology','Demo Information Technology department','Main Office - Floor 3',3750000.00,NULL,'20000000-0000-4000-8000-000000000007','2026-08-14 16:35:03','2026-08-14 16:35:03'),('90f52535-cb77-4748-8f04-fe46161e44a9','22d0ec1c-9100-45ed-9db9-3b08d2880940','DEPT-GR8-OPS','Operations','Demo Operations department','Main Office - Floor 1',4000000.00,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `document_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_folders` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_folders_employee_id_index` (`employee_id`),
  CONSTRAINT `document_folders_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `document_folders` WRITE;
/*!40000 ALTER TABLE `document_folders` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_folders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `folder_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_employee_id_type_index` (`employee_id`,`type`),
  KEY `documents_folder_id_index` (`folder_id`),
  CONSTRAINT `documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `document_folders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employee_breaks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_breaks` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `break_start` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `break_end` timestamp NULL DEFAULT NULL,
  `break_duration_minutes` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_breaks_attendance_record_id_break_start_index` (`attendance_record_id`,`break_start`),
  CONSTRAINT `employee_breaks_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `employee_breaks` WRITE;
/*!40000 ALTER TABLE `employee_breaks` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_breaks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employee_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_infos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` char(36) NOT NULL,
  `control_no` varchar(100) DEFAULT NULL,
  `id_card_no` varchar(100) DEFAULT NULL,
  `active_status` varchar(50) DEFAULT 'Active',
  `payment_method` varchar(50) DEFAULT NULL,
  `account_no` varchar(100) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `last_edited_at` timestamp NULL DEFAULT NULL,
  `last_edited_reason` text DEFAULT NULL,
  `taxcode` varchar(100) DEFAULT NULL,
  `tin_no` varchar(50) DEFAULT NULL,
  `sss_no` varchar(50) DEFAULT NULL,
  `hdmf_no` varchar(50) DEFAULT NULL,
  `philhealth_no` varchar(50) DEFAULT NULL,
  `hmo_no` varchar(50) DEFAULT NULL,
  `email_personal` varchar(255) DEFAULT NULL,
  `email_company` varchar(255) DEFAULT NULL,
  `resigned_date` date DEFAULT NULL,
  `regular_date` date DEFAULT NULL,
  `resign_process` varchar(100) DEFAULT NULL,
  `resign_on_next_payroll` tinyint(1) NOT NULL DEFAULT 0,
  `paycode` varchar(100) DEFAULT NULL,
  `period_type` varchar(100) DEFAULT NULL,
  `paylevel` varchar(100) DEFAULT NULL,
  `job_grade` varchar(100) DEFAULT NULL,
  `cola` decimal(12,2) DEFAULT NULL,
  `basic_pay_2` decimal(12,2) DEFAULT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `shuttle_location` varchar(100) DEFAULT NULL,
  `cost_center` varchar(100) DEFAULT NULL,
  `sub_cost_center` varchar(100) DEFAULT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `group_schedule` varchar(100) DEFAULT NULL,
  `allow_flexible_time` tinyint(1) NOT NULL DEFAULT 0,
  `max_sick` decimal(8,2) DEFAULT NULL,
  `max_vacation` decimal(8,2) DEFAULT NULL,
  `max_sl` decimal(8,2) DEFAULT NULL,
  `max_spl` decimal(8,2) DEFAULT NULL,
  `max_pl` decimal(8,2) DEFAULT NULL,
  `max_vawc` decimal(8,2) DEFAULT NULL,
  `max_ml` decimal(8,2) DEFAULT NULL,
  `max_bl` decimal(8,2) DEFAULT NULL,
  `max_el` decimal(8,2) DEFAULT NULL,
  `cluster` varchar(100) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `sub_section` varchar(100) DEFAULT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `line_name` varchar(100) DEFAULT NULL,
  `mfg_position` varchar(100) DEFAULT NULL,
  `contract_ref` varchar(100) DEFAULT NULL,
  `project` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `allowance_1` decimal(12,2) DEFAULT NULL,
  `allowance_2` decimal(12,2) DEFAULT NULL,
  `allowance_3` decimal(12,2) DEFAULT NULL,
  `allowance_4` decimal(12,2) DEFAULT NULL,
  `allowance_5` decimal(12,2) DEFAULT NULL,
  `allow_transpo` decimal(12,2) DEFAULT NULL,
  `allow_housing` decimal(12,2) DEFAULT NULL,
  `allow_communication` decimal(12,2) DEFAULT NULL,
  `allow_4_nt` decimal(12,2) DEFAULT NULL,
  `allow_5_nt` decimal(12,2) DEFAULT NULL,
  `override_sss_exclude` tinyint(1) NOT NULL DEFAULT 0,
  `override_sss_employee` decimal(12,2) DEFAULT NULL,
  `override_sss_employer` decimal(12,2) DEFAULT NULL,
  `override_philhealth_exclude` tinyint(1) NOT NULL DEFAULT 0,
  `override_philhealth_employee` decimal(12,2) DEFAULT NULL,
  `override_philhealth_employer` decimal(12,2) DEFAULT NULL,
  `override_pagibig_exclude` tinyint(1) NOT NULL DEFAULT 0,
  `override_pagibig_employee` decimal(12,2) DEFAULT NULL,
  `override_pagibig_employer` decimal(12,2) DEFAULT NULL,
  `override_tax_exclude` tinyint(1) NOT NULL DEFAULT 0,
  `pagibig_voluntary` tinyint(1) NOT NULL DEFAULT 0,
  `pagibig_vol_amount` decimal(12,2) DEFAULT NULL,
  `tax_computation_type` varchar(30) DEFAULT 'annualized',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_infos_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_infos_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `employee_infos` WRITE;
/*!40000 ALTER TABLE `employee_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_infos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employee_other_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_other_infos_employee_id_unique` (`employee_id`),
  CONSTRAINT `employee_other_infos_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `employee_other_infos` WRITE;
/*!40000 ALTER TABLE `employee_other_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_other_infos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employee_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_schedules` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `department_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('Working','Day Off','Leave','Official Business','Holiday','Overtime','Regular Holiday','Special Holiday','Absent') NOT NULL DEFAULT 'Working',
  `schedule_type` enum('fixed','flexible') NOT NULL DEFAULT 'fixed',
  `required_hours` decimal(4,2) NOT NULL DEFAULT 8.00,
  `schedule_template_id` char(36) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_schedules_employee_id_date_unique` (`employee_id`,`date`),
  KEY `employee_schedules_created_by_foreign` (`created_by`),
  KEY `employee_schedules_employee_id_date_index` (`employee_id`,`date`),
  KEY `employee_schedules_department_id_date_index` (`department_id`,`date`),
  KEY `employee_schedules_date_index` (`date`),
  KEY `employee_schedules_schedule_template_id_index` (`schedule_template_id`),
  CONSTRAINT `employee_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_schedules_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_schedules_schedule_template_id_foreign` FOREIGN KEY (`schedule_template_id`) REFERENCES `schedule_templates` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `employee_schedules` WRITE;
/*!40000 ALTER TABLE `employee_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_schedules` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `employee_id` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `department_id` char(36) NOT NULL,
  `position_id` char(36) DEFAULT NULL,
  `created_by` char(36) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL,
  `hire_date` date NOT NULL,
  `employee_status` varchar(255) DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `home_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `sex` varchar(255) DEFAULT NULL,
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
  `payment_method` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payroll_template_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_position_id_foreign` (`position_id`),
  KEY `employees_created_by_foreign` (`created_by`),
  KEY `employees_company_id_index` (`company_id`),
  KEY `employees_payroll_template_id_foreign` (`payroll_template_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_payroll_template_id_foreign` FOREIGN KEY (`payroll_template_id`) REFERENCES `payroll_templates` (`id`),
  CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES ('20000000-0000-4000-8000-000000000001','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0001',NULL,NULL,'Jerson Marg',NULL,'Cerezo','09170000000','1c5856ce-c769-4fe4-9a36-b2dc8552c155','01a00120-8428-73d8-bf52-d4013dabeddf',NULL,48000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000002','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0002',NULL,NULL,'Curt Vincent',NULL,'Guiling','09170000000','90f52535-cb77-4748-8f04-fe46161e44a9','01a00120-844d-7082-a7c6-f112f9e901b1',NULL,30000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000003','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0003',NULL,NULL,'Charlie',NULL,'Cawile','09170000000','4599a57b-fdb5-477b-a410-70ad0487e5fe','01a00120-8447-7183-887e-c4f6578b3b2c',NULL,50000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000004','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0004',NULL,NULL,'Reece',NULL,'Bibaro','09170000000','1c5856ce-c769-4fe4-9a36-b2dc8552c155','01a00120-8430-725a-be69-b6394ba2fc9c',NULL,28000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000005','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0005',NULL,NULL,'Alexander',NULL,'Estares','09170000000','7c2e9daf-b6fc-4667-9792-b4b3f6b0c495','01a00120-8442-7177-b09b-a3cc84e876f1',NULL,32000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000006','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0006',NULL,NULL,'Maria',NULL,'Sampalok','09170000000','7c2e9daf-b6fc-4667-9792-b4b3f6b0c495','01a00120-8442-7177-b09b-a3cc84e876f1',NULL,28000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000007','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0007',NULL,NULL,'Reyven',NULL,'Plaza','09170000000','84014998-93d1-4b1f-9d31-efb5e5940e46','01a00120-843c-70bd-94e9-d08d195cde41',NULL,55000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000008','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0008',NULL,NULL,'Bulbasaur',NULL,'Poke','09170000000','84014998-93d1-4b1f-9d31-efb5e5940e46','01a00120-8436-7008-98de-807b4ed775e3',NULL,36000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000009','22d0ec1c-9100-45ed-9db9-3b08d2880940','EMP-0009',NULL,NULL,'Taylor',NULL,'Swift','09170000000','1c5856ce-c769-4fe4-9a36-b2dc8552c155','01a00120-8430-725a-be69-b6394ba2fc9c',NULL,30000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('20000000-0000-4000-8000-000000000010','10000000-0000-4000-8000-000000000002','NPI-0001',NULL,NULL,'Nora',NULL,'Santos','09170000000','48560b40-f3c5-4c90-adad-851d8105f56d','01a00120-8457-70da-a7d9-e6b97d783249',NULL,35000.00,'2025-01-10','active',NULL,NULL,NULL,NULL,NULL,NULL,'09170000000',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-14 16:35:03','2026-08-14 16:35:03',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `hr_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hr_contacts_responded_by_foreign` (`responded_by`),
  KEY `hr_contacts_user_id_index` (`user_id`),
  KEY `hr_contacts_employee_id_index` (`employee_id`),
  KEY `hr_contacts_status_index` (`status`),
  KEY `hr_contacts_category_index` (`category`),
  KEY `hr_contacts_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `hr_contacts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hr_contacts_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `hr_contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `hr_contacts` WRITE;
/*!40000 ALTER TABLE `hr_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `hr_contacts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `leave_balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `emergency_days_total` int(11) NOT NULL DEFAULT 0,
  `emergency_days_used` int(11) NOT NULL DEFAULT 0,
  `maternity_days_total` int(11) NOT NULL DEFAULT 0,
  `maternity_days_used` int(11) NOT NULL DEFAULT 0,
  `paternity_days_total` int(11) NOT NULL DEFAULT 0,
  `paternity_days_used` int(11) NOT NULL DEFAULT 0,
  `bereavement_days_total` int(11) NOT NULL DEFAULT 0,
  `bereavement_days_used` int(11) NOT NULL DEFAULT 0,
  `study_days_total` int(11) NOT NULL DEFAULT 0,
  `study_days_used` int(11) NOT NULL DEFAULT 0,
  `spl_days_total` int(11) NOT NULL DEFAULT 0,
  `spl_days_used` int(11) NOT NULL DEFAULT 0,
  `vawc_days_total` int(11) NOT NULL DEFAULT 0,
  `vawc_days_used` int(11) NOT NULL DEFAULT 0,
  `bl_days_total` int(11) NOT NULL DEFAULT 0,
  `bl_days_used` int(11) NOT NULL DEFAULT 0,
  `is_balance_set` tinyint(1) NOT NULL DEFAULT 0,
  `sil_deferred` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_balances_employee_id_year_unique` (`employee_id`,`year`),
  KEY `leave_balances_year_index` (`year`),
  CONSTRAINT `leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `leave_balances` WRITE;
/*!40000 ALTER TABLE `leave_balances` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_balances` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `expiry_attempt` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `first_expired_at` timestamp NULL DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `final_expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_approved_by_foreign` (`approved_by`),
  KEY `leave_requests_employee_id_status_index` (`employee_id`,`status`),
  KEY `leave_requests_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `leave_requests_leave_type_status_index` (`leave_type`,`status`),
  KEY `leave_req_expiry_window_idx` (`status`,`expiry_attempt`,`expires_at`),
  CONSTRAINT `leave_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `loan_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loan_payments` (
  `id` char(36) NOT NULL,
  `loan_id` char(36) NOT NULL,
  `payroll_id` char(36) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `balance_after` decimal(12,2) NOT NULL,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_payments_created_by_foreign` (`created_by`),
  KEY `loan_payments_loan_id_index` (`loan_id`),
  KEY `loan_payments_payroll_id_index` (`payroll_id`),
  CONSTRAINT `loan_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_payments_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `loan_payments` WRITE;
/*!40000 ALTER TABLE `loan_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `loan_payments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `loan_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loan_types` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `interest_type` enum('flat','diminishing') NOT NULL DEFAULT 'flat',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_types_company_id_index` (`company_id`),
  CONSTRAINT `loan_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `loan_types` WRITE;
/*!40000 ALTER TABLE `loan_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `loan_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loans` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `loan_type_id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `principal_amount` decimal(12,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `interest_type` enum('flat','diminishing') NOT NULL DEFAULT 'flat',
  `term_months` smallint(5) unsigned NOT NULL,
  `amortization_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_repayable` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remaining_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `requested_by` char(36) DEFAULT NULL,
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loans_loan_type_id_foreign` (`loan_type_id`),
  KEY `loans_requested_by_foreign` (`requested_by`),
  KEY `loans_approved_by_foreign` (`approved_by`),
  KEY `loans_employee_id_status_index` (`employee_id`,`status`),
  KEY `loans_company_id_index` (`company_id`),
  CONSTRAINT `loans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loans_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loans_loan_type_id_foreign` FOREIGN KEY (`loan_type_id`) REFERENCES `loan_types` (`id`),
  CONSTRAINT `loans_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `login_logs` WRITE;
/*!40000 ALTER TABLE `login_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_logs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_07_13_134312_add_google_fields_to_accounts_table',1),(2,'2025_07_13_134935_add_microsoft_id_to_accounts_table',1),(3,'2025_09_18_145540_create_departments_table',1),(4,'2025_09_18_145542_create_accounts_table',1),(5,'2025_09_18_145543_create_positions_table',1),(6,'2025_09_18_145544_create_employees_table',1),(7,'2025_09_18_145545_add_employee_id_foreign_to_accounts_table',1),(8,'2025_09_18_145548_create_payrolls_table',1),(9,'2025_09_18_150533_create_sessions_table',1),(10,'2025_09_18_150601_create_cache_table',1),(11,'2025_09_18_150612_create_jobs_table',1),(12,'2025_09_18_150624_create_failed_jobs_table',1),(13,'2025_09_19_022316_create_attendance_records_table',1),(14,'2025_09_19_022330_create_overtime_requests_table',1),(15,'2025_09_19_022330_create_work_schedules_table',1),(16,'2025_09_19_022331_create_attendance_settings_table',1),(17,'2025_09_19_022331_create_leave_balances_table',1),(18,'2025_09_19_022331_create_leave_requests_table',1),(19,'2025_09_19_022346_create_attendance_exceptions_table',1),(20,'2025_09_19_022346_create_attendance_logs_table',1),(21,'2025_09_30_171709_create_employee_schedules_table',1),(22,'2025_10_18_014123_update_employee_schedules_holiday_status',1),(23,'2025_10_18_040316_add_night_shift_to_attendance_records_table',1),(24,'2025_10_19_221115_create_temp_timekeeping_table',1),(25,'2025_10_19_224709_create_companies_table',1),(26,'2025_10_19_225233_update_companies_table_use_uuid',1),(27,'2025_10_20_000939_add_approval_fields_to_payrolls_table',1),(28,'2025_10_20_011053_add_holiday_pay_fields_to_payrolls_table',1),(29,'2025_10_20_011838_add_special_holiday_premium_to_payrolls_table',1),(30,'2025_10_20_020910_add_scheduled_hours_to_payrolls_table',1),(31,'2025_10_20_034409_add_holiday_day_counts_to_payrolls_table',1),(32,'2025_10_20_074714_create_user_sessions_table',1),(33,'2025_10_20_083249_update_user_sessions_table_use_uuid',1),(34,'2025_10_24_005407_create_positions_table',1),(35,'2025_10_24_021817_create_periods_table',1),(36,'2025_10_24_023441_create_tax_brackets_table',1),(37,'2025_10_24_134745_update_attendance_records_status_enum',1),(38,'2025_10_24_135307_update_employee_schedules_status_enum_add_absent',1),(39,'2025_10_27_075937_add_company_id_to_departments_table',1),(40,'2025_10_27_075942_add_company_id_to_employees_table',1),(41,'2025_10_27_075950_add_company_id_to_positions_table',1),(42,'2025_10_27_080510_assign_existing_data_to_eternal_bright_company',1),(43,'2025_10_27_095544_add_password_reset_to_accounts_table',1),(44,'2025_11_29_010000_create_payments_table',1),(45,'2025_11_29_010001_add_employee_id_foreign_to_payments_table',1),(46,'2025_11_29_222408_update_payments_table_data_types',1),(47,'2025_12_01_142945_add_payment_method_to_payments_table',1),(48,'2025_12_01_143627_describe_payments_table',1),(49,'2025_12_02_154550_add_missing_payroll_columns',1),(50,'2025_12_04_170938_add_statutory_deduction_columns',1),(51,'2026_01_05_204451_create_breaks_table',1),(52,'2026_01_20_140700_update_payrolls_status_column',1),(53,'2026_01_20_141615_fix_payrolls_status_column_length',1),(54,'2026_01_20_142158_add_rejection_columns_to_payrolls_table',1),(55,'2026_01_21_130228_create_documents_table',1),(56,'2026_01_28_154754_create_login_logs_table',1),(57,'2026_02_02_100000_create_time_entries_table',1),(58,'2026_02_03_000000_create_hr_contacts_table',1),(59,'2026_02_20_000001_add_employee_detail_fields_to_employees_table',1),(60,'2026_02_25_000001_add_missing_columns_to_payments_table',1),(61,'2026_02_25_000002_add_foreign_key_processed_by_to_payments_table',1),(62,'2026_02_25_000003_convert_positions_id_to_uuid',1),(63,'2026_02_25_000004_add_missing_columns_to_payments_table',1),(64,'2026_02_25_000005_add_foreign_key_processed_by_to_payments_table',1),(65,'2026_03_02_000001_add_attendance_category_to_hr_contacts_enum',1),(66,'2026_03_03_000002_create_previous_employments_table',1),(67,'2026_03_04_000003_create_employee_other_infos_table',1),(68,'2026_05_08_161109_add_completed_to_attendance_records_status_enum',1),(69,'2026_07_08_000000_create_official_business_requests_table',1),(70,'2026_07_08_000001_fix_official_business_requests_id_columns',1),(71,'2026_07_09_000000_add_leave_pay_columns_to_payrolls_table',1),(72,'2026_07_10_000000_add_ob_times_to_official_business_requests_table',1),(73,'2026_07_12_000000_add_ob_workflow_fields_to_official_business_requests_table',1),(74,'2026_07_14_000000_add_expires_at_to_leave_requests_table',1),(75,'2026_07_14_010000_add_expired_status_to_leave_requests_table',1),(76,'2026_07_14_112142_add_expires_at_to_overtime_requests_table',1),(77,'2026_07_14_192608_add_schedule_type_to_employee_schedules_table',1),(78,'2026_07_16_000000_add_archived_at_to_departments_table',1),(79,'2026_07_16_000001_add_supervisor_id_to_departments_table',1),(80,'2026_07_16_135216_add_holiday_types_to_employee_schedule_status_enum',1),(81,'2026_07_16_180038_create_payroll_templates_table',1),(82,'2026_07_16_180047_add_payroll_template_id_to_employees_and_positions',1),(83,'2026_07_16_184913_add_deleted_at_to_payroll_templates_table',1),(84,'2026_07_17_094107_add_unpaid_leave_deduction_to_payrolls_table',1),(85,'2026_07_18_000000_create_notifications_table',1),(86,'2026_07_18_000000_update_employees_table_for_extended_profile_fields',1),(87,'2026_07_18_000001_add_cutoff_days_to_companies_table',1),(88,'2026_07_18_121318_add_unpaid_leave_columns_to_payrolls_table',1),(89,'2026_07_19_000000_add_unique_period_constraint_to_payrolls_table',1),(90,'2026_07_19_060000_add_sil_days_to_leave_balances_table',1),(91,'2026_07_21_140000_upgrade_periods_for_payroll_workflow',1),(92,'2026_07_21_152742_add_validation_fields_to_periods_table',1),(93,'2026_07_21_180000_add_phase2_validation_fields_to_periods_table',1),(94,'2026_07_21_190000_add_review_and_finalize_fields_to_periods_table',1),(95,'2026_07_22_010000_set_gr8_tech_as_system_company',1),(96,'2026_07_22_020000_restore_multi_company_switching',1),(97,'2026_07_22_030000_add_period_id_to_payrolls_table',1),(98,'2026_07_22_040000_assign_default_employee_schedules',1),(99,'2026_07_22_050000_add_payroll_snapshot_breakdown_columns',1),(100,'2026_07_22_060000_add_loan_deduction_to_payrolls_table',1),(101,'2026_07_22_061000_add_attendance_correction_audit',1),(102,'2026_07_24_000001_rename_supervisor_id_to_manager_id_on_departments_table',1),(103,'2026_07_24_130000_add_canceled_to_overtime_requests_status_enum',1),(104,'2026_07_25_000000_add_unlock_reopen_fields_to_periods_table',1),(105,'2026_07_25_000001_create_request_corrections_table',1),(106,'2026_07_25_121759_change_time_entries_columns_to_datetime',1),(107,'2026_07_26_000001_add_new_fields_to_employees_table',1),(108,'2026_07_26_152557_add_legacy_fields_to_employees_table',1),(109,'2026_07_26_162352_add_legacy_fields_to_employees_table',1),(110,'2026_07_27_000001_add_employee_info_fields_to_employees_table',1),(111,'2026_07_27_041032_create_clean_payroll_tables',1),(112,'2026_07_27_050034_update_all_account_passwords',1),(113,'2026_07_27_060000_rename_sick_leave_columns_to_paid_leave_on_payrolls_table',1),(114,'2026_07_28_000000_add_last_edited_columns_to_employee_infos_table',1),(115,'2026_07_28_000001_add_payment_method_to_employees_table',1),(116,'2026_07_28_010820_add_additional_leaves_to_leave_balances_table',1),(117,'2026_07_28_100000_create_overtime_reminders_table',1),(118,'2026_07_28_174717_create_schedule_templates_table',1),(119,'2026_07_28_174929_add_schedule_template_id_to_employee_schedules_table',1),(120,'2026_07_28_180000_enforce_one_account_per_employee',1),(121,'2026_07_29_000001_revert_employees_address_columns_to_split_fields',1),(122,'2026_07_30_000000_create_activity_logs_table',1),(123,'2026_07_30_000001_add_deleted_at_to_accounts_table',1),(124,'2026_07_30_000001_normalize_attendance_statuses',1),(125,'2026_07_30_100000_add_deadlines_to_periods_table',1),(126,'2026_07_30_100100_add_validation_results_to_periods_table',1),(127,'2026_07_30_120139_create_loan_types_table',1),(128,'2026_07_30_120225_create_loans_table',1),(129,'2026_07_30_120303_create_loan_payments_table',1),(130,'2026_08_02_000000_add_official_business_to_employee_schedule_status',1),(131,'2026_08_03_000001_create_document_folders_table',1),(132,'2026_08_06_000000_create_payroll_adjustments_table',1),(133,'2026_08_10_000000_add_two_stage_expiry_to_requests',1),(134,'2026_08_10_000001_normalize_request_notification_urls',1),(135,'2026_08_11_000001_add_is_paid_to_leave_requests_table',1),(136,'2026_08_11_000002_add_balance_tracking_to_leave_balances_table',1),(137,'2026_08_11_010736_change_emergency_days_default_to_zero_in_leave_balances_table',1),(138,'2026_08_11_142136_add_review_tracking_to_periods_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` char(36) NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `official_business_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `official_business_requests` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `cutoff_period_key` varchar(40) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `expiry_attempt` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `first_expired_at` timestamp NULL DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `final_expired_at` timestamp NULL DEFAULT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `official_business_requests_attendance_record_id_foreign` (`attendance_record_id`),
  KEY `official_business_requests_employee_id_date_index` (`employee_id`,`date`),
  KEY `official_business_requests_status_index` (`status`),
  KEY `official_business_requests_status_expires_at_index` (`status`,`expires_at`),
  KEY `ob_req_expiry_window_idx` (`status`,`expiry_attempt`,`expires_at`),
  CONSTRAINT `official_business_requests_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `official_business_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `official_business_requests` WRITE;
/*!40000 ALTER TABLE `official_business_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `official_business_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `overtime_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `overtime_reminders` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `attendance_record_id` char(36) DEFAULT NULL,
  `date` date NOT NULL,
  `required_hours` decimal(8,2) NOT NULL DEFAULT 8.00,
  `worked_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `extra_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('pending','submitted','dismissed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `emp_date_unique_ot_reminder` (`employee_id`,`date`),
  KEY `overtime_reminders_attendance_record_id_foreign` (`attendance_record_id`),
  KEY `overtime_reminders_employee_id_status_index` (`employee_id`,`status`),
  CONSTRAINT `overtime_reminders_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `overtime_reminders_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `overtime_reminders` WRITE;
/*!40000 ALTER TABLE `overtime_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `overtime_reminders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `overtime_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `overtime_requests` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `date` date NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `hours` decimal(8,2) NOT NULL,
  `rate_multiplier` decimal(3,2) NOT NULL DEFAULT 1.50,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected','expired','canceled') NOT NULL DEFAULT 'pending',
  `approved_by` char(36) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `expiry_attempt` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `first_expired_at` timestamp NULL DEFAULT NULL,
  `resubmitted_at` timestamp NULL DEFAULT NULL,
  `final_expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `overtime_requests_approved_by_foreign` (`approved_by`),
  KEY `overtime_requests_employee_id_date_index` (`employee_id`,`date`),
  KEY `overtime_requests_status_date_index` (`status`,`date`),
  KEY `ot_req_expiry_window_idx` (`status`,`expiry_attempt`,`expires_at`),
  CONSTRAINT `overtime_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `overtime_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `overtime_requests` WRITE;
/*!40000 ALTER TABLE `overtime_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `overtime_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_payroll_id_index` (`payroll_id`),
  KEY `payments_employee_id_index` (`employee_id`),
  KEY `payments_processed_by_foreign` (`processed_by`),
  CONSTRAINT `payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payroll_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payroll_adjustments` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `employee_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('bonus','allowance','deduction','manual') NOT NULL,
  `direction` enum('earning','deduction') NOT NULL,
  `frequency` enum('one_time','recurring') NOT NULL DEFAULT 'one_time',
  `amount` decimal(12,2) NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `reason` text NOT NULL,
  `created_by` char(36) DEFAULT NULL,
  `updated_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_adjustments_created_by_foreign` (`created_by`),
  KEY `payroll_adjustments_updated_by_foreign` (`updated_by`),
  KEY `payroll_adjustments_employee_dates_idx` (`employee_id`,`effective_from`,`effective_to`),
  KEY `payroll_adjustments_company_id_index` (`company_id`),
  KEY `payroll_adjustments_employee_id_index` (`employee_id`),
  CONSTRAINT `payroll_adjustments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payroll_adjustments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_adjustments_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payroll_adjustments` WRITE;
/*!40000 ALTER TABLE `payroll_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_adjustments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payroll_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payroll_templates` WRITE;
/*!40000 ALTER TABLE `payroll_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_templates` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `late_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `undertime_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `late_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `undertime_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `absence_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `night_differential_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_rate` decimal(8,2) NOT NULL DEFAULT 0.00,
  `night_differential_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `rest_day_premium_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `other_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_leave_days` int(11) NOT NULL DEFAULT 0,
  `paid_leave_pay` decimal(10,2) NOT NULL DEFAULT 0.00,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payrolls_employee_period_unique` (`employee_id`,`pay_period_start`,`pay_period_end`),
  KEY `payrolls_approved_by_foreign` (`approved_by`),
  KEY `payrolls_period_id_index` (`period_id`),
  KEY `payrolls_company_id_index` (`company_id`),
  CONSTRAINT `payrolls_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payrolls_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periods` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `previous_period_id` char(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `period_month` tinyint(3) unsigned DEFAULT NULL,
  `period_year` smallint(5) unsigned DEFAULT NULL,
  `period_no` tinyint(3) unsigned DEFAULT NULL,
  `period_type` varchar(30) NOT NULL DEFAULT 'regular',
  `processing_type` varchar(30) NOT NULL DEFAULT 'regular',
  `payroll_date` date DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `request_deadline_at` timestamp NULL DEFAULT NULL,
  `preparation_deadline_at` timestamp NULL DEFAULT NULL,
  `validation_deadline_at` timestamp NULL DEFAULT NULL,
  `lock_deadline_at` timestamp NULL DEFAULT NULL,
  `deadline_extended_at` timestamp NULL DEFAULT NULL,
  `deadline_extended_by` char(36) DEFAULT NULL,
  `deadline_extension_reason` text DEFAULT NULL,
  `working_days` smallint(5) unsigned NOT NULL DEFAULT 0,
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
  `validation_results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_results`)),
  `ready_at` timestamp NULL DEFAULT NULL,
  `ready_by` char(36) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` char(36) DEFAULT NULL,
  `review_notified_at` timestamp NULL DEFAULT NULL,
  `review_expires_at` timestamp NULL DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `finalized_by` char(36) DEFAULT NULL,
  `department_id` char(36) DEFAULT NULL,
  `employee_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employee_ids`)),
  `created_by` varchar(255) NOT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  `unlocked_at` timestamp NULL DEFAULT NULL,
  `unlocked_by` char(36) DEFAULT NULL,
  `reopened_at` timestamp NULL DEFAULT NULL,
  `reopened_by` char(36) DEFAULT NULL,
  `reopen_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periods_company_cycle_unique` (`company_id`,`period_year`,`period_month`,`period_no`),
  KEY `periods_department_id_foreign` (`department_id`),
  KEY `periods_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `periods_previous_period_id_foreign` (`previous_period_id`),
  KEY `periods_company_dates_index` (`company_id`,`start_date`,`end_date`),
  KEY `periods_company_cycle_index` (`company_id`,`period_year`,`period_month`),
  CONSTRAINT `periods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `periods_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `periods_previous_period_id_foreign` FOREIGN KEY (`previous_period_id`) REFERENCES `periods` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS */;
/*!40000 ALTER TABLE `periods` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `payroll_template_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_name_unique` (`name`),
  UNIQUE KEY `positions_code_unique` (`code`),
  KEY `positions_department_id_foreign` (`department_id`),
  KEY `positions_is_active_department_id_index` (`is_active`,`department_id`),
  KEY `positions_level_index` (`level`),
  KEY `positions_company_id_index` (`company_id`),
  KEY `positions_payroll_template_id_foreign` (`payroll_template_id`),
  CONSTRAINT `positions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `positions_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `positions_payroll_template_id_foreign` FOREIGN KEY (`payroll_template_id`) REFERENCES `payroll_templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES ('01a00120-8428-73d8-bf52-d4013dabeddf','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Human Resources Manager','GR8-HRM','Demo GR8TECH Human Resources Manager position','Manager','1c5856ce-c769-4fe4-9a36-b2dc8552c155',45000.00,70000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8430-725a-be69-b6394ba2fc9c','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Human Resources Associate','GR8-HRA','Demo GR8TECH Human Resources Associate position','Associate','1c5856ce-c769-4fe4-9a36-b2dc8552c155',24000.00,38000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8436-7008-98de-807b4ed775e3','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Software Developer','GR8-SDEV','Demo GR8TECH Software Developer position','Associate','84014998-93d1-4b1f-9d31-efb5e5940e46',30000.00,60000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-843c-70bd-94e9-d08d195cde41','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Information Technology Manager','GR8-ITM','Demo GR8TECH Information Technology Manager position','Manager','84014998-93d1-4b1f-9d31-efb5e5940e46',50000.00,80000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8442-7177-b09b-a3cc84e876f1','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Finance Associate','GR8-FINA','Demo GR8TECH Finance Associate position','Associate','7c2e9daf-b6fc-4667-9792-b4b3f6b0c495',26000.00,42000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8447-7183-887e-c4f6578b3b2c','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Sales and Marketing Manager','GR8-SMM','Demo GR8TECH Sales and Marketing Manager position','Manager','4599a57b-fdb5-477b-a410-70ad0487e5fe',45000.00,70000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-844d-7082-a7c6-f112f9e901b1','22d0ec1c-9100-45ed-9db9-3b08d2880940','GR8TECH Operations Associate','GR8-OPSA','Demo GR8TECH Operations Associate position','Associate','90f52535-cb77-4748-8f04-fe46161e44a9',26000.00,42000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8452-714c-94f2-db6ba3a02de0','10000000-0000-4000-8000-000000000002','NPI Human Resources Manager','NPI-HRM','Demo NPI Human Resources Manager position','Manager','7175f562-6b5c-48bb-9c35-fb71233c7c2a',45000.00,70000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL),('01a00120-8457-70da-a7d9-e6b97d783249','10000000-0000-4000-8000-000000000002','NPI Software Developer','NPI-SDEV','Demo NPI Software Developer position','Associate','48560b40-f3c5-4c90-adad-851d8105f56d',30000.00,60000.00,1,'[\"Relevant education or equivalent experience\"]','[\"Perform assigned role responsibilities\"]','2026-08-14 16:35:03','2026-08-14 16:35:03',NULL);
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `previous_employments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `previous_employments` (
  `id` char(36) NOT NULL,
  `employee_id` char(36) NOT NULL,
  `sequence` tinyint(3) unsigned NOT NULL,
  `employment_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `start_month` tinyint(3) unsigned DEFAULT NULL,
  `start_year` smallint(5) unsigned DEFAULT NULL,
  `end_month` tinyint(3) unsigned DEFAULT NULL,
  `end_year` smallint(5) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `previous_employments_employee_id_sequence_unique` (`employee_id`,`sequence`),
  CONSTRAINT `previous_employments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `previous_employments` WRITE;
/*!40000 ALTER TABLE `previous_employments` DISABLE KEYS */;
/*!40000 ALTER TABLE `previous_employments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `request_corrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_corrections` (
  `id` char(36) NOT NULL,
  `request_type` varchar(20) NOT NULL,
  `request_id` char(36) NOT NULL,
  `corrected_by` char(36) DEFAULT NULL,
  `reason` text NOT NULL,
  `original_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`original_values`)),
  `corrected_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`corrected_values`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `request_corrections_request_type_request_id_index` (`request_type`,`request_id`),
  KEY `request_corrections_request_id_index` (`request_id`),
  KEY `request_corrections_corrected_by_index` (`corrected_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `request_corrections` WRITE;
/*!40000 ALTER TABLE `request_corrections` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_corrections` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `schedule_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedule_templates` (
  `id` char(36) NOT NULL,
  `company_id` char(36) DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `schedule_type` enum('fixed','flexible') NOT NULL DEFAULT 'fixed',
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `required_hours` decimal(4,2) NOT NULL DEFAULT 8.00,
  `created_by` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_templates_company_id_code_unique` (`company_id`,`code`),
  KEY `schedule_templates_created_by_foreign` (`created_by`),
  KEY `schedule_templates_company_id_index` (`company_id`),
  CONSTRAINT `schedule_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedule_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `schedule_templates` WRITE;
/*!40000 ALTER TABLE `schedule_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_templates` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tax_brackets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_brackets_is_active_sort_order_index` (`is_active`,`sort_order`),
  KEY `tax_brackets_min_income_max_income_index` (`min_income`,`max_income`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tax_brackets` WRITE;
/*!40000 ALTER TABLE `tax_brackets` DISABLE KEYS */;
INSERT INTO `tax_brackets` VALUES ('165cd3aa-85ca-4118-ae7b-7501ca1d8a99','20% Bracket','₱33,334 – ₱66,667 - ₱1,875 + 20% of the excess over ₱33,333',33334.00,66667.00,20.00,1875.00,33333.00,3,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11'),('25de5b51-0338-4eeb-b635-0d588af42370','25% Bracket','₱66,668 – ₱166,667 - ₱8,541.80 + 25% of the excess over ₱66,667',66668.00,166667.00,25.00,8541.80,66667.00,4,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11'),('7d434f52-d8e5-4502-a895-5309bcddad32','30% Bracket','₱166,668 – ₱666,667 - ₱33,541.80 + 30% of the excess over ₱166,667',166668.00,666667.00,30.00,33541.80,166667.00,5,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11'),('a157f7c1-47bd-4b2c-a7c3-54c7ce1b6f08','35% Bracket','Over ₱666,667 - ₱183,541.80 + 35% of the excess over ₱666,667',666668.00,NULL,35.00,183541.80,666667.00,6,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11'),('d93a6d3c-3df2-4a47-97f2-92a9f6323a81','15% Bracket','₱20,834 – ₱33,333 - 15% of the excess over ₱20,833',20834.00,33333.00,15.00,0.00,20833.00,2,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11'),('da8c746b-db65-4311-b977-a28d6ec18822','Exempt','Up to ₱20,833 - Exempt from tax',0.00,20833.00,0.00,0.00,0.00,1,1,'2023-01-01',NULL,'2026-08-14 16:35:11','2026-08-14 16:35:11');
/*!40000 ALTER TABLE `tax_brackets` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `temp_timekeeping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `temp_timekeeping_employee_id_date_index` (`employee_id`,`date`),
  KEY `temp_timekeeping_import_batch_id_index` (`import_batch_id`),
  KEY `temp_timekeeping_is_processed_index` (`is_processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `temp_timekeeping` WRITE;
/*!40000 ALTER TABLE `temp_timekeeping` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_timekeeping` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `time_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `time_entries` (
  `id` char(36) NOT NULL,
  `attendance_record_id` char(36) NOT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  `hours_worked` decimal(8,2) NOT NULL DEFAULT 0.00,
  `entry_type` enum('regular','overtime','makeup') NOT NULL DEFAULT 'regular',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_entries_attendance_record_id_time_in_index` (`attendance_record_id`,`time_in`),
  CONSTRAINT `time_entries_attendance_record_id_foreign` FOREIGN KEY (`attendance_record_id`) REFERENCES `attendance_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `time_entries` WRITE;
/*!40000 ALTER TABLE `time_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_entries` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_sessions_session_id_unique` (`session_id`),
  KEY `user_sessions_user_id_is_current_index` (`user_id`,`is_current`),
  KEY `user_sessions_expires_at_index` (`expires_at`),
  CONSTRAINT `user_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `work_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_schedules_employee_id_is_active_index` (`employee_id`,`is_active`),
  KEY `work_schedules_effective_date_end_date_index` (`effective_date`,`end_date`),
  CONSTRAINT `work_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `work_schedules` WRITE;
/*!40000 ALTER TABLE `work_schedules` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_schedules` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
