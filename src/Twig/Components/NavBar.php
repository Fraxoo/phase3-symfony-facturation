<?php

namespace App\Twig\Components;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NavBar
{
    #[CurrentUser]
    public ?User $user = null;

    public function getInitial(): string
    {
        if (!$this->user) {
            return '';
        }

        $initial = $this->user->getFirstName()[0] ?? '';

        

        return $initial;
    }
}