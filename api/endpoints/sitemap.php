<?php
require_once __DIR__ . '/../../config/Database.php';

class SitemapEndpoint {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function handle($method, $params = []) {
        if ($method === 'GET') {
            if (isset($params['action']) && $params['action'] === 'exclusions') {
                return $this->getExclusions();
            }
        } elseif ($method === 'POST' && isset($params['action']) && $params['action'] === 'exclusions') {
            $data = json_decode(file_get_contents('php://input'), true);
            return $this->updateExclusions($data);
        }
        
        throw new Exception('Invalid sitemap endpoint');
    }

    private function getExclusions() {
        try {
            $query = "SELECT value FROM config WHERE name = 'sitemap_exclusions'";
            $result = $this->db->getConnection()->query($query);
            $row = $result->fetchArray(SQLITE3_ASSOC);
            
            return [
                'exclusions' => $row ? json_decode($row['value'], true) : []
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to get sitemap exclusions');
        }
    }

    private function updateExclusions($data) {
        if (!isset($data['exclusions']) || !is_array($data['exclusions'])) {
            throw new Exception('Invalid exclusions data');
        }

        try {
            $value = json_encode($data['exclusions']);
            $query = "INSERT OR REPLACE INTO config (name, value, type) VALUES ('sitemap_exclusions', :value, 'json')";
            $stmt = $this->db->getConnection()->prepare($query);
            $stmt->bindValue(':value', $value, SQLITE3_TEXT);
            $stmt->execute();
            
            return ['success' => true, 'message' => 'Sitemap exclusions updated successfully'];
        } catch (Exception $e) {
            throw new Exception('Failed to update sitemap exclusions');
        }
    }
}
