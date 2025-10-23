<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$firestore = $app->make(\App\Services\FirestoreRestService::class);

echo "Testing queryDocuments với orderBy...\n\n";

$result = $firestore->queryDocuments(
    'transactions',
    [
        'user_id' => ['value' => 'cfT4zfDX4YRkuwd4T6X3seJhtbl1', 'op' => 'EQUAL'],
        'amount' => ['value' => 50000, 'op' => 'EQUAL'],
        'status' => ['value' => 'pending', 'op' => 'EQUAL']
    ],
    ['field' => 'created_at', 'direction' => 'DESCENDING'],
    1
);

echo "Success: " . ($result['success'] ? 'Yes' : 'No') . "\n";
echo "Documents found: " . count($result['documents'] ?? []) . "\n";

if (!empty($result['documents'])) {
    echo "First document ID: " . $result['documents'][0]['id'] . "\n";
    echo "Created at: " . ($result['documents'][0]['data']['created_at'] ?? 'N/A') . "\n";
} else {
    echo "Error: " . ($result['error'] ?? 'Unknown') . "\n";
}
