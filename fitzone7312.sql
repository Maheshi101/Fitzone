-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 08:40 AM
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
-- Database: `fitzone7312`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookappointment`
--

CREATE TABLE `bookappointment` (
  `appointmentID` int(12) NOT NULL,
  `userID` int(12) NOT NULL,
  `trainerID` int(12) NOT NULL,
  `sessionType` varchar(255) NOT NULL,
  `appointmentDate` date NOT NULL,
  `appointmentTime` time NOT NULL,
  `appointmentStatus` enum('pending','confirmed','rejected','') NOT NULL,
  `appointmentMsg` varchar(255) NOT NULL,
  `createAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `messageID` int(12) NOT NULL,
  `userID` int(12) NOT NULL,
  `trainerID` int(12) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(500) NOT NULL,
  `reply` varchar(500) NOT NULL,
  `status` enum('Pending','Replied','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notificationbox`
--

CREATE TABLE `notificationbox` (
  `ID` int(12) NOT NULL,
  `titleNB` varchar(255) NOT NULL,
  `messageNB` varchar(255) NOT NULL,
  `addedTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainerID` int(12) NOT NULL,
  `userID` int(12) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `specialities` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainerID`, `userID`, `fullName`, `specialities`) VALUES
(15, 39, 'Ethan Matthew', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(12) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `phoneNumber` int(24) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','staff','admin','') NOT NULL,
  `profileImg` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `phoneNumber`, `email`, `password`, `role`, `profileImg`) VALUES
(37, 'Maheshi', 'Kasundi', 770000000, 'maheshi@kasundi.com', '$2y$10$bw8LNNxsnPKLqOYT6EiGF.aXKgMtAkrf4k1VjdzAenkAwgKtM3zv2', 'customer', ''),
(38, 'James', 'Daniel', 770000000, 'james@daniel.com', '$2y$10$qxaj6/9712cyAY842.YLQOCjUs2VaEpHJmzIE45BPqCphRNvJY4Dm', 'admin', ''),
(39, 'Ethan', 'Matthew', 770000000, 'ethan@mathew.com', '$2y$10$ynKm2rp1GdzjxhtoD9bWGOzMOYBRKQ32F5IxgjkppDJuEca0X8nrW', 'staff', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookappointment`
--
ALTER TABLE `bookappointment`
  ADD PRIMARY KEY (`appointmentID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `trainerID` (`trainerID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`messageID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `trainerID` (`trainerID`);

--
-- Indexes for table `notificationbox`
--
ALTER TABLE `notificationbox`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainerID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookappointment`
--
ALTER TABLE `bookappointment`
  MODIFY `appointmentID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `messageID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `notificationbox`
--
ALTER TABLE `notificationbox`
  MODIFY `ID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainerID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookappointment`
--
ALTER TABLE `bookappointment`
  ADD CONSTRAINT `bookappointment_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookappointment_ibfk_2` FOREIGN KEY (`trainerID`) REFERENCES `trainers` (`trainerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`trainerID`) REFERENCES `trainers` (`trainerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `trainers`
--
ALTER TABLE `trainers`
  ADD CONSTRAINT `trainers_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
