<?php
require_once __DIR__ . '/../Api.php';

class PagesEndpoint extends Api {
    public function get($params = []) {
        if (isset($params['id'])) {
            return $this->getPage($params['id']);
        }
        return $this->getPages();
    }

    public function post($data) {
        $required = ['title', 'slug'];
        $this->validateRequired($required, $data);
        
        return $this->createPage($data);
    }

    public function put($id, $data) {
        $this->validateId($id);
        return $this->updatePage($id, $data);
    }

    public function delete($id) {
        $this->validateId($id);
        return $this->deletePage($id);
    }

    private function getPages() {
        $query = "SELECT * FROM pages ORDER BY created_at DESC";
        return $this->db->query($query);
    }

    private function getPage($id) {
        $query = "SELECT * FROM pages WHERE id = ?";
        $page = $this->db->query($query, [$id], true);
        
        if (!$page) {
            throw new Exception("Page not found", 404);
        }
        
        // Get page sections
        $query = "SELECT s.*, ps.sort_order 
                 FROM sections s 
                 JOIN page_sections ps ON s.id = ps.section_id 
                 WHERE ps.page_id = ? 
                 ORDER BY ps.sort_order";
        $page['sections'] = $this->db->query($query, [$id]);
        
        // Get page components
        $query = "SELECT c.*, pc.sort_order, pc.section_id 
                 FROM components c 
                 JOIN page_components pc ON c.id = pc.component_id 
                 WHERE pc.page_id = ? 
                 ORDER BY pc.sort_order";
        $page['components'] = $this->db->query($query, [$id]);
        
        return $page;
    }

    private function createPage($data) {
        $query = "INSERT INTO pages (name, title, slug, template, status, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'] ?? $data['title'],
            $data['title'],
            $data['slug'],
            $data['template'] ?? 'default',
            $data['status'] ?? 'draft',
            $data['is_active'] ?? 1
        ];
        
        return $this->db->query($query, $params);
    }

    private function updatePage($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'title', 'slug', 'template', 'status', 'is_active'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            throw new Exception("No valid fields to update", 400);
        }
        
        $params[] = $id;
        $query = "UPDATE pages SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        return $this->db->query($query, $params);
    }

    private function deletePage($id) {
        // Delete related data first
        $this->db->query("DELETE FROM page_sections WHERE page_id = ?", [$id]);
        $this->db->query("DELETE FROM page_components WHERE page_id = ?", [$id]);
        $this->db->query("DELETE FROM section_data WHERE page_id = ?", [$id]);
        $this->db->query("DELETE FROM component_data WHERE page_id = ?", [$id]);
        
        // Delete the page
        return $this->db->query("DELETE FROM pages WHERE id = ?", [$id]);
    }

    // Section management
    public function getSections($pageId) {
        $query = "SELECT s.*, ps.sort_order 
                 FROM sections s 
                 JOIN page_sections ps ON s.id = ps.section_id 
                 WHERE ps.page_id = ? 
                 ORDER BY ps.sort_order";
        return $this->db->query($query, [$pageId]);
    }

    public function addSection($pageId, $data) {
        $required = ['section_id'];
        $this->validateRequired($required, $data);
        
        $query = "INSERT INTO page_sections (page_id, section_id, sort_order) 
                 VALUES (?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 
                               FROM page_sections WHERE page_id = ?))";
        return $this->db->query($query, [$pageId, $data['section_id'], $pageId]);
    }

    public function removeSection($pageId, $sectionId) {
        $query = "DELETE FROM page_sections WHERE page_id = ? AND section_id = ?";
        return $this->db->query($query, [$pageId, $sectionId]);
    }

    // Component management
    public function getComponents($pageId) {
        $query = "SELECT c.*, pc.sort_order, pc.section_id 
                 FROM components c 
                 JOIN page_components pc ON c.id = pc.component_id 
                 WHERE pc.page_id = ? 
                 ORDER BY pc.sort_order";
        return $this->db->query($query, [$pageId]);
    }

    public function addComponent($pageId, $data) {
        $required = ['component_id'];
        $this->validateRequired($required, $data);
        
        $query = "INSERT INTO page_components (page_id, component_id, section_id, sort_order) 
                 VALUES (?, ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 
                                 FROM page_components WHERE page_id = ?))";
        return $this->db->query($query, [
            $pageId, 
            $data['component_id'], 
            $data['section_id'] ?? null, 
            $pageId
        ]);
    }

    public function removeComponent($pageId, $componentId) {
        $query = "DELETE FROM page_components WHERE page_id = ? AND component_id = ?";
        return $this->db->query($query, [$pageId, $componentId]);
    }
}

// Register the endpoint
Api::registerEndpoint('pages', new PagesEndpoint());
