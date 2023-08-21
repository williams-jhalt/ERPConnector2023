<?php

namespace App\Service;

use App\Model\Product;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ProductService
{

    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getNewProducts(DateTimeInterface $since, int $limit = 100, int $offset = 0): array
    {

        $datestr = $since->format('m/d/Y');

        $cacheId = md5("ProductService:getNewProducts:$datestr:$limit:$offset");

        return $this->cache->get($cacheId, function(ItemInterface $item) use($datestr, $limit, $offset) { 

            $item->expiresAfter(3600);

            $query = "FOR EACH item NO-LOCK " .
                "WHERE item.company_it = '" . $this->erpCompany . "' ". 
                "AND item.date_added > '" . $datestr . "', " .
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
                'item.upc1, ' .
                'item.req_date';

            $response = $this->service->read($query, $fields, $limit, $offset);

            $result = array();

            foreach ($response as $erpItem) {
                $result[] = $this->_buildFromErp($erpItem);
            }

            return $result;

        });
    }

    public function getProducts(int $limit = 100, int $offset = 0): array
    {

        $cacheId = md5("ProductService:getProducts:$limit:$offset");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(3600);

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
                'item.upc1, ' .
                'item.req_date';

            $response = $this->service->read($query, $fields, $limit, $offset);

            $result = array();

            foreach ($response as $erpItem) {
                $result[] = $this->_buildFromErp($erpItem);
            }

            return $result;

        });

    }

    public function getProduct(string $itemNumber): Product
    {

        $cacheId = md5("ProductService:getProduct:$itemNumber");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($itemNumber) {
            $item->expiresAfter(3600);

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
                'item.upc1, ' .
                'item.req_date';

            $response = $this->service->read($query, $fields, 1);

            return $this->_buildFromErp($response[0]);

        });


    }

    private function _buildFromErp($erpItem)
    {

        $t = new Product();
        $t->setCompany($erpItem['item_company_it'])
            ->setWebsite($erpItem['item_profile'][0])
            ->setItemNumber($erpItem['item_item'])
            ->setName($erpItem['item_descr'][0] . $erpItem['item_descr'][1])
            ->setTypeCode($erpItem['item_profile'][2])
            ->setManufacturerCode($erpItem['item_profile'][4])
            ->setWholesalePrice($erpItem['wa_item_list_price'])
            ->setOnHandQuantity($erpItem['wa_item_qty_oh'])
            ->setActive($erpItem['item_profile'][1])
            ->setDateCreated($erpItem['item_date_added'])
            ->setUpc($erpItem['item_upc1'])
            ->setMaxDiscountRate($erpItem['item_profile'][3])
            ->setSaleable($erpItem['item_profile'][6])
            ->setReleaseDate($erpItem['item_req_date']);            

        return $t;

    }

}