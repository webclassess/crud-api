-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2022 at 02:37 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crud_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_slot`
--

CREATE TABLE `tbl_slot` (
  `id` int(11) NOT NULL,
  `slot` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_slot`
--

INSERT INTO `tbl_slot` (`id`, `slot`) VALUES
(1, 'a:3:{i:0;a:3:{i:0;s:1:\"1\";i:1;s:1:\"1\";i:2;s:1:\"1\";}i:1;a:3:{i:0;s:1:\"2\";i:1;s:1:\"2\";i:2;s:1:\"2\";}i:2;a:3:{i:0;s:1:\"3\";i:1;s:1:\"3\";i:2;s:1:\"3\";}}');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` bigint(20) NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `role` int(11) DEFAULT NULL COMMENT '1= admin and 2= user',
  `password` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `created_date` date DEFAULT NULL,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `name`, `email`, `mobile`, `role`, `password`, `image`, `created_date`, `modified_date`) VALUES
(1, 'sunil', 'sunil@mail.com', '7503688503', 1, '123', 'assets/uploads/750368850386/2038251052-sunil12.jpg', '2022-02-22', '2022-03-12 11:34:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_slot`
--
ALTER TABLE `tbl_slot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_slot`
--
ALTER TABLE `tbl_slot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
