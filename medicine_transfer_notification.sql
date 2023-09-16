-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2023 at 12:45 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `multihospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `medicine_transfer_notification`
--

CREATE TABLE `medicine_transfer_notification` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `is_view` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine_transfer_notification`
--

INSERT INTO `medicine_transfer_notification` (`id`, `medicine_id`, `hospital_id`, `is_view`, `created_at`) VALUES
(5, 2872, 457, 0, '2023-09-16 14:00:37'),
(6, 2867, 416, 1, '2023-09-16 14:00:37'),
(7, 2866, 457, 0, '2023-09-16 14:01:36'),
(8, 2866, 416, 1, '2023-09-16 14:01:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medicine_transfer_notification`
--
ALTER TABLE `medicine_transfer_notification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medicine_transfer_notification`
--
ALTER TABLE `medicine_transfer_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
