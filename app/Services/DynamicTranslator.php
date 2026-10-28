<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Translates free-text values coming from the admin API (e.g. the employer's business type)
 * into the worker's language at display time. Each text is sent to Google once and cached
 * for a year; when Google is unavailable the English text is shown unchanged.
 */
class DynamicTranslator
{
    private const ENDPOINT = 'https://translation.googleapis.com/language/translate/v2';

    public function to(string $text, ?string $locale = null): string
    {
        $text = trim($text);
        $locale = $locale ?: app()->getLocale();
        $key = (string) config('services.google.translate_key');
        if ($text === '' || $locale === 'en' || $key === '') {
            return $text;
        }

        $cacheKey = 'dyn-translate:' . $locale . ':' . md5($text);
        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        try {
            $res = Http::asJson()->timeout(8)->post(self::ENDPOINT . '?key=' . urlencode($key), [
                'q' => [$text],
                'source' => 'en',
                'target' => $locale,
                'format' => 'text',
            ]);
            $out = trim((string) ($res->json('data.translations.0.translatedText') ?? ''));
            if ($res->successful() && $out !== '') {
                Cache::put($cacheKey, $out, now()->addYear());

                return $out;
            }
            Log::warning('Dynamic translate failed', ['status' => $res->status(), 'locale' => $locale]);
        } catch (\Throwable $e) {
            Log::warning('Dynamic translate error', ['error' => $e->getMessage()]);
        }

        return $text;
    }
}
