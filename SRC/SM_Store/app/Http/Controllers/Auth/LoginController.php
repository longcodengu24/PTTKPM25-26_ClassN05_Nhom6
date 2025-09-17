<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, FirebaseAuth $auth)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu tối thiểu 6 ký tự.',
        ]);

        try {
            $signIn = $auth->signInWithEmailAndPassword($request->email, $request->password);
            $uid    = $signIn->firebaseUserId();

            // Lấy record để đọc custom claims (role)
            $record = $auth->getUser($uid);
            $role   = $record->customClaims['role'] ?? 'user';

            // Lưu session
            session([
                'firebase_uid' => $uid,
                'email'        => $record->email,
                'name'         => $record->displayName,
                'role'         => $role,
            ]);

            // Điều hướng theo role
            return match ($role) {
                'admin'    => redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công (admin)!'),
                'business' => redirect()->route('business.dashboard')->with('success', 'Đăng nhập thành công (business)!'),
                default    => redirect()->route('home')->with('success', 'Đăng nhập thành công!'),
            };
        } catch (InvalidPassword $e) {
            return back()->withErrors(['password' => 'Sai mật khẩu.']);
        } catch (UserNotFound $e) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Đăng nhập thất bại: '.$e->getMessage()]);
        }
    }

    public function logout()
    {
        session()->forget(['firebase_uid','email','name','role']);
        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
    }
}
