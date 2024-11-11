<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../api/Api.php';

// Token generation and validation
class TokenAuth {
    private static $secretKey = 'your-secret-key-here'; // In production, this should be in env config
    
    public static function generateToken($userId) {
        $payload = [
            'user_id' => $userId,
            'exp' => time() + (60 * 60), // 1 hour expiration
            'iat' => time()
        ];
        
        return base64_encode(json_encode($payload)) . '.' . 
               hash_hmac('sha256', base64_encode(json_encode($payload)), self::$secretKey);
    }
    
    public static function validateToken($token) {
        list($payload, $signature) = explode('.', $token);
        
        if (hash_hmac('sha256', $payload, self::$secretKey) !== $signature) {
            return false;
        }
        
        $data = json_decode(base64_decode($payload), true);
        
        if ($data['exp'] < time()) {
            return false;
        }
        
        return $data;
    }
}

// API Endpoint Handler
class UploadApi extends Api {
    public function __construct() {
        parent::__construct('uploads', [
            'file_name',
            'file_path',
            'file_size',
            'mime_type',
            'created_at',
            'updated_at'
        ]);
    }
    
    public function handleRequest($method = null) {
        // Validate token except for OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                http_response_code(401);
                echo json_encode(['error' => 'No authorization token provided']);
                exit;
            }
            
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            if (!TokenAuth::validateToken($token)) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid or expired token']);
                exit;
            }
        }
        
        parent::handleRequest($method);
    }
}

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Initialize and handle API request
$api = new UploadApi();
$api->handleRequest();

// Create curl.sh testing script
$curlScript = <<<'EOD'
#!/bin/bash

# Configuration
API_URL="http://localhost/components/upload/api.php"
TEST_USER_ID=1

# Generate test token
TOKEN=$(php -r '
require_once "api.php";
echo TokenAuth::generateToken('$TEST_USER_ID');
')

# Colors for output
GREEN="\033[0;32m"
RED="\033[0;31m"
NC="\033[0m"

echo "Running Upload API Tests..."
echo "-------------------------"

# Test 1: Get all uploads
echo -e "\nTest 1: Get all uploads"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL"

# Test 2: Create new upload
echo -e "\nTest 2: Create new upload"
curl -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "file_name": "test.txt",
         "file_path": "/uploads/test.txt",
         "file_size": 1024,
         "mime_type": "text/plain",
         "created_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL"

# Store the ID from the creation response
UPLOAD_ID=$(curl -s -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "file_name": "test.txt",
         "file_path": "/uploads/test.txt",
         "file_size": 1024,
         "mime_type": "text/plain",
         "created_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL" | jq -r '.id')

# Test 3: Get specific upload
echo -e "\nTest 3: Get upload by ID"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$UPLOAD_ID"

# Test 4: Update upload
echo -e "\nTest 4: Update upload"
curl -X PUT \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "file_name": "updated.txt",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL?id=$UPLOAD_ID"

# Test 5: Delete upload
echo -e "\nTest 5: Delete upload"
curl -X DELETE \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$UPLOAD_ID"

echo -e "\nTests completed!"
EOD;

// Write curl testing script
file_put_contents(__DIR__ . '/curl.sh', $curlScript);
chmod(__DIR__ . '/curl.sh', 0755);
?>
