<?php
require_once __DIR__ . '/env.php';

class AdminDatabase {
    private static $instance = null;
    private $db;

    private function __construct() {
        // Use the DB_PATH from the environment configuration, with a default for admin database
        $dbPath = DB_PATH . '_admin.sqlite';
        $dbDir = dirname($dbPath);
        
        // Create database directory if it doesn't exist
        if (!file_exists($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        try {
            $this->db = new SQLite3($dbPath);
            $this->db->enableExceptions(true);
            
            // Set pragmas for better performance and security
            $this->db->exec('PRAGMA journal_mode=WAL');
            $this->db->exec('PRAGMA foreign_keys=ON');

            // Initialize database schema if needed
            $this->initializeSchema();
        } catch (Exception $e) {
            error_log('Database connection error: ' . $e->getMessage());
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    private function initializeSchema() {
        // Create basic tables if they don't exist
        $this->db->exec('CREATE TABLE IF NOT EXISTS sites (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            domain TEXT NOT NULL UNIQUE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');

        $this->db->exec('CREATE TABLE IF NOT EXISTS pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            site_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            slug TEXT NOT NULL,
            status TEXT DEFAULT "draft",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(site_id) REFERENCES sites(id)
        )');

        $this->db->exec('CREATE TABLE IF NOT EXISTS sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            title TEXT,
            type TEXT,
            position INTEGER DEFAULT 0,
            content TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(page_id) REFERENCES pages(id)
        )');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new AdminDatabase();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    public function prepare($sql) {
        return $this->db->prepare($sql);
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $result = $stmt->execute();
            return $result;
        } catch (Exception $e) {
            error_log('Query error: ' . $e->getMessage());
            throw new Exception('Query execution failed: ' . $e->getMessage());
        }
    }

    public function changes() {
        return $this->db->changes();
    }

    public function lastInsertRowID() {
        return $this->db->lastInsertRowID();
    }

    public function close() {
        if ($this->db) {
            $this->db->close();
        }
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
