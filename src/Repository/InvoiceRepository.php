<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
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
    {;

        $query = $this->createQueryBuilder('i')
            ->select('i', 'c')
            ->join('i.client_id', 'c')
            ->where('i.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('i.created_at', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    public function countInvoicesForUserCurrentMonth(User $user, ?\DateTimeInterface $forDate = null): int
    {
        $forDate ??= new \DateTimeImmutable('now');

        $start = new \DateTimeImmutable($forDate->format('Y-m-01'));
        $next = $start->modify('+1 month');

        return (int) $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->andWhere('i.user_id = :user')
            ->andWhere('i.created_at >= :start')
            ->andWhere('i.created_at < :next')
            ->setParameter('user', $user)
            ->setParameter('start', $start, Types::DATE_IMMUTABLE)
            ->setParameter('next', $next, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getInvoiceWithInvoiceItemsAndClient(Invoice $invoice)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.id = :invoiceId')
            ->setParameter('invoiceId', $invoice->getId())
            ->join('i.client_id', 'c')
            ->join('i.invoiceItems', 'ii')
            ->join('ii.product_id', 'p')
            ->addSelect('c', 'ii', 'p')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
