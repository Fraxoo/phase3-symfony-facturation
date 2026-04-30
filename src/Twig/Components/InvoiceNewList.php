<?php

namespace App\Twig\Components;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class InvoiceNewList
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Invoice $invoice = null;

    #[LiveAction]
    public function getInvoiceItems(): void {


    }
}
