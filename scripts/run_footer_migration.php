<?php
// Load environment variables from .env
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    $envLines = explode("\n", $envContents);
    foreach ($envLines as $line) {
        $line = trim($line);
        if (!empty($line) && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set environment variable
            putenv("$key=$value");
        }
    }
}

require_once __DIR__ . '/../config/Database.php';

function runFooterMigration() {
    try {
        $db = Database::getInstance();
        
        $migrationSql = file_get_contents(__DIR__ . '/../migration_add_footer_tables.sql');
        
        // Split the SQL into individual statements
        $statements = explode(';', $migrationSql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $db->execute($statement);
            }
        }
        
        echo "Footer tables migration completed successfully.\n";
    } catch (Exception $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        throw $e;
    }
}

// Run the migration if this script is executed directly
if (php_sapi_name() === 'cli') {
    runFooterMigration();
}
