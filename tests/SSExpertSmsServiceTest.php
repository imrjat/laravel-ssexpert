<?php

namespace Imrjat\SSExpert\Tests;

use Illuminate\Support\Facades\Http;
use Imrjat\SSExpert\DTOs\SmsApiResponse;
use Imrjat\SSExpert\DTOs\SmsData;
use Imrjat\SSExpert\Exceptions\SSExpertApiException;
use Imrjat\SSExpert\Exceptions\SSExpertAuthException;
use Imrjat\SSExpert\Services\SSExpertSmsService;

class SSExpertSmsServiceTest extends TestCase
{
    protected SSExpertSmsService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = new SSExpertSmsService(array_merge($this->getPackageConfig(), [
            'sender_id' => 'ORPATG',
            'principle_entity_id' => '1101554433221100123',
        ]));
    }

    public function test_send_sms_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => '1234567890abcdef',
            ], 200),
        ]);

        $sms = new SmsData(
            mobileNumbers: '9770231935',
            message: 'Your Login OTP is 456789. Do not share OTP for security reasons to anyone. - Orpat',
            templateId: '1707167402281919826',
        );

        $response = $this->smsService->send($sms);

        $this->assertInstanceOf(SmsApiResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('1234567890abcdef', $response->getMessageId());

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $request->url() === 'http://api.ssexpertsystem.com/api/v2/SendSMS'
                && $request->method() === 'POST'
                && $body['mobileNumbers'] === '9770231935'
                && $body['senderId'] === 'ORPATG'
                && $body['templateId'] === '1707167402281919826'
                && $body['principleEntityId'] === '1101554433221100123'
                && $body['apiKey'] === 'test_api_key_123'
                && $body['clientId'] === 'test_client_id_456';
        });
    }

    public function test_send_otp_helper_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
                'errorCode' => 0,
                'data' => 'msg_otp_9999',
            ], 200),
        ]);

        $response = $this->smsService->sendOtp('9770231935', '839201');

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('msg_otp_9999', $response->getMessageId());

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $body['mobileNumbers'] === '9770231935'
                && str_contains($body['message'], '839201')
                && $body['templateId'] === '1707167402281919826';
        });
    }

    public function test_invalid_mobile_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new SmsData('123', 'Hello');
    }

    public function test_empty_message_throws_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new SmsData('9770231935', '');
    }

    public function test_send_sms_unauthorized_throws_exception(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
                'message' => 'Unauthorized',
            ], 401),
        ]);

        $this->expectException(SSExpertAuthException::class);
        $this->smsService->sendOtp('9770231935', '123456');
    }

    public function test_send_sms_gateway_error_code(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
                'errorCode' => 104,
                'errorDescription' => 'Insufficient credit balance',
                'data' => null,
            ], 200),
        ]);

        $response = $this->smsService->sendOtp('9770231935', '123456');

        $this->assertFalse($response->isSuccess());
        $this->assertEquals(104, $response->errorCode);
        $this->assertEquals('Insufficient credit balance', $response->getErrorMessage());
    }
}
