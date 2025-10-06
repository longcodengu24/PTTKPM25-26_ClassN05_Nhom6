<?php

namespace App\Http\Controllers\Saler;

use App\Http\Controllers\Controller;
use App\Services\FirestoreSimple;
use Illuminate\Http\Request;

class SalerController extends Controller
{
    // Hiển thị danh sách sheet nhạc
    public function index(FirestoreSimple $fs)
    {
        $resp = $fs->listDocuments('sheets', 100);
        $rows = [];

        foreach (($resp['documents'] ?? []) as $doc) {
            $id = basename($doc['name']);
            $f  = $doc['fields'] ?? [];

            $rows[] = [
                'id'                => $id,
                'title'             => $f['title']['stringValue'] ?? '---',
                'composer'          => $f['composer']['stringValue'] ?? '---',
                'genre'             => $f['genre']['stringValue'] ?? '---',
                'difficulty'        => $f['difficulty']['stringValue'] ?? '---',
                'price'             => isset($f['price']['integerValue']) ? (int)$f['price']['integerValue'] : 0,
                'status'            => $f['status']['stringValue'] ?? '---',
                'preview_image_url' => $f['preview_image_url']['stringValue'] ?? '',
            ];
        }

        return view('saler.products.index', ['sheets' => $rows]);
    }

    // Hiển thị form thêm mới
    public function create()
    {
        return view('saler.products.create');
    }

    // Xử lý lưu vào Firestore
    public function store(Request $req, FirestoreSimple $fs)
    {
        $data = $req->validate([
            'title'          => 'required|string|max:150',
            'composer'       => 'required|string|max:150',
            'genre'          => 'required|string|max:50',
            'difficulty'     => 'required|string|max:50',
            'description'    => 'nullable|string',
            'price'          => 'required|integer|min:0',
            'allow_discount' => 'sometimes|accepted',
            'discount_price' => 'nullable|integer|min:0',
            'status'         => 'required|in:draft,published,scheduled',
            'featured'       => 'sometimes|accepted',
            'allow_comments' => 'sometimes|accepted',
        ]);

        // Nếu không bật giảm giá thì set = 0
        if (empty($data['allow_discount'])) {
            $data['discount_price'] = 0;
        }

        $payload = [
            'title'          => $data['title'],
            'composer'       => $data['composer'],
            'genre'          => $data['genre'],
            'difficulty'     => $data['difficulty'],
            'description'    => $data['description'] ?? '',
            'price'          => (int) $data['price'],
            'allow_discount' => isset($data['allow_discount']),
            'discount_price' => (int) ($data['discount_price'] ?? 0),
            'status'         => $data['status'],
            'featured'       => isset($data['featured']),
            'allow_comments' => isset($data['allow_comments']),
            'preview_image_url' => '',
            'sheet_file_url'    => '',
            'owner_uid'    => session('firebase_uid'),
            'created_at'   => now()->toAtomString(),
            'updated_at'   => now()->toAtomString(),
        ];

        $resp = $fs->createDocument('sheets', $payload);
        $id = isset($resp['name']) ? basename($resp['name']) : null;

        return redirect()->route('saler.products')
            ->with('success', 'Đã thêm sheet nhạc ' . ($id ? "(#{$id})" : 'thành công!'));
    }

    // Hiển thị form sửa
    public function edit($id, FirestoreSimple $fs)
    {
        $resp = $fs->listDocuments('sheets', 100);
        
        // Tìm document theo ID
        foreach (($resp['documents'] ?? []) as $doc) {
            $docId = basename($doc['name']);
            if ($docId === $id) {
                $f = $doc['fields'] ?? [];
                $sheet = [
                    'id'                => $docId,
                    'title'             => $f['title']['stringValue'] ?? '',
                    'composer'          => $f['composer']['stringValue'] ?? '',
                    'genre'             => $f['genre']['stringValue'] ?? '',
                    'difficulty'        => $f['difficulty']['stringValue'] ?? '',
                    'description'       => $f['description']['stringValue'] ?? '',
                    'price'             => isset($f['price']['integerValue']) ? (int)$f['price']['integerValue'] : 0,
                    'allow_discount'    => isset($f['allow_discount']['booleanValue']) ? $f['allow_discount']['booleanValue'] : false,
                    'discount_price'    => isset($f['discount_price']['integerValue']) ? (int)$f['discount_price']['integerValue'] : 0,
                    'status'            => $f['status']['stringValue'] ?? 'draft',
                    'featured'          => isset($f['featured']['booleanValue']) ? $f['featured']['booleanValue'] : false,
                    'allow_comments'    => isset($f['allow_comments']['booleanValue']) ? $f['allow_comments']['booleanValue'] : false,
                    'preview_image_url' => $f['preview_image_url']['stringValue'] ?? '',
                ];
                
                return view('saler.products.edit', ['sheet' => $sheet]);
            }
        }
        
        return redirect()->route('saler.products')->with('error', 'Không tìm thấy sheet nhạc');
    }

    // Cập nhật sheet nhạc
    public function update($id, Request $request)
    {
        // Tạm thời chỉ redirect với thông báo
        // Sẽ implement update functionality sau
        return redirect()->route('saler.products')
            ->with('success', "Đã cập nhật sheet #{$id} (tạm thời)");
    }

    // Xóa sheet nhạc
    public function destroy($id)
    {
        // Tạm thời chỉ redirect với thông báo
        // Sẽ implement delete functionality sau
        return redirect()->route('saler.products')
            ->with('success', "Đã xóa sheet #{$id} (tạm thời)");
    }
}
