<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\EmailExists;

class RegisterController extends Controller
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        try {
            $this->auth->createUser([
                'email' => $request->email,
                'password' => $request->password,
                'displayName' => $request->name,
            ]);

            return redirect()->route('login')->with('success', 'Đăng ký thành công! Mời bạn đăng nhập.');
        } catch (EmailExists $e) {
            return back()->withErrors(['email' => 'Email đã được đăng ký.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Lỗi đăng ký: ' . $e->getMessage()]);
        }
    }
}
