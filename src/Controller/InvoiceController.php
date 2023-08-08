<?php

namespace App\Controller;

use App\Service\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/api/invoice', name: 'app_list_invoices')]
    public function list(Request $request, InvoiceService $invoiceService): JsonResponse
    {

        $limit = (int) $request->get('limit', 100);
        $offset = (int) $request->get('offset', 0);

        $invoices = $invoiceService->getInvoices($limit, $offset);

        $data = [];

        foreach ($invoices as $invoice) {

            $data[] = [
                'website' => $invoice->getWebsite(),
                'open' => $invoice->getOpen(),
                'record_type' => $invoice->getRecordType(),
                'company' => $invoice->getCompany(),
                'record_sequence' => $invoice->getRecordSequence(),
                'order_number' => $invoice->getOrderNumber(),
                'customer_number' => $invoice->getCustomerNumber(),
                'invoice_date' => $invoice->getInvoiceDate(),
                'subtotal' => $invoice->getSubtotal(),
                'shipping_cost' => $invoice->getShippingCost()
            ];

        }

        return $this->json($data);
    }

    #[Route('/api/invoice/{orderNumber}', name: 'app_get_invoice')]
    public function get(string $orderNumber, Request $request, InvoiceService $invoiceService): JsonResponse
    {
        $invoice = $invoiceService->getInvoice($orderNumber);

        $data = [
            'website' => $invoice->getWebsite(),
            'open' => $invoice->getOpen(),
            'record_type' => $invoice->getRecordType(),
            'company' => $invoice->getCompany(),
            'record_sequence' => $invoice->getRecordSequence(),
            'order_number' => $invoice->getOrderNumber(),
            'customer_number' => $invoice->getCustomerNumber(),
            'invoice_date' => $invoice->getInvoiceDate(),
            'subtotal' => $invoice->getSubtotal(),
            'shipping_cost' => $invoice->getShippingCost()
        ];

        return $this->json($data);
    }
}
