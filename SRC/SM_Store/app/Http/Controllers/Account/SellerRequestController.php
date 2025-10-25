<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class SellerRequestController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị trang helpseller
     */
    public function index()
    {
        $userId = session('firebase_uid');
        
        try {
            // Lấy thông tin user
            $user = $this->firestoreService->getDocument('users', $userId);
            $userRole = $user['role'] ?? 'user';
            
            // Lấy trạng thái yêu cầu seller
            $sellerRequest = $this->firestoreService->queryDocuments('seller_requests', [
                ['field' => 'user_id', 'operator' => '==', 'value' => $userId],
                ['field' => 'status', 'operator' => 'in', 'value' => ['pending', 'rejected']]
            ]);
            
            $requestStatus = null;
            if (!empty($sellerRequest['documents'])) {
                $latestRequest = $sellerRequest['documents'][0];
                
                // Parse dữ liệu từ Firestore
                if (isset($latestRequest['data'])) {
                    $requestStatus = $latestRequest['data']['status'] ?? null;
                } else {
                    $fields = $latestRequest['fields'] ?? [];
                    foreach ($fields as $key => $field) {
                        if ($key === 'status' && isset($field['stringValue'])) {
                            $requestStatus = $field['stringValue'];
                            break;
                        }
                    }
                }
            }
            
            return view('account.helpseller')->with([
                'user_role' => $userRole,
                'seller_request_status' => $requestStatus
            ]);
        } catch (\Exception $e) {
            Log::error('SellerRequestController index error: ' . $e->getMessage());
            return view('account.helpseller', [
                'user_role' => 'user',
                'seller_request_status' => null
            ]);
        }
    }

    /**
     * Xử lý yêu cầu trở thành seller
     */
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
            'experience' => 'required|in:beginner,intermediate,advanced,professional',
            'portfolio' => 'nullable|url|max:255'
        ]);

        $userId = session('firebase_uid');
        
        try {
            // Kiểm tra xem đã có yêu cầu pending chưa
            $existingRequest = $this->firestoreService->queryDocuments('seller_requests', [
                ['field' => 'user_id', 'operator' => '==', 'value' => $userId],
                ['field' => 'status', 'operator' => '==', 'value' => 'pending']
            ]);
            
            if (!empty($existingRequest['documents'])) {
                return redirect()->back()->with('error', 'Bạn đã có yêu cầu đang chờ duyệt. Vui lòng chờ Admin xem xét.');
            }
            
            // Kiểm tra xem đã có yêu cầu rejected gần đây chưa (trong vòng 24h)
            $rejectedRequest = $this->firestoreService->queryDocuments('seller_requests', [
                ['field' => 'user_id', 'operator' => '==', 'value' => $userId],
                ['field' => 'status', 'operator' => '==', 'value' => 'rejected']
            ]);
            
            if (!empty($rejectedRequest['documents'])) {
                $latestRejected = $rejectedRequest['documents'][0];
                $rejectedAt = null;
                
                if (isset($latestRejected['data'])) {
                    $rejectedAt = $latestRejected['data']['rejected_at'] ?? null;
                } else {
                    $fields = $latestRejected['fields'] ?? [];
                    foreach ($fields as $key => $field) {
                        if ($key === 'rejected_at' && isset($field['stringValue'])) {
                            $rejectedAt = $field['stringValue'];
                            break;
                        }
                    }
                }
                
                if ($rejectedAt && strtotime($rejectedAt) > strtotime('-24 hours')) {
                    return redirect()->back()->with('error', 'Yêu cầu của bạn vừa bị từ chối. Vui lòng chờ 24 giờ trước khi gửi yêu cầu mới.');
                }
            }
            
            // Tạo yêu cầu mới
            $requestData = [
                'user_id' => $userId,
                'reason' => $request->reason,
                'experience' => $request->experience,
                'portfolio' => $request->portfolio,
                'status' => 'pending',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];
            
            $result = $this->firestoreService->createDocument('seller_requests', $requestData);
            
            if ($result) {
                return redirect()->back()->with('success', 'Yêu cầu trở thành Seller đã được gửi thành công! Admin sẽ xem xét và phản hồi sớm nhất.');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi yêu cầu. Vui lòng thử lại.');
            }
        } catch (\Exception $e) {
            Log::error('SellerRequestController store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
