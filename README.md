# Laravel SSExpert (`imrjat/laravel-ssexpert`)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![Total Downloads](https://img.shields.io/packagist/dt/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

A modern, fluent, and strongly-typed Laravel client for the **SSExpertSystem SMS & Template Gateway API** ([http://api.ssexpertsystem.com](http://api.ssexpertsystem.com)).

---

## Features

- 📑 **Full Template CRUD**: Manage SMS DLT templates (`list`, `create`, `update`, `delete`, `findById`, `findByDltTemplateId`, `findByName`).
- 📲 **SMS & OTP Dispatching**: Send single SMS, transactional messages, and OTPs with approved DLT templates.
- 🛡️ **Strongly Typed DTOs**: Clean `TemplateData`, `TemplateResponse`, `TemplateApiResponse`, `SmsData`, and `SmsApiResponse` models.
- ⚡ **Resilient HTTP Client**: Built-in timeout management and automated exponential retry via Laravel's HTTP Client.
- 🛠️ **Artisan Test Command**: Interactive terminal command (`ssexpert:test-sms`) for rapid testing.
- 🧩 **Laravel Auto-Discovery**: Automatic service provider and facade registration for Laravel 10, 11, and 12.
- 🧪 **100% Testable**: Works seamlessly with `Http::fake()` for mock testing in unit and feature test suites.

---

## Installation

Install the package via Composer:

```bash
composer require imrjat/laravel-ssexpert
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=ssexpert-config
```

---

## Configuration

Add the following environment variables to your `.env` file:

```env
SSEXPERT_BASE_URL=http://api.ssexpertsystem.com
SSEXPERT_API_KEY=your_api_key_here
SSEXPERT_CLIENT_ID=your_client_id_here
SSEXPERT_PEID=your_dlt_principal_entity_id_here
SSEXPERT_SENDER_ID=ORPATG
SSEXPERT_TIMEOUT=15
SSEXPERT_RETRY_TIMES=3
SSEXPERT_RETRY_SLEEP=100
```

---

## Usage

### 1. Sending SMS & OTPs

#### Send OTP SMS
```php
use Imrjat\SSExpert\Facades\SSExpertSms;

// Send OTP using the default or specified approved DLT Template ID
$response = SSExpertSms::sendOtp('9770231935', '456789', '1707167402281919826');

if ($response->isSuccess()) {
    echo "SMS Sent! Message ID: " . $response->getMessageId();
} else {
    echo "Failed: " . $response->getErrorMessage();
}
```

#### Send Custom SMS with DTO
```php
use Imrjat\SSExpert\Facades\SSExpertSms;
use Imrjat\SSExpert\DTOs\SmsData;

$sms = new SmsData(
    mobileNumbers: '9770231935',
    message: 'Dear Customer, your service request ID 12345 is registered. - Orpat',
    templateId: '1707168060254867084'
);

$response = SSExpertSms::send($sms);
```

---

### 2. Testing via Artisan Command

Send a test SMS directly from the command line:

```bash
php artisan ssexpert:test-sms 9770231935 --template=1707167402281919826 --otp=123456
```

---

### 3. Managing DLT Templates

#### Listing Templates
```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

// Get all templates as a Collection of TemplateResponse DTOs
$templates = SSExpertTemplate::list();

foreach ($templates as $template) {
    echo $template->templateName;
    echo $template->messageTemplate;
    echo $template->dltTemplateId;
    echo $template->isApproved ? 'Approved' : 'Pending';
}
```

#### Creating a Template
```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;
use Imrjat\SSExpert\DTOs\TemplateData;

$data = new TemplateData(
    templateName: 'LOGIN_OTP_SMS',
    messageTemplate: 'Your Login OTP is {#var#}. Do not share with anyone. - Orpat',
    dltTemplateId: '1707167402281919826'
);

$response = SSExpertTemplate::create($data);
```

#### Finding Templates
```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

// Find by DLT Template ID
$template = SSExpertTemplate::findByDltTemplateId('1707167402281919826');

// Find by internal ID
$template = SSExpertTemplate::findById(15231);

// Find by Name
$template = SSExpertTemplate::findByName('OTP for security');
```

---

## Testing

Run the test suite using PHPUnit:

```bash
./vendor/bin/phpunit packages/laravel-ssexpert/tests
```

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
