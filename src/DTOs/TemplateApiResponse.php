<?php

namespace Imrjat\SSExpert\DTOs;

use ArrayAccess;
use JsonSerializable;

class TemplateApiResponse implements JsonSerializable, ArrayAccess
{
    public function __construct(
        public readonly int $errorCode,
        public readonly ?string $errorDescription = null,
        public readonly mixed $data = null,
        public readonly array $raw = [],
    ) {}

    /**
     * Determine if the response indicates success.
     */
    public function isSuccess(): bool
    {
        return $this->errorCode === 0;
    }

    /**
     * Get error message if any.
     */
    public function getErrorMessage(): ?string
    {
        return $this->isSuccess() ? null : ($this->errorDescription ?? 'Unknown error occurred.');
    }

    /**
     * Static factory from API array response.
     */
    public static function fromArray(array $response): self
    {
        return new self(
            errorCode: (int) ($response['errorCode'] ?? 0),
            errorDescription: $response['errorDescription'] ?? null,
            data: $response['data'] ?? null,
            raw: $response,
        );
    }

    public function toArray(): array
    {
        return [
            'error_code' => $this->errorCode,
            'error_description' => $this->errorDescription,
            'data' => $this->data,
            'success' => $this->isSuccess(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->raw[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->raw[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}
