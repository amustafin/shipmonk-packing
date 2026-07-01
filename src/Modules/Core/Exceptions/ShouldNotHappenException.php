<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Exception;
use Throwable;

/**
 * This exception represents errors that never should happen in normal circumstances.
 * It is used to indicate that there is a bug in the code and should be fixed by developers.
 */
final class ShouldNotHappenException extends Exception
{
    public function __construct(?string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message ?? $previous?->getMessage() ?? '', $previous?->getCode() ?? $code, $previous);
    }
}
