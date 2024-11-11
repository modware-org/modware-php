<?php
require_once __DIR__ . '/../../config/Database.php';

class MenuQuery {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getMenuData($adminContext = false) {
        $data = [
            'config' => $this->getConfig(),
            'items' => $this->getMenuItems(null, $adminContext),
            'categories' => $this->getMenuCategories(null, $adminContext)
        ];
        return $data;
    }

    private function getConfig() {
        $result = $this->db->query(
            "SELECT name, value FROM config WHERE name LIKE 'menu_%'"
        );
        
        $config = [];
        foreach ($result as $row) {
            $key = str_replace('menu_', '', $row['name']);
            $config[$key] = $row['value'];
        }
        
        return $config;
    }

    private function getMenuItems($parentId = null, $adminContext = false) {
        $query = "SELECT * FROM menu_items WHERE parent_id " . 
                 ($parentId === null ? "IS NULL" : "= :parent_id");
        
        $params = [];
        if ($parentId !== null) {
            $params[':parent_id'] = $parentId;
        }
        
        // Only filter by is_active if not in admin context
        if (!$adminContext) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY position ASC";
        
        $result = $this->db->query($query, $params);
        
        $items = [];
        foreach ($result as $row) {
            $item = $row;
            $children = $this->getMenuItems($row['id'], $adminContext);
            if (!empty($children)) {
                $item['children'] = $children;
            }
            $items[] = $item;
        }
        
        return $items;
    }

    private function getMenuCategories($parentId = null, $adminContext = false) {
        $query = "SELECT * FROM menu_categories WHERE parent_id " . 
                 ($parentId === null ? "IS NULL" : "= :parent_id");
        
        $params = [];
        if ($parentId !== null) {
            $params[':parent_id'] = $parentId;
        }
        
        // Only filter by is_active and show_in_menu if not in admin context
        if (!$adminContext) {
            $query .= " AND is_active = 1 AND show_in_menu = 1";
        }
        
        $query .= " ORDER BY menu_position ASC";
        
        $result = $this->db->query($query, $params);
        
        $categories = [];
        foreach ($result as $row) {
            $category = $row;
            $children = $this->getMenuCategories($row['id'], $adminContext);
            if (!empty($children)) {
                $category['children'] = $children;
            }
            $categories[] = $category;
        }
        
        return $categories;
    }

    public function updateMenuItem($id, $data) {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE menu_items 
             SET title = :title, 
                 url = :url, 
                 parent_id = :parent_id, 
                 position = :position, 
                 is_active = :is_active,
                 target = :target,
                 icon_class = :icon_class,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id"
        );
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':url', $data['url'], PDO::PARAM_STR);
        $stmt->bindValue(':parent_id', $data['parent_id'], $data['parent_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':position', $data['position'], PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_INT);
        $stmt->bindValue(':target', $data['target'] ?? '_self', PDO::PARAM_STR);
        $stmt->bindValue(':icon_class', $data['icon_class'] ?? '', PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function addMenuItem($data) {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO menu_items 
             (title, url, parent_id, position, is_active, target, icon_class) 
             VALUES (:title, :url, :parent_id, :position, :is_active, :target, :icon_class)"
        );
        
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':url', $data['url'], PDO::PARAM_STR);
        $stmt->bindValue(':parent_id', $data['parent_id'], $data['parent_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':position', $data['position'], PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_INT);
        $stmt->bindValue(':target', $data['target'] ?? '_self', PDO::PARAM_STR);
        $stmt->bindValue(':icon_class', $data['icon_class'] ?? '', PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function deleteMenuItem($id) {
        // First update any children to have no parent
        $updateStmt = $this->db->getConnection()->prepare(
            "UPDATE menu_items SET parent_id = NULL WHERE parent_id = :id"
        );
        $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Then delete the item
        $deleteStmt = $this->db->getConnection()->prepare(
            "DELETE FROM menu_items WHERE id = :id"
        );
        $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $deleteStmt->execute();
    }

    public function updateCategory($id, $data) {
        $stmt = $this->db->getConnection()->prepare(
            "UPDATE menu_categories 
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 parent_id = :parent_id,
                 sort_order = :sort_order,
                 is_active = :is_active,
                 show_in_menu = :show_in_menu,
                 menu_position = :menu_position,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id"
        );
        
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(':parent_id', $data['parent_id'], $data['parent_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':sort_order', $data['sort_order'], PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_INT);
        $stmt->bindValue(':show_in_menu', $data['show_in_menu'], PDO::PARAM_INT);
        $stmt->bindValue(':menu_position', $data['menu_position'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function addCategory($data) {
        $stmt = $this->db->getConnection()->prepare(
            "INSERT INTO menu_categories 
             (name, slug, description, parent_id, sort_order, is_active, show_in_menu, menu_position) 
             VALUES (:name, :slug, :description, :parent_id, :sort_order, :is_active, :show_in_menu, :menu_position)"
        );
        
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(':parent_id', $data['parent_id'], $data['parent_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':sort_order', $data['sort_order'], PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_INT);
        $stmt->bindValue(':show_in_menu', $data['show_in_menu'], PDO::PARAM_INT);
        $stmt->bindValue(':menu_position', $data['menu_position'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        // First update any children to have no parent
        $updateStmt = $this->db->getConnection()->prepare(
            "UPDATE menu_categories SET parent_id = NULL WHERE parent_id = :id"
        );
        $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $updateStmt->execute();

        // Then delete the category
        $deleteStmt = $this->db->getConnection()->prepare(
            "DELETE FROM menu_categories WHERE id = :id"
        );
        $deleteStmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $deleteStmt->execute();
    }

    public function updateConfig($data) {
        foreach ($data as $key => $value) {
            $name = 'menu_' . $key;
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR REPLACE INTO config (name, value, type, description) 
                 VALUES (:name, :value, 'text', 'Menu configuration')"
            );
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->execute();
        }
        return true;
    }
}
