-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 19, 2026 at 09:20 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bmis`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_messages`
--

CREATE TABLE `admin_messages` (
  `id_admin_msg` int NOT NULL,
  `id_resident` int NOT NULL,
  `message_text` text NOT NULL,
  `date_sent` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_messages`
--

INSERT INTO `admin_messages` (`id_admin_msg`, `id_resident`, `message_text`, `date_sent`, `status`) VALUES
(14, 66, 'VALID ID SUBMITTED - Please verify my account. Note: none', '2026-04-29 21:48:30', 'unread'),
(18, 73, 'VALID ID SUBMITTED - Please verify my account. Note: detgt', '2026-05-15 14:47:06', 'unread'),
(19, 78, 'VALID ID SUBMITTED - Please verify my account. Note: rgh23rthj', '2026-05-18 22:47:05', 'unread'),
(20, 77, 'VALID ID SUBMITTED - Please verify my account. Note: 123r4tyhj', '2026-05-18 22:48:51', 'unread'),
(21, 76, 'VALID ID SUBMITTED - Please verify my account. Note: 5tyhjmnhgr', '2026-05-18 22:52:03', 'unread'),
(22, 76, '3r4tjhm', '2026-05-18 22:52:30', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `document_settings`
--

CREATE TABLE `document_settings` (
  `id` int NOT NULL DEFAULT '1',
  `barangay_name` varchar(100) DEFAULT 'Barangay San Pedro',
  `city` varchar(100) DEFAULT 'City of Iriga',
  `province` varchar(100) DEFAULT 'Province of Camarines Sur',
  `punong_name` varchar(150) DEFAULT 'JOSEPH B. BEBONIA',
  `punong_title` varchar(100) DEFAULT 'Punong Barangay',
  `office_title` varchar(150) DEFAULT 'OFFICE OF THE PUNONG BARANGAY',
  `footer_note` varchar(255) DEFAULT 'Not valid w/o official seal',
  `logo_path` varchar(255) DEFAULT 'icons/logo.png',
  `seal_path` varchar(255) DEFAULT 'icons/Documents/seal.png',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_settings`
--

INSERT INTO `document_settings` (`id`, `barangay_name`, `city`, `province`, `punong_name`, `punong_title`, `office_title`, `footer_note`, `logo_path`, `seal_path`, `updated_at`) VALUES
(1, 'Barangay San Pedro', 'City of Iriga', 'Province of Camarines Sur', 'JOSEPH B. BEBONIA', 'Punong Barangay', 'OFFICE OF THE PUNONG BARANGAY', 'Not valid w/o official seal', 'icons/logo.png', 'icons/Documents/seal.png', '2026-05-15 14:46:32');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `type` enum('system','sms','email','announcement') DEFAULT 'system',
  `recipient_type` varchar(50) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resident_messages`
--

CREATE TABLE `resident_messages` (
  `id_message` int NOT NULL,
  `id_resident` int NOT NULL,
  `message_text` text NOT NULL,
  `date_sent` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resident_messages`
--

INSERT INTO `resident_messages` (`id_message`, `id_resident`, `message_text`, `date_sent`, `status`) VALUES
(3, 47, 'you\'re id is ready come pick it up ', '2026-04-11 19:20:37', 'sent'),
(5, 44, 'dbfng', '2026-04-12 17:38:32', 'sent'),
(6, 44, 'evrbt', '2026-04-12 17:48:20', 'sent'),
(7, 44, 'sbvdndnt', '2026-04-12 19:37:18', 'sent'),
(8, 44, 'sbvdndnt', '2026-04-12 19:38:05', 'sent'),
(9, 44, 'evrrtg5hy', '2026-04-12 19:41:17', 'sent'),
(10, 44, 'fsbdnh', '2026-04-12 19:42:26', 'sent'),
(14, 65, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-04-27 21:20:14', 'sent'),
(16, 67, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-04-28 18:16:23', 'sent'),
(21, 44, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-01 22:09:07', 'sent'),
(22, 44, 'rgrh', '2026-05-03 08:30:41', 'sent'),
(23, 47, 'EFVEHR', '2026-05-15 09:52:58', 'sent'),
(24, 47, 'EFVEHR', '2026-05-15 09:56:17', 'sent'),
(25, 54, 'SGEG4', '2026-05-15 09:57:57', 'sent'),
(26, 52, 'wefegr', '2026-05-15 09:59:48', 'sent'),
(27, 53, 'd3wg4g3', '2026-05-15 10:00:10', 'sent'),
(28, 72, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-15 10:15:03', 'sent'),
(29, 78, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-18 22:48:12', 'sent'),
(30, 73, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-18 22:48:15', 'sent'),
(31, 77, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-18 22:51:41', 'sent'),
(32, 76, '✅ Your account has been verified! You can now request barangay certificates and other services.', '2026-05-18 22:58:41', 'sent'),
(33, 58, 'WGHJIKJHGFDSA', '2026-05-19 13:39:12', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `resident_tokens`
--

CREATE TABLE `resident_tokens` (
  `id` int NOT NULL,
  `resident_id` int NOT NULL,
  `token` text NOT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int NOT NULL,
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
(5, 'sanpedroiriga@gmail.com', NULL, '$2y$10$EUyPeW.y.WUjSb590jMBjO/cyHo0H0/EKMQdugVbjd0Plv6UWPKMG', 'This', 'Is', 'Admin', 'administrator'),
(10, 'dominge@gmail.com', NULL, '$2y$10$0kaMFdn8NAzSkm3vxCQdDOQq1a4rMXo.jaVorWc96xgScaEfiNJCa', 'nobleza', 'domingo', 'b', 'administrator'),
(11, 'jeanrosenosipeda@gmail.com', NULL, '$2y$10$jxMdSDhpJZQ17JGwQHgunu1GVhkPLsBqb381P3kxFmIGXoM/dtkiW', 'x 11\")', 'US-Letter', '(', 'administrator');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement`
--

CREATE TABLE `tbl_announcement` (
  `id_announcement` int NOT NULL,
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
(73, 'btrgqwerg', NULL, '2026-05-18', 'This, Is Admin', 'active', '1779115602_6a0b26524442f.jpg,1779115602_6a0b265245c84.jpg'),
(74, '2werftghjfdsa', NULL, '2026-05-19', 'This, Is Admin', 'active', '1779181136_6a0c2650c6e7d.png,1779181136_6a0c2650c8115.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement_comments`
--

CREATE TABLE `tbl_announcement_comments` (
  `id_comment` int NOT NULL,
  `announcement_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement_reactions`
--

CREATE TABLE `tbl_announcement_reactions` (
  `id_reaction` int NOT NULL,
  `announcement_id` int NOT NULL,
  `user_id` int NOT NULL,
  `reaction_type` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_announcement_reactions`
--

INSERT INTO `tbl_announcement_reactions` (`id_reaction`, `announcement_id`, `user_id`, `reaction_type`, `created_at`) VALUES
(75, 73, 66, 'like', '2026-05-19 16:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_archive`
--

CREATE TABLE `tbl_archive` (
  `id_archive` int UNSIGNED NOT NULL,
  `record_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'resident | certofres | certofindigency | clearance | bspermit | blotter | youth | brgyid | staff',
  `record_id` int UNSIGNED NOT NULL COMMENT 'Original primary key value from source table',
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Formatted name for quick display',
  `summary` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Short detail string (address, purpose, etc.)',
  `record_data` json NOT NULL COMMENT 'Full row stored as JSON for recovery',
  `deleted_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin username who performed the delete',
  `deleted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `restored_at` datetime DEFAULT NULL,
  `restored_by` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_restored` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_blotter`
--

CREATE TABLE `tbl_blotter` (
  `id_blotter` int NOT NULL,
  `id_resident` int NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `brgy` varchar(255) NOT NULL,
  `municipal` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `narrative` mediumtext NOT NULL,
  `timeapplied` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = active, 1 = soft-deleted',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Timestamp when the record was deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_blotter`
--

INSERT INTO `tbl_blotter` (`id_blotter`, `id_resident`, `lname`, `fname`, `mi`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `narrative`, `timeapplied`, `is_deleted`, `deleted_at`) VALUES
(19, 78, 'x 11', 'US-Letter', '(', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', '09090987658', 'qrklytrewqfrthyukio', '2026-05-19 16:59:34', 0, NULL),
(20, 66, 'DOmingo', 'nobleza', 'bendal', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', '09090987658', 'qwertfgyjhu,k', '2026-05-19 16:59:34', 0, NULL);

--
-- Triggers `tbl_blotter`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_blotter` BEFORE DELETE ON `tbl_blotter` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_blotter 
        AND record_type = 'blotter'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'blotter',
            OLD.id_blotter,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Brgy: ', OLD.brgy, ' | ', LEFT(OLD.narrative, 60)),
            JSON_OBJECT(
                'id_blotter',  OLD.id_blotter,
                'id_resident', OLD.id_resident,
                'lname',       OLD.lname,
                'fname',       OLD.fname,
                'mi',          OLD.mi,
                'houseno',     OLD.houseno,
                'street',      OLD.street,
                'brgy',        OLD.brgy,
                'municipal',   OLD.municipal,
                'contact',     OLD.contact,
                'narrative',   OLD.narrative
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_brgyid`
--

CREATE TABLE `tbl_brgyid` (
  `id_brgyid` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `inc_houseno` varchar(50) DEFAULT NULL,
  `inc_street` varchar(100) DEFAULT NULL,
  `inc_brgy` varchar(100) DEFAULT NULL,
  `inc_municipal` varchar(100) DEFAULT NULL,
  `res_photo` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = active, 1 = soft-deleted',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Timestamp when the record was deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_brgyid`
--

INSERT INTO `tbl_brgyid` (`id_brgyid`, `id_resident`, `lname`, `fname`, `mi`, `houseno`, `street`, `brgy`, `municipal`, `bplace`, `bdate`, `contact`, `inc_lname`, `inc_fname`, `inc_mi`, `inc_contact`, `relation`, `inc_houseno`, `inc_street`, `inc_brgy`, `inc_municipal`, `res_photo`, `is_deleted`, `deleted_at`) VALUES
(20, 66, 'DOmingo', 'nobleza', 'bendal', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', 'Antipolo, Rizal', '2026-03-31', '09070560963', 'x 11\")', 'US-Letter', '', '09090987658', '5tyhjnr3', NULL, NULL, NULL, NULL, NULL, 0, NULL);

--
-- Triggers `tbl_brgyid`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_brgyid` BEFORE DELETE ON `tbl_brgyid` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_brgyid 
        AND record_type = 'brgyid'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'brgyid',
            OLD.id_brgyid,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Brgy: ', OLD.brgy, ' | Municipal: ', OLD.municipal),
            JSON_OBJECT(
                'id_brgyid',    OLD.id_brgyid,
                'id_resident',  OLD.id_resident,
                'lname',        OLD.lname,
                'fname',        OLD.fname,
                'mi',           OLD.mi,
                'houseno',      OLD.houseno,
                'street',       OLD.street,
                'brgy',         OLD.brgy,
                'municipal',    OLD.municipal,
                'bplace',       OLD.bplace,
                'bdate',        OLD.bdate,
                'contact',      OLD.contact,
                'relation',     OLD.relation,
                'inc_lname',    OLD.inc_lname,
                'inc_fname',    OLD.inc_fname,
                'inc_contact',  OLD.inc_contact
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bspermit`
--

CREATE TABLE `tbl_bspermit` (
  `id_bspermit` int NOT NULL,
  `id_resident` int NOT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `mi` varchar(255) DEFAULT NULL,
  `bsname` varchar(255) DEFAULT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(252) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `bsindustry` varchar(255) DEFAULT NULL,
  `aoe` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_bspermit`
--

INSERT INTO `tbl_bspermit` (`id_bspermit`, `id_resident`, `lname`, `fname`, `mi`, `bsname`, `houseno`, `street`, `brgy`, `municipal`, `bsindustry`, `aoe`) VALUES
(11, 78, 'x 11', 'US-Letter', '(', 'bagasan', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', 'Food', 534567890),
(12, 66, 'DOmingo', 'nobleza', 'bendal', 'bagasan', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', 'HealthCare', 234);

--
-- Triggers `tbl_bspermit`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_bspermit` BEFORE DELETE ON `tbl_bspermit` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_bspermit 
        AND record_type = 'bspermit'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'bspermit',
            OLD.id_bspermit,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Business: ', OLD.bsname, ' | Industry: ', OLD.bsindustry),
            JSON_OBJECT(
                'id_bspermit',  OLD.id_bspermit,
                'id_resident',  OLD.id_resident,
                'lname',        OLD.lname,
                'fname',        OLD.fname,
                'mi',           OLD.mi,
                'bsname',       OLD.bsname,
                'houseno',      OLD.houseno,
                'street',       OLD.street,
                'brgy',         OLD.brgy,
                'municipal',    OLD.municipal,
                'bsindustry',   OLD.bsindustry,
                'aoe',          OLD.aoe
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_clearance`
--

CREATE TABLE `tbl_clearance` (
  `id_clearance` int NOT NULL,
  `id_resident` int NOT NULL,
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
(6, 78, 'x 11', 'US-Letter', '(', 'Police Clearance', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', 'Married', '565'),
(7, 66, 'DOmingo', 'nobleza', 'bendal', 'Police Clearance', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', 'Married', '122');

--
-- Triggers `tbl_clearance`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_clearance` BEFORE DELETE ON `tbl_clearance` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_clearance 
        AND record_type = 'clearance'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'clearance',
            OLD.id_clearance,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Purpose: ', OLD.purpose, ' | Status: ', OLD.status),
            JSON_OBJECT(
                'id_clearance', OLD.id_clearance,
                'id_resident',  OLD.id_resident,
                'lname',        OLD.lname,
                'fname',        OLD.fname,
                'mi',           OLD.mi,
                'purpose',      OLD.purpose,
                'houseno',      OLD.houseno,
                'street',       OLD.street,
                'brgy',         OLD.brgy,
                'municipal',    OLD.municipal,
                'status',       OLD.status,
                'age',          OLD.age
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_complaints`
--

CREATE TABLE `tbl_complaints` (
  `id` int NOT NULL,
  `resident_id` int DEFAULT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','resolved') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `admin_remarks` text COLLATE utf8mb4_general_ci,
  `date_submitted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fcm_tokens`
--

CREATE TABLE `tbl_fcm_tokens` (
  `id` int NOT NULL,
  `resident_id` int NOT NULL,
  `fcm_token` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_hidden_announcements`
--

CREATE TABLE `tbl_hidden_announcements` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `announcement_id` int DEFAULT NULL
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
(19, 44, 27),
(20, 44, 25),
(21, 44, 26),
(22, 44, 24),
(23, 44, 29),
(24, 44, 30),
(25, 44, 31),
(26, 44, 32),
(27, 58, 22),
(28, 67, 22),
(29, 67, 34),
(30, 66, 22),
(31, 43, 43),
(32, 43, 43),
(33, 43, 43),
(34, 43, 43),
(35, 44, 43),
(36, 44, 44),
(37, 66, 45),
(38, 66, 43),
(39, 66, 50),
(40, 66, 51),
(41, 67, 52),
(42, 67, 53),
(43, 66, 52),
(44, 66, 53),
(45, 66, 46),
(46, 66, 44),
(47, NULL, 48);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_id_uploads`
--

CREATE TABLE `tbl_id_uploads` (
  `id_upload` int NOT NULL,
  `id_resident` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `message_note` text,
  `upload_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_id_uploads`
--

INSERT INTO `tbl_id_uploads` (`id_upload`, `id_resident`, `file_name`, `original_name`, `file_type`, `message_note`, `upload_date`, `status`) VALUES
(11, 44, 'validid_44_1777644510.jpg', 'b97485c7-e6d6-4b17-a79e-eae059e1cb6f.jpg', 'image/jpeg', 'hanep pa validate po', '2026-05-01 22:08:30', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_indigency`
--

CREATE TABLE `tbl_indigency` (
  `id_indigency` int NOT NULL,
  `id_resident` int NOT NULL,
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
(10, 66, 'DOmingo', 'nobleza', 'bendal', 'batman', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', 'Financial Transaction', '2026-05-04');

--
-- Triggers `tbl_indigency`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_indigency` BEFORE DELETE ON `tbl_indigency` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_indigency 
        AND record_type = 'certofindigency'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'certofindigency',
            OLD.id_indigency,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Purpose: ', OLD.purpose, ' | Date: ', OLD.date),
            JSON_OBJECT(
                'id_indigency', OLD.id_indigency,
                'id_resident',  OLD.id_resident,
                'lname',        OLD.lname,
                'fname',        OLD.fname,
                'mi',           OLD.mi,
                'nationality',  OLD.nationality,
                'houseno',      OLD.houseno,
                'street',       OLD.street,
                'brgy',         OLD.brgy,
                'municipal',    OLD.municipal,
                'purpose',      OLD.purpose,
                'date',         OLD.date
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_programs`
--

CREATE TABLE `tbl_programs` (
  `id_program` int NOT NULL,
  `program_code` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `category` enum('Sports','Education','Livelihood','Health','Environment','Arts','Community Service') NOT NULL,
  `event_type` enum('Workshop','Seminar','Training','Competition','Outreach','Festival','Meeting') NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `registration_deadline` datetime NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `max_participants` int DEFAULT NULL COMMENT 'NULL = unlimited',
  `target_age_min` int DEFAULT '15',
  `target_age_max` int DEFAULT '30',
  `requirements` text,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('Draft','Published','Registration Open','Registration Closed','Ongoing','Completed','Cancelled') DEFAULT 'Draft',
  `banner_image` varchar(500) DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_programs`
--

INSERT INTO `tbl_programs` (`id_program`, `program_code`, `title`, `description`, `category`, `event_type`, `start_date`, `end_date`, `registration_deadline`, `venue`, `max_participants`, `target_age_min`, `target_age_max`, `requirements`, `contact_person`, `contact_number`, `status`, `banner_image`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'EDU-2026-7681', 'hehe', 'hvche', 'Education', 'Seminar', '2026-04-30 00:00:00', '2026-04-14 00:00:00', '2026-04-30 00:00:00', 'barangay hall', NULL, 10, 99, 'wala', '09070569634', '09070560963', 'Ongoing', 'uploads/program_banners/banner_1777543443.jpg', 1, '2026-04-30 10:04:03', '2026-04-30 10:04:03');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_program_attendance`
--

CREATE TABLE `tbl_program_attendance` (
  `id_attendance` int NOT NULL,
  `registration_id` int NOT NULL,
  `scan_datetime` datetime NOT NULL,
  `scan_type` enum('Check-in','Check-out','Manual Entry') DEFAULT 'Manual Entry',
  `scanned_by` int DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_program_gallery`
--

CREATE TABLE `tbl_program_gallery` (
  `id_media` int NOT NULL,
  `program_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` enum('Image','Video') DEFAULT 'Image',
  `caption` text,
  `uploaded_by` int DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_featured` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_program_registrations`
--

CREATE TABLE `tbl_program_registrations` (
  `id_registration` int NOT NULL,
  `program_id` int NOT NULL,
  `user_id` int NOT NULL,
  `registration_code` varchar(60) NOT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Approved','Rejected','Waitlisted','Cancelled') DEFAULT 'Pending',
  `attended` tinyint(1) DEFAULT '0',
  `attendance_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rescert`
--

CREATE TABLE `tbl_rescert` (
  `id_rescert` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `remarks` text NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = active, 1 = soft-deleted',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Timestamp when the record was deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_rescert`
--

INSERT INTO `tbl_rescert` (`id_rescert`, `id_resident`, `lname`, `fname`, `mi`, `age`, `nationality`, `houseno`, `street`, `brgy`, `municipal`, `date`, `purpose`, `remarks`, `is_deleted`, `deleted_at`) VALUES
(111119, 78, 'x 11', 'US-Letter', '(', '565', 'batman', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', '2026-05-20', 'Financial Transaction', '', 0, NULL);

--
-- Triggers `tbl_rescert`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_rescert` BEFORE DELETE ON `tbl_rescert` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_rescert 
        AND record_type = 'certofres'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'certofres',
            OLD.id_rescert,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Purpose: ', OLD.purpose, ' | Date: ', OLD.date),
            JSON_OBJECT(
                'id_rescert',  OLD.id_rescert,
                'id_resident', OLD.id_resident,
                'lname',       OLD.lname,
                'fname',       OLD.fname,
                'mi',          OLD.mi,
                'age',         OLD.age,
                'nationality', OLD.nationality,
                'houseno',     OLD.houseno,
                'street',      OLD.street,
                'brgy',        OLD.brgy,
                'municipal',   OLD.municipal,
                'date',        OLD.date,
                'purpose',     OLD.purpose
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident`
--

CREATE TABLE `tbl_resident` (
  `id_resident` int NOT NULL,
  `res_photo` mediumblob,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `age` int NOT NULL,
  `sex` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bdate` date NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `family_role` varchar(255) NOT NULL,
  `voter` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_at` datetime DEFAULT NULL,
  `verified_by` varchar(100) DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = active, 1 = archived',
  `archived_at` datetime DEFAULT NULL COMMENT 'Timestamp when the resident was archived'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_resident`
--

INSERT INTO `tbl_resident` (`id_resident`, `res_photo`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `contact`, `age`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `address`, `bdate`, `bplace`, `nationality`, `family_role`, `voter`, `role`, `is_verified`, `verified_at`, `verified_by`, `addedby`, `is_archived`, `archived_at`) VALUES
(47, NULL, 'jeanrose@gmail.com', NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', '11xc', NULL, '2026-04-01', 'hibago', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(49, NULL, 'jeanrosenosipeda@gmail.com', NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', '11xc', NULL, '2026-04-01', 'hibago', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(50, NULL, 'jean@gmail.com', NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Single', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', '11xc', NULL, '2026-04-01', 'hibago', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(51, NULL, 'jenay@gmail.com', NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'xw', '11xc', NULL, '2026-04-08', 'hibago', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(52, NULL, NULL, NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Single', 'Blk. 14 Lot 25', '222', 'San Pedro', 'iriga', NULL, '2026-04-01', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(53, NULL, 'domingonobleza011@gmail.com', NULL, '', 'x 11\")', 'US-Letter', '(', NULL, 18, 'Female', 'Married', 'Blk. 14 Lot 25', '222', 'San Pedro', 'iriga', NULL, '2026-04-08', 'hibago', 'Filipino', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(54, NULL, NULL, NULL, '', 'tttbtg', 'efegr', 'egr', NULL, 23, 'Male', 'Married', 'efvsb', 'tntnuyu', 'San Pedro', 'tyntyny', NULL, '2026-04-07', 'Antipolo, Rizal', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(55, NULL, NULL, NULL, '', 'tttbtg', 'efegr', 'egr', NULL, 23, 'Female', 'Single', 'efvsb', 'tntnuyu', 'San Pedro', 'tyntyny', NULL, '2026-04-07', 'Antipolo, Rizal', 'rety', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(57, NULL, 'domdom@gmail.com', NULL, '', 'nobleza', 'domingo', 'bendal', NULL, 23, 'Male', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'tyntyny', NULL, '2026-04-07', 'Antipolo, Rizal', 'Filipino', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(58, NULL, NULL, NULL, '', 'DOmingo', 'nobleza', 'bendal', NULL, 122, 'Male', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-03-31', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(64, NULL, 'dom@gmail.com', NULL, '$2y$10$mfXInKxO5tJYWWcpur5i5u9pLTT6LG/0YaPwcOeeIj98G6J5.lp2C', 'DOmingo', 'nobleza', 'bendal', NULL, 122, 'Female', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-03-31', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-04-27 20:37:48', 'Admin', 'Resident', 0, NULL),
(65, NULL, 'doma@gmail.com', NULL, '$2y$10$7L2ZeEiQOA/MjAQvhPSS/.cXvOIEh/D8gthvAndbYAkebEDkwIbWu', 'DOmingo', 'nobleza', 'bendal', NULL, 122, 'Male', 'Divorced', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-03-31', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-04-27 21:20:14', 'Admin', 'Resident', 0, NULL),
(66, NULL, NULL, '090706569634', '$2y$10$g6CmSida60tvPQWo61DZoeqU36BMEdP5j5c9FaTigy1GwEX/1.g5O', 'DOmingo', 'nobleza', 'bendal', '', 122, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-03-31', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-04-29 21:59:20', 'Admin', 'Resident', 0, NULL),
(67, NULL, NULL, '09070659634', '$2y$10$u3V2BFa5C4F4.R8dewGoK.wu6RtenXHGqcMXpyswMpsduGD/ZjDIm', 'DOmingo', 'nobleza', 'bendal', NULL, 122, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-03-31', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-04-28 18:16:23', 'Admin', 'Resident', 0, NULL),
(68, NULL, NULL, '09867543332', '$2y$10$G2fXtS1FynnCUAuB.3hp0.56ZLOJLt4qSnoDlj7Xa8QWowTAy0ixK', 'Nobleza', 'Domingo', 'B', NULL, 23, 'Male', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-04-30', 'hibago', 'superman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, NULL, 0, NULL),
(69, NULL, NULL, '09867543335', '$2y$10$RjfLtYTdjZ9uLkPSGFrZFeWHK0iQdxATccGUcVtKM8qvY.yzxyKKy', 'Nobleza', 'Domingo', 'B', NULL, 23, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-04-30', 'hibago', 'batman', 'Yes', 'No', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(70, NULL, NULL, 'mnobleza75', '$2y$10$EWkKLUKKVa8h4mP4lZnwKOYb2esvvP82oKHcC3E.vTLPiUDOPlbhy', 'Nobleza', 'Domingo', 'B', '09070560963', 23, 'Male', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-04-30', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(71, NULL, NULL, 'mnobleza@75', '$2y$10$Kht2x0lQUi63kb8PFd1vyuIIKf3vC4vOflKo03qmgsbxC/nGstwJq', 'Nobleza', 'Domingo', 'B', '09070560963', 23, 'Male', 'Single', 'Blk. 14 Lot 25', 'El Chapo', 'San Pedro', 'iriga', NULL, '2026-04-30', 'hibago', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 1, '2026-05-14 20:23:01'),
(72, NULL, NULL, 'US-Letter', '$2y$10$s59zdT.eFEChk2asj2zvKO5peulmBS8D9q2Ili6x.Zv5BH6MlebG2', 'x 11\")', 'US-Letter', '(', '09070560963', 565, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-06', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-05-15 10:15:03', 'Admin', 'Resident', 0, NULL),
(73, NULL, NULL, '09070560963', '$2y$10$KPgo7f1BkRLKDNVQnx80LeTNYkodDI5OJLvnpzddyua6xtSFZlXdq', 'x 11\")', 'US-Letter', '(', '09070560963', 565, 'Male', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-06', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 1, '2026-05-18 22:48:15', 'Admin', 'Resident', 0, NULL),
(74, NULL, NULL, '09090987654', '$2y$10$cnkib3fk/wcqaI7QN0mxZOyjx.rKqHNV9kp4hVm2pRYxfh8Psgv9O', 'x 11\")', 'US-Letter', '(', '09090987654', 565, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(75, NULL, NULL, '09090987655', '$2y$10$5LmXdQ8HN/bfaI5odXQSl.aYOmQSJTf2eZUSdjY2WBlJimcox57FG', 'x 11\")', 'US-Letter', '(', '09090987655', 565, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'Yes', 'No', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(76, NULL, NULL, '09090987656', '$2y$10$Hpu8zK5UNxQgahR5fjzsW.CvQK5Vm3cTMFq4SSuIO/IL4m7.T.kJK', 'x 11\")', 'US-Letter', '(', '09090987656', 565, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'Yes', 'No', 'resident', 1, '2026-05-18 22:58:41', 'Admin', 'Resident', 0, NULL),
(77, NULL, NULL, '09090987657', '$2y$10$5WZnm0tmzYs3N0c1ipdzH.XLQ8Z5AbYk6igzIhfegseV8vbtnVL5e', 'x 11\")', 'US-Letter', '(', '09090987657', 565, 'Female', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'Yes', 'No', 'resident', 1, '2026-05-18 22:51:41', 'Admin', 'Resident', 0, NULL),
(78, NULL, NULL, NULL, '', 'x 11\")', 'US-Letter', '(', '09090987658', 565, 'Female', 'Divorced', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'No', 'No', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(79, NULL, NULL, NULL, '', 'x 11\")', 'US-Letter', '(', '09345678998', 565, 'Female', 'Divorced', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'Yes', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL),
(80, NULL, NULL, NULL, '', 'x 11\")', 'US-Letter', '(', '09345678999', 565, 'Male', 'Married', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', NULL, '2026-05-07', 'Antipolo, Rizal', 'batman', 'No', 'Yes', 'resident', 0, NULL, NULL, 'Resident', 0, NULL);

--
-- Triggers `tbl_resident`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_resident` BEFORE DELETE ON `tbl_resident` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_resident 
        AND record_type = 'resident'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'resident',
            OLD.id_resident,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Brgy: ', OLD.brgy, ' | Municipal: ', OLD.municipal, ' | Age: ', OLD.age),
            JSON_OBJECT(
                'id_resident',  OLD.id_resident,
                'res_photo',    OLD.res_photo,
                'email',        OLD.email,
                'phone_number', OLD.phone_number,
                'password',     OLD.password,       -- ✅ ADDED (required for login)
                'lname',        OLD.lname,
                'fname',        OLD.fname,
                'mi',           OLD.mi,
                'age',          OLD.age,
                'sex',          OLD.sex,
                'status',       OLD.status,
                'houseno',      OLD.houseno,
                'street',       OLD.street,
                'brgy',         OLD.brgy,
                'municipal',    OLD.municipal,
                'address',      OLD.address,
                'contact',      OLD.contact,
                'bdate',        OLD.bdate,
                'bplace',       OLD.bplace,
                'nationality',  OLD.nationality,
                'voter',        OLD.voter,
                'family_role',  OLD.family_role,
                'role',         OLD.role,
                'is_verified',  OLD.is_verified,    -- ✅ ADDED (required for login)
                'verified_at',  OLD.verified_at,
                'verified_by',  OLD.verified_by,    -- ✅ ADDED
                'addedby',      OLD.addedby
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sms_log`
--

CREATE TABLE `tbl_sms_log` (
  `id` int NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text,
  `type` varchar(50) DEFAULT 'manual',
  `status` varchar(20) DEFAULT 'pending',
  `sent_by` varchar(255) DEFAULT NULL,
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_sms_log`
--

INSERT INTO `tbl_sms_log` (`id`, `recipient_name`, `phone`, `message`, `type`, `status`, `sent_by`, `sent_at`) VALUES
(1, 'Nobleza, Domingo', '09070560963', 'jshcvjvrkbjbknbhvf', 'broadcast', 'failed', 'This, Is', '2026-05-08 13:19:54'),
(2, 'Nobleza, Domingo', '09070560963', 'dbvnym', 'broadcast', 'failed', 'This, Is', '2026-05-08 13:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int NOT NULL,
  `login_identity` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `login_identity`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`, `photo`) VALUES
(37, 'techosquad@gmail.com', 'techosquad@gmail.com', '', '', 'x 11\")', 'US-Letter', '(', 23, 'Male', 'Sagrada, Iriga', '09345678998', 'IPMRR Representative', 'user', 'This, Is', 'uploads/1777876743_ef558819-e415-4eb8-a1e2-2384de50af15.jpg'),
(38, 'techosquad@gmail.com', 'techosquad@gmail.com', '', '', 'x 11\")', 'US-Letter', '(', 23, 'Male', 'Sagrada, Iriga', '09345678998', 'Committee on Agriculture', 'user', 'This, Is', 'uploads/1777894241_Screenshot_4-5-2026_154228_barangaysanpedro.gt.tc.jpeg'),
(39, 'sanpedro@gmail.com', 'sanpedro@gmail.com', '', '', 'x 11\")', 'US-Letter', '(', 23, 'Male', 'Sagrada, Iriga', '09070560963', 'Punong Barangay', 'user', 'This, Is', 'uploads/1778241702_Screenshot_4-5-2026_154228_barangaysanpedro.gt.tc.jpeg'),
(40, 'sanpedroiriga@gmail.com', 'sanpedroiriga@gmail.com', '', '', 'x 11\")', 'US-Letter', '(', 122, 'Male', 'jeanrosenosipeda@gmail.com', '09070560963', 'IPMRR Representative', 'user', 'This, Is', 'uploads/1779181811_Screenshot_4-5-2026_154228_barangaysanpedro.gt.tc.jpeg');

--
-- Triggers `tbl_user`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_staff` BEFORE DELETE ON `tbl_user` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_user 
        AND record_type = 'staff'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'staff',
            OLD.id_user,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Position: ', OLD.position, ' | Role: ', OLD.role),
            JSON_OBJECT(
                'id_user',        OLD.id_user,
                'login_identity', OLD.login_identity,
                'email',          OLD.email,
                'phone_number',   OLD.phone_number,
                'lname',          OLD.lname,
                'fname',          OLD.fname,
                'mi',             OLD.mi,
                'age',            OLD.age,
                'sex',            OLD.sex,
                'address',        OLD.address,
                'contact',        OLD.contact,
                'position',       OLD.position,
                'role',           OLD.role,
                'addedby',        OLD.addedby,
                'photo',          OLD.photo
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth`
--

CREATE TABLE `tbl_youth` (
  `id_youth` int NOT NULL,
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
(11, 'US-Letter', 'x 11\")', '', '565', 'Female', 'Single', '9070560963', 'jeanrosenosipeda@gmail.com', 'college graduate', 'Employed', '7jthm,gt'),
(12, 'US-Letter', 'x 11\")', '', '122', 'Male', 'Single', '9070560963', 'jeanrosenosipeda@gmail.com', 'college graduate', 'Employed', 'wedrfghjjmgfdsa');

--
-- Triggers `tbl_youth`
--
DELIMITER $$
CREATE TRIGGER `trg_archive_youth` BEFORE DELETE ON `tbl_youth` FOR EACH ROW BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM tbl_archive 
        WHERE record_id = OLD.id_youth 
        AND record_type = 'youth'
    ) THEN
        INSERT INTO tbl_archive (record_type, record_id, full_name, summary, record_data)
        VALUES (
            'youth',
            OLD.id_youth,
            CONCAT(OLD.lname, ', ', OLD.fname, IF(OLD.mi IS NOT NULL AND OLD.mi != '', CONCAT(' ', OLD.mi, '.'), '')),
            CONCAT('Age: ', OLD.age, ' | Status: ', OLD.civil_status, ' | Skills: ', OLD.skill_name),
            JSON_OBJECT(
                'id_youth',       OLD.id_youth,
                'lname',          OLD.lname,
                'fname',          OLD.fname,
                'mi',             OLD.mi,
                'age',            OLD.age,
                'sex',            OLD.sex,
                'civil_status',   OLD.civil_status,
                'contact_number', OLD.contact_number,
                'email_address',  OLD.email_address,
                'educ_attain',    OLD.educ_attain,
                'emp_status',     OLD.emp_status,
                'skill_name',     OLD.skill_name
            )
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_bulletin`
--

CREATE TABLE `tbl_youth_bulletin` (
  `id_post` int NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_content` text NOT NULL,
  `post_type` enum('Announcement','Opportunity','Reminder','Achievement','General') DEFAULT 'General',
  `posted_by` varchar(100) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT '0',
  `date_posted` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_bulletin`
--

INSERT INTO `tbl_youth_bulletin` (`id_post`, `post_title`, `post_content`, `post_type`, `posted_by`, `is_pinned`, `date_posted`) VALUES
(1, 'Welcome to the Barangay San Pedro Youth Portal!', 'We are excited to launch the new Youth Engagement section of our Barangay Management System. Register your profile, join programs, and stay updated with youth activities!', 'Announcement', 'SK Admin', 1, '2026-05-06 04:04:20'),
(2, 'Scholarship Opportunity: CHED Free Tuition', 'All incoming college students are reminded to secure their CHED scholarship forms at the Barangay Hall. Deadline is this month!', 'Opportunity', 'SK Admin', 0, '2026-05-06 04:04:20');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_enrollment`
--

CREATE TABLE `tbl_youth_enrollment` (
  `id_enrollment` int NOT NULL,
  `id_program` int NOT NULL,
  `id_youth` int NOT NULL,
  `youth_name` varchar(200) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Enrolled','Attended','Dropped') DEFAULT 'Enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_enrollment`
--

INSERT INTO `tbl_youth_enrollment` (`id_enrollment`, `id_program`, `id_youth`, `youth_name`, `contact`, `enrolled_at`, `status`) VALUES
(1, 4, 66, ' DOmingo', '', '2026-05-06 04:11:42', 'Enrolled'),
(2, 2, 66, ' DOmingo', '', '2026-05-06 04:11:48', 'Enrolled'),
(3, 3, 66, ' DOmingo', '', '2026-05-06 04:12:11', 'Enrolled'),
(4, 1, 66, 'nobleza DOmingo', '09070560963', '2026-05-06 04:15:54', 'Enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth_programs`
--

CREATE TABLE `tbl_youth_programs` (
  `id_program` int NOT NULL,
  `program_title` varchar(200) NOT NULL,
  `program_type` enum('Training','Sports','Arts','Leadership','Health','Livelihood','Scholarship','Community Service','Other') NOT NULL DEFAULT 'Other',
  `description` text,
  `venue` varchar(200) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `slots` int DEFAULT '0',
  `requirements` text,
  `status` enum('Upcoming','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Upcoming',
  `created_by` varchar(100) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth_programs`
--

INSERT INTO `tbl_youth_programs` (`id_program`, `program_title`, `program_type`, `description`, `venue`, `event_date`, `event_time`, `slots`, `requirements`, `status`, `created_by`, `date_created`) VALUES
(1, 'SK Leadership Seminar 2025', 'Leadership', 'A seminar aimed at developing leadership skills among the youth of Barangay San Pedro.', 'Barangay Hall Multi-Purpose Room', '2026-05-20', '08:00:00', 50, 'Must be 15-30 years old, Barangay resident', 'Upcoming', 'Admin', '2026-05-06 04:04:20'),
(2, 'Free Computer Literacy Training', 'Training', 'Basic computer skills training including MS Office and internet usage.', 'SK Office', '2026-05-13', '13:00:00', 30, 'No prior experience needed, ages 15-25', 'Cancelled', 'Admin', '2026-05-06 04:04:20'),
(3, 'Barangay Youth Basketball League', 'Sports', 'Inter-purok basketball tournament for youth aged 15-30.', 'Barangay Basketball Court', '2026-05-27', '07:00:00', 80, 'Must be a registered youth resident', 'Upcoming', 'Admin', '2026-05-06 04:04:20'),
(4, 'tybyum', 'Scholarship', 'xdgbgj', 'barangay hall', '2026-05-04', '00:10:00', 100, 'wala', 'Ongoing', ' This', '2026-05-06 04:10:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_messages`
--
ALTER TABLE `admin_messages`
  ADD PRIMARY KEY (`id_admin_msg`);

--
-- Indexes for table `document_settings`
--
ALTER TABLE `document_settings`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `resident_tokens`
--
ALTER TABLE `resident_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_token` (`token`(255));

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
-- Indexes for table `tbl_archive`
--
ALTER TABLE `tbl_archive`
  ADD PRIMARY KEY (`id_archive`),
  ADD KEY `idx_record_type` (`record_type`),
  ADD KEY `idx_deleted_at` (`deleted_at`),
  ADD KEY `idx_is_restored` (`is_restored`);

--
-- Indexes for table `tbl_blotter`
--
ALTER TABLE `tbl_blotter`
  ADD PRIMARY KEY (`id_blotter`),
  ADD KEY `idx_blotter_is_deleted` (`is_deleted`);

--
-- Indexes for table `tbl_brgyid`
--
ALTER TABLE `tbl_brgyid`
  ADD PRIMARY KEY (`id_brgyid`),
  ADD KEY `idx_brgyid_is_deleted` (`is_deleted`);

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
  ADD UNIQUE KEY `unique_resident` (`resident_id`);

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
-- Indexes for table `tbl_programs`
--
ALTER TABLE `tbl_programs`
  ADD PRIMARY KEY (`id_program`),
  ADD UNIQUE KEY `program_code` (`program_code`);

--
-- Indexes for table `tbl_program_attendance`
--
ALTER TABLE `tbl_program_attendance`
  ADD PRIMARY KEY (`id_attendance`),
  ADD KEY `registration_id` (`registration_id`);

--
-- Indexes for table `tbl_program_gallery`
--
ALTER TABLE `tbl_program_gallery`
  ADD PRIMARY KEY (`id_media`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `tbl_program_registrations`
--
ALTER TABLE `tbl_program_registrations`
  ADD PRIMARY KEY (`id_registration`),
  ADD UNIQUE KEY `registration_code` (`registration_code`),
  ADD UNIQUE KEY `unique_registration` (`program_id`,`user_id`);

--
-- Indexes for table `tbl_rescert`
--
ALTER TABLE `tbl_rescert`
  ADD PRIMARY KEY (`id_rescert`),
  ADD KEY `idx_rescert_is_deleted` (`is_deleted`);

--
-- Indexes for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  ADD PRIMARY KEY (`id_resident`),
  ADD KEY `idx_is_archived` (`is_archived`);

--
-- Indexes for table `tbl_sms_log`
--
ALTER TABLE `tbl_sms_log`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id_admin_msg` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resident_messages`
--
ALTER TABLE `resident_messages`
  MODIFY `id_message` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `resident_tokens`
--
ALTER TABLE `resident_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  MODIFY `id_announcement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `tbl_announcement_comments`
--
ALTER TABLE `tbl_announcement_comments`
  MODIFY `id_comment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_announcement_reactions`
--
ALTER TABLE `tbl_announcement_reactions`
  MODIFY `id_reaction` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `tbl_archive`
--
ALTER TABLE `tbl_archive`
  MODIFY `id_archive` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `tbl_blotter`
--
ALTER TABLE `tbl_blotter`
  MODIFY `id_blotter` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_brgyid`
--
ALTER TABLE `tbl_brgyid`
  MODIFY `id_brgyid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_bspermit`
--
ALTER TABLE `tbl_bspermit`
  MODIFY `id_bspermit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_clearance`
--
ALTER TABLE `tbl_clearance`
  MODIFY `id_clearance` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_complaints`
--
ALTER TABLE `tbl_complaints`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_fcm_tokens`
--
ALTER TABLE `tbl_fcm_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_hidden_announcements`
--
ALTER TABLE `tbl_hidden_announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tbl_id_uploads`
--
ALTER TABLE `tbl_id_uploads`
  MODIFY `id_upload` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_indigency`
--
ALTER TABLE `tbl_indigency`
  MODIFY `id_indigency` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_programs`
--
ALTER TABLE `tbl_programs`
  MODIFY `id_program` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_program_attendance`
--
ALTER TABLE `tbl_program_attendance`
  MODIFY `id_attendance` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_program_gallery`
--
ALTER TABLE `tbl_program_gallery`
  MODIFY `id_media` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_program_registrations`
--
ALTER TABLE `tbl_program_registrations`
  MODIFY `id_registration` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rescert`
--
ALTER TABLE `tbl_rescert`
  MODIFY `id_rescert` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111120;

--
-- AUTO_INCREMENT for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  MODIFY `id_resident` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tbl_sms_log`
--
ALTER TABLE `tbl_sms_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_youth`
--
ALTER TABLE `tbl_youth`
  MODIFY `id_youth` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_youth_bulletin`
--
ALTER TABLE `tbl_youth_bulletin`
  MODIFY `id_post` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_youth_enrollment`
--
ALTER TABLE `tbl_youth_enrollment`
  MODIFY `id_enrollment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_youth_programs`
--
ALTER TABLE `tbl_youth_programs`
  MODIFY `id_program` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Constraints for table `tbl_program_attendance`
--
ALTER TABLE `tbl_program_attendance`
  ADD CONSTRAINT `tbl_program_attendance_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `tbl_program_registrations` (`id_registration`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_program_gallery`
--
ALTER TABLE `tbl_program_gallery`
  ADD CONSTRAINT `tbl_program_gallery_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `tbl_programs` (`id_program`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_program_registrations`
--
ALTER TABLE `tbl_program_registrations`
  ADD CONSTRAINT `tbl_program_registrations_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `tbl_programs` (`id_program`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_youth_enrollment`
--
ALTER TABLE `tbl_youth_enrollment`
  ADD CONSTRAINT `tbl_youth_enrollment_ibfk_1` FOREIGN KEY (`id_program`) REFERENCES `tbl_youth_programs` (`id_program`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
