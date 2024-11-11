<?php
require_once __DIR__ . '/../../config/Database.php';

class RssQuery {
    private $db;
    private $config;
    private $baseUrl;
    private $cacheDir;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadConfig();
        $this->baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $this->cacheDir = __DIR__ . '/../../cache/rss';
    }

    private function loadConfig() {
        $this->config = [];
        $result = $this->db->getConnection()->query(
            "SELECT name, value FROM config WHERE name LIKE 'rss_%'"
        );
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = str_replace('rss_', '', $row['name']);
            $this->config[$key] = $row['value'];
        }
    }

    public function generateFeed($type = 'all', $categorySlug = null) {
        $cacheKey = $type . ($categorySlug ? '_' . $categorySlug : '');
        
        if ($this->isCacheValid($cacheKey)) {
            return $this->getCachedFeed($cacheKey);
        }

        $settings = $this->getRssSettings();
        $items = $this->getFeedItems($type, $categorySlug);
        
        $xml = $this->generateFeedXml($settings, $items);
        $this->cacheFeed($xml, $cacheKey);
        
        return $xml;
    }

    private function getRssSettings() {
        $query = "SELECT * FROM rss_settings WHERE is_active = 1 LIMIT 1";
        $result = $this->db->getConnection()->query($query);
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    private function getFeedItems($type, $categorySlug = null) {
        $items = [];
        $excludedCategories = array_filter(explode(',', $this->config['excluded_categories'] ?? ''));
        $excludedStr = $excludedCategories ? 'AND p.category_id NOT IN (' . implode(',', $excludedCategories) . ')' : '';
        
        $query = "SELECT p.*, 
                        u.username as author_name,
                        c.name as category_name,
                        c.slug as category_slug
                 FROM blog_posts p
                 LEFT JOIN users u ON p.author_id = u.id
                 LEFT JOIN menu_categories c ON p.category_id = c.id
                 WHERE p.status = 'published'";

        if ($type === 'news') {
            $query .= " AND p.type = 'news'";
        } elseif ($type === 'articles') {
            $query .= " AND p.type = 'article'";
        }

        if ($categorySlug) {
            $query .= " AND c.slug = :category_slug";
        }

        $query .= " $excludedStr ORDER BY p.published_at DESC LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($query);
        if ($categorySlug) {
            $stmt->bindValue(':category_slug', $categorySlug, SQLITE3_TEXT);
        }
        $stmt->bindValue(':limit', $this->getRssSettings()['items_count'], SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $item = [
                'title' => $row['title'],
                'link' => $this->baseUrl . '/blog/' . $row['slug'],
                'guid' => $this->baseUrl . '/blog/' . $row['slug'],
                'pubDate' => date('r', strtotime($row['published_at'])),
                'description' => $this->config['include_content'] === 'true' ? $row['content'] : $row['excerpt']
            ];

            if ($this->config['include_author'] === 'true') {
                $item['author'] = $row['author_name'];
            }

            if ($this->config['include_categories'] === 'true' && $row['category_name']) {
                $item['category'] = $row['category_name'];
            }

            if ($this->config['include_featured_image'] === 'true' && $row['featured_image']) {
                $item['image'] = $this->baseUrl . $row['featured_image'];
            }

            $items[] = $item;
        }

        return $items;
    }

    private function generateFeedXml($settings, $items) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0"';
        
        // Add standard namespaces
        $xml .= ' xmlns:content="http://purl.org/rss/1.0/modules/content/"';
        $xml .= ' xmlns:dc="http://purl.org/dc/elements/1.1/"';
        $xml .= ' xmlns:atom="http://www.w3.org/2005/Atom"';
        
        // Add custom namespaces
        if (!empty($this->config['custom_namespaces'])) {
            $xml .= ' ' . $this->config['custom_namespaces'];
        }
        
        $xml .= '>' . PHP_EOL;
        $xml .= '<channel>' . PHP_EOL;
        
        // Required channel elements
        $xml .= '  <title>' . htmlspecialchars($settings['feed_title']) . '</title>' . PHP_EOL;
        $xml .= '  <link>' . htmlspecialchars($this->baseUrl) . '</link>' . PHP_EOL;
        $xml .= '  <description>' . htmlspecialchars($settings['feed_description']) . '</description>' . PHP_EOL;
        
        // Optional channel elements
        $xml .= '  <language>' . htmlspecialchars($settings['feed_language']) . '</language>' . PHP_EOL;
        $xml .= '  <lastBuildDate>' . date('r') . '</lastBuildDate>' . PHP_EOL;
        $xml .= '  <generator>DBT Unity RSS Generator</generator>' . PHP_EOL;
        
        // Atom self link
        $xml .= '  <atom:link href="' . htmlspecialchars($this->baseUrl . $_SERVER['REQUEST_URI']) . '" rel="self" type="application/rss+xml" />' . PHP_EOL;
        
        // Custom feed elements
        if (!empty($this->config['custom_elements'])) {
            $xml .= '  ' . $this->config['custom_elements'] . PHP_EOL;
        }
        
        // Feed items
        foreach ($items as $item) {
            $xml .= '  <item>' . PHP_EOL;
            $xml .= '    <title>' . htmlspecialchars($item['title']) . '</title>' . PHP_EOL;
            $xml .= '    <link>' . htmlspecialchars($item['link']) . '</link>' . PHP_EOL;
            $xml .= '    <guid isPermaLink="true">' . htmlspecialchars($item['guid']) . '</guid>' . PHP_EOL;
            $xml .= '    <pubDate>' . $item['pubDate'] . '</pubDate>' . PHP_EOL;
            
            if (isset($item['author'])) {
                $xml .= '    <dc:creator>' . htmlspecialchars($item['author']) . '</dc:creator>' . PHP_EOL;
            }
            
            if (isset($item['category'])) {
                $xml .= '    <category>' . htmlspecialchars($item['category']) . '</category>' . PHP_EOL;
            }
            
            // Description/content
            if ($this->config['include_content'] === 'true') {
                $xml .= '    <content:encoded><![CDATA[';
                if (isset($item['image'])) {
                    $xml .= '<img src="' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['title']) . '" /><br/>';
                }
                $xml .= $item['description'];
                $xml .= ']]></content:encoded>' . PHP_EOL;
                $xml .= '    <description><![CDATA[' . substr(strip_tags($item['description']), 0, 500) . '...]]></description>' . PHP_EOL;
            } else {
                $xml .= '    <description><![CDATA[' . $item['description'] . ']]></description>' . PHP_EOL;
            }
            
            $xml .= '  </item>' . PHP_EOL;
        }
        
        $xml .= '</channel>' . PHP_EOL;
        $xml .= '</rss>';

        return $xml;
    }

    private function isCacheValid($key) {
        $cacheFile = $this->getCacheFilePath($key);
        if (!file_exists($cacheFile)) return false;

        $cacheLifetime = intval($this->config['cache_lifetime'] ?? 3600);
        return (time() - filemtime($cacheFile)) < $cacheLifetime;
    }

    private function getCachedFeed($key) {
        return file_get_contents($this->getCacheFilePath($key));
    }

    private function cacheFeed($content, $key) {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $filepath = $this->getCacheFilePath($key);
        file_put_contents($filepath, $content);

        if ($this->config['compression'] === 'true') {
            $gzFilepath = $filepath . '.gz';
            file_put_contents($gzFilepath, gzencode($content, 9));
        }
    }

    private function getCacheFilePath($key) {
        return $this->cacheDir . '/feed_' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.xml';
    }

    public function getFeedTypes() {
        $types = [];
        $query = "SELECT * FROM rss_feed_types WHERE is_active = 1 ORDER BY name ASC";
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $types[] = $row;
        }
        
        return $types;
    }

    public function updateSettings($data) {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE rss_settings SET 
             feed_title = :title,
             feed_description = :description,
             feed_language = :language,
             items_count = :items_count,
             is_active = :is_active,
             updated_at = CURRENT_TIMESTAMP
             WHERE id = 1"
        );
        
        $stmt->bindValue(':title', $data['feed_title'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['feed_description'], SQLITE3_TEXT);
        $stmt->bindValue(':language', $data['feed_language'], SQLITE3_TEXT);
        $stmt->bindValue(':items_count', $data['items_count'], SQLITE3_INTEGER);
        $stmt->bindValue(':is_active', $data['is_active'], SQLITE3_INTEGER);
        
        return $stmt->execute();
    }

    public function updateFeedType($slug, $data) {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE rss_feed_types SET 
             name = :name,
             description = :description,
             is_active = :is_active
             WHERE slug = :slug"
        );
        
        $stmt->bindValue(':slug', $slug, SQLITE3_TEXT);
        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['description'], SQLITE3_TEXT);
        $stmt->bindValue(':is_active', $data['is_active'], SQLITE3_INTEGER);
        
        return $stmt->execute();
    }

    public function addExclusion($contentId, $contentType, $reason = '') {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO rss_exclusions (content_id, content_type, reason) 
             VALUES (:content_id, :content_type, :reason)"
        );
        
        $stmt->bindValue(':content_id', $contentId, SQLITE3_INTEGER);
        $stmt->bindValue(':content_type', $contentType, SQLITE3_TEXT);
        $stmt->bindValue(':reason', $reason, SQLITE3_TEXT);
        
        return $stmt->execute();
    }

    public function removeExclusion($id) {
        $stmt = $this->db->getConnection()->prepare(
            "DELETE FROM rss_exclusions WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    public function clearCache() {
        $files = glob($this->cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        return true;
    }
}
