<?php

declare(strict_types=1);

namespace App\Types;

use App\Helpers\JsonHelper;
use InvalidArgumentException;

class Json
{
    private function __construct(
        public string $json {
            set {
                $this->json = self::fromString($value)->json;
            }
        }
    ) {
    }

    public static function fromString(string $json): self
    {
        if (JsonHelper::isJson($json)) {
            return new self($json);
        }
        throw new InvalidArgumentException('Invalid JSON string provided.');
    }
}
