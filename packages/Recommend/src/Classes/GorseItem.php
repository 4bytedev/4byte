<?php

namespace Packages\Recommend\Classes;

use Carbon\Carbon;
use JsonSerializable;

class GorseItem implements JsonSerializable
{
    /**
     * @param array<int, string> $labels
     * @param array<int, string> $categories
     */
    public function __construct(
        private readonly string $itemId,
        private readonly array $labels,
        private readonly array $categories,
        private readonly string $comment,
        private readonly bool $isHidden,
        private readonly string $timestamp
    ) {
    }

    /**
     * Get the item ID.
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }

    /**
     * Get labels of the item.
     *
     * @return array<int, string>
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Get the categories.
     *
     * @return array<int, string>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Get comment.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Check if item is hidden.
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * Get timestamp.
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Serialize item to array.
     *
     * @return array{
     *     ItemId: string,
     *     Labels: array<int, string>,
     *     Categories: array<int, string>,
     *     Comment: string,
     *     IsHidden: bool,
     *     Timestamp: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'ItemId'     => $this->itemId,
            'Labels'     => $this->labels,
            'Categories' => $this->categories,
            'Comment'    => $this->comment,
            'IsHidden'   => $this->isHidden,
            'Timestamp'  => $this->timestamp,
        ];
    }

    /**
     * Create a GorseItem from a JSON array.
     *
     * @param array{
     *     ItemId: string,
     *     Labels: array<int, string>,
     *     Categories: array<int, string>,
     *     Comment: string,
     *     IsHidden: bool,
     *     Timestamp?: string
     * } $json
     */
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
