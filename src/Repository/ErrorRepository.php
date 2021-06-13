<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Vim\ErrorTracking\Entity\Error;

/**
 * @method Error|null find($id, $lockMode = null, $lockVersion = null)
 * @method Error|null findOneBy(array $criteria, array $orderBy = null)
 * @method Error[]    findAll()
 * @method Error[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Error::class);
    }

    public function findOneByHash(string $hash): ?Error
    {
        $results = $this->createQueryBuilder('e')
            ->andWhere('e.hash = :hash')
            ->setParameter('hash', $hash)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        return $results[0] ?? null;
    }
}
