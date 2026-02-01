<?php

namespace App\Model\Box;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a box available in the warehouse.
 *
 * Warehouse workers pack a set of products for a given order into one of these boxes.
 */
#[ORM\Entity]
class Box
{
    public function __construct(
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $width,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $height,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $length,
        #[ORM\Column(type: Types::FLOAT)]
        protected(set) float $maxWeight,
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

    public function toJson(): string
    {
        return json_encode([
            'name' => 'Box ' . $this->getId(),
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'maxWeight' => $this->maxWeight,
        ]) ?? '';
    }
}
