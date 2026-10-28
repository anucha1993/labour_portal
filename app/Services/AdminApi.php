<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the admin system's worker API (admin.importer168.com /api/portal/*).
 * The shared secret never reaches the browser: every call is server-to-server.
 */
class AdminApi
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $key,
        private readonly int $timeout = 20,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            rtrim((string) config('services.admin.url'), '/'),
            (string) config('services.admin.key'),
            (int) config('services.admin.timeout', 20),
        );
    }

    public function configured(): bool
    {
        return $this->baseUrl !== '' && $this->key !== '';
    }

    /**
     * @return array{labour_id:int, profile:array}
     *
     * @throws AdminApiException invalid_credentials | account_disabled | throttled | unavailable
     */
    public function login(string $passport, string $password, ?string $clientIp = null): array
    {
        $res = $this->send(fn (PendingRequest $http) => $http
            ->withHeaders(array_filter(['X-Client-Ip' => $clientIp]))
            ->post('/api/portal/login', ['passport' => $passport, 'password' => $password]));

        if ($res->status() === 422) {
            $code = (string) ($res->json('errors.passport.0') ?? 'invalid_credentials');
            throw new AdminApiException(in_array($code, ['invalid_credentials', 'account_disabled', 'ambiguous_name'], true) ? $code : 'invalid_credentials', 422);
        }
        if ($res->status() === 429) {
            throw new AdminApiException('throttled', 429);
        }
        $this->guard($res);

        return ['labour_id' => (int) $res->json('labour_id'), 'profile' => (array) $res->json('profile')];
    }

    /** Full profile, or null when the account no longer exists / was disabled (the portal then logs the worker out). */
    public function profile(int $labourId): ?array
    {
        $res = $this->send(fn (PendingRequest $http) => $http->get("/api/portal/labours/{$labourId}"));
        if (in_array($res->status(), [403, 404], true)) {
            return null;
        }
        $this->guard($res);

        return (array) $res->json('profile');
    }

    /** @return array{url:string, name:string, file_name:string, expires_at:string}|null */
    public function documentLink(int $labourId, string $key): ?array
    {
        $res = $this->send(fn (PendingRequest $http) => $http->get("/api/portal/labours/{$labourId}/documents/" . rawurlencode($key) . '/link'));
        if (in_array($res->status(), [403, 404], true)) {
            return null;
        }
        $this->guard($res);

        return $res->json();
    }

    // ------------------------------------------------------------------

    private function send(callable $call): Response
    {
        if (! $this->configured()) {
            throw new AdminApiException('not_configured', 503);
        }
        try {
            return $call(Http::baseUrl($this->baseUrl)
                ->acceptJson()
                ->asJson()
                ->withHeaders(['X-Portal-Key' => $this->key])
                ->timeout($this->timeout)
                // Retry only network failures and 5xx: a 422 (wrong password) must not be sent twice.
                ->retry(2, 300, fn ($e) => $e instanceof ConnectionException || ($e instanceof RequestException && $e->response->serverError()), throw: false));
        } catch (ConnectionException $e) {
            report($e);
            throw new AdminApiException('unavailable', 503, $e);
        }
    }

    private function guard(Response $res): void
    {
        if ($res->successful()) {
            return;
        }
        logger()->error('Admin API error', ['status' => $res->status(), 'body' => mb_substr($res->body(), 0, 500)]);
        throw new AdminApiException($res->status() === 401 ? 'bad_key' : 'unavailable', 503);
    }
}
