# Laravel SSExpert (`imrjat/laravel-ssexpert`)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![Total Downloads](https://img.shields.io/packagist/dt/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.2-777bb4.svg?style=flat-square)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-ff2d20.svg?style=flat-square)](https://laravel.com/)

A modern, robust, and strongly-typed Laravel client package for the **[SSExpertSystem SMS & Template Gateway API](http://api.ssexpertsystem.com)**.

Built with clean architecture, resilient HTTP retries, DLT regulatory compliance (PEID / Entity IDs), strongly-typed DTOs, and full testability with `Http::fake()`.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Method 1: Standard Installation (Packagist)](#method-1-standard-installation-packagist)
  - [Method 2: Local Monorepo / Path Repository](#method-2-local-monorepo--path-repository)
  - [Method 3: Direct Git Repository](#method-3-direct-git-repository)
- [Configuration](#configuration)
  - [Environment Variables (.env)](#environment-variables-env)
  - [Publish Configuration](#publish-configuration)
- [Usage Guide](#usage-guide)
  - [1. Sending SMS & OTPs](#1-sending-sms--otps)
    - [Quick OTP Sending](#quick-otp-sending)
    - [Sending Custom SMS with DTO](#sending-custom-sms-with-dto)
    - [Using an Associative Array](#using-an-associative-array)
    - [Handling Responses & Message IDs](#handling-responses--message-ids)
  - [2. Managing DLT Templates](#2-managing-dlt-templates)
    - [Listing All Templates](#listing-all-templates)
    - [Finding a Template by DLT ID](#finding-a-template-by-dlt-id)
    - [Finding a Template by Name or ID](#finding-a-template-by-name-or-id)
    - [Creating a New Template](#creating-a-new-template)
    - [Updating an Existing Template](#updating-an-existing-template)
    - [Deleting a Template](#deleting-a-template)
  - [3. Artisan Test Command](#3-artisan-test-command)
  - [4. Dependency Injection](#4-dependency-injection)
  - [5. Laravel Custom Notification Channel](#5-laravel-custom-notification-channel)
  - [6. Migration Guide (Replacing MSG91)](#6-migration-guide-replacing-msg91)
- [Error Handling & Exceptions](#error-handling--exceptions)
- [Testing & Mocking](#testing--mocking)
- [Publishing to GitHub & Packagist](#publishing-to-github--packagist)
- [Changelog](#changelog)
- [License](#license)

---

## Features

- 📲 **SMS & OTP Dispatching**: High-throughput transactional SMS and OTP delivery using approved DLT templates.
- 📑 **Complete Template CRUD**: Programmatically manage and query DLT templates (`list`, `create`, `update`, `delete`, `findById`, `findByDltTemplateId`, `findByName`).
- 🛡️ **Strongly Typed DTOs**: Clean `SmsData`, `SmsApiResponse`, `TemplateData`, `TemplateResponse`, and `TemplateApiResponse` models.
- 🇮🇳 **TRAI DLT Compliant**: Native support for Principal Entity ID (`PEID`), Header / Sender IDs, and DLT Template IDs.
- ⚡ **Resilient HTTP Client**: Built-in exponential backoff retries (`Http::retry()`), timeout control, and structured contextual logging.
- 🛠️ **Built-in Artisan Tool**: Interactive `php artisan ssexpert:test-sms` CLI command for zero-friction live testing.
- 🧩 **Laravel Package Auto-Discovery**: Automatic Facade and ServiceProvider registration on Laravel 10, 11, and 12.
- 🧪 **100% Mock Testable**: Test your controllers and services offline using Laravel's standard `Http::fake()`.

---

## Requirements

- **PHP**: `^8.2` or higher
- **Laravel Framework**: `^10.0`, `^11.0`, or `^12.0`
- **Guzzle / HTTP Client**: `^7.8`

---

## Installation

### Method 1: Standard Installation (Packagist)

Once published to Packagist, install the package in any Laravel project with:

```bash
composer require imrjat/laravel-ssexpert
```

---

### Method 2: Local Monorepo / Path Repository

To use or develop the package locally inside your project (e.g., under `packages/laravel-ssexpert`):

1. Add the path repository to your root `composer.json`:
   ```json
   "repositories": [
       {
           "type": "path",
           "url": "packages/laravel-ssexpert"
       }
   ],
   "require": {
       "imrjat/laravel-ssexpert": "@dev"
   }
   ```
2. Or configure direct PSR-4 autoloading in your root `composer.json`:
   ```json
   "autoload": {
       "psr-4": {
           "App\\": "app/",
           "Imrjat\\SSExpert\\": "packages/laravel-ssexpert/src/"
       }
   }
   ```
3. Run `composer dump-autoload`.

---

### Method 3: Direct Git Repository

To install directly from GitHub before registering on Packagist:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/imrjat/laravel-ssexpert.git"
    }
],
"require": {
    "imrjat/laravel-ssexpert": "dev-main"
}
```

---

## Configuration

### Environment Variables (`.env`)

Add the following credentials to your `.env` file:

```env
# SSExpert Gateway Settings
SSEXPERT_BASE_URL=http://api.ssexpertsystem.com
SSEXPERT_API_KEY=your_ssexpert_api_key
SSEXPERT_CLIENT_ID=your_ssexpert_client_id

# TRAI DLT Compliance Settings
SSEXPERT_PEID=your_dlt_principal_entity_id
SSEXPERT_SENDER_ID=ORPATG

# HTTP Client Performance & Resilience
SSEXPERT_TIMEOUT=15
SSEXPERT_RETRY_TIMES=3
SSEXPERT_RETRY_SLEEP=100
```

#### Settings Reference

| Key | Description | Default |
|---|---|---|
| `SSEXPERT_BASE_URL` | Base API gateway endpoint | `http://api.ssexpertsystem.com` |
| `SSEXPERT_API_KEY` | Your SSExpert account API key | `""` *(Required)* |
| `SSEXPERT_CLIENT_ID` | Your SSExpert account Client ID | `""` *(Required)* |
| `SSEXPERT_PEID` | TRAI DLT Principal Entity ID (Corporate Identifier) | `""` |
| `SSEXPERT_SENDER_ID` | Default approved 6-character header / sender ID | `ORPATG` |
| `SSEXPERT_TIMEOUT` | HTTP request timeout in seconds | `15` |
| `SSEXPERT_RETRY_TIMES` | Maximum automatic retries on network connection failure | `3` |
| `SSEXPERT_RETRY_SLEEP` | Sleep duration in milliseconds between retries | `100` |

---

### Publish Configuration

You can publish the default configuration file to `config/ssexpert.php`:

```bash
php artisan vendor:publish --tag=ssexpert-config
```

---

## Usage Guide

### 1. Sending SMS & OTPs

#### Quick OTP Sending
Send an OTP to any 10-digit mobile number using an approved DLT Template ID:

```php
use Imrjat\SSExpert\Facades\SSExpertSms;

// Send OTP (Default DLT Template: 1707167402281919826)
$response = SSExpertSms::sendOtp(
    mobile: '9770231935',
    otp: '582914',
    templateId: '1707167402281919826'
);

if ($response->isSuccess()) {
    echo "SMS Dispatched! Message ID: " . $response->getMessageId();
} else {
    echo "Failed: " . $response->getErrorMessage();
}
```

---

#### Sending Custom SMS with DTO
Use `SmsData` for type safety and compile-time validation:

```php
use Imrjat\SSExpert\Facades\SSExpertSms;
use Imrjat\SSExpert\DTOs\SmsData;

$sms = new SmsData(
    mobileNumbers: '9770231935',
    message: 'Dear Customer, your service request ID 98765 is registered. - Orpat',
    templateId: '1707168060254867084',
    senderId: 'ORPATG',             // Optional override
    principleEntityId: '1101554433' // Optional override
);

$response = SSExpertSms::send($sms);

if ($response->isSuccess()) {
    $messageId = $response->getMessageId();
}
```

---

#### Using an Associative Array

```php
use Imrjat\SSExpert\Facades\SSExpertSms;

$response = SSExpertSms::send([
    'mobile' => '9770231935',
    'message' => 'Your Happy Code for Service Request ID 12345 is 789012. Orpat',
    'template_id' => '1707168060254867084',
]);
```

---

#### Handling Responses & Message IDs

The `SmsApiResponse` object gives you convenient helper methods:

```php
$response = SSExpertSms::sendOtp('9770231935', '123456');

// 1. Check if accepted by telecom gateway
if ($response->isSuccess()) {
    // 2. Get unique Gateway Message ID (e.g. 1f66ba54-5066-428a-b36b-0f3199318dab)
    $messageId = $response->getMessageId();
} else {
    // 3. Get error code and human-readable error description
    $code = $response->errorCode;
    $error = $response->getErrorMessage();
}

// 4. Access full raw JSON response
$rawArray = $response->raw;
```

---

### 2. Managing DLT Templates

#### Listing All Templates

Fetch all registered templates as a Laravel `Collection` of `TemplateResponse` DTOs:

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

$templates = SSExpertTemplate::list();

foreach ($templates as $template) {
    echo "ID: " . $template->templateId . PHP_EOL;
    echo "Name: " . $template->templateName . PHP_EOL;
    echo "DLT ID: " . $template->dltTemplateId . PHP_EOL;
    echo "Message: " . $template->messageTemplate . PHP_EOL;
    echo "Status: " . ($template->isApproved ? 'Approved' : 'Pending') . PHP_EOL;
}
```

---

#### Finding a Template by DLT ID

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

$template = SSExpertTemplate::findByDltTemplateId('1707167402281919826');

if ($template) {
    echo "Found: " . $template->templateName;
    echo "Format: " . $template->messageTemplate;
}
```

---

#### Finding a Template by Name or ID

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

// Find by Template Name (case-insensitive)
$template = SSExpertTemplate::findByName('OTP for security');

// Find by internal SSExpert system ID
$template = SSExpertTemplate::findById(15231);
```

---

#### Creating a New Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;
use Imrjat\SSExpert\DTOs\TemplateData;

$data = new TemplateData(
    templateName: 'PAYMENT_RECEIPT_SMS',
    messageTemplate: 'Dear {#var#}, we received your payment of INR {#var#}. - Orpat',
    dltTemplateId: '1707174039816404337'
);

$response = SSExpertTemplate::create($data);

if ($response->isSuccess()) {
    echo "Template registered successfully!";
}
```

---

#### Updating an Existing Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;
use Imrjat\SSExpert\DTOs\TemplateData;

$response = SSExpertTemplate::update(15231, new TemplateData(
    templateName: 'OTP for security updated',
    messageTemplate: 'Your Login OTP is {#var#}. Valid for 2 mins. - Orpat',
    dltTemplateId: '1707167402281919826'
));
```

---

#### Deleting a Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

$response = SSExpertTemplate::delete(15231);

if ($response->isSuccess()) {
    echo "Template deleted.";
}
```

---

### 3. Artisan Test Command

The package includes an Artisan console command for immediate verification from the CLI:

```bash
# Send test OTP using default template
php artisan ssexpert:test-sms 9770231935

# Send test OTP specifying a custom OTP and DLT template ID
php artisan ssexpert:test-sms 9770231935 --template=1707167402281919826 --otp=839201
```

**Example Output:**
```
Sending test SMS via SSExpertSystem...
+------------------+-------------------------------+
| Parameter        | Value                         |
+------------------+-------------------------------+
| Target Mobile    | 9770231935                    |
| Generated OTP    | 839201                        |
| DLT Template ID  | 1707167402281919826           |
| Sender ID        | ORPATG                        |
| Gateway Base URL | http://api.ssexpertsystem.com |
+------------------+-------------------------------+
Found Approved Template: [OTP for security]
Template Format: Your Login OTP is {#var#}. Do not share OTP for security reasons to anyone. - Orpat

✔ SMS sent successfully!
Message ID: 1f66ba54-5066-428a-b36b-0f3199318dab
```

---

### 4. Dependency Injection

You can inject `SmsServiceInterface` or `TemplateServiceInterface` anywhere in your application:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\TemplateServiceInterface;

class AuthController extends Controller
{
    public function sendOtp(Request $request, SmsServiceInterface $sms)
    {
        $otp = (string) rand(100000, 999999);
        
        $response = $sms->sendOtp(
            mobile: $request->input('mobile'),
            otp: $otp
        );

        if (! $response->isSuccess()) {
            return response()->json(['error' => $response->getErrorMessage()], 400);
        }

        return response()->json([
            'message' => 'OTP sent successfully',
            'message_id' => $response->getMessageId(),
        ]);
    }
}
```

---

### 5. Laravel Custom Notification Channel

To send SMS via Laravel Notifications:

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
        $message = $notification->toSSExpert($notifiable);

        if ($message instanceof SmsData) {
            $this->sms->send($message);
        }
    }
}
```

Then in your notification class:

```php
namespace App\Notifications;

use App\Channels\SSExpertChannel;
use Illuminate\Notifications\Notification;
use Imrjat\SSExpert\DTOs\SmsData;

class ServiceAssignedNotification extends Notification
{
    public function via($notifiable): array
    {
        return [SSExpertChannel::class];
    }

    public function toSSExpert($notifiable): SmsData
    {
        return new SmsData(
            mobileNumbers: $notifiable->mobile,
            message: "Dear {$notifiable->name}, your technician is assigned. - Orpat",
            templateId: '1707168060261075283'
        );
    }
}
```

---

### 6. Migration Guide (Replacing MSG91)

If your project currently calls MSG91 (e.g. via `App\Helpers\SendSMS` or raw cURL), replacing it with `SSExpert` is a drop-in change:

#### Legacy MSG91 Call:
```php
// BEFORE (MSG91 API query parameter call)
$url = "http://api.msg91.com/api/sendhttp.php?route=4&sender=ORPATG&mobiles=91{$number}&authkey={$key}&message={$message}&DLT_TE_ID={$templateId}";
Http::get($url);
```

#### New SSExpert Call:
```php
// AFTER (Strongly typed, compliant, auto-retried)
use Imrjat\SSExpert\Facades\SSExpertSms;

SSExpertSms::sendOtp($number, $otp, '1707167402281919826');
```

---

## Error Handling & Exceptions

All gateway operations throw domain-specific exceptions on critical errors:

- **`Imrjat\SSExpert\Exceptions\SSExpertAuthException`**: Thrown when `ApiKey` or `ClientId` are missing, invalid, or return `401 Unauthorized`.
- **`Imrjat\SSExpert\Exceptions\SSExpertApiException`**: Thrown on `500+` server errors, network connection drops, or HTTP client timeouts.

```php
use Imrjat\SSExpert\Facades\SSExpertSms;
use Imrjat\SSExpert\Exceptions\SSExpertAuthException;
use Imrjat\SSExpert\Exceptions\SSExpertApiException;

try {
    $response = SSExpertSms::sendOtp('9770231935', '123456');
} catch (SSExpertAuthException $e) {
    Log::critical('SSExpert Credentials invalid: ' . $e->getMessage());
} catch (SSExpertApiException $e) {
    Log::error('SSExpert API Connection failed: ' . $e->getMessage());
}
```

---

## Testing & Mocking

You can mock all SSExpert API calls in your test suites using Laravel's `Http::fake()` without sending actual SMS messages:

```php
use Illuminate\Support\Facades\Http;
use Imrjat\SSExpert\Facades\SSExpertSms;

public function test_user_receives_otp_sms()
{
    // Mock the SSExpert SendSMS endpoint
    Http::fake([
        'http://api.ssexpertsystem.com/api/v2/SendSMS' => Http::response([
            'errorCode' => 0,
            'data' => [
                [
                    'MessageErrorCode' => 0,
                    'MessageId' => 'mock-message-id-123',
                ]
            ],
        ], 200),
    ]);

    $response = SSExpertSms::sendOtp('9770231935', '582914');

    $this->assertTrue($response->isSuccess());
    $this->assertEquals('mock-message-id-123', $response->getMessageId());

    // Assert that the request was sent with proper payload
    Http::assertSent(function ($request) {
        return $request->url() === 'http://api.ssexpertsystem.com/api/v2/SendSMS'
            && $request['mobileNumbers'] === '9770231935'
            && $request['templateId'] === '1707167402281919826';
    });
}
```

To run the package's internal unit test suite:

```bash
./vendor/bin/phpunit packages/laravel-ssexpert/tests
```

---

## Publishing to GitHub & Packagist

### Step 1: Push to GitHub

The repository is pre-initialized in `packages/laravel-ssexpert`. Push it to your GitHub account:

```bash
cd packages/laravel-ssexpert
git push -u origin main
```

### Step 2: Tag a Release Version

```bash
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

### Step 3: Publish to Packagist

1. Go to **[https://packagist.org/packages/submit](https://packagist.org/packages/submit)**.
2. Enter your repository URL: `https://github.com/imrjat/laravel-ssexpert`.
3. Click **Check** and then **Submit**.
4. Set up the GitHub Webhook on Packagist to automatically sync future releases.

Once published, anyone can install your package with:
```bash
composer require imrjat/laravel-ssexpert
```

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for details on changes in each release.

---

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more details.
