# Hệ Thống Activities - Cấu Trúc Mới

## Tổng Quan

Hệ thống activities đã được cập nhật để lưu trữ các hoạt động của người dùng theo cấu trúc `activities-{uid}` trong Firestore, thay vì lưu tất cả trong một collection `activities` duy nhất.

## Cấu Trúc Dữ Liệu

### Collection Structure

-   **Cũ**: `activities` (tất cả activities của tất cả users)
-   **Mới**: `activities-{uid}` (activities riêng cho từng user)

### Activity Data Format

```json
{
    "type": "purchase|deposit|download",
    "title": "Tiêu đề hoạt động",
    "description": "Mô tả chi tiết",
    "created_at": "2024-01-01T00:00:00Z",
    "read": false
    // Các field bổ sung tùy theo loại activity
}
```

## Các Loại Activities

### 1. Purchase Activity (Mua Sheet)

-   **Type**: `purchase`
-   **Title**: "Mua sheet thành công"
-   **Fields**: `total_amount`, `items_count`, `transaction_id`, `sheet_ids`

### 2. Deposit Activity (Nạp Coins)

-   **Type**: `deposit`
-   **Title**: "Nạp Sky Coins"
-   **Fields**: `amount`, `sepay_id`, `reference_code`, `source`

### 3. Download Activity (Tải Sheet)

-   **Type**: `download`
-   **Title**: "Bạn đã tải sheet nhạc về máy"
-   **Fields**: `sheet_title`, `file_name`, `file_size`, `transaction_type`

## Cách Sử Dụng

### Tạo Activity

```php
use App\Services\ActivityService;

$activityService = new ActivityService();

$activityId = $activityService->createActivity(
    $userId,
    'purchase', // type
    'Mô tả hoạt động', // message
    [
        'total_amount' => 25000,
        'items_count' => 2,
        // ... các field bổ sung
    ]
);
```

### Lấy Activities của User

```php
$activities = $activityService->getUserActivities($userId, 20); // limit = 20
```

### Đánh Dấu Activity Đã Đọc

```php
$activityService->markAsRead($userId, $activityId);
```

## Tích Hợp Trong Controllers

### PaymentController

-   Tạo activity khi thanh toán thành công
-   Tạo activity khi nạp coins thành công

### AccountController

-   Tạo activity khi download sheet thành công

## Hiển Thị trong View

File `resources/views/account/activity.blade.php` đã được cập nhật để hiển thị activities theo cấu trúc mới với:

-   Icon phù hợp cho từng loại activity
-   Hiển thị số tiền coins (âm cho purchase, dương cho deposit)
-   Thông tin debug khi `APP_DEBUG=true`
-   Timestamp hiển thị thời gian tương đối

## Lợi Ích của Cấu Trúc Mới

1. **Performance**: Truy vấn nhanh hơn vì chỉ lấy activities của user cụ thể
2. **Scalability**: Dễ dàng scale khi có nhiều users
3. **Security**: Mỗi user chỉ có thể truy cập activities của mình
4. **Organization**: Dữ liệu được tổ chức rõ ràng hơn

## Migration

Nếu cần migrate từ cấu trúc cũ sang mới, có thể tạo một command để:

1. Lấy tất cả activities từ collection `activities` cũ
2. Phân loại theo `user_id`
3. Tạo các collection `activities-{uid}` mới
4. Xóa collection cũ sau khi migration hoàn tất

## Testing

Chạy file test để kiểm tra hệ thống:

```bash
php test_activities.php
```

File test sẽ:

-   Tạo các loại activities khác nhau
-   Kiểm tra việc lưu trữ
-   Kiểm tra việc lấy dữ liệu
-   Hiển thị kết quả
