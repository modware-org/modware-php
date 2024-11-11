<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';

class Api {
    protected $db;
    protected $table;
    protected $allowedFields = [];
    private static $endpoints = [];

    public function __construct($table = null, $allowedFields = []) {
        $this->db = Database::getInstance()->getConnection();
        $this->table = $table;
        $this->allowedFields = $allowedFields;
    }

    protected function validateRequired($required, $data) {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Missing required field: $field", 400);
            }
        }
    }

    protected function validateId($id) {
        if (!$id || !is_numeric($id)) {
            throw new Exception("Invalid ID provided", 400);
        }
    }

    protected function sanitizeInput($data) {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->allowedFields)) {
                $sanitized[$key] = SQLite3::escapeString($value);
            }
        }
        return $sanitized;
    }

    protected function buildWhereClause($conditions) {
        if (empty($conditions)) {
            return '';
        }

        $where = [];
        foreach ($conditions as $field => $value) {
            $where[] = "$field = '" . SQLite3::escapeString($value) . "'";
        }
        return ' WHERE ' . implode(' AND ', $where);
    }

    public function getAll($conditions = []) {
        try {
            $sql = "SELECT * FROM {$this->table}" . $this->buildWhereClause($conditions);
            $result = $this->db->query($sql);
            
            $items = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $items[] = $row;
            }
            return $items;
        } catch (Exception $e) {
            error_log('Get all error: ' . $e->getMessage());
            throw new Exception('Failed to retrieve records');
        }
    }

    public function getPaginated($page = 1, $perPage = 10, $conditions = []) {
        try {
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM {$this->table}" . 
                   $this->buildWhereClause($conditions) . 
                   " LIMIT $perPage OFFSET $offset";
            
            $result = $this->db->query($sql);
            
            $items = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $items[] = $row;
            }
            return $items;
        } catch (Exception $e) {
            error_log('Get paginated error: ' . $e->getMessage());
            throw new Exception('Failed to retrieve paginated records');
        }
    }

    public function getById($id) {
        try {
            $id = SQLite3::escapeString($id);
            $sql = "SELECT * FROM {$this->table} WHERE id = '$id'";
            $result = $this->db->query($sql);
            return $result->fetchArray(SQLITE3_ASSOC);
        } catch (Exception $e) {
            error_log('Get by ID error: ' . $e->getMessage());
            throw new Exception('Failed to retrieve record');
        }
    }

    public function search($term, $fields) {
        try {
            $conditions = [];
            foreach ($fields as $field) {
                $conditions[] = "$field LIKE '%" . SQLite3::escapeString($term) . "%'";
            }
            
            $sql = "SELECT * FROM {$this->table} WHERE " . implode(' OR ', $conditions);
            $result = $this->db->query($sql);
            
            $items = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $items[] = $row;
            }
            return $items;
        } catch (Exception $e) {
            error_log('Search error: ' . $e->getMessage());
            throw new Exception('Search failed');
        }
    }

    public function create($data) {
        try {
            $data = $this->sanitizeInput($data);
            
            if (empty($data)) {
                throw new Exception('No valid fields to insert');
            }
            
            $fields = array_keys($data);
            $values = array_values($data);
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") 
                    VALUES ('" . implode("', '", $values) . "')";
            
            $result = $this->db->query($sql);
            if ($result === false) {
                throw new Exception('Insert failed');
            }
            
            $lastId = $this->db->lastInsertRowID();
            return $this->getById($lastId);
        } catch (Exception $e) {
            error_log('Create error: ' . $e->getMessage());
            throw new Exception('Failed to create record');
        }
    }

    public function update($id, $data) {
        try {
            $data = $this->sanitizeInput($data);
            
            if (empty($data)) {
                throw new Exception('No valid fields to update');
            }
            
            $updates = [];
            foreach ($data as $field => $value) {
                $updates[] = "$field = '$value'";
            }
            
            $id = SQLite3::escapeString($id);
            $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = '$id'";
            $result = $this->db->query($sql);
            
            if ($result === false) {
                throw new Exception('Update failed');
            }
            
            return $this->getById($id);
        } catch (Exception $e) {
            error_log('Update error: ' . $e->getMessage());
            throw new Exception('Failed to update record');
        }
    }

    public function delete($id) {
        try {
            $id = SQLite3::escapeString($id);
            $sql = "DELETE FROM {$this->table} WHERE id = '$id'";
            $result = $this->db->query($sql);
            
            if ($result === false) {
                throw new Exception('Delete failed');
            }
            
            return true;
        } catch (Exception $e) {
            error_log('Delete error: ' . $e->getMessage());
            throw new Exception('Failed to delete record');
        }
    }

    public static function registerEndpoint($name, $endpoint) {
        self::$endpoints[$name] = $endpoint;
    }

    public static function getEndpoint($name) {
        return isset(self::$endpoints[$name]) ? self::$endpoints[$name] : null;
    }

    public function handleRequest($method = null) {
        if ($method === null) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        $response = ['success' => false, 'message' => 'Invalid request'];

        try {
            switch ($method) {
                case 'GET':
                    if (isset($_GET['id'])) {
                        $response = $this->getById($_GET['id']);
                    } elseif (isset($_GET['search']) && isset($_GET['fields'])) {
                        $fields = explode(',', $_GET['fields']);
                        $response = $this->search($_GET['search'], $fields);
                    } elseif (isset($_GET['page'])) {
                        $page = (int)$_GET['page'];
                        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
                        $response = $this->getPaginated($page, $perPage);
                    } else {
                        $response = $this->getAll();
                    }
                    break;

                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $this->create($data);
                    break;

                case 'PUT':
                    if (!isset($_GET['id'])) {
                        throw new Exception('ID is required for update');
                    }
                    $data = json_decode(file_get_contents('php://input'), true);
                    $response = $this->update($_GET['id'], $data);
                    break;

                case 'DELETE':
                    if (!isset($_GET['id'])) {
                        throw new Exception('ID is required for deletion');
                    }
                    $response = $this->delete($_GET['id']);
                    break;
            }
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
