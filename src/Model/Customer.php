<?php

namespace App\Model;

class Customer {

    private $company;
    private $website;
    private $customerNumber;
    private $name;
    private $address1;
    private $address2;
    private $city;
    private $state;
    private $postalCode;
    private $countryCode;
    private $phone;
    private $cellPhone;
    private $fax;
    private $email;
    private $attention;
    private $creditManager;
    private $dateOpened;
    private $active;
    private $discountRate;
    private $creditLimit;    

    public function getCompany() {
        return $this->company;
    }

    public function setCompany($company) {
        $this->company = $company;
        return $this;
    }

    public function getWebsite() {
        return $this->website;
    }

    public function setWebsite($website) {
        $this->website = $website;
        return $this;
    }

    public function getCustomerNumber() {
        return $this->customerNumber;
    }

    public function setCustomerNumber($customerNumber) {
        $this->customerNumber = $customerNumber;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getAddress1() {
        return $this->address1;
    }

    public function setAddress1($address1) {
        $this->address1 = $address1;
        return $this;
    }

    public function getAddress2() {
        return $this->address2;        
    }

    public function setAddress2($address2) {
        $this->address2 = $address2;
        return $this;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
        return $this;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
        return $this;
    }

    public function getPostalCode() {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getCellPhone() {
        return $this->cellPhone;
    }

    public function setCellPhone($cellPhone) {
        $this->cellPhone = $cellPhone;
        return $this;
    }

    public function getFax() {
        return $this->fax;
    }

    public function setFax($fax) {
        $this->fax = $fax;
        return $this;
    }



	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * @param mixed $email 
	 * @return self
	 */
	public function setEmail($email): self {
		$this->email = $email;
		return $this;
	}

    public function getAttention() {
        return $this->attention;
    }

    public function setAttention($attention) {
        $this->attention = $attention;
        return $this;
    }

    public function getCreditManager() {
        return $this->creditManager;
    }

    public function setCreditManager($creditManager) {
        $this->creditManager = $creditManager;
        return $this;
    }

    public function getDateOpened() {
        return $this->dateOpened;
    }

    public function setDateOpened($dateOpened) {
        $this->dateOpened = $dateOpened;
        return $this;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    public function getDiscountRate() {
        return $this->discountRate;
    }

    public function setDiscountRate($discountRate) {
        $this->discountRate = $discountRate;
        return $this;
    }

    public function getCreditLimit() {
        return $this->creditLimit;
    }

    public function setCreditLimit($creditLimit) {
        $this->creditLimit = $creditLimit;
        return $this;
    }
}