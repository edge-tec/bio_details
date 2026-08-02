-- ============================================
-- Dynamic Personal Biography Website
-- Database Schema - Medical Doctor Specialist Edition
-- ============================================
-- 
-- Run this SQL file to create all required tables
-- and seed medical doctor profile & biography data.
-- 
-- Charset: utf8mb4
-- ============================================

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
-- TABLE: portfolio (Medical Cases & Procedures)
-- ============================================
CREATE TABLE IF NOT EXISTS `portfolio` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `short_description` VARCHAR(500) DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `technologies` VARCHAR(500) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT 'Clinical Practice',
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
    `icon` VARCHAR(100) DEFAULT 'bi-heart-pulse',
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
-- TABLE: blog (Medical articles)
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
-- TABLE: testimonials (Patient reviews)
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
-- TABLE: resume (Medical CV)
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
-- TABLE: certificates (Medical Board Certifications)
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
-- TABLE: achievements (Medical Honors & Awards)
-- ============================================
CREATE TABLE IF NOT EXISTS `achievements` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` VARCHAR(100) DEFAULT 'bi-award',
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
-- SEED DATA - MEDICAL DOCTOR SPECIALIST
-- ============================================

-- Default admin user (password: Admin@123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `is_active`) VALUES
('Dr. Admin', 'admin@example.com', '$2y$12$4K0BMKkjHCB3U0IdYCGI3evyZgPRpu61n1gSiZjJfArmOPxkvFgIq', 'admin', 1)
ON DUPLICATE KEY UPDATE `password` = '$2y$12$4K0BMKkjHCB3U0IdYCGI3evyZgPRpu61n1gSiZjJfArmOPxkvFgIq', `login_attempts` = 0, `locked_until` = NULL;

-- Medical Doctor Profile
INSERT INTO `profile` (`user_id`, `full_name`, `profession`, `bio_short`, `bio_full`, `birthday`, `nationality`, `location`, `languages`, `email`, `phone`, `website`, `mission`, `vision`, `goals`, `typing_texts`, `stats_experience_years`, `stats_projects`, `stats_clients`, `stats_awards`) VALUES
(1, 'Dr. Mizanur Rahman', 'Senior Medical Consultant & Surgeon',
'Dedicated Specialist Physician & Surgeon with over 12 years of clinical excellence, committed to patient-centered healthcare, advanced medical research, and surgical precision.',
'Dr. Mizanur Rahman is a highly acclaimed Senior Medical Specialist & Surgeon with over 12 years of dedicated clinical experience in diagnosing, treating, and managing complex medical conditions. He holds an MBBS, FCPS, and MD, representing top-tier post-graduate medical qualifications.\n\nThroughout his career, Dr. Rahman has successfully treated thousands of patients and performed over 1,500 specialized surgical procedures. His clinical philosophy centers on compassionate patient care, evidence-based medicine, and utilizing state-of-the-art diagnostic and surgical technologies.\n\nIn addition to his hospital consultation and surgical practice, Dr. Rahman is actively involved in medical research, academic lecturing, and public health awareness campaigns. He regularly contributes to peer-reviewed international medical journals and speaks at national healthcare conferences.\n\nDr. Rahman believes that effective healthcare combines clinical expertise with deep empathy. Whether performing advanced surgical operations or providing personalized outpatient consultations, his primary goal is ensuring patient safety, comfort, and long-term recovery.',
'1985-06-12', 'Bangladeshi', 'Dhaka, Bangladesh', 'English, Bangla',
'dr.mizanur@example.com', '+880 1711 000000', 'https://eliteali.com',
'To deliver world-class, accessible, and compassionate healthcare solutions through evidence-based medicine and surgical excellence.',
'To be a trusted global leader in medical innovation, patient care, and healthcare education.',
'Expand advanced telemedicine accessibility, conduct groundbreaking clinical research, and mentor the next generation of healthcare professionals.',
'["Senior Specialist Physician", "Laparoscopic Surgeon", "Medical Researcher", "Clinical Consultant", "Healthcare Educator"]',
12, 1500, 8500, 18)
ON DUPLICATE KEY UPDATE 
`full_name` = 'Dr. Mizanur Rahman',
`profession` = 'Senior Medical Consultant & Surgeon',
`bio_short` = 'Dedicated Specialist Physician & Surgeon with over 12 years of clinical excellence, committed to patient-centered healthcare, advanced medical research, and surgical precision.';

-- Medical Skills / Expertise
INSERT INTO `skills` (`name`, `percentage`, `category`, `icon`, `sort_order`) VALUES
('Clinical Diagnosis & Examination', 98, 'Clinical Expertise', 'bi-stethoscope', 1),
('General & Laparoscopic Surgery', 94, 'Surgical Expertise', 'bi-bandaid', 2),
('Patient Consultation & Care', 99, 'Patient Care', 'bi-heart-pulse', 3),
('Emergency & ICU Critical Care', 92, 'Emergency Care', 'bi-hospital', 4),
('Preventive Health & Screening', 95, 'Preventive Medicine', 'bi-shield-check', 5),
('Medical Research & Publications', 88, 'Academic & Research', 'bi-journal-medical', 6),
('Telemedicine & Remote Care', 90, 'Digital Health', 'bi-camera-video', 7),
('Healthcare & Hospital Management', 85, 'Management', 'bi-building-add', 8);

-- Medical Work Experience
INSERT INTO `experience` (`company`, `position`, `start_date`, `end_date`, `is_current`, `description`, `responsibilities`, `technologies`, `sort_order`) VALUES
('Square Hospital / DMC Hospital', 'Senior Consultant & Specialist Surgeon', '2020-01-01', NULL, 1,
'Leading clinical consultations, performing complex surgical operations, and managing ICU critical care units for international-standard patient care.',
'["Conduct specialized outpatient and inpatient medical consultations", "Perform minimally invasive laparoscopic and general surgical procedures", "Lead multidisciplinary clinical teams in intensive care units", "Conduct clinical research and publish in medical journals"]',
'Laparoscopy, Ultrasound, EMR Systems, ICU Monitoring', 1),

('Dhaka Medical College Hospital', 'Associate Professor & Medical Specialist', '2016-03-01', '2019-12-31', 0,
'Taught undergraduate medical students, supervised resident doctors, and managed outpatient clinical departments.',
'["Lectured MBBS students on advanced pathology and clinical medicine", "Supervised junior doctors and post-graduate medical trainees", "Managed emergency room triage and acute patient admissions"]',
'Clinical Pharmacology, Diagnostic Imaging, Pathology', 2),

('National Institute of Cardiovascular Diseases', 'Resident Medical Officer (RMO)', '2012-01-01', '2016-02-28', 0,
'Managed emergency acute care, cardiac monitoring, and post-operative surgical patient recovery.',
'["Managed emergency admissions and acute patient stabilization", "Monitored post-operative cardiac and surgical recovery", "Assisted senior surgeons in major surgical interventions"]',
'ECG, Defibrillation, Patient Triage, Critical Monitoring', 3);

-- Medical Education
INSERT INTO `education` (`institute`, `degree`, `field_of_study`, `start_year`, `passing_year`, `grade`, `description`, `sort_order`) VALUES
('Bangladesh College of Physicians and Surgeons (BCPS)', 'FCPS (Fellowship)', 'General Surgery & Medicine', 2015, 2019,
'Highest Clinical Distinction', 'Specialized fellowship training in advanced clinical diagnosis and surgical management.', 1),
('Bangabandhu Sheikh Mujib Medical University (BSMMU)', 'MD (Doctor of Medicine)', 'Internal Medicine', 2012, 2015,
'First Class Honours', 'Advanced post-graduate degree focusing on internal pathology, therapeutics, and clinical research.', 2),
('Dhaka Medical College (DMC)', 'MBBS', 'Medicine & Surgery', 2005, 2011,
'Distinction in Physiology & Surgery', 'Bachelor of Medicine and Bachelor of Surgery degree with clinical internship training.', 3);

-- Medical Services Offered
INSERT INTO `services` (`title`, `description`, `icon`, `features`, `sort_order`) VALUES
('Specialist Consultation', 'Comprehensive health evaluations, diagnostic examinations, and personalized treatment plans for complex health issues.', 'bi-stethoscope',
'["Complete Physical Examination", "Diagnostic Test Interpretation", "Personalized Treatment Plans", "Chronic Disease Management", "Second Medical Opinions"]', 1),

('Advanced Surgical Procedures', 'State-of-the-art general and laparoscopic surgical operations ensuring high precision and faster recovery times.', 'bi-bandaid',
'["Minimally Invasive Laparoscopy", "Abdominal & General Surgery", "Post-Op Wound Management", "Pre-Surgical Risk Evaluation", "Outpatient Minor Procedures"]', 2),

('Preventive Health Screening', 'Proactive health checkups and diagnostic screening to detect diseases early and promote longevity.', 'bi-heart-pulse',
'["Full Body Health Checkup", "Cardiovascular Risk Assessment", "Diabetic & Metabolic Screening", "Cancer Screening & Risk Management", "Lifestyle & Nutrition Counseling"]', 3),

('Emergency & Critical Care', 'Immediate emergency medical response and critical care stabilization for acute life-threatening conditions.', 'bi-hospital',
'["Acute Triage & Stabilization", "ICU & Critical Care Management", "Emergency Trauma Care", "Vital Function Monitoring"]', 4),

('Telemedicine Consultation', 'Convenient online video consultation and digital prescription services for remote patients.', 'bi-camera-video',
'["HD Video Consultation", "Digital Prescription Delivery", "Lab Report Review", "Follow-up Care Online"]', 5),

('Post-Operative Rehabilitation', 'Comprehensive post-surgical care and physical rehabilitation guidance to ensure complete recovery.', 'bi-activity',
'["Surgical Wound Management", "Pain Management Protocols", "Rehabilitation Exercise Plans", "Follow-up Monitoring"]', 6);

-- Medical Blog Categories
INSERT INTO `blog_categories` (`name`, `slug`, `description`, `sort_order`) VALUES
('Health & Wellness', 'health-wellness', 'Tips and guidelines for maintaining a healthy lifestyle.', 1),
('Preventive Care', 'preventive-care', 'Disease prevention strategies and early diagnostic screening.', 2),
('Medical Advances', 'medical-advances', 'Latest breakthroughs in surgical techniques and pharmaceuticals.', 3),
('Patient Education', 'patient-education', 'Clear medical explanations for common health conditions.', 4);

-- Medical Blog Articles
INSERT INTO `blog` (`title`, `slug`, `content`, `excerpt`, `category_id`, `tags`, `views`, `reading_time`, `status`, `is_featured`, `published_at`, `author_id`) VALUES
('Essential Health Checkups Every Adult Should Have', 'essential-health-checkups-every-adult-should-have',
'<h2>Why Regular Health Checkups Matter</h2>\n<p>Preventive medicine is the cornerstone of a long, healthy life. Many chronic conditions, such as hypertension, diabetes, and early-stage cardiovascular issues, develop silently without obvious symptoms.</p>\n\n<h3>1. Cardiovascular & Blood Pressure Screening</h3>\n<p>High blood pressure is often called the silent killer. Adults over 25 should have their blood pressure checked at least once a year, or more frequently if risk factors exist.</p>\n\n<h3>2. Diabetes & Blood Sugar Testing</h3>\n<p>Fasting blood glucose and HbA1c tests evaluate how your body processes sugar. Early detection of prediabetes allows for lifestyle interventions that can prevent full-blown Type 2 diabetes.</p>\n\n<h3>3. Lipid Profile (Cholesterol Panel)</h3>\n<p>Evaluating HDL, LDL, and triglycerides provides critical insights into your arterial health and reduces heart attack and stroke risks.</p>\n\n<p>Consulting your doctor annually for routine screening ensures early diagnosis when treatment is most effective.</p>',
'Learn about the crucial routine medical health checkups and diagnostic tests recommended for adults to prevent chronic illness.',
2, 'health, screening, preventive care, wellness', 420, 5, 'published', 1, NOW(), 1),

('Understanding Laparoscopic vs Open Surgery', 'understanding-laparoscopic-vs-open-surgery',
'<h2>The Evolution of Modern Surgery</h2>\n<p>Surgical techniques have evolved dramatically over the past two decades. Laparoscopic (minimally invasive) surgery has transformed how surgeons approach abdominal procedures.</p>\n\n<h3>What is Laparoscopic Surgery?</h3>\n<p>Instead of making a single large incision, laparoscopic surgery utilizes tiny keyhole incisions (5-10mm) through which a specialized camera (laparoscope) and miniaturized instruments are inserted.</p>\n\n<h3>Key Patient Benefits</h3>\n<ul>\n<li>Significantly reduced post-operative pain</li>\n<li>Shorter hospital stay (often discharged within 24-48 hours)</li>\n<li>Minimal scarring and smaller surgical wounds</li>\n<li>Faster return to work and daily activities</li>\n</ul>\n\n<p>While not every condition is suitable for laparoscopy, it remains the gold standard for gallbladder, hernia, and appendix procedures today.</p>',
'An informative guide explaining the advantages, recovery times, and differences between laparoscopic keyhole surgery and traditional open surgery.',
3, 'surgery, laparoscopy, medical advances, patient guide', 350, 4, 'published', 1, NOW(), 1);

-- Patient Reviews
INSERT INTO `testimonials` (`name`, `review`, `rating`, `company`, `position`, `sort_order`) VALUES
('Rahim Chowdhury', 'Dr. Mizanur Rahman is an exceptional surgeon and physician. He performed my laparoscopic surgery with extreme precision. I felt minimal pain and recovered within three days. His polite demeanor and explanation put my family at ease.', 5, 'Dhaka', 'Patient', 1),
('Dr. Nusrat Jahan', 'As a colleague in the medical field, I highly admire Dr. Rahman''s clinical diagnostic accuracy and surgical expertise. He is one of the most reliable and compassionate specialists I know.', 5, 'Square Hospital', 'Consultant', 2),
('Tanvir Ahmed', 'The online consultation with Dr. Mizanur was smooth and very helpful. He thoroughly reviewed my medical history and reports, giving me clear medical advice that brought quick relief.', 5, 'Chittagong', 'Patient', 3);

-- Medical Certifications
INSERT INTO `certificates` (`title`, `issuer`, `issue_date`, `credential_url`, `description`, `sort_order`) VALUES
('Board Certified Specialist in Surgery (FCPS)', 'Bangladesh College of Physicians & Surgeons', '2019-01-10', 'https://bcps.edu.bd', 'Highest national professional fellowship credential in clinical medicine & surgery.', 1),
('Advanced Cardiovascular Life Support (ACLS)', 'American Heart Association (AHA)', '2022-05-15', 'https://heart.org', 'International certification in emergency resuscitation and acute cardiovascular care.', 2),
('Certificate in Minimally Invasive Laparoscopic Surgery', 'World Association of Laparoscopic Surgeons', '2021-09-20', 'https://wals.org', 'Advanced surgical training certification in minimally invasive keyhole procedures.', 3);

-- Doctor Achievements
INSERT INTO `achievements` (`title`, `description`, `icon`, `achievement_date`, `sort_order`) VALUES
('Excellence in Clinical Medicine Award 2023', 'Awarded for outstanding clinical care and high success rate in surgical procedures.', 'bi-award', '2023-12-10', 1),
('Published 15+ Medical Research Papers', 'Authored peer-reviewed research papers in indexed national and international medical journals.', 'bi-journal-medical', '2023-08-15', 2),
('Over 1,500 Successful Surgical Operations', 'Achieved a landmark milestone of performing over 1,500 specialized surgical procedures.', 'bi-check2-circle', '2023-05-01', 3);

-- Site Settings
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'Dr. Mizanur Rahman - Medical Consultant & Surgeon', 'general'),
('site_description', 'Official Biography & Clinical Consultation Website of Dr. Mizanur Rahman, MBBS, FCPS, MD.', 'general'),
('site_logo', '', 'general'),
('site_favicon', '', 'general'),
('footer_text', '© 2024 Dr. Mizanur Rahman. All rights reserved.', 'general'),
('ga_tracking_id', '', 'analytics'),
('gsc_verification', '', 'analytics'),
('contact_email', 'dr.mizanur@example.com', 'contact'),
('contact_phone', '+880 1711 000000', 'contact'),
('contact_address', 'Chamber: Square Hospital, West Panthapath, Dhaka', 'contact'),
('contact_whatsapp', '+8801711000000', 'contact'),
('contact_telegram', 'drmizanur', 'contact'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301384!2d90.38072437597143!3d23.75085808875569!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563962f3cff37!2sDhaka!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd', 'contact'),
('newsletter_enabled', '1', 'features'),
('comments_enabled', '1', 'features'),
('cookie_consent_enabled', '1', 'features'),
('maintenance_mode', '0', 'general')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- SEO Data
INSERT INTO `seo` (`page_slug`, `meta_title`, `meta_description`, `meta_keywords`, `robots`) VALUES
('home', 'Dr. Mizanur Rahman - Specialist Physician & Surgeon in Dhaka', 'Official website of Dr. Mizanur Rahman, Senior Consultant Specialist & Surgeon. Book appointments, view medical qualifications, and read patient reviews.', 'doctor, surgeon, medical specialist, consultation, dhaka, fcps, md', 'index, follow'),
('about', 'About Dr. Mizanur Rahman | MBBS, FCPS, MD', 'Learn about Dr. Mizanur Rahman medical background, fellowship credentials, clinical philosophy, and surgical experience.', 'about doctor, medical specialist, fcps surgeon, dmc, bsmmu', 'index, follow'),
('skills', 'Medical Expertise - Dr. Mizanur Rahman', 'Specialized clinical expertise including laparoscopic surgery, emergency critical care, preventive medicine, and diagnostic medicine.', 'medical skills, surgery, clinical diagnosis, critical care', 'index, follow'),
('experience', 'Clinical Experience & Hospital Appointments - Dr. Mizanur Rahman', 'View Dr. Mizanur Rahman hospital appointments, academic lectureships, and surgical career timeline.', 'doctor experience, hospital appointment, consultant surgeon', 'index, follow'),
('education', 'Medical Education & Qualifications - Dr. Mizanur Rahman', 'Degrees and post-graduate medical qualifications: MBBS (DMC), MD (BSMMU), FCPS (BCPS).', 'mbbs, fcps, md, medical degree, dmc, bcps', 'index, follow'),
('portfolio', 'Clinical Cases & Procedures - Dr. Mizanur Rahman', 'Overview of specialized surgical operations, clinical case studies, and treatment methodologies.', 'surgical cases, clinical operations, medical procedures', 'index, follow'),
('services', 'Medical Services & Consultation - Dr. Mizanur Rahman', 'Specialist consultation, laparoscopic surgery, health screening, critical care, and telemedicine services.', 'doctor appointment, surgery, telemedicine, health checkup', 'index, follow'),
('blog', 'Health Blog - Dr. Mizanur Rahman | Medical Tips & Guides', 'Read medical articles, health tips, disease prevention guidelines, and surgical guides.', 'health blog, medical tips, doctor blog, disease prevention', 'index, follow'),
('contact', 'Book Appointment / Contact Dr. Mizanur Rahman', 'Schedule a hospital consultation or telemedicine appointment with Dr. Mizanur Rahman.', 'doctor appointment, chamber location, contact doctor, dhaka', 'index, follow')
ON DUPLICATE KEY UPDATE `meta_title` = VALUES(`meta_title`), `meta_description` = VALUES(`meta_description`);
