<?php

define('VALIDATE_FINANCE_IBAN_OK',                 1);
define('VALIDATE_FINANCE_IBAN_ERROR',             -1);
define('VALIDATE_FINANCE_IBAN_GENERAL_INVALID',   -2);
define('VALIDATE_FINANCE_IBAN_TOO_SHORT',         -4);
define('VALIDATE_FINANCE_IBAN_TOO_LONG',          -5);
define('VALIDATE_FINANCE_IBAN_COUNTRY_INVALID',   -6);
define('VALIDATE_FINANCE_IBAN_INVALID_FORMAT',    -7);
define('VALIDATE_FINANCE_IBAN_CHECKSUM_INVALID',  -8);

class Validate_Finance_IBAN {

    var $_iban = '';
    var $_errorcode = 0;

    function _getCountrycodeCountryname()
    {
        static $_iban_countrycode_countryname;
        if (!isset($_iban_countrycode_countryname)) {
            $_iban_countrycode_countryname =
                array(
                    'AD' => 'Andorra',
                    'AT' => 'Austria',
                    'BE' => 'Belgium',
                    'CH' => 'Swiss',
                    'CZ' => 'Czech Republic',
                    'DE' => 'Germany',
                    'DK' => 'Denmark',
                    'ES' => 'Spain',
                    'FI' => 'Finland',
                    'FR' => 'France',
                    'GB' => 'Great Britain',
                    'GI' => 'Gibraltar',
                    'GR' => 'Greece',
                    'HU' => 'Hungary',
                    'IE' => 'Ireland',
                    'IS' => 'Island',
                    'IT' => 'Italy',
                    'LU' => 'Luxembourg',
                    'NL' => 'The Netherlands',
                    'NO' => 'Norwegian',
                    'PL' => 'Poland',
                    'PT' => 'Portugal',
                    'SE' => 'Sweden',
                    'SI' => 'Slovenia'
                );    
        }
        return $_iban_countrycode_countryname;
    }

    function _getCountrycodeIBANLength()
    {
        static $_iban_countrycode_length;
        if (!isset($_iban_countrycode_length)) {
            $_iban_countrycode_length =
                array(
                    'AD' => 24,
                    'AT' => 20,
                    'BE' => 16,
                    'CH' => 21,
                    'CZ' => 24,
                    'DE' => 22,
                    'DK' => 18,
                    'ES' => 24,
                    'FI' => 18,
                    'FR' => 27,
                    'GB' => 22,
                    'GI' => 23,
                    'GR' => 27,
                    'HU' => 28,
                    'IE' => 22,
                    'IS' => 26,
                    'IT' => 27,
                    'LU' => 20,
                    'NL' => 18,
                    'NO' => 15,
                    'PL' => 28,
                    'PT' => 25,
                    'SE' => 24,
                    'SI' => 19
                );
        }
        return $_iban_countrycode_length;
    }

    function _getCountrycodeBankcode()
    {
        static $_iban_countrycode_bankcode;
        if (!isset($_iban_countrycode_bankcode)) {
            $_iban_countrycode_bankcode =
                array(
                    'AD' => array('start' =>  4, 'length' =>  8),
                    'AT' => array('start' =>  4, 'length' =>  5),
                    'BE' => array('start' =>  4, 'length' =>  3),
                    'CH' => array('start' =>  4, 'length' =>  5),
                    'CZ' => array('start' =>  4, 'length' =>  4),
                    'DE' => array('start' =>  4, 'length' =>  8),
                    'DK' => array('start' =>  4, 'length' =>  4),
                    'ES' => array('start' =>  4, 'length' =>  8),
                    'FI' => array('start' =>  4, 'length' =>  6),
                    'FR' => array('start' =>  4, 'length' => 10),
                    'GB' => array('start' =>  4, 'length' =>  4),
                    'GI' => array('start' =>  4, 'length' =>  4),
                    'GR' => array('start' =>  4, 'length' =>  7),
                    'HU' => array('start' =>  4, 'length' =>  7),
                    'IE' => array('start' =>  4, 'length' => 10),
                    'IS' => array('start' =>  4, 'length' =>  4),
                    'IT' => array('start' =>  4, 'length' => 11),
                    'LU' => array('start' =>  4, 'length' =>  3),
                    'NL' => array('start' =>  4, 'length' =>  4),
                    'NO' => array('start' =>  4, 'length' =>  4),
                    'PL' => array('start' =>  4, 'length' =>  8),
                    'PT' => array('start' =>  4, 'length' =>  8),
                    'SE' => array('start' =>  4, 'length' =>  3),
                    'SI' => array('start' =>  4, 'length' =>  5)
                );
        }
        return $_iban_countrycode_bankcode;
    }

    function _getCountrycodeBankaccount()
    {
        static $_iban_countrycode_bankaccount;
        if (!isset($_iban_countrycode_bankaccount)) {
            $_iban_countrycode_bankaccount =
                array(
                    'AD' => array('start' => 12, 'length' => 12),
                    'AT' => array('start' =>  9, 'length' => 11),
                    'BE' => array('start' =>  7, 'length' =>  9),
                    'CH' => array('start' =>  9, 'length' => 12),
                    'CZ' => array('start' =>  8, 'length' => 16),
                    'DE' => array('start' => 12, 'length' => 10),
                    'DK' => array('start' =>  8, 'length' => 10),
                    'ES' => array('start' => 12, 'length' => 12),
                    'FI' => array('start' => 10, 'length' =>  8),
                    'FR' => array('start' => 14, 'length' => 13),
                    'GB' => array('start' =>  8, 'length' => 14),
                    'GI' => array('start' =>  8, 'length' => 15),
                    'GR' => array('start' => 11, 'length' => 16),
                    'HU' => array('start' => 12, 'length' => 15),
                    'IE' => array('start' => 14, 'length' =>  8),
                    'IS' => array('start' =>  8, 'length' => 18),
                    'IT' => array('start' => 15, 'length' => 12),
                    'LU' => array('start' =>  8, 'length' => 13),
                    'NL' => array('start' =>  8, 'length' => 10),
                    'NO' => array('start' =>  8, 'length' =>  7),
                    'PL' => array('start' => 12, 'length' => 16),
                    'PT' => array('start' => 12, 'length' => 13),
                    'SE' => array('start' =>  7, 'length' => 17),
                    'SE' => array('start' =>  7, 'length' =>  8)
                );
        }
        return $_iban_countrycode_bankaccount;
    }

    function _getCountrycodeRegex()
    {
        static $_iban_countrycode_regex;
        if (!isset($_iban_countrycode_regex)) {
            $_iban_countrycode_regex =
                array(
                    'AD' => '/^AD[0-9]{2}[0-9]{8}[A-Z0-9]{12}$/',
                    'AT' => '/^AT[0-9]{2}[0-9]{5}[0-9]{11}$/',
                    'BE' => '/^BE[0-9]{2}[0-9]{3}[0-9]{9}$/',
                    'CH' => '/^CH[0-9]{2}[0-9]{5}[A-Z0-9]{12}$/',
                    'CZ' => '/^CH[0-9]{2}[0-9]{4}[0-9]{16}$/',
                    'DE' => '/^DE[0-9]{2}[0-9]{8}[0-9]{10}$/',
                    'DK' => '/^DK[0-9]{2}[0-9]{4}[0-9]{10}$/',
                    'ES' => '/^ES[0-9]{2}[0-9]{8}[0-9]{12}$/',
                    'FI' => '/^FI[0-9]{2}[0-9]{6}[0-9]{8}$/',
                    'FR' => '/^FR[0-9]{2}[0-9]{10}[A-Z0-9]{13}$/',
                    'GB' => '/^GB[0-9]{2}[A-Z]{4}[0-9]{14}$/',
                    'GI' => '/^GB[0-9]{2}[A-Z]{4}[A-Z0-9]{15}$/',
                    'GR' => '/^GB[0-9]{2}[0-9]{7}[A-Z0-9]{16}$/',
                    'HU' => '/^GB[0-9]{2}[0-9]{7}[0-9]{1}[0-9]{15}[0-9]{1}$/',
                    'IE' => '/^IE[0-9]{2}[A-Z0-9]{4}[0-9]{6}[0-9]{8}$/',
                    'IS' => '/^IS[0-9]{2}[0-9]{4}[0-9]{18}$/',
                    'IT' => '/^IT[0-9]{2}[A-Z]{1}[0-9]{10}[A-Z0-9]{12}$/',
                    'LU' => '/^LU[0-9]{2}[0-9]{3}[A-Z0-9]{13}$/',
                    'NL' => '/^NL[0-9]{2}[A-Z]{4}[0-9]{10}$/',
                    'NO' => '/^NO[0-9]{2}[0-9]{4}[0-9]{7}$/',
                    'PL' => '/^PL[0-9]{2}[0-9]{8}[0-9]{16}$/',
                    'PT' => '/^PT[0-9]{2}[0-9]{8}[0-9]{13}$/',
                    'SE' => '/^SE[0-9]{2}[0-9]{3}[0-9]{17}$/',
                    'SI' => '/^SE[0-9]{2}[0-9]{5}[0-9]{8}[0-9]{2}$/'
                );
        }
        return $_iban_countrycode_regex;
    }

    function Validate_Finance_IBAN($iban='')
    {
        $iban = strtoupper($iban);
        $this->_iban = $iban;
    }

    function apiVersion()
    {
        return 1.0;
    }

    function getIBAN()
    {
        return $this->_iban;
    }

    function setIBAN($iban='')
    {
        $iban = strtoupper($iban);
        $this->_iban = $iban;
    }

    function validate($arg=null)
    {
        if ( isset($this) && is_a($this, 'Validate_Finance_IBAN') ) {
            $iban = $this->_iban;
        } else {
            $iban = $arg;
        }

        $errorcode=VALIDATE_FINANCE_IBAN_OK;

        static $_iban_countrycode_countryname;
        if (!isset($_iban_countrycode_countryname)) {
            $_iban_countrycode_countryname = Validate_Finance_IBAN::_getCountrycodeCountryname();
        }
        static $_iban_countrycode_length;
        if (!isset($_iban_countrycode_length)) {
            $_iban_countrycode_ibanlength      = Validate_Finance_IBAN::_getCountrycodeIBANLength();
        }
        static $_iban_countrycode_regex;
        if (!isset($_iban_countrycode_regex)) {
            $_iban_countrycode_regex       = Validate_Finance_IBAN::_getCountrycodeRegex();
        }
        
        if (strlen($iban) <= 4) {
            $errorcode = VALIDATE_FINANCE_IBAN_TOO_SHORT;
        } elseif (!isset( $_iban_countrycode_countryname[ substr($iban,0,2) ] )) {
            $errorcode = VALIDATE_FINANCE_IBAN_COUNTRY_INVALID;
        } elseif (strlen($iban) < $_iban_countrycode_ibanlength[ substr($iban,0,2) ]) {
            $errorcode = VALIDATE_FINANCE_IBAN_TOO_SHORT;
        } elseif (strlen($iban) > $_iban_countrycode_ibanlength[ substr($iban,0,2) ]) {
            $errorcode = VALIDATE_FINANCE_IBAN_TOO_LONG;
        } elseif (!preg_match($_iban_countrycode_regex[ substr($iban,0,2) ],$iban)) {
            $errorcode = VALIDATE_FINANCE_IBAN_INVALID_FORMAT;
        } else {
            $iban_replace_chars = range('A','Z');
            foreach (range(10,35) as $tempvalue) {
                $iban_replace_values[]=strval($tempvalue);
            }
            $tempiban = substr($iban, 4).substr($iban, 0, 4);
            $tempiban = str_replace($iban_replace_chars, $iban_replace_values, $tempiban);
            $tempcheckvalue = intval(substr($tempiban, 0, 1));
            for ($strcounter = 1; $strcounter < strlen($tempiban); $strcounter++) {
                $tempcheckvalue *= 10;
                $tempcheckvalue += intval(substr($tempiban,$strcounter,1));
                $tempcheckvalue %= 97;
            }
            if ($tempcheckvalue != 1) {
                $errorcode=VALIDATE_FINANCE_IBAN_CHECKSUM_INVALID;
            } else {
                $errorcode=VALIDATE_FINANCE_IBAN_OK;
            }
        }

        if ( isset($this) ) {
            $this->_errorcode=$errorcode;
        }
        return ($errorcode == VALIDATE_FINANCE_IBAN_OK);
    }

    function getErrorcode()
    {
        return $this->_errorcode;
    }

    function getCountrycode()
    {
        if (strlen($this->_iban)>4) {
            return substr($this->_iban,0,2);
        } else {
            $this->_errorcode = VALIDATE_FINANCE_IBAN_TOO_SHORT;
            return PEAR::raiseError($this->errorMessage($this->_errorcode), $this->_errorcode, PEAR_ERROR_TRIGGER, E_USER_WARNING, $this->errorMessage($this->_errorcode)." in VALIDATE_FINANCE_IBAN::getCountrycode()");
        }
    }

    function getCountryname()
    {
        $countrycode = $this->getCountrycode();
        if (is_string($countrycode)) {
            $_iban_countrycode_countryname = Validate_Finance_IBAN::_getCountrycodeCountryname();
            return $_iban_countrycode_countryname[$countrycode];
        } else {
            return $countrycode;
        }
    }

    function getBankcode()
    {
        if (!$this->validate()) {
            $this->_errorcode = VALIDATE_FINANCE_IBAN_GENERAL_INVALID;
            return PEAR::raiseError($this->errorMessage($this->_errorcode), $this->_errorcode, PEAR_ERROR_TRIGGER, E_USER_WARNING, $this->errorMessage($this->_errorcode)." in VALIDATE_FINANCE_IBAN::getBankcode()");
        } else {
            $_iban_countrycode_bankcode = Validate_Finance_IBAN::_getCountrycodeBankcode();
            $currCountrycodeBankcode = $_iban_countrycode_bankcode[ substr($this->_iban,0,2) ];
            return substr($this->_iban, $currCountrycodeBankcode['start'], $currCountrycodeBankcode['length']);
        }
    }

    function getBankaccount()
    {
        if (!$this->validate()) {
            $this->_errorcode = VALIDATE_FINANCE_IBAN_GENERAL_INVALID;
            return PEAR::raiseError($this->errorMessage($this->_errorcode), $this->_errorcode, PEAR_ERROR_TRIGGER, E_USER_WARNING, $this->errorMessage($this->_errorcode)." in VALIDATE_FINANCE_IBAN::getBankaccount()");
        } else {
            $_iban_countrycode_bankaccount = Validate_Finance_IBAN::_getCountrycodeBankaccount();
            $currCountrycodeBankaccount = $_iban_countrycode_bankaccount[ substr($iban,0,2) ];
            return substr($this->_iban, $currCountrycodeBankaccount['start'], $currCountrycodeBankaccount['length']);
        }
    }

    function errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                VALIDATE_FINANCE_IBAN_OK                => 'no error',
                VALIDATE_FINANCE_IBAN_ERROR             => 'unknown error',
                VALIDATE_FINANCE_IBAN_GENERAL_INVALID   => 'IBAN generally invalid',
                VALIDATE_FINANCE_IBAN_TOO_SHORT         => 'IBAN is too short',
                VALIDATE_FINANCE_IBAN_TOO_LONG          => 'IBAN is too long',
                VALIDATE_FINANCE_IBAN_COUNTRY_INVALID   => 'IBAN countrycode is invalid',
                VALIDATE_FINANCE_IBAN_INVALID_FORMAT    => 'IBAN has invalid format',
                VALIDATE_FINANCE_IBAN_CHECKSUM_INVALID  => 'IBAN checksum is invalid'
            );
        }
        if (VALIDATE_FINANCE_IBAN::isError($value)) {
            $value = $value->getCode();
        }
        return isset($errorMessages[$value]) ? $errorMessages[$value] : $errorMessages[VALIDATE_FINANCE_IBAN_ERROR];
    }
}
?>