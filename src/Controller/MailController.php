<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class MailController extends AbstractController
{
    #[Route('/mail/{id}', name: 'app_mail')]
    public function index( GotenbergPdfInterface $gotenberg,InvoiceRepository $invoiceRepository, MailerInterface $mailer, int $id): Response
    {

        $gotenbergPdfResult = $gotenberg->url()
            ->url('http://localhost:8000/mail/' . $id)
            ->generate();



        $email = new Email();
        $email->from("SasFacturation@Johnhardy.com")
            ->to("massi.chaouchi@gmail.com")
            ->subject("Ceci est un mail Test sujet")
            ->attach($gotenbergPdfResult, "facture.pdf", "application/pdf")
            ->text("Ceci est un mail Test texte");

        $mailer->send($email);


        return $this->render('mail/index.html.twig', [
            'invoice' => $invoiceRepository->getInvoiceWithInvoiceItemsAndClient($id)
        ]);
    }
}
