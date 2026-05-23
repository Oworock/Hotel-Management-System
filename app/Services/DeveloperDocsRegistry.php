<?php

namespace App\Services;

class DeveloperDocsRegistry
{
    protected static array $apis = [];
    protected static array $webhooks = [];

    /**
     * Register an API endpoint.
     */
    public static function registerApi(string $group, array $details): void
    {
        self::$apis[$group][] = $details;
    }

    /**
     * Register a Webhook event.
     */
    public static function registerWebhook(string $group, array $details): void
    {
        self::$webhooks[$group][] = $details;
    }

    /**
     * Get all registered API endpoints.
     */
    public static function getApis(): array
    {
        return self::$apis;
    }

    /**
     * Get all registered Webhook events.
     */
    public static function getWebhooks(): array
    {
        return self::$webhooks;
    }
}
