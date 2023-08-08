<?php

namespace App\Service;

use App\Model\ShippedItem;
use Psr\Log\LoggerInterface;

class ShippedItemService
{

    public function __construct(
        private LoggerInterface $logger,
        private ErpService $service,
        private string $erpCompany
    ) {
    }

    public function getShippedItems($orderNumber, int $limit = 100, int $offset = 0): array
    {
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
            'oe_line.item,  ' .
            'oe_line.q_comm_d, ' .
            'oe_ship_pack.order';

        $response = $this->service->read($query, $fields, $limit, $offset);

        $result = array();

        foreach ($response as $erpItem) {
            $result[] = $this->_buildFromErp($erpItem);
        }

        return $result;

    }

    private function _buildFromErp($erpItem)
    {

        $this->logger->info("Building shipped item from: " . print_r($erpItem, true));

        $t = new ShippedItem();
        $t->setManifestId($erpItem['oe_ship_pack_Manifest_id'])
        ->setCompany($erpItem['oe_ship_pack_company_oe'])
        ->setRecordType($erpItem['oe_line_rec_type'])
        ->setRecordSequence($erpItem['oe_line_rec_seq'])
        ->setOpen($erpItem['oe_line_opn'])
        ->setCustomerNumber($erpItem['oe_line_customer'])
        ->setPackedOn($erpItem['oe_ship_pack_last_mod_date'])
        ->setWeborderNumber($erpItem['oe_head_ord_ext'])
        ->setItemNumber($erpItem['oe_line_item'])
        ->setQuantityCommitted($erpItem['oe_line_q_comm_d'])
        ->setOrderNumber($erpItem['oe_ship_pack_order']);

        return $t;

    }

}