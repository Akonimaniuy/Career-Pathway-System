-- Migration to align the 'pathways' table with the 'CareerPathModel'

-- Rename the table from 'pathways' to 'career_paths'
ALTER TABLE `pathways` RENAME `career_paths`;

-- Add columns that are used in the CareerPathController and UserModel
ALTER TABLE `career_paths`
  ADD COLUMN `title` VARCHAR(255) NOT NULL AFTER `category_id`,
  ADD COLUMN `status` ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft' AFTER `image_url`,
  ADD COLUMN `views` INT NOT NULL DEFAULT 0 AFTER `status`,
  ADD COLUMN `requirements` TEXT NULL AFTER `description`,
  ADD COLUMN `salary_range` VARCHAR(100) NULL AFTER `requirements`,
  ADD COLUMN `growth_potential` TEXT NULL AFTER `salary_range`,
  ADD COLUMN `industry` VARCHAR(100) NULL AFTER `growth_potential`,
  ADD COLUMN `experience_level` VARCHAR(50) NULL AFTER `industry`,
  ADD COLUMN `remote_friendly` TINYINT(1) NOT NULL DEFAULT 0 AFTER `experience_level`,
  ADD COLUMN `skills_needed` JSON NULL AFTER `remote_friendly`,
  ADD COLUMN `created_by` INT NULL AFTER `skills_needed`;

-- Copy 'name' to 'title' for existing records and then drop the 'name' column
UPDATE `career_paths` SET `title` = `name`;
ALTER TABLE `career_paths` DROP COLUMN `name`;