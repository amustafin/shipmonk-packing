<?php

declare(strict_types=1);

namespace App\Modules\Packaging\InternalPackager;

use App\Model\Box\Box;
use App\Model\Box\BoxRepository;
use App\Model\Product\Product;
use Latuconsinafr\BinPackager\BinPackager3D\Bin;
use Latuconsinafr\BinPackager\BinPackager3D\Item;
use Latuconsinafr\BinPackager\BinPackager3D\Packager;

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
        $bins = $this->prepareBins($availableBoxes);
        $items = $this->prepareItems($products);

        foreach ($bins as $key => $bin) {
            if ($this->isBinSuitable($items, $bin)) {
                return $this->boxRepository->find($key);
            }
        }

        // no suitable box found
        return null;
    }

    /**
     * @param array<Box> $boxes
     * @return array<Bin>
     */
    private function prepareBins(array $boxes): array
    {
        $bins = [];
        foreach ($boxes as $box) {
            $bins[$box->getId()] = new Bin(
                id: $box->getId(),
                length: $box->length,
                height: $box->height,
                breadth: $box->width,
                weight: $box->maxWeight,
            );
        }
        return $bins;
    }

    /**
     * @param array<Product> $products
     * @return array<Item>
     */
    private function prepareItems(array $products): array
    {
        $items = [];
        foreach ($products as $product) {
            $items[] = new Item(
                id: $product->getId(),
                length: $product->length,
                height: $product->height,
                breadth: $product->width,
                weight: $product->weight,
            );
        }
        return $items;
    }

    /**
     * @param array<Item> $items
     */
    private function isBinSuitable(array $items, Bin $bin): bool
    {
        $packager = new Packager();
        $packager->addBin($bin);

        foreach ($items as $item) {
            $packager->packItemToBin($bin, $item);
        }

        return $bin->getTotalUnfittedVolume() === 0.0;
    }
}
