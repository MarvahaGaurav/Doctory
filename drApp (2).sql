-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 10, 2017 at 06:31 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `drApp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$V8msfLi9syfMLzEwYG6HEemA9oOJMcOVCvY3SojDmJiYdgqkyDBHe', 'admin', 1, '2017-11-07 10:58:08', '2017-11-07 13:02:51');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` bigint(20) NOT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `patient_gender` varchar(255) DEFAULT NULL,
  `question` text,
  `previous_illness_desc` text,
  `doctor_id` bigint(20) NOT NULL,
  `time_slot_id` bigint(20) NOT NULL,
  `day_id` bigint(20) NOT NULL,
  `appointment_date` date DEFAULT NULL,
  `status_of_appointment` varchar(255) NOT NULL DEFAULT 'Pending' COMMENT '0 (pending) 1 ( accepted by doctor ) 2 (precessing) 3 (complete) 4 (rejected by doctor )',
  `reffered_to_doctor_id` bigint(20) DEFAULT NULL,
  `rescheduled_by_doctor` bigint(20) DEFAULT NULL,
  `rescheduled_by_patient` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `patient_age`, `patient_gender`, `question`, `previous_illness_desc`, `doctor_id`, `time_slot_id`, `day_id`, `appointment_date`, `status_of_appointment`, `reffered_to_doctor_id`, `rescheduled_by_doctor`, `rescheduled_by_patient`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL, NULL, NULL, 1, 1, 1, NULL, 'Rejected', 3, NULL, NULL, '2017-10-30 12:27:09', '2017-10-31 10:28:03'),
(2, 2, NULL, NULL, NULL, NULL, 1, 2, 6, NULL, 'Pending', NULL, 1, NULL, '2017-10-30 12:45:46', '2017-11-02 04:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` enum('sp','qu') NOT NULL DEFAULT 'sp',
  `icon_path` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon_path`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Heart1', 'sp', '1510308784_beautyplus_20170618225131_save_720.jpg', 'desc', 1, '2017-10-24 14:09:43', '2017-11-10 04:43:04'),
(3, 'speciality3', 'sp', '1510308809_images_(13).jpg', 'Test', 1, '2017-10-24 14:09:48', '2017-11-10 04:45:59'),
(4, 'speciality4', 'sp', '1510308820_images_(11).jpg', 'Description', 1, '2017-10-24 14:09:55', '2017-11-10 04:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `days`
--

CREATE TABLE `days` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `days`
--

INSERT INTO `days` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Sunday', '2017-10-28 09:09:51', '2017-10-28 09:09:51'),
(2, 'Monday', '2017-10-28 09:10:01', '2017-10-28 09:10:01'),
(3, 'Tuesday', '2017-10-28 09:10:15', '2017-10-28 09:10:48'),
(4, 'Wednesday', '2017-10-28 09:10:23', '2017-10-28 09:10:56'),
(5, 'Thursday', '2017-10-28 09:10:23', '2017-10-28 09:10:56'),
(6, 'Friday', '2017-10-28 09:10:23', '2017-10-28 09:10:56'),
(7, 'Saturday', '2017-10-28 09:10:23', '2017-10-28 09:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availa`
--

CREATE TABLE `doctor_availa` (
  `id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) NOT NULL,
  `00:00 - 00:30` int(11) NOT NULL DEFAULT '0',
  `00:30 - 01:00` int(11) NOT NULL DEFAULT '0',
  `01:00 - 01:30` int(11) NOT NULL DEFAULT '0',
  `01:30 - 02:00` int(11) NOT NULL DEFAULT '0',
  `02:00 - 02:30` int(11) NOT NULL DEFAULT '0',
  `02:30 - 03:00` int(11) NOT NULL DEFAULT '0',
  `03:00 - 03:30` int(11) NOT NULL DEFAULT '0',
  `03:30 - 04:00` int(11) NOT NULL DEFAULT '0',
  `04:00 - 04:30` int(11) NOT NULL DEFAULT '0',
  `04:30 - 05:00` int(11) NOT NULL DEFAULT '0',
  `05:00 - 05:30` int(11) NOT NULL DEFAULT '0',
  `05:30 - 06:00` int(11) NOT NULL DEFAULT '0',
  `06:00 - 06:30` int(11) NOT NULL DEFAULT '0',
  `06:30 - 07:00` int(11) NOT NULL DEFAULT '0',
  `07:00 - 07:30` int(11) NOT NULL DEFAULT '0',
  `07:30 - 08:00` int(11) NOT NULL DEFAULT '0',
  `08:00 - 08:30` int(11) NOT NULL DEFAULT '0',
  `08:30 - 09:00` int(11) NOT NULL DEFAULT '0',
  `09:00 - 09:30` int(11) NOT NULL DEFAULT '0',
  `09:30 - 10:00` int(11) NOT NULL DEFAULT '0',
  `10:00 - 10:30` int(11) NOT NULL DEFAULT '0',
  `10:30 - 11:00` int(11) NOT NULL DEFAULT '0',
  `11:00 - 11:30` int(11) NOT NULL DEFAULT '0',
  `11:30 - 12:00` int(11) NOT NULL DEFAULT '0',
  `12:00 - 12:30` int(11) NOT NULL DEFAULT '0',
  `12:30 - 13:00` int(11) NOT NULL DEFAULT '0',
  `13:00 - 13:30` int(11) NOT NULL DEFAULT '0',
  `13:30 - 14:00` int(11) NOT NULL DEFAULT '0',
  `14:00 - 14:30` int(11) NOT NULL DEFAULT '0',
  `14:30 - 15:00` int(11) NOT NULL DEFAULT '0',
  `15:00 - 15:30` int(11) NOT NULL DEFAULT '0',
  `15:30 - 16:00` int(11) NOT NULL DEFAULT '0',
  `16:00 - 16:30` int(11) NOT NULL DEFAULT '0',
  `16:30 - 17:00` int(11) NOT NULL DEFAULT '0',
  `17:00 - 17:30` int(11) NOT NULL DEFAULT '0',
  `17:30 - 18:00` int(11) NOT NULL DEFAULT '0',
  `18:00 - 18:30` int(11) NOT NULL DEFAULT '0',
  `18:30 - 19:00` int(11) NOT NULL DEFAULT '0',
  `19:00 - 19:30` int(11) NOT NULL DEFAULT '0',
  `19:30 - 20:00` int(11) NOT NULL DEFAULT '0',
  `20:00 - 20:30` int(11) NOT NULL DEFAULT '0',
  `20:30 - 21:00` int(11) NOT NULL DEFAULT '0',
  `21:00 - 21:30` int(11) NOT NULL DEFAULT '0',
  `21:30 - 22:00` int(11) NOT NULL DEFAULT '0',
  `22:00 - 22:30` int(11) NOT NULL DEFAULT '0',
  `22:30 - 23:00` int(11) NOT NULL DEFAULT '0',
  `23:00 - 23:30` int(11) NOT NULL DEFAULT '0',
  `23:30 - 24:00` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availabilities`
--

CREATE TABLE `doctor_availabilities` (
  `id` bigint(20) NOT NULL,
  `day_id` bigint(20) NOT NULL,
  `time_slot_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) NOT NULL,
  `availability_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0(not available) 1 (available)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor_availabilities`
--

INSERT INTO `doctor_availabilities` (`id`, `day_id`, `time_slot_id`, `doctor_id`, `availability_status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(2, 1, 3, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(3, 1, 5, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(4, 1, 7, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(5, 1, 8, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(6, 1, 9, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(7, 1, 10, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(8, 2, 1, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(9, 2, 3, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(10, 2, 5, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(11, 2, 7, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(12, 2, 8, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(13, 2, 9, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(14, 2, 10, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(15, 3, 1, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(16, 3, 3, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(17, 3, 5, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(18, 3, 7, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(19, 3, 8, 1, '1', '2017-10-30 11:29:44', '2017-10-30 11:29:44'),
(20, 3, 9, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(21, 3, 10, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(22, 4, 1, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(23, 4, 3, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(24, 4, 5, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(25, 4, 7, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(26, 4, 8, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(27, 4, 9, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(28, 4, 10, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(29, 5, 1, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(30, 5, 3, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(31, 5, 5, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(32, 5, 7, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(33, 5, 8, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(34, 5, 9, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(35, 5, 10, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(36, 6, 1, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(37, 6, 3, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(38, 6, 5, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(39, 6, 7, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(40, 6, 8, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(41, 6, 9, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(42, 6, 10, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(43, 7, 1, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(44, 7, 3, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(45, 7, 5, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(46, 7, 7, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(47, 7, 8, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(48, 7, 9, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(49, 7, 10, 1, '1', '2017-10-30 11:29:45', '2017-10-30 11:29:45'),
(50, 1, 25, 1, '1', '2017-10-30 11:30:47', '2017-10-30 11:30:47'),
(51, 2, 25, 1, '1', '2017-10-30 11:30:47', '2017-10-30 11:30:47'),
(52, 3, 25, 1, '1', '2017-10-30 11:30:47', '2017-10-30 11:30:47'),
(53, 4, 25, 1, '1', '2017-10-30 11:30:47', '2017-10-30 11:30:47'),
(54, 5, 25, 1, '1', '2017-10-30 11:30:48', '2017-10-30 11:30:48'),
(55, 6, 25, 1, '1', '2017-10-30 11:30:48', '2017-10-30 11:30:48'),
(56, 7, 25, 1, '1', '2017-10-30 11:30:48', '2017-10-30 11:30:48');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_motherlanguages`
--

CREATE TABLE `doctor_motherlanguages` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `mother_language_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor_motherlanguages`
--

INSERT INTO `doctor_motherlanguages` (`id`, `user_id`, `mother_language_id`, `created_at`, `updated_at`) VALUES
(20, 3, 2, '2017-10-29 01:20:53', '2017-10-29 01:20:53'),
(21, 2, 1, '2017-10-30 04:01:36', '2017-10-30 04:01:36'),
(22, 2, 2, '2017-10-30 04:01:36', '2017-10-30 04:01:36'),
(41, 1, 1, '2017-11-01 01:27:23', '2017-11-01 01:27:23'),
(42, 1, 2, '2017-11-01 01:27:23', '2017-11-01 01:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_qualifications`
--

CREATE TABLE `doctor_qualifications` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qualification_id` int(11) NOT NULL COMMENT 'qualification id from category and subcategory table',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor_qualifications`
--

INSERT INTO `doctor_qualifications` (`id`, `user_id`, `qualification_id`, `created_at`, `updated_at`) VALUES
(199, 3, 4, '2017-10-29 01:20:52', '2017-10-29 01:20:52'),
(215, 1, 1, '2017-11-01 01:27:23', '2017-11-01 01:27:23'),
(216, 1, 3, '2017-11-01 01:27:23', '2017-11-10 07:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `locales`
--

CREATE TABLE `locales` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locales`
--

INSERT INTO `locales` (`id`, `created_at`, `updated_at`, `code`, `lang_code`, `name`, `display_name`) VALUES
(1, '2017-11-06 04:54:13', '2017-11-06 04:54:13', 'en', NULL, 'English', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2015_02_03_180720_create_locales_table', 2),
(4, '2015_02_03_180721_create_translations_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `mother_languages`
--

CREATE TABLE `mother_languages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mother_languages`
--

INSERT INTO `mother_languages` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'mother_languages12', '2017-10-28 07:55:38', '2017-11-10 07:27:21'),
(2, 'mother_languages2', '2017-10-28 07:55:43', '2017-10-28 07:55:43'),
(3, 'mother_languages3', '2017-10-28 07:55:48', '2017-10-28 07:55:48'),
(4, 'mother_languages4', '2017-10-28 07:55:54', '2017-10-28 07:55:54'),
(5, 'Mother_languages5', '2017-11-09 01:34:49', '2017-11-09 01:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `varified` int(11) NOT NULL DEFAULT '0' COMMENT '0 (false) 1 (true)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`id`, `user_id`, `otp`, `varified`, `created_at`, `updated_at`) VALUES
(1, 1, '', 1, '2017-10-27 06:10:41', '2017-11-07 00:10:01'),
(2, 4, '542202', 0, '2017-11-06 23:44:15', '2017-11-06 23:44:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_bookmarks`
--

CREATE TABLE `patient_bookmarks` (
  `id` bigint(20) NOT NULL,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient_bookmarks`
--

INSERT INTO `patient_bookmarks` (`id`, `patient_id`, `doctor_id`, `created_at`, `updated_at`) VALUES
(3, 2, 1, '2017-10-28 05:55:52', '2017-10-28 05:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `qualifications`
--

CREATE TABLE `qualifications` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` bigint(20) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `qualifications`
--

INSERT INTO `qualifications` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Qualifications1', 1, '2017-10-27 11:24:59', '2017-11-10 05:28:24'),
(3, 'Qualifications2', 1, '2017-10-27 11:25:17', '2017-11-10 07:28:04'),
(4, 'qualifications3', 1, '2017-10-27 11:25:22', '2017-10-27 11:25:22'),
(5, 'qualifications4', 1, '2017-10-27 11:25:28', '2017-10-27 11:25:28');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) NOT NULL,
  `patient_id` bigint(20) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) NOT NULL,
  `status_by_doctor` int(11) NOT NULL DEFAULT '0' COMMENT '0 ( if doctor not published) , 1 (if doctor accept to publish) 2 (if doctor rejected to publish)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `patient_id`, `doctor_id`, `review_text`, `rating`, `status_by_doctor`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'review_text', 4, 2, '2017-10-29 11:16:05', '2017-10-29 06:19:30');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'speciality1', 1, '2017-10-26 12:21:54', '2017-10-25 13:30:08'),
(2, 1, 'speciality2', 1, '2017-10-26 10:31:30', '2017-10-25 13:30:16'),
(3, 2, 'qualification1', 1, '2017-10-26 10:31:07', '2017-10-25 13:30:29'),
(4, 2, 'qualification2', 1, '2017-10-26 10:31:14', '2017-10-25 13:30:39');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, '00:00 am', ' 00:30 am', '2017-10-28 05:57:32', '2017-10-28 09:23:56'),
(2, '00:30 am', '01:00 am', '2017-10-28 05:57:32', '2017-10-28 09:23:52'),
(3, '01:00 am', '01:30 am', '2017-10-28 05:57:32', '2017-10-28 09:24:14'),
(4, '01:30 am', '02:00 am', '2017-10-28 05:57:32', '2017-10-28 09:24:26'),
(5, '02:00 am', '02:30 am', '2017-10-28 05:57:32', '2017-10-28 09:24:41'),
(6, '02:30 am', '03:00 am', '2017-10-28 05:57:32', '2017-10-28 09:24:51'),
(7, '03:00 am', '03:30 am', '2017-10-28 05:57:32', '2017-10-28 09:25:01'),
(8, '03:30 am', '04:00 am', '2017-10-28 05:57:32', '2017-10-28 09:25:11'),
(9, '04:00 am', '04:30 am', '2017-10-28 05:57:32', '2017-10-28 09:25:22'),
(10, '04:30 am', '05:00 am', '2017-10-28 05:57:32', '2017-10-28 09:25:31'),
(11, '05:00 am', '05:30 am', '2017-10-28 05:57:32', '2017-10-28 09:25:40'),
(12, '05:30 am', '06:00 am', '2017-10-28 05:57:32', '2017-10-28 09:25:50'),
(13, '06:00 am', '06:30 am', '2017-10-28 05:57:32', '2017-10-28 09:26:02'),
(14, '06:30 am', '07:00 am', '2017-10-28 05:57:32', '2017-10-28 09:26:11'),
(15, '07:00 am', '07:30 am', '2017-10-28 05:57:32', '2017-10-28 09:26:19'),
(16, '07:30 am', '08:00 am', '2017-10-28 05:57:32', '2017-10-28 09:26:28'),
(17, '08:00 am', '08:30 am', '2017-10-28 05:57:32', '2017-10-28 09:26:37'),
(18, '08:30 am', '09:00 am', '2017-10-28 05:57:32', '2017-10-28 09:26:46'),
(19, '09:00 am', '09:30 am', '2017-10-28 05:57:32', '2017-10-28 09:26:54'),
(20, '09:30 am', '10:00 am', '2017-10-28 05:57:32', '2017-10-28 09:27:05'),
(21, '10:00 am', '10:30 am', '2017-10-28 05:57:32', '2017-10-28 09:27:12'),
(22, '10:30 am', '11:00 am', '2017-10-28 05:57:32', '2017-10-28 09:27:19'),
(23, '11:00 am', '11:30 am', '2017-10-28 05:57:32', '2017-10-28 09:27:31'),
(24, '11:30 am', '12:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:31:27'),
(25, '12:00 pm', '12:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:31:44'),
(26, '12:30 pm', '01:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:10'),
(27, '01:00 pm', '01:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:26'),
(28, '01:30 pm', '02:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:36'),
(29, '02:00 pm', '02:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:43'),
(30, '02:30 pm', '03:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:50'),
(31, '15:00 pm', '03:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:53'),
(32, '15:30 pm', '04:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:32:57'),
(33, '04:00 pm', '04:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:33:21'),
(34, '04:30 pm', '05:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:33:13'),
(35, '05:00 pm', '05:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:34:48'),
(36, '05:30 pm', '06:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:11'),
(37, '06:00 pm', '06:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:23'),
(38, '06:30', '07:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:32'),
(39, '07:00 pm', '07:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:36'),
(40, '07:30 pm', '08:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:41'),
(41, '08:00 pm', '08:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:35:45'),
(42, '08:30 pm', '09:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:37:09'),
(43, '09:30 pm', '09:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:37:12'),
(44, '09:30 pm', '10:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:37:16'),
(45, '10:00 pm', '10:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:37:25'),
(46, '10:30 pm', '11:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:36:40'),
(47, '11:00 pm', '11:30 pm', '2017-10-28 05:57:32', '2017-10-28 09:36:50'),
(48, '11:30 pm', '12:00 pm', '2017-10-28 05:57:32', '2017-10-28 09:37:52');

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `locale_id` int(10) UNSIGNED NOT NULL,
  `translation_id` int(10) UNSIGNED DEFAULT NULL,
  `translation` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `created_at`, `updated_at`, `locale_id`, `translation_id`, `translation`) VALUES
(1, '2017-11-06 04:54:14', '2017-11-06 04:54:14', 1, NULL, 'My Blog'),
(2, '2017-11-06 04:54:14', '2017-11-06 04:54:14', 1, NULL, 'Translate me!');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speciality_id` bigint(20) DEFAULT NULL,
  `experience` bigint(20) DEFAULT '0',
  `working_place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_me` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0(android),1(ios)',
  `user_type` int(11) NOT NULL COMMENT '1 (Doctor) , 2 (Patient)',
  `medical_licence_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuing_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `profile_status` bigint(20) NOT NULL DEFAULT '0',
  `notification` int(11) NOT NULL DEFAULT '1' COMMENT '1 (on) 0 (off)',
  `language` enum('en','ar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `country_code`, `mobile`, `password`, `profile_image`, `speciality_id`, `experience`, `working_place`, `latitude`, `longitude`, `about_me`, `remember_token`, `device_token`, `device_type`, `user_type`, `medical_licence_number`, `issuing_country`, `status`, `profile_status`, `notification`, `language`, `created_at`, `updated_at`) VALUES
(1, 'Gaurav Marvaha', 'gauravmrvh1@gmail.com', '+91', '8410107878', '$2y$10$V8msfLi9syfMLzEwYG6HEemA9oOJMcOVCvY3SojDmJiYdgqkyDBHe', '_1509519443_images.jpg', 2, 222, 'noida', '111515', '121515', 'aboutMeaboutMeaboutMe', 'fcbaf5766cbda68f625a7a104ecd1dc5', 'wefwefwefwefwefwefwefrgrtbr', '1', 1, '321515616516516515', 'india', 1, 1, 0, 'en', '2017-10-27 06:10:41', '2017-11-09 04:52:34'),
(2, 'Patient', 'gauravmrvh11@gmail.com', '+91', '8410107875', '$2y$10$rrK9c14I3JIkkDbBjhp0C.oAxqxctuW4yeXxU3ykiYBxBsHRS10PG', 'thumbnail_1509356546_images.png', 2, 222, 'noida', '111515', '121515', NULL, 'ba442c9c13d6470e8947e36464e0f5d2', 'wefwefwefwefwefwefwefrgrtbr', '1', 2, NULL, NULL, 1, 1, 0, 'en', '2017-10-27 06:10:41', '2017-11-09 06:38:39'),
(3, 'doctor', 'doctor11@gmail.com', '+91', '8410107871', '$2y$10$rrK9c14I3JIkkDbBjhp0C.oAxqxctuW4yeXxU3ykiYBxBsHRS10PG', 'thumbnail_1509259852_images (2).jpg', 2, 222, 'noida', '111515', '121515', NULL, '', 'wefwefwefwefwefwefwefrgrtbr', '1', 1, NULL, NULL, 1, 1, 0, 'ar', '2017-10-27 06:10:41', '2017-11-09 04:52:51'),
(4, 'gaurav marvaha', 'gauravmrvh123@gmail.com', '+91', '88148518985215', '$2y$10$gJOkKngpFrhYt5FsG7I4cOBCWIDjy6l6vQaLmQqw0RdLBRG7fucbK', NULL, NULL, 0, '', NULL, NULL, '', 'e1b9099efdbab7d5c5f31cb3a73a34c1', 'device_token', '0', 1, NULL, NULL, 1, 0, 1, 'ar', '2017-11-06 23:44:15', '2017-11-09 04:52:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_availa`
--
ALTER TABLE `doctor_availa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_availabilities`
--
ALTER TABLE `doctor_availabilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_motherlanguages`
--
ALTER TABLE `doctor_motherlanguages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_qualifications`
--
ALTER TABLE `doctor_qualifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locales`
--
ALTER TABLE `locales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `locales_code_unique` (`code`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mother_languages`
--
ALTER TABLE `mother_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `patient_bookmarks`
--
ALTER TABLE `patient_bookmarks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qualifications`
--
ALTER TABLE `qualifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translations_locale_id_foreign` (`locale_id`),
  ADD KEY `translations_translation_id_foreign` (`translation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `days`
--
ALTER TABLE `days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `doctor_availa`
--
ALTER TABLE `doctor_availa`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `doctor_availabilities`
--
ALTER TABLE `doctor_availabilities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `doctor_motherlanguages`
--
ALTER TABLE `doctor_motherlanguages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `doctor_qualifications`
--
ALTER TABLE `doctor_qualifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;
--
-- AUTO_INCREMENT for table `locales`
--
ALTER TABLE `locales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `mother_languages`
--
ALTER TABLE `mother_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `patient_bookmarks`
--
ALTER TABLE `patient_bookmarks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `translations_locale_id_foreign` FOREIGN KEY (`locale_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `translations_translation_id_foreign` FOREIGN KEY (`translation_id`) REFERENCES `translations` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
