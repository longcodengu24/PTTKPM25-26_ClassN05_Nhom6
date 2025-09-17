<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    // Hiển thị chi tiết bài viết cộng đồng
    public function show($id)
    {
        // Bạn có thể thay thế logic này bằng truy vấn model thực tế
        // Ví dụ: $post = Post::findOrFail($id);
        return view('page.community.post-detail', ['id' => $id]);
    }
}
