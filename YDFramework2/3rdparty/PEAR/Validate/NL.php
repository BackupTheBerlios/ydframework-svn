<?php

require_once('Validate.php');

define( "VALIDATE_NL_PHONENUMBER_TYPE_ANY",     0);
define( "VALIDATE_NL_PHONENUMBER_TYPE_NORMAL",  1);
define( "VALIDATE_NL_PHONENUMBER_TYPE_MOBILE",  2);

class Validate_NL
{

    function postcode($postcode, $strong=false)
    {
        return (ereg('^[0-9]{4}\ {0,1}[A-Za-z]{2}$', $postcode)); // '1234 AB', '1234AB', '1234 ab'
    }

    function phonenumber($number, $type = PHONENUMBER_TYPE_ANY)
    {
        $result = false;

        if (ereg("^[+0-9]{9,}$", $number)) {
            $number = substr($number, strlen($number)-9);

            if (strlen($number) >= 9) {
                switch ($type)
                {
                    case VALIDATE_NL_PHONENUMBER_TYPE_ANY:
                        $result = true;
                        break;
                    case VALIDATE_NL_PHONENUMBER_TYPE_NORMAL:
                        if ((int)$number[0] != 6)
                            $result = true;
                        break;
                    case VALIDATE_NL_PHONENUMBER_TYPE_MOBILE:
                        if ((int)$number[0] == 6)
                            $result = true;
                        break;
                 }
            }
        }

        return $result;
    }

    function ssn($ssn)
    {
        return (ereg("^[0-9]{9}$", $ssn));
    }

    function bankAccountNumber($number)
    {
        $result     = false;
        $checksum   = 0;

        if (is_numeric((string)$number) && strlen((string)$number) <= 10) {
            $number = str_pad($number, 10, '0', STR_PAD_LEFT);
            for ($i=0; $i < 10; $i++) {
                $checksum += ( (int)$number[$i] * (10 - $i) );
            }
            if ($checksum > 0 && $checksum % 11 == 0)
                $result = true;
            return $result;
        }
    }

}
?>
