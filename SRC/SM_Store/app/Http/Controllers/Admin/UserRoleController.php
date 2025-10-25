<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use App\Services\FirestoreSimple;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class UserRoleController extends Controller
{
    protected $firestoreService;
    protected $activityService;

    public function __construct(private Auth $auth) 
    {
        $this->firestoreService = new FirestoreSimple();
        $this->activityService = new ActivityService();
    }

    public function index()
    {
        $users = [];
        foreach ($this->auth->listUsers() as $user) {
            if (!$user->email) continue;
            $users[] = [
                'uid'   => $user->uid,
                'email' => $user->email,
                'role'  => $user->customClaims['role'] ?? 'user',
            ];
        }
        usort($users, fn($a, $b) => strcmp($a['email'], $b['email']));

        // Lấy danh sách yêu cầu seller
        $sellerRequests = [];
        try {
            $requests = $this->firestoreService->queryDocuments('seller_requests', [
                ['field' => 'status', 'operator' => '==', 'value' => 'pending']
            ]);
            
            Log::info('Raw seller requests query result:', ['requests' => $requests]);
            
            if (isset($requests['documents']) && is_array($requests['documents'])) {
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
                    
                    // Lấy email từ user
                    try {
                        if (isset($data['user_id'])) {
                            $user = $this->auth->getUser($data['user_id']);
                            $data['email'] = $user->email ?? 'N/A';
                        } else {
                            $data['email'] = 'N/A';
                        }
                    } catch (\Exception $e) {
                        Log::warning('Error getting user email for seller request: ' . $e->getMessage());
                        $data['email'] = 'N/A';
                    }
                    
                    $sellerRequests[] = $data;
                }
            }
            
            Log::info('Processed seller requests:', ['count' => count($sellerRequests), 'requests' => $sellerRequests]);
            
            // Sort by created_at descending
            usort($sellerRequests, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');
                return $timeB - $timeA;
            });
        } catch (\Exception $e) {
            Log::error('Error fetching seller requests: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        // ✅ Trả về view trong admin layout
        Log::info('Returning to view with data:', [
            'users_count' => count($users),
            'sellerRequests_count' => count($sellerRequests),
            'sellerRequests_sample' => array_slice($sellerRequests, 0, 2)
        ]);
        
        return view('admin.roles.index', compact('users', 'sellerRequests'));
    }

    public function updateRole(Request $request, string $uid)
    {
        $request->validate([
            'role' => 'required|in:admin,saler,user',
        ]);

        try {
            $user = $this->auth->getUser($uid);
            $this->auth->setCustomUserClaims($uid, ['role' => $request->role]);

            if (session('firebase_uid') === $uid) {
                session(['role' => $request->role]);
            }

            // ✅ Sau khi cập nhật, quay lại trang phân quyền trong admin
            return redirect()->route('admin.roles.index')
                ->with('success', "Đã cập nhật quyền cho {$user->email} thành '{$request->role}'.");
        } catch (UserNotFound $e) {
            return back()->withErrors(['error' => 'Không tìm thấy tài khoản trên Firebase.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Lỗi khi cập nhật quyền: '.$e->getMessage()]);
        }
    }

    /**
     * Chấp nhận yêu cầu trở thành seller
     */
    public function approveSellerRequest(Request $request, string $requestId)
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
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'Đã chấp nhận yêu cầu trở thành Seller.');
                
        } catch (\Exception $e) {
            Log::error('Approve seller request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối yêu cầu trở thành seller
     */
    public function rejectSellerRequest(Request $request, string $requestId)
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
            
            return redirect()->route('admin.roles.index')
                ->with('success', 'Đã từ chối yêu cầu trở thành Seller.');
                
        } catch (\Exception $e) {
            Log::error('Reject seller request error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
