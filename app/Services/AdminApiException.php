<?php

namespace App\Services;

use RuntimeException;

class AdminApiException extends RuntimeException
{
    public function __construct(public readonly string $reason, int $status = 503, ?\Throwable $previous = null)
    {
        parent::__construct($reason, $status, $previous);
    }

    /** Bilingual message shown to the worker. */
    public function userMessage(): string
    {
        return match ($this->reason) {
            'invalid_credentials' => 'เลขที่หนังสือเดินทางหรือวันเกิดไม่ถูกต้อง / Passport number or birthday is incorrect',
            'account_disabled' => 'บัญชีนี้ถูกปิดการใช้งาน กรุณาติดต่อบริษัท / This account is disabled, please contact the company',
            'throttled' => 'ลองเข้าสู่ระบบบ่อยเกินไป กรุณารอสักครู่ / Too many attempts, please wait a minute',
            'not_configured', 'bad_key' => 'ระบบยังตั้งค่าไม่สมบูรณ์ กรุณาติดต่อผู้ดูแล / System not configured, please contact the administrator',
            default => 'ไม่สามารถเชื่อมต่อระบบได้ กรุณาลองใหม่ / Cannot reach the server, please try again',
        };
    }
}
