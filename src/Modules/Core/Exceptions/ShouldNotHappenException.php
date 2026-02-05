<?php

declare(strict_types=1);

namespace App\Modules\Core\Exceptions;

use Exception;

/**
 * This exception represents errors that never should happen in normal circumstances.
 * It is used to indicate that there is a bug in the code and should be fixed by developers.
 */
final class ShouldNotHappenException extends Exception
{
}
