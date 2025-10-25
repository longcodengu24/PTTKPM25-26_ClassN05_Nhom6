<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use App\Services\FirestoreSimple;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class SupportAccController extends Controller
{
    protected $auth;
    protected $firestoreService;
    protected $activityService;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->firestoreService = new FirestoreSimple();
        $this->activityService = new ActivityService();
    }

    /**
     * Hiển thị danh sách yêu cầu seller
     */
    public function anyf()
    {
        try {
            // Lấy danh sách yêu cầu seller từ Firestore
            $sellerRequests = [];
            $requests = $this->firestoreService->queryDocuments('seller_requests', [
                ['field' => 'status', 'operator' => '==', 'value' => 'pending']
            ]);
            
            Log::info('SupportAccController - Raw seller requests query result:', ['requests' => $requests]);
            
            if (isset($requests['documents'])) {
                foreach ($requests['documents'] as $doc) {
                    // Kiểm tra cấu trúc dữ liệu từ logs
                    if (isset($doc['data'])) {
                        // Cấu trúc mới từ FirestoreSimple
                        $data = $doc['data'];
                        $data['id'] = $doc['id'];
                    } else {
                        // Cấu trúc cũ từ Firestore REST API
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
                        $data['id'] = $id;
                    }
                    
                    // Lấy thông tin user từ Firebase
                    try {
                        $user = $this->auth->getUser($data['user_id']);
                        $data['email'] = $user->email ?? 'N/A';
                        $data['display_name'] = $user->displayName ?? 'N/A';
                        $data['photo_url'] = $user->photoUrl ?? '/img/default-avatar.png';
                    } catch (\Exception $e) {
                        $data['email'] = 'N/A';
                        $data['display_name'] = 'N/A';
                        $data['photo_url'] = '/img/default-avatar.png';
                    }
                    
                    $sellerRequests[] = $data;
                }
            }
            
            // Sort by created_at descending
            usort($sellerRequests, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');
                return $timeB - $timeA;
            });

            Log::info('SupportAccController - Processed seller requests:', ['count' => count($sellerRequests), 'requests' => $sellerRequests]);

            return view('admin.supportacc.anyf', [
                'sellerRequests' => $sellerRequests
            ]);
        } catch (\Exception $e) {
            Log::error('SupportAccController anyf error: ' . $e->getMessage());
            return view('admin.supportacc.anyf', [
                'sellerRequests' => []
            ])->with('error', 'Có lỗi xảy ra khi tải danh sách yêu cầu');
        }
    }

    /**
     * Chấp nhận yêu cầu trở thành seller
     */
    public function approveRequest(Request $request, string $requestId)
    {
        try {
            // Lấy thông tin yêu cầu
            $sellerRequest = $this->firestoreService->getDocument('seller_requests', $requestId);
            
            if (!$sellerRequest) {
                return redirect()->back()->with('error', 'Không tìm thấy yêu cầu.');
            }
            
            $userId = $sellerRequest['user_id'];
            
            // Cập nhật role user thành saler
            $this->auth->setCustomUserClaims($userId, ['role' => 'saler']);
            
            // Cập nhật trạng thái yêu cầu
            $this->firestoreService->updateDocument('seller_requests', $requestId, [
                'status' => 'approved',
                'approved_at' => now()->toISOString(),
                'approved_by' => session('firebase_uid')
            ]);
            
            // Tạo activity cho user
            $this->activityService->createActivity(
                $userId,
                'seller_approved',
                'Yêu cầu trở thành Seller của bạn đã được chấp nhận! Bạn có thể truy cập Seller Panel để bắt đầu bán hàng.',
                [
                    'request_id' => $requestId,
                    'approved_by' => session('firebase_uid')
                ]
            );
            
            return redirect()->route('admin.anyf')
                ->with('success', 'Đã chấp nhận yêu cầu trở thành Seller.');
                
        } catch (\Exception $e) {
            Log::error('Approve seller request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối yêu cầu trở thành seller
     */
    public function rejectRequest(Request $request, string $requestId)
    {
        try {
            // Lấy thông tin yêu cầu
            $sellerRequest = $this->firestoreService->getDocument('seller_requests', $requestId);
            
            if (!$sellerRequest) {
                return redirect()->back()->with('error', 'Không tìm thấy yêu cầu.');
            }
            
            $userId = $sellerRequest['user_id'];
            
            // Cập nhật trạng thái yêu cầu
            $this->firestoreService->updateDocument('seller_requests', $requestId, [
                'status' => 'rejected',
                'rejected_at' => now()->toISOString(),
                'rejected_by' => session('firebase_uid')
            ]);
            
            // Tạo activity cho user
            $this->activityService->createActivity(
                $userId,
                'seller_rejected',
                'Yêu cầu trở thành Seller của bạn đã bị từ chối. Bạn có thể gửi lại yêu cầu mới.',
                [
                    'request_id' => $requestId,
                    'rejected_by' => session('firebase_uid')
                ]
            );
            
            return redirect()->route('admin.anyf')
                ->with('success', 'Đã từ chối yêu cầu trở thành Seller.');
                
        } catch (\Exception $e) {
            Log::error('Reject seller request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
