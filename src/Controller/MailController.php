<?php

namespace App\Controller;

use App\Enum\Status;
use App\Repository\InvoiceRepository;
use App\Repository\UserRepository;
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
            ->attach($gotenbergPdfResult, $invoice->getNumber() . '.pdf', "application/pdf")
            ->text("Ceci est un mail Test texte");

        $mailer->send($email);

        return $this->redirectToRoute('app_invoice_show', ['id' => $id]);


        // return $this->render('mail/index.html.twig', [
        //     'invoice' => $invoice,
        //     'tailwindCss' => $tailwindCss,
        // ]);
    }

    #[Route('/mail/relance', name: 'app_mail_relance')]
    public function Relance(UserRepository $userRepository, GotenbergPdfInterface $gotenberg, InvoiceRepository $invoiceRepository, MailerInterface $mailer): Response
    {
        $users = $userRepository->findAll();

        $tailwindCssPath = $this->getParameter('kernel.project_dir') . '/var/tailwind/app.built.css';
        $tailwindCss = is_string($tailwindCssPath) && is_file($tailwindCssPath) ? (string) file_get_contents($tailwindCssPath) : '';

        $sentCount = 0;

        foreach ($users as $user) {
            foreach ($user->getClients() as $client) {
                foreach ($client->getInvoices() as $invoice) {
                    if ($invoice->getStatus() !== Status::pending_payment) {
                        continue;
                    }

                    $invoiceWithDetails = $invoiceRepository->getInvoiceWithInvoiceItemsAndClient($invoice->getId());
                    if (!$invoiceWithDetails) {
                        continue;
                    }

                    $gotenbergPdfResult = $gotenberg->html()
                        ->content("mail/index.html.twig", [
                            'invoice' => $invoiceWithDetails,
                            'tailwindCss' => $tailwindCss,
                        ])
                        ->processor(new TempfileProcessor())
                        ->generate()
                        ->process();

                    $email = new Email();
                    $email->from("SasFacturation@Johnhardy.com")
                        ->to($invoiceWithDetails->getClientId()?->getEmail() ?? '')
                        ->subject("Ceci est un mail Test sujet")
                        ->attach($gotenbergPdfResult, $invoiceWithDetails->getNumber() . '.pdf', "application/pdf")
                        ->text("Ceci est un mail Test texte");

                    if ($invoiceWithDetails->getClientId()?->getEmail()) {
                        $mailer->send($email);
                        $sentCount++;
                    }
                }
            }
        }

        if ($sentCount > 0) {
            $this->addFlash('success', sprintf('%d relance(s) envoyée(s).', $sentCount));
        } else {
            $this->addFlash('info', 'Aucune facture en attente de paiement à relancer.');
        }

        return $this->redirectToRoute('app_dashboard');
    }




    // return $this->render('mail/index.html.twig', [
    //     'invoice' => $invoice,
    //     'tailwindCss' => $tailwindCss,
    // ]);

}
