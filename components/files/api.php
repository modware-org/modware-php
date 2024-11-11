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
class FilesApi extends Api {
    public function __construct() {
        parent::__construct('files', [
            'file_name',
            'original_name',
            'file_path',
            'file_size',
            'mime_type',
            'category',
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

        // Handle file upload
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $this->handleFileUpload();
            return;
        }
        
        parent::handleRequest($method);
    }

    private function handleFileUpload() {
        $response = ['success' => false];
        
        try {
            $file = $_FILES['file'];
            $category = $_POST['category'] ?? null;
            
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload failed');
            }

            // Create upload directory if it doesn't exist
            $uploadDir = __DIR__ . '/../../uploads/files/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $filePath = $uploadDir . $fileName;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                throw new Exception('Failed to move uploaded file');
            }

            // Create database record
            $data = [
                'file_name' => $fileName,
                'original_name' => $file['name'],
                'file_path' => $filePath,
                'file_size' => $file['size'],
                'mime_type' => $file['type'],
                'category' => $category,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->create($data);
            
            $response = [
                'success' => true,
                'data' => $result
            ];
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
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
$api = new FilesApi();
$api->handleRequest();

// Create curl.sh testing script
$curlScript = <<<'EOD'
#!/bin/bash

# Configuration
API_URL="http://localhost/components/files/api.php"
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

echo "Running Files API Tests..."
echo "-------------------------"

# Test 1: Get all files
echo -e "\nTest 1: Get all files"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL"

# Test 2: Upload file
echo -e "\nTest 2: Upload file"
echo "Test content" > test_file.txt
curl -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -F "file=@test_file.txt" \
     -F "category=documents" \
     "$API_URL"
rm test_file.txt

# Store the ID from a file creation for further tests
RESPONSE=$(curl -s -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "file_name": "test.txt",
         "original_name": "test.txt",
         "file_path": "/uploads/files/test.txt",
         "file_size": 1024,
         "mime_type": "text/plain",
         "category": "documents",
         "created_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL")
FILE_ID=$(echo $RESPONSE | jq -r '.id')

# Test 3: Get specific file
echo -e "\nTest 3: Get file by ID"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$FILE_ID"

# Test 4: Update file metadata
echo -e "\nTest 4: Update file"
curl -X PUT \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "category": "updated_category",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL?id=$FILE_ID"

# Test 5: Delete file
echo -e "\nTest 5: Delete file"
curl -X DELETE \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$FILE_ID"

echo -e "\nTests completed!"
EOD;

// Write curl testing script
file_put_contents(__DIR__ . '/curl.sh', $curlScript);
chmod(__DIR__ . '/curl.sh', 0755);
?>
