<?php

namespace Imrjat\SSExpert\DTOs;

use InvalidArgumentException;

class BulkSmsData
{
    /** @var array<int, BulkMessageItem> */
    public readonly array $messageParameters;

    /**
     * @param  array<int, BulkMessageItem|array>  $messages
     */
    public function __construct(
        array $messages,
        public readonly ?string $templateId = null,
        public readonly ?string $senderId = null,
        public readonly ?string $principleEntityId = null,
        public readonly ?string $scheduleDateTime = null,
        public readonly bool $isUnicode = false,
        public readonly bool $isFlash = false,
        public readonly bool $isRegisteredForDelivery = true,
        public readonly int $dataCoding = 0,
    ) {
        if (empty($messages)) {
            throw new InvalidArgumentException('Bulk messages list cannot be empty.');
        }

        $items = [];
        foreach ($messages as $item) {
            $items[] = is_array($item) ? BulkMessageItem::fromArray($item) : $item;
        }

        $this->messageParameters = $items;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            messages: $data['messages'] ?? $data['messageParameters'] ?? $data['message_parameters'] ?? [],
            templateId: isset($data['templateId']) ? (string) $data['templateId'] : (isset($data['template_id']) ? (string) $data['template_id'] : null),
            senderId: $data['senderId'] ?? $data['sender_id'] ?? null,
            principleEntityId: $data['principleEntityId'] ?? $data['principle_entity_id'] ?? $data['peid'] ?? null,
            scheduleDateTime: $data['scheduleDateTime'] ?? $data['schedule_date_time'] ?? null,
            isUnicode: (bool) ($data['isUnicode'] ?? $data['is_unicode'] ?? false),
            isFlash: (bool) ($data['isFlash'] ?? $data['is_flash'] ?? false),
            isRegisteredForDelivery: (bool) ($data['isRegisteredForDelivery'] ?? $data['is_registered_for_delivery'] ?? true),
            dataCoding: (int) ($data['dataCoding'] ?? $data['data_coding'] ?? 0),
        );
    }

    public function toPayload(string $apiKey, string $clientId, ?string $defaultSenderId = null, ?string $defaultPeid = null): array
    {
        return [
            'senderId' => $this->senderId ?: $defaultSenderId,
            'isUnicode' => $this->isUnicode,
            'isFlash' => $this->isFlash,
            'isRegisteredForDelivery' => $this->isRegisteredForDelivery,
            'validityPeriod' => null,
            'dataCoding' => $this->dataCoding,
            'scheduleDateTime' => $this->scheduleDateTime,
            'principleEntityId' => $this->principleEntityId ?: $defaultPeid,
            'templateId' => $this->templateId,
            'messageParameters' => array_map(fn (BulkMessageItem $item) => $item->toPayload(), $this->messageParameters),
            'apiKey' => $apiKey,
            'clientId' => $clientId,
        ];
    }
}
