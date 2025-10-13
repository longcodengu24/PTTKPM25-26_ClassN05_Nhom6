<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Services\FirestoreSimple;
use App\Services\ActivityService;

class AccountController extends Controller
{
    protected $auth;
    protected $firestoreService;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Get user data (prefer from session, updated by middleware)
     */
    private function getUserData()
    {
        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                return null;
            }

            // Lấy coins từ Firestore (dữ liệu quan trọng không cache trong session)
            $firestore = new \App\Services\FirestoreSimple();
            $userDoc = $firestore->getDocument('users', $uid);
            $coins = $userDoc['coins'] ?? 0;

            // Sử dụng dữ liệu từ session (đã được cập nhật bởi getUserData middleware)
            $userData = [
                'name' => session('name', ''),
                'email' => session('email', ''),
                'avatar' => session('avatar', '/img/default-avatar.png'),
                'coins' => $coins, // Chỉ coins cần query real-time
                'uid' => $uid
            ];

            return $userData;
        } catch (\Exception $e) {
            Log::error('Error getting user data: ' . $e->getMessage());
            return null;
        }
    }

    public function settings()
    {
        $userData = $this->getUserData();

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
        $userData = $this->getUserData();
        return view('account.index', compact('userData'));
    }

    public function sheets()
    {
        $userData = $this->getUserData();

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

                // Get products from products collection only (user's own products)
                $allProducts = $productModel->getAllActive();

                // Convert Collection to array if needed
                if (!is_array($allProducts)) {
                    $allProducts = $allProducts->toArray();
                }

                Log::info('Debug products - Total products: ' . count($allProducts));
                Log::info('Debug products - Current user UID: ' . $uid);

                // Filter products by seller_id (own products)
                $userProducts = collect($allProducts)->filter(function ($product) use ($uid) {
                    $hasCreatedBy = isset($product['created_by']) && $product['created_by'] === $uid;
                    $hasUserId = isset($product['user_id']) && $product['user_id'] === $uid;
                    $hasSellerUid = isset($product['seller_uid']) && $product['seller_uid'] === $uid;
                    $hasSellerId = isset($product['seller_id']) && $product['seller_id'] === $uid;

                    return $hasCreatedBy || $hasUserId || $hasSellerUid || $hasSellerId;
                });

                // Get purchased products from purchases collection
                $firestoreService = new \App\Services\FirestoreSimple();
                $purchasesResponse = $firestoreService->listDocuments('purchases');
                $purchasedProducts = collect();

                if (isset($purchasesResponse['documents'])) {
                    foreach ($purchasesResponse['documents'] as $doc) {
                        $docData = [];
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

                        // Filter by buyer_id
                        if (($docData['buyer_id'] ?? '') === $uid) {
                            $purchasedProducts->push($docData);
                        }
                    }
                }

                $totalUserProducts = $userProducts->count();
                $totalPurchasedProducts = $purchasedProducts->count();

                Log::info('Debug products - Found user products: ' . $totalUserProducts);
                Log::info('Debug products - Found purchased products: ' . $totalPurchasedProducts);
            }

            return view('account.sheets', compact('userData', 'userProducts', 'totalUserProducts', 'purchasedProducts', 'totalPurchasedProducts'));
        } catch (\Exception $e) {
            Log::error('Error fetching user sheets: ' . $e->getMessage());
            return view('account.sheets', compact('userData'))->with('error', 'Có lỗi xảy ra khi tải danh sách sheet nhạc.');
        }
    }

    public function activity()
    {
        $userData = $this->getUserData();

        $activities = [];

        try {
            $uid = session('firebase_uid');
            if ($uid) {
                // Sử dụng ActivityService để lấy tất cả activities đã được sort chính xác chronologically
                $activityService = new ActivityService();
                $activities = $activityService->getUserActivities($uid, 30);

                Log::info('Activities loaded via ActivityService', [
                    'user_id' => $uid,
                    'count' => count($activities)
                ]);
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

    public function downloadSheet($id)
    {
        try {
            // Get current user's UID
            $uid = session('firebase_uid');

            Log::info('Download sheet attempt', [
                'requested_id' => $id,
                'user_uid' => $uid,
                'session_data' => session()->all()
            ]);

            if (!$uid) {
                return response()->json(['error' => 'Bạn cần đăng nhập để tải file'], 401);
            }

            // Initialize services
            $productModel = new Product();
            $firestoreService = new \App\Services\FirestoreSimple();

            // Find the product in products collection OR purchases collection
            $product = null;
            $filePath = null;
            $isPurchased = false;

            // First check purchases collection (for purchased products)
            $purchasesResponse = $firestoreService->listDocuments('purchases');
            if (isset($purchasesResponse['documents'])) {
                foreach ($purchasesResponse['documents'] as $doc) {
                    $docData = [];
                    $docPath = $doc['name'] ?? '';
                    $docId = basename($docPath);

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

                    // Check if this is the purchase record for current user
                    if (($docData['product_id'] ?? '') === $id && ($docData['buyer_id'] ?? '') === $uid) {
                        $product = $docData;
                        $filePath = $docData['file_path'] ?? null;
                        $isPurchased = true;
                        Log::info('Found purchased product', [
                            'product_id' => $id,
                            'file_path' => $filePath,
                            'buyer_id' => $docData['buyer_id'] ?? 'N/A',
                            'product_name' => $docData['product_name'] ?? 'N/A',
                            'all_fields' => $docData
                        ]);
                        break;
                    }
                }
            }

            // If not found in purchases, check products collection (for owned products) 
            if (!$product) {
                $allProducts = $productModel->getAllActive();
                if ($allProducts) {
                    $productsArray = is_array($allProducts) ? $allProducts : $allProducts->toArray();
                    foreach ($productsArray as $prod) {
                        if (($prod['id'] ?? '') === $id) {
                            $product = $prod;
                            $filePath = $prod['file_path'] ?? null;
                            Log::info('Found owned product', [
                                'product_id' => $id,
                                'file_path' => $filePath,
                                'seller_id' => $prod['seller_id'] ?? 'N/A'
                            ]);
                            break;
                        }
                    }
                }
            }

            if (!$product) {
                Log::error('Product not found for download', [
                    'requested_id' => $id,
                    'user_uid' => $uid,
                    'searched_purchases' => isset($purchasesResponse['documents']) ? count($purchasesResponse['documents']) : 0,
                    'searched_products' => isset($allProducts) ? count($allProducts) : 0
                ]);
                return response()->json(['error' => 'Không tìm thấy sheet nhạc hoặc bạn chưa mua sản phẩm này'], 404);
            }

            // Check if user owns this product or has purchased it
            $isOwner = isset($product['seller_id']) && $product['seller_id'] === $uid;
            $hasPurchased = $isPurchased || (isset($product['buyer_id']) && $product['buyer_id'] === $uid);

            Log::info('Download permission check', [
                'product_id' => $id,
                'user_uid' => $uid,
                'is_owner' => $isOwner,
                'has_purchased' => $hasPurchased,
                'is_purchased_flag' => $isPurchased,
                'product_seller_id' => $product['seller_id'] ?? 'N/A',
                'product_buyer_id' => $product['buyer_id'] ?? 'N/A'
            ]);

            if (!$isOwner && !$hasPurchased) {
                Log::error('Download permission denied', [
                    'product_id' => $id,
                    'user_uid' => $uid,
                    'is_owner' => $isOwner,
                    'has_purchased' => $hasPurchased,
                    'product_seller_id' => $product['seller_id'] ?? 'N/A',
                    'product_buyer_id' => $product['buyer_id'] ?? 'N/A'
                ]);
                return response()->json(['error' => 'Bạn không có quyền tải file này. Bạn chỉ có thể tải những sheet đã mua hoặc của chính mình.'], 403);
            }

            if (!$filePath) {
                return response()->json(['error' => 'Đường dẫn file không tồn tại trong database'], 404);
            }

            // Normalize file path (remove leading slash if exists)
            $normalizedPath = ltrim($filePath, '/\\');

            // Try multiple possible locations for the file
            $possiblePaths = [
                public_path($normalizedPath),
                public_path($filePath),
                storage_path('app/public/' . $normalizedPath),
                storage_path('app/' . $normalizedPath),
                base_path($normalizedPath)
            ];

            Log::info("Download debug - File search details", [
                'original_file_path' => $filePath,
                'normalized_path' => $normalizedPath,
                'product_name' => $product['product_name'] ?? $product['name'] ?? 'N/A',
                'is_purchased' => $isPurchased,
                'is_owner' => $isOwner ?? false
            ]);

            $fullPath = null;
            foreach ($possiblePaths as $index => $path) {
                $exists = file_exists($path);
                Log::info("Download debug - Path {$index}: {$path} - Exists: " . ($exists ? 'YES' : 'NO'));

                if ($exists) {
                    $fullPath = $path;
                    Log::info("Download debug - Selected path: {$fullPath}");
                    break;
                }
            }

            // Additional debug: Try to find files in the seller's directory
            if (!$fullPath) {
                $sellerPath = public_path('seller_files');
                Log::info("Download debug - Exploring seller_files directory", [
                    'seller_files_path' => $sellerPath,
                    'exists' => file_exists($sellerPath) ? 'YES' : 'NO'
                ]);

                if (file_exists($sellerPath)) {
                    // List directories in seller_files
                    $directories = scandir($sellerPath);
                    Log::info("Download debug - Seller directories", [
                        'directories' => array_filter($directories, function ($item) use ($sellerPath) {
                            return $item !== '.' && $item !== '..' && is_dir($sellerPath . '/' . $item);
                        })
                    ]);
                }
            }

            if (!$fullPath) {
                // Let's explore what files actually exist in the seller directory and try to find alternatives
                $sellerId = $product['seller_id'] ?? 'unknown';
                $sellerDir = public_path("seller_files/{$sellerId}");
                $productName = $product['product_name'] ?? $product['name'] ?? '';

                if (is_dir($sellerDir)) {
                    $actualFiles = [];
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($sellerDir),
                        \RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($iterator as $file) {
                        if ($file->isFile()) {
                            $relativePath = str_replace($sellerDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
                            $actualFiles[] = str_replace('\\', '/', $relativePath);
                        }
                    }

                    Log::info('File not found - searching alternatives', [
                        'seller_id' => $sellerId,
                        'seller_directory' => $sellerDir,
                        'looking_for' => basename($filePath),
                        'product_name' => $productName,
                        'actual_files' => $actualFiles
                    ]);

                    // ❌ DISABLED: All fallback matching to prevent wrong file downloads
                    // The purchase records contain incorrect file paths, 
                    // so any fallback logic might download wrong files
                    Log::warning('File fallback matching disabled to prevent wrong downloads', [
                        'product_name' => $productName,
                        'original_file_path' => $filePath,
                        'available_files' => $actualFiles,
                        'reason' => 'Purchase records contain outdated file paths'
                    ]);
                }

                if (!$fullPath) {
                    Log::error('File not found - no exact match found', [
                        'original_path' => $filePath,
                        'tried_paths' => $possiblePaths,
                        'seller_directory' => $sellerDir ?? 'N/A',
                        'product_name' => $productName,
                        'available_files' => $actualFiles ?? []
                    ]);

                    $errorMessage = 'File không tìm thấy';
                    if (empty($filePath)) {
                        $errorMessage = 'Sản phẩm này chưa có file đính kèm. Vui lòng liên hệ người bán.';
                    } else {
                        $errorMessage = "File '$productName' không tồn tại hoặc đã bị di chuyển. Vui lòng liên hệ người bán.";
                    }

                    return response()->json([
                        'error' => $errorMessage,
                        'file_path' => $filePath,
                        'product_name' => $productName
                    ], 404);
                }
            }

            // Get original filename from file_path
            $originalFilename = basename($filePath);

            // Log for debugging
            Log::info('Download filename processing', [
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'product_name' => $product['name'] ?? $product['product_name'] ?? 'N/A'
            ]);

            // Determine the best filename to use
            if (!empty($originalFilename) && $originalFilename !== '.' && $originalFilename !== '..' && strlen($originalFilename) > 3) {
                // Use original filename, but clean it up
                $filename = $originalFilename;

                // Remove timestamp prefix if it exists (e.g., "1760092263_")
                $filename = preg_replace('/^\d+_/', '', $filename);

                // Only replace truly dangerous characters for file systems
                $filename = str_replace(['<', '>', ':', '"', '/', '\\', '|', '?', '*'], '_', $filename);
            } else {
                // Fallback to product name
                $productName = $product['product_name'] ?? $product['name'] ?? $product['title'] ?? 'sheet-nhac';

                // Clean product name but preserve Vietnamese characters
                $filename = str_replace(['<', '>', ':', '"', '/', '\\', '|', '?', '*'], '_', $productName);

                // Add .txt extension if no extension exists
                if (!preg_match('/\.(txt|pdf|doc|docx|jpg|png)$/i', $filename)) {
                    $filename .= '.txt';
                }
            }

            // Ensure filename is not too long (Windows limit is 255 characters)
            if (strlen($filename) > 200) {
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $baseName = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 190);
                $filename = $baseName . '.' . ($extension ?: 'txt');
            }

            Log::info('Download filename', [
                'original_file_path' => $filePath,
                'original_filename' => $originalFilename,
                'final_filename' => $filename
            ]);

            return response()->download($fullPath, $filename);
        } catch (\Exception $e) {
            Log::error('Error downloading sheet: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi tải file'], 500);
        }
    }
}
