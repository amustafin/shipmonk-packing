<?php

declare(strict_types=1);

namespace App\Model\Product;

final readonly class ProductDTO
{
    public function __construct(
       public float $width,
       public float $height,
       public float $length,
       public float $weight,
    ) {
    }
}
