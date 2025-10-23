<?php

// Test payment status endpoint
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Đọc transaction ID từ file
$transactionId = trim(file_get_contents(__DIR__ . '/test_transaction_id.txt'));

// Test với transaction mới nhất
$request = Illuminate\Http\Request::create(
    '/api/payment/status/' . $transactionId,
    'GET'
);

$response = $kernel->handle($request);

echo "Transaction ID: " . $transactionId . "\n";
echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Response: " . $response->getContent() . "\n";

$kernel->terminate($request, $response);
