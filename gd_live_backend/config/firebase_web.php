<?php

return [
    'api_key' => env('FIREBASE_WEB_API_KEY', ''),
    'auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN', ''),
    'project_id' => env('FIREBASE_WEB_PROJECT_ID', env('FIREBASE_PROJECT_ID', '')),
    'storage_bucket' => env('FIREBASE_WEB_STORAGE_BUCKET', ''),
    'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID', ''),
    'app_id' => env('FIREBASE_WEB_APP_ID', ''),
];
