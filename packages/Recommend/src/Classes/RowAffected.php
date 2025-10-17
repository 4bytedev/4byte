<?php

namespace Packages\Recommend\Classes;

class RowAffected
{
    public function __construct(
        private readonly int $rowAffected
    ) {
    }

    public static function fromJSON(array $json): self
    {
        return new self($json['RowAffected']);
    }

    public function getRowAffected(): int
    {
        return $this->rowAffected;
    }
}
