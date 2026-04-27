/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  janamac31
 * Created: Apr 27, 2026
 */

-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 18, 2026 at 09:39 AM
-- Server version: 5.7.24
-- PHP Version: 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sanad_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `full_name`, `email`, `password`) VALUES
(1, 'Laila Alharbi', 'admin@sanad.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `medicationrequest`
--

CREATE TABLE `medicationrequest` (
  `request_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `medication_name` varchar(100) NOT NULL,
  `priority_level` varchar(20) NOT NULL,
  `request_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `notes` text,
  `prescription_file` varchar(255) NOT NULL,
  `request_date` datetime NOT NULL,
  `city` varchar(50) NOT NULL,
  `zone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `medicationrequest`
--

INSERT INTO `medicationrequest` (`request_id`, `patient_id`, `admin_id`, `medication_name`, `priority_level`, `request_status`, `notes`, `prescription_file`, `request_date`, `city`, `zone`) VALUES
(1, 1, 1, 'Augmentin 625mg', 'High', 'Pending', 'I need this medication as soon as possible because I use it regularly and I am running out.', 'prescription_1024.jpg', '2026-03-27 10:30:00', 'Riyadh', 'North Riyadh'),
(2, 2, 1, 'Panadol Extra', 'Medium', 'Approved', 'This is for severe headache and I need it today if possible.', 'prescription_1025.jpg', '2026-03-26 14:10:00', 'Riyadh', 'East Riyadh'),
(3, 3, 1, 'Ventolin Inhaler', 'High', 'Rejected', 'Needed for asthma treatment. Please help me find it quickly.', 'prescription_1026.jpg', '2026-03-25 09:15:00', 'Riyadh', 'West Riyadh'),
(4, 4, 1, 'Concor 5mg', 'Low', 'Pending', 'This medication is part of my regular treatment. I still have some left but need a refill soon.', 'prescription_1027.jpg', '2026-03-24 16:40:00', 'Riyadh', 'North Riyadh'),
(5, 5, 1, 'Nexium 40mg', 'Medium', 'Approved', 'I need this for stomach treatment and would prefer a nearby pharmacy.', 'prescription_1028.jpg', '2026-03-23 11:20:00', 'Riyadh', 'South Riyadh');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `DOB` date NOT NULL,
  `city` varchar(50) NOT NULL,
  `zone` varchar(50) NOT NULL,
  `account_status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `full_name`, `email`, `password`, `phone_number`, `DOB`, `city`, `zone`, `account_status`) VALUES
(1, 'Sarah Ahmed', 'sarah@example.com', 'sarah123', '0501234567', '2002-05-14', 'Riyadh', 'North Riyadh', 'Active'),
(2, 'Rashed Ali', 'rashed@example.com', 'rashed123', '0502345678', '1998-11-21', 'Riyadh', 'East Riyadh', 'Blocked'),
(3, 'Huda Saad', 'huda@example.com', 'huda123', '0503456789', '2000-08-09', 'Riyadh', 'West Riyadh', 'Active'),
(4, 'Khalid Omar', 'khalid@example.com', 'khalid123', '0504567890', '1995-03-18', 'Riyadh', 'North Riyadh', 'Active'),
(5, 'Reem Faisal', 'reem@example.com', 'reem123', '0505678901', '2001-12-01', 'Riyadh', 'South Riyadh', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

CREATE TABLE `pharmacy` (
  `pharmacy_id` int(11) NOT NULL,
  `pharmacy_name` varchar(100) NOT NULL,
  `license_no` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `zone` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pharmacy`
--

INSERT INTO `pharmacy` (`pharmacy_id`, `pharmacy_name`, `license_no`, `email`, `password`, `phone`, `zone`, `address`, `city`) VALUES
(1, 'Al-Nahdi Pharmacy', 'LIC1001', 'nahdi@sanad.com', 'noor123', '0112345678', 'North Riyadh', 'Al Nakheel District', 'Riyadh'),
(2, 'Al-Dawaa Pharmacy', 'LIC1002', 'dawaa@sanad.com', 'dawaa123', '0113456789', 'East Riyadh, Central Riyadh', 'Al Rawdah District', 'Riyadh'),
(3, 'Whites Pharmacy', 'LIC1003', 'whites@sanad.com', 'care123', '0114567890', 'North Riyadh', 'Al Olaya Street', 'Riyadh'),
(4, 'Shifa Pharmacy', 'LIC1004', 'shifa@sanad.com', 'shifa123', '0115678901', 'South Riyadh', 'Al Aziziyah District', 'Riyadh');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacyoffer`
--

CREATE TABLE `pharmacyoffer` (
  `offer_id` int(11) NOT NULL,
  `offer_status` varchar(20) NOT NULL,
  `message` text,
  `offer_date` datetime NOT NULL,
  `price` decimal(6,2) DEFAULT NULL,
  `request_id` int(11) NOT NULL,
  `pharmacy_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pharmacyoffer`
--

INSERT INTO `pharmacyoffer` (`offer_id`, `offer_status`, `message`, `offer_date`, `price`, `request_id`, `pharmacy_id`) VALUES
(1, 'Pending', 'The medication is available at our branch in Al Rawdah.', '2026-03-26 16:00:00', '28.50', 2, 2),
(2, 'Accepted', 'Available now. You may collect it today before 10 PM.', '2026-03-26 16:20:00', '26.00', 2, 1),
(3, 'Pending', 'The medication is available. Please visit our branch in Al Aziziyah.', '2026-03-23 13:00:00', '35.00', 5, 4),
(4, 'Rejected', 'We cannot provide the medication because it is out of stock.', '2026-03-23 13:15:00', NULL, 5, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `medicationrequest`
--
ALTER TABLE `medicationrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD PRIMARY KEY (`pharmacy_id`),
  ADD UNIQUE KEY `license_no` (`license_no`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pharmacyoffer`
--
ALTER TABLE `pharmacyoffer`
  ADD PRIMARY KEY (`offer_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `medicationrequest`
--
ALTER TABLE `medicationrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `pharmacy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pharmacyoffer`
--
ALTER TABLE `pharmacyoffer`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medicationrequest`
--
ALTER TABLE `medicationrequest`
  ADD CONSTRAINT `medicationrequest_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`),
  ADD CONSTRAINT `medicationrequest_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `pharmacyoffer`
--
ALTER TABLE `pharmacyoffer`
  ADD CONSTRAINT `pharmacyoffer_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `medicationrequest` (`request_id`),
  ADD CONSTRAINT `pharmacyoffer_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`pharmacy_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
