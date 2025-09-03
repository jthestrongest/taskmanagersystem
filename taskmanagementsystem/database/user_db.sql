-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 01:28 PM
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
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_to` int(11) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `completion_attachment` varchar(255) DEFAULT NULL,
  `progress` int(11) DEFAULT 0,
  `estimated_hours` float DEFAULT NULL,
  `actual_hours` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `due_date`, `status`, `priority`, `created_at`, `assigned_to`, `attachment`, `completion_attachment`, `progress`, `estimated_hours`, `actual_hours`) VALUES
(45, 41, 'system', 'make a system', '2025-01-20', 'completed', 'medium', '2025-05-23 08:05:48', 39, '68302c5c03b17.jpg', '68302ec35f4e1_completion.jpg', 0, NULL, NULL),
(46, 43, 'system2', 'ok ka', '0213-03-12', 'completed', 'high', '2025-08-26 02:09:54', 44, '', '68ad1796ed4c4_completion.jpg', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `name`, `email`, `password`, `user_type`) VALUES
(31, 'jhon', 'abdul@gmail.com', '$2y$10$BRvsEOCvjWFID1mwc.QKOurHvm2i05Ti1w6WA0XnLzB5BbI9IFp1u', 'admin'),
(39, 'froilan', 'froilan@gmail.com', '$2y$10$5fk42ycU9ksavPjiArCoUuUC9DxcH9Mm960Y9ujGLOprfD47MUYpK', 'user'),
(43, 'thegoat', 'gabales@gmail.com', '$2y$10$l2UB8TswMOTqZvYm.OAPtOFZjDR8BlHayukFNHwkNhntYTJYC4WL.', 'admin'),
(44, 'lac2', 'lab2@gmail.com', '$2y$10$HJ.z30mc2W9gJWTplA0LRuytPRFGxpmU4Zq/jTWP7TEANzni3vst2', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `user_form` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
