<?php

namespace App\Controller;

use App\Service\TrackingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class TrackingController extends AbstractController
{
    #[Route('/api/tracking/{orderNumber}', name: 'app_get_tracking')]
    public function list(string $orderNumber, Request $request, TrackingService $trackingService): JsonResponse
    {

        $tracking = $trackingService->getTracking($orderNumber);

        $data[] = [
            'manifest_id' => $tracking->getManifestId(),
            'company' => $tracking->getCompany(),
            'record_type' => $tracking->getRecordType(),
            'record_sequence' => $tracking->getRecordSequence(),
            'open' => $tracking->getOpen(),
            'customer_number' => $tracking->getCustomerNumber(),
            'packed_on' => $tracking->getPackedOn(),
            'weborder_number' => $tracking->getWeborderNumber(),
            'order_number' => $tracking->getOrderNumber(),
            'tracking_number' => $tracking->getTrackingNumber(),
            'service_type' => $tracking->getServiceType(),
            'shipping_cost' => $tracking->getShippingCost()
        ];

        return $this->json($data);
    }

}