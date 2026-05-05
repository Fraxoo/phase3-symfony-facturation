<?php

namespace App\Repository;

use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Invoice>
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Invoice::class);
    }

    //    /**
    //     * @return Invoice[] Returns an array of Invoice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Invoice
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAllInvoiceWithClientByUser(int $userId): array
    {
        ;

        $query = $this->createQueryBuilder('i')
            ->select('i', 'c')
            ->join('i.client_id', 'c')
            ->where('i.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery();

        return $query->getResult();
    }


    public function getNumberInvoiceByMonth(int $month): int
    {
        $start = (new \DateTime('first day of this month'))->setTime(0, 0);
        $end = (new \DateTime('last day of this month'))->setTime(23, 59, 59);




        $query = $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.created_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}
