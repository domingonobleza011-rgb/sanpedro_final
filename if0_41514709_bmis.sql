-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql106.infinityfree.com
-- Generation Time: Jul 28, 2026 at 09:44 PM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41514709_bmis`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `id_admin_msg` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `date_sent` datetime DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread',
  `reply_text` text DEFAULT NULL,
  `reply_date` datetime DEFAULT NULL,
  `replied_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_messages`
--

INSERT INTO `admin_messages` (`id_admin_msg`, `id_resident`, `message_text`, `date_sent`, `status`, `reply_text`, `reply_date`, `replied_by`) VALUES
(4, 44, 'dbvrbr', '2026-04-13 03:38:14', 'read', NULL, NULL, NULL),
(5, 44, 'hahahahha', '2026-04-13 03:39:00', 'read', NULL, NULL, NULL),
(7, 44, 'bsjskakaksjsnsbs', '2026-04-16 04:05:40', 'read', NULL, NULL, NULL),
(8, 54, 'thank youu sir', '2026-04-18 01:52:11', 'read', NULL, NULL, NULL),
(20, 81, 'VALID ID SUBMITTED - Please verify my account. Note: rtyunhjikl;lkjtr', '2026-05-20 05:42:48', 'read', NULL, NULL, NULL),
(58, 128, 'VALID ID SUBMITTED - Please verify my account. Note: none', '2026-07-03 19:12:56', 'read', NULL, NULL, NULL),
(61, 130, 'VALID ID SUBMITTED - Please verify my account. Note: none', '2026-07-08 22:58:19', 'read', NULL, NULL, NULL),
(62, 130, 'VALID ID SUBMITTED - Please verify my account. Note: none', '2026-07-08 23:02:11', 'read', NULL, NULL, NULL),
(66, 136, 'hello po', '2026-07-26 04:56:54', 'read', NULL, NULL, NULL),
(67, 136, 'punta na po ako', '2026-07-26 04:57:08', '', 'sige po sir', '2026-07-27 03:42:47', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('system','sms','email','announcement') DEFAULT 'system',
  `recipient_type` varchar(50) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resident_messages`
--

CREATE TABLE `resident_messages` (
  `id_message` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `message_text` text NOT NULL,
  `date_sent` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resident_messages`
--

INSERT INTO `resident_messages` (`id_message`, `id_resident`, `message_text`, `date_sent`, `status`) VALUES
(3, 47, 'you\'re id is ready come pick it up ', '2026-04-11 19:20:37', 'sent'),
(8, 44, 'sbvdndnt', '2026-04-12 19:38:05', 'sent'),
(9, 44, 'evrrtg5hy', '2026-04-12 19:41:17', 'sent'),
(13, 54, 'hi beybi ko\r\n', '2026-04-16 03:38:39', 'sent'),
(14, 62, 'thankyou sa pag register sir', '2026-04-20 22:47:30', 'sent'),
(15, 63, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-04-28 05:21:58', 'sent'),
(20, 70, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-03 22:53:14', 'sent'),
(21, 78, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-14 19:16:41', 'sent'),
(22, 72, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-14 19:16:45', 'sent'),
(23, 80, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-19 07:55:06', 'sent'),
(24, 80, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-19 07:56:11', 'sent'),
(25, 80, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-19 07:57:41', 'sent'),
(26, 81, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-20 05:43:51', 'sent'),
(27, 81, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-20 07:05:21', 'sent'),
(28, 83, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-21 01:46:33', 'sent'),
(29, 87, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-22 03:07:07', 'sent'),
(30, 88, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-24 04:01:16', 'sent'),
(31, 89, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-24 04:07:31', 'sent'),
(32, 90, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-25 02:35:27', 'sent'),
(33, 91, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-26 05:02:13', 'sent'),
(35, 94, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-26 21:28:51', 'sent'),
(36, 93, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-26 21:28:54', 'sent'),
(37, 95, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-27 01:49:02', 'sent'),
(38, 96, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-27 05:41:50', 'sent'),
(39, 99, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-28 22:45:55', 'sent'),
(40, 99, 'helow', '2026-05-28 23:04:14', 'sent'),
(41, 102, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 05:06:49', 'sent'),
(42, 101, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 05:06:53', 'sent'),
(43, 101, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 05:06:58', 'sent'),
(44, 101, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 05:07:16', 'sent'),
(45, 104, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 21:37:53', 'sent'),
(46, 105, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-05-31 21:41:38', 'sent'),
(47, 108, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-03 22:26:19', 'sent'),
(48, 107, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-03 22:26:22', 'sent'),
(50, 110, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-06 07:26:21', 'sent'),
(52, 113, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-06 19:08:31', 'sent'),
(55, 114, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-06 19:26:17', 'sent'),
(57, 116, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-09 03:36:43', 'sent'),
(58, 119, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-11 17:35:47', 'sent'),
(59, 123, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-06-15 03:22:27', 'sent'),
(60, 127, 'âŒ Your valid ID submission was rejected. Reason: your id is invalid Please upload a clearer or valid government-issued ID.', '2026-07-03 05:57:08', 'sent'),
(61, 120, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-07-08 22:40:49', 'sent'),
(62, 130, 'upload ka ulit', '2026-07-08 23:01:19', 'sent'),
(63, 130, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-07-08 23:03:31', 'sent'),
(64, 130, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-07-08 23:03:35', 'sent'),
(65, 132, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-09 06:53:01', 'sent'),
(66, 133, 'âœ… Your account has been verified! You can now request barangay certificates and other services.', '2026-07-10 07:59:29', 'sent'),
(67, 120, 'hello\r\n', '2026-07-13 21:20:09', 'sent'),
(68, 134, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-19 05:31:41', 'sent'),
(69, 134, 'udma mag pa barangay ka', '2026-07-19 05:37:02', 'sent'),
(70, 135, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-19 05:43:20', 'sent'),
(71, 136, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-26 04:08:41', 'sent'),
(72, 137, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-26 04:10:01', 'sent'),
(73, 138, 'âœ… Your registration has been approved! You can now log in to your account.', '2026-07-26 04:22:32', 'sent'),
(74, 136, 'approved na po', '2026-07-26 04:53:01', 'sent'),
(75, 136, 'hello', '2026-07-26 04:55:03', 'sent'),
(76, 137, 'what the fuck bro', '2026-07-26 19:03:16', 'sent'),
(77, 136, 'sige po sir', '2026-07-27 03:42:47', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_activity_log`
--

CREATE TABLE `tbl_activity_log` (
  `id_log` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `admin_name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL,
  `module` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_activity_log`
--

INSERT INTO `tbl_activity_log` (`id_log`, `id_admin`, `admin_name`, `role`, `action`, `module`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(193, 5, 'Is This', 'administrator', 'GENERATE_DOCUMENT', 'Certificate of Indigency', 'Generated Certificate of Indigency for LOVITE, JAY MARK JUDAVAR (Resident ID: 120)', '61.9.10.255', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-17 19:48:44'),
(194, 5, 'Is This', 'administrator', 'DELETE_Resident', 'Resident', 'Deleted Resident Record #134', '61.9.10.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-19 05:53:21'),
(195, 5, 'Is This', 'administrator', 'PROMOTE_Resident', 'Resident', 'Promoted Resident #120 (Lovite, Jay Mark) to Staff â€” Position: Punong Barangay', '61.9.10.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-26 03:14:17'),
(196, NULL, 'Jay Mark Lovite', 'user', 'DELETE_Staff', 'Staff', 'Deleted Staff Record #67', '49.147.38.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 03:49:07'),
(197, NULL, 'Jay Mark Lovite', 'user', 'DELETE_Resident', 'Resident', 'Deleted Resident Record #120', '49.147.38.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 03:59:37'),
(198, 5, 'Is This', 'administrator', 'DELETE_Resident', 'Resident', 'Deleted Resident Record #138', '61.9.8.126', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 04:24:58'),
(199, 5, 'Is This', 'administrator', 'GENERATE_DOCUMENT', 'Business Permit', 'Generated Business Permit for BAN, MIKE BOL â€“ Business: mike business (Resident ID: 136)', '61.9.8.64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-07-26 04:53:08'),
(200, 5, 'Is This', 'administrator', 'GENERATE_DOCUMENT', 'Business Permit', 'Generated Business Permit for BAN, MIKE BOL â€“ Business: mike business (Resident ID: 136)', '61.9.10.38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:02:10'),
(201, 5, 'Is This', 'administrator', 'GENERATE_DOCUMENT', 'Barangay ID', 'Generated Barangay ID for NOBLEZA, DOMINGO BENDAL (ID Record: 15)', '61.9.10.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:02:59'),
(202, 5, 'Is This', 'administrator', 'DELETE_DOCUMENT', 'Document', 'Deleted Barangay ID #15', '61.9.10.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:03:05'),
(203, 5, 'Is This', 'administrator', 'DELETE_DOCUMENT', 'Document', 'Deleted Barangay ID #15', '61.9.10.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:03:08'),
(204, 5, 'Is This', 'administrator', 'GENERATE_DOCUMENT', 'Business Permit', 'Generated Business Permit for LOVITE, JAY MARK JUDAVAR â€“ Business: IT  (Resident ID: 120)', '61.9.10.38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:03:52'),
(205, 5, 'Is This', 'administrator', 'DELETE_Announcement', 'Announcement', 'Deleted Announcement Record #71', '61.9.10.38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `role`) VALUES
(5, 'sanpedroiriga@gmail.com', NULL, '$2y$10$F3EsNQGYS/M4XHv249VtE.KqtroRdaqYrY2TUtvx9g7vUOuXPaIkS', 'This', 'Is', 'Admin', 'administrator'),
(10, 'dominge@gmail.com', NULL, '$2y$10$0kaMFdn8NAzSkm3vxCQdDOQq1a4rMXo.jaVorWc96xgScaEfiNJCa', 'nobleza', 'domingo', 'b', 'administrator'),
(11, 'domingo@gmail.com', NULL, '$2y$10$hSDzudbjpBAJH23ossDLHukIZbFmaaU8TK8H9HF4oOldetaZUBzze', 'x 11\")', 'US-Letter', '(', 'administrator'),
(12, 'sanet@gmail.com', NULL, '$2y$10$tRxFZpd.2O95VBnb1xWWlOjy1CeNUuuFveHvFh6Z98khZFy0tFXX2', 'Tagoro', 'Eugene', '', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement`
--

CREATE TABLE `tbl_announcement` (
  `id_announcement` int(11) NOT NULL,
  `event` varchar(1000) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `addedby` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_announcement`
--

INSERT INTO `tbl_announcement` (`id_announcement`, `event`, `target`, `start_date`, `addedby`, `status`, `image`) VALUES
(70, 'ðŸ“¢ Your Voice Matters â€” ISO/IEC Survey\r\nBarangay San Pedro Iriga is currently working towards ISO/IEC certification to strengthen and improve the services we provide to every resident. To get there, we need YOUR help!\r\nWe\'re inviting all residents to take just a few minutes to answer a short survey. Your honest feedback will help us identify what\'s working, what needs improvement, and how we can serve you better moving forward.\r\nðŸ‘‰ Take the survey here: https://sites.google.com/view/bmis-survey/home\r\nIt only takes a few minutes, but it makes a big difference for our barangay. Thank you for being part of this journey towards better service! ðŸ™\r\nâ€” Admin', NULL, '2026-07-14', 'This, Is Admin', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement_comments`
--

CREATE TABLE `tbl_announcement_comments` (
  `id_comment` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement_reactions`
--

CREATE TABLE `tbl_announcement_reactions` (
  `id_reaction` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_announcement_reactions`
--

INSERT INTO `tbl_announcement_reactions` (`id_reaction`, `announcement_id`, `user_id`, `reaction_type`, `created_at`) VALUES
(66, 70, 136, 'like', '2026-07-26 04:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_archive`
--

CREATE TABLE `tbl_archive` (
  `id_archive` int(10) UNSIGNED NOT NULL,
  `record_type` varchar(50) NOT NULL COMMENT 'resident | certofres | certofindigency | clearance | bspermit | blotter | youth | brgyid | staff',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT 'Original primary key value from source table',
  `full_name` varchar(255) NOT NULL COMMENT 'Formatted name for quick display',
  `summary` varchar(512) DEFAULT NULL COMMENT 'Short detail string (address, purpose, etc.)',
  `record_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Full row stored as JSON for recovery'
) ;

--
-- Dumping data for table `tbl_archive`
--

INSERT INTO `tbl_archive` (`id_archive`, `record_type`, `record_id`, `full_name`, `summary`, `record_data`, `deleted_by`, `deleted_at`, `restored_at`, `restored_by`, `is_restored`) VALUES
(107, 'staff', 67, 'Briones, Clint Joseph  Beldad.', 'Role: user | Email: clintjoseph90@gmail.com', '{\"id_user\":67,\"login_identity\":\"clintjoseph90@gmail.com\",\"email\":\"clintjoseph90@gmail.com\",\"phone_number\":null,\"password\":\"$2y$10$c2HewYSbxpmUH6.xMr4eVu6yiubIL8qGtEh95IYry8Q6PnJKgj6my\",\"lname\":\"Briones\",\"fname\":\"Clint Joseph \",\"mi\":\"Beldad\",\"age\":26,\"sex\":\"Male\",\"address\":\"25 E badiola st , San Jose, Baao\",\"contact\":\"09108300407\",\"position\":\"Punong Barangay\",\"role\":\"user\",\"addedby\":\"This, Is\",\"photo\":null,\"res_is_verified\":null,\"res_verified_at\":null,\"res_verified_by\":null}', NULL, '2026-07-26 03:49:07', NULL, NULL, 0),
(108, 'resident', 120, 'Lovite, Jay Mark Judavar .', 'N/A Santan Street Salvacion  Baao | Male | Age:', '{\"id_resident\":120,\"login_identity\":null,\"res_photo\":null,\"email\":\"jaymarklovite20@gmail.com\",\"phone_number\":null,\"password\":\"$2y$10$uSgMHJwhue798DiFXwiBouInBfcv4..Ny7gCeioGNWCgu2K13Anjm\",\"security_question\":\"What was your childhood nickname?\",\"security_answer\":\"pogi\",\"lname\":\"Lovite\",\"fname\":\"Jay Mark\",\"mi\":\"Judavar \",\"age\":null,\"sex\":\"Male\",\"status\":\"Single\",\"houseno\":\"N\\/A\",\"street\":\"Santan Street\",\"brgy\":\"Salvacion \",\"municipal\":\"Baao\",\"address\":null,\"contact\":\"09563719899\",\"bdate\":\"2004-09-11\",\"bplace\":\"Salvacion \",\"nationality\":\"Filipino \",\"family_role\":\"No\",\"voter\":\"Yes\",\"role\":\"resident\",\"is_verified\":1,\"verified_at\":\"2026-07-08 22:40:49\",\"verified_by\":\"Admin\",\"addedby\":\"Resident\",\"pwd\":\"No\",\"is_archived\":0,\"must_change_password\":1}', NULL, '2026-07-26 03:59:37', NULL, NULL, 0),
(109, 'resident', 138, 'sadsa, dsadsa dsad.', '1 zone 1 Buyon Bacarra | Male | Age:', '{\"id_resident\":138,\"login_identity\":null,\"res_photo\":null,\"email\":\"sample1@gmail.com\",\"phone_number\":null,\"password\":\"$2y$10$Tk6g4zSIyc4icsbr1P13uewjLsK32FgkZWKPNRIPIzvrRkerh7p.m\",\"security_question\":null,\"security_answer\":null,\"lname\":\"sadsa\",\"fname\":\"dsadsa\",\"mi\":\"dsad\",\"age\":null,\"sex\":\"Male\",\"status\":\"Single\",\"houseno\":\"1\",\"street\":\"zone 1\",\"brgy\":\"Buyon\",\"municipal\":\"Bacarra\",\"address\":null,\"contact\":\"09876543210\",\"bdate\":\"2026-12-25\",\"bplace\":\"baaooo\",\"nationality\":\"fil\",\"family_role\":\"Yes\",\"voter\":\"Yes\",\"role\":\"resident\",\"is_verified\":1,\"verified_at\":\"2026-07-26 04:22:32\",\"verified_by\":\"Admin\",\"addedby\":\"Resident\",\"pwd\":\"Yes\",\"is_archived\":0,\"must_change_password\":0}', NULL, '2026-07-26 04:24:58', NULL, NULL, 0),
(110, 'brgyid', 15, 'Nobleza, domingo Bendal.', 'Blk. 14 Lot 25 El Chapo, San Pedro | Bdate: 2005-03-20', '{\"id_brgyid\":15,\"id_resident\":119,\"lname\":\"Nobleza\",\"fname\":\"domingo\",\"mi\":\"Bendal\",\"houseno\":\"Blk. 14 Lot 25\",\"street\":\"El Chapo\",\"brgy\":\"San Pedro\",\"municipal\":\"ocampo\",\"bplace\":\"Antipolo, Rizal\",\"bdate\":\"2005-03-20\",\"contact\":\"09070560963\",\"inc_lname\":\"nobleza\",\"inc_fname\":\"domingo\",\"inc_mi\":\"(\",\"inc_contact\":\"09070569634\",\"relation\":\"wth\",\"inc_houseno\":\"Blk. 14 Lot 25\",\"inc_street\":\"hibago\",\"inc_brgy\":\"hibago\",\"inc_municipal\":\"ocampo\"}', NULL, '2026-07-27 03:03:05', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blotter`
--

CREATE TABLE `tbl_blotter` (
  `id_blotter` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `narrative` mediumtext NOT NULL,
  `timeapplied` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_blotter`
--

INSERT INTO `tbl_blotter` (`id_blotter`, `id_resident`, `lname`, `fname`, `mi`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `narrative`, `timeapplied`) VALUES
(19, 119, 'Nobleza', 'domingo', 'Bendal', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'ocampo', '09070569634', 'sinapok ako ni carl', '2026-06-17 18:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_brgyid`
--

CREATE TABLE `tbl_brgyid` (
  `id_brgyid` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `bdate` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `inc_lname` varchar(255) NOT NULL,
  `inc_fname` varchar(255) NOT NULL,
  `inc_mi` varchar(255) NOT NULL,
  `inc_contact` varchar(255) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `inc_houseno` varchar(255) NOT NULL,
  `inc_street` varchar(255) NOT NULL,
  `inc_brgy` varchar(255) NOT NULL,
  `inc_municipal` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_brgyid`
--

INSERT INTO `tbl_brgyid` (`id_brgyid`, `id_resident`, `lname`, `fname`, `mi`, `houseno`, `street`, `brgy`, `municipal`, `bplace`, `bdate`, `contact`, `inc_lname`, `inc_fname`, `inc_mi`, `inc_contact`, `relation`, `inc_houseno`, `inc_street`, `inc_brgy`, `inc_municipal`) VALUES
(16, 120, 'Lovite', 'Jay Mark', 'Judavar ', 'N/A', 'Santan Street', 'Salvacion ', 'Baao', 'Salvacion baao camarines sur ', '2004-09-11', '09563719899', 'Judavar', 'Daisy', 'Bayrante', '09562749124', 'mother', 'N/A', 'Zone 6 santan Street ', 'Salvacion ', 'Baao');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bspermit`
--

CREATE TABLE `tbl_bspermit` (
  `id_bspermit` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `mi` varchar(255) DEFAULT NULL,
  `bsname` varchar(255) DEFAULT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(252) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `bsindustry` varchar(255) DEFAULT NULL,
  `aoe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_bspermit`
--

INSERT INTO `tbl_bspermit` (`id_bspermit`, `id_resident`, `lname`, `fname`, `mi`, `bsname`, `houseno`, `street`, `brgy`, `municipal`, `bsindustry`, `aoe`) VALUES
(15, 120, 'Lovite', 'Jay Mark', 'Judavar ', 'IT ', 'N/A', 'Santan Street', 'Salvacion ', 'Baao', 'Computer', 111),
(16, 130, 'dela cruz', 'juan', 'castro', 'bagasan', 'Blk. 14 Lot 25', 'El Chapo', 'Antipolo', 'City of Iriga', 'Food', 1207),
(17, 136, 'ban', 'mike', 'bol', 'mike business', '1', 'zone 1', 'Uguis', 'Nueva Era', 'Computer', 25);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_clearance`
--

CREATE TABLE `tbl_clearance` (
  `id_clearance` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_clearance`
--

INSERT INTO `tbl_clearance` (`id_clearance`, `id_resident`, `lname`, `fname`, `mi`, `purpose`, `houseno`, `street`, `brgy`, `municipal`, `status`, `age`) VALUES
(6, 120, 'Lovite', 'Jay Mark', 'Judavar ', 'Job Requirement', 'N/A', 'Santan Street', 'Salvacion ', 'Baao', 'Single', '21');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_complaints`
--

CREATE TABLE `tbl_complaints` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','in_progress','resolved') NOT NULL DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_name` varchar(150) DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `assigned_by` varchar(150) DEFAULT NULL,
  `admin_remarks` text DEFAULT NULL,
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fcm_tokens`
--

CREATE TABLE `tbl_fcm_tokens` (
  `id` int(11) NOT NULL,
  `resident_id` int(11) NOT NULL,
  `fcm_token` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_fcm_tokens`
--

INSERT INTO `tbl_fcm_tokens` (`id`, `resident_id`, `fcm_token`, `updated_at`) VALUES
(1, 95, 'fj-aPdKqJtg5-F6ulwCMfO:APA91bHp7u8rLqrybb213kTF7n1rOPlLszh9ih6aL1_K1jZpmnECYx8AjSIegiitskLM8yulIU2sSP3KcjG5W60d1zlQaJYOeWl5j283euB2tZhIOU0ZP2A', '2026-05-24 00:56:00'),
(7, 102, 'fj-aPdKqJtg5-F6ulwCMfO:APA91bHp7u8rLqrybb213kTF7n1rOPlLszh9ih6aL1_K1jZpmnECYx8AjSIegiitskLM8yulIU2sSP3KcjG5W60d1zlQaJYOeWl5j283euB2tZhIOU0ZP2A', '2026-05-24 01:21:52'),
(14, 119, 'dyNA4ZH4xU0pHr0_jpvi1C:APA91bGpOVrqKfL7zrLVAPeLqDsKArHV0qOqYAtScnWrjfLXBqxWe21B9yUac0cRxp0HIwjMV1LcTy7471lPUQew3mUrjq1s_03qAz0iTOWCHXrLkpLweIs', '2026-06-18 01:58:22'),
(20, 129, 'edSB87ppk0w1RJmH9b2WdH:APA91bE7-rF4CdFVP1187HdPTQc7rq67HWMjO1GCTkti-Vbvwm1kDNsUC-01i2353BJMiZED93B-epRTHSZm0EkzbOh1q7C_NqnDx86i7mB3p2AzQtkyh5Y', '2026-07-09 05:36:05'),
(25, 136, 'fwjoJOXxzHP2zQJHXlSeCl:APA91bHwMxerm0R6kckzhZgHaGLf3vpVZqtkdokHOxRosqxgmU1BGMpA8C09jcbKirbXn87uH2g56EQDRHH2gyUUHHZ-XrFVN4ELg3iLnsPclDankvGKSk8', '2026-07-26 11:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hidden_announcements`
--

CREATE TABLE `tbl_hidden_announcements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `announcement_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_hidden_announcements`
--

INSERT INTO `tbl_hidden_announcements` (`id`, `user_id`, `announcement_id`) VALUES
(1, 44, 19),
(2, 47, 19),
(3, NULL, 18),
(4, NULL, 17),
(5, NULL, 17),
(6, NULL, 17),
(7, NULL, 16),
(8, NULL, 17),
(9, NULL, 17),
(10, 44, 21),
(11, 44, 20),
(12, 47, 20),
(13, NULL, 20),
(14, NULL, 20),
(15, 44, 22),
(16, 49, 20),
(17, 49, 22),
(18, 49, 23),
(19, 44, 23),
(20, 44, 24),
(21, 54, 25),
(22, 54, 26),
(23, 63, 30),
(24, 75, 35),
(25, 76, 34),
(26, NULL, 34),
(27, 68, 35),
(28, 68, 34),
(29, 68, 33),
(30, 90, 40),
(31, 90, 40),
(32, 95, 43),
(33, 95, 43),
(34, 93, 43),
(35, 93, 43),
(36, 93, 44),
(37, 93, 50),
(38, 93, 50),
(39, 93, 51),
(40, 93, 52),
(41, 93, 51),
(42, 93, 52),
(43, 93, 49),
(44, 93, 53),
(45, 93, 57),
(46, 93, 54),
(47, 93, 56),
(48, 113, 58),
(49, 119, 64);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_id_uploads`
--

CREATE TABLE `tbl_id_uploads` (
  `id_upload` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `message_note` text DEFAULT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_id_uploads`
--

INSERT INTO `tbl_id_uploads` (`id_upload`, `id_resident`, `file_name`, `original_name`, `file_type`, `message_note`, `upload_date`, `status`) VALUES
(13, 88, 'validid_88_1779620402.png', 'Screenshot 2026-05-10 225040.png', 'image/png', 'q2wedrftgynhujmiko/;ljkhgf', '2026-05-24 04:00:02', 'approved'),
(14, 89, 'validid_89_1779620819.jpg', 'IMG20260523183743.jpg', 'image/jpeg', 'Hahahhahaha', '2026-05-24 04:07:00', 'approved'),
(15, 90, 'validid_90_1779679262.png', 'Screenshot 2026-05-10 225040.png', 'image/png', 'qwfertyguh', '2026-05-24 20:21:02', 'approved'),
(16, 91, 'validid_91_1779796694.png', 'goku.png', 'image/png', 'please accept my id verification', '2026-05-26 04:58:13', 'approved'),
(17, 92, 'validid_92_1779800922.png', 'qrcode_gemini.google.com.png', 'image/png', 'wdfghgfsd', '2026-05-26 06:08:42', 'approved'),
(18, 93, 'validid_93_1779855872.jpg', '7f0c7c32-a5ea-4828-b654-6f0b78f95d0c.jpg', 'image/jpeg', '', '2026-05-26 21:24:32', 'approved'),
(19, 94, 'validid_94_1779856099.jpg', '7f0c7c32-a5ea-4828-b654-6f0b78f95d0c.jpg', 'image/jpeg', '', '2026-05-26 21:28:19', 'approved'),
(20, 95, 'validid_95_1779871725.png', 'luffy.png', 'image/png', '', '2026-05-27 01:48:44', 'approved'),
(21, 96, 'validid_96_1779872238.png', 'gojo.png', 'image/png', '', '2026-05-27 01:57:18', 'approved'),
(22, 99, 'validid_99_1780033538.jpeg', '775835f4-b4a4-4265-97b4-7df4db15128f.jpeg', 'image/jpeg', '', '2026-05-28 22:45:38', 'approved'),
(23, 101, 'validid_101_1780199523.png', 'gojo.png', 'image/png', '2e3rftghyj', '2026-05-30 20:52:03', 'approved'),
(24, 102, 'validid_102_1780226300.png', 'screenshot-desktop.png', 'image/png', 'qwertgyjhk', '2026-05-31 04:18:20', 'approved'),
(25, 104, 'validid_104_1780288639.jpg', 'RISK-REGISTER_page-0002.jpg', 'image/jpeg', 'qwerfjhklkjhgerwq', '2026-05-31 21:37:19', 'approved'),
(26, 105, 'validid_105_1780288868.png', 'naruto.png', 'image/png', '1qwedfghjnmkl', '2026-05-31 21:41:08', 'approved'),
(27, 107, 'validid_107_1780485117.png', '2.png', 'image/png', '2ertghjkl;\';lkjhtgre', '2026-06-03 04:11:57', 'approved'),
(28, 108, 'validid_108_1780550756.png', '2.png', 'image/png', 'hehehe', '2026-06-03 22:25:57', 'approved'),
(29, 109, 'validid_109_1780635514.jpg', 'IMG20260601165812.jpg', 'image/jpeg', '', '2026-06-04 21:58:35', 'approved'),
(30, 110, 'validid_110_1780752189.png', '1780285961_6a1d02093458e.png', 'image/png', 'wryuiol,mktyre1', '2026-06-06 06:23:09', 'approved'),
(31, 112, 'validid_112_1780797545.png', '2.png', 'image/png', '', '2026-06-06 18:59:04', 'approved'),
(32, 113, 'validid_113_1780798089.png', '2.png', 'image/png', 'hehe', '2026-06-06 19:08:09', 'approved'),
(33, 114, 'validid_114_1780798621.png', '2.png', 'image/png', '', '2026-06-06 19:17:01', 'approved'),
(34, 114, 'validid_114_1780799159.png', '2.png', 'image/png', '', '2026-06-06 19:25:59', 'approved'),
(35, 115, 'validid_115_1780800068.png', '2.png', 'image/png', '', '2026-06-06 19:41:08', 'approved'),
(36, 116, 'validid_116_1781001377.jpg', '494705055_1346025396513406_8211283353670620323_n.jpg', 'image/jpeg', 'hehe', '2026-06-09 03:36:17', 'approved'),
(37, 119, 'validid_119_1781224539.jpg', 'IMG20260601165812.jpg', 'image/jpeg', 'hehe', '2026-06-11 17:35:38', 'approved'),
(38, 123, 'validid_123_1781441227.jpg', 'inbound8200830726825386435.jpg', 'image/jpeg', 'Voter\'s I\'d', '2026-06-14 05:47:07', 'approved'),
(39, 127, 'validid_127_1783083312.png', 'Screenshot 2026-07-03 104720.png', 'image/png', '', '2026-07-03 05:55:12', 'rejected'),
(40, 128, 'validid_128_1783131176.png', 'Screenshot 2026-06-22 104805.png', 'image/png', '', '2026-07-03 19:12:56', 'pending'),
(41, 120, 'validid_120_1783575612.jpg', 'Screenshot_2025-02-21-23-36-41-43.jpg', 'image/jpeg', '', '2026-07-08 22:40:12', 'approved'),
(42, 130, 'validid_130_1783576700.png', 'BMIS - Context Diagram.png', 'image/png', '', '2026-07-08 22:58:19', 'approved'),
(43, 130, 'validid_130_1783576931.png', 'BMIS_Component_Diagram.png', 'image/png', '', '2026-07-08 23:02:11', 'approved'),
(44, 133, 'validid_133_1783695537.png', 'BMIS_Flowchart.png', 'image/png', '', '2026-07-10 07:58:57', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_indigency`
--

CREATE TABLE `tbl_indigency` (
  `id_indigency` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_indigency`
--

INSERT INTO `tbl_indigency` (`id_indigency`, `id_resident`, `lname`, `fname`, `mi`, `nationality`, `houseno`, `street`, `brgy`, `municipal`, `purpose`, `date`) VALUES
(8, 120, 'Lovite', 'Jay Mark', 'Judavar ', 'Filipino ', 'N/A', 'Santan Street', 'Salvacion ', 'Baao', 'Job/Employment', '2026-07-09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login_history`
--

CREATE TABLE `tbl_login_history` (
  `id_history` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `admin_name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(255) DEFAULT NULL,
  `event` enum('login','logout','failed') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_login_history`
--

INSERT INTO `tbl_login_history` (`id_history`, `id_admin`, `admin_name`, `role`, `email`, `event`, `ip_address`, `user_agent`, `created_at`) VALUES
(512, 5, 'Is This', 'administrator', 'sanpedroiriga@gmail.com', 'login', '61.9.10.38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 03:42:33'),
(513, 5, 'Is This', 'administrator', 'sanpedroiriga@gmail.com', 'login', '61.9.10.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 22:35:13'),
(514, NULL, 'NObleza Domingo', 'resident', 'domingo2@gmail.com', 'logout', '61.9.10.117', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-27 22:36:22'),
(515, 5, 'Is This', 'administrator', 'sanpedroiriga@gmail.com', 'login', '61.9.10.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', '2026-07-28 18:42:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_password_reset_requests`
--

CREATE TABLE `tbl_password_reset_requests` (
  `id` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `temp_password` varchar(255) DEFAULT NULL,
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `resolved_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_password_reset_requests`
--

INSERT INTO `tbl_password_reset_requests` (`id`, `id_resident`, `full_name`, `phone_number`, `status`, `temp_password`, `requested_at`, `resolved_at`, `resolved_by`) VALUES
(4, 120, 'Jay Mark Lovite', NULL, 'pending', NULL, '2026-07-01 19:16:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rescert`
--

CREATE TABLE `tbl_rescert` (
  `id_rescert` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident`
--

CREATE TABLE `tbl_resident` (
  `id_resident` int(11) NOT NULL,
  `login_identity` varchar(255) DEFAULT NULL,
  `res_photo` mediumblob DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `family_role` varchar(255) NOT NULL,
  `voter` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` varchar(100) DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `pwd` varchar(3) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_resident`
--

INSERT INTO `tbl_resident` (`id_resident`, `login_identity`, `res_photo`, `email`, `phone_number`, `password`, `security_question`, `security_answer`, `lname`, `fname`, `mi`, `age`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `address`, `contact`, `bdate`, `bplace`, `nationality`, `family_role`, `voter`, `role`, `is_verified`, `verified_at`, `verified_by`, `addedby`, `pwd`, `is_archived`, `must_change_password`) VALUES
(118, NULL, NULL, 'doming011@gmail.com', NULL, '$2y$10$Z4sjMTDkM8Ld./TiI2OsuuBpYhOS.XfhG6aDc1qkyZQoCWe9fVWb2', 'What is your mother\'s maiden name?', 'melanie', 'Nobleza', 'domingo', 'Bendal', NULL, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '09070560966', '2005-03-20', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 'Yes', 0, 0),
(123, NULL, NULL, 'noblemaeangelie08@gmail.com', NULL, '$2y$10$A6a.JAEu9ArnoduS.CsZk.9jdHtqg3ZdcfTUTA0/64vbrFK8moC9K', NULL, NULL, 'Noble ', 'Mae Angelie ', 'B.', NULL, 'Female', 'Single', 'Zone 3 ', 'Bagig Sirang Street ', 'San Pedro I', 'Iriga City ', NULL, '09093512720', '2004-03-23', 'Antipolo City, Rizal', 'Phillipines ', 'No', 'Yes', 'resident', 1, '2026-06-15 03:22:27', 'Admin', 'Resident', 'No', 0, 0),
(127, NULL, NULL, NULL, '09070560966', '$2y$10$AP2PY/sUolvJBjOmNdhVtenDTC3isfPWu7CJ.bOwIsN0QPg1G8Tru', NULL, NULL, 'Nobleza', 'domingo', 'Bendal', NULL, 'Male', 'Single', 'Blk. 14 Lot 25 El Chapo', 'sagrada', 'iriga', '', 'Blk. 14 Lot 25 El Chapo, sagrada, iriga', '09070560966', '2026-07-01', '', 'Filipino', 'No', 'No', 'resident', 0, NULL, NULL, 'This, Is', 'No', 0, 0),
(129, NULL, NULL, NULL, '09070569634', '$2y$10$tO/eCH9yKwZPUjt1FUefx.DV8Q/dZGHDLn6Na62T3nfQB9PJyNmuq', NULL, NULL, 'Nobleza', 'domingo', 'Bendal', NULL, 'Female', 'Single', 'Blk. 14 Lot 25 El Chapo', 'Hibago', 'Ocampo', '', 'Blk. 14 Lot 25 El Chapo, Hibago, Ocampo', '09070569634', '2026-07-05', '', 'Filipino', 'No', 'No', 'resident', 0, NULL, NULL, 'Nobleza, domingo', 'No', 0, 1),
(131, NULL, NULL, 'Markkevinzabala44@gmail.com', '', '$2y$10$27ZoGE2kkzv3tK7MNZHGNez78YLc6gLzuzyAjCn8/D9GNaDF46piu', NULL, NULL, 'Zabala', 'Mark Kevin ', 'Castro', NULL, 'Male', 'Single', '3448 Zone 3', 'San Pedro', 'Iriga city', '', '3448 Zone 3 , San Pedro , Iriga city', '09466772428', '2026-07-08', '', 'Filipino', 'No', 'No', 'resident', 0, NULL, NULL, 'This, Is', 'No', 0, 0),
(132, NULL, NULL, 'domingonobleza011@gmail.com', NULL, '$2y$10$eab8ZlyS4v0wWv7ZM3B0leHUfDKlM3bP0JqQD4G2.QuFtXDn5WCP2', NULL, NULL, 'Nobleza', 'domingo', 'Bendal', NULL, 'Male', 'Single', 'Blk. 14 Lot 25', 'El Chapo', 'Hibago', 'Ocampo', NULL, '09070569634', '2004-03-23', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-07-09 06:53:01', 'Admin', 'Resident', 'Yes', 0, 0),
(133, NULL, NULL, 'sanpedro@gmail.com', NULL, '$2y$10$a.GRR9503rPJ3qhs0EqAPux6SYXsXOkB/FgASqEPvIazGq1z94Rm6', NULL, NULL, 'dela cruz', 'juan', 'castro', NULL, 'Male', 'Single', 'Blk. 14 Lot 25 El Chapo', 'Antipolo', 'City of Iriga', '', 'Blk. 14 Lot 25 El Chapo, Antipolo, City of Iriga', '09466772428', '2026-07-10', '', 'Filipino', 'No', 'No', 'resident', 1, '2026-07-10 07:59:29', 'Admin', 'This, Is', 'No', 0, 0),
(134, NULL, NULL, 'ivanjay.visitacion03@gmail.com', NULL, '$2y$10$uLpA0ZVo/3kbWDkcZt/oKuIFx03U4YpjDYHk6CFjWXlKWO2FKKJci', NULL, NULL, 'Visitacion', 'Ivan Jay', 'Bolalin', 0, 'Male', 'Married', '08383883', 'Tagongtong', 'Agdangan Pob.', 'Baao', NULL, '09955406746', '2004-07-03', 'Bicol Medical Center', 'Filipino', 'No', 'Yes', 'resident', 1, '2026-07-19 05:31:41', 'Admin', 'Resident', '', 0, 0),
(135, NULL, NULL, NULL, 'N/A', '$2y$10$TTjaGieEZp1aP2GusPpwou5nctV9tmt/fZ84miCCLJEYsQmq64FZC', NULL, NULL, 'Marvz', 'Boss', '.', NULL, 'Male', 'Divorced', 'N/A', 'n/a', 'Seaside ', 'City of Isabela', NULL, '99999999999', '2026-07-19', 'N/a', 'N/a', 'Yes', 'No', 'resident', 1, '2026-07-19 05:43:20', 'Admin', 'Resident', 'No', 0, 0),
(136, NULL, NULL, 'sample@gmail.com', NULL, '$2y$10$C8XulqQcTOq3zrm0zTHMB.hJXlnHjd2DhpOT8/P1nfo88UpDHmrc6', NULL, NULL, 'ban', 'mike', 'bol', NULL, 'Male', 'Single', '1', 'zone 1', 'Uguis', 'Nueva Era', NULL, '01234567890', '2026-12-25', 'baaooo', 'fil', 'No', 'Yes', 'resident', 1, '2026-07-26 04:08:41', 'Admin', NULL, 'No', 0, 0),
(137, NULL, NULL, 'domingo1@gmail.com', NULL, '$2y$10$tjV6Ww3Axvpju6dsMPj48OXR5jp6OXB/QnFskL5LuVqOcBnT1rMy6', NULL, NULL, 'Nobleza', 'domingo', 'Bendal', NULL, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'Hibago', 'Ocampo', NULL, '09070569634', '2004-03-23', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-07-26 04:10:01', 'Admin', 'Resident', 'Yes', 0, 0),
(139, NULL, NULL, 'domingo2@gmail.com', NULL, '$2y$10$UcBqubM9lViveic46uGXGOfdoZaa92IGbHrM3.aYCRRGxzQteo5Ka', NULL, NULL, 'Domingo', 'NObleza', 'Bendal', NULL, 'Male', 'Single', 'Blk. 14 Lot 25', 'El Chapo', 'Hibago', 'Ocampo', NULL, '09070569634', '2004-03-23', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-07-27 22:35:39', 'Admin', 'Resident', 'No', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident_pending`
--

CREATE TABLE `tbl_resident_pending` (
  `id_pending` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `pwd` varchar(10) DEFAULT 'No',
  `sex` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `bdate` date NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `voter` varchar(255) DEFAULT NULL,
  `family_role` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'resident',
  `addedby` varchar(255) DEFAULT NULL,
  `valid_id_file` varchar(255) DEFAULT NULL,
  `valid_id_original_name` varchar(255) DEFAULT NULL,
  `valid_id_file_type` varchar(100) DEFAULT NULL,
  `application_status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, rejected (approved rows are moved into tbl_resident and removed from here)',
  `reject_reason` text DEFAULT NULL,
  `reviewed_by` varchar(100) DEFAULT NULL,
  `date_submitted` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_resident_pending`
--

INSERT INTO `tbl_resident_pending` (`id_pending`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `pwd`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`, `bplace`, `nationality`, `voter`, `family_role`, `role`, `addedby`, `valid_id_file`, `valid_id_original_name`, `valid_id_file_type`, `application_status`, `reject_reason`, `reviewed_by`, `date_submitted`) VALUES
(5, 'domingo@gmail.com', NULL, '$2y$10$8Mn826JsOPzTriEbEGC0bOwY4m3mXhpTfBoUPCZtHT5Jlc1S.85KG', 'Nobleza', 'domingo', 'Bendal', 'No', 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'Hibago', 'Ocampo', '09070569634', '2004-03-23', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 'Resident', 'pendingreg_1785063965_6a65ea1d6a7f2.png', 'Screenshot 2026-07-18 200519.png', 'image/png', 'pending', NULL, NULL, '2026-07-26 04:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `login_identity` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `res_is_verified` tinyint(1) DEFAULT NULL,
  `res_verified_at` datetime DEFAULT NULL,
  `res_verified_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `login_identity`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`, `photo`, `res_is_verified`, `res_verified_at`, `res_verified_by`) VALUES
(71, 'jaymarklovite20@gmail.com', 'jaymarklovite20@gmail.com', NULL, '$2y$10$uSgMHJwhue798DiFXwiBouInBfcv4..Ny7gCeioGNWCgu2K13Anjm', 'Lovite', 'Jay Mark', 'Judavar ', 21, 'Male', 'N/A Santan Street, Salvacion , Baao', '09563719899', 'Punong Barangay', 'user', 'This, Is', NULL, 1, '2026-07-08 22:40:49', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth`
--

CREATE TABLE `tbl_youth` (
  `id_youth` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `mi` varchar(50) NOT NULL,
  `age` varchar(50) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married','Solo Parent','Widowed') NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `educ_attain` varchar(100) NOT NULL,
  `emp_status` enum('Employed','Unemployed','Self-Employed','Student') NOT NULL,
  `skill_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth`
--

INSERT INTO `tbl_youth` (`id_youth`, `fname`, `lname`, `mi`, `age`, `sex`, `civil_status`, `contact_number`, `email_address`, `educ_attain`, `emp_status`, `skill_name`) VALUES
(133, 'juan', 'dela cruz', 'sanpedro', '0', 'Male', 'Single', '9070560963', 'domingonobleza011@gmail.com', 'college graduate', 'Employed', 'eating ');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_bulletin`
--

CREATE TABLE `tbl_youth_bulletin` (
  `id_post` int(11) NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_content` text NOT NULL,
  `post_type` enum('Announcement','Opportunity','Reminder','Achievement','General') DEFAULT 'General',
  `posted_by` varchar(100) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `date_posted` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_bulletin`
--

INSERT INTO `tbl_youth_bulletin` (`id_post`, `post_title`, `post_content`, `post_type`, `posted_by`, `is_pinned`, `date_posted`) VALUES
(6, 'basketball ', 'there is an upcoming basketball tournament in our barangay hall', 'Announcement', NULL, 0, '2026-07-11 04:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_enrollment`
--

CREATE TABLE `tbl_youth_enrollment` (
  `id_enrollment` int(11) NOT NULL,
  `id_program` int(11) NOT NULL,
  `id_youth` int(11) NOT NULL,
  `youth_name` varchar(200) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `enrolled_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('Enrolled','Attended','Dropped') DEFAULT 'Enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_enrollment`
--

INSERT INTO `tbl_youth_enrollment` (`id_enrollment`, `id_program`, `id_youth`, `youth_name`, `contact`, `enrolled_at`, `status`) VALUES
(29, 10, 133, 'dela cruz, juan', '9070560963', '2026-07-11 04:58:22', 'Enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_programs`
--

CREATE TABLE `tbl_youth_programs` (
  `id_program` int(11) NOT NULL,
  `program_title` varchar(200) NOT NULL,
  `program_type` enum('Training','Sports','Arts','Leadership','Health','Livelihood','Scholarship','Community Service','Other') NOT NULL DEFAULT 'Other',
  `description` text DEFAULT NULL,
  `venue` varchar(200) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `slots` int(11) DEFAULT 0,
  `requirements` text DEFAULT NULL,
  `status` enum('Upcoming','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Upcoming',
  `created_by` varchar(100) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_programs`
--

INSERT INTO `tbl_youth_programs` (`id_program`, `program_title`, `program_type`, `description`, `venue`, `event_date`, `event_time`, `slots`, `requirements`, `status`, `created_by`, `date_created`) VALUES
(10, 'basketball ', 'Sports', 'the sk members invite all of the youth to participate in basketball tournament', 'barangay hall', '2026-07-20', '15:00:00', 100, 'wala', 'Upcoming', 'juan dela cruz', '2026-07-11 04:58:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`id_admin_msg`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resident_messages`
--
ALTER TABLE `resident_messages`
  ADD PRIMARY KEY (`id_message`);

--
-- Indexes for table `tbl_activity_log`
--
ALTER TABLE `tbl_activity_log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_admin` (`id_admin`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  ADD PRIMARY KEY (`id_announcement`);

--
-- Indexes for table `tbl_announcement_comments`
--
ALTER TABLE `tbl_announcement_comments`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `fk_comment_announcement` (`announcement_id`);

--
-- Indexes for table `tbl_announcement_reactions`
--
ALTER TABLE `tbl_announcement_reactions`
  ADD PRIMARY KEY (`id_reaction`),
  ADD UNIQUE KEY `unique_reaction` (`announcement_id`,`user_id`);

--
-- Indexes for table `tbl_blotter`
--
ALTER TABLE `tbl_blotter`
  ADD PRIMARY KEY (`id_blotter`);

--
-- Indexes for table `tbl_brgyid`
--
ALTER TABLE `tbl_brgyid`
  ADD PRIMARY KEY (`id_brgyid`);

--
-- Indexes for table `tbl_bspermit`
--
ALTER TABLE `tbl_bspermit`
  ADD PRIMARY KEY (`id_bspermit`);

--
-- Indexes for table `tbl_clearance`
--
ALTER TABLE `tbl_clearance`
  ADD PRIMARY KEY (`id_clearance`);

--
-- Indexes for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_fcm_tokens`
--
ALTER TABLE `tbl_fcm_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_resident` (`resident_id`),
  ADD UNIQUE KEY `uq_resident_id` (`resident_id`);

--
-- Indexes for table `tbl_hidden_announcements`
--
ALTER TABLE `tbl_hidden_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_id_uploads`
--
ALTER TABLE `tbl_id_uploads`
  ADD PRIMARY KEY (`id_upload`),
  ADD KEY `fk_id_upload_resident` (`id_resident`);

--
-- Indexes for table `tbl_indigency`
--
ALTER TABLE `tbl_indigency`
  ADD PRIMARY KEY (`id_indigency`);

--
-- Indexes for table `tbl_login_history`
--
ALTER TABLE `tbl_login_history`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `idx_admin` (`id_admin`),
  ADD KEY `idx_event` (`event`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `tbl_password_reset_requests`
--
ALTER TABLE `tbl_password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reset_resident` (`id_resident`);

--
-- Indexes for table `tbl_rescert`
--
ALTER TABLE `tbl_rescert`
  ADD PRIMARY KEY (`id_rescert`);

--
-- Indexes for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  ADD PRIMARY KEY (`id_resident`);

--
-- Indexes for table `tbl_resident_pending`
--
ALTER TABLE `tbl_resident_pending`
  ADD PRIMARY KEY (`id_pending`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `tbl_youth`
--
ALTER TABLE `tbl_youth`
  ADD PRIMARY KEY (`id_youth`);

--
-- Indexes for table `tbl_youth_bulletin`
--
ALTER TABLE `tbl_youth_bulletin`
  ADD PRIMARY KEY (`id_post`);

--
-- Indexes for table `tbl_youth_enrollment`
--
ALTER TABLE `tbl_youth_enrollment`
  ADD PRIMARY KEY (`id_enrollment`),
  ADD KEY `id_program` (`id_program`);

--
-- Indexes for table `tbl_youth_programs`
--
ALTER TABLE `tbl_youth_programs`
  ADD PRIMARY KEY (`id_program`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_messages`
--
ALTER TABLE `admin_messages`
  MODIFY `id_admin_msg` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resident_messages`
--
ALTER TABLE `resident_messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `tbl_activity_log`
--
ALTER TABLE `tbl_activity_log`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  MODIFY `id_announcement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_announcement_comments`
--
ALTER TABLE `tbl_announcement_comments`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_announcement_reactions`
--
ALTER TABLE `tbl_announcement_reactions`
  MODIFY `id_reaction` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `tbl_archive`
--
ALTER TABLE `tbl_archive`
  MODIFY `id_archive` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_blotter`
--
ALTER TABLE `tbl_blotter`
  MODIFY `id_blotter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_brgyid`
--
ALTER TABLE `tbl_brgyid`
  MODIFY `id_brgyid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_bspermit`
--
ALTER TABLE `tbl_bspermit`
  MODIFY `id_bspermit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_clearance`
--
ALTER TABLE `tbl_clearance`
  MODIFY `id_clearance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_fcm_tokens`
--
ALTER TABLE `tbl_fcm_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_hidden_announcements`
--
ALTER TABLE `tbl_hidden_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tbl_id_uploads`
--
ALTER TABLE `tbl_id_uploads`
  MODIFY `id_upload` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbl_indigency`
--
ALTER TABLE `tbl_indigency`
  MODIFY `id_indigency` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_login_history`
--
ALTER TABLE `tbl_login_history`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=516;

--
-- AUTO_INCREMENT for table `tbl_password_reset_requests`
--
ALTER TABLE `tbl_password_reset_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_rescert`
--
ALTER TABLE `tbl_rescert`
  MODIFY `id_rescert` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111120;

--
-- AUTO_INCREMENT for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  MODIFY `id_resident` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `tbl_resident_pending`
--
ALTER TABLE `tbl_resident_pending`
  MODIFY `id_pending` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_youth`
--
ALTER TABLE `tbl_youth`
  MODIFY `id_youth` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `tbl_youth_bulletin`
--
ALTER TABLE `tbl_youth_bulletin`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_youth_enrollment`
--
ALTER TABLE `tbl_youth_enrollment`
  MODIFY `id_enrollment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tbl_youth_programs`
--
ALTER TABLE `tbl_youth_programs`
  MODIFY `id_program` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_announcement_comments`
--
ALTER TABLE `tbl_announcement_comments`
  ADD CONSTRAINT `fk_comment_announcement` FOREIGN KEY (`announcement_id`) REFERENCES `tbl_announcement` (`id_announcement`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_announcement_reactions`
--
ALTER TABLE `tbl_announcement_reactions`
  ADD CONSTRAINT `fk_reaction_announcement` FOREIGN KEY (`announcement_id`) REFERENCES `tbl_announcement` (`id_announcement`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_youth_enrollment`
--
ALTER TABLE `tbl_youth_enrollment`
  ADD CONSTRAINT `tbl_youth_enrollment_ibfk_1` FOREIGN KEY (`id_program`) REFERENCES `tbl_youth_programs` (`id_program`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
