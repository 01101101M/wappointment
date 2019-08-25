<?php

namespace Wappointment\Validators;

class Phone extends \Rakit\Validation\Rule
{
    protected $message = ':attribute is not a valid Phone number';
    protected $countries = null;

    public function __construct($countriesAllowed)
    {
        $this->countries = $countriesAllowed;
    }
    public function check($value)
    {
        if (empty($value)) {
            return false;
        }
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        $number = $phoneUtil->parse($value);

        if ($phoneUtil->isValidNumber($number) === false) {
            return false;
        }

        if (
            !empty($this->countries) &&
            !in_array($phoneUtil->getRegionCodeForNumber($number), $this->countries)
        ) {
            return false;
        }

        return true;
    }

    public function checkOld($value)
    {
        $value = preg_replace('/\s+/', '', $value);
        return $this->isE164($value) || $this->isNANP($value) || $this->isDigits($value);
    }

    protected function isDigits($value)
    {
        $conditions = [];
        $conditions[] = strlen($value) >= 10;
        $conditions[] = strlen($value) <= 16;
        $conditions[] = preg_match("/[^\d]/i", $value) === 0;
        return (bool)array_product($conditions);
    }

    protected function isE164($value)
    {
        $conditions = [];
        $conditions[] = strpos($value, '+') === 0;
        $conditions[] = strlen($value) >= 9;
        $conditions[] = strlen($value) <= 16;
        $conditions[] = preg_match("/[^\d+]/i", $value) === 0;
        return (bool)array_product($conditions);
    }

    protected function isNANP($value)
    {
        $conditions = [];
        $conditions[] = preg_match("/^(?:\+1|1)?\s?-?\(?\d{3}\)?(\s|-)?\d{3}-\d{4}$/i", $value) > 0;
        return (bool)array_product($conditions);
    }
}
