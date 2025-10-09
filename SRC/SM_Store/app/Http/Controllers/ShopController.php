<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * Hiển thị trang shop với danh sách sản phẩm
     */
    public function index(Request $request)
    {
        try {
            // Lấy parameters từ request
            $country = $request->get('country', 'all');
            $search = $request->get('search', '');

            // Lấy tất cả sản phẩm active từ Firestore
            $allProducts = $this->productModel->getAllActive();

            // Filter theo quốc gia
            if ($country && $country !== 'all') {
                $allProducts = $allProducts->filter(function ($product) use ($country) {
                    return stripos($product['country_region'] ?? '', $country) !== false;
                });
            }

            // Search theo tên hoặc tác giả
            if ($search) {
                $allProducts = $allProducts->filter(function ($product) use ($search) {
                    $name = $product['name'] ?? '';
                    $author = $product['author'] ?? '';
                    $transcriber = $product['transcribed_by'] ?? '';

                    // Normalize search term and text for better Vietnamese search
                    $searchNormalized = $this->normalizeText($search);
                    $nameNormalized = $this->normalizeText($name);
                    $authorNormalized = $this->normalizeText($author);
                    $transcriberNormalized = $this->normalizeText($transcriber);

                    return stripos($nameNormalized, $searchNormalized) !== false ||
                        stripos($authorNormalized, $searchNormalized) !== false ||
                        stripos($transcriberNormalized, $searchNormalized) !== false ||
                        stripos($name, $search) !== false ||
                        stripos($author, $search) !== false ||
                        stripos($transcriber, $search) !== false;
                });
            }

            // Sắp xếp theo ngày tạo mới nhất
            $products = $allProducts->sortByDesc('created_at')->values();

            // Phân trang thủ công
            $perPage = 12;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedProducts = $products->slice($offset, $perPage);

            // Lấy danh sách quốc gia có sản phẩm
            $allActiveProducts = $this->productModel->getAllActive();
            $countries = $allActiveProducts->pluck('country_region')
                ->filter()
                ->unique()
                ->values();

            Log::info('Shop page loaded', [
                'total_products' => $products->count(),
                'current_page' => $currentPage,
                'country_filter' => $country,
                'search_term' => $search
            ]);

            $userId = session('user_id', '');
            if (empty($userId)) {
                $userId = session('firebase_uid', '');
            }
            return view('page.shop.index', [
                'products' => $paginatedProducts,
                'countries' => $countries,
                'country' => $country,
                'search' => $search,
                'totalProducts' => $products->count(),
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            Log::error('Shop page error: ' . $e->getMessage());

            // Fallback: trả về view với dữ liệu rỗng
            return view('page.shop.index', [
                'products' => collect(),
                'countries' => collect(),
                'country' => 'all',
                'search' => '',
                'totalProducts' => 0,
                'currentPage' => 1,
                'perPage' => 12
            ]);
        }
    }

    /**
     * API endpoint để filter sản phẩm bằng AJAX
     */
    public function filter(Request $request)
    {
        try {
            $country = $request->get('country', 'all');
            $search = $request->get('search', '');

            $allProducts = $this->productModel->getAllActive();

            if ($country && $country !== 'all') {
                $allProducts = $allProducts->filter(function ($product) use ($country) {
                    return stripos($product['country_region'] ?? '', $country) !== false;
                });
            }

            if ($search) {
                $allProducts = $allProducts->filter(function ($product) use ($search) {
                    $name = $product['name'] ?? '';
                    $author = $product['author'] ?? '';
                    $transcriber = $product['transcribed_by'] ?? '';

                    // Normalize search term and text for better Vietnamese search
                    $searchNormalized = $this->normalizeText($search);
                    $nameNormalized = $this->normalizeText($name);
                    $authorNormalized = $this->normalizeText($author);
                    $transcriberNormalized = $this->normalizeText($transcriber);

                    return stripos($nameNormalized, $searchNormalized) !== false ||
                        stripos($authorNormalized, $searchNormalized) !== false ||
                        stripos($transcriberNormalized, $searchNormalized) !== false ||
                        stripos($name, $search) !== false ||
                        stripos($author, $search) !== false ||
                        stripos($transcriber, $search) !== false;
                });
            }

            $products = $allProducts->sortByDesc('created_at')->values();

            return response()->json([
                'success' => true,
                'products' => $products,
                'count' => $products->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Shop filter error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lọc sản phẩm'
            ], 500);
        }
    }

    /**
     * Normalize text để tìm kiếm tiếng Việt tốt hơn
     */
    private function normalizeText($text)
    {
        // Remove Vietnamese diacritics for better search
        $vietnamese = [
            'à',
            'á',
            'ạ',
            'ả',
            'ã',
            'â',
            'ầ',
            'ấ',
            'ậ',
            'ẩ',
            'ẫ',
            'ă',
            'ằ',
            'ắ',
            'ặ',
            'ẳ',
            'ẵ',
            'è',
            'é',
            'ẹ',
            'ẻ',
            'ẽ',
            'ê',
            'ề',
            'ế',
            'ệ',
            'ể',
            'ễ',
            'ì',
            'í',
            'ị',
            'ỉ',
            'ĩ',
            'ò',
            'ó',
            'ọ',
            'ỏ',
            'õ',
            'ô',
            'ồ',
            'ố',
            'ộ',
            'ổ',
            'ỗ',
            'ơ',
            'ờ',
            'ớ',
            'ợ',
            'ở',
            'ỡ',
            'ù',
            'ú',
            'ụ',
            'ủ',
            'ũ',
            'ư',
            'ừ',
            'ứ',
            'ự',
            'ử',
            'ữ',
            'ỳ',
            'ý',
            'ỵ',
            'ỷ',
            'ỹ',
            'đ',
            'À',
            'Á',
            'Ạ',
            'Ả',
            'Ã',
            'Â',
            'Ầ',
            'Ấ',
            'Ậ',
            'Ẩ',
            'Ẫ',
            'Ă',
            'Ằ',
            'Ắ',
            'Ặ',
            'Ẳ',
            'Ẵ',
            'È',
            'É',
            'Ẹ',
            'Ẻ',
            'Ẽ',
            'Ê',
            'Ề',
            'Ế',
            'Ệ',
            'Ể',
            'Ễ',
            'Ì',
            'Í',
            'Ị',
            'Ỉ',
            'Ĩ',
            'Ò',
            'Ó',
            'Ọ',
            'Ỏ',
            'Õ',
            'Ô',
            'Ồ',
            'Ố',
            'Ộ',
            'Ổ',
            'Ỗ',
            'Ơ',
            'Ờ',
            'Ớ',
            'Ợ',
            'Ở',
            'Ỡ',
            'Ù',
            'Ú',
            'Ụ',
            'Ủ',
            'Ũ',
            'Ư',
            'Ừ',
            'Ứ',
            'Ự',
            'Ử',
            'Ữ',
            'Ỳ',
            'Ý',
            'Ỵ',
            'Ỷ',
            'Ỹ',
            'Đ'
        ];

        $latin = [
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'a',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'e',
            'i',
            'i',
            'i',
            'i',
            'i',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'o',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'u',
            'y',
            'y',
            'y',
            'y',
            'y',
            'd',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'A',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'E',
            'I',
            'I',
            'I',
            'I',
            'I',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'O',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'U',
            'Y',
            'Y',
            'Y',
            'Y',
            'Y',
            'D'
        ];

        return str_replace($vietnamese, $latin, $text);
    }

    /**
     * Chuyển đổi tên quốc gia tiếng Việt sang flag emoji
     */
    private function getCountryFlag($countryName)
    {
        $flags = [
            'Việt Nam' => '🇻🇳',
            'Hàn Quốc' => '🇰🇷',
            'Nhật Bản' => '🇯🇵',
            'Trung Quốc' => '🇨🇳',
            'Âu Mỹ' => '🇺🇸',
            'US-UK' => '🇺🇸',
            'Khác' => '🌍'
        ];

        foreach ($flags as $country => $flag) {
            if (str_contains($countryName, $country)) {
                return $flag;
            }
        }

        return '🌍';
    }
}
