<?php

namespace App\Twig\Components;

use App\Entity\Invoice;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class InvoicePdf
{

public Invoice $invoice;

}
