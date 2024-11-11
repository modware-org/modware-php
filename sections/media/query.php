<?php
require_once __DIR__ . '/../../config/Database.php';

class MediaQuery {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getMediaItems($type = null) {
        $sql = "SELECT * FROM media";
        if ($type) {
            $sql .= " WHERE type = ?";
            return $this->db->query($sql, [$type]);
        }
        return $this->db->query($sql);
    }

    public function addMediaItem($data) {
        $sql = "INSERT INTO media (filename, type, title, alt_text, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, [
            $data['filename'],
            $data['type'],
            $data['title'],
            $data['alt_text']
        ]);
    }

    public function updateMediaItem($id, $data) {
        $sql = "UPDATE media 
                SET title = ?, alt_text = ?, updated_at = NOW() 
                WHERE id = ?";
        return $this->db->query($sql, [
            $data['title'],
            $data['alt_text'],
            $id
        ]);
    }

    public function deleteMediaItem($id) {
        $sql = "DELETE FROM media WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function getMediaItem($id) {
        $sql = "SELECT * FROM media WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result ? $result[0] : null;
    }
}
