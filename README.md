# Laravel SSExpert (`imrjat/laravel-ssexpert`)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![Total Downloads](https://img.shields.io/packagist/dt/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-777bb4.svg?style=flat-square)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x%20%7C%2013.x-ff2d20.svg?style=flat-square)](https://laravel.com/)

A modern, robust, and strongly-typed Laravel client package for the **[SSExpertSystem SMS & Template Gateway API](http://api.ssexpertsystem.com)** ([Swagger Specification](http://api.ssexpertsystem.com/swagger/v1/swagger.json)).

Adhering to Laravel best practices, the package exposes a single, unified entrypoint facade: **`SSExpert`**.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Environment Variables (.env)](#environment-variables-env)
  - [Publish Configuration](#publish-configuration)
- [Usage Guide](#usage-guide)
  - [1. Account Balance & Credits](#1-account-balance--credits)
  - [2. Sending Single SMS & OTPs](#2-sending-single-sms--otps)
  - [3. Sending Bulk SMS Campaigns](#3-sending-bulk-sms-campaigns)
  - [4. Message Status & Delivery Reports](#4-message-status--delivery-reports)
  - [5. DLT Template Management](#5-dlt-template-management)
  - [6. Sender ID (Header) Management](#6-sender-id-header-management)
  - [7. Contact Group Management](#7-contact-group-management)
  - [8. Artisan CLI Commands](#8-artisan-cli-commands)
  - [9. Dependency Injection](#9-dependency-injection)
  - [10. Laravel Notification Channel](#10-laravel-notification-channel)
- [Error Handling & Exceptions](#error-handling--exceptions)
- [Testing & Mocking](#testing--mocking)
- [License](#license)

---

## Features

- 💎 **Single Unified Facade**: Access all gateway services cleanly through `SSExpert` without global alias pollution.
- 💰 **Real-time Balance Queries**: Instant SMS credit tracking via `SSExpert::balance()->getCredits()`.
- 📲 **Single & OTP SMS**: Low-latency OTP and transactional SMS dispatching with DLT compliance.
- 🚀 **Bulk SMS Campaigns**: High-volume personalized bulk messaging with `BulkSmsData`.
- 📊 **Delivery Reports & Status**: Real-time message delivery logs and transmission statistics.
- 📑 **DLT Template Management**: Complete programmatic CRUD and fast lookup for registered DLT templates.
- 🏷️ **Sender ID (Header) Management**: Manage approved 6-character sender names.
- 👥 **Contact Groups**: Manage address book groups and contact counts.
- 🛡️ **Strongly Typed DTOs**: Strict validation and clean models for all requests and responses.
- ⚡ **Resilient HTTP Client**: Automated exponential retries (`Http::retry()`), timeout control, and logging.
- 🧪 **100% Mock Testable**: Comprehensive test suite and seamless mocking with `Http::fake()`.

---

## Requirements

- **PHP**: `^8.2` or higher
- **Laravel Framework**: `^10.0`, `^11.0`, `^12.0`, or `^13.0`
- **Guzzle / HTTP Client**: `^7.8`

---

## Installation

Install the package via Composer:

```bash
composer require imrjat/laravel-ssexpert
```

---

## Configuration

### Environment Variables (`.env`)

Add the following credentials to your `.env` file:

```env
# Gateway Credentials
SSEXPERT_BASE_URL=http://api.ssexpertsystem.com
SSEXPERT_API_KEY=your_api_key_here
SSEXPERT_CLIENT_ID=your_client_id_here

# TRAI DLT Compliance Settings
SSEXPERT_PEID=your_dlt_principal_entity_id_here
SSEXPERT_SENDER_ID=YOUR_SENDER_ID

# HTTP Client Performance & Resilience
SSEXPERT_TIMEOUT=15
SSEXPERT_RETRY_TIMES=3
SSEXPERT_RETRY_SLEEP=100
```

#### Settings Reference

| Variable | Description | Default |
|---|---|---|
| `SSEXPERT_BASE_URL` | Base API gateway endpoint | `http://api.ssexpertsystem.com` |
| `SSEXPERT_API_KEY` | Your SSExpert account API key | `""` *(Required)* |
| `SSEXPERT_CLIENT_ID` | Your SSExpert account Client ID | `""` *(Required)* |
| `SSEXPERT_PEID` | TRAI DLT Principal Entity ID (Corporate ID) | `""` |
| `SSEXPERT_SENDER_ID` | Default approved 6-character sender ID / header | `TESTID` |
| `SSEXPERT_TIMEOUT` | HTTP request timeout in seconds | `15` |
| `SSEXPERT_RETRY_TIMES` | Automatic retries on network glitched connections | `3` |
| `SSEXPERT_RETRY_SLEEP` | Sleep interval in milliseconds between retries | `100` |

---

### Publish Configuration

Publish the config file to `config/ssexpert.php`:

```bash
php artisan vendor:publish --tag=ssexpert-config
```

---

## Usage Guide

Everything is accessed fluently through the single **`SSExpert`** facade:

---

### 1. Account Balance & Credits

```php
use Imrjat\SSExpert\Facades\SSExpert;

// 1. Quick shortcut: Get available SMS credits as a float
$credits = SSExpert::getCredits();
echo "Available SMS Credits: " . number_format($credits, 2);

// 2. Get detailed primary balance DTO
$balance = SSExpert::balance()->get();
echo "Product: " . $balance->pluginType; // "SMS"
echo "Credits: " . $balance->credits;    // e.g. 5000.0

// 3. List all product balance records
$records = SSExpert::balance()->list();
```

---

### 2. Sending Single SMS & OTPs

> **TRAI DLT Entity ID (`principleEntityId`)**:
> In Indian telecom regulations, every SMS transaction requires a registered **Principal Entity ID (PEID)** alongside the **DLT Template ID**. 
> - **Automatic Global Injection**: Once `SSEXPERT_PEID` is configured in your `.env`, `SSExpert` **automatically attaches** your `principleEntityId` to every SMS / OTP call.
> - **Per-Request Override**: You can also explicitly pass or override `principleEntityId` directly in `SmsData` or `BulkSmsData`.

#### Quick OTP Sending (Shortcut)
```php
use Imrjat\SSExpert\Facades\SSExpert;

// principleEntityId and senderId are automatically injected from .env
$response = SSExpert::sendOtp(
    mobile: '9876543210',
    otp: '582914',
    templateId: '1107160000000000001' // Approved DLT Template ID
);

if ($response->isSuccess()) {
    echo "OTP Dispatched! Message ID: " . $response->getMessageId();
} else {
    echo "Failed: " . $response->getErrorMessage();
}
```

#### Custom SMS with DTO & Explicit PEID
```php
use Imrjat\SSExpert\Facades\SSExpert;
use Imrjat\SSExpert\DTOs\SmsData;

$sms = new SmsData(
    mobileNumbers: '9876543210',
    message: 'Dear Customer, your request ID 98765 is confirmed.',
    templateId: '1107160000000000002',              // DLT Content Template ID
    principleEntityId: '1101554433000000000',       // DLT Principal Entity ID (Optional override)
    senderId: 'TESTID'                              // Sender ID / Header (Optional override)
);

$response = SSExpert::sms()->send($sms);
```

---

### 3. Sending Bulk SMS Campaigns

Send customized messages to multiple recipients with automated or custom `principleEntityId`:

```php
use Imrjat\SSExpert\Facades\SSExpert;
use Imrjat\SSExpert\DTOs\BulkSmsData;
use Imrjat\SSExpert\DTOs\BulkMessageItem;

$bulkData = new BulkSmsData(
    messages: [
        new BulkMessageItem('9876543210', 'Dear User 1, your OTP is 112233.'),
        new BulkMessageItem('9123456780', 'Dear User 2, your OTP is 445566.'),
    ],
    templateId: '1107160000000000001',
    principleEntityId: '1101554433000000000', // DLT PEID (Optional override)
    senderId: 'TESTID'                       // Header (Optional override)
);

$response = SSExpert::sms()->sendBulk($bulkData);

if ($response->isSuccess()) {
    echo "Bulk SMS campaign accepted!";
}
```

---

### 4. Message Status & Delivery Reports

```php
use Imrjat\SSExpert\Facades\SSExpert;

// 1. Query delivery status by Gateway Message ID
$status = SSExpert::sms()->getMessageStatus('mock-uuid-112233');

// 2. Query delivery logs for the last 7 days
$report = SSExpert::sms()->getDeliveryReport(days: 7);

// 3. Paginated transmission logs with date filtering
$logs = SSExpert::sms()->getSmsLogs(start: 0, length: 50, fromDate: '2026-08-01', endDate: '2026-08-21');

// 4. Report summary statistics
$summary = SSExpert::sms()->getReportSummary(fromDate: '2026-08-01', endDate: '2026-08-21');
```

---

### 5. DLT Template Management

```php
use Imrjat\SSExpert\Facades\SSExpert;
use Imrjat\SSExpert\DTOs\TemplateData;

// List all registered templates
$templates = SSExpert::template()->list();

// Find by DLT Template ID
$template = SSExpert::template()->findByDltTemplateId('1107160000000000001');

// Find by Name
$template = SSExpert::template()->findByName('OTP_SECURITY');

// Register a new template
$response = SSExpert::template()->create(new TemplateData(
    templateName: 'PAYMENT_RECEIVED',
    messageTemplate: 'Dear {#var#}, payment of INR {#var#} received.',
    dltTemplateId: '1107160000000000003'
));

// Update template
SSExpert::template()->update(101, new TemplateData(
    templateName: 'OTP_SECURITY_V2',
    messageTemplate: 'Your Login OTP is {#var#}. Valid for 2 mins.',
    dltTemplateId: '1107160000000000001'
));

// Delete template
SSExpert::template()->delete(101);
```

---

### 6. Sender ID (Header) Management

```php
use Imrjat\SSExpert\Facades\SSExpert;

// List all approved headers
$headers = SSExpert::senderId()->list();

// Find by header name
$header = SSExpert::senderId()->findByName('TESTID');

// Submit request for new Sender ID
SSExpert::senderId()->create('NEWHDR', 'Transaction and Alert Notifications');

// Delete Sender ID
SSExpert::senderId()->delete(292);
```

---

### 7. Contact Group Management

```php
use Imrjat\SSExpert\Facades\SSExpert;

// List groups
$groups = SSExpert::group()->list();

// Create group
SSExpert::group()->create('Support Team');

// Update group
SSExpert::group()->update(10, 'Support Team North');

// Delete group
SSExpert::group()->delete(10);
```

---

### 8. Artisan CLI Commands

```bash
# Check available balance and credits
php artisan ssexpert:balance
```

---

### 9. Dependency Injection

Inject service contracts directly into Controllers, Jobs, or Commands:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\BalanceServiceInterface;

class NotificationController extends Controller
{
    public function send(Request $request, SmsServiceInterface $sms, BalanceServiceInterface $balance)
    {
        if ($balance->getCredits() < 1) {
            return response()->json(['error' => 'Insufficient SMS credits'], 402);
        }

        $response = $sms->sendOtp(
            mobile: $request->input('mobile'),
            otp: (string) rand(100000, 999999)
        );

        return response()->json(['success' => $response->isSuccess(), 'message_id' => $response->getMessageId()]);
    }
}
```

---

### 10. Laravel Notification Channel

```php
namespace App\Channels;

use Illuminate\Notifications\Notification;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\DTOs\SmsData;

class SSExpertChannel
{
    public function __construct(protected SmsServiceInterface $sms) {}

    public function send($notifiable, Notification $notification): void
    {
        $data = $notification->toSSExpert($notifiable);

        if ($data instanceof SmsData) {
            $this->sms->send($data);
        }
    }
}
```

---

## Error Handling & Exceptions

- **`Imrjat\SSExpert\Exceptions\SSExpertAuthException`**: Thrown on invalid credentials or 401 Unauthorized.
- **`Imrjat\SSExpert\Exceptions\SSExpertApiException`**: Thrown on network drops, HTTP timeouts, or gateway 500 errors.

```php
use Imrjat\SSExpert\Facades\SSExpert;
use Imrjat\SSExpert\Exceptions\SSExpertAuthException;
use Imrjat\SSExpert\Exceptions\SSExpertApiException;

try {
    $response = SSExpert::sendOtp('9876543210', '123456');
} catch (SSExpertAuthException $e) {
    Log::critical('SSExpert Auth Failed: ' . $e->getMessage());
} catch (SSExpertApiException $e) {
    Log::error('SSExpert Connection Error: ' . $e->getMessage());
}
```

---

## Testing & Mocking

Mock all SSExpert API endpoints in your test suites using `Http::fake()`:

```php
use Illuminate\Support\Facades\Http;
use Imrjat\SSExpert\Facades\SSExpert;

public function test_otp_sending()
{
    Http::fake([
        'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
            'ErrorCode' => 0,
            'Data' => [['MessageErrorCode' => 0, 'MessageId' => 'mock-id-123']],
        ], 200),
    ]);

    $res = SSExpert::sendOtp('9876543210', '582914');

    $this->assertTrue($res->isSuccess());
    $this->assertEquals('mock-id-123', $res->getMessageId());
}
```

To run the package test suite:

```bash
./vendor/bin/phpunit packages/laravel-ssexpert/tests
```

---

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more details.
