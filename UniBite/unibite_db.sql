-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 01:16 PM
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
-- Database: `unibite_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `allergens`
--

CREATE TABLE `allergens` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `credit_transactions`
--

CREATE TABLE `credit_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `related_request_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credit_transactions`
--

INSERT INTO `credit_transactions` (`id`, `user_id`, `amount`, `reason`, `related_request_id`, `created_at`) VALUES
(1, 2, -1, 'Ποινή μη εμφάνισης', 1, '2026-06-02 10:56:04'),
(2, 1, 2, 'Επιβράβευση από αξιολόγηση (5/5 αστέρια)', 1, '2026-06-02 11:09:27'),
(3, 1, 2, 'Επιβράβευση από αξιολόγηση (5/5 αστέρια)', 1, '2026-06-02 11:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `food_posts`
--

CREATE TABLE `food_posts` (
  `id` int(11) NOT NULL,
  `cook_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `portions_total` int(11) NOT NULL,
  `portions_available` int(11) NOT NULL,
  `pickup_location` varchar(255) NOT NULL,
  `pickup_lat` decimal(10,8) DEFAULT NULL,
  `pickup_lng` decimal(11,8) DEFAULT NULL,
  `pickup_time` datetime NOT NULL,
  `status` enum('active','inactive','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `allergens` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_posts`
--

INSERT INTO `food_posts` (`id`, `cook_id`, `title`, `description`, `photo`, `portions_total`, `portions_available`, `pickup_location`, `pickup_lat`, `pickup_lng`, `pickup_time`, `status`, `created_at`, `allergens`) VALUES
(1, 1, 'Σπιτικό Παστίτσιο (Τροποποιημένο)', 'Έμειναν λιγότερες μερίδες τελικά.', NULL, 4, 2, 'Έξω από την κεντρική Γραμματεία', 38.24660000, 21.73460000, '2026-06-03 15:00:00', 'deleted', '2026-05-22 14:16:51', ''),
(2, 2, 'Ζεστό Παστίτσιο', '', NULL, 3, 3, '', NULL, NULL, '2026-06-02 15:00:00', 'active', '2026-06-02 10:09:30', ''),
(3, 2, 'Ζεστό Παστίτσιο', '', NULL, 3, 3, '', NULL, NULL, '2026-06-02 15:00:00', 'active', '2026-06-02 10:09:34', ''),
(6, 1, 'Σπιτικό Παστίτσιο', 'Φρεσκοψημένο με πλούσια μπεσαμέλ.', 'pastitsio.jpg', 4, 4, '', 38.24660000, 21.73460000, '2026-06-03 14:00:00', 'active', '2026-06-02 10:36:59', ''),
(8, 1, 'Σπιτικό Παστίτσιο', 'Φρεσκοψημένο με πλούσια μπεσαμέλ.', 'pastitsio.jpg', 4, 4, '', 38.24660000, 21.73460000, '2026-06-03 14:00:00', 'active', '2026-06-02 10:44:18', NULL),
(9, 1, 'Σπιτικό Παστίτσιο', 'Φρεσκοψημένο με πλούσια μπεσαμέλ.', 'pastitsio.jpg', 4, 4, '', 38.24660000, 21.73460000, '2026-06-03 14:00:00', 'active', '2026-06-02 10:45:28', 'Γλουτένη, Ίχνη ξηρών καρπών, Λακτόζη');

-- --------------------------------------------------------

--
-- Table structure for table `pickups`
--

CREATE TABLE `pickups` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` enum('picked_up','not_picked_up') NOT NULL,
  `picked_up_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pickups`
--

INSERT INTO `pickups` (`id`, `request_id`, `status`, `picked_up_at`) VALUES
(2, 1, '', '2026-06-02 10:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `post_allergens`
--

CREATE TABLE `post_allergens` (
  `post_id` int(11) NOT NULL,
  `allergen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `request_id`, `consumer_id`, `post_id`, `rating`, `created_at`) VALUES
(5, 1, 2, 8, 5, '2026-06-02 11:09:27'),
(6, 1, 2, 8, 5, '2026-06-02 11:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `post_id`, `consumer_id`, `status`, `requested_at`, `approved_at`) VALUES
(1, 1, 2, 'rejected', '2026-05-25 10:15:17', '2026-05-25 10:14:14'),
(2, 8, 2, 'pending', '2026-06-02 11:03:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `credits` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `credits`) VALUES
(1, 'Γιώργος', 'george@mail.com', '123456', 'student', 11),
(2, 'Μαρία', 'maria@mail.com', '123456', 'student', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allergens`
--
ALTER TABLE `allergens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_credit_user` (`user_id`),
  ADD KEY `fk_credit_request` (`related_request_id`);

--
-- Indexes for table `food_posts`
--
ALTER TABLE `food_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cook_id` (`cook_id`);

--
-- Indexes for table `pickups`
--
ALTER TABLE `pickups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pickups_request` (`request_id`);

--
-- Indexes for table `post_allergens`
--
ALTER TABLE `post_allergens`
  ADD PRIMARY KEY (`post_id`,`allergen_id`),
  ADD KEY `allergen_id` (`allergen_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ratings_request` (`request_id`),
  ADD KEY `fk_ratings_user` (`consumer_id`),
  ADD KEY `fk_ratings_post` (`post_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_requests_post` (`post_id`),
  ADD KEY `fk_requests_user` (`consumer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `allergens`
--
ALTER TABLE `allergens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `food_posts`
--
ALTER TABLE `food_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pickups`
--
ALTER TABLE `pickups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `credit_transactions`
--
ALTER TABLE `credit_transactions`
  ADD CONSTRAINT `fk_credit_request` FOREIGN KEY (`related_request_id`) REFERENCES `requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_credit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `food_posts`
--
ALTER TABLE `food_posts`
  ADD CONSTRAINT `food_posts_ibfk_1` FOREIGN KEY (`cook_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pickups`
--
ALTER TABLE `pickups`
  ADD CONSTRAINT `fk_pickups_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_allergens`
--
ALTER TABLE `post_allergens`
  ADD CONSTRAINT `post_allergens_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `food_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_allergens_ibfk_2` FOREIGN KEY (`allergen_id`) REFERENCES `allergens` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `fk_ratings_post` FOREIGN KEY (`post_id`) REFERENCES `food_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ratings_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ratings_user` FOREIGN KEY (`consumer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_post` FOREIGN KEY (`post_id`) REFERENCES `food_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_requests_user` FOREIGN KEY (`consumer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
