<?php

namespace App\Repository;

use App\Entity\NFTImportItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NFTImportItem>
 *
 * @method NFTImportItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method NFTImportItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method NFTImportItem[]    findAll()
 * @method NFTImportItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NFTImportItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NFTImportItem::class);
    }

    //    /**
    //     * @return NFTImportItem[] Returns an array of NFTImportItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?NFTImportItem
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
