<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\User;
use App\Enum\Status;
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

    public function getTotalPaidByMonth(User $user, ?\DateTimeInterface $forDate = null): float
    {
        $forDate ??= new \DateTimeImmutable('now');

        $start = new \DateTimeImmutable($forDate->format('Y-m-01'));
        $next = $start->modify('+1 month');

        return (float) $this->createQueryBuilder('i')
            ->select('SUM(i.total_ttc)')
            ->andWhere('i.user_id = :user')
            ->andWhere('i.status = :status')
            ->andWhere('i.created_at >= :start')
            ->andWhere('i.created_at < :next')
            ->setParameter('user', $user)
            ->setParameter('status', Status::paid)
            ->setParameter('start', $start, Types::DATE_IMMUTABLE)
            ->setParameter('next', $next, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();
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

    public function getAllInvoiceWithClientByUser(int $userId, ?Status $filter = null): array
    {;

        $query = $this->createQueryBuilder('i')
            ->select('i', 'c')
            ->join('i.client_id', 'c')
            ->where('i.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('i.created_at', 'DESC');

        if ($filter) {
            $query->andWhere('i.status = :status')
                ->setParameter('status', $filter);
        }

        return $query->getQuery()->getResult();
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

    public function getInvoiceWithInvoiceItemsAndClient(int $invoiceId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.id = :invoiceId')
            ->setParameter('invoiceId', $invoiceId)
            ->join('i.client_id', 'c')
            ->join('i.invoiceItems', 'ii')
            ->join('ii.product_id', 'p')
            ->addSelect('c', 'ii', 'p')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTotalPaid(){
        return $this->createQueryBuilder('i')
            ->select('SUM(i.total_ttc) as total_paid')
            ->andWhere('i.status = :status')
            ->setParameter('status', Status::paid)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getCountInvoicePendingPayment(){
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id) as count_pending')
            ->andWhere('i.status = :status')
            ->setParameter('status', Status::pending_payment)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
