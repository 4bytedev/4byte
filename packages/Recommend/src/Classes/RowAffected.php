<?php

namespace Packages\Recommend\Classes;

class RowAffected
{
    public function __construct(
        private readonly int $rowAffected
    ) {
    }

    /**
     * Create a RowAffected instance from a JSON array.
     *
     * @param array<string, int> $json Array with key 'RowAffected' containing an integer
     */
    public static function fromJSON(array $json): self
    {
        return new self($json['RowAffected']);
    }

    /**
     * Get the number of affected rows.
     */
    public function getRowAffected(): int
    {
        return $this->rowAffected;
    }
}
