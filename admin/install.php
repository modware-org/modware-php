<?php
require_once __DIR__ . '/config/Database.php';

class AdminInstaller {
    private $db;
    private $errors = [];
    private $debug = [];

    public function __construct() {
        try {
            $this->db = AdminDatabase::getInstance();
            $this->debug[] = "Database instance created successfully";
        } catch (Exception $e) {
            $this->errors[] = "Database connection error: " . $e->getMessage();
        }
    }

    public function install() {
        try {
            // Ensure database file exists and is writable
            $dbPath = dirname(__DIR__) . '/admin/admin.sqlite';
            if (!file_exists($dbPath)) {
                touch($dbPath);
                chmod($dbPath, 0666);
                $this->debug[] = "Created database file: $dbPath";
            }

            // Create admin tables
            $this->executeSchema();
            
            // Verify tables were created
            $this->verifyTables();
            
            return empty($this->errors);
        } catch (Exception $e) {
            $this->errors[] = "Installation error: " . $e->getMessage();
            return false;
        }
    }

    private function executeSchema() {
        // Read schema file
        $schemaPath = __DIR__ . '/schema.sql';
        if (!file_exists($schemaPath)) {
            throw new Exception("Schema file not found: $schemaPath");
        }

        $sql = file_get_contents($schemaPath);
        if ($sql === false) {
            throw new Exception("Failed to read schema file");
        }

        // Split SQL into individual statements
        $statements = array_filter(
            array_map(
                'trim',
                explode(';', $sql)
            ),
            'strlen'
        );

        foreach ($statements as $statement) {
            try {
                $this->debug[] = "Executing SQL: " . substr($statement, 0, 100) . "...";
                $result = $this->db->getConnection()->exec($statement);
                
                if ($result === false) {
                    $error = $this->db->getConnection()->lastErrorMsg();
                    if (!strpos($error, 'already exists')) {
                        $this->errors[] = "SQL Error: $error\nStatement: $statement";
                        error_log("SQL Error: $error\nStatement: $statement");
                    } else {
                        $this->debug[] = "Table already exists (skipping): " . $error;
                    }
                } else {
                    $this->debug[] = "SQL executed successfully";
                }
            } catch (Exception $e) {
                if (!strpos($e->getMessage(), 'already exists')) {
                    $this->errors[] = "SQL Error: " . $e->getMessage() . "\nStatement: $statement";
                    error_log("SQL Exception: " . $e->getMessage() . "\nStatement: $statement");
                } else {
                    $this->debug[] = "Table already exists (skipping): " . $e->getMessage();
                }
            }
        }
    }

    private function verifyTables() {
        $requiredTables = ['admin_users', 'admin_sessions', 'admin_settings', 'admin_components', 
                          'admin_modules', 'admin_sections', 'admin_integrations', 'admin_activity_log'];
        
        foreach ($requiredTables as $table) {
            try {
                $result = $this->db->getConnection()->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
                if (!$result->fetchArray()) {
                    $this->errors[] = "Table verification failed: $table table was not created";
                } else {
                    $this->debug[] = "Verified table exists: $table";
                }
            } catch (Exception $e) {
                $this->errors[] = "Table verification error for $table: " . $e->getMessage();
            }
        }
    }

    public function getErrors() {
        return $this->errors;
    }

    public function getDebug() {
        return $this->debug;
    }
}

// Run installation
$installer = new AdminInstaller();
$success = $installer->install();

// Output results
header('Content-Type: application/json');
echo json_encode([
    'success' => $success,
    'errors' => $installer->getErrors(),
    'debug' => $installer->getDebug()
], JSON_PRETTY_PRINT);
