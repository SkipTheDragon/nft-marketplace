<?php

namespace App\Repository;

use App\Entity\AccountSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountSession>
 *
 * @method AccountSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountSession[]    findAll()
 * @method AccountSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountSession::class);
    }

    //    /**
    //     * @return AccountSession[] Returns an array of AccountSession objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AccountSession
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
