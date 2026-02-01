<?php

declare(strict_types=1);

namespace App\Model\Product;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a product box that needs to be packed.
 */
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'unique_product_dimensions', columns: ['width', 'height', 'length', 'weight'])]
class Product
{
    public function __construct(
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $width,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $height,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $length,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $weight,
        #[ORM\Id]
        #[ORM\Column(type: Types::INTEGER)]
        #[ORM\GeneratedValue]
        protected(set) ?int $id = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id
            ?? throw new \Exception('Entity not persisted yet, ID is null.');
    }

    public static function fromDTO(ProductDto $dto): self
    {
        return new self(
            width: $dto->width,
            height: $dto->height,
            length: $dto->length,
            weight: $dto->weight,
        );
    }
}
