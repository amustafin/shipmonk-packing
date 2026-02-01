<?php

declare(strict_types=1);

namespace App\Model\Order;

use App\Model\Box\Box;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents order configuration with known required Box.
 * Note: productIdList is a comma-separated list of product IDs.
 * Nullable box means the set of products cannot be packed into any available single box.
 */
#[ORM\Entity]
#[ORM\Table(name: '`order_config`')]
class Order
{
    public function __construct(
        #[ORM\Column(name: 'product_id_list', type: Types::STRING)]
        protected(set) string $productIdList,
        #[ORM\OneToOne(targetEntity: Box::class)]
        #[ORM\JoinColumn(name: 'box_id', referencedColumnName: 'id', nullable: true)]
        protected(set) ?Box $box = null,
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
}
