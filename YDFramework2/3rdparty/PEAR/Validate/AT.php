<?php

require_once('Validate.php');

class Validate_AT
{

    function postcode($postcode, $strong=false)
    {
        if ($strong) {
            static $postcodes;
    
            if (!isset($postcodes)) {
                $file = '@DATADIR@/Validate/AT_postcodes.txt';
                $postcodes = array_map('trim', file($file));
            }
    
            return in_array((int) $postcode, $postcodes);
        } else {
            return (ereg('^[0-9]{4}$', $postcode));
        }
    }

    function ssn($svn)
    {
        $matched = preg_match(
            '/^(\d{3})(\d)\D*(\d{2})\D*(\d{2})\D*(\d{2})$/',
            $svn,
            $matches
        );

        if (!$matched) {
            return false;
        }

        list(, $num, $chk, $d, $m, $y) = $matches;

        if (!Validate::date("$d-$m-$y", array('format' => '%d-%m-%y'))) {
            return false;
        }

        $str = (string) $num . $chk . $d . $m . $y;
        $len = strlen($str);
        $fkt = '3790584216';
        $sum = 0;

        for ($i = 0; $i < $len; $i++) {
            $sum += $str{$i} * $fkt{$i};
        }

        $sum = $sum % 11;
        if ($sum == 10) {
            $sum = 0;
        }

        return ($sum == $chk);
    }
}
?>