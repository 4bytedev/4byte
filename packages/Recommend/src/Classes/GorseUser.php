<?php

namespace Packages\Recommend\Classes;

use JsonSerializable;

class GorseUser implements JsonSerializable
{
    public function __construct(
        private readonly string $userId,
        private array $labels,
        private readonly array $subscribe,
        private readonly string $comment
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function getSubscribe(): array
    {
        return $this->subscribe;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function addLabel(string $label): void
    {
        $this->labels[] = $label;
    }

    public function jsonSerialize(): array
    {
        return [
            'UserId' => $this->userId,
            'Labels' => $this->labels,
            'Subscribe' => $this->subscribe,
            'Comment' => $this->comment,
        ];
    }

    public static function fromJSON(array $json): self
    {
        return new self(
            $json['UserId'],
            $json['Labels'],
            $json['Subscribe'],
            $json['Comment']
        );
    }
}
