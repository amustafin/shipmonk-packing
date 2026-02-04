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

        $box = $this->orderRepository->findByProducts(implode(',', $productIds))?->box;
        if ($box !== null) {
            return $box;
        }

        $box =
            // try to find box from api
            $this->remotePackagingService->findBox($products)
            // fallback to any box that can hold the weight
            ?? $this->internalPackagingService->findBox($products);

        $box = $box->maxWeight ?? 0 >= $totalWeight ? $box : null;

        $productIds = array_map(fn (Product $product) => $product->getId(), $products);
        $order = new Order(
            productIdList: implode(',', $productIds),
            box: $box,
        );
        $this->orderRepository->save($order);

        return $order->box;
    }
}
