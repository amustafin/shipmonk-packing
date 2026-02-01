<?php

declare(strict_types=1);

namespace App\Facades;

use App\API\PackagingApi;
use App\Model\Order\Order;
use App\Model\Order\OrderRepository;
use App\Model\Box\Box;
use App\Model\Box\BoxRepository;
use App\Model\Product\Product;
use Doctrine\ORM\EntityManager;

final readonly class PackagingFacade
{
    private function __construct(
        private BoxRepository $boxRepository,
        private OrderRepository $orderRepository,
        private PackagingAPI $api,
    ) {
    }

    public static function create(EntityManager $em): self
    {
        return new self(
            new BoxRepository($em),
            new OrderRepository($em),
            PackagingAPI::create(),
        );
    }

    /**
     * @param array<Product> $products
     */
    public function findBoxForProducts(array $products): ?Box
    {
        $productIds = array_map(fn(Product $product) => $product->getId(), $products);
        $totalWeight = array_reduce($products, fn(int $carry, Product $product) => $carry + $product->weight, 0);
        $box = $this->orderRepository->findByProducts(implode(',', $productIds))->box
            ?? $this->findBoxFromApi($products);

        return $box?->maxWeight ?? 0 >= $totalWeight ? $box : null;
    }

    /**
     * @param array<Product> $products
     */
    private function findBoxFromApi(array $products): ?Box
    {
        $availableBoxes = $this->boxRepository->findAll();

        $items = array_map(
            fn(Product $product) => [
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
            fn(Box $box) => [
                'id' => $box->getId(),
                'w' => $box->width,
                'h' => $box->height,
                'd' => $box->length,
            ],
            $availableBoxes
        );

        $boxData = $this->api->callPackIntoMany($items, $bins);

        $productIds = array_map(fn(Product $product) => $product->getId(), $products);
        $order = new Order(
            productIdList: implode(',', $productIds),
            box: $this->boxRepository->find($boxData['id']),
        );
        $this->orderRepository->save($order);

        return $order->box;
    }
}
