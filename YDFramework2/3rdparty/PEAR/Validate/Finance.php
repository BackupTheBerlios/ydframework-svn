<?php

require_once('Validate/Finance/IBAN.php');

class Validate_Finance {

    function iban($iban='')
    {
        return Validate_Finance_IBAN::validate($iban);
    }

    function banknoteEuro($banknote='')
    {
        $euro_countrycode = array('J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        if (strlen($banknote) != 12) {
            return false;
        }
        if (!in_array($banknote[0], $euro_countrycode)) {
            return false;
        }
        
        $banknote_replace_chars = range('A', 'Z');
        foreach (range(10,35) as $tempvalue) {
            $banknote_replace_values[]=strval($tempvalue);
        }

        $tempbanknote = str_replace($banknote_replace_chars, $banknote_replace_values, substr($banknote,0,-1));
        $tempcheckvalue = 0;
        for ($strcounter = 0; $strcounter < strlen($tempbanknote); $strcounter++) {
            $tempcheckvalue += intval($tempbanknote[$strcounter]);
        }
        $tempcheckvalue %= 9;
        $tempcheckvalue = 8 - $tempcheckvalue;

        return (intval($banknote[strlen($banknote)-1]) == $tempcheckvalue);
    }

}
?>
