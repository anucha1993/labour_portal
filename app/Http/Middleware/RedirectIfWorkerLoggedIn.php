<?php

namespace App\Http\Middleware;

use App\Services\WorkerSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfWorkerLoggedIn
{
    public function __construct(private readonly WorkerSession $worker)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->worker->loggedIn()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
