<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // 1) Validate đầu vào
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
        ]);

        try {
            
            app('firebase.auth')->getUserByEmail($request->email);

           
            $response = Http::post(
                'https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key=' . env('FIREBASE_WEB_API_KEY'),
                [
                    'requestType' => 'PASSWORD_RESET',
                    'email'       => $request->email,
                ]
            );

            if ($response->successful()) {
                return back()->with('success', 'Liên kết đặt lại mật khẩu đã được gửi đến email của bạn.');
            }

            
            $error = $response->json('error.message') ?? 'Không xác định';
            return back()->withErrors(['error' => 'Không thể gửi liên kết: ' . $error]);

        } catch (UserNotFound $e) {
            
            return back()->withErrors(['email' => 'Email chưa được đăng ký trong hệ thống.']);
        } catch (\Throwable $e) {
            
            return back()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }
}
