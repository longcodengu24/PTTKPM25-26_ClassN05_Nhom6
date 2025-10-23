<?php

return [
    // Path cho Kreait Firebase package (Auth)
    'credentials' => env('FIREBASE_CREDENTIALS', resource_path('key/pttkpm-65c1f-firebase-adminsdk-s5z70-b89dd6e903.json')),
    
    // Config cho FirestoreRestService
    'project_id' => env('FIREBASE_PROJECT_ID', 'pttkpm-65c1f'),
    'credentials_file' => env('FIREBASE_CREDENTIALS', resource_path('key/pttkpm-65c1f-firebase-adminsdk-s5z70-b89dd6e903.json')),
];
