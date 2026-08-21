<?php

namespace Imrjat\SSExpert\Facades;

use Illuminate\Support\Facades\Facade;
use Imrjat\SSExpert\Contracts\BalanceServiceInterface;
use Imrjat\SSExpert\Contracts\GroupServiceInterface;
use Imrjat\SSExpert\Contracts\SenderIdServiceInterface;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\TemplateServiceInterface;
use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;

/**
 * Single Unified SSExpertSystem Gateway Entrypoint.
 *
 * @method static SmsServiceInterface sms()
 * @method static TemplateServiceInterface template()
 * @method static TemplateServiceInterface templates()
 * @method static BalanceServiceInterface balance()
 * @method static SenderIdServiceInterface senderId()
 * @method static SenderIdServiceInterface senderIds()
 * @method static GroupServiceInterface group()
 * @method static GroupServiceInterface groups()
 * @method static SmsApiResponse sendOtp(string $mobile, string $otp, ?string $templateId = null)
 * @method static SmsApiResponse send(SmsData|array $smsData)
 * @method static float getCredits()
 *
 * @see \Imrjat\SSExpert\SSExpertManager
 */
class SSExpert extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ssexpert';
    }
}
