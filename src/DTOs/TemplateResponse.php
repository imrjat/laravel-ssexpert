<?php

namespace Imrjat\SSExpert\DTOs;

use ArrayAccess;
use JsonSerializable;

class TemplateResponse implements JsonSerializable, ArrayAccess
{
    public function __construct(
        public readonly int $templateId,
        public readonly int $companyId,
        public readonly string $templateName,
        public readonly string $messageTemplate,
        public readonly bool $isApproved,
        public readonly bool $isActive,
        public readonly ?string $productName = null,
        public readonly ?string $createDate = null,
        public readonly ?string $createDateString = null,
        public readonly ?string $approvedDate = null,
        public readonly ?string $approvedDateString = null,
        public readonly ?string $dltTemplateId = null,
    ) {}

    /**
     * Factory method from raw API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            templateId: (int) ($data['templateId'] ?? $data['id'] ?? 0),
            companyId: (int) ($data['companyId'] ?? 0),
            templateName: (string) ($data['templateName'] ?? ''),
            messageTemplate: (string) ($data['messageTemplate'] ?? ''),
            isApproved: (bool) ($data['isApproved'] ?? false),
            isActive: (bool) ($data['isActive'] ?? false),
            productName: $data['productName'] ?? null,
            createDate: $data['createDate'] ?? null,
            createDateString: $data['createDateString'] ?? null,
            approvedDate: $data['approvedDate'] ?? null,
            approvedDateString: $data['approvedDateString'] ?? null,
            dltTemplateId: isset($data['dltTemplateId']) ? (string) $data['dltTemplateId'] : null,
        );
    }

    /**
     * Convert to array representation.
     */
    public function toArray(): array
    {
        return [
            'template_id' => $this->templateId,
            'company_id' => $this->companyId,
            'template_name' => $this->templateName,
            'message_template' => $this->messageTemplate,
            'is_approved' => $this->isApproved,
            'is_active' => $this->isActive,
            'product_name' => $this->productName,
            'create_date' => $this->createDate,
            'create_date_string' => $this->createDateString,
            'approved_date' => $this->approvedDate,
            'approved_date_string' => $this->approvedDateString,
            'dlt_template_id' => $this->dltTemplateId,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->toArray()[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // Immutable DTO
    }

    public function offsetUnset(mixed $offset): void
    {
        // Immutable DTO
    }
}
