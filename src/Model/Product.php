<?php

namespace App\Model;

class Product
{
    private $company;
    private $website;
    private $itemNumber;
    private $name;
    private $typeCode;
    private $manufacturerCode;
    private $wholesalePrice;
    private $onHandQuantity;
    private $active;
    private $dateCreated;
    private $upc;
    private $maxDiscountRate;
    private $saleable;

    public function getCompany()
    {
        return $this->company;
    }
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
    public function getWebsite()
    {
        return $this->website;
    }
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }
    public function getItemNumber()
    {
        return $this->itemNumber;
    }
    public function setItemNumber($itemNumber)
    {
        $this->itemNumber = $itemNumber;
        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getTypeCode()
    {
        return $this->typeCode;
    }
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;
        return $this;
    }
    public function getManufacturerCode()
    {
        return $this->manufacturerCode;
    }
    public function setManufacturerCode($manufacturerCode)
    {
        $this->manufacturerCode = $manufacturerCode;
        return $this;
    }
    public function getWholesalePrice()
    {
        return $this->wholesalePrice;
    }
    public function setWholesalePrice($wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;
        return $this;
    }
    public function getOnHandQuantity()
    {
        return $this->onHandQuantity;
    }
    public function setOnHandQuantity($onHandQuantity)
    {
        $this->onHandQuantity = $onHandQuantity;
        return $this;
    }
    public function getActive()
    {
        return $this->active;
    }
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }
    public function getUpc()
    {
        return $this->upc;
    }
    public function setUpc($upc)
    {
        $this->upc = $upc;
        return $this;
    }
    public function getMaxDiscountRate()
    {
        return $this->maxDiscountRate;
    }
    public function setMaxDiscountRate($maxDiscountRate)
    {
        $this->maxDiscountRate = $maxDiscountRate;
        return $this;
    }
    public function getSaleable()
    {
        return $this->saleable;
    }
    public function setSaleable($saleable)
    {
        $this->saleable = $saleable;
        return $this;
    }
}