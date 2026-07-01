<?php

declare(strict_types=1);

namespace App\Model\ORM;

use App\Model\ORM\Exceptions\EntityNotFoundException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;

/**
 * @template TEntity of object
 * @template TId
 */
abstract readonly class AbstractBaseEntityRepository
{
    public function __construct(
        protected EntityManager $em
    ) {
    }

    /**
     * @return class-string<TEntity>
     */
    abstract public function getEntityClass(): string;

    /**
     * @param TId $id
     * @return TEntity|null
     */
    public function find($id): ?object
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param TId $id
     * @return TEntity
     * @throws EntityNotFoundException
     */
    public function get($id): object
    {
        $result = $this->find($id);
        if ($result === null) {
            throw new EntityNotFoundException();
        }
        return $result;
    }

    /**
     * @param TEntity $entity
     * @throws ORMException
     */
    public function save(object $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * @param array<string, mixed> $criteria
     * @return TEntity|null
     */
    public function findOneBy(array $criteria): ?object
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * @return TEntity[]
     */
    public function findAll(): array
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @return EntityRepository<TEntity>
     */
    private function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->getEntityClass());
    }
}
