<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị trang cài đặt admin
     */
    public function index()
    {
        try {
            // Lấy settings từ Firestore
            $settings = $this->firestoreService->getDocument('settings', 'app_settings');
            
            return view('admin.settings.settings', [
                'settings' => $settings ?: []
            ]);
        } catch (\Exception $e) {
            Log::error('Admin SettingsController index error: ' . $e->getMessage());
            return view('admin.settings.settings', [
                'settings' => []
            ])->with('error', 'Có lỗi xảy ra khi tải cài đặt');
        }
    }

    /**
     * Cập nhật cài đặt
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'site_name' => 'required|string|max:255',
                'site_description' => 'nullable|string',
                'maintenance_mode' => 'boolean',
                'registration_enabled' => 'boolean',
                'max_file_size' => 'required|integer|min:1',
                'allowed_file_types' => 'required|string'
            ]);

            $settingsData = [
                'site_name' => $request->site_name,
                'site_description' => $request->site_description,
                'maintenance_mode' => $request->boolean('maintenance_mode'),
                'registration_enabled' => $request->boolean('registration_enabled'),
                'max_file_size' => $request->max_file_size,
                'allowed_file_types' => $request->allowed_file_types,
                'updated_at' => now()->toISOString(),
                'updated_by' => auth()->user()->uid ?? 'admin'
            ];

            // Kiểm tra xem settings đã tồn tại chưa
            $existingSettings = $this->firestoreService->getDocument('settings', 'app_settings');
            
            if ($existingSettings) {
                $result = $this->firestoreService->updateDocument('settings', 'app_settings', $settingsData);
            } else {
                $result = $this->firestoreService->createDocumentWithId('settings', 'app_settings', $settingsData);
            }

            if ($result) {
                return redirect()->route('admin.settings')->with('success', 'Cập nhật cài đặt thành công');
            } else {
                return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật cài đặt');
            }
        } catch (\Exception $e) {
            Log::error('Admin SettingsController update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
