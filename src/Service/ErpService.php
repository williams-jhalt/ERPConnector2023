<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ErpService
{

    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private $erpServer,
        private $erpUsername,
        private $erpPassword,
        private $erpCompany,
        private $erpAppname
    ) {
    }

    private function _getGrantToken()
    {

        $cacheId = md5("grant_token:{$this->erpServer}:{$this->erpCompany}:{$this->erpAppname}");

        return $this->cache->get($cacheId, function (ItemInterface $item) {

            $item->expiresAfter(3600);

            $response = $this->client->request(
                'POST',
                $this->erpServer . "/distone/rest/service/authorize/grant",
                [
                    'body' => [
                        'client' => $this->erpAppname,
                        'company' => $this->erpCompany,
                        'username' => $this->erpUsername,
                        'password' => $this->erpPassword
                    ]
                ]
            );

            $data = $response->toArray();

            if (isset($data['_errors'])) {
                throw new ErpServiceException($data['_errors'][0]['_errorMsg'], $data['_errors'][0]['_errorNum']);
            }

            return $data['grant_token'];

        });

    }

    private function _getAccessToken()
    {

        $cacheId = md5("access_token:{$this->erpServer}:{$this->erpCompany}:{$this->erpAppname}");

        return $this->cache->get($cacheId, function (ItemInterface $item) {

            $item->expiresAfter(3600);

            $response = $this->client->request(
                'POST',
                $this->erpServer . "/distone/rest/service/authorize/access",
                [
                    'body' => [
                        'client' => $this->erpAppname,
                        'company' => $this->erpCompany,
                        'grant_token' => $this->_getGrantToken()
                    ]
                ]
            );

            $data = $response->toArray();

            if (isset($data['_errors'])) {
                throw new ErpServiceException($data['_errors'][0]['_errorMsg'], $data['_errors'][0]['_errorNum']);
            }

            return $data['access_token'];

        });

    }

    /**
     * 
     * @param string $company
     * @param string $table
     * @param array $records
     * @param boolean $triggers
     * @return mixed
     * @throws ErpServiceException
     */
    public function create($table, $records, $triggers = true)
    {

        $response = $this->client->request(
            'POST',
            $this->erpServer . "/distone/rest/service/data/create",
            [
                'headers' => [
                    'Authorization' => $this->_getAccessToken()
                ],
                'body' => [
                    'table' => $table,
                    'records' => $records,
                    'triggers' => $triggers
                ]
            ]
        )->toArray();

        if (isset($response['_errors'])) {
            throw new ErpServiceException($response['_errors'][0]['_errorMsg'], $response['_errors'][0]['_errorNum']);
        }

        return $response;

    }

    /**
     * 
     * @param string $query
     * @param string $columns
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     * @throws ErpServiceException
     */
    public function read($query, $columns = "*", $limit = 0, $offset = 0)
    {

        $this->logger->info("Query: " . $query);
        $this->logger->info("Columns: " . $columns);

        $response = $this->client->request(
            'POST',
            $this->erpServer . "/distone/rest/service/data/read",
            [
                'headers' => [
                    'Authorization' => $this->_getAccessToken(),
                    'Content-Type' => "application/x-www-form-urlencoded"
                ],
                'body' => [
                    'query' => $query,
                    'columns' => $columns,
                    'skip' => $offset,
                    'take' => $limit
                ]
            ]
        )->toArray();

        if (isset($response['_errors'])) {
            throw new \Exception($response['_errors'][0]['_errorMsg'], $response['_errors'][0]['_errorNum']);
        }

        return $response;

    }

    /**
     * Gets item pricing based on customer, quantity, and unit of measure
     * 
     * Returns a object containing the following:
     * 
     * item - Item Number of the item that the price was calculated for.
     * warehouse - Warehouse Code used in the price calculation.
     * customer - Customer Id used to calculate customer based pricing.
     * cu_group - Customer Group code used in the price calculation.
     * vendor - Vendor Id used in the price calculation.
     * quantity - Quantity used to get the price at a specific quantity break level.
     * price - The calculated price of the item.
     * unit - Unit of measure code (price per).
     * origin - Price calculation origin code. This code indicates how the price was calculated internally.
     * commission - A sales commission percentage for the item.
     * column - Column price label when a column price was used in the calculation.
     * 
     * @param string $company
     * @param string $itemNumber
     * @param string $customer
     * @param integer $quantity
     * @param string $uom
     * @return mixed
     */
    public function getItemPriceDetails($company, $itemNumber, $customer = null, $quantity = 1, $uom = "EA")
    {


        $response = $this->client->request(
            'GET',
            $this->erpServer . "/distone/rest/service/price/fetch",
            [
                'headers' => [
                    'Authorization' => $this->_getAccessToken()
                ],
                'params' => [
                    'item' => $itemNumber,
                    'customer' => $customer,
                    'quantity' => $quantity,
                    'unit' => $uom
                ]
            ]
        )->toArray();

        if (isset($response['_errors'])) {
            throw new ErpServiceException($response['_errors'][0]['_errorMsg'], $response['_errors'][0]['_errorNum']);
        }

        return $response;

    }

    /**
     * Type can be: invoice, pick, pack, order
     * Record is the record number
     * Sequence defaults to 1
     * 
     * Returns the following array:
     * 
     * type: type of document
     * record: record number
     * seq: record sequence
     * encoding: MIME type
     * document: encoded document
     * 
     * @param string $company
     * @param string $type
     * @param string $record
     * @param string|null $seq
     */
    public function getPdf($company, $type, $record, $seq = 1, $ch = null)
    {

        $response = $this->client->request(
            'GET',
            $this->erpServer . "/distone/rest/service/form/fetch",
            [
                'headers' => [
                    'Authorization' => $this->_getAccessToken()
                ],
                'params' => [
                    'type' => $type,
                    'record' => $record,
                    'seq' => $seq
                ]
            ]
        )->toArray();

        if (isset($response['_errors'])) {
            throw new ErpServiceException($response['_errors'][0]['_errorMsg'], $response['_errors'][0]['_errorNum']);
        }

        return $response;

    }

}