<?php

return [
    /**
     * メールによるアクション通知機能を有効にする場合はtrueを設定する
     */
    'enabled' => env('NOTIFICATION_ENABLE', false),

    /**
     * Slack webhookを有効にする場合はURLを設定する
     */
    'slack' => [
        'enabled' => env('SLACK_NOTIFICATION_ENABLE', null),
        'webhook_url' => env('SLACK_WEBHOOK_URL', null),
    ]
];
