<?php
namespace Integrations\Shortcodes;

class TranslationProcessor {
    private static $db;
    private static $currentLang = 'en';

    public static function init($db) {
        self::$db = $db;
        self::registerShortcode();
    }

    public static function setLanguage($lang) {
        self::$currentLang = $lang;
    }

    private static function registerShortcode() {
        ShortcodeProcessor::register('translate', [self::class, 'translateContent']);
    }

    public static function translateContent($attrs) {
        if (!isset($attrs['key']) || !self::$db) {
            return '';
        }

        $stmt = self::$db->prepare("
            SELECT t.translation
            FROM translations t
            JOIN languages l ON t.language_id = l.id
            WHERE l.code = ? AND t.content_type = 'dynamic'
            AND t.field_name = ?
        ");

        $stmt->execute([self::$currentLang, $attrs['key']]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['translation'] : $attrs['default'] ?? '';
    }

    public static function translate($contentType, $contentId, $fieldName, $defaultText = '') {
        if (!self::$db) {
            return $defaultText;
        }

        $stmt = self::$db->prepare("
            SELECT t.translation
            FROM translations t
            JOIN languages l ON t.language_id = l.id
            WHERE l.code = ? 
            AND t.content_type = ?
            AND t.content_id = ?
            AND t.field_name = ?
        ");

        $stmt->execute([self::$currentLang, $contentType, $contentId, $fieldName]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['translation'] : $defaultText;
    }
}

// Usage in sections:
// 1. Dynamic content: [translate key="welcome_message" default="Welcome!"]
// 2. Database content: <?php echo TranslationProcessor::translate('section', $sectionId, 'title', 'Default Title'); ?>
