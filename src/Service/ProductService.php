<?php

namespace App\Service;

use App\Model\Product;
use Psr\Log\LoggerInterface;

class ProductService
{

    public function __construct(
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getProducts(int $limit = 100, int $offset = 0): array
    {
        $query = "FOR EACH item NO-LOCK " .
            "WHERE item.company_it = '" . $this->erpCompany . "', " .
            "EACH wa_item NO-LOCK " .
            "WHERE wa_item.company_it = item.company_it " .
            "AND wa_item.item = item.item";

        $fields = 'item.company_it, ' .
            'item.profile,  ' .
            'item.item,  ' .
            'item.descr,  ' .
            'wa_item.list_price,  ' .
            'wa_item.qty_oh,  ' .
            'item.date_added,  ' .
            'item.upc1';

        $response = $this->service->read($query, $fields, $limit, $offset);

        $result = array();

        foreach ($response as $erpItem) {
            $result[] = $this->_buildFromErp($erpItem);
        }

        return $result;

    }

    public function getProduct(string $itemNumber): Product
    {
        $query = "FOR EACH item NO-LOCK " .
            "WHERE item.item = '" . $itemNumber . "' " .
            "AND item.company_it = '" . $this->erpCompany . "', " .
            "EACH wa_item NO-LOCK " .
            "WHERE wa_item.company_it = item.company_it " .
            "AND wa_item.item = item.item";

        $fields = 'item.company_it, ' .
            'item.profile,  ' .
            'item.item,  ' .
            'item.descr,  ' .
            'wa_item.list_price,  ' .
            'wa_item.qty_oh,  ' .
            'item.date_added,  ' .
            'item.upc1';

        $response = $this->service->read($query, $fields, 1);

        return $this->_buildFromErp($response[0]);


    }

    private function _buildFromErp($erpItem)
    {

        $this->logger->info("Building product from: " . print_r($erpItem, true));

        $t = new Product();
        $t->setCompany($erpItem['item_company_it'])
            ->setWebsite($erpItem['item_profile'][0])
            ->setItemNumber($erpItem['item_item'])
            ->setName($erpItem['item_descr'][0])
            ->setTypeCode($erpItem['item_profile'][2])
            ->setManufacturerCode($erpItem['item_profile'][4])
            ->setWholesalePrice($erpItem['wa_item_list_price'])
            ->setOnHandQuantity($erpItem['wa_item_qty_oh'])
            ->setActive($erpItem['item_profile'][1])
            ->setDateCreated($erpItem['item_date_added'])
            ->setUpc($erpItem['item_upc1'])
            ->setMaxDiscountRate($erpItem['item_profile'][3])
            ->setSaleable($erpItem['item_profile'][6]);

        return $t;

    }

}