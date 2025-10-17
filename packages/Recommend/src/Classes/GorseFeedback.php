<?php

namespace Packages\Recommend\Classes;

use JsonSerializable;

class GorseFeedback implements JsonSerializable
{
    public function __construct(
        private readonly string $feedbackType,
        private readonly string $userId,
        private readonly string $itemId,
        private readonly string $comment,
        private readonly string $timestamp
    ) {
    }

    public function getFeedbackType(): string
    {
        return $this->feedbackType;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function jsonSerialize(): array
    {
        return [
            'FeedbackType' => $this->feedbackType,
            'UserId' => $this->userId,
            'ItemId' => $this->itemId,
            'Comment' => $this->comment,
            'Timestamp' => $this->timestamp,
        ];
    }
}
