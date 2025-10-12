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
        $fields = $this->formatFields($data);

        $url = "{$this->baseUrl}/{$collection}?key={$this->apiKey}";
        $res = Http::post($url, ['fields' => $fields]);

        if ($res->failed()) {
            throw new \Exception('Firestore createDocument error: ' . $res->body());
        }

        $response = $res->json();
        return basename($response['name'] ?? '');
    }

    /**
     * 📄 Lấy một document theo ID
     */
    public function getDocument(string $collection, string $documentId)
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}?key={$this->apiKey}";
        $res = Http::get($url);

        if ($res->status() === 404) {
            return null;
        }

        if ($res->failed()) {
            throw new \Exception('Firestore getDocument error: ' . $res->body());
        }

        $data = $res->json();
        return $this->parseFields($data['fields'] ?? []);
    }

    /**
     * 🔄 Cập nhật document với validation
     */
    public function updateDocument(string $collection, string $documentId, array $data)
    {
        // Validation cơ bản
        $this->validateUpdateData($collection, $data);

        $fields = $this->formatFields($data);

        $url = "{$this->baseUrl}/{$collection}/{$documentId}?key={$this->apiKey}";
        $res = Http::patch($url, ['fields' => $fields]);

        if ($res->failed()) {
            throw new \Exception('Firestore updateDocument error: ' . $res->body());
        }

        // Log data mutation để audit
        \Illuminate\Support\Facades\Log::info('Firestore document updated', [
            'collection' => $collection,
            'document_id' => $documentId,
            'fields' => array_keys($data),
            'timestamp' => now()
        ]);

        return $res->json();
    }

    /**
     * ✅ Validation cho dữ liệu cập nhật
     */
    private function validateUpdateData(string $collection, array $data)
    {
        if (empty($data)) {
            throw new \InvalidArgumentException('Update data cannot be empty');
        }

        // Validation riêng cho collection users
        if ($collection === 'users') {
            $allowedFields = ['avatar', 'coins', 'email', 'name', 'role', 'restored_at', 'restored_by'];

            foreach (array_keys($data) as $field) {
                if (!in_array($field, $allowedFields)) {
                    throw new \InvalidArgumentException("Invalid field '{$field}' for users collection");
                }
            }

            // Validation kiểu dữ liệu
            if (isset($data['coins']) && !is_numeric($data['coins'])) {
                throw new \InvalidArgumentException('Field "coins" must be numeric');
            }

            if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Field "email" must be a valid email address');
            }

            if (isset($data['role']) && !in_array($data['role'], ['admin', 'saler', 'user'])) {
                throw new \InvalidArgumentException('Field "role" must be one of: admin, saler, user');
            }
        }
    }

    /**
     * 🗑️ Xóa document
     */
    public function deleteDocument(string $collection, string $documentId)
    {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}?key={$this->apiKey}";
        $res = Http::delete($url);

        if ($res->failed()) {
            throw new \Exception('Firestore deleteDocument error: ' . $res->body());
        }

        return true;
    }

    /**
     * 🔍 Query documents với điều kiện
     */
    public function queryDocuments(string $collection, array $query = [])
    {
        try {
            // Simple implementation - get all documents then filter client-side
            $response = $this->listDocuments($collection, 1000);
            $documents = $response['documents'] ?? [];

            $results = [];
            foreach ($documents as $doc) {
                $data = $this->parseFields($doc['fields'] ?? []);
                $id = basename($doc['name'] ?? '');

                // Apply where conditions
                if (isset($query['where'])) {
                    $matches = true;
                    foreach ($query['where'] as $condition) {
                        [$field, $operator, $value] = $condition;
                        if (!$this->matchesCondition($data, $field, $operator, $value)) {
                            $matches = false;
                            break;
                        }
                    }
                    if (!$matches) continue;
                }

                $results[] = ['id' => $id, 'data' => $data];
            }

            // Apply ordering
            if (isset($query['orderBy'])) {
                foreach (array_reverse($query['orderBy']) as $order) {
                    [$field, $direction] = $order;
                    usort($results, function ($a, $b) use ($field, $direction) {
                        $aVal = $a['data'][$field] ?? '';
                        $bVal = $b['data'][$field] ?? '';
                        $cmp = strcmp($aVal, $bVal);
                        return $direction === 'desc' ? -$cmp : $cmp;
                    });
                }
            }

            // Apply limit
            if (isset($query['limit'])) {
                $results = array_slice($results, 0, $query['limit']);
            }

            return $results;
        } catch (\Exception $e) {
            throw new \Exception('Firestore queryDocuments error: ' . $e->getMessage());
        }
    }

    /**
     * Format data to Firestore field format
     */
    private function formatFields(array $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $fields[$key] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }

        return $fields;
    }

    /**
     * Parse Firestore fields to PHP array
     */
    private function parseFields(array $fields)
    {
        $data = [];

        foreach ($fields as $key => $field) {
            if (isset($field['stringValue'])) {
                $data[$key] = $field['stringValue'];
            } elseif (isset($field['integerValue'])) {
                $data[$key] = (int)$field['integerValue'];
            } elseif (isset($field['doubleValue'])) {
                $data[$key] = (float)$field['doubleValue'];
            } elseif (isset($field['booleanValue'])) {
                $data[$key] = $field['booleanValue'];
            } elseif (isset($field['nullValue'])) {
                $data[$key] = null;
            } else {
                $data[$key] = '';
            }
        }

        return $data;
    }

    /**
     * Check if a document matches a condition
     */
    private function matchesCondition(array $data, string $field, string $operator, $value)
    {
        $fieldValue = $data[$field] ?? null;

        switch ($operator) {
            case '==':
                return $fieldValue == $value;
            case '!=':
                return $fieldValue != $value;
            case '>':
                return $fieldValue > $value;
            case '>=':
                return $fieldValue >= $value;
            case '<':
                return $fieldValue < $value;
            case '<=':
                return $fieldValue <= $value;
            default:
                return false;
        }
    }
}
