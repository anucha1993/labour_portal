<?php

namespace App\Http\Middleware;

use App\Services\WorkerSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkerLoggedIn
{
    public function __construct(private readonly WorkerSession $worker)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->worker->loggedIn()) {
            return redirect()->route('login')->with('info', 'กรุณาเข้าสู่ระบบ / Please log in');
        }

        return $next($request);
    }
}
