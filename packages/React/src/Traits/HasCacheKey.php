<?php

namespace Packages\React\Traits;

trait HasCacheKey
{
    private function getCacheKey(): string
    {
        return strtolower(class_basename($this)).":".$this->id;
    }
}
