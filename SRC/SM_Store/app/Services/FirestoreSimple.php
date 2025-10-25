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
     * 🧾 Lấy danh sách document trong collection (hỗ trợ subcollection)
     */
    public function listDocuments(string $collection, int $pageSize = 50)
    {
        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}?pageSize={$pageSize}";
        
        \Illuminate\Support\Facades\Log::info('Listing documents from Firestore', [
            'collection_path' => $collectionPath,
            'url' => $url
        ]);
        
        $res = Http::get($url);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore listDocuments failed', [
                'collection_path' => $collectionPath,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception('Firestore listDocuments error: ' . $res->body());
        }

        $response = $res->json();
        \Illuminate\Support\Facades\Log::info('Documents listed successfully', [
            'collection_path' => $collectionPath,
            'count' => count($response['documents'] ?? [])
        ]);

        return $response;
    }

    /**
     * ➕ Thêm document mới vào Firestore (hỗ trợ subcollection)
     */
    public function createDocument(string $collection, array $data)
    {
        $fields = $this->formatFields($data);

        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}?key={$this->apiKey}";
        
        \Illuminate\Support\Facades\Log::info('Creating document in Firestore', [
            'collection_path' => $collectionPath,
            'url' => $url,
            'data_keys' => array_keys($data)
        ]);
        
        $res = Http::post($url, ['fields' => $fields]);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore createDocument failed', [
                'collection_path' => $collectionPath,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception('Firestore createDocument error: ' . $res->body());
        }

        $response = $res->json();
        $documentId = basename($response['name'] ?? '');
        
        \Illuminate\Support\Facades\Log::info('Document created successfully', [
            'collection_path' => $collectionPath,
            'document_id' => $documentId
        ]);
        
        return $documentId;
    }

    /**
     * 📄 Tạo document với ID cụ thể (hỗ trợ subcollection)
     */
    public function createDocumentWithId(string $collection, string $documentId, array $data)
    {
        $fields = $this->formatFields($data);

        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}/{$documentId}?key={$this->apiKey}";
        
        \Illuminate\Support\Facades\Log::info('Creating document with ID in Firestore', [
            'collection_path' => $collectionPath,
            'document_id' => $documentId,
            'url' => $url
        ]);
        
        $res = Http::patch($url, ['fields' => $fields]);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore createDocumentWithId failed', [
                'collection_path' => $collectionPath,
                'document_id' => $documentId,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception("Firestore createDocumentWithId error: " . $res->body());
        }

        \Illuminate\Support\Facades\Log::info('Document with ID created successfully', [
            'collection_path' => $collectionPath,
            'document_id' => $documentId
        ]);

        return $documentId;
    }

    /**
     * 📄 Lấy một document theo ID (hỗ trợ subcollection)
     */
    public function getDocument(string $collection, string $documentId)
    {
        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}/{$documentId}?key={$this->apiKey}";
        
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
     * 🔄 Cập nhật document với validation (FIX: sử dụng updateMask để tránh ghi đè) - hỗ trợ subcollection
     */
    public function updateDocument(string $collection, string $documentId, array $data)
    {
        // Validation cơ bản
        $this->validateUpdateData($collection, $data);

        $fields = $this->formatFields($data);

        // ✅ FIX: Sử dụng updateMask để chỉ update các field cần thiết
        $fieldPaths = array_map(function ($field) {
            return "updateMask.fieldPaths={$field}";
        }, array_keys($data));
        $updateMaskQuery = implode('&', $fieldPaths);
        
        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}/{$documentId}?{$updateMaskQuery}&key={$this->apiKey}";

        $res = Http::patch($url, ['fields' => $fields]);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore updateDocument failed', [
                'collection_path' => $collectionPath,
                'document_id' => $documentId,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception('Firestore updateDocument error: ' . $res->body());
        }

        // Log data mutation để audit
        \Illuminate\Support\Facades\Log::info('Firestore document updated', [
            'collection_path' => $collectionPath,
            'document_id' => $documentId,
            'fields' => array_keys($data),
            'update_mask' => $updateMaskQuery,
            'timestamp' => now()
        ]);

        return $res->json();
    }

    /**
     * 🔒 Cập nhật document an toàn (chỉ update field coins mà không ghi đè)
     */
    public function updateCoinsOnly(string $documentId, int $newCoins)
    {
        // Validation cơ bản
        if (empty($documentId)) {
            throw new \InvalidArgumentException('Document ID cannot be empty');
        }

        if ($newCoins < 0) {
            throw new \InvalidArgumentException('Coins cannot be negative');
        }

        // Kiểm tra document có tồn tại không trước khi update
        $existingDoc = $this->getDocument('users', $documentId);
        if (!$existingDoc) {
            throw new \Exception("User document {$documentId} not found");
        }

        // Log trạng thái trước khi update
        \Illuminate\Support\Facades\Log::info('Updating coins safely', [
            'document_id' => $documentId,
            'old_coins' => $existingDoc['coins'] ?? 0,
            'new_coins' => $newCoins,
            'user_name' => $existingDoc['name'] ?? 'Unknown',
            'timestamp' => now()
        ]);

        // Chỉ cập nhật field coins sử dụng updateMask để đảm bảo chỉ field này được update
        $data = ['coins' => $newCoins];
        $fields = $this->formatFields($data);

        // Sử dụng updateMask để chỉ định field coins
        $url = "{$this->baseUrl}/users/{$documentId}?updateMask.fieldPaths=coins&key={$this->apiKey}";
        $res = Http::patch($url, ['fields' => $fields]);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore updateCoinsOnly failed', [
                'document_id' => $documentId,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception('Firestore updateCoinsOnly error: ' . $res->body());
        }

        // Log thành công
        \Illuminate\Support\Facades\Log::info('Firestore coins updated successfully', [
            'document_id' => $documentId,
            'new_coins' => $newCoins,
            'user_name' => $existingDoc['name'] ?? 'Unknown',
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
     * 🗑️ Xóa document (hỗ trợ subcollection)
     */
    public function deleteDocument(string $collection, string $documentId)
    {
        // Xử lý subcollection path (ví dụ: activities/{uid})
        $collectionPath = $this->normalizeCollectionPath($collection);
        $url = "{$this->baseUrl}/{$collectionPath}/{$documentId}?key={$this->apiKey}";
        
        $res = Http::delete($url);

        if ($res->failed()) {
            \Illuminate\Support\Facades\Log::error('Firestore deleteDocument failed', [
                'collection_path' => $collectionPath,
                'document_id' => $documentId,
                'error' => $res->body(),
                'status' => $res->status()
            ]);
            throw new \Exception('Firestore deleteDocument error: ' . $res->body());
        }

        \Illuminate\Support\Facades\Log::info('Document deleted successfully', [
            'collection_path' => $collectionPath,
            'document_id' => $documentId
        ]);

        return true;
    }

    /**
     * 🔍 Query documents với điều kiện (hỗ trợ subcollection)
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

    /**
     * 🔧 Normalize collection path để hỗ trợ subcollection
     * Ví dụ: activities/{uid} -> activities/{uid}
     * Ví dụ: purchases/{uid}/sheets -> purchases/{uid}/sheets
     */
    private function normalizeCollectionPath(string $collection): string
    {
        // Nếu collection không chứa '/' thì trả về nguyên vẹn
        if (strpos($collection, '/') === false) {
            return $collection;
        }
        
        // Nếu chứa '/' thì đây là subcollection path
        return $collection;
    }

    /**
     * Get all users from Firestore
     */
    public function getAllUsers()
    {
        try {
            $url = $this->baseUrl . '/users?key=' . $this->apiKey;

            $response = Http::get($url);
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch users: ' . $response->body());
            }

            $data = $response->json();
            $users = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $doc) {
                    $pathParts = explode('/', $doc['name']);
                    $documentId = end($pathParts);

                    if (isset($doc['fields'])) {
                        $users[$documentId] = $this->parseFields($doc['fields']);
                    }
                }
            }

            return $users;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching all users: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all products from Firestore
     */
    public function getAllProducts()
    {
        try {
            $url = $this->baseUrl . '/products?key=' . $this->apiKey;

            $response = Http::get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch products: ' . $response->body());
            }

            $data = $response->json();
            $products = [];

            if (isset($data['documents'])) {
                foreach ($data['documents'] as $doc) {
                    $pathParts = explode('/', $doc['name']);
                    $documentId = end($pathParts);

                    if (isset($doc['fields'])) {
                        $products[$documentId] = $this->parseFields($doc['fields']);
                    }
                }
            }

            return $products;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching all products: ' . $e->getMessage());
            return [];
        }
    }
}
