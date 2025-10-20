<?php

namespace Packages\Recommend\Classes;

use JsonSerializable;

/**
 * Represents a user feedback entry to be sent to the Gorse recommendation system.
 */
class GorseFeedback implements JsonSerializable
{
    /**
     * Creates a new GorseFeedback instance.
     */
    public function __construct(
        private readonly string $feedbackType,
        private readonly string $userId,
        private readonly string $itemId,
        private readonly string $comment,
        private readonly string $timestamp
    ) {
    }

    /**
     * Returns the feedback type.
     */
    public function getFeedbackType(): string
    {
        return $this->feedbackType;
    }

    /**
     * Returns the user ID who provided the feedback.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Returns the item ID that the feedback refers to.
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }

    /**
     * Returns the comment text of the feedback.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Returns the timestamp of when the feedback was created.
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * Converts the feedback data into a JSON-serializable array.
     *
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'FeedbackType' => $this->feedbackType,
            'UserId'       => $this->userId,
            'ItemId'       => $this->itemId,
            'Comment'      => $this->comment,
            'Timestamp'    => $this->timestamp,
        ];
    }
}
