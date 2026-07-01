<?php

declare(strict_types=1);

namespace App\Modules\Packaging\RemotePackager\Exceptions;

use Exception;
use Throwable;

class ApiException extends Exception
{
    private function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, $previous?->getCode() ?? 0, $previous);
    }

    public static function unknown(?string $message = null, ?Throwable $previous = null): self
    {
        return new self($message ?? $previous?->getMessage() ?? 'Unknown error from API', $previous);
    }

    public static function unexpectedFormat(): self
    {
        return new self('Unexpected API response format.');
    }

    public static function missingField(string $fieldName): self
    {
        return new self(sprintf('No `%s` field in API response.', $fieldName));
    }
}
