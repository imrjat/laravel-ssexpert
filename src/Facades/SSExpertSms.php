<?php

namespace Imrjat\SSExpert\Facades;

use Illuminate\Support\Facades\Facade;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;

/**
 * @method static SmsApiResponse send(SmsData|array $smsData)
 * @method static SmsApiResponse sendOtp(string $mobile, string $otp, string $templateId = '1707167402281919826')
 *
 * @see \Imrjat\SSExpert\Services\SSExpertSmsService
 */
class SSExpertSms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsServiceInterface::class;
    }
}
