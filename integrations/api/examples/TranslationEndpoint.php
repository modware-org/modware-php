<?php
namespace Integrations\Api\Examples;

class TranslationEndpoint {
    public static function register() {
        // Register API endpoints for translation management
        \Integrations\Api\ApiProcessor::registerEndpoint(
            '/api/translations', 
            'GET', 
            [self::class, 'getTranslations'],
            ['read_translations']
        );

        \Integrations\Api\ApiProcessor::registerEndpoint(
            '/api/translations', 
            'POST', 
            [self::class, 'createTranslation'],
            ['write_translations']
        );

        \Integrations\Api\ApiProcessor::registerEndpoint(
            '/api/translations/{id}', 
            'PUT', 
            [self::class, 'updateTranslation'],
            ['write_translations']
        );
    }

    public static function getTranslations($data) {
        global $db;
        
        $languageCode = $_GET['lang'] ?? null;
        $contentType = $_GET['type'] ?? null;
        $contentId = $_GET['content_id'] ?? null;

        $sql = "
            SELECT t.*, l.code as language_code, l.name as language_name
            FROM translations t
            JOIN languages l ON t.language_id = l.id
            WHERE 1=1
        ";
        $params = [];

        if ($languageCode) {
            $sql .= " AND l.code = ?";
            $params[] = $languageCode;
        }

        if ($contentType) {
            $sql .= " AND t.content_type = ?";
            $params[] = $contentType;
        }

        if ($contentId) {
            $sql .= " AND t.content_id = ?";
            $params[] = $contentId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return [
            'translations' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
        ];
    }

    public static function createTranslation($data) {
        global $db;

        if (!isset($data['language_code']) || !isset($data['content_type']) || 
            !isset($data['content_id']) || !isset($data['field_name']) || 
            !isset($data['translation'])) {
            throw new \Exception('Missing required fields');
        }

        // Get language ID
        $stmt = $db->prepare("SELECT id FROM languages WHERE code = ?");
        $stmt->execute([$data['language_code']]);
        $language = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$language) {
            throw new \Exception('Invalid language code');
        }

        $stmt = $db->prepare("
            INSERT INTO translations (
                language_id, content_type, content_id, field_name, translation
            ) VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $language['id'],
            $data['content_type'],
            $data['content_id'],
            $data['field_name'],
            $data['translation']
        ]);

        return [
            'id' => $db->lastInsertId(),
            'message' => 'Translation created successfully'
        ];
    }

    public static function updateTranslation($data) {
        global $db;

        $translationId = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        if (!isset($data['translation'])) {
            throw new \Exception('Missing translation content');
        }

        $stmt = $db->prepare("
            UPDATE translations 
            SET translation = ?, 
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");

        $stmt->execute([$data['translation'], $translationId]);

        return [
            'message' => 'Translation updated successfully'
        ];
    }
}

// Usage examples:

/*
// 1. Get translations for a specific language and content:
GET /api/translations?lang=es&type=section&content_id=123

// 2. Create a new translation:
POST /api/translations
{
    "language_code": "es",
    "content_type": "section",
    "content_id": 123,
    "field_name": "title",
    "translation": "¡Bienvenidos!"
}

// 3. Update an existing translation:
PUT /api/translations/456
{
    "translation": "¡Hola Mundo!"
}

// Headers required for all requests:
X-API-Key: your_api_key_here
Content-Type: application/json
*/
