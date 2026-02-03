-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2026 at 08:55 AM
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
-- Database: `ecopoint_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `ac_id` int(6) NOT NULL,
  `title` varchar(225) NOT NULL,
  `image` varchar(225) NOT NULL,
  `point` int(20) NOT NULL,
  `ac_limit` int(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `cm_id` int(10) NOT NULL,
  `news_id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `comment` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `status` enum('draft','publish') DEFAULT 'publish',
  `views` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `record_ac`
--

CREATE TABLE `record_ac` (
  `ev_id` int(7) NOT NULL,
  `ac_id` int(7) NOT NULL,
  `uid` int(7) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'panding',
  `ac_point` int(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ev_notify` int(3) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `record_points`
--

CREATE TABLE `record_points` (
  `p_id` int(6) NOT NULL,
  `uid` int(6) NOT NULL,
  `p_cup` int(20) NOT NULL,
  `p_bottle` int(20) NOT NULL,
  `p_other` varchar(225) NOT NULL,
  `p_total` int(20) NOT NULL,
  `p_giver` varchar(225) NOT NULL,
  `p_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `p_noti` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `record_points`
--

INSERT INTO `record_points` (`p_id`, `uid`, `p_cup`, `p_bottle`, `p_other`, `p_total`, `p_giver`, `p_date`, `p_noti`) VALUES
(1, 12, 1, 2, '5', 10, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:33:15', 0),
(2, 12, 1, 1, '5', 8, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:34:09', 0),
(3, 12, 0, 0, '10', 10, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:42:00', 0),
(4, 12, 1, 1, '5', 8, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:49:24', 0),
(5, 12, 1, 1, '5', 8, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:49:36', 0),
(6, 12, 1, 1, '5', 8, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:49:41', 0),
(7, 12, 1, 1, '5', 8, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:56:09', 0),
(8, 12, 2, 3, '5', 13, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:56:48', 0),
(9, 12, 0, 5, '0', 10, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 04:57:43', 0),
(10, 12, 5, 4, '10', 23, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 06:45:06', 0),
(11, 12, 1, 11, '0', 23, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 06:49:23', 0),
(12, 12, 1, 1, '0', 3, 'เพิ่มคะแนนโดย : Admin', '2025-12-25 06:54:54', 0),
(13, 12, 6, 53, '15', 127, 'เพิ่มคะแนนโดย : Admin', '2026-01-04 05:22:32', 0),
(14, 12, 0, 0, '5', 5, 'เพิ่มคะแนนโดย : Admin', '2026-01-05 14:47:52', 0),
(15, 14, 0, 999, '0', 1998, 'เพิ่มคะแนนโดย : Admin', '2026-01-05 15:04:34', 0),
(16, 14, 0, 999, '0', 1998, 'เพิ่มคะแนนโดย : Admin', '2026-01-05 15:04:40', 0),
(17, 0, 0, 0, 'ได้รับจากกิจกรรม  test', 20, '', '2026-01-07 16:11:04', 1),
(18, 0, 0, 0, 'ได้รับจากกิจกรรม  test', 20, '', '2026-01-07 16:11:53', 1),
(19, 0, 0, 0, 'ได้รับจากกิจกรรม  test', 20, '', '2026-01-07 16:14:11', 1),
(20, 0, 0, 0, 'ได้รับจากกิจกรรม  test', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-07 16:17:28', 1),
(21, 12, 0, 0, 'ได้รับจากกิจกรรม  test', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-07 16:27:42', 0),
(22, 12, 0, 0, 'ได้รับจากกิจกรรม  test', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-07 16:28:56', 0),
(23, 12, 0, 0, 'ได้รับจากกิจกรรม  test', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-07 16:29:18', 0),
(24, 12, 0, 0, 'ได้รับจากกิจกรรม ', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-08 03:33:23', 0),
(25, 12, 0, 0, 'ได้รับจากกิจกรรม กวาดถนนทำความสะอาด', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-08 03:33:29', 0),
(26, 12, 0, 0, 'ได้รับจากกิจกรรม test', 2, 'เพิ่มคะแนนโดย : Admin', '2026-01-08 03:33:32', 0),
(27, 12, 0, 0, 'ได้รับจากกิจกรรม test', 2, 'เพิ่มคะแนนโดย : Admin', '2026-01-08 03:33:34', 0),
(28, 12, 0, 0, 'ได้รับจากกิจกรรม ', 20, 'เพิ่มคะแนนโดย : Admin', '2026-01-08 03:33:37', 0);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `req_id` int(6) NOT NULL,
  `uid` int(6) NOT NULL,
  `rw_id` int(6) NOT NULL,
  `req_price` int(55) NOT NULL,
  `req_status` varchar(50) NOT NULL DEFAULT 'panding',
  `req_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `req_noti` int(5) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `rw_id` int(5) NOT NULL,
  `rw_name` varchar(225) NOT NULL,
  `rw_image` varchar(225) NOT NULL,
  `rw_price` int(22) NOT NULL,
  `rw_stock` int(20) NOT NULL,
  `rw_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `firstname` varchar(225) NOT NULL,
  `lastname` varchar(225) NOT NULL,
  `username` varchar(225) NOT NULL,
  `password` varchar(225) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(225) NOT NULL,
  `image` varchar(225) DEFAULT 'no-profile.png',
  `u_total_point` int(225) NOT NULL DEFAULT 10,
  `u_role` varchar(20) NOT NULL DEFAULT 'member',
  `u_deta` timestamp NOT NULL DEFAULT current_timestamp(),
  `u_noti` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `firstname`, `lastname`, `username`, `password`, `phone`, `email`, `image`, `u_total_point`, `u_role`, `u_deta`, `u_noti`) VALUES
(000010, 'sdfasdfas', 'fffff', 'ppp', '$2y$10$9HUyqAc9l7Ce0xoDdOMqm.FWlNBPrNlT/9OT7zadb9WXOsyJ0xgQ2', '0650574758', 'gg@gg', '1766481566_สไลด์5.JPG', 10, 'member', '2025-12-23 09:19:26', 1),
(000011, 'Thitisak', 'Test', 'hh', '$2y$10$MnTPOrnU7QsQ2VEHsj4crumm0EXDlQCbfPIW5wnXg6SoYRxRHD31K', '063-862-3972', 'titisak@gmail.com', '1766481597_IMG_2164.jpeg', 10, 'member', '2025-12-23 09:19:57', 1),
(000012, 'Admin', 'Thitisak', 'tt', '$2y$10$oe84D0pDmeZpsUOf/wFX6en59UeULRx.Kzui/VYInwU.5A2WvwmhG', '0638623972', 'titisak1412mtp@gmail.com', '1766738422_logo1.png', 298, 'Super admin', '2025-12-25 03:14:38', 1),
(000013, 'tesat', 'test', 'aa', '$2y$10$lrOlGO4FQ6ECyCwDRnXMKeZjtobEECkkWOlX69rxgTeyyoWpWXPBi', '0964548216', 'afs@Asfa', '', 10, 'member', '2025-12-29 04:32:35', 1),
(000014, 'tt', 'ttt', 'ttt', '$2y$10$0ZtDDfcsx0S77Ju/abGag.SMcwuJvdQZTZmz.5EP0GC4s41SR65Z2', '0638623970', 'asd@dasd.com', '1767625373_hatsune-miku-3840x2160-22725.jpg', 99999, 'member', '2025-12-29 06:03:15', 1),
(000015, 'Titisak', 'Phusirit', 'test01', '$2y$10$erqGtobno872McqhvHl29OQ1MEnJ4bhMP84RAtwR4DCUEJzObW4Ba', '0525214520', 'titisak1412mtp@gmail.com', '', 10, 'member', '2026-01-08 11:40:06', 1),
(000016, 'tet', 'eadad', 'fff', '$2y$10$cWmMhwdaVGNf4WJR5uCzuu9CZhQT5IVFeaQTlPc0QK04M0/2st4xa', '0638623972', 'ad2@Asd.com', '1767872823_Screenshot2025-11-17172321.png', 0, 'member', '2026-01-08 11:46:07', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`ac_id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`cm_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`);

--
-- Indexes for table `record_ac`
--
ALTER TABLE `record_ac`
  ADD PRIMARY KEY (`ev_id`);

--
-- Indexes for table `record_points`
--
ALTER TABLE `record_points`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`req_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`rw_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
  MODIFY `ac_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `cm_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `record_ac`
--
ALTER TABLE `record_ac`
  MODIFY `ev_id` int(7) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `record_points`
--
ALTER TABLE `record_points`
  MODIFY `p_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `req_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `rw_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
