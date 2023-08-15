<?php

namespace App\Service;

use App\Model\Invoice;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class InvoiceService
{

    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getInvoices(int $limit = 100, int $offset = 0): array
    {

        $cacheId = md5("getInvoices:$limit:$offset");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(3600);

            $query = "FOR EACH oe_head NO-LOCK " .
                "WHERE oe_head.rec_type = 'I' " .
                "AND oe_head.opn = 'N' " .
                "AND oe_head.company_oe = '" . $this->erpCompany . "', " .
                "EACH customer NO-LOCK " .
                "WHERE customer.company_cu = oe_head.company_cu " .
                "AND customer.customer = oe_head.customer";

            $fields = 'customer.profile, ' .
                'oe_head.opn,  ' .
                'oe_head.rec_type,  ' .
                'oe_head.company_oe,  ' .
                'oe_head.rec_seq,  ' .
                'oe_head.order,  ' .
                'oe_head.customer,  ' .
                'oe_head.invc_date,  ' .
                'oe_head.curx_c_tot_net_ar,  ' .
                'oe_head.curx_c_tot_code_amt ';

            $response = $this->service->read($query, $fields, $limit, $offset);

            $result = array();

            foreach ($response as $erpItem) {
                $result[] = $this->_buildFromErp($erpItem);
            }

            return $result;

        });

    }

    public function getInvoice(string $orderNumber): Invoice
    {

        $cacheId = md5("getInvoice:$orderNumber");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($orderNumber) {
            $item->expiresAfter(3600);

            $query = "FOR EACH oe_head NO-LOCK " .
                "WHERE oe_head.rec_type = 'I' " .
                "AND oe_head.order = '" . $orderNumber . "' " .
                "AND oe_head.opn = 'N' " .
                "AND oe_head.company_oe = '" . $this->erpCompany . "', " .
                "EACH customer NO-LOCK " .
                "WHERE customer.company_cu = oe_head.company_cu " .
                "AND customer.customer = oe_head.customer";

            $fields = 'customer.profile, ' .
                'oe_head.opn,  ' .
                'oe_head.rec_type,  ' .
                'oe_head.company_oe,  ' .
                'oe_head.rec_seq,  ' .
                'oe_head.order,  ' .
                'oe_head.customer,  ' .
                'oe_head.invc_date,  ' .
                'oe_head.curx_c_tot_net_ar,  ' .
                'oe_head.curx_c_tot_code_amt ';

            $response = $this->service->read($query, $fields, 1);

            return $this->_buildFromErp($response[0]);

        });


    }

    private function _buildFromErp($erpItem)
    {

        $t = new Invoice();
        $t->setWebsite($erpItem['customer_profile'][0])
            ->setOpen($erpItem['oe_head_opn'])
            ->setRecordType($erpItem['oe_head_rec_type'])
            ->setCompany($erpItem['oe_head_company_oe'])
            ->setOrderNumber($erpItem['oe_head_order'])
            ->setCustomerNumber($erpItem['oe_head_customer'])
            ->setInvoiceDate($erpItem['oe_head_invc_date'])
            ->setSubtotal($erpItem['oe_head_curx_c_tot_net_ar'])
            ->setShippingCost($erpItem['oe_head_curx_c_tot_code_amt'][0]);

        return $t;

    }

}