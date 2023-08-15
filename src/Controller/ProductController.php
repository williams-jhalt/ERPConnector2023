<?php

namespace App\Controller;

use App\Service\ProductService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_list_products')]
    public function list(Request $request, ProductService $productService): JsonResponse
    {

        $limit = (int) $request->get('limit', 100);
        $offset = (int) $request->get('offset', 0);
        
        if (($since = $request->get('since', null)) !== null) {

            $date = DateTimeImmutable::createFromFormat('m-d-Y', $since);

            $products = $productService->getNewProducts($date, $limit, $offset);

        } else {

            $products = $productService->getProducts($limit, $offset);

        }

        $data = [];

        foreach ($products as $product) {

            $data[] = [
                'company' => $product->getCompany(),
                'website' => $product->getWebsite(),
                'item_number' => $product->getItemNumber(),
                'name' => $product->getName(),
                'type_code' => $product->getTypeCode(),
                'manufacturer_code' => $product->getManufacturerCode(),
                'wholesale_price' => $product->getWholesalePrice(),
                'on_hand_quantity' => $product->getOnHandQuantity(),
                'active' => $product->getActive(),
                'date_created' => $product->getDateCreated(),
                'release_date' => $product->getReleaseDate(),
                'upc' => $product->getUpc(),
                'max_discount_rate' => $product->getMaxDiscountRate(),
                'saleable' => $product->getSaleable()
            ];

        }

        return $this->json($data);
    }

    #[Route('/api/product/{itemNumber}', name: 'app_get_product')]
    public function get(string $itemNumber, Request $request, ProductService $productService): JsonResponse
    {
        $product = $productService->getProduct($itemNumber);

        $data = [
            'company' => $product->getCompany(),
            'website' => $product->getWebsite(),
            'item_number' => $product->getItemNumber(),
            'name' => $product->getName(),
            'type_code' => $product->getTypeCode(),
            'manufacturer_code' => $product->getManufacturerCode(),
            'wholesale_price' => $product->getWholesalePrice(),
            'on_hand_quantity' => $product->getOnHandQuantity(),
            'active' => $product->getActive(),
            'date_created' => $product->getDateCreated(),
            'release_date' => $product->getReleaseDate(),
            'upc' => $product->getUpc(),
            'max_discount_rate' => $product->getMaxDiscountRate(),
            'saleable' => $product->getSaleable()
        ];

        return $this->json($data);
    }
}