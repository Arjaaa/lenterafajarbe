-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for guru_report
CREATE DATABASE IF NOT EXISTS `guru_report` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `guru_report`;

-- Dumping structure for table guru_report.announcements
CREATE TABLE IF NOT EXISTS `announcements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('info','warning','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_created_by_foreign` (`created_by`),
  CONSTRAINT `announcements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.announcements: ~0 rows (approximately)
INSERT IGNORE INTO `announcements` (`id`, `title`, `description`, `type`, `start_date`, `end_date`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Libur Awal Ramadhan', 'Dimulai 17 Februari, tolong semua guru diharap membaca dan mengerti!', 'info', '2026-02-17', NULL, 1, 1, '2026-05-17 09:26:32', '2026-05-17 09:26:32');

-- Dumping structure for table guru_report.classes
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `homeroom_teacher_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `classes_homeroom_teacher_id_foreign` (`homeroom_teacher_id`),
  CONSTRAINT `classes_homeroom_teacher_id_foreign` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.classes: ~4 rows (approximately)
INSERT IGNORE INTO `classes` (`id`, `name`, `homeroom_teacher_id`, `created_at`, `updated_at`) VALUES
	(1, 'Kelas A', 3, '2026-05-06 21:59:16', '2026-05-06 21:59:16'),
	(2, 'Kelas A', 3, '2026-05-08 16:57:05', '2026-05-08 16:57:05'),
	(3, 'Kelas B', 3, '2026-05-11 17:00:17', '2026-05-11 17:00:17'),
	(4, 'Kelas B', 3, '2026-05-11 17:23:07', '2026-05-11 17:23:07'),
	(5, 'Kelas B', 3, '2026-05-14 09:01:29', '2026-05-14 09:01:29'),
	(6, 'Kelas B', 3, '2026-05-14 21:12:57', '2026-05-14 21:12:57'),
	(7, 'Kelas B', 3, '2026-05-16 01:15:25', '2026-05-16 01:15:25'),
	(8, 'Kelas C', 3, '2026-05-17 00:16:10', '2026-05-17 00:16:10'),
	(9, 'Kelas B', 6, '2026-05-17 10:21:35', '2026-05-17 10:21:35'),
	(10, 'Kelas B', 6, '2026-05-18 00:47:42', '2026-05-18 00:47:42');

-- Dumping structure for table guru_report.class_students
CREATE TABLE IF NOT EXISTS `class_students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` bigint unsigned NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `class_students_class_id_student_id_unique` (`class_id`,`student_id`),
  KEY `class_students_student_id_foreign` (`student_id`),
  CONSTRAINT `class_students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_students_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.class_students: ~14 rows (approximately)
INSERT IGNORE INTO `class_students` (`id`, `class_id`, `student_id`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, NULL),
	(2, 2, 2, NULL, NULL),
	(3, 3, 3, NULL, NULL),
	(4, 4, 4, NULL, NULL),
	(5, 5, 6, NULL, NULL),
	(6, 6, 7, NULL, NULL),
	(7, 7, 8, NULL, NULL),
	(8, 8, 9, NULL, NULL),
	(9, 9, 10, NULL, NULL),
	(10, 10, 11, NULL, NULL),
	(12, 10, 13, NULL, NULL),
	(13, 10, 14, NULL, NULL),
	(14, 10, 17, NULL, NULL),
	(15, 10, 18, NULL, NULL);

-- Dumping structure for table guru_report.daily_reports
CREATE TABLE IF NOT EXISTS `daily_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `shadow_teacher_id` bigint unsigned DEFAULT NULL,
  `therapist_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_reports_student_id_date_unique` (`student_id`,`date`),
  KEY `daily_reports_shadow_teacher_id_foreign` (`shadow_teacher_id`),
  KEY `daily_reports_therapist_id_foreign` (`therapist_id`),
  CONSTRAINT `daily_reports_shadow_teacher_id_foreign` FOREIGN KEY (`shadow_teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_reports_therapist_id_foreign` FOREIGN KEY (`therapist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.daily_reports: ~17 rows (approximately)
INSERT IGNORE INTO `daily_reports` (`id`, `student_id`, `shadow_teacher_id`, `therapist_id`, `date`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 2, '2026-05-07', '2026-05-06 21:59:36', '2026-05-06 21:59:36'),
	(2, 2, NULL, 2, '2026-05-07', '2026-05-08 16:58:19', '2026-05-08 16:58:19'),
	(3, 3, NULL, 2, '2026-05-07', '2026-05-11 17:02:37', '2026-05-11 17:02:37'),
	(4, 4, NULL, 2, '2026-05-07', '2026-05-11 17:23:50', '2026-05-11 17:23:50'),
	(5, 6, NULL, 3, '2026-05-07', '2026-05-14 09:02:49', '2026-05-14 09:02:49'),
	(6, 7, NULL, 3, '2026-05-08', '2026-05-14 21:13:38', '2026-05-14 21:13:38'),
	(7, 5, NULL, 3, '2026-05-08', '2026-05-14 22:42:18', '2026-05-14 22:42:18'),
	(8, 8, NULL, 3, '2026-05-08', '2026-05-16 01:16:04', '2026-05-16 01:16:04'),
	(9, 8, NULL, 3, '2026-05-09', '2026-05-16 01:28:03', '2026-05-16 01:28:03'),
	(10, 8, NULL, 3, '2026-05-10', '2026-05-16 01:31:55', '2026-05-16 01:31:55'),
	(11, 8, NULL, 3, '2026-05-11', '2026-05-16 01:36:33', '2026-05-16 01:36:33'),
	(12, 9, NULL, 3, '2026-05-17', '2026-05-17 00:16:37', '2026-05-17 00:16:37'),
	(13, 9, NULL, 3, '2026-05-18', '2026-05-17 00:21:31', '2026-05-17 00:21:31'),
	(14, 9, NULL, 3, '2026-05-19', '2026-05-17 00:25:46', '2026-05-17 00:25:46'),
	(15, 9, NULL, 7, '2026-05-20', '2026-05-17 09:38:37', '2026-05-17 09:38:37'),
	(16, 5, NULL, 7, '2026-05-17', '2026-05-17 09:43:12', '2026-05-17 09:43:12'),
	(17, 5, NULL, 6, '2026-05-18', '2026-05-17 10:19:02', '2026-05-17 10:19:02'),
	(18, 10, NULL, 6, '2026-05-18', '2026-05-17 10:23:06', '2026-05-17 10:23:06'),
	(19, 11, NULL, 6, '2026-05-18', '2026-05-18 01:05:25', '2026-05-18 01:05:25'),
	(20, 1, NULL, 6, '2026-05-18', '2026-05-18 01:33:12', '2026-05-18 01:33:12');

-- Dumping structure for table guru_report.daily_report_classifications
CREATE TABLE IF NOT EXISTS `daily_report_classifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_report_id` bigint unsigned NOT NULL,
  `physical_condition_category` enum('positif','netral','negatif') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_energy_category` enum('positif','netral','negatif') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood_arrival_category` enum('sangat_baik','baik','cukup','kurang','sangat_kurang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood_end_category` enum('sangat_baik','baik','cukup','kurang','sangat_kurang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood_trend` enum('naik','stabil','turun') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `behavior_category` enum('positif','negatif','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_category` enum('positif','netral','negatif','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `challenge_category` enum('ringan','sedang','berat','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_challenge` tinyint(1) NOT NULL DEFAULT '0',
  `has_homework` tinyint(1) NOT NULL DEFAULT '0',
  `has_other_note` tinyint(1) NOT NULL DEFAULT '0',
  `overall_score` enum('sangat_baik','baik','cukup','kurang','sangat_kurang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_report_classifications_daily_report_id_unique` (`daily_report_id`),
  CONSTRAINT `daily_report_classifications_daily_report_id_foreign` FOREIGN KEY (`daily_report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.daily_report_classifications: ~8 rows (approximately)
INSERT IGNORE INTO `daily_report_classifications` (`id`, `daily_report_id`, `physical_condition_category`, `physical_energy_category`, `mood_arrival_category`, `mood_end_category`, `mood_trend`, `behavior_category`, `response_category`, `challenge_category`, `has_challenge`, `has_homework`, `has_other_note`, `overall_score`, `created_at`, `updated_at`) VALUES
	(1, 5, 'positif', 'positif', 'sangat_baik', 'kurang', 'turun', 'positif', 'positif', 'ringan', 1, 1, 0, 'baik', '2026-05-14 09:03:10', '2026-05-14 09:03:10'),
	(2, 6, 'positif', 'positif', 'sangat_baik', 'kurang', 'turun', 'positif', 'positif', 'ringan', 1, 1, 0, 'baik', '2026-05-14 21:13:58', '2026-05-14 21:13:58'),
	(3, 7, NULL, NULL, 'kurang', 'kurang', 'stabil', NULL, NULL, NULL, 1, 1, 1, 'kurang', '2026-05-14 22:42:22', '2026-05-14 22:42:22'),
	(4, 11, NULL, NULL, 'kurang', 'kurang', 'stabil', NULL, NULL, NULL, 1, 1, 1, 'kurang', '2026-05-16 01:36:54', '2026-05-16 01:36:54'),
	(5, 12, NULL, NULL, 'kurang', 'kurang', 'stabil', NULL, NULL, NULL, 1, 1, 1, 'kurang', '2026-05-17 00:18:46', '2026-05-17 00:18:46'),
	(6, 14, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-17 00:27:00', '2026-05-17 00:27:00'),
	(7, 15, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-17 09:39:41', '2026-05-17 09:39:41'),
	(8, 16, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-17 09:44:05', '2026-05-17 09:44:05'),
	(9, 17, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-17 10:19:46', '2026-05-17 10:19:46'),
	(10, 18, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-17 10:23:35', '2026-05-17 10:23:35'),
	(11, 19, 'negatif', 'negatif', 'cukup', 'cukup', 'stabil', 'negatif', 'negatif', 'sedang', 1, 1, 0, 'kurang', '2026-05-18 01:05:55', '2026-05-18 01:05:55'),
	(12, 20, NULL, NULL, 'cukup', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 'cukup', '2026-05-18 01:33:12', '2026-05-18 01:33:12');

-- Dumping structure for table guru_report.daily_report_details
CREATE TABLE IF NOT EXISTS `daily_report_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_report_id` bigint unsigned NOT NULL,
  `physical_condition_arrival` enum('sehat','sedikit_lelah','kurang_fit','mengantuk','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_condition_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_energy_arrival` enum('ceria','aktif','lelah','tenang','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_energy_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mood_arrival` tinyint unsigned DEFAULT NULL,
  `mood_end` tinyint unsigned DEFAULT NULL,
  `behavior` enum('kooperatif','fokus','aktif','mudah_terdistraksi','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `behavior_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity_notes` text COLLATE utf8mb4_unicode_ci,
  `response` enum('antusias','pasif','perlu_arahan','perlu_pengawasan','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `challenge` enum('kurang_fokus','mudah_terdistraksi','mood_kurang_stabil','sulit_diarahkan','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `challenge_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solution_notes` text COLLATE utf8mb4_unicode_ci,
  `has_homework` tinyint(1) NOT NULL DEFAULT '0',
  `homework_detail` text COLLATE utf8mb4_unicode_ci,
  `photo_physical` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_activity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_other` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_length` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_report_details_daily_report_id_foreign` (`daily_report_id`),
  CONSTRAINT `daily_report_details_daily_report_id_foreign` FOREIGN KEY (`daily_report_id`) REFERENCES `daily_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.daily_report_details: ~13 rows (approximately)
INSERT IGNORE INTO `daily_report_details` (`id`, `daily_report_id`, `physical_condition_arrival`, `physical_condition_other`, `physical_energy_arrival`, `physical_energy_other`, `mood_arrival`, `mood_end`, `behavior`, `behavior_other`, `activity_notes`, `response`, `response_other`, `challenge`, `challenge_other`, `solution_notes`, `has_homework`, `homework_detail`, `photo_physical`, `photo_activity`, `photo_other`, `text_length`, `created_at`, `updated_at`) VALUES
	(1, 1, 'sehat', NULL, 'ceria', NULL, 5, 4, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'reports/physical/7gRF70pzgXrHNW1H2SCNKEEeCuuaz7nW5zc5MgvA.jpg', 'reports/activity/WrevhHirD1O4ULzjhKAZ7GkJvdeYnraSViO4Aj9O.png', 'reports/other/hpB1iJGx0wsosenxunmEBBDsvONeHcLJgIrYxcSh.png', 14, '2026-05-06 21:59:36', '2026-05-06 21:59:36'),
	(2, 2, 'sehat', NULL, 'ceria', NULL, 5, 4, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778284700/guru-report/physical/ib8ty7dsagycvbponofa.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778284703/guru-report/activity/l8kbvncpjhepryp37yzk.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778284706/guru-report/other/gmsmhgknysksphzbtw4y.jpg', 14, '2026-05-08 16:58:28', '2026-05-08 16:58:28'),
	(3, 3, 'sehat', NULL, 'ceria', NULL, 5, 4, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778544158/guru-report/physical/xgqxgmxxwvwzrfi7uo9t.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778544163/guru-report/activity/nhelzkbdy6fsw1sqdk78.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778544165/guru-report/other/dc4olv8zfnucstcibtty.jpg', 14, '2026-05-11 17:02:46', '2026-05-11 17:02:46'),
	(4, 4, 'sehat', NULL, 'ceria', NULL, 5, 4, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1778545438/guru-report/physical/unx8ay7d4havuvdegsni.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778545443/guru-report/activity/rbh3pmsewj9ubhq3tfm0.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778545446/guru-report/other/f8zl1slsoybypjr2yqjg.jpg', 14, '2026-05-11 17:24:07', '2026-05-11 17:24:07'),
	(5, 5, 'sehat', NULL, 'ceria', NULL, 5, 2, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778774590/guru-report/physical/ri4hoxq3vhsbmcmgbazv.jpg', NULL, NULL, 14, '2026-05-14 09:03:10', '2026-05-14 09:03:10'),
	(6, 6, 'sehat', NULL, 'ceria', NULL, 5, 2, 'kooperatif', NULL, 'Mengaji, bermain musik, olahraga', 'antusias', NULL, 'kurang_fokus', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778818437/guru-report/physical/tbaxprn8po0c3pxoigxd.jpg', NULL, NULL, 14, '2026-05-14 21:13:58', '2026-05-14 21:13:58'),
	(7, 7, 'lainnya', 'badan anak agak demam', 'lainnya', 'anak sedikit lemas', 2, 2, 'lainnya', 'anak susah untuk fokus', 'Mengaji, membaca, olahraga', 'lainnya', 'anak kurang antusias', 'lainnya', 'anak kurang fokus mungkin karena sakit', 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778823740/guru-report/physical/ljrlpqstgtvdphcasaax.jpg', NULL, NULL, 13, '2026-05-14 22:42:22', '2026-05-14 22:42:22'),
	(8, 11, 'lainnya', 'badan anak agak demam', 'lainnya', 'anak sedikit lemas', 2, 2, 'lainnya', 'anak susah untuk fokus', 'Mengaji, membaca, olahraga', 'lainnya', 'anak kurang antusias', 'lainnya', 'anak kurang fokus mungkin karena sakit', 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778920602/guru-report/physical/llwnzsljinlmjqj4y1zn.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1778920613/guru-report/activity/rxw9famdor2utz3wsklo.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778920615/guru-report/other/iewh9sdbc8zfrxpgmdnm.png', 13, '2026-05-16 01:36:54', '2026-05-16 01:36:54'),
	(9, 12, 'lainnya', 'badan anak agak demam', 'lainnya', 'anak sedikit lemas', 2, 2, 'lainnya', 'anak susah untuk fokus', 'Mengaji, membaca, olahraga', 'lainnya', 'anak kurang antusias', 'lainnya', 'anak kurang fokus mungkin karena sakit', 'Diberi pendekatan personal dan istirahat', 1, 'Baca halaman 10-15 buku tematik', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779002275/guru-report/physical/kio4jatetinqjzsxmmqu.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1779002308/guru-report/activity/b2y1scjcy96kofgwvz6r.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779002326/guru-report/other/nkt2yqp9p0dgaenhsqqh.png', 13, '2026-05-17 00:18:46', '2026-05-17 00:18:46'),
	(10, 14, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779002768/guru-report/physical/zfwqaekhkxtpcuqyryyi.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1779002815/guru-report/activity/hk8njyzckoqbin1vtcnu.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779002821/guru-report/other/e4xjidw3siafuv5kfxq3.png', 12, '2026-05-17 00:27:00', '2026-05-17 00:27:00'),
	(11, 15, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779035953/guru-report/physical/cafp1rkobme9wbgv1ug6.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1779035976/guru-report/activity/hmbesjb5aodmj7n9xbza.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779035979/guru-report/other/ykto87mvswo8dolch28q.png', 12, '2026-05-17 09:39:41', '2026-05-17 09:39:41'),
	(12, 16, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779036214/guru-report/physical/hzlrls8xl4699ptavp3k.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1779036240/guru-report/activity/euqjbtnnmmivupgcorqf.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779036243/guru-report/other/kyslawzzquukqhbatpb4.png', 12, '2026-05-17 09:44:05', '2026-05-17 09:44:05'),
	(13, 17, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779038358/guru-report/physical/w3lx8kkyay76rewndcxi.jpg', 'https://res.cloudinary.com/dkpfxacj9/video/upload/v1779038381/guru-report/activity/vh4tzvx86evs7tyytouq.mp4', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779038383/guru-report/other/oqblghy91ctqykjlisrz.png', 12, '2026-05-17 10:19:46', '2026-05-17 10:19:46'),
	(14, 18, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779038608/guru-report/physical/fwn3zifkijy6exarjsfc.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779038611/guru-report/activity/el9v2dhcitrvfngpm22a.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779038613/guru-report/other/vtdnq3njnvgbanewjdb9.png', 12, '2026-05-17 10:23:35', '2026-05-17 10:23:35'),
	(15, 19, 'mengantuk', NULL, 'lelah', NULL, 3, 3, 'mudah_terdistraksi', NULL, 'Mengaji, bermain, tidur', 'perlu_arahan', NULL, 'mudah_terdistraksi', NULL, 'Diberi pendekatan personal dan istirahat', 1, 'Memperlancar Membaca dan menulis', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779066349/guru-report/physical/gd8ytrmnhbhrl4ujjupi.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779066352/guru-report/activity/ip892sacke28rhuddzwq.jpg', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779066354/guru-report/other/omtbnyx2ebhecbmj1jar.png', 12, '2026-05-18 01:05:55', '2026-05-18 01:05:55'),
	(16, 20, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, '2026-05-18 01:33:12', '2026-05-18 01:33:12');

-- Dumping structure for table guru_report.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table guru_report.media
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `medially_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `medially_id` bigint unsigned NOT NULL,
  `file_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_medially_type_medially_id_index` (`medially_type`,`medially_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.media: ~0 rows (approximately)

-- Dumping structure for table guru_report.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.migrations: ~5 rows (approximately)
INSERT IGNORE INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2026_03_12_014933_add_role_to_users_table', 1),
	(6, '2026_04_09_024441_create_students_table', 1),
	(7, '2026_05_06_031931_create_classes_table', 1),
	(8, '2026_05_06_031938_create_shadow_groups_table', 1),
	(9, '2026_05_06_031943_create_one_on_one_groups_table', 1),
	(10, '2026_05_07_030716_create_daily_reports_table', 1),
	(11, '2020_06_14_000001_create_media_table', 2),
	(12, '2026_05_10_081805_update_students_table', 2),
	(13, '2026_05_14_155226_create_daily_report_classifications_table', 3),
	(14, '2026_05_15_042604_add_other_notes_to_report_tables', 4),
	(15, '2026_05_16_161656_create_monthly_reports_table', 5),
	(16, '2026_05_17_115708_add_new_columns_to_monthly_reports_table', 6),
	(17, '2026_05_17_160741_create_announcement_table', 7),
	(18, '2026_05_18_080917_add_parent_info_to_students_table', 8);

-- Dumping structure for table guru_report.monthly_reports
CREATE TABLE IF NOT EXISTS `monthly_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `month` tinyint unsigned NOT NULL,
  `year` smallint unsigned NOT NULL,
  `total_reports` int unsigned NOT NULL DEFAULT '0',
  `total_homework_days` int unsigned NOT NULL DEFAULT '0',
  `total_no_homework_days` int unsigned NOT NULL DEFAULT '0',
  `total_challenges` int unsigned NOT NULL DEFAULT '0',
  `physical_condition_stats` json DEFAULT NULL,
  `physical_energy_stats` json DEFAULT NULL,
  `mood_arrival_avg` decimal(3,2) DEFAULT NULL,
  `mood_end_avg` decimal(3,2) DEFAULT NULL,
  `mood_arrival_dominant` json DEFAULT NULL,
  `mood_end_dominant` json DEFAULT NULL,
  `mood_positive_stats` json DEFAULT NULL,
  `mood_trend_stats` json DEFAULT NULL,
  `behavior_stats` json DEFAULT NULL,
  `response_stats` json DEFAULT NULL,
  `independence_stats` json DEFAULT NULL,
  `challenge_stats` json DEFAULT NULL,
  `solution_stats` json DEFAULT NULL,
  `activity_stats` json DEFAULT NULL,
  `overall_score_stats` json DEFAULT NULL,
  `ai_summary` text COLLATE utf8mb4_unicode_ci,
  `ai_attention` text COLLATE utf8mb4_unicode_ci,
  `ai_recommendation` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','generated','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `monthly_reports_student_id_month_year_unique` (`student_id`,`month`,`year`),
  CONSTRAINT `monthly_reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.monthly_reports: ~9 rows (approximately)
INSERT IGNORE INTO `monthly_reports` (`id`, `student_id`, `month`, `year`, `total_reports`, `total_homework_days`, `total_no_homework_days`, `total_challenges`, `physical_condition_stats`, `physical_energy_stats`, `mood_arrival_avg`, `mood_end_avg`, `mood_arrival_dominant`, `mood_end_dominant`, `mood_positive_stats`, `mood_trend_stats`, `behavior_stats`, `response_stats`, `independence_stats`, `challenge_stats`, `solution_stats`, `activity_stats`, `overall_score_stats`, `ai_summary`, `ai_attention`, `ai_recommendation`, `status`, `generated_at`, `created_at`, `updated_at`) VALUES
	(1, 1, 5, 2026, 1, 1, 0, 0, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 4.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Senang": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '[]', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '[]', 'Faisal menunjukkan semangat yang luar biasa dan mood yang sangat positif selama kehadirannya di sekolah bulan ini. Ia sangat kooperatif dan antusias dalam mengikuti kegiatan seperti Mengaji, Bermain musik, dan Olahraga. Kami sangat senang melihat Faisal selalu ceria dan bersemangat saat berinteraksi dengan teman-teman dan guru.', 'Kami ingin mengajak Ayah dan Bunda untuk bersama-sama memperhatikan tingkat kehadiran Faisal di sekolah bulan ini yang masih sangat terbatas. Meskipun demikian, kami melihat adanya kendala kurang fokus yang perlu terus kita pantau dan dukung bersama.', 'Untuk bulan depan, kami sangat menyarankan agar Faisal dapat hadir lebih rutin agar perkembangannya semakin optimal dan konsisten. Melanjutkan pendekatan personal dan waktu istirahat yang efektif di sekolah, kami berharap Faisal dapat terus belajar dengan nyaman dan penuh semangat.', 'generated', '2026-05-17 05:14:43', '2026-05-16 09:24:48', '2026-05-17 05:14:43'),
	(2, 2, 5, 2026, 1, 1, 0, 0, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 4.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Senang": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '[]', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '[]', 'Bulan Mei ini, Faisal menunjukkan semangat yang luar biasa dan selalu ceria saat berada di sekolah, dengan mood positif dan antusiasme yang tinggi. Ia sangat kooperatif dalam mengikuti berbagai kegiatan seperti mengaji, bermain musik, dan olahraga. Meskipun terkadang kurang fokus, kami selalu memberikan pendekatan personal dan istirahat yang membantunya kembali bersemangat.', 'Kami melihat bahwa Faisal hanya hadir satu hari di bulan Mei ini, padahal ia selalu menunjukkan mood positif dan antusiasme yang tinggi saat di sekolah. Kehadiran yang lebih rutin akan sangat membantu Faisal untuk terus berkembang dan menikmati proses belajarnya secara maksimal.', 'Untuk bulan depan, kami sangat merekomendasikan agar Faisal dapat hadir lebih sering di sekolah agar tidak ketinggalan momen-momen berharga. Mari kita terus mendukung Faisal agar ia dapat terus berinteraksi aktif dan mengembangkan potensinya dengan penuh semangat.', 'generated', '2026-05-17 05:15:03', '2026-05-16 09:24:48', '2026-05-17 05:15:03'),
	(3, 3, 5, 2026, 1, 1, 0, 0, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 4.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Senang": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '[]', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '[]', 'Bulan Mei ini, Dhiaz menunjukkan semangat yang luar biasa dan mood yang sangat positif setiap kali ia hadir di sekolah. Ia sangat kooperatif dan antusias dalam mengikuti berbagai kegiatan seperti Mengaji, Bermain musik, dan Olahraga. Meskipun kehadiran Dhiaz terbatas, kami melihat perkembangan yang baik dalam partisipasinya dan ia selalu ceria.', 'Kami mencatat bahwa kehadiran Dhiaz di sekolah pada bulan Mei ini sangat terbatas, hanya satu hari. Kami berharap dapat melihat Dhiaz hadir lebih sering agar ia dapat terus mengembangkan potensinya secara optimal bersama teman-teman.', 'Untuk bulan depan, kami sangat merekomendasikan agar Dhiaz dapat hadir di sekolah secara lebih rutin untuk memaksimalkan proses belajarnya. Mari kita bersama-sama mendukung Dhiaz agar tetap ceria dan fokus dalam setiap aktivitasnya, baik di sekolah maupun di rumah.', 'generated', '2026-05-17 05:15:24', '2026-05-16 09:24:48', '2026-05-17 05:15:24'),
	(4, 4, 5, 2026, 1, 1, 0, 0, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 4.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Senang": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '[]', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '[]', NULL, NULL, NULL, 'generated', '2026-05-17 05:15:41', '2026-05-16 09:24:48', '2026-05-17 05:15:41'),
	(5, 5, 5, 2026, 1, 1, 0, 1, '{"lainnya": {"count": 1, "percent": 100}}', '{"lainnya": {"count": 1, "percent": 100}}', 2.00, 2.00, '{"Sedih": {"count": 1, "percent": 100}}', '{"Sedih": {"count": 1, "percent": 100}}', '{"positive": {"count": 0, "percent": 0}, "neutral_negative": {"count": 1, "percent": 100}}', '{"stabil": {"count": 1, "percent": 100}}', '{"lainnya": {"count": 1, "percent": 100}}', '{"lainnya": {"count": 1, "percent": 100}}', NULL, '{"lainnya": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Membaca": {"count": 1, "percent": 100}, "Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}}', '{"kurang": {"count": 1, "percent": 100}}', 'Faisal hanya dapat hadir satu hari di bulan Mei ini, sehingga partisipasinya dalam kegiatan belajar-mengajar menjadi terbatas. Selama hari kehadirannya, kami mengamati bahwa Faisal menunjukkan suasana hati yang dominan sedih dan kurang antusias, kemungkinan besar karena kondisi tubuhnya yang kurang sehat. Kami telah memberikan pendekatan personal dan waktu istirahat agar Faisal merasa lebih nyaman saat berada di sekolah.', 'Kami sangat menyarankan Ayah dan Bunda untuk lebih memperhatikan kondisi kesehatan Faisal, terutama jika ia menunjukkan gejala demam atau lemas. Kondisi fisik yang kurang prima dapat sangat memengaruhi semangat dan fokus Faisal dalam mengikuti kegiatan di sekolah.', 'Untuk bulan depan, mari kita pastikan Faisal dalam kondisi sehat sebelum berangkat ke sekolah agar ia dapat belajar dengan lebih optimal. Mohon informasikan kepada kami jika ada perubahan kondisi kesehatan Faisal agar kami dapat memberikan dukungan yang sesuai di sekolah.', 'generated', '2026-05-17 05:16:02', '2026-05-16 09:24:48', '2026-05-17 05:16:02'),
	(6, 6, 5, 2026, 1, 1, 0, 1, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 2.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Sedih": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '{"turun": {"count": 1, "percent": 100}}', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '{"baik": {"count": 1, "percent": 100}}', 'Halo Ayah Bunda, kami senang sekali melihat Arjaw menunjukkan semangat kooperatif dan antusiasme yang tinggi dalam mengikuti kegiatan di sekolah bulan ini. Ia sangat menikmati kegiatan Mengaji, Bermain musik, dan Olahraga, serta menunjukkan skor umum yang baik pada hari kehadirannya. Meskipun moodnya sempat menurun saat pulang dan ia mengalami sedikit kendala kurang fokus, kami berhasil mengatasinya dengan pendekatan personal dan istirahat.', 'Kami ingin mengajak Ayah Bunda untuk memperhatikan kehadiran Arjaw di sekolah yang sangat terbatas bulan ini. Selain itu, tren mood Arjaw yang dominan menurun saat pulang dan kendala kurang fokus menjadi hal yang perlu kita cermati bersama agar ia bisa lebih nyaman dan optimal dalam belajar.', 'Untuk bulan depan, kami sangat merekomendasikan Arjaw untuk dapat hadir lebih rutin agar ia bisa mendapatkan manfaat maksimal dari program pembelajaran kami. Mari kita diskusikan bersama cara terbaik untuk menjaga semangatnya tetap stabil sepanjang hari dan membantu Arjaw agar lebih fokus di kelas.', 'generated', '2026-05-17 05:16:24', '2026-05-16 09:24:49', '2026-05-17 05:16:24'),
	(7, 7, 5, 2026, 1, 1, 0, 1, '{"sehat": {"count": 1, "percent": 100}}', '{"ceria": {"count": 1, "percent": 100}}', 5.00, 2.00, '{"Sangat Senang": {"count": 1, "percent": 100}}', '{"Sedih": {"count": 1, "percent": 100}}', '{"positive": {"count": 1, "percent": 100}, "neutral_negative": {"count": 0, "percent": 0}}', '{"turun": {"count": 1, "percent": 100}}', '{"kooperatif": {"count": 1, "percent": 100}}', '{"antusias": {"count": 1, "percent": 100}}', NULL, '{"kurang_fokus": {"count": 1, "percent": 100}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 100}}', '{"Mengaji": {"count": 1, "percent": 100}, "Olahraga": {"count": 1, "percent": 100}, "Bermain musik": {"count": 1, "percent": 100}}', '{"baik": {"count": 1, "percent": 100}}', 'Zul menunjukkan semangat yang luar biasa serta perilaku kooperatif dan antusias dalam mengikuti kegiatan selama kehadirannya di sekolah bulan ini. Ia sangat senang saat datang, aktif mengikuti Mengaji, Bermain musik, dan Olahraga, serta menunjukkan skor umum yang baik. Meskipun demikian, kami mengamati adanya tren penurunan mood saat pulang dan sedikit kendala fokus yang berhasil diatasi dengan pendekatan personal dan istirahat.', 'Kami melihat adanya tren penurunan mood Zul saat pulang dari sekolah, yang perlu kita perhatikan bersama agar ia tetap merasa nyaman dan bahagia. Selain itu, kehadiran Zul yang hanya satu hari bulan ini mungkin berkontribusi pada penyesuaian mood dan rutinitasnya di sekolah.', 'Mari kita bersama-sama mencari cara untuk menjaga semangat positif Zul hingga akhir hari dan memastikan ia mendapatkan istirahat yang cukup di rumah. Kami sangat merekomendasikan agar Zul dapat hadir lebih rutin di bulan depan untuk membantu stabilitas emosinya dan memaksimalkan perkembangannya di sekolah.', 'generated', '2026-05-17 05:16:46', '2026-05-16 09:24:49', '2026-05-17 05:16:46'),
	(8, 8, 5, 2026, 4, 1, 0, 1, '{"lainnya": {"count": 1, "percent": 25}}', '{"lainnya": {"count": 1, "percent": 25}}', 2.00, 2.00, '{"Sedih": {"count": 1, "percent": 25}}', '{"Sedih": {"count": 1, "percent": 25}}', '{"positive": {"count": 0, "percent": 0}, "neutral_negative": {"count": 4, "percent": 100}}', '{"stabil": {"count": 1, "percent": 25}}', '{"lainnya": {"count": 1, "percent": 25}}', '{"lainnya": {"count": 1, "percent": 25}}', NULL, '{"lainnya": {"count": 1, "percent": 25}}', '{"Diberi pendekatan personal dan istirahat": {"count": 1, "percent": 25}}', '{"Membaca": {"count": 1, "percent": 25}, "Mengaji": {"count": 1, "percent": 25}, "Olahraga": {"count": 1, "percent": 25}}', '{"kurang": {"count": 1, "percent": 25}}', NULL, NULL, NULL, 'generated', '2026-05-17 07:58:13', '2026-05-16 09:27:57', '2026-05-17 07:58:13'),
	(9, 9, 5, 2026, 3, 2, 0, 2, '{"lainnya": {"count": 1, "percent": 33.3}, "mengantuk": {"count": 1, "percent": 33.3}}', '{"lelah": {"count": 1, "percent": 33.3}, "lainnya": {"count": 1, "percent": 33.3}}', 2.50, 2.50, '{"Biasa": {"count": 1, "percent": 33.3}, "Sedih": {"count": 1, "percent": 33.3}}', '{"Biasa": {"count": 1, "percent": 33.3}, "Sedih": {"count": 1, "percent": 33.3}}', '{"positive": {"count": 0, "percent": 0}, "neutral_negative": {"count": 3, "percent": 100}}', '{"stabil": {"count": 2, "percent": 66.7}}', '{"lainnya": {"count": 1, "percent": 33.3}, "mudah_terdistraksi": {"count": 1, "percent": 33.3}}', '{"lainnya": {"count": 1, "percent": 33.3}, "perlu_arahan": {"count": 1, "percent": 33.3}}', NULL, '{"lainnya": {"count": 1, "percent": 33.3}, "mudah_terdistraksi": {"count": 1, "percent": 33.3}}', '{"Diberi pendekatan personal dan istirahat": {"count": 2, "percent": 66.7}}', '{"Tidur": {"count": 1, "percent": 33.3}, "Bermain": {"count": 1, "percent": 33.3}, "Membaca": {"count": 1, "percent": 33.3}, "Mengaji": {"count": 2, "percent": 66.7}, "Olahraga": {"count": 1, "percent": 33.3}}', '{"kurang": {"count": 2, "percent": 66.7}}', 'Bunda dan Ayah, di bulan Mei ini, kehadiran Ananda Alif di sekolah memang cukup terbatas, yaitu 3 hari, yang sejalan dengan kondisi kesehatan Alif yang kurang fit. Selama di sekolah, kami mengamati bahwa Alif menunjukkan mood yang dominan sedih dan kurang antusias dalam mengikuti kegiatan, kemungkinan besar karena badannya yang demam dan lemas. Meskipun demikian, kami selalu berupaya memberikan pendekatan personal dan waktu istirahat agar Alif merasa nyaman, namun skor umum Alif bulan ini masih tergolong kurang.', 'Penting bagi Bunda dan Ayah untuk lebih memantau kondisi kesehatan Alif di rumah, terutama jika ada gejala demam atau lemas, karena hal ini sangat memengaruhi fokus dan semangatnya di sekolah. Kami juga melihat tren mood sedih yang stabil saat datang dan pulang, sehingga perlu diperhatikan lebih lanjut apa yang mungkin menjadi penyebab ketidaknyamanan Alif.', 'Untuk bulan depan, kami menyarankan agar Bunda dan Ayah dapat memastikan Alif dalam kondisi sehat sebelum berangkat ke sekolah dan menginformasikan kepada kami jika ada keluhan kesehatan. Mari kita bersama-sama mencari cara untuk meningkatkan semangat dan antusiasme Alif, baik melalui kegiatan yang disukainya di rumah maupun dengan menciptakan suasana yang lebih ceria.', 'generated', '2026-05-17 09:22:30', '2026-05-17 00:39:45', '2026-05-17 09:22:30');

-- Dumping structure for table guru_report.one_on_one_groups
CREATE TABLE IF NOT EXISTS `one_on_one_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `one_on_one_groups_student_id_foreign` (`student_id`),
  KEY `one_on_one_groups_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `one_on_one_groups_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `one_on_one_groups_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.one_on_one_groups: ~0 rows (approximately)

-- Dumping structure for table guru_report.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table guru_report.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.personal_access_tokens: ~37 rows (approximately)
INSERT IGNORE INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 1, 'api-token', '65fdf6ab5f9c537004cfd12099264b88c23a1e9a71a431b6de65b410f2cf2d07', '["*"]', NULL, NULL, '2026-05-06 21:49:28', '2026-05-06 21:49:28'),
	(2, 'App\\Models\\User', 2, 'api-token', '9ab41feb802b3a94e0f8b31db344c819960e8d75f176834884893ae543723e25', '["*"]', NULL, NULL, '2026-05-06 21:49:46', '2026-05-06 21:49:46'),
	(3, 'App\\Models\\User', 3, 'api-token', '80c7d04488aa26b883bf8e01472f5a1268c44a655e224cecceeb35eb00dcd9aa', '["*"]', NULL, NULL, '2026-05-06 21:50:03', '2026-05-06 21:50:03'),
	(4, 'App\\Models\\User', 2, 'api-token', 'e1f5497469bcf0faa2232cf39e9ffadc31724a5f8dba4d3ac7a77a314dcee746', '["*"]', '2026-05-06 21:59:36', NULL, '2026-05-06 21:50:12', '2026-05-06 21:59:36'),
	(5, 'App\\Models\\User', 1, 'api-token', '564717ab77fa6f94277d0f3fa4205f0de0144f86f895ff142acaa209dbebd433', '["*"]', '2026-05-06 21:59:16', NULL, '2026-05-06 21:57:54', '2026-05-06 21:59:16'),
	(6, 'App\\Models\\User', 1, 'api-token', '795923ba72c734456d91f91eead9e417ab1ab36b0faf84b97115d9cdb50e60f7', '["*"]', '2026-05-08 16:57:05', NULL, '2026-05-08 16:55:59', '2026-05-08 16:57:05'),
	(7, 'App\\Models\\User', 2, 'api-token', '6461f9169031d0cd96d1980ae9089d3c709483e57fd39c673570eed5cce2d1d8', '["*"]', '2026-05-08 16:58:19', NULL, '2026-05-08 16:57:41', '2026-05-08 16:58:19'),
	(8, 'App\\Models\\User', 4, 'api-token', '9a445a39b2f0fedf6ea08859cf61df7941e0053419283e0f9144a75e42d0f668', '["*"]', NULL, NULL, '2026-05-11 16:58:56', '2026-05-11 16:58:56'),
	(9, 'App\\Models\\User', 1, 'api-token', '5c5b380dd724a68e23a00de802f86e5f793cd67be18e89a5e85a0093bc8a9dec', '["*"]', '2026-05-18 02:03:44', NULL, '2026-05-11 16:59:40', '2026-05-18 02:03:44'),
	(10, 'App\\Models\\User', 2, 'api-token', '72b0fac8cc747a35716db45e98c3aafb29b0ed8a2d22c939af2c1e3e85d08b2e', '["*"]', '2026-05-11 17:23:50', NULL, '2026-05-11 17:02:14', '2026-05-11 17:23:50'),
	(11, 'App\\Models\\User', 4, 'api-token', '0bc6f77f86af7ee9486a1e7409cafed5a649ffb2506315de7b219616bbb553b0', '["*"]', '2026-05-17 05:19:28', NULL, '2026-05-11 17:53:48', '2026-05-17 05:19:28'),
	(12, 'App\\Models\\User', 1, 'api-token', '6c24fe04960f7b8d159eee70e231f152e3ca38308f51814380f609d167ff3bf6', '["*"]', '2026-05-11 18:07:55', NULL, '2026-05-11 17:56:15', '2026-05-11 18:07:55'),
	(13, 'App\\Models\\User', 3, 'api-token', '3dda607b73d9760007df472cfb13c67bfc1539a628436f0694b448ff4a88e5ca', '["*"]', '2026-05-17 00:25:46', NULL, '2026-05-14 09:01:06', '2026-05-17 00:25:46'),
	(14, 'App\\Models\\User', 1, 'api-token', '5e05560107298065f0537de89da1d43617feef892bcee17e08e7d7990b00b338', '["*"]', '2026-05-17 07:58:55', NULL, '2026-05-16 09:26:30', '2026-05-17 07:58:55'),
	(15, 'App\\Models\\User', 5, 'api-token', '12328b4ee3e4f885b571508c83634d1da014a84351dd81bd3891c146010b3e7c', '["*"]', NULL, NULL, '2026-05-17 04:30:04', '2026-05-17 04:30:04'),
	(16, 'App\\Models\\User', 6, 'api-token', '5a6aa2bdd63a78b5c410dd1026041410086ef37266ef70f56a9caad2f8dd7008', '["*"]', NULL, NULL, '2026-05-17 04:32:34', '2026-05-17 04:32:34'),
	(17, 'App\\Models\\User', 7, 'api-token', '606aadb0a678b33d45ef09ee8fa3c4583957f415a350a43d778af1c7d8fa0a38', '["*"]', NULL, NULL, '2026-05-17 04:35:48', '2026-05-17 04:35:48'),
	(18, 'App\\Models\\User', 8, 'api-token', '6f8a3b312780a5489c64d608ede93c711f6ce885318762412e44091b804b1187', '["*"]', NULL, NULL, '2026-05-17 04:37:37', '2026-05-17 04:37:37'),
	(19, 'App\\Models\\User', 9, 'api-token', '0bdb338d35fdc8874e810a6f9ddeaa5a21ba4c3e36674297ebd89d76b749a1b5', '["*"]', NULL, NULL, '2026-05-17 04:39:30', '2026-05-17 04:39:30'),
	(20, 'App\\Models\\User', 10, 'api-token', '10b06ace5daa6ec57763bf565ce89d232a42f2e2ebff70c892962421419c9955', '["*"]', NULL, NULL, '2026-05-17 04:40:40', '2026-05-17 04:40:40'),
	(21, 'App\\Models\\User', 1, 'api-token', '81a07383ebce4450d029fc662d1e5c2284dd11f351a4022ed2f32f83f6256756', '["*"]', NULL, NULL, '2026-05-17 08:14:05', '2026-05-17 08:14:05'),
	(23, 'App\\Models\\User', 11, 'api-token', '418cbcffc998295cfdffd078bcda0e184232e6976ed3a9e1a5112a7cf3ff49a7', '["*"]', NULL, NULL, '2026-05-17 08:51:56', '2026-05-17 08:51:56'),
	(24, 'App\\Models\\User', 7, 'api-token', '82d96842bb6f181518fd0a2474e94dad46451a933105868bfe986a38a013a35f', '["*"]', NULL, NULL, '2026-05-17 09:09:10', '2026-05-17 09:09:10'),
	(25, 'App\\Models\\User', 7, 'api-token', '8c60baafc02f003ce8b8c47b5f0b61339b838de7da6dbb960b55bf95de80ec10', '["*"]', NULL, NULL, '2026-05-17 09:11:44', '2026-05-17 09:11:44'),
	(26, 'App\\Models\\User', 7, 'api-token', 'd427d6f8c07924fba8fdf2bea6a4db648fafd4623acf0a6091c48dc4bde9e2f2', '["*"]', NULL, NULL, '2026-05-17 09:12:41', '2026-05-17 09:12:41'),
	(27, 'App\\Models\\User', 7, 'api-token', 'c003e877b6ffb215b160ed1fa37ad6b518c6412aa46d8d1410047dc053ece257', '["*"]', NULL, NULL, '2026-05-17 09:19:40', '2026-05-17 09:19:40'),
	(28, 'App\\Models\\User', 1, 'api-token', 'eb17b7aef3ea4d17c370c73d43afaf1798a84c5efbd8ba492332888fe18f3115', '["*"]', '2026-05-17 09:26:32', NULL, '2026-05-17 09:21:57', '2026-05-17 09:26:32'),
	(29, 'App\\Models\\User', 7, 'api-token', 'ccbc1d27bf7004b21efbbc9859b1fe89b37aec315b77e61f5cc0c546f843bcc9', '["*"]', NULL, NULL, '2026-05-17 09:23:23', '2026-05-17 09:23:23'),
	(30, 'App\\Models\\User', 7, 'api-token', 'e819eb2dc59c70492a1c00ac7999e85ca580692e762631567220b714bcea7413', '["*"]', NULL, NULL, '2026-05-17 09:23:41', '2026-05-17 09:23:41'),
	(31, 'App\\Models\\User', 7, 'api-token', '3bd1b609189f68692ba565ba5f5bd234ef5feecbd6f7c1486e7c93fc452862b1', '["*"]', '2026-05-18 01:02:21', NULL, '2026-05-17 09:26:47', '2026-05-18 01:02:21'),
	(32, 'App\\Models\\User', 7, 'api-token', '90b828364527dfb439115a3ab96f8ea44cb714d9a030b17a07a1bc736c32c588', '["*"]', '2026-05-17 17:36:00', NULL, '2026-05-17 09:27:10', '2026-05-17 17:36:00'),
	(33, 'App\\Models\\User', 6, 'api-token', '3ca20fec2273c18c297705e68efb15cec66c5600e8dbd3fd9653f00a626040b9', '["*"]', '2026-05-18 01:05:25', NULL, '2026-05-17 10:18:29', '2026-05-18 01:05:25'),
	(34, 'App\\Models\\User', 6, 'api-token', '1fc1d1989496f440f8da485a290b2077500626f527c67a2a9c51768fbd7ecf3e', '["*"]', '2026-05-18 02:04:19', NULL, '2026-05-17 17:37:41', '2026-05-18 02:04:19'),
	(35, 'App\\Models\\User', 6, 'api-token', '4e1326d95fd3eecdb4afb663032e0ecedd512b3f56f26475022f2679a3321146', '["*"]', NULL, NULL, '2026-05-18 00:16:44', '2026-05-18 00:16:44'),
	(36, 'App\\Models\\User', 1, 'api-token', '6a6c29a0a79bb66baf806e00c70d9161d296e9802e12a02cbb69bf467bc514e2', '["*"]', '2026-05-18 00:56:21', NULL, '2026-05-18 00:46:43', '2026-05-18 00:56:21'),
	(37, 'App\\Models\\User', 1, 'api-token', 'a70bfed339ae93056064d784775792704317fd495968fca53bd64d827a395c4a', '["*"]', '2026-05-18 01:44:57', NULL, '2026-05-18 01:16:00', '2026-05-18 01:44:57'),
	(38, 'App\\Models\\User', 6, 'api-token', 'ce1b524f37cf68e10d5b0c2c35b631ae039728888b69fe58b5167a73078348ea', '["*"]', '2026-05-18 02:18:54', NULL, '2026-05-18 02:11:29', '2026-05-18 02:18:54');

-- Dumping structure for table guru_report.shadow_groups
CREATE TABLE IF NOT EXISTS `shadow_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` bigint unsigned NOT NULL,
  `pic_id` bigint unsigned NOT NULL,
  `partner_id` bigint unsigned NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shadow_groups_student_id_foreign` (`student_id`),
  KEY `shadow_groups_pic_id_foreign` (`pic_id`),
  KEY `shadow_groups_partner_id_foreign` (`partner_id`),
  CONSTRAINT `shadow_groups_partner_id_foreign` FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shadow_groups_pic_id_foreign` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shadow_groups_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.shadow_groups: ~0 rows (approximately)

-- Dumping structure for table guru_report.students
CREATE TABLE IF NOT EXISTS `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `special_needs` enum('autis','adhd','down_syndrome','lambat_belajar','tunarungu','tunawicara','tunagrahita','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diagnosis_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `parent_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `father_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mother_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `students_parent_id_foreign` (`parent_id`),
  CONSTRAINT `students_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.students: ~15 rows (approximately)
INSERT IGNORE INTO `students` (`id`, `name`, `photo`, `birth_date`, `gender`, `school_name`, `address`, `special_needs`, `diagnosis_notes`, `created_at`, `updated_at`, `parent_id`, `parent_phone`, `father_name`, `mother_name`) VALUES
	(1, 'Faisal', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779067908/guru-report/students/b4kzzxaldgum4bnl4jup.jpg', '2017-03-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Melati No. 5, Jakarta', 'adhd', 'ADHD ringan', '2026-05-06 21:59:16', '2026-05-18 01:31:48', NULL, '081234567801', 'Budi Santoso', 'Siti Aminah'),
	(2, 'Faisal', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068277/guru-report/students/cd6opwnbrvfhho0ctre9.jpg', '2017-03-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Kenanga No. 3, Bandung', 'autis', 'Autis level 1', '2026-05-08 16:57:05', '2026-05-18 01:37:57', NULL, '081234567802', 'Ahmad Fauzi', 'Nurul Hidayah'),
	(3, 'Dhiaz', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068471/guru-report/students/faobxdj1iaoadzw4smzj.jpg', '2016-05-22', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Mawar No. 10, Surabaya', 'down_syndrome', 'Down syndrome ringan', '2026-05-11 17:00:17', '2026-05-18 01:41:11', NULL, '081234567803', 'Hendra Wijaya', 'Dewi Rahayu'),
	(4, 'Osmar', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068532/guru-report/students/nvhwzhiclp34b1wn3k9g.jpg', '2017-07-08', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Anggrek No. 7, Yogyakarta', 'lambat_belajar', 'Lambat belajar', '2026-05-11 17:23:07', '2026-05-18 01:42:13', NULL, '081234567804', 'Rudi Hartono', 'Sri Wahyuni'),
	(5, 'Faisal', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1778548083/guru-report/students/klcucjnshhs4vysddfxp.jpg', '2015-03-10', 'laki-laki', 'SD Negeri 1', 'Jl. Merdeka No1', 'autis', 'Autis level 2', '2026-05-11 18:08:07', '2026-05-18 01:19:00', 4, '081234567890', 'Amba Pratama', 'Zakiyah'),
	(6, 'Arjaw', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068550/guru-report/students/hzaxdnso1pl7jyt3bbs1.jpg', '2016-11-30', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Dahlia No. 2, Semarang', 'adhd', 'ADHD sedang', '2026-05-14 09:01:29', '2026-05-18 01:42:30', NULL, '081234567806', 'Doni Setiawan', 'Rina Susanti'),
	(7, 'Zul', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068560/guru-report/students/f8sfsywh2b2ni4bgsyy5.jpg', '2017-09-14', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Flamboyan No. 8, Malang', 'autis', 'Autis level 2', '2026-05-14 21:12:57', '2026-05-18 01:42:41', NULL, '081234567807', 'Agus Salim', 'Fatimah Zahra'),
	(8, 'Aerish', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068577/guru-report/students/u6y295bbovqicocpe7rc.jpg', '2016-12-05', 'perempuan', 'SLB Lentera Fajar', 'Jl. Bougenville No. 4, Medan', 'tunarungu', 'Tunarungu sebagian', '2026-05-16 01:15:25', '2026-05-18 01:42:58', NULL, '081234567808', 'Bambang Irawan', 'Yuli Astuti'),
	(9, 'Alif', 'https://res.cloudinary.com/dkpfxacj9/image/upload/v1779068701/guru-report/students/re0efpshmfhkir3j3aag.jpg', '2017-06-18', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Cempaka No. 6, Makassar', 'lambat_belajar', 'Lambat belajar sedang', '2026-05-17 00:16:10', '2026-05-18 01:45:02', NULL, '081234567809', 'Eko Prasetyo', 'Indah Permata'),
	(10, 'Daus', NULL, '2017-03-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Teratai No. 1, Palembang', 'adhd', 'ADHD ringan', '2026-05-17 10:21:35', '2026-05-18 01:25:15', NULL, '081234567810', 'Wahyu Hidayat', 'Novi Andriani'),
	(11, 'Jumadi', NULL, '2017-05-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Seroja No. 9, Balikpapan', 'autis', 'Autis level 1', '2026-05-18 00:47:42', '2026-05-18 01:25:15', NULL, '081234567811', 'Rizky Maulana', 'Dian Safitri'),
	(13, 'Jumadi', NULL, '2017-05-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Kamboja No. 11, Denpasar', 'down_syndrome', 'Down syndrome sedang', '2026-05-18 00:53:03', '2026-05-18 01:25:15', NULL, '081234567813', 'Fajar Nugroho', 'Mega Wulandari'),
	(14, 'Daus', NULL, '2017-03-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Melati No. 3, Pontianak', 'tunawicara', 'Tunawicara ringan', '2026-05-18 00:53:17', '2026-05-18 01:25:15', NULL, '081234567814', 'Surya Darma', 'Lestari Ningrum'),
	(17, 'Jumadi', NULL, '2017-05-10', 'laki-laki', 'SLB Lentera Fajar', 'Jl. Marigold No. 15, Pekanbaru', 'lambat_belajar', 'Lambat belajar ringan', '2026-05-18 00:57:12', '2026-05-18 01:25:15', NULL, '081234567817', 'Hendro Susanto', 'Ayu Fitriani'),
	(18, 'Arza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-18 02:03:44', '2026-05-18 02:03:44', NULL, NULL, NULL, NULL);

-- Dumping structure for table guru_report.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('coordinator_main','coordinator_therapist','coordinator_shadow','coordinator_wil','shadow_pj','shadow_teacher','therapist_homeroom','therapist','parent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Lentera Fajar Indonesia',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table guru_report.users: ~11 rows (approximately)
INSERT IGNORE INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `role`, `school_name`, `is_active`, `remember_token`, `created_at`, `updated_at`, `gender`, `address`) VALUES
	(1, 'arza', 'arza@gmail.com', NULL, '$2y$12$bIRoP8pFYlOXWYTrR0bVOOZ4QuZLxB3/WyTdcpK9g4ZBPv5mGApFS', NULL, 'coordinator_main', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-06 21:49:28', '2026-05-06 21:49:28', NULL, NULL),
	(2, 'zen', 'zen@gmail.com', NULL, '$2y$12$/qjOsmbix2Js2X2DFTduKuLZVMVvzT1GwzXKcZsJ9UpMUHZjtBL/.', NULL, 'therapist', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-06 21:49:46', '2026-05-06 21:49:46', NULL, NULL),
	(3, 'waung', 'waung@gmail.com', NULL, '$2y$12$tv3V5t58CExeYqoDucQFeO3o3/LJV70lFXYLOkRPjfjGxU3PDckUu', NULL, 'therapist_homeroom', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-06 21:50:03', '2026-05-06 21:50:03', NULL, NULL),
	(4, 'daus', 'daus@gmail.com', NULL, '$2y$12$j5..TB3wU7PfQqxunI60hevjkB6K517IcO0pc8bo8VZ4qzwLyzSKW', NULL, 'parent', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-11 16:58:56', '2026-05-11 16:58:56', NULL, NULL),
	(5, 'madi', 'madi@gmail.com', NULL, '$2y$12$J7D6oy5X46XZBpNLHjT1VO6Z6D.LMFWQNRTxE6KelAcV/XBT5NIui', '09876788', 'parent', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:30:04', '2026-05-17 04:30:04', NULL, NULL),
	(6, 'aa', 'aa@gmail.com', NULL, '$2y$12$HF1.srAuMMBBCsMRd9IjVOQAfD6t0GHqLT5eQPSsNepTspGytyfa.', '860986410', 'therapist_homeroom', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:32:34', '2026-05-17 04:32:34', NULL, 'kudus'),
	(7, 'arz', 'arz@gmail.com', NULL, '$2y$12$3Q/sYqYJRebe9fp2jXFgkORdir3pDz/QF4ILY52vay8I0oSQ4u996', '26924502', 'therapist', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:35:48', '2026-05-17 04:35:48', NULL, 'semarang'),
	(8, 'arz', 'arzaw@gmail.com', NULL, '$2y$12$I34gMoFH6TGkocWmLQUcB.Bp3Q0F/kZ1qM8cj1N0Dvekv2DjRWyXq', '26924502', 'therapist', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:37:37', '2026-05-17 04:37:37', NULL, 'semarang'),
	(9, 'arji', 'arji@gmail.com', NULL, '$2y$12$kSbmoh4RlX7pWGTjEtCqeeerOid8FNWSrUl45JJtditT9sH.Qg5GO', '26924502', 'therapist', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:39:30', '2026-05-17 04:39:30', 'male', 'semarang'),
	(10, 'jyjy', 'jyjy@gmail.com', NULL, '$2y$12$Qo6dJg8auQhO8.FlHlNHJ.eJgTd4sP/8Mevbtoy49wNcCdQhvuYrC', '09524582592', 'shadow_pj', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 04:40:40', '2026-05-17 04:40:40', 'female', 'kudus'),
	(11, 'enriko', 'enriko@gmail.com', NULL, '$2y$12$DQf.d9TWDns.loE5MDsJwOuoXJoBlcZ7V4UQbgq5rYMmrU8cC6Bx.', '09524582592', 'shadow_pj', 'Lentera Fajar Indonesia', 1, NULL, '2026-05-17 08:50:27', '2026-05-17 08:50:27', 'male', 'kudus');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
