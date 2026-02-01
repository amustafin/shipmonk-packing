<?php

declare(strict_types=1);

namespace App\Model\Box;

use App\Model\ORM\AbstractBaseEntityRepository;

/**
 * @extends AbstractBaseEntityRepository<Box, int>
 */
final readonly class BoxRepository extends AbstractBaseEntityRepository
{
    public function getEntityClass(): string
    {
        return Box::class;
    }
}
