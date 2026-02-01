<?php

declare(strict_types=1);

namespace App\Model\Product;

use App\Helpers\Validator;
use InvalidArgumentException;

final readonly class ProductAssembler
{
    /**
     * @param array<int|string, mixed> $data
     * @return array<ProductDTO>
     */
    public function createProductDtoList(array $data): array
    {
        $rowProducts = $data['products'] ?? [];
        if (!is_array($rowProducts)) {
            throw new InvalidArgumentException('Products data must be an array.');
        }

        $productDTOs = [];
        foreach ($rowProducts as $data) {
            $productDTOs[] = $this->createProductDTO($data);
        }
        return $productDTOs;
    }

    /**
     * @array<int|string, mixed> $data
     */
    private function createProductDTO(array $data): ProductDTO
    {
        return new ProductDTO(
            width: Validator::validateFloat('Width', $data['width'] ?? null),
            height: Validator::validateFloat('Height', $data['height'] ?? null),
            length: Validator::validateFloat('Length', $data['length'] ?? null),
            weight: Validator::validateFloat('Weight', $data['weight'] ?? null),
        );
    }
}
