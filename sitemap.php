<?php
/**
 * Dynamic XML Sitemap Generator
 * @package PersonalBiography
 */

define('APP_RUNNING', true);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/routes.php';

spl_autoload_register(function (string $className): void {
    $classFile = CLASSES_PATH . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

$db = Database::getInstance();

header('Content-Type: application/xml; charset=utf-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Static pages
$staticPages = ['home', 'about', 'skills', 'experience', 'education', 'portfolio', 'services', 'achievements', 'certificates', 'resume', 'blog', 'gallery', 'testimonials', 'contact', 'privacy', 'terms'];

foreach ($staticPages as $p) {
    $loc = ($p === 'home') ? SITE_URL : SITE_URL . $p;
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "    <changefreq>" . ($p === 'home' || $p === 'blog' ? 'daily' : 'weekly') . "</changefreq>\n";
    echo "    <priority>" . ($p === 'home' ? '1.0' : '0.8') . "</priority>\n";
    echo "  </url>\n";
}

// Blog Posts
try {
    $posts = $db->fetchAll("SELECT slug, updated_at FROM blog WHERE status = 'published' ORDER BY published_at DESC");
    foreach ($posts as $post) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars(SITE_URL . 'blog/' . $post['slug']) . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d', strtotime($post['updated_at'])) . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
} catch (Exception $e) {}

// Portfolio Items
try {
    $projects = $db->fetchAll("SELECT slug, updated_at FROM portfolio WHERE is_active = 1");
    foreach ($projects as $proj) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars(SITE_URL . 'portfolio/' . $proj['slug']) . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d', strtotime($proj['updated_at'])) . "</lastmod>\n";
        echo "    <changefreq>monthly</changefreq>\n";
        echo "    <priority>0.7</priority>\n";
        echo "  </url>\n";
    }
} catch (Exception $e) {}

echo '</urlset>';
