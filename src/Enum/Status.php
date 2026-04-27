<?php

namespace App\Enum;

enum Status: string
{
    case draft = "Brouillon";
    case pending_payment = "En attente de paiemnt";
    case paid = "Payée";
}