<?php

namespace App\Http\Controllers;

use App\Services\AdminApi;
use App\Services\AdminApiException;
use App\Services\WorkerSession;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AdminApi $api,
        private readonly WorkerSession $worker,
        private readonly RateLimiter $limiter,
    ) {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'passport' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:20'],
        ], [
            'passport.required' => 'กรุณากรอกเลขที่หนังสือเดินทาง / Please enter your passport number',
            'password.required' => 'กรุณากรอกวันเกิด / Please enter your birthday',
        ]);

        $passport = strtoupper(preg_replace('/\s+/', '', $data['passport']));
        $throttleKey = 'login|' . $passport . '|' . $request->ip();

        if ($this->limiter->tooManyAttempts($throttleKey, 8)) {
            return back()->withInput($request->only('passport'))
                ->withErrors(['passport' => 'ลองเข้าสู่ระบบบ่อยเกินไป กรุณารอ ' . $this->limiter->availableIn($throttleKey) . ' วินาที / Too many attempts, please wait']);
        }

        try {
            $result = $this->api->login($passport, $data['password'], $request->ip());
        } catch (AdminApiException $e) {
            if (in_array($e->reason, ['invalid_credentials', 'account_disabled'], true)) {
                $this->limiter->hit($throttleKey, 60);
            }

            return back()->withInput($request->only('passport'))->withErrors(['passport' => $e->userMessage()]);
        }

        $this->limiter->clear($throttleKey);
        $this->worker->login($result['labour_id'], $result['profile']);

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request)
    {
        $this->worker->logout();

        return redirect()->route('login')->with('success', 'ออกจากระบบแล้ว / Logged out');
    }
}
