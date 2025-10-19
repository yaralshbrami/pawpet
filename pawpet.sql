-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 04:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP DATABASE IF EXISTS pawpet;
CREATE DATABASE pawpet;

DROP USER IF EXISTS 'remote_user'@'%';
CREATE USER 'remote_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON pawpet.* TO 'remote_user'@'%';
FLUSH PRIVILEGES;

USE pawpet;

-- --------------------------------------------------------

--
-- Table structure for table `animals`
--

CREATE TABLE `animals` (
  `animal_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `age` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `animals`
--

INSERT INTO `animals` (`animal_id`, `name`, `type`, `age`, `gender`, `description`, `image_url`, `user_id`) VALUES
(5, 'hiso', 'cat', '3 years', 'Male', 'cute boy', 'uploads/animal_1746888610_2766.jpg', 3),
(10, 'Gameila', 'Giraffe', '4', 'Female', 'The giraffe is the tallest living land animal in the world. They are long-necked and hoofed mammals with a coat of irregular square-shaped brown patches on light brown to beige fur - no two giraffes have the same pattern!\r\n', 'uploads/animal_1747825284_2264.jpg', 5),
(18, 'Tom', 'Cat', '1', 'Male', 'Thomas Jasper Cat Sr. also known simply as Tom, is an American character and one of the two titular main protagonists', 'uploads/animal_1747913521_1287.jpg', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`) VALUES
(1, 'sun', 'ranimr09@gmail.com', '$2y$10$nJLeHCtBnd51xC.0I3vUt.BXzBPNYO11oiRZt5kSwB08IMbbUgKwe', '123456789'),
(2, 'bobo', 'sla@gmail.com', '$2y$10$1nu9RPuDTdwHxcAQQwfRL.BkTCemSHSKqwO.9F0xdlBwjxaphHTE.', '0'),
(3, 'hhhd', 'dgsgsg@gmail.com', '$2y$10$B7B1cWo3/KZNZwcbF.hJ/eexN2gJeJZHEYn4jtk1TlOzeu4dG64cO', '999929484'),
(4, 'moh mhffd', 'dgsgsgsf@gmail.com', '$2y$10$CICYG0XUTJH.P7M7SSXl/e9T/tRW/v9MSFZWz1J88y9EnSH4dMV0S', '999929484'),
(5, 'Sara', 'sara@mail.com', '$2y$10$DQlKNfZ2wFYoQyxwIyaQUOvPlh8EvZWynX9HD/ppwdAstUzebAKJ.', '+201120117666'),
(6, 'asdfsa', 'asdfsa@asd.com', '$2y$10$JcxpLMnH5qw.7IqsFXWLEe04aLLU9AzIJUsO412nF33eNxt5tCJQy', '1234565432');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`animal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animals`
--
ALTER TABLE `animals`
  MODIFY `animal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
