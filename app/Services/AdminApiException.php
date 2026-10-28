<?php

namespace App\Services;

use RuntimeException;

class AdminApiException extends RuntimeException
{
    public function __construct(public readonly string $reason, int $status = 503, ?\Throwable $previous = null)
    {
        parent::__construct($reason, $status, $previous);
    }

    /** Message shown to the worker, in the current UI language. */
    public function userMessage(): string
    {
        return __(match ($this->reason) {
            'invalid_credentials' => 'Passport number / name or birthday is incorrect',
            'ambiguous_name' => 'Several workers share this name, please log in with your passport number',
            'account_disabled' => 'This account is disabled, please contact the company',
            'throttled' => 'Too many attempts, please wait a minute',
            'not_configured', 'bad_key' => 'System not configured, please contact the administrator',
            default => 'Cannot reach the server, please try again',
        });
    }
}
