<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirestoreRestService
{
    protected $projectId;
    protected $baseUrl;
    public $accessToken;

    public function __construct()
    {
        $this->projectId = config('firebase.project_id');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * 🔐 Lấy access token từ service account JSON
     */
    protected function getAccessToken()
    {
        try {
            $serviceAccountPath = config('firebase.credentials_file');
            if (!file_exists($serviceAccountPath)) {
                Log::error('❌ Service account file not found: ' . $serviceAccountPath);
                return null;
            }

            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);

            $now = time();
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'sub' => $serviceAccount['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/datastore'
            ];

            $jwt = $this->createJWT($payload, $serviceAccount['private_key']);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if ($response->successful()) {
                $token = $response->json()['access_token'];
                Log::info('✅ Got Firestore access token');
                return $token;
            }

            Log::error('❌ Failed to get access token: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('❌ Error getting access token: ' . $e->getMessage());
            return null;
        }
    }

    protected function createJWT($payload, $privateKey)
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

        $signature = '';
        openssl_sign(
            $base64UrlHeader . '.' . $base64UrlPayload,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        $base64UrlSignature = $this->base64UrlEncode($signature);
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // ==============================================================
    // 🔹 CRUD OPERATIONS
    // ==============================================================

    public function createDocument($collection, $documentId, $data)
    {
        try {
            $url = "{$this->baseUrl}/{$collection}?documentId={$documentId}";
            $fields = $this->convertToFirestoreFields($data);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($url, ['fields' => $fields]);

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('❌ Error creating Firestore document: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getDocument($collection, $documentId)
    {
        try {
            $url = "{$this->baseUrl}/{$collection}/{$documentId}";
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken
            ])->get($url);

            if ($response->successful()) {
                $json = $response->json();
                return [
                    'success' => true,
                    'data' => $this->convertFromFirestoreFields($json['fields'] ?? [])
                ];
            }

            return ['success' => false, 'error' => 'Document not found'];
        } catch (\Exception $e) {
            Log::error('❌ Error getting document: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateDocument($collection, $documentId, $data)
    {
        try {
            $url = "{$this->baseUrl}/{$collection}/{$documentId}";
            $fields = $this->convertToFirestoreFields($data);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->patch($url, ['fields' => $fields]);

            return $response->successful()
                ? ['success' => true, 'data' => $response->json()]
                : ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('❌ Error updating document: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ==============================================================
    // 🔹 FIRESTORE ENCODING HELPERS (Fixed arrayValue format)
    // ==============================================================

    protected function convertToFirestoreFields($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[$key] = $this->convertSingleValue($value);
        }
        return $fields;
    }

    protected function convertSingleValue($value)
    {
        if (is_null($value)) return ['nullValue' => null];
        if (is_bool($value)) return ['booleanValue' => $value];
        if (is_int($value)) return ['integerValue' => (string)$value];
        if (is_float($value)) return ['doubleValue' => $value];
        if (is_string($value)) return ['stringValue' => $value];
        if ($value instanceof \stdClass) return ['mapValue' => (object) []];

        // ✅ FIXED: đúng định dạng arrayValue trống
        if (is_array($value)) {
            if (array_keys($value) === range(0, count($value) - 1)) {
                return [
                    'arrayValue' => [
                        'values' => empty($value)
                            ? [] // Đúng định dạng JSON Firestore
                            : array_map(fn($v) => $this->convertSingleValue($v), $value)
                    ]
                ];
            }

            $fields = [];
            foreach ($value as $k => $v) {
                $fields[$k] = $this->convertSingleValue($v);
            }
            return ['mapValue' => ['fields' => $fields]];
        }

        if (is_object($value)) {
            $arr = (array) $value;
            if (array_keys($arr) === range(0, count($arr) - 1)) {
                return [
                    'arrayValue' => [
                        'values' => array_map(fn($v) => $this->convertSingleValue($v), $arr)
                    ]
                ];
            }
            return ['mapValue' => ['fields' => $this->convertToFirestoreFields($arr)]];
        }

        return ['stringValue' => strval($value)];
    }

    protected function convertFromFirestoreFields($fields)
    {
        $data = [];
        foreach ($fields as $key => $value) {
            $data[$key] = $this->convertFromFirestoreValue($value);
        }
        return $data;
    }

    protected function convertFromFirestoreValue($value)
    {
        if (isset($value['stringValue'])) return $value['stringValue'];
        if (isset($value['integerValue'])) return (int)$value['integerValue'];
        if (isset($value['doubleValue'])) return $value['doubleValue'];
        if (isset($value['booleanValue'])) return $value['booleanValue'];
        if (isset($value['nullValue'])) return null;

        if (isset($value['mapValue']['fields'])) {
            return $this->convertFromFirestoreFields($value['mapValue']['fields']);
        }

        if (isset($value['arrayValue']['values'])) {
            return array_map(fn($v) => $this->convertFromFirestoreValue($v), $value['arrayValue']['values']);
        }

        return [];
    }

    // ==============================================================
    // 🔹 CUSTOM UTILITIES
    // ==============================================================

    public function updateCoinsOnly($userId, $newCoinsValue)
    {
        try {
            $url = "{$this->baseUrl}/users/{$userId}";
            $fields = ['coins' => ['integerValue' => (int)$newCoinsValue]];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->patch($url, ['fields' => $fields]);

            if ($response->successful()) {
                Log::info("💰 Updated user coins: {$userId} -> {$newCoinsValue}");
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('❌ Error updating coins: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createSubDocument($parentPath, $subCollection, $docId, $data)
    {
        try {
            $url = "{$this->baseUrl}/{$parentPath}/{$subCollection}/{$docId}";
            $body = ['fields' => $this->convertToFirestoreFields($data)];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json'
            ])->put($url, $body);

            if ($response->successful()) {
                Log::info("✅ Subdocument created: {$parentPath}/{$subCollection}/{$docId}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error('❌ Failed to create subdocument', [
                'path' => "{$parentPath}/{$subCollection}/{$docId}",
                'status' => $response->status(),
                'error' => $response->body()
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('❌ createSubDocument error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }



/**
 * 🔹 Xóa document
 */
public function deleteDocument($collection, $documentId)
{
    try {
        $url = "{$this->baseUrl}/{$collection}/{$documentId}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->delete($url);

        if ($response->successful() || $response->status() == 404) {
            Log::info("🗑️ Document deleted: {$collection}/{$documentId}");
            return ['success' => true];
        }

        Log::warning("⚠️ Failed to delete document", [
            'collection' => $collection,
            'document_id' => $documentId,
            'status' => $response->status(),
            'response' => $response->body()
        ]);

        return ['success' => false, 'error' => $response->body()];
    } catch (\Exception $e) {
        Log::error('❌ Error deleting document: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}



}
