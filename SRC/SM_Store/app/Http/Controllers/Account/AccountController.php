<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Product;

class AccountController extends Controller
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Load user data from Firebase and update session
     */
    private function loadUserData()
    {
        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                return null;
            }

            // Lấy thông tin user từ Firebase
            $user = $this->auth->getUser($uid);

            // Cập nhật session với thông tin mới nhất từ Firebase
            $userData = [
                'name' => $user->displayName ?? session('name', ''),
                'email' => $user->email ?? session('email', ''),
                'avatar' => $user->photoUrl ?? '/img/default-avatar.png'
            ];

            // Cập nhật session
            session($userData);

            return $userData;
        } catch (\Exception $e) {
            Log::error('Error loading user data: ' . $e->getMessage());
            return null;
        }
    }

    public function settings()
    {
        $userData = $this->loadUserData();

        if (!$userData) {
            return redirect()->route('auth.login')->withErrors(['error' => 'Vui lòng đăng nhập.']);
        }

        Log::info('User data loaded for settings', [
            'uid' => session('firebase_uid'),
            'name' => $userData['name'],
            'avatar' => $userData['avatar']
        ]);

        return view('account.settings', compact('userData'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên đăng nhập.',
            'name.max' => 'Tên đăng nhập không được quá 100 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Kích thước file không được vượt quá 2MB.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                return back()->withErrors(['error' => 'Phiên đăng nhập không hợp lệ.']);
            }

            // Cập nhật display name
            $this->auth->updateUser($uid, [
                'displayName' => $request->name,
            ]);

            // Cập nhật session
            session(['name' => $request->name]);

            // Xử lý upload avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                try {
                    $avatar = $request->file('avatar');

                    Log::info('Avatar upload attempt', [
                        'original_name' => $avatar->getClientOriginalName(),
                        'size' => $avatar->getSize(),
                        'mime_type' => $avatar->getMimeType(),
                        'uid' => $uid
                    ]);

                    // Tạo thư mục nếu chưa có
                    $uploadDir = public_path('img/avatars');
                    if (!file_exists($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            throw new \Exception('Không thể tạo thư mục upload: ' . $uploadDir);
                        }
                        Log::info('Created upload directory', ['path' => $uploadDir]);
                    }

                    // Xóa avatar cũ nếu có
                    $oldAvatar = session('avatar');
                    if ($oldAvatar && strpos($oldAvatar, 'avatars/') !== false) {
                        $oldFileName = basename($oldAvatar);
                        $oldFile = $uploadDir . DIRECTORY_SEPARATOR . $oldFileName;
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                            Log::info('Deleted old avatar', ['path' => $oldFile]);
                        }
                    }

                    // Tạo tên file unique
                    $fileName = 'avatar_' . $uid . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                    $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

                    // Move file to public/img/avatars
                    if (!$avatar->move($uploadDir, $fileName)) {
                        throw new \Exception('Không thể di chuyển file đến: ' . $fullPath);
                    }

                    // Kiểm tra file đã được tạo
                    if (!file_exists($fullPath)) {
                        throw new \Exception('File không được tạo thành công: ' . $fullPath);
                    }

                    // Tạo URL cho avatar
                    $photoUrl = asset('img/avatars/' . $fileName);

                    // Cập nhật photo URL trong Firebase
                    $this->auth->updateUser($uid, [
                        'photoUrl' => $photoUrl,
                    ]);

                    // Cập nhật session
                    session(['avatar' => $photoUrl]);

                    Log::info('Avatar updated successfully', [
                        'uid' => $uid,
                        'photoUrl' => $photoUrl,
                        'file_path' => $fullPath,
                        'file_size' => filesize($fullPath)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Avatar upload error: ' . $e->getMessage(), [
                        'uid' => $uid,
                        'request_data' => $request->all()
                    ]);
                    return back()->withErrors(['avatar' => 'Lỗi upload ảnh: ' . $e->getMessage()]);
                }
            }            // Cập nhật mật khẩu nếu được cung cấp
            if ($request->filled('password')) {
                $this->auth->updateUser($uid, [
                    'password' => $request->password,
                ]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Account update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $userData = $this->loadUserData();
        return view('account.index', compact('userData'));
    }

    public function sheets()
    {
        $userData = $this->loadUserData();

        try {
            // Get current user's UID
            $uid = session('firebase_uid');
            $userProducts = collect([]);
            $totalUserProducts = 0;

            // Debug information
            Log::info('Debug sheets - UID: ' . ($uid ?? 'NULL'));

            if ($uid) {
                // Initialize Product model
                $productModel = new Product();

                // Get products from both collections
                $productsFromProducts = $productModel->getAllActive(); // From 'products' collection

                // Also get from 'sheets' collection (where saler creates products)
                $firestoreService = new \App\Services\FirestoreSimple();
                $sheetsResponse = $firestoreService->listDocuments('sheets');
                $sheetsFromSheets = [];

                // Parse the Firestore response
                if (isset($sheetsResponse['documents'])) {
                    foreach ($sheetsResponse['documents'] as $doc) {
                        $docData = [];

                        // Extract document ID
                        $docPath = $doc['name'] ?? '';
                        $docData['id'] = basename($docPath);

                        // Extract fields
                        if (isset($doc['fields'])) {
                            foreach ($doc['fields'] as $field => $value) {
                                if (isset($value['stringValue'])) {
                                    $docData[$field] = $value['stringValue'];
                                } elseif (isset($value['integerValue'])) {
                                    $docData[$field] = (int) $value['integerValue'];
                                } elseif (isset($value['booleanValue'])) {
                                    $docData[$field] = $value['booleanValue'];
                                } elseif (isset($value['timestampValue'])) {
                                    $docData[$field] = $value['timestampValue'];
                                }
                            }
                        }

                        $sheetsFromSheets[] = $docData;
                    }
                }

                // Combine both collections - convert Collection to array first
                $allProducts = array_merge(
                    is_array($productsFromProducts) ? $productsFromProducts : $productsFromProducts->toArray(),
                    $sheetsFromSheets
                );

                Log::info('Debug sheets - Total products from both collections: ' . count($allProducts));
                Log::info('Debug sheets - Products collection: ' . count($productsFromProducts));
                Log::info('Debug sheets - Sheets collection: ' . count($sheetsFromSheets));
                Log::info('Debug sheets - Current user UID: ' . $uid);
                Log::info('Debug sheets - Found user products: ' . $totalUserProducts);

                // Try different field names that might store the user who created the product
                $userProducts = collect($allProducts)->filter(function ($product) use ($uid) {
                    $hasCreatedBy = isset($product['created_by']) && $product['created_by'] === $uid;
                    $hasUserId = isset($product['user_id']) && $product['user_id'] === $uid;
                    $hasSellerUid = isset($product['seller_uid']) && $product['seller_uid'] === $uid;
                    $hasOwnerUid = isset($product['owner_uid']) && $product['owner_uid'] === $uid; // For 'sheets' collection
                    $hasSellerId = isset($product['seller_id']) && $product['seller_id'] === $uid; // For 'products' collection

                    return $hasCreatedBy || $hasUserId || $hasSellerUid || $hasOwnerUid || $hasSellerId;
                });

                $totalUserProducts = $userProducts->count();
                Log::info('Debug sheets - Found user products: ' . $totalUserProducts);
            }

            return view('account.sheets', compact('userData', 'userProducts', 'totalUserProducts'));
        } catch (\Exception $e) {
            Log::error('Error fetching user sheets: ' . $e->getMessage());
            return view('account.sheets', compact('userData'))->with('error', 'Có lỗi xảy ra khi tải danh sách sheet nhạc.');
        }
    }

    public function activity()
    {
        $userData = $this->loadUserData();

        // Lấy thông tin sheet của user để hiển thị trong hoạt động
        $userSheets = null;
        try {
            $uid = session('firebase_uid');
            if ($uid) {
                $productModel = new Product();
                $userProducts = $productModel->getBySeller($uid);

                // Chỉ lấy thông tin cơ bản cần thiết cho hoạt động
                if ($userProducts && count($userProducts) > 0) {
                    $userSheets = [];
                    $productsArray = is_array($userProducts) ? $userProducts : $userProducts->toArray();

                    foreach ($productsArray as $product) {
                        $userSheets[] = [
                            'name' => $product['name'] ?? 'Sheet nhạc',
                            'created_at' => $product['created_at'] ?? null
                        ];
                    }

                    // Sắp xếp theo thời gian tạo mới nhất
                    usort($userSheets, function ($a, $b) {
                        return strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0');
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error('Error loading user sheets for activity: ' . $e->getMessage());
        }

        return view('account.activity', compact('userData', 'userSheets'));
    }

    public function deposit()
    {
        $userData = $this->loadUserData();
        return view('account.deposit', compact('userData'));
    }

    public function withdraw()
    {
        $userData = $this->loadUserData();
        return view('account.withdraw', compact('userData'));
    }

    public function downloadSheet($id)
    {
        try {
            // Get current user's UID
            $uid = session('firebase_uid');

            if (!$uid) {
                return response()->json(['error' => 'Bạn cần đăng nhập để tải file'], 401);
            }

            // Initialize services
            $productModel = new Product();
            $firestoreService = new \App\Services\FirestoreSimple();

            // Find the product in both collections
            $product = null;
            $filePath = null;

            // Search in 'products' collection first
            $productsFromProducts = $productModel->getAllActive();
            if ($productsFromProducts) {
                $productsArray = is_array($productsFromProducts) ? $productsFromProducts : $productsFromProducts->toArray();
                foreach ($productsArray as $prod) {
                    if (($prod['id'] ?? '') === $id) {
                        $product = $prod;
                        $filePath = $prod['file_path'] ?? null;
                        break;
                    }
                }
            }

            // If not found, search in 'sheets' collection
            if (!$product) {
                $sheetsResponse = $firestoreService->listDocuments('sheets');
                if (isset($sheetsResponse['documents'])) {
                    foreach ($sheetsResponse['documents'] as $doc) {
                        $docId = basename($doc['name'] ?? '');
                        if ($docId === $id) {
                            $docData = [];
                            $docData['id'] = $docId;

                            // Extract fields
                            if (isset($doc['fields'])) {
                                foreach ($doc['fields'] as $field => $value) {
                                    if (isset($value['stringValue'])) {
                                        $docData[$field] = $value['stringValue'];
                                    }
                                }
                            }

                            $product = $docData;
                            $filePath = $docData['sheet_file_url'] ?? null;
                            break;
                        }
                    }
                }
            }

            if (!$product) {
                return response()->json(['error' => 'Không tìm thấy sheet nhạc'], 404);
            }

            // Check if user owns this product (can download their own products)
            $isOwner = (isset($product['seller_id']) && $product['seller_id'] === $uid) ||
                (isset($product['owner_uid']) && $product['owner_uid'] === $uid);

            if (!$isOwner) {
                return response()->json(['error' => 'Bạn không có quyền tải file này'], 403);
            }

            if (!$filePath) {
                return response()->json(['error' => 'File không tồn tại'], 404);
            }

            // Build full file path - files are stored in public directory
            $fullPath = public_path($filePath);

            // Debug file path
            Log::info('Download debug - File path from DB: ' . $filePath);
            Log::info('Download debug - Full path: ' . $fullPath);
            Log::info('Download debug - File exists: ' . (file_exists($fullPath) ? 'YES' : 'NO'));

            if (!file_exists($fullPath)) {
                // Try alternative paths
                $altPath1 = storage_path('app/public/' . $filePath);
                $altPath2 = storage_path('app/' . $filePath);

                Log::info('Download debug - Alternative path 1 (storage/app/public): ' . $altPath1 . ' - Exists: ' . (file_exists($altPath1) ? 'YES' : 'NO'));
                Log::info('Download debug - Alternative path 2 (storage/app): ' . $altPath2 . ' - Exists: ' . (file_exists($altPath2) ? 'YES' : 'NO'));

                return response()->json(['error' => 'File không tồn tại trên hệ thống. Path: ' . $fullPath], 404);
            }

            // Get filename for download
            $filename = $product['name'] ?? $product['title'] ?? 'sheet-nhac';
            $filename = preg_replace('/[^A-Za-z0-9\-_\.]/', '_', $filename) . '.txt';

            return response()->download($fullPath, $filename);
        } catch (\Exception $e) {
            Log::error('Error downloading sheet: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi tải file'], 500);
        }
    }
}
