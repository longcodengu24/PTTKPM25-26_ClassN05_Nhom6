<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị danh sách bài viết cho admin
     */
    public function index()
    {
        try {
            // Lấy tất cả posts từ Firestore
            $posts = $this->firestoreService->listDocuments('posts', 1000);
            $postsArray = [];
            
            if (isset($posts['documents'])) {
                foreach ($posts['documents'] as $doc) {
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
                    $postsArray[] = $data;
                }
            }

            // Sort by created_at descending
            usort($postsArray, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');
                return $timeB - $timeA;
            });

            return view('admin.posts.posts', [
                'posts' => $postsArray
            ]);
        } catch (\Exception $e) {
            Log::error('Admin PostController index error: ' . $e->getMessage());
            return view('admin.posts.posts', [
                'posts' => []
            ])->with('error', 'Có lỗi xảy ra khi tải danh sách bài viết');
        }
    }

    /**
     * Hiển thị form tạo bài viết mới
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Lưu bài viết mới
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'status' => 'required|in:draft,published'
            ]);

            $postData = [
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->status,
                'author_id' => auth()->user()->uid ?? 'admin',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];

            $result = $this->firestoreService->createDocument('posts', $postData);

            if ($result) {
                return redirect()->route('admin.posts')->with('success', 'Tạo bài viết thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo bài viết');
            }
        } catch (\Exception $e) {
            Log::error('Admin PostController store error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Hiển thị form chỉnh sửa bài viết
     */
    public function edit($id)
    {
        try {
            $post = $this->firestoreService->getDocument('posts', $id);
            
            if (!$post) {
                return redirect()->route('admin.posts')->with('error', 'Không tìm thấy bài viết');
            }

            return view('admin.posts.edit', [
                'post' => $post
            ]);
        } catch (\Exception $e) {
            Log::error('Admin PostController edit error: ' . $e->getMessage());
            return redirect()->route('admin.posts')->with('error', 'Có lỗi xảy ra');
        }
    }

    /**
     * Cập nhật bài viết
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'status' => 'required|in:draft,published'
            ]);

            $postData = [
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->status,
                'updated_at' => now()->toISOString()
            ];

            $result = $this->firestoreService->updateDocument('posts', $id, $postData);

            if ($result) {
                return redirect()->route('admin.posts')->with('success', 'Cập nhật bài viết thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật bài viết');
            }
        } catch (\Exception $e) {
            Log::error('Admin PostController update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        try {
            $result = $this->firestoreService->updateDocument('posts', $id, [
                'status' => 'deleted',
                'deleted_at' => now()->toISOString()
            ]);

            if ($result) {
                return redirect()->route('admin.posts')->with('success', 'Xóa bài viết thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa bài viết');
            }
        } catch (\Exception $e) {
            Log::error('Admin PostController destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
