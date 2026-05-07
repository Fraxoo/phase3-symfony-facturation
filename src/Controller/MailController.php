<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Sensiolabs\GotenbergBundle\Processor\TempfileProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class MailController extends AbstractController
{
    #[Route('/mail/{id}/{mail}', name: 'app_mail')]
    public function index(GotenbergPdfInterface $gotenberg, InvoiceRepository $invoiceRepository, MailerInterface $mailer, int $id, string $mail): Response
    {
        $invoice = $invoiceRepository->getInvoiceWithInvoiceItemsAndClient($id);
        if (!$invoice) {
            throw $this->createNotFoundException();
        }

        $tailwindCssPath = $this->getParameter('kernel.project_dir') . '/var/tailwind/app.built.css';
        $tailwindCss = is_string($tailwindCssPath) && is_file($tailwindCssPath) ? (string) file_get_contents($tailwindCssPath) : '';

        // $gotenbergPdfResult = $gotenberg->url()
        //     ->url('http://localhost:8000/mail/' . $id)
        //     ->generate();

        //     $httpResult = $gotenbergPdfResult->stream();

        $gotenbergPdfResult = $gotenberg->html()
            ->content("mail/index.html.twig", [
                'invoice' => $invoice,
                'tailwindCss' => $tailwindCss,
            ])
            ->processor(new TempfileProcessor())
            ->generate()
            ->process();



        $email = new Email();
        $email->from("SasFacturation@Johnhardy.com")
            ->to($mail)
            ->subject("Ceci est un mail Test sujet")
            ->attach($gotenbergPdfResult, "facture.pdf", "application/pdf")
            ->text("Ceci est un mail Test texte");

        $mailer->send($email);

        return $this->redirectToRoute('app_invoice_show', ['id' => $id]);


        // return $this->render('mail/index.html.twig', [
        //     'invoice' => $invoice,
        //     'tailwindCss' => $tailwindCss,
        // ]);
    }
}
