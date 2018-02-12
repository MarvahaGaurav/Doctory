-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 08, 2018 at 02:53 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
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
  `mobile` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `mobile`, `profile_image`, `name`, `linkedin`, `twitter`, `role`, `location`, `created_at`, `updated_at`) VALUES
(1, 'admin@gmail.com', '$2y$10$AHBebkwd/u3n8D8YISbxqeFykgtqSnz/OupJ0p53xTJyaGbJ1VVVq', '8410107875', '1516875163_myDp.jpg', 'gaurav marvaha', 'gaurav@linkedin.com', 'gaurav.twitter@twiter.com', 1, 'Delhi (Rohini)', '2017-11-07 10:58:08', '2018-01-25 10:12:43');

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
  `appointment_date` date NOT NULL,
  `status_of_appointment` varchar(255) NOT NULL DEFAULT 'Pending' COMMENT '0 (pending) 1 ( accepted by doctor ) 2 (precessing) 3 (complete) 4 (rejected by doctor )',
  `reffered_to_doctor_id` bigint(20) DEFAULT NULL,
  `rescheduled_by_doctor` bigint(20) DEFAULT NULL,
  `rescheduled_time_slot_id` varchar(255) DEFAULT NULL,
  `rescheduled_day_id` varchar(255) DEFAULT NULL,
  `rescheduled_date` date DEFAULT NULL,
  `rescheduled_by_patient` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `patient_age`, `patient_gender`, `question`, `previous_illness_desc`, `doctor_id`, `time_slot_id`, `day_id`, `appointment_date`, `status_of_appointment`, `reffered_to_doctor_id`, `rescheduled_by_doctor`, `rescheduled_time_slot_id`, `rescheduled_day_id`, `rescheduled_date`, `rescheduled_by_patient`, `created_at`, `updated_at`) VALUES
(1, 7, NULL, NULL, NULL, NULL, 6, 72, 3, '2018-01-23', 'Cancelled', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-23 11:55:14', '2018-01-23 17:25:31'),
(2, 7, 4, 'Female', 'hzjsjsjsmmm', 'nznsmsmsmsms', 6, 73, 3, '2018-01-23', 'Rejected', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-23 11:56:39', '2018-01-23 17:27:11'),
(3, 8, 2, 'female', 'Earache', 'Cleft lip and palate', 9, 43, 4, '2018-01-24', 'Completed', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-24 07:25:47', '2018-01-24 15:45:06'),
(4, 8, NULL, NULL, NULL, NULL, 9, 45, 4, '2018-01-24', 'Completed', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-24 07:59:41', '2018-01-24 15:45:06'),
(5, 8, NULL, NULL, NULL, NULL, 9, 48, 4, '2018-01-24', 'Completed', NULL, 1, '48', '4', '2018-01-24', NULL, '2018-01-24 08:04:08', '2018-01-24 15:45:06'),
(6, 8, 2, 'Male', 'dhr', 'rjtjjtjt', 9, 64, 4, '2018-01-24', 'Completed', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-24 12:32:08', '2018-01-24 15:43:11'),
(7, 8, NULL, NULL, NULL, NULL, 9, 68, 4, '2018-01-24', 'Completed', NULL, 1, '68', '4', '2018-01-24', NULL, '2018-01-24 12:44:17', '2018-01-24 15:45:19'),
(8, 15, NULL, NULL, NULL, NULL, 16, 68, 5, '2018-01-25', 'Expired', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-25 09:58:56', '2018-01-25 12:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL,
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
(1, 'Dermatology', 'sp', 'Dermatology.png', NULL, 1, '2017-10-24 14:09:32', '2017-11-06 07:19:42'),
(2, 'Pediatric', 'sp', 'Pediatric.png', NULL, 1, '2017-10-24 14:09:43', '2017-11-06 07:19:57'),
(3, 'Psychiatry', 'sp', 'Psychiatry.png', NULL, 1, '2017-10-24 14:09:48', '2017-11-06 07:20:11'),
(4, 'Psychology', 'sp', 'Psychology.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:20:23'),
(5, 'Internal Medicine', 'sp', 'InternalMedicine.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:20:34'),
(6, 'OB/GNY', 'sp', 'OB_GNY.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:20:44'),
(7, 'Plastic Surgery', 'sp', 'PlasticSurgery.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:20:56'),
(8, 'Dental', 'sp', 'Dental.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:07'),
(9, 'ENT', 'sp', 'ENT.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:17'),
(10, 'Ophthalmology', 'sp', 'Ophthalmology.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:30'),
(11, 'Diabetes & Endocrine', 'sp', 'Diabetes&Endocrine.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:40'),
(12, 'Family Medicine', 'sp', 'FamilyMedicine.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:51'),
(13, 'Urology', 'sp', 'Urology.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:21:59'),
(14, 'General Surgery', 'sp', 'GeneralSurgery.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:22:09'),
(15, 'Orthopedic', 'sp', 'Orthopedic.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:22:20'),
(16, 'Nutrition', 'sp', 'Nutrition.png', NULL, 1, '2017-10-24 14:09:55', '2017-11-06 07:22:29');

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
-- Table structure for table `doctor_availabilities`
--

CREATE TABLE `doctor_availabilities` (
  `id` bigint(20) NOT NULL,
  `day_id` bigint(20) NOT NULL,
  `time_slot_id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `doctor_availabilities`
--

INSERT INTO `doctor_availabilities` (`id`, `day_id`, `time_slot_id`, `doctor_id`, `created_at`, `updated_at`) VALUES
(1, 3, 11, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(2, 3, 15, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(3, 3, 19, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(4, 3, 23, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(5, 3, 27, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(6, 3, 35, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(7, 3, 71, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(8, 3, 72, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(9, 3, 73, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(10, 3, 74, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(11, 3, 75, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(12, 3, 76, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(13, 3, 77, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(14, 3, 78, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(15, 3, 79, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(16, 3, 80, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(17, 3, 81, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(18, 3, 82, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(19, 3, 83, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(20, 3, 84, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(21, 3, 85, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(22, 3, 86, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(23, 3, 87, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(24, 3, 88, 6, '2018-01-23 11:54:34', '2018-01-23 11:54:34'),
(25, 6, 1, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(26, 6, 5, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(27, 6, 9, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(28, 6, 13, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(29, 6, 17, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(30, 6, 18, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(31, 6, 21, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(32, 6, 27, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(33, 6, 39, 6, '2018-01-23 11:54:43', '2018-01-23 11:54:43'),
(40, 4, 43, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(41, 4, 44, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(42, 4, 45, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(43, 4, 46, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(44, 4, 47, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(45, 4, 48, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(46, 4, 63, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(47, 4, 64, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(48, 4, 65, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(49, 4, 66, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(50, 4, 67, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(51, 4, 68, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(52, 4, 69, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(53, 4, 70, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(54, 4, 71, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(55, 4, 72, 9, '2018-01-24 12:22:37', '2018-01-24 12:22:37'),
(56, 5, 61, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(57, 5, 62, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(58, 5, 63, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(59, 5, 64, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(60, 5, 65, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(61, 5, 66, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(62, 5, 67, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(63, 5, 68, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(64, 5, 69, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(65, 5, 70, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(66, 5, 71, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(67, 5, 72, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(68, 5, 73, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(69, 5, 74, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(70, 5, 75, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(71, 5, 76, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(72, 5, 77, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39'),
(73, 5, 78, 16, '2018-01-25 09:57:39', '2018-01-25 09:57:39');

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
(1, 2, 3, '2018-01-23 16:56:16', '2018-01-23 16:56:16'),
(5, 6, 2, '2018-01-23 17:25:56', '2018-01-23 17:25:56'),
(8, 9, 2, '2018-01-24 15:27:01', '2018-01-24 15:27:01'),
(9, 13, 2, '2018-01-25 14:35:50', '2018-01-25 14:35:50'),
(10, 14, 1, '2018-01-25 15:08:57', '2018-01-25 15:08:57'),
(11, 16, 1, '2018-01-25 15:26:59', '2018-01-25 15:26:59');

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
(1, 2, 4, '2018-01-23 16:56:16', '2018-01-23 16:56:16'),
(2, 2, 5, '2018-01-23 16:56:16', '2018-01-23 16:56:16'),
(9, 6, 3, '2018-01-23 17:25:56', '2018-01-23 17:25:56'),
(10, 6, 2, '2018-01-23 17:25:56', '2018-01-23 17:25:56'),
(17, 9, 1, '2018-01-24 15:27:01', '2018-01-24 15:27:01'),
(18, 9, 2, '2018-01-24 15:27:01', '2018-01-24 15:27:01'),
(19, 9, 3, '2018-01-24 15:27:01', '2018-01-24 15:27:01'),
(20, 13, 1, '2018-01-25 14:35:50', '2018-01-25 14:35:50'),
(21, 13, 2, '2018-01-25 14:35:50', '2018-01-25 14:35:50'),
(22, 14, 2, '2018-01-25 15:08:57', '2018-01-25 15:08:57'),
(23, 14, 4, '2018-01-25 15:08:57', '2018-01-25 15:08:57'),
(24, 14, 6, '2018-01-25 15:08:57', '2018-01-25 15:08:57'),
(25, 16, 4, '2018-01-25 15:26:59', '2018-01-25 15:26:59'),
(26, 16, 6, '2018-01-25 15:26:59', '2018-01-25 15:26:59'),
(27, 16, 7, '2018-01-25 15:26:59', '2018-01-25 15:26:59');

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
(2, '2014_10_12_100000_create_password_resets_table', 1);

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
(1, 'English', '2017-10-28 07:55:38', '2017-11-02 09:45:17'),
(2, 'Arabic', '2017-10-28 07:55:43', '2017-11-02 09:45:25'),
(3, 'Hindi', '2017-10-28 07:55:48', '2017-11-02 09:45:32');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `doctor_id` bigint(20) DEFAULT NULL,
  `reffered_to_doctor_id` bigint(20) DEFAULT NULL,
  `patient_id` bigint(20) DEFAULT NULL,
  `type` int(11) NOT NULL COMMENT '1 (Rescheduled appointment) 2 (Scheduled appointment) ',
  `appointment_id` int(11) DEFAULT NULL,
  `appointment_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `doctor_id`, `reffered_to_doctor_id`, `patient_id`, `type`, `appointment_id`, `appointment_status`, `created_at`, `updated_at`) VALUES
(1, 6, NULL, 7, 2, 1, NULL, '2018-01-23 11:55:14', '2018-01-23 11:55:14'),
(2, 6, NULL, 7, 9, 1, NULL, '2018-01-23 11:55:21', '2018-01-23 11:55:21'),
(3, 6, NULL, 7, 11, 1, NULL, '2018-01-23 11:55:31', '2018-01-23 11:55:31'),
(4, 6, NULL, 7, 2, 2, NULL, '2018-01-23 11:56:39', '2018-01-23 11:56:39'),
(5, 6, NULL, 7, 10, 2, NULL, '2018-01-23 11:57:12', '2018-01-23 11:57:12'),
(6, 9, NULL, 8, 2, 3, NULL, '2018-01-24 07:25:47', '2018-01-24 07:25:47'),
(7, 9, NULL, 8, 9, 3, NULL, '2018-01-24 07:26:15', '2018-01-24 07:26:15'),
(8, 9, NULL, 8, 2, 4, NULL, '2018-01-24 07:59:42', '2018-01-24 07:59:42'),
(9, 9, NULL, 8, 9, 4, NULL, '2018-01-24 07:59:58', '2018-01-24 07:59:58'),
(13, 9, NULL, 8, 3, 5, 'Accepted', '2018-01-24 08:04:29', '2018-01-24 08:04:29'),
(14, 9, NULL, 8, 12, 5, 'Cancelled', '2018-01-24 08:04:38', '2018-01-24 08:04:38'),
(15, 9, NULL, 8, 2, 6, NULL, '2018-01-24 12:32:09', '2018-01-24 12:32:09'),
(16, 9, NULL, 8, 9, 6, NULL, '2018-01-24 12:32:21', '2018-01-24 12:32:21'),
(20, 9, NULL, 8, 3, 7, 'Accepted', '2018-01-24 12:44:41', '2018-01-24 12:44:41'),
(21, 16, NULL, 15, 2, 8, NULL, '2018-01-25 09:58:56', '2018-01-25 09:58:56');

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
(1, 1, '8054', 0, '2018-01-23 16:53:02', '2018-01-23 16:53:02'),
(2, 2, '', 1, '2018-01-23 16:53:49', '2018-01-23 16:54:12'),
(3, 3, '', 1, '2018-01-23 16:56:42', '2018-01-23 16:56:54'),
(4, 4, '', 1, '2018-01-23 16:57:51', '2018-01-23 16:58:07'),
(5, 5, '5429', 0, '2018-01-23 17:00:20', '2018-01-23 17:00:20'),
(6, 6, '', 1, '2018-01-23 17:16:09', '2018-01-23 17:16:22'),
(7, 7, '', 1, '2018-01-23 17:19:16', '2018-01-23 17:19:58'),
(8, 8, '', 1, '2018-01-24 10:11:54', '2018-01-24 10:12:03'),
(9, 9, '4117', 1, '2018-01-24 10:16:48', '2018-01-24 15:21:43'),
(10, 10, '8708', 0, '2018-01-25 13:47:33', '2018-01-25 13:47:33'),
(11, 11, '', 1, '2018-01-25 14:24:26', '2018-01-25 14:24:32'),
(12, 12, '', 1, '2018-01-25 14:26:34', '2018-01-25 14:26:44'),
(13, 13, '', 1, '2018-01-25 14:35:19', '2018-01-25 14:35:28'),
(14, 14, '', 1, '2018-01-25 15:07:33', '2018-01-25 15:07:51'),
(15, 15, '', 1, '2018-01-25 15:16:00', '2018-01-25 15:16:15'),
(16, 16, '', 1, '2018-01-25 15:25:36', '2018-01-25 15:26:17');

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
(4, 8, 9, '2018-01-24 15:32:38', '2018-01-24 15:32:38'),
(8, 11, 9, '2018-01-25 15:21:15', '2018-01-25 15:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `qualifications`
--

CREATE TABLE `qualifications` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` bigint(20) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `qualifications`
--

INSERT INTO `qualifications` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'MBBS', 1, '2017-10-27 11:24:59', '2017-11-02 09:39:26'),
(2, 'Board', 1, '2017-10-27 11:25:17', '2017-11-02 09:39:36'),
(3, 'Fellowship', 1, '2017-10-27 11:25:22', '2017-11-02 09:39:49'),
(4, 'Bachelor', 1, '2017-10-27 11:25:28', '2017-12-13 19:56:59'),
(5, 'Master', 1, '2017-11-02 09:42:04', '2017-11-03 06:23:47'),
(6, 'PHD', 1, '2017-11-02 09:42:21', '2017-11-02 09:42:21'),
(7, 'BDS', 1, '2017-11-02 09:42:37', '2017-11-02 09:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) NOT NULL,
  `patient_id` bigint(20) NOT NULL,
  `appointment_id` bigint(20) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `review_text` text,
  `rating` int(11) NOT NULL,
  `status_by_doctor` int(11) DEFAULT '0' COMMENT '0 ( if doctor not published) , 1 (if doctor accept to publish) 2 (if doctor rejected to publish)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `patient_id`, `appointment_id`, `doctor_id`, `review_text`, `rating`, `status_by_doctor`, `created_at`, `updated_at`) VALUES
(1, 8, 3, 9, 'Nnn', 5, 2, '2018-01-24 11:03:20', '2018-01-24 15:27:57'),
(2, 8, 4, 9, 'hgff', 5, 0, '2018-01-24 15:34:04', '2018-01-24 15:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) NOT NULL,
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
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, '00:00:00', '00:15:00', '2017-10-28 05:57:32', '2018-01-11 09:36:36'),
(2, '00:15:00', '00:30:00', '2017-10-28 05:57:32', '2018-01-11 09:37:03'),
(3, '00:30:00', '00:45:00', '2017-10-28 05:57:32', '2018-01-11 09:41:07'),
(4, '00:45:00', '01:00:00', '2017-10-28 05:57:32', '2018-01-11 09:41:43'),
(5, '01:00:00', '01:15:00', '2017-10-28 05:57:32', '2018-01-11 09:42:43'),
(6, '01:15:00', '01:30:00', '2017-10-28 05:57:32', '2018-01-11 09:43:12'),
(7, '01:30:00', '01:45:00', '2017-10-28 05:57:32', '2018-01-11 09:43:39'),
(8, '01:45:00', '02:00:00', '2017-10-28 05:57:32', '2018-01-11 09:44:05'),
(9, '02:00:00', '02:15:00', '2017-10-28 05:57:32', '2018-01-11 09:44:29'),
(10, '02:15:00', '02:30:00', '2017-10-28 05:57:32', '2018-01-11 09:45:19'),
(11, '02:30:00', '02:45:00', '2017-10-28 05:57:32', '2018-01-11 09:45:51'),
(12, '02:45:00', '03:00:00', '2017-10-28 05:57:32', '2018-01-11 09:46:27'),
(13, '03:00:00', '03:15:00', '2017-10-28 05:57:32', '2018-01-11 09:46:53'),
(14, '03:15:00', '03:30:00', '2017-10-28 05:57:32', '2018-01-11 09:47:25'),
(15, '03:30:00', '03:45:00', '2017-10-28 05:57:32', '2018-01-11 09:47:50'),
(16, '03:45:00', '04:00:00', '2017-10-28 05:57:32', '2018-01-11 09:48:19'),
(17, '04:00:00', '04:15:00', '2017-10-28 05:57:32', '2018-01-11 09:48:51'),
(18, '04:15:00', '04:30:00', '2017-10-28 05:57:32', '2018-01-11 09:49:21'),
(19, '04:30:00', '04:45:00', '2017-10-28 05:57:32', '2018-01-11 09:49:49'),
(20, '04:45:00', '05:00:00', '2017-10-28 05:57:32', '2018-01-11 09:50:15'),
(21, '05:00:00', '05:15:00', '2017-10-28 05:57:32', '2018-01-11 09:50:39'),
(22, '05:15:00', '05:30:00', '2017-10-28 05:57:32', '2018-01-11 09:50:59'),
(23, '05:30:00', '05:45:00', '2017-10-28 05:57:32', '2018-01-11 09:51:22'),
(24, '05:45:00', '06:00:00', '2017-10-28 05:57:32', '2018-01-11 09:51:54'),
(25, '06:00:00', '06:15:00', '2017-10-28 05:57:32', '2018-01-11 09:52:19'),
(26, '06:15:00', '06:30:00', '2017-10-28 05:57:32', '2018-01-11 09:53:23'),
(27, '06:30:00', '06:45:00', '2017-10-28 05:57:32', '2018-01-11 09:53:46'),
(28, '06:45:00', '07:00:00', '2017-10-28 05:57:32', '2018-01-11 09:54:38'),
(29, '07:00:00', '07:15:00', '2017-10-28 05:57:32', '2018-01-11 09:55:06'),
(30, '07:15:00', '07:30:00', '2017-10-28 05:57:32', '2018-01-11 09:55:28'),
(31, '07:30:00', '07:45:00', '2017-10-28 05:57:32', '2018-01-11 09:56:08'),
(32, '07:45:00', '08:00:00', '2017-10-28 05:57:32', '2018-01-11 09:56:32'),
(33, '08:00:00', '08:15:00', '2017-10-28 05:57:32', '2018-01-11 09:56:55'),
(34, '08:15:00', '08:30:00', '2017-10-28 05:57:32', '2018-01-11 09:57:19'),
(35, '08:30:00', '08:45:00', '2017-10-28 05:57:32', '2018-01-11 09:58:03'),
(36, '08:45:00', '09:00:00', '2017-10-28 05:57:32', '2018-01-11 09:58:33'),
(37, '09:00:00', '09:15:00', '2017-10-28 05:57:32', '2018-01-11 09:59:27'),
(38, '09:15:00', '09:30:00', '2017-10-28 05:57:32', '2018-01-11 09:59:59'),
(39, '09:30:00', '09:45:00', '2017-10-28 05:57:32', '2018-01-11 10:00:20'),
(40, '09:45:00', '10:00:00', '2017-10-28 05:57:32', '2018-01-11 10:00:42'),
(41, '10:00:00', '10:15:00', '2017-10-28 05:57:32', '2018-01-11 10:01:21'),
(42, '10:15:00', '10:30:00', '2017-10-28 05:57:32', '2018-01-11 10:01:49'),
(43, '10:30:00', '10:45:00', '2017-10-28 05:57:32', '2018-01-11 10:02:16'),
(44, '10:45:00', '11:00:00', '2017-10-28 05:57:32', '2018-01-11 10:02:48'),
(45, '11:00:00', '11:15:00', '2017-10-28 05:57:32', '2018-01-11 10:03:14'),
(46, '11:15:00', '11:30:00', '2017-10-28 05:57:32', '2018-01-11 10:03:45'),
(47, '11:30:00', '11:45:00', '2017-10-28 05:57:32', '2018-01-11 10:04:07'),
(48, '11:45:00', '12:00:00', '2017-10-28 05:57:32', '2018-01-11 10:04:34'),
(49, '12:00:00', '12:15:00', '2017-10-28 05:57:32', '2018-01-11 10:08:33'),
(50, '12:15:00', '12:30:00', '2017-10-28 05:57:32', '2018-01-11 10:08:55'),
(51, '12:30:00', '12:45:00', '2017-10-28 05:57:32', '2018-01-11 10:09:19'),
(52, '12:45:00', '13:00:00', '2017-10-28 05:57:32', '2018-01-11 10:11:05'),
(53, '13:00:00', '13:15:00', '2017-10-28 05:57:32', '2018-01-11 10:11:28'),
(54, '13:15:00', '13:30:00', '2017-10-28 05:57:32', '2018-01-11 10:11:48'),
(55, '13:30:00', '13:45:00', '2017-10-28 05:57:32', '2018-01-11 10:12:21'),
(56, '13:45:00', '14:00:00', '2017-10-28 05:57:32', '2018-01-11 10:12:40'),
(57, '14:00:00', '14:15:00', '2017-10-28 05:57:32', '2018-01-11 10:13:03'),
(58, '14:15:00', '14:30:00', '2017-10-28 05:57:32', '2018-01-11 10:13:25'),
(59, '14:30:00', '14:45:00', '2017-10-28 05:57:32', '2018-01-11 10:13:47'),
(60, '14:45:00', '15:00:00', '2017-10-28 05:57:32', '2018-01-11 10:14:15'),
(61, '15:00:00', '15:15:00', '2017-10-28 05:57:32', '2018-01-11 10:14:39'),
(62, '15:15:00', '15:30:00', '2017-10-28 05:57:32', '2018-01-11 10:15:00'),
(63, '15:30:00', '15:45:00', '2017-10-28 05:57:32', '2018-01-11 10:16:13'),
(64, '15:45:00', '16:00:00', '2017-10-28 05:57:32', '2018-01-11 10:16:50'),
(65, '16:00:00', '16:15:00', '2017-10-28 05:57:32', '2018-01-11 10:17:11'),
(66, '16:15:00', '16:30:00', '2017-10-28 05:57:32', '2018-01-11 10:17:32'),
(67, '16:30:00', '16:45:00', '2017-10-28 05:57:32', '2018-01-11 10:17:52'),
(68, '16:45:00', '17:00:00', '2017-10-28 05:57:32', '2018-01-11 10:18:14'),
(69, '17:00:00', '17:15:00', '2017-10-28 05:57:32', '2018-01-11 10:18:34'),
(70, '17:15:00', '17:30:00', '2017-10-28 05:57:32', '2018-01-11 10:18:57'),
(71, '17:30:00', '17:45:00', '2017-10-28 05:57:32', '2018-01-11 10:19:17'),
(72, '17:45:00', '18:00:00', '2017-10-28 05:57:32', '2018-01-11 10:20:41'),
(73, '18:00:00', '18:15:00', '2017-10-28 05:57:32', '2018-01-11 10:21:51'),
(74, '18:15:00', '18:30:00', '2017-10-28 05:57:32', '2018-01-11 10:22:22'),
(75, '18:30:00', '18:45:00', '2017-10-28 05:57:32', '2018-01-11 10:22:42'),
(76, '18:45:00', '19:00:00', '2017-10-28 05:57:32', '2018-01-11 10:23:08'),
(77, '19:00:00', '19:15:00', '2017-10-28 05:57:32', '2018-01-11 10:23:29'),
(78, '19:15:00', '19:30:00', '2017-10-28 05:57:32', '2018-01-11 10:24:10'),
(79, '19:30:00', '19:45:00', '2017-10-28 05:57:32', '2018-01-11 10:24:31'),
(80, '19:45:00', '20:00:00', '2017-10-28 05:57:32', '2018-01-11 10:24:52'),
(81, '20:00:00', '20:15:00', '2017-10-28 05:57:32', '2018-01-11 10:27:22'),
(82, '20:15:00', '20:30:00', '2017-10-28 05:57:32', '2018-01-11 10:27:42'),
(83, '20:30:00', '20:45:00', '2017-10-28 05:57:32', '2018-01-11 10:28:08'),
(84, '20:45:00', '21:00:00', '2017-10-28 05:57:32', '2018-01-11 10:28:31'),
(85, '21:00:00', '21:15:00', '2017-10-28 05:57:32', '2018-01-11 10:28:53'),
(86, '21:15:00', '21:30:00', '2017-10-28 05:57:32', '2018-01-11 10:29:12'),
(87, '21:30:00', '21:45:00', '2017-10-28 05:57:32', '2018-01-11 10:29:33'),
(88, '21:45:00', '22:00:00', '2017-10-28 05:57:32', '2018-01-11 10:29:52'),
(89, '22:00:00', '22:15:00', '2017-10-28 05:57:32', '2018-01-11 10:30:14'),
(90, '22:15:00', '22:30:00', '2017-10-28 05:57:32', '2018-01-11 10:30:41'),
(91, '22:30:00', '22:45:00', '2017-10-28 05:57:32', '2018-01-11 10:31:28'),
(92, '22:45:00', '23:00:00', '2017-10-28 05:57:32', '2018-01-11 10:31:56'),
(93, '23:00:00', '23:15:00', '2017-10-28 05:57:32', '2018-01-11 10:32:20'),
(94, '23:15:00', '23:30:00', '2017-10-28 05:57:32', '2018-01-11 10:32:43'),
(95, '23:30:00', '23:45:00', '2017-10-28 05:57:32', '2018-01-11 10:33:02'),
(96, '23:45:00', '00:00:00', '2017-10-28 05:57:32', '2018-01-11 10:34:04');

-- --------------------------------------------------------

--
-- Table structure for table `time_slots_older`
--

CREATE TABLE `time_slots_older` (
  `id` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `time_slots_older`
--

INSERT INTO `time_slots_older` (`id`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, '00:00:00', '00:30:00', '2017-10-28 05:57:32', '2017-11-15 10:52:05'),
(2, '00:30:00', '01:00:00', '2017-10-28 05:57:32', '2017-11-15 10:55:07'),
(3, '01:00:00', '01:30:00', '2017-10-28 05:57:32', '2017-11-15 10:57:38'),
(4, '01:30:00', '02:00:00', '2017-10-28 05:57:32', '2017-11-15 10:58:46'),
(5, '02:00:00', '02:30:00', '2017-10-28 05:57:32', '2017-11-15 10:59:33'),
(6, '02:30:00', '03:00:00', '2017-10-28 05:57:32', '2017-11-15 10:59:56'),
(7, '03:00:00', '03:30:00', '2017-10-28 05:57:32', '2017-11-15 11:00:54'),
(8, '03:30:00', '04:00:00', '2017-10-28 05:57:32', '2017-11-15 11:01:18'),
(9, '04:00:00', '04:30:00', '2017-10-28 05:57:32', '2017-11-15 11:02:06'),
(10, '04:30:00', '05:00:00', '2017-10-28 05:57:32', '2017-11-15 11:02:32'),
(11, '05:00:00', '05:30:00', '2017-10-28 05:57:32', '2017-11-15 11:03:00'),
(12, '05:30:00', '06:00:00', '2017-10-28 05:57:32', '2017-11-15 11:03:32'),
(13, '06:00:00', '06:30:00', '2017-10-28 05:57:32', '2017-11-15 11:03:58'),
(14, '06:30:00', '07:00:00', '2017-10-28 05:57:32', '2017-11-15 11:04:13'),
(15, '07:00:00', '07:30:00', '2017-10-28 05:57:32', '2017-11-15 11:04:49'),
(16, '07:30:00', '08:00:00', '2017-10-28 05:57:32', '2017-11-15 11:05:10'),
(17, '08:00:00', '08:30:00', '2017-10-28 05:57:32', '2017-11-15 11:05:37'),
(18, '08:30:00', '09:00:00', '2017-10-28 05:57:32', '2017-11-15 11:06:29'),
(19, '09:00:00', '09:30:00', '2017-10-28 05:57:32', '2017-11-15 11:06:59'),
(20, '09:30:00', '10:00:00', '2017-10-28 05:57:32', '2017-11-15 11:07:18'),
(21, '10:00:00', '10:30:00', '2017-10-28 05:57:32', '2017-11-15 11:07:49'),
(22, '10:30:00', '11:00:00', '2017-10-28 05:57:32', '2017-11-15 11:08:20'),
(23, '11:00:00', '11:30:00', '2017-10-28 05:57:32', '2017-11-15 11:09:35'),
(24, '11:30:00', '12:00:00', '2017-10-28 05:57:32', '2017-11-15 11:10:28'),
(25, '12:00:00', '12:30:00', '2017-10-28 05:57:32', '2017-11-15 11:11:08'),
(26, '12:30:00', '13:00:00', '2017-10-28 05:57:32', '2017-11-15 11:11:41'),
(27, '13:00:00', '13:30:00', '2017-10-28 05:57:32', '2017-11-15 11:14:03'),
(28, '13:30:00', '14:00:00', '2017-10-28 05:57:32', '2017-11-15 11:14:46'),
(29, '14:00:00', '14:30:00', '2017-10-28 05:57:32', '2017-11-15 11:15:16'),
(30, '14:30:00', '15:00:00', '2017-10-28 05:57:32', '2017-11-15 11:15:53'),
(31, '15:00:00', '15:30:00', '2017-10-28 05:57:32', '2017-11-15 11:16:14'),
(32, '15:30:00', '16:00:00', '2017-10-28 05:57:32', '2017-11-15 11:16:46'),
(33, '16:00:00', '16:30:00', '2017-10-28 05:57:32', '2017-11-15 11:17:24'),
(34, '16:30:00', '17:00:00', '2017-10-28 05:57:32', '2017-11-15 11:17:53'),
(35, '17:00:00', '17:30:00', '2017-10-28 05:57:32', '2017-11-15 11:18:31'),
(36, '17:30:00', '18:00:00', '2017-10-28 05:57:32', '2017-11-15 11:18:52'),
(37, '18:00:00', '18:30:00', '2017-10-28 05:57:32', '2017-11-15 11:19:23'),
(38, '18:30:00', '19:00:00', '2017-10-28 05:57:32', '2017-11-15 11:19:56'),
(39, '19:00:00', '19:30:00', '2017-10-28 05:57:32', '2017-11-15 11:20:15'),
(40, '19:30:00', '20:00:00', '2017-10-28 05:57:32', '2017-11-15 11:20:39'),
(41, '20:00:00', '20:30:00', '2017-10-28 05:57:32', '2017-11-15 11:21:11'),
(42, '20:30:00', '21:00:00', '2017-10-28 05:57:32', '2017-11-15 11:21:44'),
(43, '21:00:00', '21:30:00', '2017-10-28 05:57:32', '2017-11-15 11:22:10'),
(44, '21:30:00', '22:00:00', '2017-10-28 05:57:32', '2017-11-15 11:22:32'),
(45, '22:00:00', '22:30:00', '2017-10-28 05:57:32', '2017-11-15 11:21:44'),
(46, '22:30:00', '23:00:00', '2017-10-28 05:57:32', '2017-11-15 11:21:08'),
(47, '23:00:00', '23:30:00', '2017-10-28 05:57:32', '2017-11-15 11:20:25'),
(48, '23:30:00', '23:59:59', '2017-10-28 05:57:32', '2017-11-15 11:18:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `firebase_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `device_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_type` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0(android),1(ios)',
  `user_type` int(11) NOT NULL COMMENT '1 (Doctor) , 2 (Patient)',
  `medical_licence_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuing_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '1(If Admin approved Dr) 1 (If Patient is Active User) 0 (If Dr no approved by admin) 0 (If Patient Is blocked by admin)',
  `profile_status` bigint(20) NOT NULL DEFAULT '0' COMMENT '0( not complete) 1 ( completed)',
  `notification` int(11) NOT NULL DEFAULT '1' COMMENT '1 (on) 0 (off)',
  `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `change_email_otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_email_otp_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '0 (not verified) / null (verified)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firebase_id`, `name`, `email`, `country_code`, `mobile`, `password`, `profile_image`, `speciality_id`, `experience`, `working_place`, `latitude`, `longitude`, `about_me`, `remember_token`, `device_token`, `device_type`, `user_type`, `medical_licence_number`, `issuing_country`, `status`, `profile_status`, `notification`, `language`, `change_email_otp`, `change_email_otp_status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Doctor11', 'doctor11@gmail.com', '+966', '88885568', '$2y$10$G4CO81MU5xnhI1s.eVRNweKR6qo.jEkMy8mz5A7jadjZ4Fa0v3ZtO', NULL, NULL, 0, '', NULL, NULL, '', '87b139ff323565bb38e12445c257dd1b', 'f0V8tgn-Q2c:APA91bG8fd-ce3o__AhqhrgwYTrKUKEaHA9SW2IH7duJMiCqR1244BdYBaZcJpEyQFFyslMZKyWxQeg_SdYKDSsSy2pTUb8S1QmrdXHpu7VOctsSJ_CK19k78WINT8UlWlJWS5khOUFp', '1', 1, NULL, NULL, 0, 0, 1, 'en', NULL, NULL, '2018-01-23 16:53:02', '2018-01-23 16:53:02'),
(2, 'rwUSkiEBrwPzYiptjt7vxeu0cXF2', 'Doctor11', 'doctor11@gmail.comm', '+966', '888855686', '$2y$10$gTFuB1Gt5e3dm0Qxlan7EuvShk0niJFKfnAVH04CPpwDFdjRsKzD2', '_1516706776_1516706776.jpeg', 3, 4, 'Noida', '0.0', '0.0', 'Hjguguvuvuvibibib', '9391d026d02de292ed86ed6effe8e805', 'f0V8tgn-Q2c:APA91bG8fd-ce3o__AhqhrgwYTrKUKEaHA9SW2IH7duJMiCqR1244BdYBaZcJpEyQFFyslMZKyWxQeg_SdYKDSsSy2pTUb8S1QmrdXHpu7VOctsSJ_CK19k78WINT8UlWlJWS5khOUFp', '1', 1, 'FHJKKKK', 'Saudi Arabia', 0, 1, 1, 'en', NULL, NULL, '2018-01-23 16:53:49', '2018-01-23 16:56:16'),
(3, 'rra599CaARUeGZvhhlwx5EnFWww2', 'Test', 'testing012345@gmail.com', '+966', '864665685338', '$2y$10$.7J87sdWY.3CjETxzKQJ7OuJ68ktugDuYHLX89ru9f7ngtn4Mrhk2', NULL, NULL, 0, '', NULL, NULL, '', '4b7fe4764abee01f6c718356fcd8b484', 'f89ZIpp8jqw:APA91bFOJswKeqOcmoSbDGC-ziJyFTu6E2EumRlUJ0RxuiIxg4086X_3NNX373M50m4zF-cl1SB8yaeyc_hizIBjeDoS-A3cVHSxEe3R_q0mpJ1LGo24JuOjHeEQVaNXlh8jrGeEe1L_', '1', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-23 16:56:42', '2018-01-23 16:56:48'),
(4, 'NSTxu4WMiCXbvPQtSYy4SM6lh4t1', 'Akansha', 'akku@gmail.com', '+966', '880586985', '$2y$10$vsHbnDg33Y2U2rzX84znJ.2AVaIVezuLAMI/CAQU2yVFyDMVQhttm', NULL, NULL, 0, '', NULL, NULL, '', '2a402c83bb7fe04e0a7759440593a538', 'dg3rwtS5_S4:APA91bFwB0-stblBNM7RynyJF18dCtFvyTT216jUG_PGnd20icS4Z2lXJjmYsLVbW5RW7HKpcuueBFffeC47bgQCMbKag4obQqpciRt6QLwtTYbB0TXksYH85fre5fGqmdIqzlBo3cIv', '1', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-23 16:57:51', '2018-01-23 16:58:03'),
(5, 'Ze7qw4MlHWYR3ktuIpBZ8vDfiLG3', 'Test Doctor', 'testing9988@gmail.com', '+966', '8946468668', '$2y$10$HTrHUCsCcuYi0q.eCKArQebXY.yQogWLSLeD.cOFi6gKXm2tRIojO', NULL, NULL, 0, '', NULL, NULL, '', '3098e47f4a877ec31acc869b6a2e6572', 'fyJuKWqLL8M:APA91bEHQkL2Mnz7l5MG8bu9tw-UA4M4AnBi7Ainel288YYOGjkI60Vio9IAC3yjuInrVnVMd41wX23FH5r0H8PAZ_8EzJ3orBBJL1Vg_F2ue_fRU9CW1O-vXkIPilYrVxN33LTwm3o3', '1', 1, NULL, NULL, 0, 0, 1, 'en', NULL, NULL, '2018-01-23 17:00:20', '2018-01-23 17:00:25'),
(6, 'CuVlmE4ZxShwYMymmgM1p9MbNHO2', 'akansha', 'akansha@gmail.commm', '+966', '222255536', '$2y$10$VhbSCxvG8KO0IPuNAtsU6.3GzSLyZkMAnBkeC4afiStX.Q3UI1mLK', '_1516708556_profile-pic.png', 8, 8, 'hshsjsksksk', NULL, NULL, NULL, '538c6c826747dfcfdbeee7672cecacc8', 'ftv2GIfm688:APA91bFQm4sYZ3sHjzLFcSzbWUh1qR1XNoS2erZURFCoNK2a3JwszqwaB7E_C1FXrtVfJ3d50g-RjmFiCC8lVOHcMx4hF4J7NxI9Zndq3lSnVltoZ_CrT5cSv2Mw2YQFJLGaol_bJKrw', '0', 1, 'bznsnsnsma', 'Saudi Arabia', 1, 1, 1, 'en', NULL, NULL, '2018-01-23 17:16:09', '2018-01-23 17:25:56'),
(7, 'ywSheTPYJUh1iz63Cls1z1jxtYS2', 'shalini', 'shalini21@gmail.com', '+966', '8802313457', '$2y$10$v8Nd.WHhlMmTK6iCLD6Y6exi9C3oaQ1WpJGOdTVo/WV8C1NXXAWfa', '_1516708236_profile-pic.png', NULL, 0, '', NULL, NULL, '', 'fbe1863b38d72fda19e8f9ce24715b6c', 'cAk-pSsBi_I:APA91bF1UWCI4TvN0RBN-veN55fZpAXmMUr0ewn7gxiiT2YoSnmWKJkcoe_5k9bW-BNxxUs2NvzEAhinNzCS2J0UXrWX9jbDfdqY26drQzxp7u_p6yMYx59xeBuvgth-9mW9wUPNO3Ra', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-23 17:19:16', '2018-01-23 17:20:36'),
(8, 'oa362GG5HDMJN5YvQQxslzIv7GA2', 'Mohammed Riyahi', 'm_riyahi@hotmail.com', '+966', '550022538', '$2y$10$o37X7tpkmPwfU575EcOxC.fXufQbqMXGpZhECYyaToaleZW9ZTIJq', '_1516795650_profile-pic.png', NULL, 0, '', NULL, NULL, '', '48737f942fdd3aeed102df6c1942a406', 'cLzHbS2C0Vw:APA91bHlN5-jk_a2M-fQ1vW9-W3hTEW-ahrG671ON1_TvWmNWPRvz7ojyEAVhT9qso_mIhjGHegT9orcAuoJQ7zWXKpfB0w0F5TnKImzGiaINdgQpTWYOcIVHuj_STkHwLenW1hAQwa6', '1', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-24 10:11:54', '2018-01-25 11:54:12'),
(9, 'yNlnuintH9PbHzSvEeYGTYoInJC2', 'Dr Mohammed Riyahi', 'makinghope88@gmail.com', '+966', '547443305', '$2y$10$F2MeuUaXHDq0q.bH8zZRD.wNHxOS4SAureUw/ISnuJYFYGIa/n5u.', '_1516796821_profile-pic.png', 2, 6, 'KKUH', NULL, NULL, NULL, '590acf362a53b864cdc6849bd0291f85', 'e4Sho-llcLg:APA91bHnHZuROXeXERylAgvHaYaBHHW58qZFfTJvALeey-yvd8JzFUyuCzlWVUhjU_m6RA5r55JZ4PgNL9uCNJnHuLy6T19feZ9TGpLGTFdxzSft-PHrzD7nU_1pT4vxbh8IxWpuish7', '0', 1, '11431', 'Saudi Arabia', 1, 1, 1, 'en', NULL, NULL, '2018-01-24 10:16:48', '2018-01-24 15:29:59'),
(10, NULL, 'Doctor 25', 'doctor25@gmail.com', '+966', '12345786543', '$2y$10$u.UCpvgkvNeZxYEi6cWeNeSB8hX.U1VU1DY7DOLWJ1ux7TX/m2vkO', NULL, NULL, 0, '', NULL, NULL, '', 'd741c574f0bd8fd5e1cc97ed0dd7d374', 'test', '1', 1, NULL, NULL, 0, 0, 1, 'en', NULL, NULL, '2018-01-25 13:47:33', '2018-01-25 13:47:33'),
(11, 'pkLwnfGmzvfPypjtPzyt4tJNSfM2', 'Test Patient', 'patient@fluper.com', '+966', '2345124565', '$2y$10$CJfjppsp2xrMVWUMvHypouBakj9BcKNj6D7zcIVx45YJwlCOPBM86', NULL, NULL, 0, '', NULL, NULL, '', '', 'dBv2F09_AmU:APA91bHA2TayP4MhUK8VOlRoygslQS3NJJ4UgVdpaFU_D0dI0JwCsRxRXkbr0gPZvzH12Lhta4phXhRHsZp0_cK5uE--YxCWngqpjOs3dLM09eM70k3f_afwXK0JaPqSqJgsHtYSZd-U', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-25 14:24:26', '2018-01-25 15:23:14'),
(12, 'ZOsbcv7uXDR4QdjCepqECzNOaYu1', 'Test Patient', 'patient1@fluper.com', '+966', '1245784521', '$2y$10$JgOv49LY7ZwnIbK2BcgrzOUagTReOE/hm5XVdBouQ.kEmpVFrI7R.', '_1516870729_profile-pic.png', NULL, 0, '', NULL, NULL, '', '', 'dBv2F09_AmU:APA91bHA2TayP4MhUK8VOlRoygslQS3NJJ4UgVdpaFU_D0dI0JwCsRxRXkbr0gPZvzH12Lhta4phXhRHsZp0_cK5uE--YxCWngqpjOs3dLM09eM70k3f_afwXK0JaPqSqJgsHtYSZd-U', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-25 14:26:34', '2018-01-25 14:39:00'),
(13, '6pH764TZL1VzuCNNCtsr7TiJX6H3', 'Test Doctor', 'doctor@fluper.com', '+966', '1245786532', '$2y$10$lv8K5bfEsUyunqEd52RaAOA/1eHI1K9w0zAdEd2a2PbqwJRelc7BO', '_1516871150_Doctory-profile-placeholder.png', 9, 9, 'Noida', NULL, NULL, NULL, '', 'eoBfe500Yh8:APA91bFwEqwXyPzMosBQfwSrnIKSsjz0ClNrI7v-uMHSIb4IeLpWNEAXmkXdPuMwNsfllWGar_nbhLZIiE-muZhWkHsnt4DPJuyBKO-uBODc1en3bRfETWT0hRbMuS6Vsb731ojdsyEh', '0', 1, '11111111111', 'Saudi Arabia', 0, 1, 1, 'en', NULL, NULL, '2018-01-25 14:35:18', '2018-01-25 15:32:33'),
(14, 'Wu1lEgBjUIgOlNSp342PQEfWAcP2', 'Doctor 25Jan', 'doctor26@gmail.com', '+966', '7864344432', '$2y$10$on.WDO0UilltFWa63hjgZeVfmh77anFAzdqHZwGxlZL.J5ap8NRaO', '_1516873137_1516873135.jpeg', 1, 1, 'India', '0.0', '0.0', NULL, '', 'cuvE6JMI5Yg:APA91bEpstHwUxOvdQcwbnr4E_vdcrDXi7PHSfxYrys8Vx9-7ZtUF-VV5s5jpa_40mEF47OvHsgxCDeBwD7dy4CP2uSouEUw6MUdrKQSQsz9n_gZLKxXFqcviHS0KyGlVcrEHUn-WPCE', '1', 1, '455VDFG', 'Saudi Arabia', 1, 1, 1, 'en', NULL, NULL, '2018-01-25 15:07:33', '2018-01-25 15:24:22'),
(15, 'pmFK12MOeweaaXn7CdfHEnNd6CJ2', 'Patient 26', 'patient26@gmail.com', '+966', '78654534545', '$2y$10$aOkdr6erfkSvzwy6bP73mewa2vi0eNPZHJS5OzNu.qS1rxPllQVzS', NULL, NULL, 0, '', NULL, NULL, '', 'c07e3db121deec58170f65ea5cfcd6b5', 'eVaUWClX-qI:APA91bGumULaq4fIFQJBYvUKfMsxD25CXqWFvrAjnbEUqUy5Q5IAusR-DtHr1SgV9Oa4_s0IjkLMG4LaD54b1G-txJjuCTFvN8H41uEq-eyI0tQcK6asKHMpKRxJfRG4WZq7-uIDIale', '1', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-25 15:16:00', '2018-01-25 15:16:04'),
(16, 'lS3wc5zx3TTS8GwRqqQGT8RFsFx2', 'Dr Khan', 'drkhan@gmail.com', '+966', '7854455555', '$2y$10$OTSSniPywgn0cTGGGy2WyenVbd3ccSWf7TIHYuM73MD1yoa8KiJRC', '_1516874219_1516874218.jpeg', 2, 1, 'India', '0.0', '0.0', NULL, '6b0b82c656812f462cdc9a48eb72fed3', 'cuvE6JMI5Yg:APA91bEpstHwUxOvdQcwbnr4E_vdcrDXi7PHSfxYrys8Vx9-7ZtUF-VV5s5jpa_40mEF47OvHsgxCDeBwD7dy4CP2uSouEUw6MUdrKQSQsz9n_gZLKxXFqcviHS0KyGlVcrEHUn-WPCE', '1', 1, 'DSFSFDF', 'Saudi Arabia', 1, 1, 1, 'en', NULL, NULL, '2018-01-25 15:25:36', '2018-01-25 15:28:05');

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
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
  ADD KEY `password_resets_email_index` (`email`(191));

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
-- Indexes for table `time_slots_older`
--
ALTER TABLE `time_slots_older`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `days`
--
ALTER TABLE `days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `doctor_availabilities`
--
ALTER TABLE `doctor_availabilities`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;
--
-- AUTO_INCREMENT for table `doctor_motherlanguages`
--
ALTER TABLE `doctor_motherlanguages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `doctor_qualifications`
--
ALTER TABLE `doctor_qualifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `mother_languages`
--
ALTER TABLE `mother_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `patient_bookmarks`
--
ALTER TABLE `patient_bookmarks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
--
-- AUTO_INCREMENT for table `time_slots_older`
--
ALTER TABLE `time_slots_older`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
