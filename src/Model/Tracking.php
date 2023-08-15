<?php

namespace App\Model;

class Tracking
{
    private $manifestId;
    private $company;
    private $recordType;
    private $recordSequence;
    private $open;
    private $customerNumber;
    private $packedOn;
    private $weborderNumber;
    private $orderNumber;
    private $trackingNumber;
    private $serviceType;
    private $shippingCost;

    public function getManifestId()
    {
        return $this->manifestId;
    }
    public function setManifestId($manifestId)
    {
        $this->manifestId = $manifestId;
        return $this;
    }
    public function getCompany()
    {
        return $this->company;
    }
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
    public function getRecordType()
    {
        return $this->recordType;
    }
    public function setRecordType($recordType)
    {
        $this->recordType = $recordType;
        return $this;
    }
    public function getRecordSequence()
    {
        return $this->recordSequence;
    }
    public function setRecordSequence($recordSequence)
    {
        $this->recordSequence = $recordSequence;
        return $this;
    }
    public function getOpen()
    {
        return $this->open;
    }
    public function setOpen($open)
    {
        $this->open = $open;
        return $this;
    }
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }
    public function setCustomerNumber($customerNumber)
    {
        $this->customerNumber = $customerNumber;
        return $this;
    }
    public function getPackedOn()
    {
        return $this->packedOn;
    }
    public function setPackedOn($packedOn)
    {
        $this->packedOn = $packedOn;
        return $this;
    }
    public function getWeborderNumber()
    {
        return $this->weborderNumber;
    }
    public function setWeborderNumber($weborderNumber)
    {
        $this->weborderNumber = $weborderNumber;
        return $this;
    }
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
        return $this;
    }
    public function getServiceType()
    {
        return $this->serviceType;
    }
    public function setServiceType($serviceType)
    {
        $this->serviceType = $serviceType;
        return $this;
    }
    public function getShippingCost()
    {
        return $this->shippingCost;
    }
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }
}