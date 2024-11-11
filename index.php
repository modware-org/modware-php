<?php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/Logger.php';
require_once __DIR__ . '/config/Database.php';
putenv("APP_NAME=index");

class PageLoader {
    private $logger;
    private $db;
    private $loadedStyles = [];
    private $loadedScripts = [];
    private $translations = [];

    public function __construct() {
        $this->logger = Logger::getInstance();
        $this->logger->log("Request started: " . $_SERVER['REQUEST_URI']);
        
        // Initialize database
        if (!getenv('DB_PATH')) {
            throw new Exception('DB_PATH environment variable is not set');
        }
        $this->db = Database::getInstance();
        
        // Load translations
        $this->loadTranslations();
    }

    private function loadTranslations() {
        try {
            $query = "SELECT l.code as lang, t.content_type || '.' || t.field_name as key, t.translation as value 
                     FROM translations t 
                     JOIN languages l ON t.language_id = l.id";
            $result = $this->db->query($query);
            foreach ($result as $row) {
                $this->translations[$row['lang']][$row['key']] = $row['value'];
            }
            $this->logger->log("Translations loaded successfully");
        } catch (Exception $e) {
            $this->logger->logError("Error loading translations", $e);
        }
    }

    private function processTranslations($content) {
        return preg_replace_callback(
            '/\[translate key="([^"]+)" lang="([^"]+)"\]/',
            function($matches) {
                $key = $matches[1];
                $lang = $matches[2];
                return $this->getTranslation($key, $lang);
            },
            $content
        );
    }

    private function getTranslation($key, $lang) {
        if (isset($this->translations[$lang][$key])) {
            return $this->translations[$lang][$key];
        }
        $this->logger->log("Translation not found: {$key} ({$lang})", "WARNING");
        return $key; // Return the key as fallback
    }

    public function loadSection($sectionName) {
        $sectionPath = __DIR__ . '/sections/' . $sectionName;
        
        // Check if section exists
        if (!is_dir($sectionPath)) {
            $this->logger->log("Section not found: {$sectionName}", "ERROR");
            return false;
        }

        // Load section files
        $this->loadSectionStyle($sectionName);
        
        // Capture section HTML output to process translations
        ob_start();
        $this->loadSectionHTML($sectionName);
        $content = ob_get_clean();
        echo $this->processTranslations($content);
        
        $this->registerSectionScript($sectionName);
        
        // Load all other files except SQL
        $files = glob($sectionPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                if ($extension !== 'sql' && 
                    $extension !== 'css' && // Skip CSS as it's already handled
                    $extension !== 'js' &&  // Skip JS as it's already handled
                    $extension !== 'php') { // Skip PHP as it's already handled
                    include $file;
                }
            }
        }
        
        return true;
    }

    private function loadSectionStyle($sectionName) {
        // Load base style from sections directory
        $baseStylePath = "/sections/{$sectionName}/style.css";
        $fullpath = __DIR__ . $baseStylePath;
        if (!in_array($baseStylePath, $this->loadedStyles) && file_exists($fullpath)) {
            $this->loadedStyles[] = $baseStylePath;
            echo "<link rel='stylesheet' href='{$baseStylePath}?v=" . filemtime($fullpath) . "'>\n";
            $this->logger->log("Loaded base style: {$baseStylePath} path: {$fullpath}");
        }

        // Check for override style in www directory
        $wwwPath = __DIR__ . '/../www';
        if (is_dir($wwwPath)) {
            $sites = glob($wwwPath . '/*', GLOB_ONLYDIR);
            foreach ($sites as $site) {
                $overrideStylePath = $site . "/sections/{$sectionName}/style.css";
                if (file_exists($overrideStylePath)) {
                    $relativePath = str_replace(__DIR__, '', $overrideStylePath);
                    if (!in_array($relativePath, $this->loadedStyles)) {
                        $this->loadedStyles[] = $relativePath;
                        echo "<link rel='stylesheet' href='{$relativePath}?v=" . filemtime($overrideStylePath) . "'>\n";
                        $this->logger->log("Loaded override style: {$relativePath} path: {$overrideStylePath}");
                    }
                }
            }
        }
    }

    private function loadSectionHTML($sectionName) {
        $htmlPath = __DIR__ . "/sections/{$sectionName}/html.php";
        if (file_exists($htmlPath)) {
            include $htmlPath;
            $this->logger->log("Loaded HTML: {$htmlPath}");
        }
    }

    private function registerSectionScript($sectionName) {
        $scriptPath = "/sections/{$sectionName}/script.js";
        if (!in_array($scriptPath, $this->loadedScripts) && file_exists(__DIR__ . $scriptPath)) {
            $this->loadedScripts[] = $scriptPath;
            echo "<script defer src='{$scriptPath}?v=" . filemtime(__DIR__ . $scriptPath) . "'></script>\n";
            $this->logger->log("Registered script: {$scriptPath}");
        }
    }

    public function run() {
        try {
            // Start session before any output
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // Get current page from URL
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $page = trim($uri, '/');
            if (empty($page)) {
                $page = 'home';
            }
            
            $this->logger->log("Loading page: $page");
            
            // Get page data
            $pageData = $this->db->query(
                "SELECT * FROM pages WHERE slug = :slug AND status = 'published'",
                ['slug' => $page]
            );
            
            if (empty($pageData)) {
                $this->logger->log("Page not found: $page", "ERROR");
                header("HTTP/1.0 404 Not Found");
                include __DIR__ . '/404.php';
                exit;
            }
            
            $pageId = $pageData[0]['id'];
            $this->logger->log("Page found, ID: $pageId");
            
            // Get page meta data
            $metaData = $this->db->query(
                "SELECT * FROM meta WHERE page_id = :page_id",
                ['page_id' => $pageId]
            );
            $this->logger->log("Meta data loaded");
            
            // Get page sections
            $sections = $this->db->query(
                "SELECT * FROM sections WHERE page_id = :page_id ORDER BY sort_order ASC",
                ['page_id' => $pageId]
            );
            $this->logger->log("Loaded " . count($sections) . " sections");
            
            // Start output buffering
            ob_start();
            
            // Include header
            include __DIR__ . '/header.php';
            
            // Render each section
            foreach ($sections as $section) {
                try {
                    $this->logger->log("Rendering section: " . $section['name']);
                    $this->loadSection($section['name']);
                } catch (Exception $e) {
                    $this->logger->logError("Error rendering section: " . $section['name'], $e);
                }
            }
            
            // Include footer
            include __DIR__ . '/footer.php';
            
            // Process translations in the final output
            $content = ob_get_clean();
            echo $this->processTranslations($content);
            
            $this->logger->log("Page rendered successfully");
            
        } catch (Exception $e) {
            $this->logger->logError("Fatal error", $e);
            
            // Clear any output
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            
            // Show error page
            header("HTTP/1.0 500 Internal Server Error");
            include __DIR__ . '/500.php';
        }
    }
}

// Initialize and run the page loader
$pageLoader = new PageLoader();
$pageLoader->run();
