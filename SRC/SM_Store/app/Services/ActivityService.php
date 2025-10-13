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
     * Tạo activity notification
     */
    public function createActivity(string $userUid, string $type, string $message, array $data = [])
    {
        try {
            $now = now();

            // Determine title based on type for consistency with existing activities
            $title = '';
            switch ($type) {
                case 'upload':
                    $title = 'Tải lên sheet nhạc mới';
                    break;
                case 'update':
                    $title = 'Cập nhật sheet nhạc';
                    break;
                case 'delete':
                    $title = 'Xóa sheet nhạc';
                    break;
                case 'purchase':
                    $title = 'Mua sheet thành công';
                    break;
                case 'sale':
                    $title = 'Bán sheet thành công';
                    break;
                default:
                    $title = ucfirst($type);
            }

            // Use existing structure format to match current activities
            $activityData = array_merge([
                'user_id' => $userUid,  // Use user_id to match existing structure
                'type' => $type,
                'title' => $title,
                'description' => $message,
                'created_at' => $now->toISOString(),
                'read' => false
            ], $data);

            Log::info('Creating activity with existing structure', [
                'activity_data' => $activityData
            ]);

            $activityId = $this->firestore->createDocument('activities', $activityData);

            if ($activityId) {
                Log::info('Activity created successfully', [
                    'activity_id' => $activityId,
                    'user_uid' => $userUid,
                    'type' => $type
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
     * Lấy activities của user
     */
    public function getUserActivities(string $userUid, int $limit = 20)
    {
        try {
            // Use where condition format that FirestoreSimple expects
            // Note: existing activities use 'user_id' field
            $query = [
                'where' => [
                    ['user_id', '==', $userUid]
                ],
                'limit' => $limit * 2 // Get more to ensure we have enough after filtering
            ];

            $results = $this->firestore->queryDocuments('activities', $query);

            if (empty($results)) {
                return [];
            }

            $userActivities = [];
            foreach ($results as $result) {
                $activity = $result['data'] ?? [];
                $activity['id'] = $result['id'] ?? '';
                $userActivities[] = $activity;
            }

            // Sort by created_at timestamp (ISO format) descending for precise chronological order
            usort($userActivities, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');

                // Descending order (newest first)
                return $timeB - $timeA;
            });

            // Limit results
            return array_slice($userActivities, 0, $limit);
        } catch (\Exception $e) {
            Log::error('Error getting user activities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Đánh dấu activity đã đọc
     */
    public function markAsRead(string $activityId)
    {
        try {
            return $this->firestore->updateDocument('activities', $activityId, ['read' => true]);
        } catch (\Exception $e) {
            Log::error('Error marking activity as read: ' . $e->getMessage());
            return false;
        }
    }
}
