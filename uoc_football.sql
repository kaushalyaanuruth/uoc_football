-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 21, 2025 at 07:35 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uoc_football`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` varchar(12) NOT NULL,
  `event_name` varchar(64) NOT NULL,
  `event_type` varchar(16) NOT NULL,
  `event_description` varchar(512) NOT NULL,
  `event_date` date NOT NULL,
  `image_name` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `image_id` int NOT NULL AUTO_INCREMENT,
  `image_ name` varchar(255) NOT NULL,
  `upload_date` date NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int NOT NULL AUTO_INCREMENT,
  `news_heading` varchar(64) NOT NULL,
  `news_body` varchar(512) NOT NULL,
  `news_date` date NOT NULL,
  `image_name` varchar(64) NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`news_id`, `news_heading`, `news_body`, `news_date`, `image_name`) VALUES
(1, 'New News', 'vksjcksdjvbshfvbvbskj kdwjhvidsjvkivwsi viv;iviv iuvhivdsvsiv ', '2025-10-20', '1760963606_102ef56251d6dc676620fe4a280a306a.jpg'),
(2, 'This is a New Heading', 'This is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news bodyThis is the news body', '2025-10-20', '1760963669_79a4637fac3e7a38bbfd5800d8ae3384.png'),
(3, 'Wade hari', 'Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda Wade goda ', '2025-10-20', '1760964094_518407686_743793255106531_2005150083093162072_n.jpg'),
(4, 'fwfewgerwvg', 'wgrgerwvgrwgbrvrwvgw', '2025-10-20', '1760965048_WhatsApp Image 2025-09-14 at 09.05.55_b7236008.jpg'),
(5, 'gdsgsgdsf', 'gdsdfg sadgsdfg dsgdsgfsdgdsvgs', '2025-10-21', '1761028493_link’s lesson on perseverance. (oc)   #comic #doodle ');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
CREATE TABLE IF NOT EXISTS `players` (
  `player_id` varchar(12) NOT NULL,
  `user_id` varchar(12) NOT NULL,
  `position` varchar(16) NOT NULL,
  `jersy_no` int NOT NULL,
  PRIMARY KEY (`player_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `player_team`
--

DROP TABLE IF EXISTS `player_team`;
CREATE TABLE IF NOT EXISTS `player_team` (
  `player_id` varchar(12) NOT NULL,
  `team_id` varchar(12) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(12) NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(64) NOT NULL,
  `user_type` varchar(12) NOT NULL,
  `pwd` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `user_type`, `pwd`) VALUES
('user_e09b4d3', 'admin', 'dummy@example.com', 'admin', '$2y$10$1mvJ5zrPIotLMQHy1juL5OM5Gt4DTUYIgVh00wKOXJhncyudrhEnu');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

