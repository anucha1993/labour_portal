<?php

namespace App\Http\Controllers;

use App\Services\AdminApi;
use App\Services\AdminApiException;
use App\Services\WorkerSession;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function __construct(private readonly AdminApi $api, private readonly WorkerSession $worker)
    {
    }

    /** Home: identity, employer, status, passport / work permit / visa / 90-day cards, address and documents. */
    public function home(Request $request)
    {
        return $this->render('home', $request);
    }

    public function documents(Request $request)
    {
        return $this->render('documents', $request);
    }

    public function profile(Request $request)
    {
        return $this->render('profile', $request);
    }

    /** Ask the admin API for a fresh signed link and send the browser there (opens inline on phones). */
    public function openDocument(string $key)
    {
        try {
            $link = $this->api->documentLink($this->worker->id(), $key);
        } catch (AdminApiException $e) {
            return redirect()->route('documents')->with('error', $e->userMessage());
        }

        if (! $link || empty($link['url'])) {
            return redirect()->route('documents')->with('error', 'ไม่พบเอกสาร / Document not found');
        }

        return redirect()->away($link['url']);
    }

    private function render(string $view, Request $request)
    {
        try {
            $profile = $this->worker->profile(fresh: $request->boolean('refresh'));
        } catch (AdminApiException $e) {
            return response()->view('unavailable', ['message' => $e->userMessage()], 503);
        }

        if ($profile === null) {
            $this->worker->logout();

            return redirect()->route('login')->with('error', 'บัญชีของคุณถูกปิดการใช้งาน กรุณาติดต่อบริษัท / Your account is disabled, please contact the company');
        }

        return view($view, ['p' => $profile]);
    }
}
