<?php

namespace App\Repository;

use App\Entity\RpcProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RpcProvider>
 *
 * @method RpcProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method RpcProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method RpcProvider[]    findAll()
 * @method RpcProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RpcProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RpcProvider::class);
    }

    //    /**
    //     * @return RpcProvider[] Returns an array of RpcProvider objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RpcProvider
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
