<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Fills lang/<locale>.json from lang/en.json using the Google Cloud Translation API (v2).
 *
 *   php artisan lang:translate            # translate only the strings still missing / identical to English
 *   php artisan lang:translate --force    # re-translate everything
 *   php artisan lang:translate --locale=my
 */
class TranslateLang extends Command
{
    protected $signature = 'lang:translate {--locale=* : Target locale(s), default: every locale in config app.locales except en} {--force : Overwrite existing translations}';

    protected $description = 'Translate lang/en.json into the other portal languages with Google Translate';

    private const ENDPOINT = 'https://translation.googleapis.com/language/translate/v2';

    public function handle(): int
    {
        $key = (string) config('services.google.translate_key');
        if ($key === '') {
            $this->error('GOOGLE_TRANSLATE_API_KEY is not set in .env');

            return self::FAILURE;
        }

        $source = json_decode((string) file_get_contents(lang_path('en.json')), true) ?: [];
        if (! $source) {
            $this->error('lang/en.json is empty');

            return self::FAILURE;
        }

        $locales = $this->option('locale') ?: array_values(array_diff(array_keys(portal_locales()), ['en']));

        foreach ($locales as $locale) {
            $path = lang_path($locale . '.json');
            $existing = is_file($path) ? (json_decode((string) file_get_contents($path), true) ?: []) : [];

            $todo = [];
            foreach ($source as $k => $en) {
                $has = isset($existing[$k]) && $existing[$k] !== '' && $existing[$k] !== $k;
                if ($this->option('force') || ! $has) {
                    $todo[] = $k;
                }
            }

            if (! $todo) {
                $this->info("[$locale] nothing to translate");
                continue;
            }

            $this->line("[$locale] translating " . count($todo) . ' strings…');
            $translated = $this->translate($todo, $locale, $key);
            if ($translated === null) {
                return self::FAILURE;
            }

            $out = [];
            foreach ($source as $k => $en) {                         // keep en.json order
                $out[$k] = $translated[$k] ?? ($existing[$k] ?? $en);
            }
            file_put_contents($path, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
            $this->info("[$locale] wrote " . count($translated) . " translations to lang/$locale.json");
        }

        return self::SUCCESS;
    }

    /** @return array<string,string>|null key => translated text */
    private function translate(array $texts, string $target, string $key): ?array
    {
        $result = [];
        foreach (array_chunk($texts, 100) as $chunk) {
            $res = Http::asJson()->timeout(60)->post(self::ENDPOINT . '?key=' . urlencode($key), [
                'q' => $chunk,
                'source' => 'en',
                'target' => $target,
                'format' => 'text',
            ]);

            if (! $res->successful()) {
                $this->error('Google Translate error ' . $res->status() . ': ' . ($res->json('error.message') ?? mb_substr($res->body(), 0, 300)));

                return null;
            }

            $rows = $res->json('data.translations') ?? [];
            foreach ($chunk as $i => $text) {
                $t = trim((string) ($rows[$i]['translatedText'] ?? ''));
                if ($t !== '') {
                    $result[$text] = $t;
                }
            }
        }

        return $result;
    }
}
