<?php

declare(strict_types=1);

function log_activity(string $action, ?string $entityType = null, ?int $entityId = null, array $metadata = []): void
{
    try {
        $userId = auth_admin()['id'] ?? auth_customer()['id'] ?? null;

        Core\Database::getInstance()->insert(
            'INSERT INTO activity_logs (user_id, action, entity_type, entity_id, ip_address, user_agent, metadata)
             VALUES (:user_id, :action, :entity_type, :entity_id, :ip_address, :user_agent, :metadata)',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
                'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_THROW_ON_ERROR),
            ]
        );
    } catch (Throwable) {
        // Non-blocking audit trail.
    }
}
