<?php

namespace App\Controller;

use App\Service\CustomerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    #[Route('/api/customer', name: 'app_list_customers')]
    public function list(Request $request, CustomerService $customerService): JsonResponse
    {

        $limit = (int) $request->get('limit', 100);
        $offset = (int) $request->get('offset', 0);

        $customers = $customerService->getCustomers($limit, $offset);

        $data = [];

        foreach ($customers as $customer) {

            $data[] = [
                'company' => $customer->getCompany(),
                'website' => $customer->getWebsite(),
                'customer_number' => $customer->getCustomerNumber(),
                'name' => $customer->getName(),
                'address1' => $customer->getAddress1(),
                'address2' => $customer->getAddress2(),
                'city' => $customer->getCity(),
                'state' => $customer->getState(),
                'postal_code' => $customer->getPostalCode(),
                'country_code' => $customer->getCountryCode(),
                'phone' => $customer->getPhone(),
                'cell_phone' => $customer->getCellPhone(),
                'fax' => $customer->getFax(),
                'email' => $customer->getEmail(),
                'attention' => $customer->getAttention(),
                'credit_manager' => $customer->getCreditManager(),
                'date_opened' => $customer->getDateOpened(),
                'active' => $customer->getActive(),
                'discount_rate' => $customer->getDiscountRate(),
                'credit_limit' => $customer->getCreditLimit()
            ];

        }

        return $this->json($data);
    }

    #[Route('/api/customer/{customerNumber}', name: 'app_get_customer')]
    public function get(string $customerNumber, Request $request, CustomerService $customerService): JsonResponse
    {
        $customer = $customerService->getCustomer($customerNumber);

        $data = [
            'company' => $customer->getCompany(),
            'website' => $customer->getWebsite(),
            'customer_number' => $customer->getCustomerNumber(),
            'name' => $customer->getName(),
            'address1' => $customer->getAddress1(),
            'address2' => $customer->getAddress2(),
            'city' => $customer->getCity(),
            'state' => $customer->getState(),
            'postal_code' => $customer->getPostalCode(),
            'country_code' => $customer->getCountryCode(),
            'phone' => $customer->getPhone(),
            'cell_phone' => $customer->getCellPhone(),
            'fax' => $customer->getFax(),
            'email' => $customer->getEmail(),
            'attention' => $customer->getAttention(),
            'credit_manager' => $customer->getCreditManager(),
            'date_opened' => $customer->getDateOpened(),
            'active' => $customer->getActive(),
            'discount_rate' => $customer->getDiscountRate(),
            'credit_limit' => $customer->getCreditLimit()
        ];

        return $this->json($data);
    }
}
