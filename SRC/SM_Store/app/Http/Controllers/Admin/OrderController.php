<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị danh sách đơn hàng cho admin
     */
    public function index()
    {
        try {
            // Lấy tất cả transactions từ Firestore
            $transactions = $this->firestoreService->listDocuments('transactions', 1000);
            $transactionsArray = [];
            
            if (isset($transactions['documents'])) {
                foreach ($transactions['documents'] as $doc) {
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
                    $transactionsArray[] = $data;
                }
            }

            // Sort by created_at descending
            usort($transactionsArray, function ($a, $b) {
                $timeA = strtotime($a['created_at'] ?? '1970-01-01T00:00:00Z');
                $timeB = strtotime($b['created_at'] ?? '1970-01-01T00:00:00Z');
                return $timeB - $timeA;
            });

            return view('admin.orders.orders', [
                'orders' => $transactionsArray
            ]);
        } catch (\Exception $e) {
            Log::error('Admin OrderController index error: ' . $e->getMessage());
            return view('admin.orders.orders', [
                'orders' => []
            ])->with('error', 'Có lỗi xảy ra khi tải danh sách đơn hàng');
        }
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        try {
            $transaction = $this->firestoreService->getDocument('transactions', $id);
            
            if (!$transaction) {
                return redirect()->route('admin.orders')->with('error', 'Không tìm thấy đơn hàng');
            }

            return view('admin.orders.show', [
                'order' => $transaction
            ]);
        } catch (\Exception $e) {
            Log::error('Admin OrderController show error: ' . $e->getMessage());
            return redirect()->route('admin.orders')->with('error', 'Có lỗi xảy ra');
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            $result = $this->firestoreService->updateDocument('transactions', $id, [
                'status' => $request->status,
                'updated_at' => now()->toISOString()
            ]);

            if ($result) {
                return redirect()->route('admin.orders')->with('success', 'Cập nhật trạng thái đơn hàng thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái');
            }
        } catch (\Exception $e) {
            Log::error('Admin OrderController updateStatus error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
