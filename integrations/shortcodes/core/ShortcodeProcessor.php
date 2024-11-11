<?php
namespace Integrations\Shortcodes;

class ShortcodeProcessor {
    private static $shortcodes = [];

    public static function register($tag, $callback) {
        self::$shortcodes[$tag] = $callback;
    }

    public static function process($content) {
        return preg_replace_callback('/\[([a-z0-9_-]+)(\s+[^\]]+)?\]/i', function($matches) {
            $tag = $matches[1];
            $attrs = isset($matches[2]) ? self::parseAttributes($matches[2]) : [];
            
            if (isset(self::$shortcodes[$tag])) {
                return call_user_func(self::$shortcodes[$tag], $attrs);
            }
            
            return $matches[0];
        }, $content);
    }

    private static function parseAttributes($text) {
        $attrs = [];
        preg_match_all('/([a-z0-9_-]+)="([^"]*)"/', trim($text), $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $attrs[$match[1]] = $match[2];
        }
        
        return $attrs;
    }
}
