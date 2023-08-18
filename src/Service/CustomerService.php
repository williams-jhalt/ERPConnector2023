<?php

namespace App\Service;

use App\Model\Customer;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CustomerService
{

    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getNewCustomers(DateTimeInterface $since, int $limit = 100, int $offset = 0): array
    {

        $datestr = $since->format('m/d/Y');

        $cacheId = md5("CustomerService:getNewCustomers:$datestr:$limit:$offset");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($datestr, $limit, $offset) {
            $item->expiresAfter(3600);

            $query = "FOR EACH customer NO-LOCK " .
            "WHERE customer.company_cu = '" . $this->erpCompany . "' " . 
            "AND customer.opn_date > '" . $datestr . "'";

            $fields = ' customer.company_cu, ' .
                'customer.profile,  ' .
                'customer.customer,  ' .
                'customer.name,  ' .
                'customer.adr,  ' .
                'customer.state,  ' .
                'customer.postal_code,  ' .
                'customer.country_code,  ' .
                'customer.phone,  ' .
                'customer.cell_phone, ' .
                'customer.fax,  ' .
                'customer.email_address,  ' .
                'customer.atn,  ' .
                'customer.cr_mgr,  ' .
                'customer.opn_date,  ' .
                'customer.Active,  ' .
                'customer.cr_limit ';

            $response = $this->service->read($query, $fields, $limit, $offset);

            $result = array();

            foreach ($response as $erpItem) {
                $result[] = $this->_buildFromErp($erpItem);
            }

            return $result;

        });

    }

    public function getCustomers(int $limit = 100, int $offset = 0): array
    {

        $cacheId = md5("CustomerService:getCustomers:$limit:$offset");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(3600);

            $query = "FOR EACH customer NO-LOCK WHERE customer.company_cu = '" . $this->erpCompany . "'";

            $fields = ' customer.company_cu, ' .
                'customer.profile,  ' .
                'customer.customer,  ' .
                'customer.name,  ' .
                'customer.adr,  ' .
                'customer.state,  ' .
                'customer.postal_code,  ' .
                'customer.country_code,  ' .
                'customer.phone,  ' .
                'customer.cell_phone, ' .
                'customer.fax,  ' .
                'customer.email_address,  ' .
                'customer.atn,  ' .
                'customer.cr_mgr,  ' .
                'customer.opn_date,  ' .
                'customer.Active,  ' .
                'customer.cr_limit ';

            $response = $this->service->read($query, $fields, $limit, $offset);

            $result = array();

            foreach ($response as $erpItem) {
                $result[] = $this->_buildFromErp($erpItem);
            }

            return $result;

        });

    }

    public function getCustomer(string $customerNumber): Customer
    {

        $cacheId = md5("CustomerService:getCustomer:$customerNumber");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($customerNumber) {
            $item->expiresAfter(3600);

            $query = "FOR EACH customer NO-LOCK "
                . "WHERE customer.company_cu = '" . $this->erpCompany . "'"
                . "AND customer.customer EQ '" . $customerNumber . "'";

            $fields = ' customer.company_cu, ' .
                'customer.profile,  ' .
                'customer.customer,  ' .
                'customer.name,  ' .
                'customer.adr,  ' .
                'customer.state,  ' .
                'customer.postal_code,  ' .
                'customer.country_code,  ' .
                'customer.phone,  ' .
                'customer.cell_phone, ' .
                'customer.fax,  ' .
                'customer.email_address,  ' .
                'customer.atn,  ' .
                'customer.cr_mgr,  ' .
                'customer.opn_date,  ' .
                'customer.Active,  ' .
                'customer.cr_limit ';

            $response = $this->service->read($query, $fields, 1);

            return $this->_buildFromErp($response[0]);

        });

    }

    private function _buildFromErp($erpItem)
    {

        $t = new Customer();
        $t->setCompany($erpItem['customer_company_cu'])
            ->setWebsite($erpItem['customer_profile'][0])
            ->setCustomerNumber($erpItem['customer_customer'])
            ->setName($erpItem['customer_name'])
            ->setAddress1($erpItem['customer_adr'][0])
            ->setAddress2($erpItem['customer_adr'][1])
            ->setCity($erpItem['customer_adr'][3])
            ->setState($erpItem['customer_state'])
            ->setPostalCode($erpItem['customer_postal_code'])
            ->setCountryCode($erpItem['customer_country_code'])
            ->setPhone($erpItem['customer_phone'])
            ->setCellPhone($erpItem['customer_cell_phone'])
            ->setFax($erpItem['customer_fax'])
            ->setEmail($erpItem['customer_email_address'])
            ->setAttention($erpItem['customer_atn'])
            ->setCreditManager($erpItem['customer_cr_mgr'])
            ->setDateOpened($erpItem['customer_opn_date'])
            ->setActive($erpItem['customer_Active'])
            ->setDiscountRate($erpItem['customer_profile'][2])
            ->setCreditLimit($erpItem['customer_cr_limit']);

        return $t;

    }

}