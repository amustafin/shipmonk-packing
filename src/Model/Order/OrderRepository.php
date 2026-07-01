<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\ORM\AbstractBaseEntityRepository;

/**
 * @extends AbstractBaseEntityRepository<Order, int>
 */
final readonly class OrderRepository extends AbstractBaseEntityRepository
{
    public function getEntityClass(): string
    {
        return Order::class;
    }

    public function findByProducts(string $productIds): ?Order
    {
        return $this->findOneBy([
            'productIdList' => $productIds,
        ]);
    }
}
