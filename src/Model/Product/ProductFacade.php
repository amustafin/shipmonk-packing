<?php

declare(strict_types=1);

namespace App\Model\Product;

use Doctrine\ORM\Exception\ORMException;

final readonly class ProductFacade
{
    public function __construct(
        private ProductAssembler $assembler,
        private ProductRepository $repository,
    ) {
    }

    /**
     * @return array<Product>
     * @throws ORMException
     */
    public function getOrCreateProductsFromRequest(string $data): array
    {
        $productDtoList = $this->assembler->createProductDtoList(json_decode($data, true));

        $productList = [];
        foreach ($productDtoList as $productDto) {
            $product = $this->repository->findOneByDTO($productDto);
            if ($product === null) {
                $product = Product::fromDTO($productDto);
                $this->repository->save($product);
            }
            $productList[] = $product;
        }
        return $productList;
    }
}
