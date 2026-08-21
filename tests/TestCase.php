<?php

namespace Imrjat\SSExpert\Tests;

use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageConfig(): array
    {
        return [
            'base_url' => 'http://api.ssexpertsystem.com',
            'api_key' => 'test_api_key_123',
            'client_id' => 'test_client_id_456',
            'timeout' => 5,
            'retry' => [
                'times' => 1,
                'sleep' => 10,
            ],
        ];
    }
}
