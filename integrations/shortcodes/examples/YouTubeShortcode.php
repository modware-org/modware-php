<?php
namespace Integrations\Shortcodes\Examples;

class YouTubeShortcode {
    public static function register() {
        \Integrations\Shortcodes\ShortcodeProcessor::register('youtube', [self::class, 'render']);
    }

    public static function render($attrs) {
        $defaults = [
            'id' => '',
            'width' => '560',
            'height' => '315',
            'autoplay' => '0',
            'controls' => '1'
        ];

        $attrs = array_merge($defaults, $attrs);
        
        return sprintf(
            '<div class="video-container">
                <iframe 
                    width="%s" 
                    height="%s" 
                    src="https://www.youtube.com/embed/%s?autoplay=%s&controls=%s" 
                    frameborder="0" 
                    allowfullscreen>
                </iframe>
            </div>',
            htmlspecialchars($attrs['width']),
            htmlspecialchars($attrs['height']),
            htmlspecialchars($attrs['id']),
            htmlspecialchars($attrs['autoplay']),
            htmlspecialchars($attrs['controls'])
        );
    }
}

// Usage example in a section:
// [youtube id="VIDEO_ID" width="560" height="315" autoplay="0" controls="1"]
