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
class GalleryApi extends Api {
    private $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxFileSize = 10485760; // 10MB
    
    public function __construct() {
        parent::__construct('gallery', [
            'title',
            'description',
            'image_path',
            'thumbnail_path',
            'alt_text',
            'category',
            'sort_order',
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

        // Handle image upload
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $this->handleImageUpload();
            return;
        }
        
        parent::handleRequest($method);
    }

    private function handleImageUpload() {
        $response = ['success' => false];
        
        try {
            $image = $_FILES['image'];
            
            // Validate file
            if ($image['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Image upload failed');
            }

            // Validate file type
            if (!in_array($image['type'], $this->allowedImageTypes)) {
                throw new Exception('Invalid image type');
            }

            // Validate file size
            if ($image['size'] > $this->maxFileSize) {
                throw new Exception('File size exceeds limit');
            }

            // Create upload directories if they don't exist
            $uploadDir = __DIR__ . '/../../uploads/gallery/';
            $thumbDir = $uploadDir . 'thumbnails/';
            foreach ([$uploadDir, $thumbDir] as $dir) {
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
            }

            // Generate unique filename
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $imagePath = $uploadDir . $fileName;
            $thumbPath = $thumbDir . $fileName;

            // Move uploaded file
            if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                throw new Exception('Failed to move uploaded file');
            }

            // Create thumbnail
            $this->createThumbnail($imagePath, $thumbPath);

            // Create database record
            $data = [
                'title' => $_POST['title'] ?? pathinfo($image['name'], PATHINFO_FILENAME),
                'description' => $_POST['description'] ?? '',
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbPath,
                'alt_text' => $_POST['alt_text'] ?? '',
                'category' => $_POST['category'] ?? null,
                'sort_order' => $_POST['sort_order'] ?? 0,
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

    private function createThumbnail($sourcePath, $targetPath, $maxWidth = 300, $maxHeight = 300) {
        list($width, $height) = getimagesize($sourcePath);
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        // Create new image
        $sourceImage = imagecreatefromstring(file_get_contents($sourcePath));
        $targetImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG images
        if (mime_content_type($sourcePath) == 'image/png') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
        }
        
        // Resize
        imagecopyresampled(
            $targetImage, $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );
        
        // Save thumbnail
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION);
        switch(strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($targetImage, $targetPath, 90);
                break;
            case 'png':
                imagepng($targetImage, $targetPath, 9);
                break;
            case 'gif':
                imagegif($targetImage, $targetPath);
                break;
            case 'webp':
                imagewebp($targetImage, $targetPath, 90);
                break;
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($targetImage);
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
$api = new GalleryApi();
$api->handleRequest();

// Create curl.sh testing script
$curlScript = <<<'EOD'
#!/bin/bash

# Configuration
API_URL="http://localhost/components/gallery/api.php"
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

echo "Running Gallery API Tests..."
echo "-------------------------"

# Test 1: Get all images
echo -e "\nTest 1: Get all images"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL"

# Test 2: Upload image
echo -e "\nTest 2: Upload image"
# Create a test image using ImageMagick if available
if command -v convert &> /dev/null; then
    convert -size 100x100 xc:white -draw "text 10,50 'Test Image'" test_image.jpg
    curl -X POST \
         -H "Authorization: Bearer $TOKEN" \
         -F "image=@test_image.jpg" \
         -F "title=Test Image" \
         -F "description=A test image" \
         -F "alt_text=Test image alt text" \
         -F "category=test" \
         "$API_URL"
    rm test_image.jpg
else
    echo "ImageMagick not found - skipping image upload test"
fi

# Store the ID from an image creation for further tests
RESPONSE=$(curl -s -X POST \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "title": "Test Image",
         "description": "A test image",
         "image_path": "/uploads/gallery/test.jpg",
         "thumbnail_path": "/uploads/gallery/thumbnails/test.jpg",
         "alt_text": "Test image alt text",
         "category": "test",
         "sort_order": 0,
         "created_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'",
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL")
IMAGE_ID=$(echo $RESPONSE | jq -r '.id')

# Test 3: Get specific image
echo -e "\nTest 3: Get image by ID"
curl -X GET \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$IMAGE_ID"

# Test 4: Update image metadata
echo -e "\nTest 4: Update image"
curl -X PUT \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
         "title": "Updated Test Image",
         "description": "Updated description",
         "category": "updated_test",
         "sort_order": 1,
         "updated_at": "'$(date -u +"%Y-%m-%d %H:%M:%S")'"
     }' \
     "$API_URL?id=$IMAGE_ID"

# Test 5: Delete image
echo -e "\nTest 5: Delete image"
curl -X DELETE \
     -H "Authorization: Bearer $TOKEN" \
     -H "Content-Type: application/json" \
     "$API_URL?id=$IMAGE_ID"

echo -e "\nTests completed!"
EOD;

// Write curl testing script
file_put_contents(__DIR__ . '/curl.sh', $curlScript);
chmod(__DIR__ . '/curl.sh', 0755);
?>
