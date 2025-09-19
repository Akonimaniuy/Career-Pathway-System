-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 19, 2025 at 01:14 PM
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
-- Database: `cps`
--

-- --------------------------------------------------------

--
-- Table structure for table `answer_options`
--

DROP TABLE IF EXISTS `answer_options`;
CREATE TABLE IF NOT EXISTS `answer_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_questions`
--

DROP TABLE IF EXISTS `assessment_questions`;
CREATE TABLE IF NOT EXISTS `assessment_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pathway_id` int NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false') NOT NULL DEFAULT 'multiple_choice',
  `difficulty_level` enum('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pathway_id` (`pathway_id`),
  KEY `difficulty_level` (`difficulty_level`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_results`
--

DROP TABLE IF EXISTS `assessment_results`;
CREATE TABLE IF NOT EXISTS `assessment_results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `pathway_id` int NOT NULL,
  `correct_answers` int NOT NULL DEFAULT '0',
  `total_questions` int NOT NULL DEFAULT '0',
  `percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session_pathway` (`session_id`,`pathway_id`),
  KEY `pathway_id` (`pathway_id`),
  KEY `session_id` (`session_id`),
  KEY `percentage` (`percentage` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_sessions`
--

DROP TABLE IF EXISTS `assessment_sessions`;
CREATE TABLE IF NOT EXISTS `assessment_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `category_id` int NOT NULL,
  `selected_pathways` json NOT NULL,
  `status` enum('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
  `current_question_id` int DEFAULT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `current_question_id` (`current_question_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `token_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_auth_tokens_user` (`user_id`),
  KEY `token_hash` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `ip` (`ip`),
  KEY `attempted_at` (`attempted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pathways`
--

DROP TABLE IF EXISTS `pathways`;
CREATE TABLE IF NOT EXISTS `pathways` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `password_hash`, `created_at`) VALUES
(1, 'kimwhat', 'kim@gmail.com', 'user', '$argon2id$v=19$m=131072,t=4,p=2$OHZUYS5hSWdMTzF2M1BEWQ$iigq7BSt01KuLnOOkQbZeB47M3nTuKq8Wy0c9hylB7o', '2025-09-16 20:14:11'),
(2, 'neil ragas', 'neil@gmail.com', 'admin', '$argon2id$v=19$m=131072,t=4,p=2$Lm1VOVh0WWRpeTR2ZFFxUw$nVcHqZCUbfAwvKbvFPVG6OUbCOnS8n0b5YLc9bqI6tU', '2025-09-17 19:42:59'),
(11, 'Admin Neil', 'neiladmin@gmail.com', 'user', '$argon2id$v=19$m=131072,t=4,p=2$MEdjNEc0Qkd4RHJpcXgzTA$c7wTEuUchquvrwQpHmp0aZ56SAQks2Bl61IXmXdkk/4', '2025-09-18 08:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

DROP TABLE IF EXISTS `user_answers`;
CREATE TABLE IF NOT EXISTS `user_answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `session_id` int NOT NULL,
  `question_id` int NOT NULL,
  `selected_option_id` int NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `answered_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `selected_option_id` (`selected_option_id`),
  KEY `session_id` (`session_id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer_options`
--
ALTER TABLE `answer_options`
  ADD CONSTRAINT `answer_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `assessment_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_questions`
--
ALTER TABLE `assessment_questions`
  ADD CONSTRAINT `assessment_questions_ibfk_1` FOREIGN KEY (`pathway_id`) REFERENCES `pathways` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_results`
--
ALTER TABLE `assessment_results`
  ADD CONSTRAINT `assessment_results_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_results_ibfk_2` FOREIGN KEY (`pathway_id`) REFERENCES `pathways` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assessment_sessions`
--
ALTER TABLE `assessment_sessions`
  ADD CONSTRAINT `assessment_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_sessions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assessment_sessions_ibfk_3` FOREIGN KEY (`current_question_id`) REFERENCES `assessment_questions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `fk_auth_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathways`
--
ALTER TABLE `pathways`
  ADD CONSTRAINT `pathways_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `assessment_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_3` FOREIGN KEY (`selected_option_id`) REFERENCES `answer_options` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
