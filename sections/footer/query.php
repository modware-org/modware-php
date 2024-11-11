<?php
require_once __DIR__ . '/../../config/Database.php';

class FooterQuery {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getFooterData() {
        $data = [
            'config' => $this->getConfig(),
            'links' => $this->getLinks(),
            'social' => $this->getSocialLinks()
        ];
        return $data;
    }

    private function getConfig() {
        $results = $this->db->query(
            "SELECT name, value FROM config WHERE name LIKE 'footer_%'"
        );
        
        $config = [];
        foreach ($results as $row) {
            $key = str_replace('footer_', '', $row['name']);
            $config[$key] = $row['value'];
        }
        
        return $config;
    }

    private function getLinks() {
        $results = $this->db->query(
            "SELECT * FROM footer_links WHERE is_active = 1 ORDER BY column_number, sort_order ASC"
        );
        
        $links = [];
        foreach ($results as $row) {
            $columnNum = $row['column_number'];
            if (!isset($links[$columnNum])) {
                $links[$columnNum] = [];
            }
            $links[$columnNum][] = $row;
        }
        
        return $links;
    }

    private function getSocialLinks() {
        $results = $this->db->query(
            "SELECT * FROM footer_social WHERE is_active = 1 ORDER BY sort_order ASC"
        );
        
        return $results;
    }

    public function updateLink($id, $data) {
        $sql = "UPDATE footer_links 
                SET title = :title, 
                    url = :url, 
                    column_number = :column_number, 
                    sort_order = :sort_order, 
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':title' => $data['title'],
            ':url' => $data['url'],
            ':column_number' => $data['column_number'],
            ':sort_order' => $data['sort_order'],
            ':is_active' => $data['is_active']
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function addLink($data) {
        $sql = "INSERT INTO footer_links 
                (title, url, column_number, sort_order, is_active) 
                VALUES (:title, :url, :column_number, :sort_order, :is_active)";
        
        $params = [
            ':title' => $data['title'],
            ':url' => $data['url'],
            ':column_number' => $data['column_number'],
            ':sort_order' => $data['sort_order'],
            ':is_active' => $data['is_active']
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function deleteLink($id) {
        $sql = "DELETE FROM footer_links WHERE id = :id";
        $params = [':id' => $id];
        return $this->db->execute($sql, $params);
    }

    public function updateSocialLink($id, $data) {
        $sql = "UPDATE footer_social 
                SET platform = :platform, 
                    url = :url, 
                    icon_svg = :icon_svg, 
                    sort_order = :sort_order, 
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':platform' => $data['platform'],
            ':url' => $data['url'],
            ':icon_svg' => $data['icon_svg'],
            ':sort_order' => $data['sort_order'],
            ':is_active' => $data['is_active']
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function addSocialLink($data) {
        $sql = "INSERT INTO footer_social 
                (platform, url, icon_svg, sort_order, is_active) 
                VALUES (:platform, :url, :icon_svg, :sort_order, :is_active)";
        
        $params = [
            ':platform' => $data['platform'],
            ':url' => $data['url'],
            ':icon_svg' => $data['icon_svg'],
            ':sort_order' => $data['sort_order'],
            ':is_active' => $data['is_active']
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function deleteSocialLink($id) {
        $sql = "DELETE FROM footer_social WHERE id = :id";
        $params = [':id' => $id];
        return $this->db->execute($sql, $params);
    }

    public function updateConfig($data) {
        foreach ($data as $key => $value) {
            $name = 'footer_' . $key;
            $sql = "INSERT OR REPLACE INTO config (name, value, type, description) 
                    VALUES (:name, :value, 'text', 'Footer configuration')";
            
            $params = [
                ':name' => $name,
                ':value' => $value
            ];
            
            $this->db->execute($sql, $params);
        }
        return true;
    }
}
