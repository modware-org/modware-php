<?php
require_once __DIR__ . '/../../config/Database.php';

class SitemapQuery {
    private $db;
    private $config;
    private $baseUrl;
    private $cacheDir;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadConfig();
        $this->baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $this->cacheDir = __DIR__ . '/../../cache/sitemap';
    }

    private function loadConfig() {
        $this->config = [];
        $result = $this->db->getConnection()->query(
            "SELECT name, value FROM config WHERE name LIKE 'sitemap_%'"
        );
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $key = str_replace('sitemap_', '', $row['name']);
            $this->config[$key] = $row['value'];
        }
    }

    public function generateSitemap() {
        if ($this->isCacheValid()) {
            return $this->getCachedSitemap();
        }

        $urls = [];
        $urls = array_merge($urls, $this->getHomeUrls());
        $urls = array_merge($urls, $this->getBlogUrls());
        $urls = array_merge($urls, $this->getCategoryUrls());
        $urls = array_merge($urls, $this->getTagUrls());
        $urls = array_merge($urls, $this->getPageUrls());
        $urls = array_merge($urls, $this->getAdditionalUrls());

        // Remove excluded URLs
        $urls = $this->filterExcludedUrls($urls);

        // Split into multiple sitemaps if needed
        $maxUrls = intval($this->config['max_urls'] ?? 50000);
        if (count($urls) > $maxUrls) {
            return $this->generateSitemapIndex($urls, $maxUrls);
        }

        $xml = $this->generateSitemapXml($urls);
        $this->cacheSitemap($xml);
        
        if ($this->config['auto_ping'] === 'true') {
            $this->pingSearchEngines();
        }

        return $xml;
    }

    private function getHomeUrls() {
        $settings = $this->getSitemapSettings('home');
        if (!$settings['is_active']) return [];

        return [[
            'loc' => $this->baseUrl,
            'lastmod' => date('c'),
            'changefreq' => $settings['changefreq'],
            'priority' => $settings['priority']
        ]];
    }

    private function getBlogUrls() {
        $urls = [];
        $settings = $this->getSitemapSettings('blog_posts');
        if (!$settings['is_active']) return $urls;

        $query = "SELECT slug, updated_at FROM blog_posts WHERE status = 'published'";
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $urls[] = [
                'loc' => $this->baseUrl . '/blog/' . $row['slug'],
                'lastmod' => date('c', strtotime($row['updated_at'])),
                'changefreq' => $settings['changefreq'],
                'priority' => $settings['priority']
            ];
        }

        return $urls;
    }

    private function getCategoryUrls() {
        $urls = [];
        $settings = $this->getSitemapSettings('blog_categories');
        if (!$settings['is_active']) return $urls;

        $excludedCategories = array_filter(explode(',', $this->config['excluded_categories'] ?? ''));
        $excludedStr = $excludedCategories ? 'AND id NOT IN (' . implode(',', $excludedCategories) . ')' : '';

        $query = "SELECT slug FROM menu_categories WHERE is_active = 1 $excludedStr";
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $urls[] = [
                'loc' => $this->baseUrl . '/blog/category/' . $row['slug'],
                'changefreq' => $settings['changefreq'],
                'priority' => $settings['priority']
            ];
        }

        return $urls;
    }

    private function getTagUrls() {
        $urls = [];
        $settings = $this->getSitemapSettings('blog_tags');
        if (!$settings['is_active']) return $urls;

        $query = "SELECT slug FROM tags";
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $urls[] = [
                'loc' => $this->baseUrl . '/blog/tag/' . $row['slug'],
                'changefreq' => $settings['changefreq'],
                'priority' => $settings['priority']
            ];
        }

        return $urls;
    }

    private function getPageUrls() {
        $urls = [];
        $settings = $this->getSitemapSettings('pages');
        if (!$settings['is_active']) return $urls;

        $query = "SELECT slug, updated_at FROM content WHERE type = 'page' AND status = 'published'";
        $result = $this->db->getConnection()->query($query);
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $urls[] = [
                'loc' => $this->baseUrl . '/' . $row['slug'],
                'lastmod' => date('c', strtotime($row['updated_at'])),
                'changefreq' => $settings['changefreq'],
                'priority' => $settings['priority']
            ];
        }

        return $urls;
    }

    private function getAdditionalUrls() {
        $urls = [];
        $additionalUrls = array_filter(explode("\n", $this->config['additional_urls'] ?? ''));
        
        foreach ($additionalUrls as $url) {
            $urls[] = [
                'loc' => trim($url),
                'changefreq' => 'monthly',
                'priority' => '0.5'
            ];
        }

        return $urls;
    }

    private function getSitemapSettings($type) {
        $query = "SELECT * FROM sitemap_settings WHERE type = :type LIMIT 1";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        return $result->fetchArray(SQLITE3_ASSOC) ?: [
            'is_active' => true,
            'changefreq' => 'weekly',
            'priority' => 0.5
        ];
    }

    private function filterExcludedUrls($urls) {
        $query = "SELECT url FROM sitemap_exclusions";
        $result = $this->db->getConnection()->query($query);
        $excluded = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $excluded[] = $row['url'];
        }

        return array_filter($urls, function($url) use ($excluded) {
            return !in_array($url['loc'], $excluded);
        });
    }

    private function generateSitemapXml($urls) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        
        if ($this->config['include_images'] === 'true') {
            $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
        }
        
        $xml .= '>' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            
            if ($this->config['include_last_mod'] === 'true' && isset($url['lastmod'])) {
                $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            }
            
            if (isset($url['changefreq'])) {
                $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            }
            
            if (isset($url['priority'])) {
                $xml .= '    <priority>' . number_format($url['priority'], 1) . '</priority>' . PHP_EOL;
            }
            
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    private function generateSitemapIndex($urls, $maxUrls) {
        $sitemaps = array_chunk($urls, $maxUrls);
        $index = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $index .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($sitemaps as $i => $sitemap) {
            $filename = 'sitemap-' . ($i + 1) . '.xml';
            $xml = $this->generateSitemapXml($sitemap);
            $this->cacheSitemap($xml, $filename);

            $index .= '  <sitemap>' . PHP_EOL;
            $index .= '    <loc>' . $this->baseUrl . '/sitemap/' . $filename . '</loc>' . PHP_EOL;
            $index .= '    <lastmod>' . date('c') . '</lastmod>' . PHP_EOL;
            $index .= '  </sitemap>' . PHP_EOL;
        }

        $index .= '</sitemapindex>';
        $this->cacheSitemap($index, 'sitemap.xml');

        return $index;
    }

    private function isCacheValid() {
        $cacheFile = $this->cacheDir . '/sitemap.xml';
        if (!file_exists($cacheFile)) return false;

        $cacheLifetime = intval($this->config['cache_lifetime'] ?? 3600);
        return (time() - filemtime($cacheFile)) < $cacheLifetime;
    }

    private function getCachedSitemap() {
        return file_get_contents($this->cacheDir . '/sitemap.xml');
    }

    private function cacheSitemap($content, $filename = 'sitemap.xml') {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }

        $filepath = $this->cacheDir . '/' . $filename;
        file_put_contents($filepath, $content);

        if ($this->config['compression'] === 'true') {
            $gzFilepath = $filepath . '.gz';
            file_put_contents($gzFilepath, gzencode($content, 9));
        }
    }

    private function pingSearchEngines() {
        $sitemapUrl = urlencode($this->baseUrl . '/sitemap.xml');
        $searchEngines = array_filter(explode(',', $this->config['search_engines'] ?? ''));
        
        foreach ($searchEngines as $engine) {
            $engine = trim($engine);
            $pingUrl = "http://www.$engine/ping?sitemap=$sitemapUrl";
            
            $ch = curl_init($pingUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    public function addExclusion($url, $reason = '') {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO sitemap_exclusions (url, reason) VALUES (:url, :reason)"
        );
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);
        $stmt->bindValue(':reason', $reason, SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function removeExclusion($url) {
        $stmt = $this->db->getConnection()->prepare(
            "DELETE FROM sitemap_exclusions WHERE url = :url"
        );
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function updateSettings($type, $data) {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT OR REPLACE INTO sitemap_settings 
             (type, changefreq, priority, is_active, updated_at) 
             VALUES (:type, :changefreq, :priority, :is_active, CURRENT_TIMESTAMP)"
        );
        
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
        $stmt->bindValue(':changefreq', $data['changefreq'], SQLITE3_TEXT);
        $stmt->bindValue(':priority', $data['priority'], SQLITE3_FLOAT);
        $stmt->bindValue(':is_active', $data['is_active'], SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
}
