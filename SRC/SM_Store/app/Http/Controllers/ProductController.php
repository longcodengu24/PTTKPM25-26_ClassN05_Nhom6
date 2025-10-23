<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Lấy thông tin sản phẩm theo ID
     */
    public function getProductById($id)
    {
        try {
            Log::info('📦 Getting product by ID', ['product_id' => $id]);

            // Lấy tất cả sản phẩm
            $allProducts = $this->productModel->getAllActive();
            $productsArray = is_array($allProducts) ? $allProducts : $allProducts->toArray();

            // Tìm sản phẩm theo ID
            foreach ($productsArray as $product) {
                if (($product['id'] ?? '') === $id) {
                    Log::info('✅ Product found', [
                        'product_id' => $id,
                        'seller_id' => $product['seller_id'] ?? null,
                        'name' => $product['name'] ?? null
                    ]);

                    return response()->json([
                        'success' => true,
                        'data' => $product
                    ]);
                }
            }

            // Không tìm thấy
            Log::warning('⚠️ Product not found', ['product_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm'
            ], 404);

        } catch (\Exception $e) {
            Log::error('❌ Error getting product: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy thông tin sản phẩm: ' . $e->getMessage()
            ], 500);
        }
    }
}
