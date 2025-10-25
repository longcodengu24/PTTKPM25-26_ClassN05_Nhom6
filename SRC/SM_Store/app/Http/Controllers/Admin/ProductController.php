<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productModel;
    protected $firestoreService;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị danh sách sản phẩm cho admin
     */
    public function index()
    {
        try {
            $products = $this->productModel->getAllActive();
            $productsArray = is_array($products) ? $products : $products->toArray();

            return view('admin.products.products', [
                'products' => $productsArray
            ]);
        } catch (\Exception $e) {
            Log::error('Admin ProductController index error: ' . $e->getMessage());
            return view('admin.products.products', [
                'products' => []
            ])->with('error', 'Có lỗi xảy ra khi tải danh sách sản phẩm');
        }
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm
     */
    public function edit($id)
    {
        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                return redirect()->route('admin.products')->with('error', 'Không tìm thấy sản phẩm');
            }

            return view('admin.products.edit', [
                'product' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Admin ProductController edit error: ' . $e->getMessage());
            return redirect()->route('admin.products')->with('error', 'Có lỗi xảy ra');
        }
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'status' => 'required|in:active,inactive'
            ]);

            $productData = [
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'category' => $request->category,
                'status' => $request->status,
                'updated_at' => now()->toISOString()
            ];

            $result = $this->firestoreService->updateDocument('products', $id, $productData);

            if ($result) {
                return redirect()->route('admin.products')->with('success', 'Cập nhật sản phẩm thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật sản phẩm');
            }
        } catch (\Exception $e) {
            Log::error('Admin ProductController update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa sản phẩm
     */
    public function destroy($id)
    {
        try {
            $result = $this->firestoreService->updateDocument('products', $id, [
                'status' => 'inactive',
                'deleted_at' => now()->toISOString()
            ]);

            if ($result) {
                return redirect()->route('admin.products')->with('success', 'Xóa sản phẩm thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa sản phẩm');
            }
        } catch (\Exception $e) {
            Log::error('Admin ProductController destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
