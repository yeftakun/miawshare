-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Sep 15, 2024 at 09:40 AM
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
-- Database: `db_miawshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `level_id` int(11) NOT NULL,
  `level_name` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`level_id`, `level_name`) VALUES
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `liked_post_id` int(11) NOT NULL,
  `liked_user_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`liked_post_id`, `liked_user_name`) VALUES
(41, 'yefta'),
(42, 'admin'),
(42, 'yefta'),
(44, 'yefta'),
(46, 'admin'),
(46, 'yefta'),
(47, 'admin'),
(47, 'yefta'),
(48, 'admin'),
(48, 'yefta'),
(49, 'yefta'),
(50, 'admin'),
(50, 'yefta'),
(54, 'admin'),
(54, 'yefta'),
(56, 'yefta');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `user_name` varchar(30) DEFAULT NULL,
  `otp_code` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `to_use` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_img_path` varchar(200) DEFAULT NULL,
  `post_title` varchar(100) DEFAULT NULL,
  `post_description` varchar(300) DEFAULT NULL,
  `post_link` varchar(300) DEFAULT NULL,
  `classify` varchar(10) DEFAULT NULL,
  `create_in` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `user_id`, `post_img_path`, `post_title`, `post_description`, `post_link`, `classify`, `create_in`) VALUES
(40, 123, 'HitoriGotou.jpg', 'Bocchi Glasses', '#animegirl #kawaii #waifu', '', 'sfw', '2024-07-19 17:28:22'),
(41, 123, 'shiroko.jpg', 'Shiroko', '#animegirl #waifu #kawaii', '', 'sfw', '2024-07-19 17:28:49'),
(42, 123, '4e0f5bb9-dd98-4fe6-83f8-c86d86001b48.jpg', 'CPP for Beginer', '#meme #book', '', 'sfw', '2024-07-19 17:29:23'),
(43, 123, 'Rocking 4K Anime Crew by BinsentoOmosura (1).jpg', 'Kessoku Band Wallpaper', '#desktop #wallpaper', '', 'sfw', '2024-07-19 17:29:58'),
(44, 123, 'Astolfo_reading_ABAP_objects.png', 'Astolfo Reading', '#book #trap', '', 'sfw', '2024-07-19 17:30:28'),
(46, 123, 'sunaookami-shiroko-blue-archive-v0-mnsvdd9qraw91.png', 'Shiroko Winter', 'Pov me, wkwk #shiroko #winter', '', 'sfw', '2024-07-19 17:31:40'),
(47, 123, 'Bocchi_the_rock_Hitori_Gotoh_the_c++_programming_language.png', 'Bocchi Holding CPP Book', '', '', 'sfw', '2024-07-19 17:32:31'),
(48, 123, 'Kawaii Shiroko - pixiv.jpg', 'Shiroko', '#animegirl', '', 'sfw', '2024-07-19 17:33:00'),
(49, 123, 'Bocchi Py.jpg', 'Python Is Bocchi Reference?!', '#anime #python #meme', '', 'sfw', '2024-07-19 17:33:34'),
(50, 123, 'Polite cat.jpeg', 'Beluga Cat', '#beluga #cat', '', 'sfw', '2024-07-19 17:34:02'),
(54, 123, 'FB_IMG_1718369577280.jpg', 'Miaw Cosplay', '', '', 'sfw', '2024-07-21 16:16:13'),
(55, 123, 'download.jpeg', 'Java', '', '', 'sfw', '2024-07-21 16:16:39'),
(56, 123, 'FB_IMG_1721570386958.jpg', 'Jawir?!', '', '', 'sfw', '2024-07-21 16:17:08'),
(79, 127, 'migu gaming.jpeg', 'Migu Gaming', '#miku #gaming', '', 'sfw', '2024-07-27 11:18:09'),
(82, 127, 'Keqing.600.3538467.jpg', 'Keqing', '#game #animegirl', '', 'sfw', '2024-07-27 12:23:47'),
(85, 127, 'animegirl-purple-hair-yellow-eyes-catgirl-city-279171890.jpg', 'Cat Girl', '#animegirl', '', 'sfw', '2024-07-27 12:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `posts_tags`
--

CREATE TABLE `posts_tags` (
  `tag_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `post_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date_liked` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `user_name_reported` varchar(30) DEFAULT NULL,
  `post_id_reported` int(11) DEFAULT NULL,
  `post_reported` varchar(100) DEFAULT NULL,
  `user_name_reporter` varchar(30) DEFAULT NULL,
  `reason` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_title` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(30) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `user_profile_path` varchar(200) DEFAULT NULL,
  `user_bio` varchar(300) DEFAULT NULL,
  `level_id` int(11) DEFAULT NULL,
  `password` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL,
  `create_in` timestamp NOT NULL DEFAULT current_timestamp(),
  `delete_in` timestamp NOT NULL DEFAULT (current_timestamp() + interval 3 minute),
  `tele_chat_id` varchar(11) DEFAULT NULL,
  `to_suspend` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `name`, `user_profile_path`, `user_bio`, `level_id`, `password`, `status`, `create_in`, `delete_in`, `tele_chat_id`, `to_suspend`) VALUES
(87, 'admin', 'Admin', 'admin-1.jpg', 'Ini akun admin', 1, '123', 'Aktif', '2024-06-07 06:39:18', '2024-06-07 06:42:18', '1627790263', 3),
(123, 'yefta', 'Yefta Asyel', 'yefta.jpg', 'Admin', 2, '123', 'Aktif', '2024-06-09 09:58:30', '2024-06-09 10:01:30', '0', 3),
(127, 'bocchi', 'Hitori Gotou', 'bocchi.jpg', 'Guitar Hero🎸', 2, '123', 'Aktif', '2024-07-24 00:02:39', '2024-07-24 00:05:39', '0', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`liked_post_id`,`liked_user_name`),
  ADD KEY `liked_user_name` (`liked_user_name`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_name_2` (`user_name`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD KEY `idx_tag_id` (`tag_id`),
  ADD KEY `idx_post_id` (`post_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_post_id` (`post_id_reported`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_name_2` (`user_name`),
  ADD UNIQUE KEY `user_name_3` (`user_name`),
  ADD KEY `idx_level_id` (`level_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`liked_post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`liked_user_name`) REFERENCES `users` (`user_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `fk_user_name` FOREIGN KEY (`user_name`) REFERENCES `users` (`user_name`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts_tags`
--
ALTER TABLE `posts_tags`
  ADD CONSTRAINT `posts_tags_ibfk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`),
  ADD CONSTRAINT `posts_tags_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_post_id` FOREIGN KEY (`post_id_reported`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `level` (`level_id`);

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `hapus_data_nonaktif` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-06-01 13:43:44' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM users WHERE status = 'Nonaktif' AND delete_in <= NOW();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
