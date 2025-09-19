-- Migration: create users, auth_tokens (remember-me tokens), and login_attempts tables
-- Notes: run this against the intended database (e.g., 'cps'). Requires privileges to create/alter tables.

-- Temporarily disable foreign key checks while creating/altering tables
SET FOREIGN_KEY_CHECKS = 0;

-- 1) Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure users table uses InnoDB and correct collation (will convert if needed)
ALTER TABLE `users` ENGINE = InnoDB, COLLATE = 'utf8mb4_unicode_ci';

-- 2) Auth tokens (remember-me) table
-- token_hash is stored hashed (we never store raw tokens)
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `token_hash` VARCHAR(255) NOT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_auth_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Login attempts table (simple throttle / lockout)
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) DEFAULT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `attempted_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (`email`),
  INDEX (`ip`),
  INDEX (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: Database Schema for Pathways and Assessment System
-- File: migrations/002_create_pathways_assessment_schema.sql

-- Categories table (main pathway categories)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pathways/Majors/Tracks table
CREATE TABLE IF NOT EXISTS `pathways` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `image_url` VARCHAR(500),
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  INDEX (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment questions table
CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pathway_id` INT NOT NULL,
  `question_text` TEXT NOT NULL,
  `difficulty_level` ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`pathway_id`) REFERENCES `pathways`(`id`) ON DELETE CASCADE,
  INDEX (`pathway_id`),
  INDEX (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Question options table
CREATE TABLE IF NOT EXISTS `question_options` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question_id` INT NOT NULL,
  `option_text` TEXT NOT NULL,
  `is_correct` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
  INDEX (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment sessions table
CREATE TABLE IF NOT EXISTS `assessment_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  `status` ENUM('in_progress', 'completed', 'abandoned') DEFAULT 'in_progress',
  `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME NULL,
  `total_questions` INT DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  INDEX (`user_id`),
  INDEX (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment session pathways (selected pathways for comparison)
CREATE TABLE IF NOT EXISTS `assessment_session_pathways` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT NOT NULL,
  `pathway_id` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pathway_id`) REFERENCES `pathways`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_session_pathway` (`session_id`, `pathway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment answers table
CREATE TABLE IF NOT EXISTS `assessment_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `selected_option_id` INT NOT NULL,
  `is_correct` BOOLEAN DEFAULT FALSE,
  `answered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`selected_option_id`) REFERENCES `question_options`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_session_question` (`session_id`, `question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assessment results table
CREATE TABLE IF NOT EXISTS `assessment_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` INT NOT NULL,
  `pathway_id` INT NOT NULL,
  `total_questions` INT NOT NULL DEFAULT 0,
  `correct_answers` INT NOT NULL DEFAULT 0,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pathway_id`) REFERENCES `pathways`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_session_pathway_result` (`session_id`, `pathway_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO `categories` (`name`, `description`) VALUES
('Technology', 'Computer Science, IT, Software Development, and related tech fields'),
('Healthcare', 'Medical, Nursing, Therapy, and health-related professions'),
('Business', 'Management, Marketing, Finance, and business administration'),
('Engineering', 'Civil, Mechanical, Electrical, and other engineering disciplines'),
('Arts & Design', 'Creative fields including graphic design, fine arts, and media');

INSERT INTO `pathways` (`category_id`, `name`, `description`, `image_url`) VALUES
(1, 'Software Development', 'Build applications, websites, and software systems', 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400'),
(1, 'Data Science', 'Analyze data to extract insights and drive decisions', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400'),
(1, 'Cybersecurity', 'Protect systems and data from digital threats', 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=400'),
(2, 'Nursing', 'Provide patient care and medical support', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400'),
(2, 'Physical Therapy', 'Help patients recover and improve mobility', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400'),
(3, 'Digital Marketing', 'Promote products and services online', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400'),
(3, 'Financial Analysis', 'Analyze financial data and investment opportunities', 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400'),
(4, 'Civil Engineering', 'Design and build infrastructure projects', 'https://images.unsplash.com/photo-1541976590-713941681591?w=400'),
(5, 'Graphic Design', 'Create visual communications and designs', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400');

-- Sample questions for Software Development
INSERT INTO `questions` (`pathway_id`, `question_text`, `difficulty_level`) VALUES
(1, 'Which programming language is primarily used for web development?', 'easy'),
(1, 'What does HTML stand for?', 'easy'),
(1, 'Which of the following is a JavaScript framework?', 'medium'),
(1, 'What is the time complexity of binary search?', 'hard'),
(1, 'Which design pattern is used to create objects?', 'medium');

-- Sample options for the first question (Software Development)
INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`) VALUES
(1, 'Python', FALSE),
(1, 'JavaScript', TRUE),
(1, 'C++', FALSE),
(1, 'Java', FALSE),
(2, 'Hyper Text Markup Language', TRUE),
(2, 'High Tech Modern Language', FALSE),
(2, 'Home Tool Markup Language', FALSE),
(2, 'Hyperlink and Text Markup Language', FALSE),
(3, 'React', TRUE),
(3, 'Django', FALSE),
(3, 'Laravel', FALSE),
(3, 'Spring', FALSE),
(4, 'O(n)', FALSE),
(4, 'O(log n)', TRUE),
(4, 'O(n²)', FALSE),
(4, 'O(1)', FALSE),
(5, 'Factory Pattern', TRUE),
(5, 'Observer Pattern', FALSE),
(5, 'Strategy Pattern', FALSE),
(5, 'Decorator Pattern', FALSE);

-- Sample questions for Data Science
INSERT INTO `questions` (`pathway_id`, `question_text`, `difficulty_level`) VALUES
(2, 'Which Python library is commonly used for data manipulation?', 'easy'),
(2, 'What does SQL stand for?', 'easy'),
(2, 'Which algorithm is used for classification?', 'medium'),
(2, 'What is overfitting in machine learning?', 'hard'),
(2, 'Which metric is used to evaluate regression models?', 'medium');

INSERT INTO `question_options` (`question_id`, `option_text`, `is_correct`) VALUES
(6, 'Pandas', TRUE),
(6, 'Flask', FALSE),
(6, 'Django', FALSE),
(6, 'Requests', FALSE),
(7, 'Structured Query Language', TRUE),
(7, 'Simple Query Language', FALSE),
(7, 'Standard Query Language', FALSE),
(7, 'System Query Language', FALSE),
(8, 'Decision Tree', TRUE),
(8, 'Linear Search', FALSE),
(8, 'Binary Search', FALSE),
(8, 'Quick Sort', FALSE),
(9, 'When model performs well on training but poor on test data', TRUE),
(9, 'When model performs poor on both training and test data', FALSE),
(9, 'When model is too simple', FALSE),
(9, 'When model has too few parameters', FALSE),
(10, 'Mean Squared Error', TRUE),
(10, 'Accuracy', FALSE),
(10, 'Precision', FALSE),
(10, 'F1-Score', FALSE);

-- Re-enable FK checks
SET FOREIGN_KEY_CHECKS = 1;