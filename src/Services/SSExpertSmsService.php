<?php

namespace Imrjat\SSExpert\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;
use Imrjat\SSExpert\Exceptions\SSExpertApiException;
use Imrjat\SSExpert\Exceptions\SSExpertAuthException;

class SSExpertSmsService implements SmsServiceInterface
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $clientId;
    protected ?string $defaultSenderId;
    protected ?string $defaultPeid;
    protected int $timeout;
    protected int $retryTimes;
    protected int $retrySleep;

    public function __construct(?array $config = null)
    {
        $config = $config ?? config('ssexpert', []);

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? 'http://api.ssexpertsystem.com'), '/');
        $this->apiKey = (string) ($config['api_key'] ?? '');
        $this->clientId = (string) ($config['client_id'] ?? '');
        $this->defaultSenderId = ! empty($config['sender_id']) ? (string) $config['sender_id'] : null;
        $this->defaultPeid = ! empty($config['principle_entity_id']) ? (string) $config['principle_entity_id'] : null;
        $this->timeout = (int) ($config['timeout'] ?? 15);
        $this->retryTimes = (int) ($config['retry']['times'] ?? 3);
        $this->retrySleep = (int) ($config['retry']['sleep'] ?? 100);
    }

    /**
     * Build base HTTP client instance.
     */
    protected function httpClient(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeout)
            ->retry($this->retryTimes, $this->retrySleep, throw: false)
            ->acceptJson()
            ->asJson();
    }

    /**
     * Send single SMS message.
     *
     * @throws SSExpertApiException
     */
    public function send(SmsData|array $smsData): SmsApiResponse
    {
        $this->validateCredentials();

        $dto = is_array($smsData) ? SmsData::fromArray($smsData) : $smsData;
        $payload = $dto->toPayload(
            $this->apiKey,
            $this->clientId,
            $this->defaultSenderId,
            $this->defaultPeid
        );

        try {
            $response = $this->httpClient()->post('/api/v2/SendSMS', $payload);
            $parsed = $this->handleResponse($response, 'send');

            $apiResponse = SmsApiResponse::fromArray($parsed);

            if ($apiResponse->isSuccess()) {
                Log::info('SSExpertSmsService::send: SMS sent successfully', [
                    'mobile' => $dto->mobileNumbers,
                    'template_id' => $dto->templateId,
                    'response' => $parsed,
                ]);
            } else {
                Log::warning('SSExpertSmsService::send: Gateway returned error', [
                    'mobile' => $dto->mobileNumbers,
                    'template_id' => $dto->templateId,
                    'error_code' => $apiResponse->errorCode,
                    'error' => $apiResponse->getErrorMessage(),
                ]);
            }

            return $apiResponse;
        } catch (SSExpertApiException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('SSExpertSmsService::send: Exception occurred while sending SMS', [
                'mobile' => $dto->mobileNumbers,
                'template_id' => $dto->templateId,
                'error' => $e->getMessage(),
            ]);

            throw new SSExpertApiException('Failed to send SMS via SSExpert: ' . $e->getMessage(), 0, null, null, $e);
        }
    }

    /**
     * Helper to send OTP SMS with a registered template.
     *
     * @param  string  $mobile  10-digit mobile number
     * @param  string  $otp  OTP code
     * @param  string  $templateId  DLT Template ID (Default: 1707167402281919826)
     */
    public function sendOtp(string $mobile, string $otp, string $templateId = '1707167402281919826'): SmsApiResponse
    {
        // Template format for 1707167402281919826:
        // "Your Login OTP is {#var#}. Do not share OTP for security reasons to anyone. - Orpat"
        $message = "Your Login OTP is {$otp}. Do not share OTP for security reasons to anyone. - Orpat";

        return $this->send(new SmsData(
            mobileNumbers: $mobile,
            message: $message,
            templateId: $templateId,
        ));
    }

    /**
     * Validate configured credentials.
     *
     * @throws SSExpertAuthException
     */
    protected function validateCredentials(): void
    {
        if (trim($this->apiKey) === '' || trim($this->clientId) === '') {
            throw new SSExpertAuthException('SSExpert API Key and Client ID must be configured.');
        }
    }

    /**
     * Handle HTTP response.
     *
     * @throws SSExpertAuthException
     * @throws SSExpertApiException
     */
    protected function handleResponse(Response $response, string $operation): array
    {
        $status = $response->status();
        $body = $response->json();

        if ($status === 401) {
            Log::error("SSExpertSmsService::$operation: 401 Unauthorized", ['response' => $body]);
            throw new SSExpertAuthException('Unauthorized: Invalid SSExpert API key or client ID.', 401, 401, is_array($body) ? $body : []);
        }

        if ($status >= 500) {
            Log::error("SSExpertSmsService::$operation: $status Server Error", ['response' => $response->body()]);
            throw new SSExpertApiException("SSExpert server returned $status error.", $status, $status, is_array($body) ? $body : []);
        }

        if (! $response->successful() || ! is_array($body)) {
            Log::error("SSExpertSmsService::$operation: Request failed ($status)", ['response' => $response->body()]);
            throw new SSExpertApiException("SSExpert request failed with status $status.", $status, $status, is_array($body) ? $body : []);
        }

        return $body;
    }
}
