<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Load user data from Firebase and update session
     */
    private function loadUserData()
    {
        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                return null;
            }

            // Lấy thông tin user từ Firebase
            $user = $this->auth->getUser($uid);

            // Cập nhật session với thông tin mới nhất từ Firebase
            $userData = [
                'name' => $user->displayName ?? session('name', ''),
                'email' => $user->email ?? session('email', ''),
                'avatar' => $user->photoUrl ?? '/img/default-avatar.png'
            ];

            // Cập nhật session
            session($userData);

            return $userData;
        } catch (\Exception $e) {
            Log::error('Error loading user data: ' . $e->getMessage());
            return null;
        }
    }

    public function settings()
    {
        $userData = $this->loadUserData();

        if (!$userData) {
            return redirect()->route('auth.login')->withErrors(['error' => 'Vui lòng đăng nhập.']);
        }

        Log::info('User data loaded for settings', [
            'uid' => session('firebase_uid'),
            'name' => $userData['name'],
            'avatar' => $userData['avatar']
        ]);

        return view('account.settings', compact('userData'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên đăng nhập.',
            'name.max' => 'Tên đăng nhập không được quá 100 ký tự.',
            'avatar.image' => 'File phải là hình ảnh.',
            'avatar.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'avatar.max' => 'Kích thước file không được vượt quá 2MB.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        try {
            $uid = session('firebase_uid');
            if (!$uid) {
                return back()->withErrors(['error' => 'Phiên đăng nhập không hợp lệ.']);
            }

            // Cập nhật display name
            $this->auth->updateUser($uid, [
                'displayName' => $request->name,
            ]);

            // Cập nhật session
            session(['name' => $request->name]);

            // Xử lý upload avatar
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                try {
                    $avatar = $request->file('avatar');

                    Log::info('Avatar upload attempt', [
                        'original_name' => $avatar->getClientOriginalName(),
                        'size' => $avatar->getSize(),
                        'mime_type' => $avatar->getMimeType(),
                        'uid' => $uid
                    ]);

                    // Tạo thư mục nếu chưa có
                    $uploadDir = public_path('img/avatars');
                    if (!file_exists($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            throw new \Exception('Không thể tạo thư mục upload: ' . $uploadDir);
                        }
                        Log::info('Created upload directory', ['path' => $uploadDir]);
                    }

                    // Xóa avatar cũ nếu có
                    $oldAvatar = session('avatar');
                    if ($oldAvatar && strpos($oldAvatar, 'avatars/') !== false) {
                        $oldFileName = basename($oldAvatar);
                        $oldFile = $uploadDir . DIRECTORY_SEPARATOR . $oldFileName;
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                            Log::info('Deleted old avatar', ['path' => $oldFile]);
                        }
                    }

                    // Tạo tên file unique
                    $fileName = 'avatar_' . $uid . '_' . time() . '.' . $avatar->getClientOriginalExtension();
                    $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

                    // Move file to public/img/avatars
                    if (!$avatar->move($uploadDir, $fileName)) {
                        throw new \Exception('Không thể di chuyển file đến: ' . $fullPath);
                    }

                    // Kiểm tra file đã được tạo
                    if (!file_exists($fullPath)) {
                        throw new \Exception('File không được tạo thành công: ' . $fullPath);
                    }

                    // Tạo URL cho avatar
                    $photoUrl = asset('img/avatars/' . $fileName);

                    // Cập nhật photo URL trong Firebase
                    $this->auth->updateUser($uid, [
                        'photoUrl' => $photoUrl,
                    ]);

                    // Cập nhật session
                    session(['avatar' => $photoUrl]);

                    Log::info('Avatar updated successfully', [
                        'uid' => $uid,
                        'photoUrl' => $photoUrl,
                        'file_path' => $fullPath,
                        'file_size' => filesize($fullPath)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Avatar upload error: ' . $e->getMessage(), [
                        'uid' => $uid,
                        'request_data' => $request->all()
                    ]);
                    return back()->withErrors(['avatar' => 'Lỗi upload ảnh: ' . $e->getMessage()]);
                }
            }            // Cập nhật mật khẩu nếu được cung cấp
            if ($request->filled('password')) {
                $this->auth->updateUser($uid, [
                    'password' => $request->password,
                ]);
            }

            return back()->with('success', 'Cập nhật thông tin thành công!');
        } catch (\Exception $e) {
            Log::error('Account update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        $userData = $this->loadUserData();
        return view('account.index', compact('userData'));
    }

    public function sheets()
    {
        $userData = $this->loadUserData();
        return view('account.sheets', compact('userData'));
    }

    public function activity()
    {
        $userData = $this->loadUserData();
        return view('account.activity', compact('userData'));
    }

    public function deposit()
    {
        $userData = $this->loadUserData();
        return view('account.deposit', compact('userData'));
    }

    public function withdraw()
    {
        $userData = $this->loadUserData();
        return view('account.withdraw', compact('userData'));
    }
}
