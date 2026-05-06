<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\Product;
use App\Entity\User;
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
    #[Route(name: 'app_invoice_index', methods: ['GET' , 'POST'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository): Response
    {
        $filter = $request->query->get('filter');
        $trueFilter = $filter === 'draft' ? Status::draft : ($filter === 'pending_payment' ? Status::pending_payment : ($filter === 'paid' ? Status::paid : null));

        $user = $this->getUser();
        $userId = $user->getId();

        return $this->render('invoice/index.html.twig', [
            'invoices' => $invoiceRepository->getAllInvoiceWithClientByUser($userId, $trueFilter),
        ]);
    }

    #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(InvoiceRepository $invoiceRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $invoice = new Invoice();
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $invoice->setUserId($user);


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
            $invoiceDate = $invoice->getCreatedAt() ?? new \DateTime('now');
            $count = $invoiceRepository->countInvoicesForUserCurrentMonth($user, $invoiceDate);
            $number = 'FACT-' . $invoiceDate->format('Y-m-d') . '-' . ($count + 1);
            $invoice->setNumber($number);

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
    public function show(InvoiceRepository $invoiceRepository, Invoice $invoice): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoiceRepository->getInvoiceWithInvoiceItemsAndClient($invoice->getId())
        ]);
    }

    #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {

        if ($invoice->getStatus() !== Status::draft) {
            throw $this->createAccessDeniedException();
        }

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
        if ($invoice->getStatus() !== Status::draft) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $invoice->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}
