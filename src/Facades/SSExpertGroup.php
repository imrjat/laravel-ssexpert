<?php

namespace Imrjat\SSExpert\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Imrjat\SSExpert\Contracts\GroupServiceInterface;
use Imrjat\SSExpert\DTOs\GroupResponse;

/**
 * @method static Collection<int, GroupResponse> list()
 * @method static array create(string $groupName)
 * @method static array update(int $id, string $groupName)
 * @method static array delete(int $id)
 *
 * @see \Imrjat\SSExpert\Services\SSExpertGroupService
 */
class SSExpertGroup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GroupServiceInterface::class;
    }
}
