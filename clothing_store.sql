-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2025 at 09:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothing_stotre`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$e1W8lzFUmROu8YQqjKmD2esxKPqSviMTz0VdDWbs6Bp0zL3v7vYOS', '2025-07-14 15:12:27'),
(5, 'jeet', 'jeet@admin.com', '$2y$10$9pPmorO4DAGhl9ufJLlymOv0mOmKtlhlT5YcBdpCWYPD6dsU2l6xm', '2025-07-14 15:33:51');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `created_at`, `quantity`) VALUES
(1, 13, 4, '2025-04-08 07:38:29', 1),
(2, 13, 3, '2025-04-08 07:38:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_details`
--

CREATE TABLE `delivery_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `delivery_details`
--

INSERT INTO `delivery_details` (`id`, `order_id`, `full_name`, `address`, `city`, `state`, `postal_code`, `country`, `phone`) VALUES
(1, 14, 'prashant 12 vishwakarma', 'mumbai', 'Mumbai', 'Maharashtra', 'wrkdl', 'India', '07620171492'),
(2, 15, 'prashant vishwakarma', 'mumbai', 'Mumbai', 'Maharashtra', 'wrkdl', 'India', '07620171492');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT 'COD'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `shipping_address`, `created_at`, `status`, `payment_method`) VALUES
(1, 1, 18700.00, 'c-204 vraj township near patel nagar opp saroli bridge jangirpura surat', '2025-04-01 07:53:22', 'Pending', 'COD'),
(2, 15, 3700.00, 'bardoli', '2025-04-08 08:41:59', 'Pending', 'COD'),
(3, 15, 3700.00, 'bardoli', '2025-04-08 08:45:48', 'Pending', 'COD'),
(4, 15, 3700.00, 'bardloi', '2025-04-08 09:00:05', 'Pending', 'COD'),
(5, 16, 9100.00, 'jangirpura', '2025-04-08 09:07:18', 'Pending', 'COD'),
(6, 17, 7000.00, 'galaxy row house', '2025-04-08 09:32:49', 'Pending', 'COD'),
(7, 18, 3800.00, 'morabhagal', '2025-04-08 14:02:47', 'Pending', 'COD'),
(8, 19, 1500.00, 'tulsi ro house', '2025-04-09 03:23:18', 'Pending', 'COD'),
(9, 20, 2700.00, 'surat', '2025-06-16 04:21:31', 'Pending', 'COD'),
(10, 20, 1200.00, 'mumbai', '2025-06-16 04:31:02', 'Pending', 'COD'),
(11, 21, 3500.00, '', '2025-07-14 12:41:24', 'Pending', 'COD'),
(12, 21, 3500.00, '', '2025-07-14 12:41:38', 'Pending', 'COD'),
(13, 21, 4700.00, '', '2025-07-14 14:18:51', 'Pending', 'COD'),
(14, 21, 4700.00, '', '2025-07-14 14:29:05', 'Pending', 'COD'),
(15, 21, 1200.00, '', '2025-07-14 14:30:31', 'Pending', 'UPI');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `product_price` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_id`, `quantity`, `product_price`) VALUES
(1, 4, 2, 1, 1500.00),
(2, 4, 4, 1, 2200.00),
(3, 5, 2, 3, 1500.00),
(4, 5, 3, 2, 1200.00),
(5, 5, 4, 1, 2200.00),
(6, 6, 1, 1, 2000.00),
(7, 6, 6, 1, 5000.00),
(8, 7, 1, 1, 2000.00),
(9, 7, 5, 1, 1800.00),
(10, 8, 2, 1, 1500.00),
(11, 9, 3, 1, 1200.00),
(12, 9, 2, 1, 1500.00),
(13, 10, 3, 1, 1200.00),
(14, 14, 1, 1, 2000.00),
(15, 14, 2, 1, 1500.00),
(16, 14, 3, 1, 1200.00),
(17, 15, 3, 1, 1200.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `image`) VALUES
(1, 'T-shirt', 'Cotton T-shirt with logo', 2000.00, 50, 'tshirt.jpg'),
(2, 'pants', 'Denim jeans with a slim fit', 1500.00, 30, 'pants.jpg'),
(3, 'hoodie', 'Winter jacket with insulated lining', 1200.00, 20, 'hoodie.jpg'),
(4, 'shirt', 'Winter jacket with insulated lining', 2200.00, 20, 'shirt.jpg'),
(5, 'cargo', 'Winter jacket with insulated lining', 1800.00, 20, 'cargo.jpg'),
(6, 'shoess', 'Winter jacket with insulated lining', 5000.00, 20, 'shoes.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(20, 'xyz', 'munna1@gmail.com', '$2y$10$kqlPSlK1SCvB.NwOpbKHqO4kGFmyKaOk8xRJ.FoD7IbUnoyzBCnN6', '2025-06-16 04:20:54'),
(19, 'nakrani parth', 'parthnakrani@gmail.com', '$2y$10$u1EbQchtANaDVjUzJtIx8OWtZdZaKuOcgRLOUunMZ1kOhM3QVmiii', '2025-04-09 03:22:27'),
(18, 'priyas', 'priyas1@gmail.com', '$2y$10$doPORF5z1lOMTFcQZzCbCeJ7m1lMvfCJ6p1j7eI2YjwXBrAJlzBq2', '2025-04-08 14:02:10'),
(17, 'rudra sarang', 'rudra1@gmail.com', '$2y$10$1I0qKZm6Nmju5z9GOj9xGeH1GV0eiFbCpoAlRRDOq8bJkKqbPAhVO', '2025-04-08 09:31:59'),
(15, 'parth1', 'parth1@gmail.com', '$2y$10$yKNL9wpXe6umfPWm59j7becfDvrHIZMaHcuoDjvdNioWoP5agoYAm', '2025-04-08 08:11:52'),
(16, 'rajiv patel', 'rajiv1@gmail.com', '$2y$10$0c74NWdIvrJdt1hT/bnTS.l0DAZeW0KsFlo748gpw0B1yoN.xiadO', '2025-04-08 09:01:48'),
(13, 'josh', 'josh1@gmail.com', '$2y$10$ydb1W38ohVzuLsmSpTT0S.ahXURKVefN90E5LXnxR0yfvKaYsnUb.', '2025-04-08 07:38:13'),
(12, 'priyas', 'priyas1@gmail.com', '$2y$10$cOx2tcRRY3p/f8t6hTzTN.RRKq6yL85iD37NpElVNa6f.CuKYGuyi', '2025-04-08 06:58:00'),
(21, 'hari', 'hari@gmail.com', '$2y$10$TBpnWqOEItuQBekZYkxozOW/dxLmxvRFHZJ.ScGEQ4nexBJi7oGCC', '2025-07-14 12:39:54'),
(22, 'user', 'user@gmail.com', '$2y$10$QA8bD5cSp2L4PKUiwpYsbO70rco5WHMUSRW1Gt4m4ad4/MuMLCQAy', '2025-07-14 19:36:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_details`
--
ALTER TABLE `delivery_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_details`
--
ALTER TABLE `delivery_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
