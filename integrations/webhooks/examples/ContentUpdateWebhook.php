<?php
namespace Integrations\Webhooks\Examples;

class ContentUpdateWebhook {
    public static function register() {
        // Register event handlers for content updates
        \Integrations\Webhooks\WebhookProcessor::registerEvent('content.created', [self::class, 'handleContentCreate']);
        \Integrations\Webhooks\WebhookProcessor::registerEvent('content.updated', [self::class, 'handleContentUpdate']);
        \Integrations\Webhooks\WebhookProcessor::registerEvent('content.deleted', [self::class, 'handleContentDelete']);
    }

    public static function handleContentCreate($data) {
        return [
            'event_type' => 'content.created',
            'timestamp' => time(),
            'content' => [
                'id' => $data['content_id'],
                'type' => $data['content_type'],
                'title' => $data['title'] ?? null,
                'section' => $data['section'] ?? null,
                'created_by' => $data['user_id'] ?? null
            ]
        ];
    }

    public static function handleContentUpdate($data) {
        return [
            'event_type' => 'content.updated',
            'timestamp' => time(),
            'content' => [
                'id' => $data['content_id'],
                'type' => $data['content_type'],
                'changes' => $data['changes'] ?? [],
                'section' => $data['section'] ?? null,
                'updated_by' => $data['user_id'] ?? null
            ]
        ];
    }

    public static function handleContentDelete($data) {
        return [
            'event_type' => 'content.deleted',
            'timestamp' => time(),
            'content' => [
                'id' => $data['content_id'],
                'type' => $data['content_type'],
                'section' => $data['section'] ?? null,
                'deleted_by' => $data['user_id'] ?? null
            ]
        ];
    }
}

// Usage example in sections:
/*
// In your section's query.php when content is updated:
WebhookProcessor::trigger('content.updated', [
    'content_id' => $contentId,
    'content_type' => 'section',
    'section' => $sectionName,
    'user_id' => $currentUserId,
    'changes' => [
        'title' => [
            'old' => $oldTitle,
            'new' => $newTitle
        ],
        'content' => [
            'old' => $oldContent,
            'new' => $newContent
        ]
    ]
]);

// Configure webhook in admin panel to send to external URL:
// e.g., https://api.example.com/webhooks/content-updates
// The external service will receive JSON payloads for all content changes
*/
