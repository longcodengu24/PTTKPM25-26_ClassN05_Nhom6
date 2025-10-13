<?php

namespace App\Http\Controllers\Saler;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalerController extends Controller
{
    // Hiển thị danh sách products
    public function index()
    {
        $productModel = new Product();
        $allProducts = $productModel->getAllActive();

        // Convert to array if needed
        $products = is_array($allProducts) ? $allProducts : $allProducts->toArray();

        // Filter products by current seller
        $sellerUid = session('firebase_uid');
        $sellerProducts = [];

        if ($sellerUid) {
            foreach ($products as $product) {
                if (isset($product['seller_id']) && $product['seller_id'] === $sellerUid) {
                    $sellerProducts[] = [
                        'id' => $product['id'] ?? '',
                        'title' => $product['name'] ?? '---',
                        'composer' => $product['author'] ?? '---',
                        'genre' => $product['genre'] ?? '---',
                        'difficulty' => $product['difficulty'] ?? '---',
                        'price' => $product['price'] ?? 0,
                        'status' => $product['is_active'] ? 'published' : 'draft',
                        'preview_image_url' => $product['image_path'] ?? '',
                    ];
                }
            }
        }

        return view('saler.products.index', ['sheets' => $sellerProducts]);
    }

    // Hiển thị form thêm mới
    public function create()
    {
        return view('saler.products.create');
    }

    // Xử lý lưu product mới
    public function store(Request $req)
    {
        $data = $req->validate([
            'title'          => 'required|string|max:150',
            'composer'       => 'required|string|max:150',
            'genre'          => 'nullable|string|max:50',
            'difficulty'     => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'country_region' => 'nullable|string|max:100',
            'youtube_demo_url' => 'nullable|url',
        ]);

        $productData = [
            'name' => $data['title'],
            'author' => $data['composer'],
            'transcribed_by' => 'Saler: ' . session('firebase_uid'),
            'country_region' => $data['country_region'] ?? 'Vietnam',
            'file_path' => '', // Will be updated when file is uploaded
            'image_path' => '', // Will be updated when image is uploaded
            'price' => (int) $data['price'],
            'youtube_demo_url' => $data['youtube_demo_url'] ?? '',
            'downloads_count' => 0,
            'is_active' => true,
            'seller_id' => session('firebase_uid'),
            'genre' => $data['genre'] ?? '',
            'difficulty' => $data['difficulty'] ?? '',
            'description' => $data['description'] ?? ''
        ];

        try {
            $productModel = new Product();
            $newProduct = $productModel->create($productData);

            // 📢 Tạo thông báo activity cho seller
            if ($newProduct) {
                try {
                    $activityService = new ActivityService();
                    $activityService->createActivity(
                        session('firebase_uid'),
                        'upload',
                        'Đã tải lên sheet nhạc "' . $productData['name'] . '"',
                        [
                            'product_name' => $productData['name'],
                            'price' => $productData['price'],
                            'composer' => $productData['author'],
                            'amount' => '+0 xu' // Upload doesn't give coins immediately
                        ]
                    );

                    Log::info('Activity notification created for product upload', [
                        'seller_uid' => session('firebase_uid'),
                        'product_name' => $productData['name']
                    ]);
                } catch (\Exception $activityError) {
                    Log::error('Failed to create activity notification: ' . $activityError->getMessage());
                    // Don't fail the whole process if activity creation fails
                }
            }

            return redirect()->route('saler.products')
                ->with('success', 'Đã thêm product thành công: ' . $productData['name']);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo product');
        }
    }

    // Hiển thị form sửa
    public function edit($id)
    {
        try {
            $productModel = new Product();
            $allProducts = $productModel->getAllActive();
            $productsArray = is_array($allProducts) ? $allProducts : $allProducts->toArray();

            // Find the product by ID
            $product = null;
            foreach ($productsArray as $prod) {
                if (($prod['id'] ?? '') === $id) {
                    $product = $prod;
                    break;
                }
            }

            if (!$product) {
                return redirect()->route('saler.products')->with('error', 'Không tìm thấy product');
            }

            // Check if current user owns this product
            $sellerUid = session('firebase_uid');
            if (($product['seller_id'] ?? '') !== $sellerUid) {
                return redirect()->route('saler.products')->with('error', 'Bạn không có quyền chỉnh sửa product này');
            }

            // Convert to format expected by view
            $sheet = [
                'id' => $product['id'] ?? '',
                'title' => $product['name'] ?? '',
                'composer' => $product['author'] ?? '',
                'genre' => $product['genre'] ?? '',
                'difficulty' => $product['difficulty'] ?? '',
                'description' => $product['description'] ?? '',
                'price' => $product['price'] ?? 0,
                'country_region' => $product['country_region'] ?? '',
                'youtube_demo_url' => $product['youtube_demo_url'] ?? '',
                'status' => $product['is_active'] ? 'published' : 'draft',
                'preview_image_url' => $product['image_path'] ?? '',
            ];

            return view('saler.products.edit', ['sheet' => $sheet]);
        } catch (\Exception $e) {
            Log::error('Error editing product: ' . $e->getMessage());
            return redirect()->route('saler.products')->with('error', 'Có lỗi xảy ra');
        }
    }

    // Cập nhật product
    public function update($id, Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:150',
            'composer'       => 'required|string|max:150',
            'genre'          => 'nullable|string|max:50',
            'difficulty'     => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'country_region' => 'nullable|string|max:100',
            'youtube_demo_url' => 'nullable|url',
            'status'         => 'required|in:draft,published',
        ]);

        try {
            $sellerUid = session('firebase_uid');
            $productModel = new Product();
            $updateData = [
                'name' => $data['title'],
                'author' => $data['composer'],
                'genre' => $data['genre'] ?? '',
                'difficulty' => $data['difficulty'] ?? '',
                'description' => $data['description'] ?? '',
                'price' => (int) $data['price'],
                'country_region' => $data['country_region'] ?? 'Vietnam',
                'youtube_demo_url' => $data['youtube_demo_url'] ?? '',
                'is_active' => $data['status'] === 'published',
                'updated_at' => now()->toISOString(),
            ];

            $productModel->update($id, $updateData, $sellerUid);

            return redirect()->route('saler.products')
                ->with('success', "Đã cập nhật product #{$id} thành công");
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật product');
        }
    }

    // Xóa product
    public function destroy($id)
    {
        try {
            $productModel = new Product();

            // Check if current user owns this product
            $allProducts = $productModel->getAllActive();
            $productsArray = is_array($allProducts) ? $allProducts : $allProducts->toArray();

            $product = null;
            foreach ($productsArray as $prod) {
                if (($prod['id'] ?? '') === $id) {
                    $product = $prod;
                    break;
                }
            }

            if (!$product) {
                return redirect()->route('saler.products')->with('error', 'Không tìm thấy product');
            }

            $sellerUid = session('firebase_uid');
            if (($product['seller_id'] ?? '') !== $sellerUid) {
                return redirect()->route('saler.products')->with('error', 'Bạn không có quyền xóa product này');
            }

            $productModel->delete($id, $sellerUid);

            return redirect()->route('saler.products')
                ->with('success', "Đã xóa product #{$id} thành công");
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()->route('saler.products')->with('error', 'Có lỗi xảy ra khi xóa product');
        }
    }
}
