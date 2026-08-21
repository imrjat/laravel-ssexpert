<?php

namespace Imrjat\SSExpert\Contracts;

use Illuminate\Support\Collection;
use Imrjat\SSExpert\DTOs\BalanceResponse;

interface BalanceServiceInterface
{
    /**
     * Retrieve all balance / credit records for the account.
     *
     * @return Collection<int, BalanceResponse>
     */
    public function list(): Collection;

    /**
     * Get primary balance record.
     */
    public function get(): ?BalanceResponse;

    /**
     * Get current SMS credits count as a float.
     */
    public function getCredits(): float;
}
