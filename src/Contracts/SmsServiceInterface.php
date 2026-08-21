<?php

namespace Imrjat\SSExpert\Contracts;

use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;

interface SmsServiceInterface
{
    /**
     * Send single SMS message.
     */
    public function send(SmsData|array $smsData): SmsApiResponse;

    /**
     * Helper to send OTP SMS with a registered template.
     */
    public function sendOtp(string $mobile, string $otp, string $templateId = '1707167402281919826'): SmsApiResponse;
}
