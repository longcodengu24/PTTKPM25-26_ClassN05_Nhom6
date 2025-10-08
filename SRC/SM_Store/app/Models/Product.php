<?php

namespace App\Models;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class Product
{
    protected $firestoreService;
    protected $collection = 'products';

    protected $fillable = [
        'name',
        'author',
        'transcribed_by',
        'country_region',
        'file_path',
        'image_path',
        'price',
        'youtube_demo_url',
        'downloads_count',
        'is_active',
        'seller_id'
    ];

    protected $attributes = [
        'downloads_count' => 0,
        'is_active' => true,
    ];

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create(array $data)
    {
        try {
            // Thêm timestamps
            $data['created_at'] = now()->toISOString();
            $data['updated_at'] = now()->toISOString();

            // Đảm bảo có default values
            $data['downloads_count'] = $data['downloads_count'] ?? 0;
            $data['is_active'] = $data['is_active'] ?? true;

            // Tạo document trong Firestore
            $documentId = $this->firestoreService->createDocument($this->collection, $data);

            Log::info('Product created successfully', ['id' => $documentId, 'seller_id' => $data['seller_id'] ?? 'unknown']);

            return $documentId;
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy tất cả sản phẩm của seller
     */
    public function getBySeller(string $sellerId, int $limit = 50)
    {
        try {
            $documents = $this->firestoreService->queryDocuments($this->collection, [
                'where' => [
                    ['seller_id', '==', $sellerId]
                ],
                'orderBy' => [
                    ['created_at', 'desc']
                ],
                'limit' => $limit
            ]);

            return array_map(function ($doc) {
                $data = $doc['data'];
                $data['id'] = $doc['id'];
                return $data;
            }, $documents);
        } catch (\Exception $e) {
            Log::error('Error getting products by seller: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tất cả sản phẩm active cho shop
     */
    public function getAllActive(int $limit = 100)
    {
        try {
            $documents = $this->firestoreService->queryDocuments($this->collection, [
                'where' => [
                    ['is_active', '==', true]
                ],
                'orderBy' => [
                    ['created_at', 'desc']
                ],
                'limit' => $limit
            ]);

            return collect(array_map(function ($doc) {
                $data = $doc['data'];
                $data['id'] = $doc['id'];
                return $data;
            }, $documents));
        } catch (\Exception $e) {
            Log::error('Error getting all active products: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Tìm sản phẩm theo ID và seller
     */
    public function findBySeller(string $id, string $sellerId)
    {
        try {
            $document = $this->firestoreService->getDocument($this->collection, $id);

            if ($document && ($document['seller_id'] ?? '') === $sellerId) {
                $document['id'] = $id;
                return $document;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error finding product by seller: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(string $id, array $data, string $sellerId)
    {
        try {
            // Kiểm tra quyền sở hữu
            $existing = $this->findBySeller($id, $sellerId);
            if (!$existing) {
                throw new \Exception('Product not found or access denied');
            }

            // Thêm timestamp
            $data['updated_at'] = now()->toISOString();

            $this->firestoreService->updateDocument($this->collection, $id, $data);

            Log::info('Product updated successfully', ['id' => $id, 'seller_id' => $sellerId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa sản phẩm
     */
    public function delete(string $id, string $sellerId)
    {
        try {
            // Kiểm tra quyền sở hữu
            $existing = $this->findBySeller($id, $sellerId);
            if (!$existing) {
                throw new \Exception('Product not found or access denied');
            }

            $this->firestoreService->deleteDocument($this->collection, $id);

            Log::info('Product deleted successfully', ['id' => $id, 'seller_id' => $sellerId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tìm kiếm sản phẩm của seller
     */
    public function searchBySeller(string $sellerId, string $search = '', string $status = 'all')
    {
        try {
            $whereConditions = [
                ['seller_id', '==', $sellerId]
            ];

            if ($status !== 'all') {
                $isActive = $status === 'active';
                $whereConditions[] = ['is_active', '==', $isActive];
            }

            $documents = $this->firestoreService->queryDocuments($this->collection, [
                'where' => $whereConditions,
                'orderBy' => [
                    ['created_at', 'desc']
                ]
            ]);

            $results = array_map(function ($doc) {
                $data = $doc['data'];
                $data['id'] = $doc['id'];
                return $data;
            }, $documents);

            // Client-side search filtering (do Firestore không hỗ trợ full-text search)
            if (!empty($search)) {
                $search = strtolower($search);
                $results = array_filter($results, function ($product) use ($search) {
                    return strpos(strtolower($product['name'] ?? ''), $search) !== false ||
                        strpos(strtolower($product['author'] ?? ''), $search) !== false;
                });
            }

            return array_values($results);
        } catch (\Exception $e) {
            Log::error('Error searching products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thống kê sản phẩm của seller
     */
    public function getSellerStats(string $sellerId)
    {
        try {
            $products = $this->getBySeller($sellerId, 1000); // Lấy nhiều để thống kê

            $stats = [
                'total' => count($products),
                'active' => 0,
                'inactive' => 0,
                'draft' => 0,
                'downloads' => 0
            ];

            foreach ($products as $product) {
                if ($product['is_active'] ?? false) {
                    $stats['active']++;
                } else {
                    $stats['inactive']++;
                }

                $stats['downloads'] += $product['downloads_count'] ?? 0;
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting seller stats: ' . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'draft' => 0,
                'downloads' => 0
            ];
        }
    }

    /**
     * Upload file và trả về đường dẫn
     */
    public function uploadFile(UploadedFile $file, string $type = 'sheet', string $sellerId = 'default')
    {
        try {
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Tạo thư mục theo seller
            $uploadPath = "seller_files/{$sellerId}/" . ($type === 'image' ? 'images' : 'sheets');

            if (!is_dir(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0755, true);
            }

            // Lưu file
            $file->move(public_path($uploadPath), $fileName);

            return $uploadPath . '/' . $fileName;
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse thông tin từ file content
     */
    public function parseFileInfo(string $content, string $fileName)
    {
        // Mặc định lấy tên từ filename
        $name = pathinfo($fileName, PATHINFO_FILENAME);
        $author = 'Chưa xác định';
        $transcribed_by = 'Seller';

        // Xử lý encoding và BOM
        $cleanContent = $this->cleanFileContent($content);

        // Thử parse JSON trước
        $jsonData = json_decode($cleanContent, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
            return $this->parseJsonContent($jsonData, $name, $author, $transcribed_by);
        }

        // Parse text content
        return $this->parseTextContent($cleanContent, $name, $author, $transcribed_by);
    }

    private function cleanFileContent(string $content)
    {
        // Xử lý BOM
        $boms = [
            "\xFF\xFE\x00\x00" => 4, // UTF-32 LE
            "\x00\x00\xFE\xFF" => 4, // UTF-32 BE  
            "\xFF\xFE" => 2,         // UTF-16 LE
            "\xFE\xFF" => 2,         // UTF-16 BE
            "\xEF\xBB\xBF" => 3,     // UTF-8
        ];

        foreach ($boms as $bom => $length) {
            if (substr($content, 0, $length) === $bom) {
                $content = substr($content, $length);
                break;
            }
        }

        // Normalize line endings và trim
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        return trim($content);
    }

    private function parseJsonContent(array $jsonData, string $defaultName, string $defaultAuthor, string $defaultTranscriber)
    {
        // Xử lý JSON array hoặc object
        $songData = isset($jsonData[0]) && is_array($jsonData[0]) ? $jsonData[0] : $jsonData;

        return [
            'name' => !empty($songData['name']) ? trim($songData['name']) : $defaultName,
            'author' => !empty($songData['author']) ? trim($songData['author']) : $defaultAuthor,
            'transcribed_by' => !empty($songData['transcribedBy']) ? trim($songData['transcribedBy']) : $defaultTranscriber
        ];
    }

    private function parseTextContent(string $content, string $defaultName, string $defaultAuthor, string $defaultTranscriber)
    {
        $name = $defaultName;
        $author = $defaultAuthor;
        $transcribed_by = $defaultTranscriber;

        // Parse các pattern thông dụng
        if (preg_match('/name[:\s]+(.*)/i', $content, $matches)) {
            $name = trim($matches[1]);
        }

        if (preg_match('/author[:\s]+(.*)/i', $content, $matches)) {
            $author = trim($matches[1]);
        } elseif (preg_match('/composer[:\s]+(.*)/i', $content, $matches)) {
            $author = trim($matches[1]);
        }

        if (preg_match('/transcribed[:\s]+by[:\s]+(.*)/i', $content, $matches)) {
            $transcribed_by = trim($matches[1]);
        }

        return [
            'name' => $name,
            'author' => $author,
            'transcribed_by' => $transcribed_by
        ];
    }
}
