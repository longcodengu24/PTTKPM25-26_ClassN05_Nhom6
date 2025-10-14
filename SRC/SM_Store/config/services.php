<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials' => env('FIREBASE_CREDENTIALS'), // JSON string or path to service account
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    ],

    'sepay' => [
        'token' => env('SEPAY_TOKEN'),
        'secret_key' => env('SEPAY_SECRET_KEY'),
        'bank_id' => env('SEPAY_BANK_ID'),
        'account_id' => env('SEPAY_ACCOUNT_ID'),
        'webhook_url' => env('SEPAY_WEBHOOK_URL'),
    ],

];
