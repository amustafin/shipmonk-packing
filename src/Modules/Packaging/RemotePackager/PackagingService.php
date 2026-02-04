<?php

declare(strict_types=1);

namespace App\Modules\Packaging\RemotePackager;

use App\Model\Box\Box;
use App\Model\Box\BoxRepository;
use App\Model\Product\Product;
use App\Modules\Packaging\RemotePackager\API\PackagingApi;

final readonly class PackagingService
{
    public function __construct(
        private BoxRepository $boxRepository,
        private PackagingApi $api,
    ) {
    }

    /**
     * @param array<Product> $products
     */
    public function findBox(array $products): ?Box
    {
        $availableBoxes = $this->boxRepository->findAll();

        $items = array_map(
            fn (Product $product) => [
                'id' => $product->getId(),
                'w' => $product->width,
                'h' => $product->height,
                'd' => $product->length,
                'wg' => $product->weight,
                'q' => 1,
                'vr' => 1,
            ],
            $products
        );

        $bins = array_map(
            fn (Box $box) => [
                'id' => $box->getId(),
                'w' => $box->width,
                'h' => $box->height,
                'd' => $box->length,
                'max_wg' => $box->maxWeight,
            ],
            $availableBoxes
        );

        $boxData = $this->api->callPackIntoMany($items, $bins);

        return $this->boxRepository->find($boxData['id']);
    }
}
