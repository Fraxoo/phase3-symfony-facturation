<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ProductRepository $productRepository, ClientRepository $clientRepository, InvoiceRepository $invoiceRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'totalProduct' => $productRepository->getCountProductByUser($user),
            'totalClient' => $clientRepository->getCountClientByUser($user->getId()),
            'totalPaid' => $invoiceRepository->getTotalPaid(),
            'countPending' => $invoiceRepository->getCountInvoicePendingPayment(),
        ]);
    }
}
