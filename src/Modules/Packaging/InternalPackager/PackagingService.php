<?php

declare(strict_types=1);

namespace App\Modules\Packaging\InternalPackager;

use App\Model\Box\Box;
use App\Model\Box\BoxRepository;
use App\Model\Product\Product;

final readonly class PackagingService
{
    public function __construct(
        private BoxRepository $boxRepository,
    ) {
    }

    /**
     * @param array<Product> $products
     */
    public function findBox(array $products): ?Box
    {
        $availableBoxes = $this->boxRepository->findAll();

        return $this->boxRepository->find($availableBoxes[0]->getId());
    }
}
