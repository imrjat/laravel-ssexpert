# Laravel SSExpert (`imrjat/laravel-ssexpert`)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![Total Downloads](https://img.shields.io/packagist/dt/imrjat/laravel-ssexpert.svg?style=flat-square)](https://packagist.org/packages/imrjat/laravel-ssexpert)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

A modern, fluent, and strongly-typed Laravel client for the **SSExpertSystem SMS & Template Gateway API** ([http://api.ssexpertsystem.com](http://api.ssexpertsystem.com)).

---

## Features

- 📑 **Full Template CRUD**: Manage SMS DLT templates (`list`, `create`, `update`, `delete`, `findById`, `findByDltTemplateId`, `findByName`).
- 🛡️ **Strongly Typed DTOs**: Clean `TemplateData`, `TemplateResponse`, and `TemplateApiResponse` models.
- ⚡ **Resilient HTTP Client**: Built-in timeout management and automated exponential retry via Laravel's HTTP Client.
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
SSEXPERT_TIMEOUT=15
SSEXPERT_RETRY_TIMES=3
SSEXPERT_RETRY_SLEEP=100
```

---

## Usage

### 1. Listing Templates

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

### 2. Creating a Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;
use Imrjat\SSExpert\DTOs\TemplateData;

$data = new TemplateData(
    templateName: 'LOGIN_OTP_SMS',
    messageTemplate: 'Your Login OTP is {#var#}. Do not share with anyone.',
    dltTemplateId: '1707168060263570881'
);

$response = SSExpertTemplate::create($data);

if ($response->isSuccess()) {
    echo "Template created: " . $response->data;
} else {
    echo "Error: " . $response->getErrorMessage();
}
```

You can also pass a plain array:

```php
$response = SSExpertTemplate::create([
    'template_name' => 'SERVICE_UPDATE',
    'message_template' => 'Dear {#var#}, your service request #{#var#} is updated.',
    'dlt_template_id' => '1707176588486445859'
]);
```

### 3. Updating a Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;
use Imrjat\SSExpert\DTOs\TemplateData;

$response = SSExpertTemplate::update(12345, [
    'template_name' => 'LOGIN_OTP_SMS_V2',
    'message_template' => 'Your new Login OTP is {#var#}.',
    'dlt_template_id' => '1707168060263570881'
]);
```

### 4. Deleting a Template

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

$response = SSExpertTemplate::delete(12345);
```

### 5. Finding Templates by Identifier

```php
use Imrjat\SSExpert\Facades\SSExpertTemplate;

// Find by internal SSExpert ID
$template = SSExpertTemplate::findById(12345);

// Find by DLT Template ID
$template = SSExpertTemplate::findByDltTemplateId('1707168060263570881');

// Find by Template Name
$template = SSExpertTemplate::findByName('LOGIN_OTP_SMS');
```

### 6. Dependency Injection

You can inject `Imrjat\SSExpert\Contracts\TemplateServiceInterface` into controllers, commands, or services:

```php
namespace App\Http\Controllers;

use Imrjat\SSExpert\Contracts\TemplateServiceInterface;

class NotificationController extends Controller
{
    public function index(TemplateServiceInterface $templateService)
    {
        $templates = $templateService->list();

        return view('notifications.templates', compact('templates'));
    }
}
```

---

## Testing

Run the test suite using PHPUnit:

```bash
./vendor/bin/phpunit packages/laravel-ssexpert/tests
```

---

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
