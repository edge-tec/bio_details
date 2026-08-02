<?php
/**
 * ============================================
 * Route Configuration
 * ============================================
 * 
 * Maps URL slugs to their corresponding page files.
 * Add new routes here when creating new pages.
 * 
 * @package PersonalBiography
 */

// Prevent direct access
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    exit('Direct access forbidden.');
}

/**
 * Available routes
 * Format: 'url-slug' => 'page-filename' (without .php)
 */
$routes = [
    // Main pages
    'home'          => 'home',
    'about'         => 'about',
    'skills'        => 'skills',
    'experience'    => 'experience',
    'education'     => 'education',
    'portfolio'     => 'portfolio',
    'projects'      => 'projects',
    'services'      => 'services',
    'achievements'  => 'achievements',
    'certificates'  => 'certificates',
    'resume'        => 'resume',
    'blog'          => 'blog',
    'blog-single'   => 'blog-single',
    'gallery'       => 'gallery',
    'testimonials'  => 'testimonials',
    'contact'       => 'contact',
    
    // Legal pages
    'privacy'       => 'privacy',
    'terms'         => 'terms',
    
    // Error pages
    '404'           => '404',
];

/**
 * Page titles for SEO fallback
 * Used when no dynamic SEO data is available
 */
$pageTitles = [
    'home'          => 'Home',
    'about'         => 'About Me',
    'skills'        => 'My Skills',
    'experience'    => 'Work Experience',
    'education'     => 'Education',
    'portfolio'     => 'Portfolio',
    'projects'      => 'Projects',
    'services'      => 'Services',
    'achievements'  => 'Achievements',
    'certificates'  => 'Certificates',
    'resume'        => 'Resume',
    'blog'          => 'Blog',
    'blog-single'   => 'Blog Post',
    'gallery'       => 'Gallery',
    'testimonials'  => 'Testimonials',
    'contact'       => 'Contact',
    'privacy'       => 'Privacy Policy',
    'terms'         => 'Terms of Service',
    '404'           => 'Page Not Found',
];

/**
 * Navigation menu items
 * Used in header template
 */
$navMenu = [
    ['slug' => 'home',         'label' => 'Home',         'icon' => 'bi-house'],
    ['slug' => 'about',        'label' => 'About',        'icon' => 'bi-person'],
    ['slug' => 'skills',       'label' => 'Skills',       'icon' => 'bi-gear'],
    ['slug' => 'experience',   'label' => 'Experience',   'icon' => 'bi-briefcase'],
    ['slug' => 'education',    'label' => 'Education',    'icon' => 'bi-mortarboard'],
    ['slug' => 'portfolio',    'label' => 'Portfolio',    'icon' => 'bi-collection'],
    ['slug' => 'services',     'label' => 'Services',     'icon' => 'bi-tools'],
    ['slug' => 'blog',         'label' => 'Blog',         'icon' => 'bi-journal-text'],
    ['slug' => 'gallery',      'label' => 'Gallery',      'icon' => 'bi-images'],
    ['slug' => 'testimonials', 'label' => 'Testimonials', 'icon' => 'bi-chat-quote'],
    ['slug' => 'contact',      'label' => 'Contact',      'icon' => 'bi-envelope'],
];
