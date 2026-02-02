<?php

declare(strict_types=1);

namespace App\Helpers;

use InvalidArgumentException;

final readonly class Validator
{
    public static function validateFloat(string $name, mixed $value): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }

        throw new InvalidArgumentException(sprintf('%s must be a float or int.', $name));
    }
}
