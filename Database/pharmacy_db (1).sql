-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 22, 2026 at 06:02 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmacy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `description`) VALUES
(1, 'Vitamins', NULL),
(2, 'Antipyretics', NULL),
(3, 'Analgesics', NULL),
(4, 'Antibiotics', NULL),
(5, 'Antiseptics', NULL),
(6, 'Mood stabilizers', NULL),
(7, 'CNS', NULL),
(13, 'Citapriobin', '');

-- --------------------------------------------------------

--
-- Table structure for table `customer_list`
--

CREATE TABLE `customer_list` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `contact` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customer_list`
--

INSERT INTO `customer_list` (`id`, `name`, `contact`, `address`, `phone`) VALUES
(1, 'Regular Customer', '01800000000', 'Walk-in Customer', NULL),
(3, 'Shabbir Mahmud B', '2354880544543', 'etsertyewryy', '2354880544543'),
(4, 'Nasir uddin patwary', '017804458', 'Mirpur-2, panirtenki, Shialbari, Dhaka 1216', '017804458'),
(6, 'Rokon Mahmud', '0166559900', 'East Monhampara,Dhaka 1207', '0166559900');

-- --------------------------------------------------------

--
-- Table structure for table `expired_product`
--

CREATE TABLE `expired_product` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `date_expired` date NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '1= stockin , 2 = stockout',
  `stock_from` varchar(100) NOT NULL COMMENT 'sales/receiving',
  `form_id` int NOT NULL,
  `expiry_date` date NOT NULL,
  `expired_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `other_details` text NOT NULL,
  `remarks` text NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `type_id` int DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `price` double NOT NULL,
  `name` varchar(150) NOT NULL,
  `measurement` varchar(50) DEFAULT NULL,
  `description` text NOT NULL,
  `prescription` tinyint(1) NOT NULL DEFAULT '0',
  `expiry_date` date DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `quantity` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `type_id`, `sku`, `price`, `name`, `measurement`, `description`, `prescription`, `expiry_date`, `supplier_id`, `quantity`) VALUES
(2, 4, 2, '59118534', 10, 'Cephalexin 250', '250mg', 'Antibiotic capsule', 0, NULL, NULL, 8),
(3, 3, 5, '90433847', 15, 'Napa Extra', '500mg', 'Paracetamol tablet', 0, NULL, NULL, 10),
(5, 1, 5, '#234534ffdsg', 100, 'vitamin A', '500mg', 'vitamin er upor kono medicine nai', 0, NULL, NULL, 0),
(6, 3, 5, 'fdsgvdsfgsdfs', 120, 'Fexo', '120', '', 0, NULL, NULL, 10),
(7, 5, 1, 'fdshgsgfdh35', 200.01, 'Fenadine', '120', '', 0, '2026-11-17', 2, 6),
(8, 1, NULL, '#234534ffdsgdfd', 50, 'Rocal D', '500mg', 'Vitamin D', 0, '2026-11-26', 1, 80),
(9, 3, NULL, 'ga-777', 250, 'Gaviscon', '500ml', 'Gastriside Medicine', 0, '2026-11-30', 2, 45),
(10, 5, NULL, 'mx-2334', 7, 'Maxpro', '20', 'Gastriside Medicine', 0, '2026-11-18', 3, 1995),
(11, 5, NULL, 'mx-23348', 8, 'Maxpro', '40', 'Gastriside', 0, '2026-11-26', 3, 1999);

-- --------------------------------------------------------

--
-- Table structure for table `receiving_items`
--

CREATE TABLE `receiving_items` (
  `id` int NOT NULL,
  `receiving_id` int NOT NULL,
  `product_id` int NOT NULL,
  `qty` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `receiving_items`
--

INSERT INTO `receiving_items` (`id`, `receiving_id`, `product_id`, `qty`, `price`, `total`) VALUES
(1, 7, 2, 20, 50.00, 1000.00),
(2, 8, 3, 10, 5.00, 50.00),
(3, 8, 6, 10, 8.00, 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `receiving_list`
--

CREATE TABLE `receiving_list` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `total_items` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `receiving_list`
--

INSERT INTO `receiving_list` (`id`, `supplier_id`, `total_items`, `total_amount`, `date_created`) VALUES
(2, 1, 4, 1999.96, '2026-11-14 12:02:20'),
(3, 1, 4, 1999.96, '2026-11-14 12:02:58'),
(4, 1, 4, 1999.96, '2026-11-14 12:03:00'),
(5, 1, 4, 1999.96, '2026-11-14 12:05:54'),
(6, 2, 5, 2220.00, '2026-11-14 12:06:35'),
(7, 3, 20, 1000.00, '2026-11-21 09:51:58'),
(8, 3, 20, 130.00, '2026-11-21 09:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `sales_list`
--

CREATE TABLE `sales_list` (
  `id` int NOT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `customer_id` int NOT NULL DEFAULT '0',
  `total_amount` double NOT NULL,
  `amount_tendered` double NOT NULL,
  `amount_change` double NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sales_list`
--

INSERT INTO `sales_list` (`id`, `ref_no`, `customer_id`, `total_amount`, `amount_tendered`, `amount_change`, `date_updated`, `date_created`) VALUES
(1, 'REF-267011', 1, 14, 20, 6, '2026-11-14 12:50:05', '2026-11-14 12:50:05'),
(2, 'REF-377751', 1, 14, 20, 6, '2026-11-14 12:50:34', '2026-11-14 12:50:34'),
(3, 'REF-540931', 1, 2500, 3000, 500, '2026-11-15 12:34:30', '2026-11-15 12:34:30'),
(4, 'REF-854789', 1, 200.01, 300, 99.99, '2026-11-15 12:48:51', '2026-11-15 12:48:51'),
(5, 'REF-938472', 4, 1250, 1500, 250, '2026-11-21 10:02:40', '2026-11-21 10:02:40'),
(6, 'REF-644844', 4, 50, 600, 550, '2026-11-21 10:15:54', '2026-11-21 10:15:54'),
(7, 'REF-477243', 1, 70, 100, 30, '2026-11-22 10:12:54', '2026-11-22 10:12:54'),
(8, 'REF-841025', 1, 35, 40, 5, '2026-11-22 11:09:47', '2026-11-22 11:09:47'),
(9, 'REF-105073', 4, 8, 10, 2, '2026-11-22 11:19:20', '2026-11-22 11:19:20');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int NOT NULL,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `price`, `qty`, `total`) VALUES
(1, 1, 1, 7.00, 2, 14.00),
(2, 2, 1, 7.00, 2, 14.00),
(3, 3, 7, 500.00, 5, 2500.00),
(4, 4, 7, 200.01, 1, 200.01),
(5, 5, 9, 250.00, 5, 1250.00),
(6, 6, 2, 10.00, 5, 50.00),
(7, 7, 2, 10.00, 7, 70.00),
(8, 8, 10, 7.00, 5, 35.00),
(9, 9, 11, 8.00, 1, 8.00);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_list`
--

CREATE TABLE `supplier_list` (
  `id` int NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `supplier_list`
--

INSERT INTO `supplier_list` (`id`, `supplier_name`, `contact_person`, `phone`, `email`, `address`) VALUES
(1, 'Drug Internationals', 'Rahim Khan', '2354880544543', 'rahim@gmail.com', 'asf es dfdsgsdgrdsgdrfhbf'),
(2, 'Incepta', 'Mahir Ahmed', '2354880544543', 'mahirrrrrrr@gmail.com', 'Narayangonj,Godnail,2208'),
(3, 'Beximco Pharmacy', 'Tanvir Hasan', '0187439734574', 'ggg@gmail.com', 'Basundhara , Riverview. Dhaka 1212'),
(4, 'square Pharmacitical', 'Tanvir Hasan', '0166559900', 'tannu@gmail.com', 'Dhanmondi 32, Dhaka 1207');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'Pharmacy Sales & Inventory Management System', 'admin@pharmacy.com', '+8801700000000', '', 'Pharmacy Management System for modern store management.');

-- --------------------------------------------------------

--
-- Table structure for table `type_list`
--

CREATE TABLE `type_list` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `type_list`
--

INSERT INTO `type_list` (`id`, `name`, `description`) VALUES
(1, 'Liquid', NULL),
(2, 'Capsule', NULL),
(3, 'Drops', NULL),
(4, 'Inhalers', NULL),
(5, 'Tablet', NULL),
(7, 'Fluid', 'afvsdfgsaed'),
(8, 'Gel', 'Gel fluid Based'),
(9, 'Inhale', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1=admin, 2=cashier, 3=staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`) VALUES
(1, 'Administrator', 'admin', 'admin123', 1),
(2, 'Cashier User', 'cashier', 'cashier123', 2),
(3, 'Staff User', 'staff', 'staff123', 3),
(5, 'Mahir Ahmed', 'manager', '$2y$10$zubIhHsPVzEFRf0QQNB58eEPcaf3Hn.vOykqDtZ2K8EsCDxg4Nd.m', 2),
(7, 'cashier1', 'cashier1', '$2y$10$Ef9PhVc3PBWetWPY3ngP6ulyOtRs.Bpj5RxMYQlhwrpUvtFIthCyi', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_list`
--
ALTER TABLE `customer_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expired_product`
--
ALTER TABLE `expired_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receiving_items`
--
ALTER TABLE `receiving_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receiving_list`
--
ALTER TABLE `receiving_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_list`
--
ALTER TABLE `sales_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_list`
--
ALTER TABLE `supplier_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_list`
--
ALTER TABLE `type_list`
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
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customer_list`
--
ALTER TABLE `customer_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expired_product`
--
ALTER TABLE `expired_product`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `receiving_items`
--
ALTER TABLE `receiving_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `receiving_list`
--
ALTER TABLE `receiving_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales_list`
--
ALTER TABLE `sales_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `supplier_list`
--
ALTER TABLE `supplier_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `type_list`
--
ALTER TABLE `type_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
