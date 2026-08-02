<?php
/**
 * Admin Header Template
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$user = Auth::getUser();
$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($adminTitle ?? 'Admin Panel'); ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/admin.css'); ?>">
</head>
<body class="admin-body">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Panel
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo SITE_URL; ?>admin/dashboard" class="sidebar-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-grid-fill"></i> Dashboard
                </a>
            </li>
            
            <li class="sidebar-heading">Content Management</li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>admin/profile" class="sidebar-link <?php echo $page === 'profile' ? 'active' : ''; ?>">
                    <i class="bi bi-person-bounding-box"></i> Profile & Bio
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/skills" class="sidebar-link <?php echo $page === 'skills' ? 'active' : ''; ?>">
                    <i class="bi bi-gear"></i> Skills
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/experience" class="sidebar-link <?php echo $page === 'experience' ? 'active' : ''; ?>">
                    <i class="bi bi-briefcase"></i> Experience
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/education" class="sidebar-link <?php echo $page === 'education' ? 'active' : ''; ?>">
                    <i class="bi bi-mortarboard"></i> Education
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/portfolio" class="sidebar-link <?php echo $page === 'portfolio' ? 'active' : ''; ?>">
                    <i class="bi bi-collection"></i> Portfolio
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/services" class="sidebar-link <?php echo $page === 'services' ? 'active' : ''; ?>">
                    <i class="bi bi-tools"></i> Services
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/blog" class="sidebar-link <?php echo $page === 'blog' ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text"></i> Blog Manager
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/gallery" class="sidebar-link <?php echo $page === 'gallery' ? 'active' : ''; ?>">
                    <i class="bi bi-images"></i> Gallery
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/testimonials" class="sidebar-link <?php echo $page === 'testimonials' ? 'active' : ''; ?>">
                    <i class="bi bi-chat-quote"></i> Testimonials
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/certificates" class="sidebar-link <?php echo $page === 'certificates' ? 'active' : ''; ?>">
                    <i class="bi bi-award"></i> Certificates
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/achievements" class="sidebar-link <?php echo $page === 'achievements' ? 'active' : ''; ?>">
                    <i class="bi bi-trophy"></i> Achievements
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/resume" class="sidebar-link <?php echo $page === 'resume' ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-pdf"></i> Resume Upload
                </a>
            </li>
            
            <li class="sidebar-heading">Communications</li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>admin/messages" class="sidebar-link <?php echo $page === 'messages' ? 'active' : ''; ?>">
                    <i class="bi bi-envelope"></i> Messages
                </a>
            </li>
            
            <li class="sidebar-heading">System & Settings</li>
            
            <li>
                <a href="<?php echo SITE_URL; ?>admin/seo" class="sidebar-link <?php echo $page === 'seo' ? 'active' : ''; ?>">
                    <i class="bi bi-search"></i> SEO Manager
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/settings" class="sidebar-link <?php echo $page === 'settings' ? 'active' : ''; ?>">
                    <i class="bi bi-sliders"></i> General Settings
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/backup" class="sidebar-link <?php echo $page === 'backup' ? 'active' : ''; ?>">
                    <i class="bi bi-database-down"></i> DB Backup
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/logs" class="sidebar-link <?php echo $page === 'logs' ? 'active' : ''; ?>">
                    <i class="bi bi-list-check"></i> Activity Logs
                </a>
            </li>
            <li>
                <a href="<?php echo SITE_URL; ?>admin/logout" class="sidebar-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Wrapper -->
    <div class="admin-wrapper">
        <!-- Topbar -->
        <header class="admin-topbar">
            <button type="button" class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            
            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-globe me-1"></i> View Website
                </a>
                
                <button type="button" class="btn-theme-toggle" id="themeToggle">
                    <i class="bi bi-sun-fill theme-icon-light"></i>
                    <i class="bi bi-moon-fill theme-icon-dark"></i>
                </button>
                
                <div class="dropdown">
                    <button class="btn btn-sm btn-glass dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> <?php echo e($user['name'] ?? 'Admin'); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-menu-item dropdown-item" href="<?php echo SITE_URL; ?>admin/profile"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-menu-item dropdown-item text-danger" href="<?php echo SITE_URL; ?>admin/logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="admin-content">
            <?php echo renderFlashMessages(); ?>
