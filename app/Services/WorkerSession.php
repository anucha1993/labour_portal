<?php

namespace App\Services;

use Illuminate\Contracts\Session\Session;

/**
 * The logged-in worker lives in the PHP session only; this app has no user table.
 * The profile is cached for a short time so moving between pages does not hit the admin API every time.
 */
class WorkerSession
{
    public const KEY = 'worker';

    /** Seconds a cached profile is reused before asking the admin API again. */
    public const PROFILE_TTL = 120;

    public function __construct(private readonly Session $session, private readonly AdminApi $api)
    {
    }

    public function loggedIn(): bool
    {
        return (int) $this->session->get(self::KEY . '.id', 0) > 0;
    }

    public function id(): int
    {
        return (int) $this->session->get(self::KEY . '.id', 0);
    }

    public function login(int $labourId, array $profile): void
    {
        $this->session->regenerate();
        $this->session->put(self::KEY, [
            'id' => $labourId,
            'name' => $profile['full_name'] ?? '',
            'passport' => $profile['passport_number'] ?? '',
            'profile' => $profile,
            'fetched_at' => time(),
        ]);
    }

    public function logout(): void
    {
        $this->session->forget(self::KEY);
        $this->session->invalidate();
        $this->session->regenerateToken();
    }

    /**
     * Current profile (cached for PROFILE_TTL seconds, or refreshed on demand).
     * Returns null when the worker is gone / disabled on the admin side: the caller should log out.
     */
    public function profile(bool $fresh = false): ?array
    {
        if (! $this->loggedIn()) {
            return null;
        }
        $cached = $this->session->get(self::KEY . '.profile');
        $age = time() - (int) $this->session->get(self::KEY . '.fetched_at', 0);

        if (! $fresh && is_array($cached) && $cached && $age < self::PROFILE_TTL) {
            return $cached;
        }

        try {
            $profile = $this->api->profile($this->id());
        } catch (AdminApiException $e) {
            // Admin API temporarily unreachable: fall back to the cached copy rather than kicking the worker out.
            return is_array($cached) && $cached ? $cached : throw $e;
        }

        if ($profile === null) {
            return null;
        }

        $this->session->put(self::KEY . '.profile', $profile);
        $this->session->put(self::KEY . '.fetched_at', time());
        $this->session->put(self::KEY . '.name', $profile['full_name'] ?? '');

        return $profile;
    }
}
