<?php
namespace Integrations\Webhooks;

class WebhookProcessor {
    private static $db;
    private static $events = [];

    public static function init($db) {
        self::$db = $db;
    }

    public static function registerEvent($eventName, $callback) {
        self::$events[$eventName] = $callback;
    }

    public static function trigger($eventName, $data = []) {
        if (!self::$db) {
            return false;
        }

        $stmt = self::$db->prepare("
            SELECT id, url, secret_key 
            FROM webhooks 
            WHERE is_active = 1 
            AND JSON_CONTAINS(events, ?)
        ");

        $stmt->execute([json_encode($eventName)]);
        $webhooks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($webhooks as $webhook) {
            self::sendWebhook($webhook, $eventName, $data);
        }

        // Execute local event handler if registered
        if (isset(self::$events[$eventName])) {
            call_user_func(self::$events[$eventName], $data);
        }
    }

    private static function sendWebhook($webhook, $eventName, $data) {
        $payload = json_encode([
            'event' => $eventName,
            'timestamp' => time(),
            'data' => $data
        ]);

        $signature = hash_hmac('sha256', $payload, $webhook['secret_key']);

        $ch = curl_init($webhook['url']);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-Webhook-Signature: ' . $signature
            ]
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Update last triggered timestamp
        $stmt = self::$db->prepare("
            UPDATE webhooks 
            SET last_triggered = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$webhook['id']]);

        return $status >= 200 && $status < 300;
    }
}

// Usage example:
// 1. Register event handler:
// WebhookProcessor::registerEvent('content.updated', function($data) {
//     // Handle content update locally
// });
//
// 2. Trigger webhook in section:
// WebhookProcessor::trigger('content.updated', [
//     'section_id' => $sectionId,
//     'type' => 'update',
//     'changes' => $changes
// ]);
