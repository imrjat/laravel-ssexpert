<?php

namespace Imrjat\SSExpert\Tests;

use Illuminate\Support\Facades\Http;
use Imrjat\SSExpert\Contracts\BalanceServiceInterface;
use Imrjat\SSExpert\Contracts\GroupServiceInterface;
use Imrjat\SSExpert\Contracts\SenderIdServiceInterface;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\TemplateServiceInterface;
use Imrjat\SSExpert\Facades\SSExpert;

class SSExpertManagerTest extends TestCase
{
    public function test_umbrella_facade_sub_services(): void
    {
        $this->assertInstanceOf(SmsServiceInterface::class, SSExpert::sms());
        $this->assertInstanceOf(TemplateServiceInterface::class, SSExpert::template());
        $this->assertInstanceOf(TemplateServiceInterface::class, SSExpert::templates());
        $this->assertInstanceOf(BalanceServiceInterface::class, SSExpert::balance());
        $this->assertInstanceOf(SenderIdServiceInterface::class, SSExpert::senderId());
        $this->assertInstanceOf(SenderIdServiceInterface::class, SSExpert::senderIds());
        $this->assertInstanceOf(GroupServiceInterface::class, SSExpert::group());
        $this->assertInstanceOf(GroupServiceInterface::class, SSExpert::groups());
    }

    public function test_umbrella_facade_shortcuts(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
                'ErrorCode' => 0,
                'Data' => 'msg_123',
            ], 200),
            'http://api.ssexpertsystem.com/api/v2/Balance*' => Http::response([
                'ErrorCode' => 0,
                'Data' => [
                    ['PluginType' => 'SMS', 'Credits' => '1500'],
                ],
            ], 200),
        ]);

        $res = SSExpert::sendOtp('9876543210', '999111');
        $this->assertTrue($res->isSuccess());

        $credits = SSExpert::getCredits();
        $this->assertEquals(1500.0, $credits);
    }
}
