<?php

namespace App\Service;

use App\Model\Tracking;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TrackingService
{

    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getTracking($orderNumber): Tracking
    {

        $cacheId = md5("TrackingService:getTracking:$orderNumber");

        return $this->cache->get($cacheId, function (ItemInterface $item) use ($orderNumber) {
            $item->expiresAfter(3600);

            $query = "FOR EACH oe_ship_pack NO-LOCK " .
                "WHERE oe_ship_pack.company_oe = '" . $this->erpCompany . "' " .
                "AND oe_ship_pack.order = '" . $orderNumber . "', " .
                "EACH oe_line NO-LOCK " .
                "WHERE oe_line.order = oe_ship_pack.order " .
                "AND oe_line.rec_type = oe_ship_pack.rec_type " .
                "AND oe_line.rec_seq = oe_ship_pack.rec_seq " .
                "AND oe_line.company_oe = oe_ship_pack.company_oe " .
                "AND oe_line.opn = 'no', " .
                "EACH oe_head NO-LOCK " .
                "WHERE oe_head.order = oe_line.order " .
                "AND oe_head.rec_seq = oe_line.rec_seq " .
                "AND oe_head.rec_type = oe_line.rec_type " .
                "AND oe_head.company_oe = oe_line.company_oe";

            $fields = ' oe_ship_pack.Manifest_id, ' .
                'oe_ship_pack.company_oe,  ' .
                'oe_line.rec_type,  ' .
                'oe_line.rec_seq,  ' .
                'oe_line.opn,  ' .
                'oe_line.customer,  ' .
                'oe_ship_pack.last_mod_date,  ' .
                'oe_head.ord_ext,  ' .
                'oe_ship_pack.tracking_no,  ' .
                'oe_ship_pack.service_type,  ' .
                'oe_head.curx_c_tot_code_amt,  ' .
                'oe_ship_pack.order';

            $response = $this->service->read($query, $fields, 1);

            return $this->_buildFromErp($response[0]);

        });

    }

    private function _buildFromErp($erpItem)
    {

        $t = new Tracking();
        $t->setManifestId($erpItem['oe_ship_pack_Manifest_id'])
            ->setCompany($erpItem['oe_ship_pack_company_oe'])
            ->setRecordType($erpItem['oe_line_rec_type'])
            ->setRecordSequence($erpItem['oe_line_rec_seq'])
            ->setOpen($erpItem['oe_line_opn'])
            ->setCustomerNumber($erpItem['oe_line_customer'])
            ->setPackedOn($erpItem['oe_ship_pack_last_mod_date'])
            ->setWeborderNumber($erpItem['oe_head_ord_ext'])
            ->setOrderNumber($erpItem['oe_ship_pack_order'])
            ->setTrackingNumber($erpItem['oe_ship_pack_tracking_no'])
            ->setServiceType($erpItem['oe_ship_pack_service_type'])
            ->setShippingCost($erpItem['oe_head_curx_c_tot_code_amt'][0]);

        return $t;

    }

}