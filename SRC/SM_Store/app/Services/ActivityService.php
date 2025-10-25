<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class ActivityService
{
    protected $firestore;

    public function __construct()
    {
        $this->firestore = new FirestoreSimple();
    }

    /**
     * Tạo activity notification theo cấu trúc activities-{uid}
     */
    public function createActivity(string $userUid, string $type, string $message, array $data = [])
    {
        try {
            $now = now();

            // Determine title based on type for consistency with existing activities
            $title = '';
            switch ($type) {
                case 'purchase':
                    $title = 'Mua sheet thành công';
                    break;
                case 'deposit':
                    $title = 'Nạp Sky Coins';
                    break;
                case 'download':
                    $title = 'Bạn đã tải sheet nhạc về máy';
                    break;
                case 'sale':
                    $title = 'Bán sheet thành công';
                    break;
                default:
                    $title = ucfirst($type);
            }

            // Tạo activity data với cấu trúc mới
            $activityData = array_merge([
                'type' => $type,
                'title' => $title,
                'description' => $message,
                'created_at' => $now->toISOString(),
                'read' => false
            ], $data);

            // Đảm bảo các field số được convert đúng
            if (isset($activityData['amount'])) {
                $activityData['amount'] = floatval($activityData['amount']);
            }
            if (isset($activityData['total_amount'])) {
                $activityData['total_amount'] = floatval($activityData['total_amount']);
            }
            if (isset($activityData['balance'])) {
                $activityData['balance'] = floatval($activityData['balance']);
            }

            // Tạo collection name theo format: activities-{uid}
            $collectionName = "activities-{$userUid}";
            
            Log::info('Creating activity with collection structure', [
                'collection_name' => $collectionName,
                'activity_data' => $activityData
            ]);

            // Lưu activity vào collection riêng của user
            $activityId = $this->firestore->createDocument($collectionName, $activityData);

            if ($activityId) {
                Log::info('Activity created successfully', [
                    'activity_id' => $activityId,
                    'user_uid' => $userUid,
                    'type' => $type,
                    'collection' => $collectionName
                ]);
                return $activityId;
            } else {
                Log::error('Failed to create activity - createDocument returned false');
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error creating activity: ' . $e->getMessage(), [
                'user_uid' => $userUid,
                'type' => $type,
                'message' => $message,
                'exception' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Tạo activity cho việc mua hàng
     */
    public function createPurchaseActivity(string $buyerUid, string $sellerUid, string $productName, int $price)
    {
        // Tạo activity cho người mua trước (để có timestamp sớm hơn một chút)
        $this->createActivity(
            $buyerUid,
            'purchase',
            "Bạn đã mua sheet nhạc \"{$productName}\" với giá " . number_format($price) . " coins",
            [
                'product_name' => $productName,
                'price' => $price,
                'seller_uid' => $sellerUid,
                'transaction_type' => 'purchase'
            ]
        );

        // Chờ một chút để đảm bảo timestamp khác nhau
        usleep(1000); // 1ms delay

        // Activity cho người bán (timestamp sẽ muộn hơn một chút)
        $this->createActivity(
            $sellerUid,
            'sale',
            "Sheet nhạc \"{$productName}\" của bạn đã được mua với giá " . number_format($price) . " coins",
            [
                'product_name' => $productName,
                'price' => $price,
                'buyer_uid' => $buyerUid,
                'transaction_type' => 'sale'
            ]
        );
    }

    /**
     * Lấy activities của user từ collection activities-{uid}
     */
    public function getUserActivities(string $userUid, int $limit = 20)
    {
        try {
            // Tạo collection name theo format: activities-{uid}
            $collectionName = "activities-{$userUid}";
            
            // Lấy tất cả documents từ collection của user
            $response = $this->firestore->listDocuments($collectionName, 1000);
            $results = [];
            
            if (isset($response['documents'])) {
                foreach ($response['documents'] as $doc) {
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    
                    // Parse fields manually
                    foreach ($fields as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        } elseif (isset($field['timestampValue'])) {
                            $data[$key] = $field['timestampValue'];
                        }
                    }
                    
                    $id = basename($doc['name'] ?? '');
                    $results[] = ['id' => $id, 'data' => $data];
                }
            }

            if (empty($results)) {
                Log::info('No activities found for user', [
                    'user_uid' => $userUid,
                    'collection' => $collectionName
                ]);
                return [];
            }

            $userActivities = [];
            foreach ($results as $result) {
                $activity = $result['data'] ?? [];
                $activity['id'] = $result['id'] ?? '';
                
                // Chỉ thêm activity có đầy đủ thông tin cơ bản
                if (!empty($activity['type']) && !empty($activity['title']) && !empty($activity['created_at'])) {
                    $userActivities[] = $activity;
                }
            }

            // Sort by created_at timestamp (ISO format) descending for precise chronological order
            usort($userActivities, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');

                // Descending order (newest first)
                return $timeB - $timeA;
            });

            // Limit results
            $limitedActivities = array_slice($userActivities, 0, $limit);
            
            Log::info('Retrieved user activities', [
                'user_uid' => $userUid,
                'collection' => $collectionName,
                'total_found' => count($userActivities),
                'returned' => count($limitedActivities),
                'sample_activity' => $limitedActivities[0] ?? null
            ]);
            
            return $limitedActivities;
        } catch (\Exception $e) {
            Log::error('Error getting user activities: ' . $e->getMessage(), [
                'user_uid' => $userUid,
                'exception' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Đánh dấu activity đã đọc
     */
    public function markAsRead(string $userUid, string $activityId)
    {
        try {
            $collectionName = "activities-{$userUid}";
            return $this->firestore->updateDocument($collectionName, $activityId, ['read' => true]);
        } catch (\Exception $e) {
            Log::error('Error marking activity as read: ' . $e->getMessage());
            return false;
        }
    }
}
