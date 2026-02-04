<?php

declare(strict_types=1);

namespace App\Modules\Packaging;

use App\Model\Box\Box;
use App\Model\Order\Order;
use App\Model\Order\OrderRepository;
use App\Model\Product\Product;
use App\Modules\Packaging\InternalPackager\PackagingService as InternalPackagingService;
use App\Modules\Packaging\RemotePackager\PackagingService as RemotePackagingService;

final readonly class PackagingFacade
{
    public function __construct(
        private OrderRepository $orderRepository,
        private RemotePackagingService $remotePackagingService,
        private InternalPackagingService $internalPackagingService,
    ) {
    }

    /**
     * @param array<Product> $products
     */
    public function findBoxForProducts(array $products): ?Box
    {
        $productIds = array_map(fn (Product $product) => $product->getId(), $products);
        $totalWeight = array_reduce($products, fn (float $carry, Product $product) => $carry + $product->weight, 0);
        $box =
            // Try to find a known configuration in db
            $this->orderRepository->findByProducts(implode(',', $productIds))->box
            // try to find box from api
            ?? $this->remotePackagingService->findBox($products)
            // fallback to any box that can hold the weight
            ?? $this->internalPackagingService->findBox($products);

        // if now box is null, products cannot be packed into any available single box

        $productIds = array_map(fn (Product $product) => $product->getId(), $products);
        $order = new Order(
            productIdList: implode(',', $productIds),
            box: $box,
        );
        $this->orderRepository->save($order);

        return $box->maxWeight ?? 0 >= $totalWeight ? $box : null;
    }
}
