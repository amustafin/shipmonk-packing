<?php

declare(strict_types=1);

namespace App\Helpers;

use SensitiveParameter;

final readonly class BinPackagingConfig
{
    public function __construct(
        public string $baseUrl,
        #[SensitiveParameter]
        public string $user,
        #[SensitiveParameter]
        public string $apiKey,
    ) {
    }
}
