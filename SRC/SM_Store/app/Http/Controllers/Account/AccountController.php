<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Services\FirestoreSimple;
use App\Services\UserPurchaseService;
use App\Services\ActivityService;

class AccountController extends Controller
{
    protected $auth;
    protected $firestoreService;
    protected $userPurchaseService;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->firestoreService = new FirestoreSimple();
        $this->userPurchaseService = new UserPurchaseService();
    }

    private function getUserData()
    {
        try {
            $uid = session('firebase_uid');
            if (!$uid) return null;

            $firestore = new FirestoreSimple();
            $userDoc = $firestore->getDocument('users', $uid);
            $coins = $userDoc['coins'] ?? 0;

            return [
                'name' => session('name', ''),
                'email' => session('email', ''),
                'avatar' => session('avatar', '/img/default-avatar.png'),
                'coins' => $coins,
                'uid' => $uid
            ];
        } catch (\Exception $e) {
            Log::error('Error getting user data: ' . $e->getMessage());
            return null;
        }
    }

    public function settings()
    {
        $userData = $this->getUserData();
        if (!$userData) return redirect()->route('auth.login')->withErrors(['error' => 'Vui lòng đăng nhập.']);
        return view('account.settings', compact('userData'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        try {
            $uid = session('firebase_uid');
            if (!$uid) return back()->withErrors(['error' => 'Phiên đăng nhập không hợp lệ.']);

            $this->auth->updateUser($uid, ['displayName' => $request->name]);
            session(['name' => $request->name]);

            // Upload avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $avatar = $request->file('avatar');
                $uploadDir = public_path('img/avatars');
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

                $oldAvatar = session('avatar');
                if ($oldAvatar && strpos($oldAvatar, 'avatars/') !== false) {
                    $oldFile = $uploadDir . '/' . basename($oldAvatar);
                    if (file_exists($oldFile)) unlink($oldFile);
                }

                $fileName = 'avatar_' . $uid . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move($uploadDir, $fileName);
                $photoUrl = asset('img/avatars/' . $fileName);

                $this->auth->updateUser($uid, ['photoUrl' => $photoUrl]);
                session(['avatar' => $photoUrl]);
            }

            if ($request->filled('password')) {
                $this->auth->updateUser($uid, ['password' => $request->password]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Account update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $userData = $this->getUserData();
        return view('account.index', compact('userData'));
    }

    public function sheets()
    {
        $userData = $this->getUserData();
        try {
            $uid = session('firebase_uid');
            $userProducts = collect();
            $purchasedProducts = collect();
            $totalUserProducts = 0;

            if ($uid) {
                // Lấy products của user (sản phẩm họ đã tạo)
                $productModel = new Product();
                $allProducts = $productModel->getAllActive() ?? [];
                $userProducts = collect($allProducts)->filter(fn($p) =>
                    ($p['seller_id'] ?? '') === $uid ||
                    ($p['created_by'] ?? '') === $uid
                );

                // Lấy sheets đã mua từ subcollection mới
                $sheets = $this->userPurchaseService->getUserSheets($uid);
                
                // Chuyển đổi format để tương thích với view
                foreach ($sheets as $sheet) {
                    $sheetData = $sheet['data'];
                    $purchasedProducts->push([
                        'id' => $sheet['id'],
                        'product_id' => $sheetData['product_id'] ?? '',
                        'name' => $sheetData['title'] ?? '',
                        'author' => $sheetData['seller_name'] ?? '',
                        'price' => $sheetData['price'] ?? 0,
                        'status' => $sheetData['status'] ?? 'active',
                        'file_path' => $sheetData['file_url'] ?? '', // file_url từ Firestore
                        'image_path' => $sheetData['image_path'] ?? '',
                        'purchased_at' => $sheetData['purchased_at'] ?? '',
                        'transaction_id' => $sheetData['transaction_id'] ?? '',
                        'category' => $sheetData['category'] ?? '',
                        'description' => $sheetData['description'] ?? '',
                        'rating' => $sheetData['rating'] ?? 0
                    ]);
                }
                
                $totalUserProducts = $userProducts->count();
            }

            return view('account.sheets', compact('userData', 'userProducts', 'purchasedProducts', 'totalUserProducts'));
        } catch (\Exception $e) {
            Log::error('Error fetching user sheets: ' . $e->getMessage());
            return view('account.sheets', compact('userData'))->with('error', 'Có lỗi xảy ra.');
        }
    }

    public function activity()
    {
        $userData = $this->getUserData();
        $activities = [];

        try {
            $uid = session('firebase_uid');
            if ($uid) {
                $activityService = new ActivityService();
                $activities = $activityService->getUserActivities($uid, 30);
            }
        } catch (\Exception $e) {
            Log::error('Error loading user activities: ' . $e->getMessage());
        }

        return view('account.activity', compact('userData', 'activities'));
    }

    public function deposit()
    {
        $userData = $this->getUserData();
        return view('account.deposit', compact('userData'));
    }

    public function withdraw()
    {
        $userData = $this->getUserData();
        return view('account.withdraw', compact('userData'));
    }

    public function processWithdraw(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:5000|max:50000000',
                'method' => 'required|in:momo,zalopay,bank',
                'account_info' => 'required|string|min:9|max:20'
            ]);

            $userId = session('firebase_uid');
            if (!$userId) return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập'], 401);

            $firestore = new \App\Services\FirestoreRestService();
            $userDoc = $firestore->getDocument('users', $userId);
            if (!$userDoc['success']) return response()->json(['success' => false, 'message' => 'Không tìm thấy người dùng'], 404);

            $userData = $userDoc['data'];
            $currentCoins = $userData['coins'] ?? 0;
            $amount = $validated['amount'];

            if ($amount % 5000 !== 0) return response()->json(['success' => false, 'message' => 'Số tiền rút phải là bội số của 5,000']);
            if ($currentCoins < $amount) return response()->json(['success' => false, 'message' => 'Số dư không đủ'], 400);

            $userData['coins'] = $currentCoins - $amount;
            $firestore->updateDocument('users', $userId, $userData);

            $activityService = new ActivityService();
            $activityService->createActivity($userId, 'withdraw', 'Rút tiền thành công', [
                'amount' => $amount,
                'method' => $validated['method']
            ]);

            return response()->json(['success' => true, 'message' => 'Rút tiền thành công!']);
        } catch (\Exception $e) {
            Log::error('❌ processWithdraw error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function showMySheets()
    {
        $userId = session('firebase_uid');
        if (!$userId) return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');

        $firestore = app(\App\Services\FirestoreRestService::class);
        $userDoc = $firestore->getDocument('users', $userId);
        if (!$userDoc['success']) return back()->with('error', 'Không tìm thấy người dùng trong Firestore.');

        $userData = $userDoc['data'];
        $purchasedProducts = collect();

        if (isset($userData['listsheets']) && is_array($userData['listsheets'])) {
            if (array_keys($userData['listsheets']) !== range(0, count($userData['listsheets']) - 1)) {
                foreach ($userData['listsheets'] as $id => $sheet) {
                    $sheet['product_id'] = $id;
                    $purchasedProducts->push($sheet);
                }
            } else {
                $purchasedProducts = collect($userData['listsheets']);
            }
        }

        return view('account.sheets', [
            'purchasedProducts' => $purchasedProducts,
            'totalPurchasedProducts' => $purchasedProducts->count(),
        ]);
    }

    /**
     * Download sheet file
     */
    public function downloadSheet($id)
    {
        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                // Return JSON error for AJAX requests
                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng đăng nhập để tải file.',
                        'redirect' => route('login')
                    ], 401);
                }
                
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tải file.');
            }

            // Lấy thông tin sheet từ subcollection
            $sheets = $this->userPurchaseService->getUserSheets($uid);
            $targetSheet = null;
            
            Log::info('Debug downloadSheet:', [
                'uid' => $uid,
                'requested_id' => $id,
                'sheets_count' => count($sheets),
                'sheets' => $sheets
            ]);
            
            foreach ($sheets as $sheet) {
                if ($sheet['id'] === $id) {
                    $targetSheet = $sheet['data'];
                    break;
                }
            }

            if (!$targetSheet) {
                $errorMsg = 'Không tìm thấy sheet này hoặc bạn chưa mua sheet này.';
                
                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg
                    ], 404);
                }
                
                return redirect()->route('account.sheets')->with('error', $errorMsg);
            }

            $filePath = $targetSheet['file_url'] ?? '';
            
            Log::info('Debug file path:', [
                'file_url' => $filePath,
                'target_sheet' => $targetSheet
            ]);
            
            if (empty($filePath)) {
                $errorMsg = 'File không tồn tại hoặc đã bị xóa.';
                
                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg
                    ], 404);
                }
                
                return redirect()->route('account.sheets')->with('error', $errorMsg);
            }

            // Kiểm tra file có tồn tại không
            $fullPath = public_path($filePath);
            
            Log::info('Debug file check:', [
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath)
            ]);
            
            if (!file_exists($fullPath)) {
                $errorMsg = 'File không tồn tại trên server: ' . basename($filePath);
                
                if (request()->ajax() || request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg
                    ], 404);
                }
                
                return redirect()->route('account.sheets')->with('error', $errorMsg);
            }

            // Tạo tên file download thân thiện hơn
            $originalFileName = basename($filePath);
            $sheetTitle = $targetSheet['title'] ?? 'sheet_' . $id;
            
            // Làm sạch tên file và giữ nguyên extension
            $cleanTitle = preg_replace('/[^a-zA-Z0-9\-_\s]/', '', $sheetTitle);
            $cleanTitle = preg_replace('/\s+/', '_', trim($cleanTitle));
            
            // Lấy extension từ file gốc
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = 'txt'; // Default extension
            }
            
            $downloadFileName = $cleanTitle . '.' . $extension;

            // Set headers để đảm bảo download thành công
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $downloadFileName . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ];

            Log::info('Downloading file:', [
                'original_path' => $filePath,
                'download_name' => $downloadFileName,
                'file_size' => filesize($fullPath)
            ]);

            // Tạo activity cho việc download
            $activityService = new \App\Services\ActivityService();
            $activityService->createActivity(
                $uid,
                'download',
                "Bạn đã tải về sheet nhạc: {$targetSheet['title']}",
                [
                    'sheet_title' => $targetSheet['title'],
                    'file_name' => $downloadFileName,
                    'file_size' => filesize($fullPath),
                    'transaction_type' => 'download'
                ]
            );

            return response()->download($fullPath, $downloadFileName, $headers);

        } catch (\Exception $e) {
            Log::error('Download sheet error: ' . $e->getMessage());
            
            $errorMsg = 'Có lỗi khi tải file: ' . $e->getMessage();
            
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 500);
            }
            
            return redirect()->route('account.sheets')->with('error', $errorMsg);
        }
    }
}
