<?php

namespace Imrjat\SSExpert\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Imrjat\SSExpert\Contracts\BalanceServiceInterface;
use Imrjat\SSExpert\DTOs\BalanceResponse;

/**
 * @method static Collection<int, BalanceResponse> list()
 * @method static BalanceResponse|null get()
 * @method static float getCredits()
 *
 * @see \Imrjat\SSExpert\Services\SSExpertBalanceService
 */
class SSExpertBalance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BalanceServiceInterface::class;
    }
}
