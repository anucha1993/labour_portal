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
            'passport.required' => __('Please enter your passport number or name'),
            'password.required' => __('Please enter your birthday'),
        ]);

        $passport = strtoupper(preg_replace('/\s+/', '', $data['passport']));
        $throttleKey = 'login|' . $passport . '|' . $request->ip();

        if ($this->limiter->tooManyAttempts($throttleKey, 8)) {
            return back()->withInput($request->only('passport'))
                ->withErrors(['passport' => __('Too many attempts, please wait a minute') . ' (' . $this->limiter->availableIn($throttleKey) . 's)']);
        }

        try {
            $result = $this->api->login($passport, $data['password'], $request->ip());
        } catch (AdminApiException $e) {
            if (in_array($e->reason, ['invalid_credentials', 'account_disabled', 'ambiguous_name'], true)) {
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

        return redirect()->route('login')->with('success', __('Logged out'));
    }
}
