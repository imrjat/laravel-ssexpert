<?php

namespace Imrjat\SSExpert\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Imrjat\SSExpert\DTOs\TemplateApiResponse;
use Imrjat\SSExpert\DTOs\TemplateData;
use Imrjat\SSExpert\DTOs\TemplateResponse;
use Imrjat\SSExpert\Exceptions\SSExpertApiException;
use Imrjat\SSExpert\Exceptions\SSExpertAuthException;
use Imrjat\SSExpert\Services\SSExpertTemplateService;

class SSExpertTemplateServiceTest extends TestCase
{
    protected SSExpertTemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SSExpertTemplateService($this->getPackageConfig());
    }

    public function test_list_templates_returns_collection_of_template_responses(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => [
                    [
                        'templateId' => 101,
                        'companyId' => 202,
                        'templateName' => 'LOGIN_OTP',
                        'messageTemplate' => 'Your OTP is {#var#}. Orpat',
                        'isApproved' => true,
                        'isActive' => true,
                        'productName' => 'SMS',
                        'createDate' => '2026-01-01T10:00:00',
                        'createDateString' => '2026-01-01',
                        'approvedDate' => '2026-01-02T10:00:00',
                        'approvedDateString' => '2026-01-02',
                        'dltTemplateId' => '1707168060263570881',
                    ],
                    [
                        'templateId' => 102,
                        'companyId' => 202,
                        'templateName' => 'SERVICE_ASSIGNED',
                        'messageTemplate' => 'Technician assigned {#var#}. Orpat',
                        'isApproved' => true,
                        'isActive' => true,
                        'productName' => 'SMS',
                        'createDate' => '2026-01-03T10:00:00',
                        'createDateString' => '2026-01-03',
                        'approvedDate' => '2026-01-04T10:00:00',
                        'approvedDateString' => '2026-01-04',
                        'dltTemplateId' => '1707168060261075283',
                    ],
                ],
            ], 200),
        ]);

        $templates = $this->service->list();

        $this->assertInstanceOf(Collection::class, $templates);
        $this->assertCount(2, $templates);

        $first = $templates->first();
        $this->assertInstanceOf(TemplateResponse::class, $first);
        $this->assertEquals(101, $first->templateId);
        $this->assertEquals(202, $first->companyId);
        $this->assertEquals('LOGIN_OTP', $first->templateName);
        $this->assertEquals('Your OTP is {#var#}. Orpat', $first->messageTemplate);
        $this->assertEquals('1707168060263570881', $first->dltTemplateId);
        $this->assertTrue($first->isApproved);
        $this->assertTrue($first->isActive);

        Http::assertSent(function ($request) {
            return $request->url() === 'http://api.ssexpertsystem.com/api/v2/Template?ApiKey=test_api_key_123&ClientId=test_client_id_456'
                && $request->method() === 'GET';
        });
    }

    public function test_create_template_with_dto_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => 'Template created successfully',
            ], 200),
        ]);

        $dto = new TemplateData(
            templateName: 'LOGIN_OTP',
            messageTemplate: 'Your OTP is {#var#}',
            dltTemplateId: '1707168060263570881'
        );

        $response = $this->service->create($dto);

        $this->assertInstanceOf(TemplateApiResponse::class, $response);
        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Template created successfully', $response->data);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $request->url() === 'http://api.ssexpertsystem.com/api/v2/Template'
                && $request->method() === 'POST'
                && $body['templateName'] === 'LOGIN_OTP'
                && $body['messageTemplate'] === 'Your OTP is {#var#}'
                && $body['templateId'] === '1707168060263570881'
                && $body['apiKey'] === 'test_api_key_123'
                && $body['clientId'] === 'test_client_id_456';
        });
    }

    public function test_create_template_with_array_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => 'Template created successfully',
            ], 200),
        ]);

        $response = $this->service->create([
            'template_name' => 'NEW_LEAD',
            'message_template' => 'New Lead received {#var#}',
            'dlt_template_id' => '1707168060254867084',
        ]);

        $this->assertTrue($response->isSuccess());

        Http::assertSent(function ($request) {
            $body = $request->data();

            return $request->method() === 'POST'
                && $body['templateName'] === 'NEW_LEAD'
                && $body['templateId'] === '1707168060254867084';
        });
    }

    public function test_update_template_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template?id=101' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => 'Template updated successfully',
            ], 200),
        ]);

        $response = $this->service->update(101, [
            'template_name' => 'LOGIN_OTP_UPDATED',
            'message_template' => 'Your OTP code is {#var#}',
            'dlt_template_id' => '1707168060263570881',
        ]);

        $this->assertTrue($response->isSuccess());
        $this->assertEquals('Template updated successfully', $response->data);

        Http::assertSent(function ($request) {
            $body = $request->data();

            return str_contains($request->url(), 'id=101')
                && $request->method() === 'PUT'
                && $body['templateName'] === 'LOGIN_OTP_UPDATED';
        });
    }

    public function test_delete_template_success(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'errorCode' => 0,
                'errorDescription' => 'Success',
                'data' => 'Template deleted successfully',
            ], 200),
        ]);

        $response = $this->service->delete(101);

        $this->assertTrue($response->isSuccess());

        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE'
                && str_contains($request->url(), 'id=101')
                && str_contains($request->url(), 'ApiKey=test_api_key_123')
                && str_contains($request->url(), 'ClientId=test_client_id_456');
        });
    }

    public function test_find_helpers(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'errorCode' => 0,
                'data' => [
                    [
                        'templateId' => 500,
                        'companyId' => 202,
                        'templateName' => 'COMPLAINT_REGISTERED',
                        'messageTemplate' => 'Your complaint ID is {#var#}',
                        'isApproved' => true,
                        'isActive' => true,
                        'dltTemplateId' => '999888777666',
                    ],
                ],
            ], 200),
        ]);

        $byId = $this->service->findById(500);
        $this->assertNotNull($byId);
        $this->assertEquals(500, $byId->templateId);

        $byDlt = $this->service->findByDltTemplateId('999888777666');
        $this->assertNotNull($byDlt);
        $this->assertEquals('COMPLAINT_REGISTERED', $byDlt->templateName);

        $byName = $this->service->findByName('complaint_registered');
        $this->assertNotNull($byName);
        $this->assertEquals(500, $byName->templateId);

        $nonExistent = $this->service->findById(9999);
        $this->assertNull($nonExistent);
    }

    public function test_missing_credentials_throws_auth_exception(): void
    {
        $unconfiguredService = new SSExpertTemplateService([
            'api_key' => '',
            'client_id' => '',
        ]);

        $this->expectException(SSExpertAuthException::class);
        $this->expectExceptionMessage('SSExpert API Key and Client ID must be configured.');

        $unconfiguredService->list();
    }

    public function test_unauthorized_status_throws_auth_exception(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'message' => 'Invalid credentials',
            ], 401),
        ]);

        $this->expectException(SSExpertAuthException::class);
        $this->service->list();
    }

    public function test_server_error_throws_api_exception(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'message' => 'Internal server error',
            ], 500),
        ]);

        $this->expectException(SSExpertApiException::class);
        $this->service->list();
    }

    public function test_runtime_custom_credentials(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'errorCode' => 0,
                'data' => [],
            ], 200),
        ]);

        $customService = $this->service->withCredentials('custom_key', 'custom_client');
        $customService->list();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'ApiKey=custom_key')
                && str_contains($request->url(), 'ClientId=custom_client');
        });
    }

    public function test_facade_and_container_resolution(): void
    {
        config([
            'ssexpert.api_key' => 'config_api_key',
            'ssexpert.client_id' => 'config_client_id',
        ]);

        $resolved = app(\Imrjat\SSExpert\Contracts\TemplateServiceInterface::class);
        $this->assertInstanceOf(SSExpertTemplateService::class, $resolved);

        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template*' => Http::response([
                'errorCode' => 0,
                'data' => [],
            ], 200),
        ]);

        $res = \Imrjat\SSExpert\Facades\SSExpertTemplate::list();
        $this->assertInstanceOf(Collection::class, $res);
    }

    public function test_template_data_validation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TemplateData('', 'Message');
    }

    public function test_template_data_message_validation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new TemplateData('NAME', '');
    }

    public function test_gateway_error_response_dto(): void
    {
        Http::fake([
            'http://api.ssexpertsystem.com/api/v2/Template' => Http::response([
                'errorCode' => 1001,
                'errorDescription' => 'Template name already exists',
                'data' => null,
            ], 200),
        ]);

        $response = $this->service->create([
            'template_name' => 'DUPLICATE_NAME',
            'message_template' => 'Some text',
        ]);

        $this->assertFalse($response->isSuccess());
        $this->assertEquals(1001, $response->errorCode);
        $this->assertEquals('Template name already exists', $response->getErrorMessage());
    }
}
