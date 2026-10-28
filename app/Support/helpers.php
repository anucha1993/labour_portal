<?php

use Carbon\Carbon;

if (! function_exists('t_en')) {
    /**
     * Secondary (English) label shown under a translated label, so a worker reading Burmese
     * still sees the English term staff use. Empty when the page is already in English.
     */
    function t_en(string $key): string
    {
        return app()->getLocale() === 'en' ? '' : $key;
    }
}

if (! function_exists('portal_locales')) {
    /** @return array<string,string> code => native name */
    function portal_locales(): array
    {
        return (array) config('app.locales', ['en' => 'English']);
    }
}

if (! function_exists('age_parts')) {
    /** Years and months since an ISO date, or null when unknown. @return array{0:int,1:int}|null */
    function age_parts(?string $iso): ?array
    {
        if (! $iso) {
            return null;
        }
        try {
            $d = Carbon::parse($iso)->diff(Carbon::today());

            return [$d->y, $d->m];
        } catch (\Throwable $e) {
            return null;
        }
    }
}
