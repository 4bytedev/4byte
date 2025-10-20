<?php

namespace Packages\React;

use Illuminate\Database\Eloquent\Model;

class Helpers
{
    /**
     * Generates cache key from model.
     *
     * @param string|int $parts
     */
    public static function cacheKey(Model $model, ...$parts): string
    {
        $base = strtolower(class_basename($model));

        /* @phpstan-ignore-next-line */
        return "{$base}:{$model->id}:" . implode(':', $parts);
    }
}
