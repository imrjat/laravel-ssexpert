<?php

namespace Imrjat\SSExpert;

use Imrjat\SSExpert\Contracts\BalanceServiceInterface;
use Imrjat\SSExpert\Contracts\GroupServiceInterface;
use Imrjat\SSExpert\Contracts\SenderIdServiceInterface;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\TemplateServiceInterface;
use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;

class SSExpertManager
{
    public function __construct(
        protected SmsServiceInterface $smsService,
        protected TemplateServiceInterface $templateService,
        protected BalanceServiceInterface $balanceService,
        protected SenderIdServiceInterface $senderIdService,
        protected GroupServiceInterface $groupService,
    ) {}

    /**
     * Access SMS & OTP service.
     */
    public function sms(): SmsServiceInterface
    {
        return $this->smsService;
    }

    /**
     * Access DLT Template service.
     */
    public function template(): TemplateServiceInterface
    {
        return $this->templateService;
    }

    /**
     * Alias for template().
     */
    public function templates(): TemplateServiceInterface
    {
        return $this->templateService;
    }

    /**
     * Access Balance & Credits service.
     */
    public function balance(): BalanceServiceInterface
    {
        return $this->balanceService;
    }

    /**
     * Access Sender ID (Header) service.
     */
    public function senderId(): SenderIdServiceInterface
    {
        return $this->senderIdService;
    }

    /**
     * Alias for senderId().
     */
    public function senderIds(): SenderIdServiceInterface
    {
        return $this->senderIdService;
    }

    /**
     * Access Contact Group service.
     */
    public function group(): GroupServiceInterface
    {
        return $this->groupService;
    }

    /**
     * Alias for group().
     */
    public function groups(): GroupServiceInterface
    {
        return $this->groupService;
    }

    /**
     * Quick shortcut: Send an OTP SMS.
     */
    public function sendOtp(string $mobile, string $otp, ?string $templateId = null): SmsApiResponse
    {
        return $this->smsService->sendOtp($mobile, $otp, $templateId);
    }

    /**
     * Quick shortcut: Send a custom SMS.
     */
    public function send(SmsData|array $smsData): SmsApiResponse
    {
        return $this->smsService->send($smsData);
    }

    /**
     * Quick shortcut: Get available SMS credits.
     */
    public function getCredits(): float
    {
        return $this->balanceService->getCredits();
    }
}
