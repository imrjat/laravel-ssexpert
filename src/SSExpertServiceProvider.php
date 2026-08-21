<?php

namespace Imrjat\SSExpert;

use Illuminate\Support\ServiceProvider;
use Imrjat\SSExpert\Commands\SendTestSmsCommand;
use Imrjat\SSExpert\Contracts\SmsServiceInterface;
use Imrjat\SSExpert\Contracts\TemplateServiceInterface;
use Imrjat\SSExpert\Services\SSExpertSmsService;
use Imrjat\SSExpert\Services\SSExpertTemplateService;

class SSExpertServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ssexpert.php', 'ssexpert');

        $this->app->singleton(TemplateServiceInterface::class, function ($app) {
            return new SSExpertTemplateService($app['config']['ssexpert'] ?? []);
        });

        $this->app->singleton(SmsServiceInterface::class, function ($app) {
            return new SSExpertSmsService($app['config']['ssexpert'] ?? []);
        });

        $this->app->alias(TemplateServiceInterface::class, 'ssexpert.template');
        $this->app->alias(TemplateServiceInterface::class, SSExpertTemplateService::class);

        $this->app->alias(SmsServiceInterface::class, 'ssexpert.sms');
        $this->app->alias(SmsServiceInterface::class, SSExpertSmsService::class);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ssexpert.php' => config_path('ssexpert.php'),
            ], 'ssexpert-config');

            $this->commands([
                SendTestSmsCommand::class,
            ]);
        }
    }
}
