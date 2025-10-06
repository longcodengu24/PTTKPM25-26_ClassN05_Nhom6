<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirestoreSimple
{
    protected ?string $projectId;
    protected string $database;
    protected string $baseUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        // Lấy thông tin từ .env
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->database  = '(default)';
        $this->apiKey    = env('FIREBASE_API_KEY');

        // Kiểm tra các biến môi trường bắt buộc
        if (empty($this->projectId)) {
            throw new \Exception('FIREBASE_PROJECT_ID is not configured in .env file');
        }

        if (empty($this->apiKey)) {
            throw new \Exception('FIREBASE_API_KEY is not configured in .env file');
        }

        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/{$this->database}/documents";
    }

    /**
     * 🧾 Lấy danh sách document trong collection
     */
    public function listDocuments(string $collection, int $pageSize = 50)
    {
        $url = "{$this->baseUrl}/{$collection}?pageSize={$pageSize}";
        $res = Http::get($url);

        if ($res->failed()) {
            throw new \Exception('Firestore listDocuments error: ' . $res->body());
        }

        return $res->json();
    }

    /**
     * ➕ Thêm document mới vào Firestore
     */
    public function createDocument(string $collection, array $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }

        $url = "{$this->baseUrl}/{$collection}?key={$this->apiKey}";
        $res = Http::post($url, ['fields' => $fields]);

        if ($res->failed()) {
            throw new \Exception('Firestore createDocument error: ' . $res->body());
        }

        return $res->json();
    }
}