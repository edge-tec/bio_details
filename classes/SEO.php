<?php
/**
 * ============================================
 * SEO Class
 * ============================================
 * 
 * Dynamic SEO meta tags, Open Graph, Twitter Cards,
 * JSON-LD schema markup (Person, Article, Breadcrumb),
 * canonical URLs, and slug generation.
 * 
 * @package PersonalBiography
 */

class SEO
{
    /** @var Database Database instance */
    private Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get SEO data for a specific page
     *
     * @param string $pageSlug
     * @return array
     */
    public function getPageSeo(string $pageSlug): array
    {
        try {
            $seo = $this->db->fetch(
                "SELECT * FROM seo WHERE page_slug = ?",
                [$pageSlug]
            );
            return $seo ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Render meta tags for page head
     *
     * @param array $seoData SEO data from database
     * @param string $page Current page slug
     * @return string HTML meta tags
     */
    public function renderMetaTags(array $seoData, string $page = 'home'): string
    {
        $title = $seoData['meta_title'] ?? SITE_NAME;
        $description = $seoData['meta_description'] ?? SITE_DESCRIPTION;
        $keywords = $seoData['meta_keywords'] ?? '';
        $robots = $seoData['robots'] ?? 'index, follow';
        $ogImage = $seoData['og_image'] ?? '';
        $ogTitle = $seoData['og_title'] ?? $title;
        $ogDescription = $seoData['og_description'] ?? $description;
        $canonical = $seoData['canonical_url'] ?? $this->getCanonicalUrl($page);

        $html = '';
        
        // Basic meta tags
        $html .= '    <meta name="description" content="' . e($description) . '">' . "\n";
        if (!empty($keywords)) {
            $html .= '    <meta name="keywords" content="' . e($keywords) . '">' . "\n";
        }
        $html .= '    <meta name="robots" content="' . e($robots) . '">' . "\n";
        $html .= '    <meta name="author" content="' . e(SITE_NAME) . '">' . "\n";
        
        // Canonical URL
        $html .= '    <link rel="canonical" href="' . e($canonical) . '">' . "\n";
        
        // Open Graph tags
        $html .= '    <meta property="og:type" content="website">' . "\n";
        $html .= '    <meta property="og:title" content="' . e($ogTitle) . '">' . "\n";
        $html .= '    <meta property="og:description" content="' . e($ogDescription) . '">' . "\n";
        $html .= '    <meta property="og:url" content="' . e($canonical) . '">' . "\n";
        $html .= '    <meta property="og:site_name" content="' . e(SITE_NAME) . '">' . "\n";
        $html .= '    <meta property="og:locale" content="en_US">' . "\n";
        if (!empty($ogImage)) {
            $html .= '    <meta property="og:image" content="' . e($ogImage) . '">' . "\n";
            $html .= '    <meta property="og:image:width" content="1200">' . "\n";
            $html .= '    <meta property="og:image:height" content="630">' . "\n";
        }
        
        // Twitter Card tags
        $html .= '    <meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '    <meta name="twitter:title" content="' . e($ogTitle) . '">' . "\n";
        $html .= '    <meta name="twitter:description" content="' . e($ogDescription) . '">' . "\n";
        if (!empty($ogImage)) {
            $html .= '    <meta name="twitter:image" content="' . e($ogImage) . '">' . "\n";
        }

        return $html;
    }

    /**
     * Render JSON-LD Person schema
     *
     * @param array $profile Profile data
     * @param array $socialLinks Social links
     * @return string JSON-LD script tag
     */
    public function renderPersonSchema(array $profile, array $socialLinks = []): string
    {
        $sameAs = array_map(fn($link) => $link['url'], $socialLinks);
        
        $schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'Person',
            'name'       => $profile['full_name'] ?? SITE_NAME,
            'jobTitle'   => $profile['profession'] ?? '',
            'description' => $profile['bio_short'] ?? '',
            'url'        => SITE_URL,
            'email'      => $profile['email'] ?? '',
            'telephone'  => $profile['phone'] ?? '',
        ];
        
        if (!empty($profile['photo'])) {
            $schema['image'] = SITE_URL . 'assets/uploads/' . $profile['photo'];
        }
        
        if (!empty($profile['nationality'])) {
            $schema['nationality'] = ['@type' => 'Country', 'name' => $profile['nationality']];
        }
        
        if (!empty($profile['location'])) {
            $schema['address'] = [
                '@type'           => 'PostalAddress',
                'addressLocality' => $profile['location'],
            ];
        }
        
        if (!empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        return '<script type="application/ld+json">' . "\n" 
             . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
             . "\n" . '</script>';
    }

    /**
     * Render JSON-LD Article schema for blog posts
     *
     * @param array $post Blog post data
     * @param array $profile Author profile
     * @return string JSON-LD script tag
     */
    public function renderArticleSchema(array $post, array $profile = []): string
    {
        $schema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $post['title'],
            'description'   => $post['excerpt'] ?? '',
            'datePublished' => $post['published_at'] ?? $post['created_at'],
            'dateModified'  => $post['updated_at'] ?? $post['created_at'],
            'url'           => SITE_URL . 'blog/' . $post['slug'],
            'wordCount'     => str_word_count(strip_tags($post['content'] ?? '')),
        ];
        
        if (!empty($post['featured_image'])) {
            $schema['image'] = SITE_URL . 'assets/uploads/' . $post['featured_image'];
        }
        
        if (!empty($profile)) {
            $schema['author'] = [
                '@type' => 'Person',
                'name'  => $profile['full_name'] ?? 'Admin',
                'url'   => SITE_URL,
            ];
        }
        
        $schema['publisher'] = [
            '@type' => 'Person',
            'name'  => $profile['full_name'] ?? SITE_NAME,
            'url'   => SITE_URL,
        ];

        return '<script type="application/ld+json">' . "\n" 
             . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
             . "\n" . '</script>';
    }

    /**
     * Render JSON-LD BreadcrumbList schema
     *
     * @param array $breadcrumbs Array of ['name' => '', 'url' => '']
     * @return string JSON-LD script tag
     */
    public function renderBreadcrumbSchema(array $breadcrumbs): string
    {
        $items = [];
        foreach ($breadcrumbs as $index => $crumb) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            ];
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];

        return '<script type="application/ld+json">' . "\n" 
             . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
             . "\n" . '</script>';
    }

    /**
     * Render JSON-LD WebSite schema for sitelinks search box
     *
     * @return string JSON-LD script tag
     */
    public function renderWebsiteSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => SITE_NAME,
            'url'      => SITE_URL,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => SITE_URL . 'blog?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        return '<script type="application/ld+json">' . "\n" 
             . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) 
             . "\n" . '</script>';
    }

    /**
     * Generate canonical URL for a page
     *
     * @param string $page Page slug
     * @return string
     */
    public function getCanonicalUrl(string $page): string
    {
        if ($page === 'home') {
            return rtrim(SITE_URL, '/') . '/';
        }
        return rtrim(SITE_URL, '/') . '/' . $page;
    }

    /**
     * Generate SEO-friendly slug from text
     *
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        // Transliterate
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        // Replace non-alphanumeric with dashes
        $text = preg_replace('/[^a-zA-Z0-9\s-]/', '', $text);
        // Replace spaces and multiple dashes with single dash
        $text = preg_replace('/[\s-]+/', '-', $text);
        // Trim dashes from ends
        $text = trim($text, '-');
        // Lowercase
        return strtolower($text);
    }

    /**
     * Ensure slug is unique in a table
     *
     * @param string $slug Base slug
     * @param string $table Table name
     * @param int|null $excludeId ID to exclude (for updates)
     * @return string Unique slug
     */
    public function uniqueSlug(string $slug, string $table, ?int $excludeId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $where = "slug = ?";
            $params = [$slug];
            
            if ($excludeId !== null) {
                $where .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            if (!$this->db->exists($table, $where, $params)) {
                return $slug;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }
}
