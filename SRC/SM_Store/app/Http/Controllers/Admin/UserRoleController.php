<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class UserRoleController extends Controller
{
    public function __construct(private Auth $auth) {}

    public function index()
    {
        $users = [];
        foreach ($this->auth->listUsers() as $user) {
            if (!$user->email) continue;
            $users[] = [
                'uid'   => $user->uid,
                'email' => $user->email,
                'role'  => $user->customClaims['role'] ?? 'user',
            ];
        }
        usort($users, fn($a, $b) => strcmp($a['email'], $b['email']));

        // ✅ Trả về view trong admin layout
        return view('admin.roles.index', compact('users'));
    }

    public function updateRole(Request $request, string $uid)
    {
        $request->validate([
            'role' => 'required|in:admin,business,user',
        ]);

        try {
            $user = $this->auth->getUser($uid);
            $this->auth->setCustomUserClaims($uid, ['role' => $request->role]);

            if (session('firebase_uid') === $uid) {
                session(['role' => $request->role]);
            }

            // ✅ Sau khi cập nhật, quay lại trang phân quyền trong admin
            return redirect()->route('admin.roles.index')
                ->with('success', "Đã cập nhật quyền cho {$user->email} thành '{$request->role}'.");
        } catch (UserNotFound $e) {
            return back()->withErrors(['error' => 'Không tìm thấy tài khoản trên Firebase.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Lỗi khi cập nhật quyền: '.$e->getMessage()]);
        }
    }
}
