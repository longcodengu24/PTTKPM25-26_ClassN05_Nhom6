<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Chuẩn hóa YouTube URL về dạng embed
     */
    private function normalizeYouTubeUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        // Các pattern YouTube
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                $videoId = $matches[1];
                return "https://www.youtube.com/embed/{$videoId}";
            }
        }

        return $url; // Trả về URL gốc nếu không match
    }

    public function index()
    {
        try {
            $sellerId = session('firebase_uid');

            // 🔥 VALIDATION: Đảm bảo có seller_id
            if (!$sellerId) {
                Log::error('No seller_id in session for index');
                return redirect()->route('login')
                    ->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            }

            $products = $this->productModel->getBySeller($sellerId, 50);

            Log::info('Products loaded for seller', ['seller_id' => $sellerId, 'count' => count($products)]);

            return view('saler.products.products', compact('products'));
        } catch (\Exception $e) {
            Log::error('Product index error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi khi tải danh sách sản phẩm.');
        }
    }

    public function create()
    {
        return view('saler.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'music_file' => 'required|file|mimes:txt,json|max:2048',
            'name' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'transcribed_by' => 'nullable|string|max:255',
            'country_region' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'youtube_url' => 'nullable|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean'
        ]);

        $file = $request->file('music_file');
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Get seller ID
        $sellerId = session('firebase_uid');

        // Tạo thư mục theo seller và quốc gia
        $countryFolder = $this->getCountryFolder($request->input('country_region'));
        $uploadPath = 'seller_files/' . $sellerId . '/songs/' . $countryFolder;
        if (!is_dir(public_path($uploadPath))) {
            mkdir(public_path($uploadPath), 0755, true);
        }

        // Lưu file nhạc
        $file->move(public_path($uploadPath), $fileName);
        $filePath = $uploadPath . '/' . $fileName;

        // Xử lý upload ảnh (nếu có)
        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imageFile = $request->file('cover_image');
            $imageName = time() . '_cover_' . $imageFile->getClientOriginalName();

            // Tạo thư mục cho ảnh
            $imageUploadPath = 'seller_files/' . $sellerId . '/images/covers/' . $countryFolder;
            if (!is_dir(public_path($imageUploadPath))) {
                mkdir(public_path($imageUploadPath), 0755, true);
            }

            // Lưu ảnh
            $imageFile->move(public_path($imageUploadPath), $imageName);
            $imagePath = $imageUploadPath . '/' . $imageName;
        }

        try {
            $sellerId = session('firebase_uid') ?? 'demo_seller_id';

            // Tạo sản phẩm mới với Firebase (giữ nguyên tên cột)
            $productData = [
                'name' => $request->input('name'),
                'author' => $request->input('author') ?: 'Chưa xác định',
                'transcribed_by' => $request->input('transcribed_by') ?: 'Seller',
                'country_region' => $request->input('country_region'),
                'file_path' => $filePath,
                'image_path' => $imagePath,
                'price' => $request->input('price'),
                'youtube_demo_url' => $this->normalizeYouTubeUrl($request->input('youtube_url')),
                'downloads_count' => 0,
                'sold_count' => 0,
                'is_active' => $request->boolean('is_active', false),
                'seller_id' => $sellerId
            ];

            $product = $this->productModel->create($productData);

            // Tạo activity record cho việc upload sheet
            $activityService = new ActivityService();
            $activityService->createActivity(
                $sellerId,
                'upload',
                "Tải lên sheet nhạc '{$request->input('name')}' - Giá: " . number_format($request->input('price')) . "đ",
                [
                    'product_id' => $product['id'] ?? null,
                    'product_name' => $request->input('name'),
                    'price' => $request->input('price'),
                    'country_region' => $request->input('country_region')
                ]
            );

            Log::info('Sheet uploaded successfully with activity', [
                'seller_id' => $sellerId,
                'product_name' => $request->input('name'),
                'activity_created' => 'yes'
            ]);

            return redirect()->route('saler.products.index')
                ->with('success', 'Đã thêm bản nhạc mới thành công!');
        } catch (\Exception $e) {
            Log::error('Product store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Get folder name based on country
     */
    private function getCountryFolder($country)
    {
        $folders = [
            'Việt Nam' => 'vietnam',
            'Hàn Quốc' => 'korea',
            'Nhật Bản' => 'japan',
            'Trung Quốc' => 'china',
            'Âu Mỹ' => 'western',
            'Khác' => 'others'
        ];

        return $folders[$country] ?? 'others';
    }

    private function parseFileInfo($content, $fileName)
    {
        // Mặc định lấy tên từ filename
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $author = 'Chưa xác định';
        $transcribed_by = 'Admin';

        // Làm sạch content và xử lý encoding
        $cleanContent = $content; // Không trim ngay để giữ nguyên BOM

        Log::info('Raw file analysis:', [
            'contentLength' => strlen($cleanContent),
            'firstBytes' => bin2hex(substr($cleanContent, 0, 10)),
            'fileName' => $fileName
        ]);

        // Phát hiện và xử lý các loại BOM với thứ tự ưu tiên
        $boms = [
            "\xFF\xFE\x00\x00" => 'UTF-32 LE BOM', // UTF-32 LE BOM (4 bytes) - check trước
            "\x00\x00\xFE\xFF" => 'UTF-32 BE BOM', // UTF-32 BE BOM (4 bytes)
            "\xFF\xFE" => 'UTF-16 LE BOM',         // UTF-16 LE BOM (2 bytes)
            "\xFE\xFF" => 'UTF-16 BE BOM',         // UTF-16 BE BOM (2 bytes)
            "\xEF\xBB\xBF" => 'UTF-8 BOM',         // UTF-8 BOM (3 bytes)
        ];

        $detectedBom = null;
        foreach ($boms as $bom => $type) {
            if (substr($cleanContent, 0, strlen($bom)) === $bom) {
                $cleanContent = substr($cleanContent, strlen($bom));
                $detectedBom = $type;
                Log::info("Detected and removed {$type} from file content");
                break;
            }
        }

        // Xử lý encoding với ưu tiên UTF-16 LE
        $originalEncoding = null;

        // Nếu có BOM UTF-16 LE hoặc detect được UTF-16 LE
        if ($detectedBom === 'UTF-16 LE BOM' || (!$detectedBom && !mb_check_encoding($cleanContent, 'UTF-8'))) {
            // Thử UTF-16 LE trước
            $encoding = mb_detect_encoding($cleanContent, ['UTF-16LE', 'UTF-16BE', 'UTF-32LE', 'UTF-32BE'], true);

            if ($encoding) {
                $originalEncoding = $encoding;
                $cleanContent = mb_convert_encoding($cleanContent, 'UTF-8', $encoding);
                Log::info("Converted content from {$encoding} to UTF-8");
            } else if (!mb_check_encoding($cleanContent, 'UTF-8')) {
                // Fallback: thử các encoding khác
                $encoding = mb_detect_encoding($cleanContent, ['ISO-8859-1', 'Windows-1252', 'ASCII'], true);
                if ($encoding) {
                    $originalEncoding = $encoding;
                    $cleanContent = mb_convert_encoding($cleanContent, 'UTF-8', $encoding);
                    Log::info("Fallback: Converted content from {$encoding} to UTF-8");
                }
            }
        }

        // Chuẩn hóa line endings (Unix LF -> Windows CRLF or vice versa)
        $cleanContent = str_replace(["\r\n", "\r"], "\n", $cleanContent); // Normalize to LF

        // Làm sạch cuối cùng
        $cleanContent = trim($cleanContent);

        // Xử lý các ký tự ẩn khác
        $cleanContent = preg_replace('/^[\x00-\x1F\x7F]+/', '', $cleanContent);
        $cleanContent = preg_replace('/[\x00-\x1F\x7F]+$/', '', $cleanContent);
        $cleanContent = trim($cleanContent);

        Log::info('Attempting to parse file:', [
            'fileName' => $fileName,
            'contentLength' => strlen($cleanContent),
            'contentStart' => substr($cleanContent, 0, 200), // Tăng lên 200 ký tự
            'contentEnd' => substr($cleanContent, -50), // 50 ký tự cuối
            'firstChar' => ord($cleanContent[0] ?? ''), // Mã ASCII ký tự đầu
            'encoding' => mb_detect_encoding($cleanContent), // Detect encoding
            'hasUTF8BOM' => substr($cleanContent, 0, 3) === "\xEF\xBB\xBF" // Check BOM
        ]);

        // Thử parse JSON trước (bất kể đuôi file)
        $jsonData = json_decode($cleanContent, true);
        $jsonError = json_last_error();

        Log::info('JSON Parse attempt:', [
            'jsonError' => $jsonError,
            'jsonErrorMsg' => json_last_error_msg(),
            'isArray' => is_array($jsonData),
            'dataType' => gettype($jsonData)
        ]);

        if ($jsonError === JSON_ERROR_NONE && is_array($jsonData)) {
            Log::info('JSON parsed successfully, extracting song data...');

            // Xử lý cả JSON array và JSON object
            if (isset($jsonData[0]) && is_array($jsonData[0])) {
                // Nếu là array of objects, lấy phần tử đầu tiên
                $songData = $jsonData[0];
                Log::info('Using first element of JSON array');
            } else {
                // Nếu là object đơn lẻ hoặc array đơn giản
                $songData = $jsonData;
                Log::info('Using JSON data directly');
            }

            Log::info('Song data extracted:', [
                'available_keys' => array_keys($songData),
                'name' => $songData['name'] ?? 'not found',
                'author' => $songData['author'] ?? 'not found',
                'transcribedBy' => $songData['transcribedBy'] ?? 'not found'
            ]);

            // Lấy thông tin từ JSON
            if (isset($songData['name']) && !empty(trim($songData['name']))) {
                $name = trim($songData['name']);
                Log::info('Name extracted from JSON: ' . $name);
            }

            if (isset($songData['author']) && !empty(trim($songData['author']))) {
                $author = trim($songData['author']);
                Log::info('Author extracted from JSON: ' . $author);
            }

            if (isset($songData['transcribedBy']) && !empty(trim($songData['transcribedBy']))) {
                $transcribed_by = trim($songData['transcribedBy']);
                Log::info('TranscribedBy extracted from JSON: ' . $transcribed_by);
            }

            // Debug: Log kết quả cuối cùng
            Log::info('Final JSON Parse Result:', [
                'name' => $name,
                'author' => $author,
                'transcribed_by' => $transcribed_by
            ]);
        } else {
            // Nếu không phải JSON, parse như text thô (logic cũ)
            if (preg_match('/name[:\s]+(.*)/i', $content, $matches)) {
                $name = trim($matches[1]);
            }

            if (preg_match('/author[:\s]+(.*)/i', $content, $matches)) {
                $author = trim($matches[1]);
            } elseif (preg_match('/composer[:\s]+(.*)/i', $content, $matches)) {
                $author = trim($matches[1]);
            } elseif (preg_match('/by[:\s]+(.*)/i', $content, $matches)) {
                $author = trim($matches[1]);
            }

            if (preg_match('/transcribed[:\s]+by[:\s]+(.*)/i', $content, $matches)) {
                $transcribed_by = trim($matches[1]);
            } elseif (preg_match('/transcriber[:\s]+(.*)/i', $content, $matches)) {
                $transcribed_by = trim($matches[1]);
            }

            Log::info('Text Parse Result:', [
                'name' => $name,
                'author' => $author,
                'transcribed_by' => $transcribed_by
            ]);
        }

        return [
            'name' => $name,
            'author' => $author,
            'transcribed_by' => $transcribed_by
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $sellerId = session('firebase_uid');

            // 🔥 VALIDATION: Đảm bảo có seller_id
            if (!$sellerId) {
                Log::error('No seller_id in session for edit');
                return redirect()->route('login')
                    ->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            }

            $product = $this->productModel->findBySeller($id, $sellerId);

            if (!$product) {
                Log::error('Product not found for edit', ['id' => $id, 'seller_id' => $sellerId]);
                return redirect()->route('saler.products.index')
                    ->with('error', 'Không tìm thấy sản phẩm.');
            }

            Log::info('Product edit loaded', ['id' => $id, 'seller_id' => $sellerId, 'product_name' => $product['name'] ?? 'unknown']);

            return view('saler.products.edit', compact('product'));
        } catch (\Exception $e) {
            Log::error('Product edit error: ' . $e->getMessage());
            return redirect()->route('saler.products.index')
                ->with('error', 'Có lỗi khi tải form chỉnh sửa.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $sellerId = session('firebase_uid');

            // 🔥 VALIDATION: Đảm bảo có seller_id
            if (!$sellerId) {
                Log::error('No seller_id in session');
                return redirect()->route('login')
                    ->with('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            }

            // Lấy thông tin sản phẩm hiện tại
            $product = $this->productModel->findBySeller($id, $sellerId);
            if (!$product) {
                Log::error('Product not found', ['id' => $id, 'seller_id' => $sellerId]);
                return redirect()->route('saler.products.index')
                    ->with('error', 'Không tìm thấy sản phẩm.');
            }

            $request->validate([
                'music_file' => 'nullable|file|mimes:txt,json|max:2048',
                'name' => 'required|string|max:255',
                'author' => 'nullable|string|max:255',
                'transcribed_by' => 'nullable|string|max:255',
                'country_region' => 'required|string|max:100',
                'price' => 'required|numeric|min:0',
                'youtube_url' => 'nullable|url',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'is_active' => 'nullable|boolean'
            ]);

            // Xử lý upload file mới (nếu có)
            $updateData = [
                'name' => $request->input('name'),
                'author' => $request->input('author') ?: 'Chưa xác định',
                'transcribed_by' => $request->input('transcribed_by') ?: 'Seller',
                'country_region' => $request->input('country_region'),
                'price' => $request->input('price'),
                'youtube_demo_url' => $this->normalizeYouTubeUrl($request->input('youtube_url')),
                'is_active' => $request->boolean('is_active', false),
                'seller_id' => $sellerId,  // 🔥 QUAN TRỌNG: Đảm bảo seller_id không bị mất
                'downloads_count' => $product['downloads_count'] ?? 0,  // 🔥 Preserve downloads count
                'sold_count' => $product['sold_count'] ?? 0,  // 🔥 Preserve sold count
                'file_path' => $product['file_path'] ?? '',  // 🔥 Preserve file path (sẽ được update nếu có file mới)
                'image_path' => $product['image_path'] ?? null  // 🔥 Preserve image path (sẽ được update nếu có ảnh mới)
            ];

            if ($request->hasFile('music_file')) {
                // Xóa file cũ
                if (!empty($product['file_path']) && file_exists(public_path($product['file_path']))) {
                    unlink(public_path($product['file_path']));
                }

                $file = $request->file('music_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $countryFolder = $this->getCountryFolder($request->input('country_region'));
                $uploadPath = 'seller_files/' . $sellerId . '/songs/' . $countryFolder;
                if (!is_dir(public_path($uploadPath))) {
                    mkdir(public_path($uploadPath), 0755, true);
                }

                $file->move(public_path($uploadPath), $fileName);
                $updateData['file_path'] = $uploadPath . '/' . $fileName;
            }

            // Xử lý upload ảnh mới (nếu có)
            if ($request->hasFile('cover_image')) {
                // Xóa ảnh cũ
                if (!empty($product['image_path']) && file_exists(public_path($product['image_path']))) {
                    unlink(public_path($product['image_path']));
                }

                $imageFile = $request->file('cover_image');
                $imageName = time() . '_cover_' . $imageFile->getClientOriginalName();
                $countryFolder = $this->getCountryFolder($request->input('country_region'));
                $imageUploadPath = 'seller_files/' . $sellerId . '/images/covers/' . $countryFolder;
                if (!is_dir(public_path($imageUploadPath))) {
                    mkdir(public_path($imageUploadPath), 0755, true);
                }

                $imageFile->move(public_path($imageUploadPath), $imageName);
                $updateData['image_path'] = $imageUploadPath . '/' . $imageName;
            }

            // Cập nhật thông tin sản phẩm
            Log::info('Update product data: ', [
                'id' => $id,
                'seller_id' => $sellerId,
                'update_data' => $updateData
            ]);

            $this->productModel->update($id, $updateData, $sellerId);

            // Tạo activity record cho việc cập nhật sheet
            $activityService = new ActivityService();
            $activityService->createActivity(
                $sellerId,
                'update',
                "Cập nhật sheet nhạc '{$updateData['name']}' - Giá: " . number_format($updateData['price']) . "đ",
                [
                    'product_id' => $id,
                    'product_name' => $updateData['name'],
                    'price' => $updateData['price'],
                    'country_region' => $updateData['country_region'] ?? null
                ]
            );

            return redirect()->route('saler.products.index')
                ->with('success', 'Đã cập nhật bản nhạc "' . $updateData['name'] . '" thành công!');
        } catch (\Exception $e) {
            Log::error('Update product error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật bản nhạc. Vui lòng thử lại.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $sellerId = session('firebase_uid') ?? 'demo_seller_id';

            // Lấy thông tin sản phẩm để xóa files
            $product = $this->productModel->findBySeller($id, $sellerId);
            if (!$product) {
                return redirect()->route('saler.products.index')
                    ->with('error', 'Không tìm thấy sản phẩm.');
            }

            // Xóa file nhạc nếu tồn tại
            if (!empty($product['file_path']) && file_exists(public_path($product['file_path']))) {
                unlink(public_path($product['file_path']));
            }

            // Xóa ảnh cover nếu tồn tại
            if (!empty($product['image_path']) && file_exists(public_path($product['image_path']))) {
                unlink(public_path($product['image_path']));
            }

            // Xóa bản ghi trong Firebase
            $this->productModel->delete($id, $sellerId);

            // Tạo activity record cho việc xóa sheet
            $activityService = new ActivityService();
            $activityService->createActivity(
                $sellerId,
                'delete',
                "Xóa sheet nhạc '{$product['name']}' - Giá: " . number_format($product['price'] ?? 0) . "đ",
                [
                    'product_id' => $id,
                    'product_name' => $product['name'],
                    'price' => $product['price'] ?? 0,
                    'country_region' => $product['country_region'] ?? null
                ]
            );

            return redirect()->route('saler.products.index')
                ->with('success', 'Đã xóa bản nhạc "' . ($product['name'] ?? 'sản phẩm') . '" thành công!');
        } catch (\Exception $e) {
            Log::error('Delete product error: ' . $e->getMessage());

            return redirect()->route('saler.products.index')
                ->with('error', 'Có lỗi xảy ra khi xóa bản nhạc. Vui lòng thử lại.');
        }
    }

    /**
     * API endpoint để preview thông tin từ file upload
     */
    public function previewFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:txt,json|max:2048'
            ]);

            $file = $request->file('file');
            $content = file_get_contents($file->getPathname());
            $fileName = $file->getClientOriginalName();

            // Parse thông tin từ file
            $parsedInfo = $this->parseFileInfo($content, $fileName);

            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $parsedInfo['name'],
                    'author' => $parsedInfo['author'],
                    'transcribed_by' => $parsedInfo['transcribed_by'],
                    'file_name' => $fileName,
                    'file_size' => $this->formatFileSize($file->getSize())
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('File preview error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Không thể đọc file. Vui lòng kiểm tra định dạng file.'
            ], 400);
        }
    }

    /**
     * Format file size to human readable
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
