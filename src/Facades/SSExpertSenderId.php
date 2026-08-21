<?php

namespace Imrjat\SSExpert\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Imrjat\SSExpert\Contracts\SenderIdServiceInterface;
use Imrjat\SSExpert\DTOs\SenderIdResponse;

/**
 * @method static Collection<int, SenderIdResponse> list()
 * @method static SenderIdResponse|null findByName(string $senderId)
 * @method static array create(string $senderId, string $purpose, ?int $productId = null)
 * @method static array delete(int $id)
 *
 * @see \Imrjat\SSExpert\Services\SSExpertSenderIdService
 */
class SSExpertSenderId extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SenderIdServiceInterface::class;
    }
}
