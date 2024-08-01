<?php

namespace App\Repository;

use App\Entity\AccountWallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountWallet>
 *
 * @method AccountWallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountWallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountWallet[]    findAll()
 * @method AccountWallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountWalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountWallet::class);
    }

    //    /**
    //     * @return AccountWallet[] Returns an array of AccountWallet objects
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

    //    public function findOneBySomeField($value): ?AccountWallet
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
