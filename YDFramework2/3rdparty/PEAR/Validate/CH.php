<?php

require_once('Validate.php');

class Validate_CH
{

    function ssn($ssn)
    {
        $t_regex = preg_match('/\d{3}\.\d{2}\.\d{3}\.\d{3}/', $ssn, $matches );
        if (!$t_regex)
            return false;
        $weights = array(5, 4, 3, 0, 2, 7, 0, 6, 5, 4, 0, 3, 2);
        $t_check = Validate::_check_control_number($ssn, $weights, 11, 11);
        return $t_check; 
    } 
    
    function studentid($umn)
    {
        $umn = preg_replace('/(\d{2})-(\d{3})-(\d{3})/', '$1$2$3', $umn); 
        $t_regex = preg_match('/\d{8}/', $umn);

        if (!$t_regex)
            return false;

        $weights = array(2, 1, 2, 1, 2, 1, 2);
    
        $sum = 0; 

        for ($i = 0; $i <= 6; ++$i) {
            $tsum =  $umn{$i} * $weights[$i]; 
            $sum += ($tsum > 9 ? $tsum - 9 : $tsum);
        }

        $sum = 10 - $sum%10;

        return ($sum == $umn[7]); 
    }

    function postcode($postcode, $strong=false)
    {
        if ($strong) {
            static $postcodes;
    
            if (!isset($postcodes)) {
                $file = '@DATADIR@/Validate/CH_postcodes.txt';
                $postcodes = array_map('trim', file($file));
            }
    
            return in_array($postcode, $postcodes);
        } else {
            return (ereg('^[0-9]{4}$', $postcode));
        }
    } 
}
?>
