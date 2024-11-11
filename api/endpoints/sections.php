<?php
require_once __DIR__ . '/../../config/Database.php';

class SectionsEndpoint {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function handle($method, $params = []) {
        $section = $params['id'] ?? null;
        
        if ($method === 'GET') {
            if ($section) {
                return $this->getSectionData($section);
            }
            return $this->getAllSections();
        }
        
        throw new Exception('Method not allowed');
    }

    private function getSectionData($section) {
        $sectionPath = __DIR__ . '/../../sections/' . $section . '/query.php';
        if (!file_exists($sectionPath)) {
            throw new Exception('Section not found');
        }

        require_once $sectionPath;
        if (function_exists('get' . ucfirst($section) . 'Data')) {
            $funcName = 'get' . ucfirst($section) . 'Data';
            return $funcName();
        }
        
        throw new Exception('Section data function not found');
    }

    private function getAllSections() {
        $sectionsPath = __DIR__ . '/../../sections/';
        $sections = [];
        
        foreach (glob($sectionsPath . '*', GLOB_ONLYDIR) as $dir) {
            $sectionName = basename($dir);
            if (file_exists($dir . '/query.php')) {
                $sections[] = $sectionName;
            }
        }
        
        return $sections;
    }
}
