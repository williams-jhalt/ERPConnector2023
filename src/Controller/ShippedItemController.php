<?php

namespace App\Controller;

use App\Service\ShippedItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShippedItemController extends AbstractController
{
    #[Route('/api/shipped-items/{orderNumber}', name: 'app_get_shipped_items')]
    public function list(string $orderNumber, Request $request, ShippedItemService $shippedItemService): JsonResponse
    {

        $shippedItems = $shippedItemService->getShippedItems($orderNumber);

        $data = [];

        foreach ($shippedItems as $shippedItem) {

            $data[] = [
                'manifest_id' => $shippedItem->getManifestId(),
                'company' => $shippedItem->getCompany(),
                'record_type' => $shippedItem->getRecordType(),
                'record_sequence' => $shippedItem->getRecordSequence(),
                'open' => $shippedItem->getOpen(),
                'customer_number' => $shippedItem->getCustomerNumber(),
                'packed_on' => $shippedItem->getPackedOn(),
                'weborder_number' => $shippedItem->getWeborderNumber(),
                'item_number' => $shippedItem->getItemNumber(),
                'quantity_committed' => $shippedItem->getQuantityCommitted(),
                'order_number' => $shippedItem->getOrderNumber()
            ];

        }

        return $this->json($data);
    }

}
