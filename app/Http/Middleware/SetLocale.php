<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Picks the UI language: ?lang=xx (remembered in session + a 1-year cookie),
 * else the cookie, else the browser's Accept-Language, else English.
 */
class SetLocale
{
    public const COOKIE = 'portal_lang';

    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(portal_locales());
        $chosen = null;

        if ($request->filled('lang') && in_array($request->query('lang'), $supported, true)) {
            $chosen = $request->query('lang');
            $request->session()->put('lang', $chosen);
            Cookie::queue(Cookie::make(self::COOKIE, $chosen, 60 * 24 * 365, null, null, null, false));
        }

        $locale = $chosen
            ?: $request->session()->get('lang')
            ?: $request->cookie(self::COOKIE)
            ?: $request->getPreferredLanguage($supported)
            ?: config('app.fallback_locale', 'en');

        if (! in_array($locale, $supported, true)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
