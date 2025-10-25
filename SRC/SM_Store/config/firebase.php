<?php

return [
    // ✅ Cấu hình Firebase cho cả Auth và Firestore
    'credentials' => env('FIREBASE_CREDENTIALS', resource_path('key/pttkpm-65c1f-firebase-adminsdk-s5z70-b89dd6e903.json')),
    
    // ✅ Thông tin project
    'project_id' => env('FIREBASE_PROJECT_ID', 'pttkpm-65c1f'),

    // ✅ File credentials cho FirestoreRestService
    'credentials_file' => env('FIREBASE_CREDENTIALS', resource_path('key/pttkpm-65c1f-firebase-adminsdk-s5z70-b89dd6e903.json')),
];
