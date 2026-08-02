-- ============================================
-- Dynamic Personal Biography Website
-- Database Schema
-- ============================================
-- 
-- Run this SQL file to create all required tables
-- and seed initial data.
-- 
-- Database: biography_db
-- Charset: utf8mb4
-- ============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `biography_db` 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE `biography_db`;

-- ============================================
-- TABLE: users (Admin authentication)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
    `avatar` VARCHAR(255) DEFAULT NULL,
    `login_attempts` TINYINT UNSIGNED DEFAULT 0,
    `locked_until` DATETIME DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: profile (Personal information & bio)
-- ============================================
CREATE TABLE IF NOT EXISTS `profile` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `profession` VARCHAR(255) DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `bio_short` TEXT DEFAULT NULL,
    `bio_full` LONGTEXT DEFAULT NULL,
    `birthday` DATE DEFAULT NULL,
    `nationality` VARCHAR(100) DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `languages` VARCHAR(500) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `mission` TEXT DEFAULT NULL,
    `vision` TEXT DEFAULT NULL,
    `goals` TEXT DEFAULT NULL,
    `typing_texts` TEXT DEFAULT NULL COMMENT 'JSON array of typing animation texts',
    `stats_experience_years` INT UNSIGNED DEFAULT 0,
    `stats_projects` INT UNSIGNED DEFAULT 0,
    `stats_clients` INT UNSIGNED DEFAULT 0,
    `stats_awards` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: skills
-- ============================================
CREATE TABLE IF NOT EXISTS `skills` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `percentage` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0-100',
    `category` VARCHAR(100) DEFAULT 'General',
    `icon` VARCHAR(100) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: experience (Work history)
-- ============================================
CREATE TABLE IF NOT EXISTS `experience` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `company` VARCHAR(200) NOT NULL,
    `position` VARCHAR(200) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE DEFAULT NULL COMMENT 'NULL = present',
    `is_current` TINYINT(1) DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `responsibilities` TEXT DEFAULT NULL COMMENT 'JSON array',
    `technologies` VARCHAR(500) DEFAULT NULL,
    `company_logo` VARCHAR(255) DEFAULT NULL,
    `company_url` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`),
    INDEX `idx_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: education
-- ============================================
CREATE TABLE IF NOT EXISTS `education` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `institute` VARCHAR(255) NOT NULL,
    `degree` VARCHAR(255) NOT NULL,
    `field_of_study` VARCHAR(255) DEFAULT NULL,
    `start_year` YEAR DEFAULT NULL,
    `passing_year` YEAR DEFAULT NULL,
    `grade` VARCHAR(100) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `institute_logo` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: portfolio (Projects)
-- ============================================
CREATE TABLE IF NOT EXISTS `portfolio` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `technologies` VARCHAR(500) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT 'Web Development',
    `live_url` VARCHAR(255) DEFAULT NULL,
    `github_url` VARCHAR(255) DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: services
-- ============================================
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT 'bi-gear',
    `features` TEXT DEFAULT NULL COMMENT 'JSON array of features',
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_categories
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` VARCHAR(500) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog (Blog posts)
-- ============================================
CREATE TABLE IF NOT EXISTS `blog` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `featured_image` VARCHAR(255) DEFAULT NULL,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `tags` VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated tags',
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` VARCHAR(500) DEFAULT NULL,
    `meta_keywords` VARCHAR(500) DEFAULT NULL,
    `views` INT UNSIGNED DEFAULT 0,
    `reading_time` SMALLINT UNSIGNED DEFAULT 0 COMMENT 'Minutes',
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `is_featured` TINYINT(1) DEFAULT 0,
    `published_at` DATETIME DEFAULT NULL,
    `author_id` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published` (`published_at`),
    INDEX `idx_featured` (`is_featured`),
    INDEX `idx_category` (`category_id`),
    FULLTEXT `ft_search` (`title`, `content`, `tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: blog_comments
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_comments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blog_id` INT UNSIGNED NOT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `comment` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`blog_id`) REFERENCES `blog`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `blog_comments`(`id`) ON DELETE CASCADE,
    INDEX `idx_blog` (`blog_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: gallery
-- ============================================
CREATE TABLE IF NOT EXISTS `gallery` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `image` VARCHAR(255) NOT NULL,
    `caption` VARCHAR(255) DEFAULT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT 'General',
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_category` (`category`),
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: testimonials
-- ============================================
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `review` TEXT NOT NULL,
    `rating` TINYINT UNSIGNED DEFAULT 5 COMMENT '1-5 stars',
    `company` VARCHAR(200) DEFAULT NULL,
    `position` VARCHAR(200) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: contact_messages
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(50) DEFAULT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_read` (`is_read`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: settings (Key-value site settings)
-- ============================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT DEFAULT NULL,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_key` (`setting_key`),
    INDEX `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: seo (Per-page SEO metadata)
-- ============================================
CREATE TABLE IF NOT EXISTS `seo` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `page_slug` VARCHAR(100) NOT NULL UNIQUE,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` VARCHAR(500) DEFAULT NULL,
    `meta_keywords` VARCHAR(500) DEFAULT NULL,
    `og_title` VARCHAR(255) DEFAULT NULL,
    `og_description` VARCHAR(500) DEFAULT NULL,
    `og_image` VARCHAR(255) DEFAULT NULL,
    `canonical_url` VARCHAR(255) DEFAULT NULL,
    `robots` VARCHAR(100) DEFAULT 'index, follow',
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_page` (`page_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: social_links
-- ============================================
CREATE TABLE IF NOT EXISTS `social_links` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `platform` VARCHAR(50) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `icon` VARCHAR(100) NOT NULL,
    `color` VARCHAR(20) DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: resume
-- ============================================
CREATE TABLE IF NOT EXISTS `resume` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) DEFAULT NULL,
    `file_size` INT UNSIGNED DEFAULT NULL,
    `download_count` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: certificates
-- ============================================
CREATE TABLE IF NOT EXISTS `certificates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `issuer` VARCHAR(200) NOT NULL,
    `issue_date` DATE DEFAULT NULL,
    `expiry_date` DATE DEFAULT NULL,
    `credential_id` VARCHAR(100) DEFAULT NULL,
    `credential_url` VARCHAR(255) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: achievements
-- ============================================
CREATE TABLE IF NOT EXISTS `achievements` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT 'bi-trophy',
    `achievement_date` DATE DEFAULT NULL,
    `sort_order` INT UNSIGNED DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: activity_logs
-- ============================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: newsletter_subscribers
-- ============================================
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `is_active` TINYINT(1) DEFAULT 1,
    `subscribed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME DEFAULT NULL,
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- SEED DATA
-- ============================================

-- Default admin user (password: Admin@123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`) VALUES
('Admin', 'admin@example.com', '$2y$12$LJ6P2Fd1c2v3dZ5C1g7YpOPXlZ7VQ5W0M.Q9Jx4VVmEq1Y9BS6qHa', 'admin', 1);

-- Default profile
INSERT INTO `profile` (`user_id`, `full_name`, `profession`, `bio_short`, `bio_full`, `birthday`, `nationality`, `location`, `languages`, `email`, `phone`, `website`, `mission`, `vision`, `goals`, `typing_texts`, `stats_experience_years`, `stats_projects`, `stats_clients`, `stats_awards`) VALUES
(1, 'John Doe', 'Full Stack Web Developer & Digital Marketer',
'Passionate full-stack web developer with expertise in PHP, JavaScript, and modern web technologies. Building scalable, secure, and high-performance web applications.',
'I am a passionate and dedicated full-stack web developer with over 5 years of experience in building modern, scalable, and high-performance web applications. My expertise spans across PHP, JavaScript, MySQL, and various modern frameworks and tools.\n\nI specialize in creating elegant solutions to complex problems, focusing on clean code architecture, security best practices, and exceptional user experiences. From e-commerce platforms to affiliate marketing systems, I have delivered projects that drive real business results.\n\nMy journey in web development started with a curiosity about how things work on the internet, and it has evolved into a profession I am deeply passionate about. I continuously learn and adopt new technologies to stay at the cutting edge of web development.\n\nBeyond coding, I am also experienced in SEO, digital marketing, and server management, which gives me a holistic understanding of the web ecosystem. I believe in writing code that not only works but is maintainable, scalable, and secure.',
'1995-01-15', 'Bangladeshi', 'Dhaka, Bangladesh', 'Bangla, English, Hindi',
'contact@johndoe.com', '+880 1234 567890', 'https://johndoe.com',
'To deliver innovative web solutions that empower businesses and individuals to achieve their digital goals through technology.',
'To become a leading web technology expert, creating impactful digital products that make a difference in people''s lives.',
'Continue mastering modern web technologies, contribute to open-source projects, mentor aspiring developers, and build products that solve real-world problems.',
'["Full Stack Developer", "PHP Expert", "Digital Marketer", "SEO Specialist", "Tech Enthusiast"]',
5, 120, 85, 15);

-- Default skills
INSERT INTO `skills` (`name`, `percentage`, `category`, `icon`, `sort_order`) VALUES
('PHP', 95, 'Programming', 'bi-filetype-php', 1),
('JavaScript', 88, 'Programming', 'bi-filetype-js', 2),
('HTML5', 97, 'Programming', 'bi-filetype-html', 3),
('CSS3', 92, 'Programming', 'bi-filetype-css', 4),
('MySQL', 90, 'Database', 'bi-database', 5),
('Bootstrap', 94, 'Framework', 'bi-bootstrap', 6),
('Laravel', 80, 'Framework', 'bi-box', 7),
('WordPress', 85, 'CMS', 'bi-wordpress', 8),
('SEO', 88, 'Marketing', 'bi-search', 9),
('Digital Marketing', 82, 'Marketing', 'bi-megaphone', 10),
('Affiliate Marketing', 78, 'Marketing', 'bi-link-45deg', 11),
('UI/UX Design', 80, 'Design', 'bi-palette', 12),
('Server Management', 75, 'DevOps', 'bi-server', 13),
('Docker', 70, 'DevOps', 'bi-box-seam', 14),
('Git', 88, 'DevOps', 'bi-git', 15),
('Cloudflare', 82, 'DevOps', 'bi-cloud', 16),
('API Development', 86, 'Programming', 'bi-plug', 17),
('Python', 72, 'Programming', 'bi-filetype-py', 18);

-- Default experience
INSERT INTO `experience` (`company`, `position`, `start_date`, `end_date`, `is_current`, `description`, `responsibilities`, `technologies`, `sort_order`) VALUES
('Tech Solutions Ltd.', 'Senior Full Stack Developer', '2022-01-01', NULL, 1,
'Leading development of enterprise web applications, managing a team of 5 developers, and architecting scalable solutions for high-traffic platforms.',
'["Lead development of enterprise web applications", "Architect scalable solutions for high-traffic platforms", "Mentor junior developers and conduct code reviews", "Implement CI/CD pipelines and DevOps best practices", "Optimize database performance and application security"]',
'PHP, Laravel, JavaScript, Vue.js, MySQL, Docker, AWS', 1),

('Digital Agency Co.', 'Web Developer', '2019-06-01', '2021-12-31', 0,
'Developed custom web applications, e-commerce platforms, and content management systems for various clients across different industries.',
'["Developed custom web applications and e-commerce platforms", "Built RESTful APIs for mobile and web applications", "Implemented SEO strategies increasing organic traffic by 200%", "Managed hosting environments and server configurations"]',
'PHP, WordPress, WooCommerce, JavaScript, MySQL, cPanel', 2),

('Freelance', 'Freelance Web Developer', '2018-01-01', '2019-05-31', 0,
'Started freelancing career building websites and web applications for small businesses and startups.',
'["Built responsive websites for small businesses", "Developed custom WordPress themes and plugins", "Provided SEO and digital marketing consultation", "Managed client relationships and project timelines"]',
'PHP, HTML, CSS, JavaScript, WordPress, MySQL', 3);

-- Default education
INSERT INTO `education` (`institute`, `degree`, `field_of_study`, `start_year`, `passing_year`, `grade`, `description`, `sort_order`) VALUES
('University of Dhaka', 'Bachelor of Science', 'Computer Science & Engineering', 2014, 2018,
'3.75/4.00 GPA', 'Focused on software engineering, database systems, and web technologies. Completed thesis on web application security.', 1),
('Dhaka College', 'Higher Secondary Certificate (HSC)', 'Science', 2012, 2014,
'GPA 5.00/5.00', 'Completed higher secondary education with distinction in mathematics and physics.', 2),
('Model High School', 'Secondary School Certificate (SSC)', 'Science', 2010, 2012,
'GPA 5.00/5.00', 'Completed secondary education with outstanding results.', 3);

-- Default services
INSERT INTO `services` (`title`, `description`, `icon`, `features`, `sort_order`) VALUES
('Web Development', 'Custom web application development using modern technologies. From simple websites to complex enterprise applications.', 'bi-code-slash',
'["Responsive Web Design", "Custom Web Applications", "Single Page Applications", "Progressive Web Apps", "Performance Optimization"]', 1),
('PHP Development', 'Expert PHP development including custom frameworks, APIs, and backend systems. Clean, secure, and scalable code.', 'bi-filetype-php',
'["Core PHP Applications", "Laravel Development", "REST API Development", "Database Design", "Third-party Integrations"]', 2),
('E-commerce Solutions', 'Complete e-commerce development including payment gateway integration, inventory management, and order processing.', 'bi-cart3',
'["Custom E-commerce Platforms", "WooCommerce Development", "Payment Gateway Integration", "Inventory Management", "Order Processing Systems"]', 3),
('Affiliate Network Development', 'Build custom affiliate marketing platforms with tracking, reporting, and commission management systems.', 'bi-diagram-3',
'["Affiliate Tracking Systems", "Commission Management", "Real-time Analytics", "Fraud Detection", "Publisher/Advertiser Portals"]', 4),
('SEO Optimization', 'Comprehensive SEO services including on-page optimization, technical SEO, and content strategy for better search rankings.', 'bi-search',
'["On-page SEO", "Technical SEO Audit", "Content Strategy", "Keyword Research", "Performance Optimization"]', 5),
('Digital Marketing', 'Full-service digital marketing including social media management, content marketing, and analytics.', 'bi-megaphone',
'["Social Media Marketing", "Content Marketing", "Email Campaigns", "Analytics & Reporting", "Brand Strategy"]', 6),
('API Development', 'Design and develop RESTful APIs with proper documentation, authentication, and rate limiting.', 'bi-plug',
'["RESTful API Design", "API Documentation", "OAuth Authentication", "Rate Limiting", "Webhook Integration"]', 7),
('Website Maintenance', 'Ongoing website maintenance including updates, security patches, backups, and performance monitoring.', 'bi-wrench',
'["Regular Updates", "Security Patches", "Performance Monitoring", "Backup Management", "24/7 Support"]', 8);

-- Default blog categories
INSERT INTO `blog_categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Web Development', 'web-development', 'Articles about web development technologies and best practices.', 1),
('PHP', 'php', 'PHP programming tutorials and tips.', 2),
('JavaScript', 'javascript', 'JavaScript programming and frameworks.', 3),
('SEO', 'seo', 'Search engine optimization strategies and guides.', 4),
('Digital Marketing', 'digital-marketing', 'Digital marketing tips and strategies.', 5),
('Technology', 'technology', 'General technology news and insights.', 6),
('Tutorials', 'tutorials', 'Step-by-step tutorials and guides.', 7);

-- Default blog posts
INSERT INTO `blog` (`title`, `slug`, `content`, `excerpt`, `category_id`, `tags`, `views`, `reading_time`, `status`, `is_featured`, `published_at`, `author_id`) VALUES
('Getting Started with PHP 8 Features', 'getting-started-with-php8-features',
'<h2>Introduction to PHP 8</h2>\n<p>PHP 8 brings numerous improvements and new features that make PHP development more efficient and enjoyable. In this comprehensive guide, we will explore the most impactful features introduced in PHP 8 and how you can leverage them in your projects.</p>\n\n<h3>Named Arguments</h3>\n<p>Named arguments allow you to pass arguments to a function based on the parameter name, rather than the parameter position. This makes code more readable and self-documenting.</p>\n<pre><code class=\"language-php\">// Before PHP 8\nhtmlspecialchars($string, ENT_COMPAT | ENT_HTML401, ''UTF-8'', false);\n\n// PHP 8 with named arguments\nhtmlspecialchars($string, double_encode: false);</code></pre>\n\n<h3>Match Expression</h3>\n<p>The match expression is a more powerful alternative to switch statements. It uses strict comparison and can return values directly.</p>\n\n<h3>Union Types</h3>\n<p>PHP 8 supports union types, allowing you to declare multiple types for parameters, return types, and properties.</p>\n\n<h3>Nullsafe Operator</h3>\n<p>The nullsafe operator (?->) allows you to chain method calls without checking for null at each step.</p>\n\n<p>PHP 8 represents a major step forward for the language, making it more modern, expressive, and performant. Start adopting these features today to write better PHP code.</p>',
'Explore the most impactful features introduced in PHP 8 including named arguments, match expressions, union types, and the nullsafe operator.',
2, 'php, php8, programming, features', 245, 5, 'published', 1, NOW(), 1),

('Building Secure Web Applications with PDO', 'building-secure-web-applications-with-pdo',
'<h2>Why PDO Matters for Security</h2>\n<p>SQL injection remains one of the most common web application vulnerabilities. PDO (PHP Data Objects) provides a clean, consistent interface for database access with built-in support for prepared statements, which are the primary defense against SQL injection attacks.</p>\n\n<h3>Setting Up PDO</h3>\n<p>Creating a PDO connection is straightforward. Always set the error mode to exceptions and disable emulated prepares for maximum security.</p>\n<pre><code class=\"language-php\">$pdo = new PDO(\n    ''mysql:host=localhost;dbname=mydb;charset=utf8mb4'',\n    $username,\n    $password,\n    [\n        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n        PDO::ATTR_EMULATE_PREPARES => false,\n    ]\n);</code></pre>\n\n<h3>Prepared Statements</h3>\n<p>Always use prepared statements for any query that includes user input. Never concatenate user input directly into SQL queries.</p>\n\n<h3>Additional Security Measures</h3>\n<p>Beyond prepared statements, always validate and sanitize input, use htmlspecialchars for output, implement CSRF tokens, and follow the principle of least privilege for database users.</p>',
'Learn how to use PDO prepared statements to build secure PHP applications and prevent SQL injection attacks.',
2, 'php, pdo, security, sql-injection, database', 189, 4, 'published', 0, NOW(), 1),

('SEO Best Practices for Modern Websites', 'seo-best-practices-modern-websites',
'<h2>Modern SEO Strategies</h2>\n<p>Search engine optimization continues to evolve. With search engines becoming more sophisticated and AI-powered, it is essential to adopt modern SEO practices that focus on providing genuine value to users.</p>\n\n<h3>Technical SEO Fundamentals</h3>\n<p>Technical SEO forms the foundation of any successful SEO strategy. This includes proper site structure, fast loading times, mobile responsiveness, and clean URL structures.</p>\n\n<h3>Core Web Vitals</h3>\n<p>Google''s Core Web Vitals (LCP, FID, CLS) are now ranking factors. Optimizing these metrics improves both user experience and search rankings.</p>\n\n<h3>Structured Data & Schema Markup</h3>\n<p>Implementing JSON-LD schema markup helps search engines understand your content better and can result in rich snippets in search results.</p>\n\n<h3>Content Quality</h3>\n<p>High-quality, original content that genuinely helps users remains the most important ranking factor. Focus on E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness).</p>\n\n<p>SEO is a marathon, not a sprint. Focus on building a solid technical foundation and creating valuable content, and the rankings will follow.</p>',
'Discover modern SEO strategies including technical SEO, Core Web Vitals optimization, schema markup, and content quality guidelines.',
4, 'seo, web-development, optimization, google, search', 312, 6, 'published', 1, NOW(), 1);

-- Default testimonials
INSERT INTO `testimonials` (`name`, `review`, `rating`, `company`, `position`, `sort_order`) VALUES
('Sarah Johnson', 'Working with John was an absolute pleasure. He delivered our e-commerce platform ahead of schedule with excellent code quality. His attention to detail and commitment to security best practices gave us complete confidence in the final product.', 5, 'TechStart Inc.', 'CEO', 1),
('Michael Chen', 'John''s expertise in PHP and web development is outstanding. He rebuilt our legacy system into a modern, scalable application that handles 10x our previous traffic without breaking a sweat. Highly recommended!', 5, 'Digital Solutions', 'CTO', 2),
('Emily Rodriguez', 'I hired John for SEO optimization and the results were incredible. Our organic traffic increased by 300% within 6 months. He also redesigned our website with a focus on user experience and performance.', 5, 'GrowthHub', 'Marketing Director', 3),
('David Park', 'John developed our affiliate tracking platform from scratch. The system is robust, scalable, and handles millions of tracking events daily. His understanding of both the technical and business aspects is impressive.', 4, 'AffiliateNet', 'Founder', 4);

-- Default social links
INSERT INTO `social_links` (`platform`, `url`, `icon`, `color`, `sort_order`) VALUES
('GitHub', 'https://github.com/johndoe', 'bi-github', '#333333', 1),
('LinkedIn', 'https://linkedin.com/in/johndoe', 'bi-linkedin', '#0077B5', 2),
('Twitter', 'https://twitter.com/johndoe', 'bi-twitter-x', '#000000', 3),
('Facebook', 'https://facebook.com/johndoe', 'bi-facebook', '#1877F2', 4),
('Instagram', 'https://instagram.com/johndoe', 'bi-instagram', '#E4405F', 5),
('YouTube', 'https://youtube.com/@johndoe', 'bi-youtube', '#FF0000', 6),
('WhatsApp', 'https://wa.me/8801234567890', 'bi-whatsapp', '#25D366', 7),
('Telegram', 'https://t.me/johndoe', 'bi-telegram', '#0088CC', 8);

-- Default certificates
INSERT INTO `certificates` (`title`, `issuer`, `issue_date`, `credential_url`, `description`, `sort_order`) VALUES
('PHP Developer Certification', 'Zend Technologies', '2022-06-15', 'https://example.com/cert/php', 'Certified PHP developer demonstrating expertise in PHP programming, security, and best practices.', 1),
('Google Digital Marketing', 'Google', '2023-03-20', 'https://example.com/cert/google-dm', 'Comprehensive digital marketing certification covering SEO, SEM, social media, and analytics.', 2),
('AWS Cloud Practitioner', 'Amazon Web Services', '2023-08-10', 'https://example.com/cert/aws', 'Foundation-level certification for AWS cloud services and infrastructure.', 3);

-- Default achievements
INSERT INTO `achievements` (`title`, `description`, `icon`, `achievement_date`, `sort_order`) VALUES
('Best Web Developer Award 2023', 'Recognized as the Best Web Developer at the National Tech Awards for innovative contributions to web development.', 'bi-trophy', '2023-12-15', 1),
('Open Source Contributor', 'Active contributor to multiple open-source PHP projects with over 500 commits on GitHub.', 'bi-github', '2023-06-01', 2),
('100+ Projects Delivered', 'Successfully delivered over 100 web development projects for clients across 15 countries.', 'bi-check-circle', '2023-09-01', 3),
('SEO Excellence Award', 'Awarded for achieving top 3 Google rankings for competitive keywords across multiple client projects.', 'bi-search', '2022-11-20', 4);

-- Default settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Dynamic Personal Biography', 'general'),
('site_description', 'Professional Personal Biography & Portfolio Website', 'general'),
('site_logo', '', 'general'),
('site_favicon', '', 'general'),
('footer_text', '© 2024 John Doe. All rights reserved.', 'general'),
('ga_tracking_id', '', 'analytics'),
('gsc_verification', '', 'analytics'),
('contact_email', 'contact@johndoe.com', 'contact'),
('contact_phone', '+880 1234 567890', 'contact'),
('contact_address', 'Dhaka, Bangladesh', 'contact'),
('contact_whatsapp', '+8801234567890', 'contact'),
('contact_telegram', 'johndoe', 'contact'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d233668.38703692693!2d90.27487408963817!3d23.780573258035702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563962f3cff37!2sDhaka!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd', 'contact'),
('newsletter_enabled', '1', 'features'),
('comments_enabled', '1', 'features'),
('cookie_consent_enabled', '1', 'features'),
('maintenance_mode', '0', 'general');

-- Default SEO data for pages
INSERT INTO `seo` (`page_slug`, `meta_title`, `meta_description`, `meta_keywords`, `robots`) VALUES
('home', 'John Doe - Full Stack Web Developer & Digital Marketer', 'Professional full-stack web developer specializing in PHP, JavaScript, and modern web technologies. View my portfolio, skills, and experience.', 'web developer, php developer, full stack developer, portfolio, dhaka', 'index, follow'),
('about', 'About Me - John Doe | Full Stack Developer', 'Learn about my journey as a full-stack web developer, my skills, experience, and passion for building modern web applications.', 'about, biography, web developer, php, javascript', 'index, follow'),
('skills', 'My Skills - John Doe | Technical Expertise', 'Explore my technical skills including PHP, JavaScript, MySQL, Laravel, WordPress, SEO, and more.', 'skills, expertise, php, javascript, mysql, seo', 'index, follow'),
('experience', 'Work Experience - John Doe', 'View my professional work experience in web development, from freelancing to leading development teams.', 'experience, work history, career, web development', 'index, follow'),
('education', 'Education - John Doe', 'My educational background in Computer Science and Engineering.', 'education, degree, computer science, university', 'index, follow'),
('portfolio', 'Portfolio - John Doe | Web Development Projects', 'Browse my portfolio of web development projects including e-commerce platforms, APIs, and custom applications.', 'portfolio, projects, web development, php projects', 'index, follow'),
('services', 'Services - John Doe | Web Development Services', 'Professional web development services including PHP development, e-commerce solutions, SEO, and digital marketing.', 'services, web development, php development, seo services', 'index, follow'),
('blog', 'Blog - John Doe | Web Development & SEO Insights', 'Read articles about web development, PHP programming, SEO strategies, and digital marketing tips.', 'blog, articles, web development, php, seo', 'index, follow'),
('contact', 'Contact Me - John Doe', 'Get in touch with me for web development projects, collaborations, or inquiries.', 'contact, hire, web developer, freelancer', 'index, follow'),
('gallery', 'Gallery - John Doe', 'Photo gallery showcasing my work, events, and professional journey.', 'gallery, photos, events', 'index, follow'),
('testimonials', 'Testimonials - John Doe | Client Reviews', 'Read what clients say about working with me on their web development projects.', 'testimonials, reviews, client feedback', 'index, follow');
