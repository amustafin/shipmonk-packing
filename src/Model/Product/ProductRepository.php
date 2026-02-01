<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Model\ORM\AbstractBaseEntityRepository;

/**
 * @extends AbstractBaseEntityRepository<Product, int>
 */
final readonly class ProductRepository extends AbstractBaseEntityRepository
{
    public function getEntityClass(): string
    {
        return Product::class;
    }

    public function findOneByDTO(ProductDTO $dto): ?Product
    {
        return $this->findOneBy([
            'width' => $dto->width,
            'height' => $dto->height,
            'length' => $dto->length,
            'weight' => $dto->weight,
        ]);
    }
}
