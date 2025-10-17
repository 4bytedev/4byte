<?php

namespace Packages\Recommend\Classes;

use Carbon\Carbon;
use JsonSerializable;

class GorseItem implements JsonSerializable
{
    public function __construct(
        private readonly string $itemId,
        private readonly array $labels,
        private readonly array $categories,
        private readonly string $comment,
        private readonly bool $isHidden,
        private readonly string $timestamp
    ) {
        $this->timestamp = $timestamp ?? Carbon::now()->toDateTimeString();
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function jsonSerialize(): array
    {
        return [
            'ItemId' => $this->itemId,
            'Labels' => $this->labels,
            'Categories' => $this->categories,
            'Comment' => $this->comment,
            'IsHidden' => $this->isHidden,
            'Timestamp' => $this->timestamp,
        ];
    }

    public static function fromJSON(array $json): self
    {
        return new self(
            $json['ItemId'],
            $json['Labels'],
            $json['Categories'],
            $json['Comment'],
            $json['IsHidden'],
            $json['Timestamp'] ?? Carbon::now()->toDateTimeString()
        );
    }
}
