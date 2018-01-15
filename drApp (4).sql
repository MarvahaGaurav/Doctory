-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 15, 2018 at 01:11 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.26-2+ubuntu16.04.1+deb.sury.org+2

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
(1, 'admin@gmail.com', '$2y$10$AHBebkwd/u3n8D8YISbxqeFykgtqSnz/OupJ0p53xTJyaGbJ1VVVq', '8410107875', '1513248017_myDp.jpg', 'gaurav marvaha', 'gaurav@linkedin.com', 'gaurav.twitter@twiter.com', 1, 'Delhi (Rohini)', '2017-11-07 10:58:08', '2017-12-14 16:10:17');

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
(1, 3, NULL, NULL, NULL, NULL, 4, 73, 2, '2018-01-15', 'Expired', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-15 11:23:52', '2018-01-15 18:00:38'),
(2, 3, NULL, NULL, NULL, NULL, 4, 82, 2, '2018-01-15', 'Pending', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-15 12:27:10', '2018-01-15 12:27:10'),
(3, 8, NULL, NULL, NULL, NULL, 6, 83, 2, '2018-01-15', 'Accepted', NULL, NULL, NULL, NULL, NULL, NULL, '2018-01-15 13:11:04', '2018-01-15 18:41:36');

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
(1, 2, 49, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(2, 2, 50, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(3, 2, 51, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(4, 2, 52, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(5, 2, 53, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(6, 2, 54, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(7, 2, 55, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(8, 2, 56, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(9, 2, 57, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(10, 2, 58, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(11, 2, 59, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(12, 2, 60, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(13, 2, 61, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(14, 2, 62, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(15, 2, 63, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(16, 2, 64, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(17, 2, 65, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(18, 2, 66, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(19, 2, 67, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(20, 2, 68, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(21, 2, 69, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(22, 2, 70, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(23, 2, 71, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(24, 2, 72, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(25, 2, 73, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(26, 2, 74, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(27, 2, 75, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(28, 2, 76, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(29, 2, 77, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(30, 2, 78, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(31, 2, 79, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(32, 2, 80, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(33, 2, 81, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(34, 2, 82, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(35, 2, 83, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(36, 2, 84, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(37, 2, 85, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(38, 2, 86, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(39, 2, 87, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(40, 2, 88, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(41, 2, 89, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(42, 2, 90, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(43, 2, 91, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(44, 2, 92, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(45, 2, 93, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(46, 2, 94, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(47, 2, 95, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(48, 2, 96, 4, '2018-01-15 11:22:39', '2018-01-15 11:22:39'),
(49, 3, 69, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(50, 3, 70, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(51, 3, 71, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(52, 3, 73, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(53, 3, 74, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(54, 3, 75, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(55, 3, 76, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(56, 3, 77, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(57, 3, 78, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(58, 3, 79, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(59, 3, 80, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(60, 3, 81, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(61, 3, 82, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(62, 3, 83, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(63, 3, 84, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(64, 3, 85, 4, '2018-01-15 11:23:09', '2018-01-15 11:23:09'),
(65, 2, 10, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(66, 2, 14, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(67, 2, 18, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(68, 2, 22, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(69, 2, 50, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(70, 2, 54, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(71, 2, 58, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(72, 2, 62, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(73, 2, 66, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(74, 2, 67, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(75, 2, 70, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12'),
(76, 2, 83, 6, '2018-01-15 13:08:12', '2018-01-15 13:08:12');

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
(1, 1, 1, '2018-01-15 15:07:32', '2018-01-15 15:07:32'),
(2, 2, 1, '2018-01-15 15:11:31', '2018-01-15 15:11:31'),
(3, 4, 3, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(4, 6, 2, '2018-01-15 17:23:01', '2018-01-15 17:23:01');

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
(1, 1, 4, '2018-01-15 15:07:32', '2018-01-15 15:07:32'),
(2, 1, 5, '2018-01-15 15:07:32', '2018-01-15 15:07:32'),
(3, 1, 6, '2018-01-15 15:07:32', '2018-01-15 15:07:32'),
(4, 1, 3, '2018-01-15 15:07:32', '2018-01-15 15:07:32'),
(5, 2, 3, '2018-01-15 15:11:31', '2018-01-15 15:11:31'),
(6, 4, 4, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(7, 4, 1, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(8, 4, 5, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(9, 4, 6, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(10, 4, 3, '2018-01-15 16:47:34', '2018-01-15 16:47:34'),
(11, 6, 3, '2018-01-15 17:23:01', '2018-01-15 17:23:01'),
(12, 6, 2, '2018-01-15 17:23:01', '2018-01-15 17:23:01');

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
  `appointment_id` int(11) NOT NULL,
  `appointment_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `doctor_id`, `reffered_to_doctor_id`, `patient_id`, `type`, `appointment_id`, `appointment_status`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, 3, 2, 1, NULL, '2018-01-15 11:23:53', '2018-01-15 11:23:53'),
(2, 4, NULL, 3, 2, 2, NULL, '2018-01-15 12:27:10', '2018-01-15 12:27:10'),
(3, 6, NULL, 8, 2, 3, NULL, '2018-01-15 13:11:05', '2018-01-15 13:11:05'),
(4, 6, NULL, 8, 9, 3, NULL, '2018-01-15 13:11:36', '2018-01-15 13:11:36');

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
(1, 1, '', 1, '2018-01-15 15:02:42', '2018-01-15 15:06:23'),
(3, 3, '', 1, '2018-01-15 15:13:44', '2018-01-15 15:14:10'),
(4, 4, '', 1, '2018-01-15 16:33:42', '2018-01-15 16:44:30'),
(5, 5, '4638', 0, '2018-01-15 17:21:55', '2018-01-15 17:21:55'),
(6, 6, '', 1, '2018-01-15 17:22:15', '2018-01-15 17:22:25'),
(7, 7, '', 1, '2018-01-15 17:33:23', '2018-01-15 17:33:32'),
(8, 8, '', 1, '2018-01-15 18:26:40', '2018-01-15 18:26:56');

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
(1, NULL, 'Dr Gaurav Marvaha', 'gauravmrvh1@gmail.com', '+91', '8410107875', '$2y$10$uzypHFFaTzQeGpbxllVTwuI.3f42yyi5B4u/j.8wvfi4WAmUXaqZK', '_1516009052_BeautyPlus_20171229140634_save.jpg', 1, 12, 'Noida', NULL, NULL, 'Test', '6db4a4acc0b86a7bdfc74535a00c87f0', 'fJlZZ-pNiVI:APA91bGc4ZcT_I7dAG1W22p41WImetKA3F-ePJuO9aToKbDvvvFrYfDOQmPqwqOJ9aVWq73WFDTr9irSWO-DZE5pTCIr3VIG6utL33oMj4z-e2Sc0J7Cyyvfus0_oIpjyGScaloanIP5', '1', 1, 'gsvisjs', 'India', 1, 1, 1, 'en', NULL, NULL, '2018-01-15 15:02:42', '2018-01-15 16:21:17'),
(3, 'VATUpSxMaBZSc3223DkgLuYAXZI2', 'gaurav Patient', 'Gauravmrvh2@gmail.com', '+91', '8881438096', '$2y$10$7dRAMA4XCYUqrEOcALgnuOOaXnfJKdLafvaecCDj3gOWhQL.HrgXy', NULL, NULL, 0, '', NULL, NULL, '', 'dcc9e47e905647aae2e59d3fe0a1463b', 'fJ5cK8y9nrc:APA91bGKPkwY5QmgAiuw4tWCOcW_PxPV6NCS0uOFqRSHRFOXEqH96CYtYgHquguAdUoScnFZpB7SxV7WP6-ocEXWZiviEW_ya1j8Me550wxXlVI7QLoHTuIXavyaJKSze-X--i9yaKsF', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-15 15:13:44', '2018-01-15 16:13:19'),
(4, NULL, 'G Doctor 2', 'Gauravmrvh11@gmail.com', '+91', '9897726108', '$2y$10$DapwOyQjSna4C70cn48aoO/l/PaQZR8KyPrO.yzmE2aYknwUl.oii', '_1516015054_BeautyPlus_20171228194926_save.jpg', 1, 11, 'Delhi', NULL, NULL, 'Test about me', '', 'fJlZZ-pNiVI:APA91bGc4ZcT_I7dAG1W22p41WImetKA3F-ePJuO9aToKbDvvvFrYfDOQmPqwqOJ9aVWq73WFDTr9irSWO-DZE5pTCIr3VIG6utL33oMj4z-e2Sc0J7Cyyvfus0_oIpjyGScaloanIP5', '0', 1, 'vsuwjbshs', 'India', 1, 1, 1, 'en', NULL, NULL, '2018-01-15 16:33:42', '2018-01-15 16:59:35'),
(5, NULL, 'test', 'test@gmail.com', '+966', '87979799', '$2y$10$nm/NwgQoMRLr1q4uwpVGgOOA/PCPQUZvV.JaNNfh9DyIwpiZcA18a', NULL, NULL, 0, '', NULL, NULL, '', 'b10daf7e6901ece2cfcf074b6acdea18', 'fwc9wOLPKyw:APA91bHfk5yQxUF0x86ahqXIh0vjYaidNVcfNQltWtzAI1zDj5thV4axNbOvasnTPyNjdc2vAFGIolhsDK5TWHZDIeKhxOhfhE20vR4ycK4aSaI2laI3aWLqltBZlr7hBOpYfLst_AIx', '0', 1, NULL, NULL, 0, 0, 1, 'en', NULL, NULL, '2018-01-15 17:21:55', '2018-01-15 17:21:55'),
(6, 'RORqjQQTuUR3GKlQLKcB0JRrdjy2', 'test', 'test@gmail.comm', '+966', '879797991', '$2y$10$/yTjohIpDuBlfXYVhn4Mz.6VDssEFky/nZa8e.piEhMEEP2araoRa', '_1516017181_Doctory-profile-placeholder.png', 10, 10, 'hsjskkskskd', NULL, NULL, 'hsbshnsnzjzjzkz', '5279f7a9f57c0d1dceae06f556fbc430', 'fwc9wOLPKyw:APA91bHfk5yQxUF0x86ahqXIh0vjYaidNVcfNQltWtzAI1zDj5thV4axNbOvasnTPyNjdc2vAFGIolhsDK5TWHZDIeKhxOhfhE20vR4ycK4aSaI2laI3aWLqltBZlr7hBOpYfLst_AIx', '0', 1, 'usjjskskskss', 'Saudi Arabia', 1, 1, 1, 'en', NULL, NULL, '2018-01-15 17:22:15', '2018-01-15 18:36:42'),
(7, 'EGBtz9Bznef3x7kTT1jZ01DvOti2', 'VIVE', 'VIVE@gmail.com', '+966', '878787879', '$2y$10$AcUBMuZcwQIjKgoh0ySgk.vfuBjT6iwnV43mJ1/4XsFW7trf592Vq', NULL, NULL, 0, '', NULL, NULL, '', '', 'eBE0tJRYv8U:APA91bEXLbclmjfZ08qTkqHw18werDLLcf-v-xAVlvlQh4Z0wF5aC5GHwjIrKxcHrPLkemI1XY7ZVv3xHccjUzWT8xaR9-dyGcMkhdW9A9AUoHCCaM3iuu7zQ6ptpgYFki2Jj8LJrbi3', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-15 17:33:23', '2018-01-15 17:34:09'),
(8, '4Q8GNbHXIqPgXbbLfnK2UFldxIA3', 'qwerty', 'qwerty@gmail.com', '+966', '578787878', '$2y$10$oHuzrHi5adOpO.9SzrL1peNXWCbHTC/Fr/S0uO5dHg7NZgxjqlCOu', NULL, NULL, 0, '', NULL, NULL, '', '61b67174c827f8fbca9437d520faf558', 'eBE0tJRYv8U:APA91bEXLbclmjfZ08qTkqHw18werDLLcf-v-xAVlvlQh4Z0wF5aC5GHwjIrKxcHrPLkemI1XY7ZVv3xHccjUzWT8xaR9-dyGcMkhdW9A9AUoHCCaM3iuu7zQ6ptpgYFki2Jj8LJrbi3', '0', 2, NULL, NULL, 1, 1, 1, 'en', NULL, NULL, '2018-01-15 18:26:40', '2018-01-15 18:26:48');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;
--
-- AUTO_INCREMENT for table `doctor_motherlanguages`
--
ALTER TABLE `doctor_motherlanguages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `doctor_qualifications`
--
ALTER TABLE `doctor_qualifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `mother_languages`
--
ALTER TABLE `mother_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `patient_bookmarks`
--
ALTER TABLE `patient_bookmarks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `qualifications`
--
ALTER TABLE `qualifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
