<?php

namespace App\Twig\Components;

use App\Entity\Invoice;
use App\Enum\Status;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Doctrine\ORM\EntityManagerInterface;


#[AsLiveComponent]
final class InvoiceActionButton
{
    use DefaultActionTrait;

    private EntityManagerInterface $em;

    #[LiveProp(writable: true)]
    public Invoice $invoice;

    function __construct( EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[LiveAction]
    public function setStatusPaid(): void
    {

        $this->invoice->setStatus(Status::paid);
        $this->em->persist($this->invoice);
        $this->em->flush();
    }
}
