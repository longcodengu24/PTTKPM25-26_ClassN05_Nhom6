<?php

// Test create payment
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$data = [
    'amount' => 50000,
    'user_id' => 'cfT4zfDX4YRkuwd4T6X3seJhtbl1'
];

$request = Illuminate\Http\Request::create(
    '/api/payment/create',
    'POST',
    [], // parameters
    [], // cookies
    [], // files
    [], // server
    json_encode($data) // content
);
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

$response = $kernel->handle($request);

$responseData = json_decode($response->getContent(), true);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Transaction ID: " . ($responseData['data']['transaction_id'] ?? 'N/A') . "\n";
echo "Amount: " . ($responseData['data']['amount'] ?? 'N/A') . "\n";
echo "QR URL: " . (isset($responseData['data']['qr_url']) ? 'Yes' : 'No') . "\n";

// Save transaction ID for next test
if (isset($responseData['data']['transaction_id'])) {
    file_put_contents(__DIR__ . '/test_transaction_id.txt', $responseData['data']['transaction_id']);
    echo "\n✅ Transaction ID saved to test_transaction_id.txt\n";
}

$kernel->terminate($request, $response);
