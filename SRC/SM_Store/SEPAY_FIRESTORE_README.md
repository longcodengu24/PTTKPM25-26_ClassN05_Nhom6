# Hệ thống thanh toán SePay với Firebase Firestore

## Tổng quan

Hệ thống thanh toán tích hợp SePay để nạp coins, sử dụng Firebase Firestore để lưu trữ transactions và Firebase Realtime Database để quản lý số dư user.

## Cấu trúc dữ liệu

### Firestore Collections

#### 1. `transactions` Collection

```json
{
  "transaction_id": "DEP_1729707123_abc123def456",
  "user_id": "cfT4zfDX4YRkuwd4T6X3seJhtbl1",
  "type": "deposit",
  "amount": 25000,
  "currency": "VND",
  "status": "pending|processing|completed|failed",
  "payment_method": "sepay",
  "description": "User UID for transfer content",
  "reference_code": "Transaction reference",
  "sepay_data": {
    "created_via": "web_form|webhook",
    "ip": "192.168.1.1",
    "bank_code": "970423",
    "account_number": "20588668888",
    "webhook_data": {...}
  },
  "processed": false,
  "created_at": "2024-10-23T10:30:00.000Z",
  "updated_at": "2024-10-23T10:30:00.000Z",
  "completed_at": null,
  "processed_at": null,
  "firestore_id": "auto-generated-id"
}
```

#### 2. `user_activities` Collection

```json
{
    "user_id": "cfT4zfDX4YRkuwd4T6X3seJhtbl1",
    "action": "deposit_created|deposit_completed",
    "data": {
        "transaction_id": "DEP_1729707123_abc123def456",
        "amount": 25000,
        "old_balance": 1000,
        "new_balance": 26000
    },
    "timestamp": "2024-10-23T10:30:00.000Z",
    "ip": "192.168.1.1"
}
```

### Firebase Realtime Database

#### User Balance Structure

```json
{
    "users": {
        "{firebase_uid}": {
            "coins": 25000,
            "balance_history": {
                "{push_id}": {
                    "delta": 25000,
                    "type": "deposit",
                    "note": "Deposit from SePay",
                    "transaction_id": "DEP_1729707123_abc123def456",
                    "timestamp": "2024-10-23T10:30:00.000Z"
                }
            }
        }
    }
}
```

## API Endpoints

### 1. Tạo yêu cầu nạp tiền

**POST** `/payment/deposit/create`

```json
{
    "amount": 25000,
    "user_id": "cfT4zfDX4YRkuwd4T6X3seJhtbl1"
}
```

**Response:**

```json
{
    "success": true,
    "data": {
        "transaction_id": "DEP_1729707123_abc123def456",
        "amount": 25000,
        "qr_code": "https://img.vietqr.io/image/970423-20588668888-compact.jpg?amount=25000&addInfo=cfT4zfDX4YRkuwd4T6X3seJhtbl1",
        "bank_info": {
            "bank_code": "970423",
            "bank_name": "TPBank",
            "account_number": "20588668888",
            "account_name": "NGUYEN KHAC LONG",
            "content": "cfT4zfDX4YRkuwd4T6X3seJhtbl1"
        }
    }
}
```

### 2. Kiểm tra trạng thái giao dịch

**GET** `/payment/check-status/{transaction_id}`

**Response:**

```json
{
    "success": true,
    "data": {
        "transaction_id": "DEP_1729707123_abc123def456",
        "status": "completed",
        "amount": 25000,
        "created_at": "23/10/2024 10:30:00",
        "completed": true,
        "processed": true
    }
}
```

### 3. Webhook SePay

**POST** `/api/sepay/webhook`

```json
{
    "transferAmount": 25000,
    "content": "Nap tien cfT4zfDX4YRkuwd4T6X3seJhtbl1",
    "referenceCode": "SEPAY_1729707123",
    "gateway": "TPBank",
    "transferTime": "2024-10-23T10:30:00.000Z",
    "bankCode": "970423",
    "accountNumber": "20588668888"
}
```

## Debug Endpoints

### 1. Xem transactions của user

**GET** `/debug/transactions/{userId?}`

### 2. Kiểm tra status transaction

**GET** `/debug/check-status/{transactionId}`

### 3. Test webhook manual

**GET** `/debug/test-webhook/{amount?}/{uid?}`

### 4. Manual webhook simulation

**POST** `/debug/manual-webhook`

## Quy trình thanh toán

### 1. User tạo yêu cầu nạp tiền

1. User chọn số tiền và submit form
2. Hệ thống tạo transaction trong Firestore với status `pending`
3. Trả về QR code và thông tin ngân hàng
4. User chuyển khoản với nội dung = Firebase UID

### 2. Xử lý webhook từ SePay

1. SePay gửi webhook khi có chuyển khoản
2. Hệ thống extract Firebase UID từ nội dung chuyển khoản
3. Tìm transaction pending với user_id và amount tương ứng
4. Cập nhật transaction status = `completed`
5. Cộng coins vào Firebase Realtime Database
6. Ghi log activity

### 3. Kiểm tra trạng thái real-time

1. Frontend polling API check status
2. Hiển thị thông báo khi hoàn tất
3. Reload page để cập nhật số dư

## Cấu hình Environment

```env
# Firebase
FIREBASE_CREDENTIALS="path/to/firebase.json"
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-api-key
FIREBASE_DATABASE_URL="https://your-project-default-rtdb.region.firebasedatabase.app"

# SePay
SEPAY_TOKEN=your-sepay-token
SEPAY_SECRET_KEY=your-sepay-secret
SEPAY_BANK_ID=15404
SEPAY_ACCOUNT_ID=20588668888
SEPAY_WEBHOOK_URL="https://your-domain.com/api/sepay/webhook"
```

## Testing

### 1. Test tạo transaction

```bash
curl -X POST http://localhost/payment/deposit/create \
  -H "Content-Type: application/json" \
  -d '{"amount": 25000, "user_id": "test_user_123"}'
```

### 2. Test webhook

```bash
curl http://localhost/debug/test-webhook/25000/test_user_123
```

### 3. Xem transactions

```bash
curl http://localhost/debug/transactions/test_user_123
```

## Security Features

1. **UID Validation**: Chỉ extract Firebase UID hợp lệ (28 ký tự alphanumeric)
2. **Amount Matching**: Webhook chỉ xử lý khi amount khớp với transaction pending
3. **Duplicate Prevention**: Transaction đã completed không được xử lý lại
4. **Firestore Rules**: Cần thiết lập rules để bảo vệ dữ liệu
5. **Webhook Verification**: Có thể thêm signature verification từ SePay

## Monitoring & Logging

Tất cả các hoạt động được log với các emoji để dễ tracking:

-   💳 Transaction created
-   📥 Webhook received
-   🔍 UID extracted
-   ✅ Deposit success
-   ❌ Error occurred
-   ⚠️ Warning/duplicate

## Backup & Recovery

1. **Firestore**: Tự động backup bởi Google
2. **Realtime Database**: Export định kỳ
3. **Dual Storage**: Transaction lưu Firestore, balance lưu Realtime DB
4. **Activity Log**: Ghi lại mọi thay đổi để audit

## Tối ưu hóa

1. **Indexing**: Tạo index cho các truy vấn thường xuyên
2. **Caching**: Cache balance trong session
3. **Rate Limiting**: Giới hạn số request tạo transaction
4. **Batch Processing**: Xử lý nhiều webhook cùng lúc nếu cần
