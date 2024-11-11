<?php
class Logger {
    private static $instance = null;
    private $logFile;
    private $errorLogFile;
    private $startTime;

    private function __construct() {
        $app_name = getenv('APP_NAME');
        $date = date('Y-m-d');
        $this->logFile = __DIR__ . "/../logs/{$app_name}_{$date}.log";
        $this->errorLogFile = __DIR__ . '/../logs/error.log';
        $this->startTime = microtime(true);
        
        // Create logs directory if it doesn't exist
        if (!is_dir(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
        
        // Start new log entry
        $this->log("=== Page Load Started ===");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function log($message, $type = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $elapsed = round((microtime(true) - $this->startTime) * 1000, 2);
        $logMessage = "[$timestamp][$type][{$elapsed}ms] $message" . PHP_EOL;
        
        // Ensure we're using today's log file
        $date = date('Y-m-d');
        $this->logFile = __DIR__ . "/../logs/app_{$date}.log";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function getCallerInfo() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = isset($trace[2]) ? $trace[2] : $trace[1];
        
        $file = isset($caller['file']) ? str_replace(__DIR__ . '/../', '', $caller['file']) : 'unknown';
        $line = isset($caller['line']) ? $caller['line'] : 'unknown';
        
        return "[$file:$line]";
    }

    private function extractSqlFileInfo($query) {
        // Try to find SQL file reference in comments
        if (preg_match('/\/\*\s*File:\s*([^\*]+)\s*\*\//', $query, $matches)) {
            return '[SQL:' . trim($matches[1]) . ']';
        }
        return '';
    }

    public function logSqlFile($filePath, $operation = 'Loading') {
        $relativePath = str_replace(__DIR__ . '/../', '', $filePath);
        $callerInfo = $this->getCallerInfo();
        $this->log("$callerInfo $operation SQL file: $relativePath", 'SQL_FILE');
    }

    public function logSQL($query, $params = [], $result = null) {
        // Get caller information
        $callerInfo = $this->getCallerInfo();
        $sqlFileInfo = $this->extractSqlFileInfo($query);
        
        // Format the SQL query with parameters
        $sql = $query;
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $sql = str_replace(":$key", is_string($value) ? "'$value'" : $value, $sql);
            }
        }
        
        // Log the SQL statement with file information
        $this->log("$callerInfo$sqlFileInfo SQL: $sql", 'SQL');
        
        // Log the result if provided
        if ($result !== null) {
            if (is_array($result)) {
                $count = count($result);
                $this->log("$callerInfo$sqlFileInfo Result: $count rows returned", 'SQL_RESULT');
            } else {
                $this->log("$callerInfo$sqlFileInfo Result: $result", 'SQL_RESULT');
            }
        }
    }

    public function logError($message, $exception = null) {
        $timestamp = date('Y-m-d H:i:s');
        $elapsed = round((microtime(true) - $this->startTime) * 1000, 2);
        
        $errorMsg = "ERROR: $message";
        if ($exception) {
            $errorMsg .= "\nException: " . $exception->getMessage() . 
                        "\nTrace: " . $exception->getTraceAsString();
        }
        
        // Format error message
        $logMessage = "[$timestamp][ERROR][{$elapsed}ms] $errorMsg" . PHP_EOL;
        
        // Write to both logs - append to both files
        $date = date('Y-m-d');
        $this->logFile = __DIR__ . "/../logs/app_{$date}.log";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        file_put_contents($this->errorLogFile, $logMessage, FILE_APPEND); // Now appending instead of overwriting
    }
}
