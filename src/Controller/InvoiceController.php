<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Product;
use App\Enum\Status;
use App\Form\InvoiceType;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/invoice')]
final class InvoiceController extends AbstractController
{
    #[Route(name: 'app_invoice_index', methods: ['GET'])]
    public function index(InvoiceRepository $invoiceRepository): Response
    {
    $user = $this->getUser();
    $userId = $user->getId();

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->getAllInvoiceWithClientByUser($userId),
        ]);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(InvoiceRepository $invoiceRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();
        $invoice->setUserId($this->getUser());


        $products = $entityManager->getRepository(Product::class)->findAll();
        $productData = array_values(array_filter(array_map(static function (Product $product): ?array {
            if (null === $product->getId()) {
                return null;
            }

            return [
                'id' => $product->getId(),
                'name' => (string) $product->getName(),
                'price' => (string) $product->getPrice(),
            ];
        }, $products)));

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $numberByMonth = $invoiceRepository->getNumberInvoiceByMonth((int) date('m')) + 1;
            $createdAt = "FACT-" . date('Y-m-d') . "-" . $numberByMonth;
            $invoice->setNumber($createdAt);

            $form->get('saveDump')->isClicked() ? $invoice->setStatus(Status::draft) : $invoice->setStatus(Status::pending_payment);


            $entityManager->persist($invoice);
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'productData' => $productData,
        ]);
    }



    #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();
        $productData = array_values(array_filter(array_map(static function (Product $product): ?array {
            if (null === $product->getId()) {
                return null;
            }

            return [
                'id' => $product->getId(),
                'name' => (string) $product->getName(),
                'price' => (string) $product->getPrice(),
            ];
        }, $products)));

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
            'productData' => $productData,
        ]);
    }

    #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
    public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
