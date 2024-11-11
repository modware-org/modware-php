<?php
require_once __DIR__ . '/Logger.php';

class Database {
    private static $instance = null;
    private $connection = null;
    private $logger;
    private $dbPath;

    private function __construct($dbPath) {
        $this->logger = Logger::getInstance();
        $this->dbPath = $dbPath;
        $this->connect();
    }

    public static function getInstance($dbPath = null) {
        if (self::$instance === null) {
            if ($dbPath === null) {
                $dbPath = getenv('DB_PATH');
                if (!$dbPath) {
                    throw new Exception('DB_PATH environment variable is not set');
                }
            }
            self::$instance = new self($dbPath);
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $this->logger->log("Connecting to database at path: " . $this->dbPath);
            
            // Create directory if it doesn't exist
            $dbDir = dirname($this->dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            
            // Use PDO instead of SQLite3
            $this->connection = new PDO("sqlite:" . $this->dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->logger->log("Database connection established successfully");
        } catch (Exception $e) {
            $this->logger->logError("Database connection failed for path: " . $this->dbPath, $e);
            throw $e;
        }
    }

    public function query($sql, $params = []) {
        try {
            $this->logger->logSQL($sql, $params);
            
            $stmt = $this->connection->prepare($sql);
            
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->logger->logSQL($sql, $params, $rows);
            
            return $rows;
        } catch (Exception $e) {
            $this->logger->logError("Query execution failed", $e);
            throw $e;
        }
    }

    public function execute($sql, $params = []) {
        try {
            $this->logger->logSQL($sql, $params);
            
            $stmt = $this->connection->prepare($sql);
            
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $result = $stmt->execute();
            $this->logger->logSQL($sql, $params, "Statement executed");
            
            return $result;
        } catch (Exception $e) {
            $this->logger->logError("Statement execution failed", $e);
            throw $e;
        }
    }

    public function getLastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        $this->logger->log("Beginning transaction", "TRANSACTION");
        return $this->connection->beginTransaction();
    }

    public function commit() {
        $this->logger->log("Committing transaction", "TRANSACTION");
        return $this->connection->commit();
    }

    public function rollback() {
        $this->logger->log("Rolling back transaction", "TRANSACTION");
        return $this->connection->rollBack();
    }

    public function getConnection() {
        return $this->connection;
    }
}

// Global database connection
try {
    $db = Database::getInstance()->getConnection();
} catch (Exception $e) {
    error_log("Failed to establish database connection: " . $e->getMessage());
    $db = null;
}
