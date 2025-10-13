<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreSimple();
    }

    /**
     * Create a new user in Firestore with duplicate checking
     */
    public function createUser(array $userData): ?string
    {
        // Check for required fields
        if (!isset($userData['email'])) {
            throw new \InvalidArgumentException('Email is required');
        }

        // Check if email already exists
        if ($this->emailExists($userData['email'])) {
            Log::warning('Attempted to create user with existing email', [
                'email' => $userData['email']
            ]);
            throw new \Exception('Email already exists: ' . $userData['email']);
        }

        // Add default fields
        $userData['created_at'] = now()->toISOString();
        $userData['updated_at'] = now()->toISOString();

        try {
            $documentId = $this->firestore->createDocument('users', $userData);

            Log::info('User created successfully', [
                'document_id' => $documentId,
                'email' => $userData['email']
            ]);

            return $documentId;
        } catch (\Exception $e) {
            Log::error('Error creating user', [
                'email' => $userData['email'],
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if email already exists in Firestore
     */
    public function emailExists(string $email): bool
    {
        try {
            $response = $this->firestore->listDocuments('users', 100);

            if (!isset($response['documents'])) {
                return false;
            }

            foreach ($response['documents'] as $doc) {
                if (isset($doc['fields']['email']['stringValue'])) {
                    if ($doc['fields']['email']['stringValue'] === $email) {
                        return true;
                    }
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error checking email existence', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false; // Assume doesn't exist if we can't check
        }
    }

    /**
     * Update existing user
     */
    public function updateUser(string $documentId, array $updateData): bool
    {
        // Add updated timestamp
        $updateData['updated_at'] = now()->toISOString();

        try {
            $result = $this->firestore->updateDocument('users', $documentId, $updateData);

            Log::info('User updated successfully', [
                'document_id' => $documentId,
                'fields_updated' => array_keys($updateData)
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'document_id' => $documentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?array
    {
        try {
            $response = $this->firestore->listDocuments('users', 100);

            if (!isset($response['documents'])) {
                return null;
            }

            foreach ($response['documents'] as $doc) {
                $userData = [];
                $docPath = $doc['name'] ?? '';
                $userData['id'] = basename($docPath);

                // Extract fields
                if (isset($doc['fields'])) {
                    foreach ($doc['fields'] as $field => $value) {
                        if (isset($value['stringValue'])) {
                            $userData[$field] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $userData[$field] = (int) $value['integerValue'];
                        } elseif (isset($value['booleanValue'])) {
                            $userData[$field] = $value['booleanValue'];
                        } elseif (isset($value['timestampValue'])) {
                            $userData[$field] = $value['timestampValue'];
                        } elseif (isset($value['doubleValue'])) {
                            $userData[$field] = (float) $value['doubleValue'];
                        }
                    }
                }

                if (($userData['email'] ?? '') === $email) {
                    return $userData;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting user by email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Sync user from Firebase Auth to Firestore (safe method)
     */
    public function syncUserFromAuth(string $uid, array $authUserData): ?string
    {
        $email = $authUserData['email'] ?? null;
        if (!$email) {
            throw new \InvalidArgumentException('Email is required for sync');
        }

        // Check if user already exists
        if ($this->emailExists($email)) {
            Log::info('User already exists in Firestore, skipping sync', [
                'email' => $email,
                'uid' => $uid
            ]);
            return null;
        }

        // Create user data
        $userData = [
            'uid' => $uid,
            'email' => $email,
            'name' => $authUserData['displayName'] ?? $authUserData['name'] ?? 'User',
            'role' => $authUserData['role'] ?? 'customer',
            'avatar' => $authUserData['photoURL'] ?? null,
            'coins' => $authUserData['coins'] ?? 0,
        ];

        return $this->createUser($userData);
    }
}
