-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:4306:4306
-- Generation Time: May 21, 2025 at 07:12 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `j`
--

CREATE TABLE `j` (
  `id` int(11) NOT NULL,
  `jewellery_name` varchar(100) NOT NULL,
  `gold_weight` float NOT NULL,
  `wastage_percent` float NOT NULL,
  `making_charge_percent` float NOT NULL,
  `tax_percent` float NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `j`
--

INSERT INTO `j` (`id`, `jewellery_name`, `gold_weight`, `wastage_percent`, `making_charge_percent`, `tax_percent`, `image_url`) VALUES
(1, 'Elegant Gold Ring', 8, 5, 12, 3, 'images/ring.jpg'),
(2, 'Royal Gold Necklace', 25, 8, 15, 3, 'images/necklace.jpg'),
(3, 'Classic Gold Bracelet', 15, 6, 10, 3, 'images/bracelet.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `j`
--
ALTER TABLE `j`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `j`
--
ALTER TABLE `j`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
