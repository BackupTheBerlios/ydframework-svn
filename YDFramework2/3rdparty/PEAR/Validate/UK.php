<?php

require_once('Validate.php');

class Validate_UK
{
    function getZipValFunc(){
        return 'postcode';
    }

    function postcode($postcode, $strong=false)
    {
        $postcode = strtoupper(str_replace(' ', '', $postcode));

        $preg = "/^((GIR0AA)|((([A-PR-UWYZ][0-9][0-9]?)|([A-PR-UWYZ][A-HK-Y][0-9][0-9]?)|([A-PR-UWYZ][0-9][A-HJKSTUW])|([A-PR-UWYZ][A-HK-Y][0-9][ABEHMNPRVWXY]))[0-9][ABD-HJLNP-UW-Z]{2}))$/";
        $match = preg_match($preg, $postcode)? true : false;
        return $match;
    }

    function ni($ni){
        $ni = strtoupper(str_replace(' ', '', $ni));
        $preg = "/^[A-CEGHJ-NOPR-TW-Z][A-CEGHJ-NPR-TW-Z][0-9]{6}[ABCD]?$/";
        if (preg_match($preg, $ni)) {
            $bad_prefixes = array('GB', 'BG', 'NK', 'KN', 'TN', 'NT', 'ZZ');
            return (array_search(substr($ni, 0, 2), $bad_prefixes) === false);
        } else {
            return false;
        }
    }
    
    function ssn($ssn)
    {
        return ni($ssn);
    }

    function sortCode($sc){
        $preg = "/[0-9]{2}\-[0-9]{2}\-[0-9]{2}/";
        $match = (preg_match($preg, $sc))? true : false;
        return $match;
    }

    function bankAC($ac){
        $preg = "/[0-9]{6,8}/";
        $match = (preg_match($preg, $ac))? true : false;
        return $match;
    }

    function tel($tel){
        $tel = str_replace(Array('(', ')', '-', '+', '.', ' '), '', $tel);
        $preg = "/^0[0-9]{8,10}/";
        $match = (preg_match($preg, $tel))? true : false;
        return $match;
    }

    function carReg($reg){
        $reg = strtoupper(str_replace(' ', '', $reg));
        $suffpreg = "/[A-Z]{3}[0-9]{1,3}[A-Z]/";
        $suffres = preg_match($suffpreg, $reg);
        $prepreg = "/[A-Z][0-9]{1,3}[A-Z]{3}/";
        $suffres = preg_match($prepreg, $reg);
        $newpreg = "/[A-Z][0-9][05][A-Z]{3}/";
        $suffres = preg_match($newpreg, $reg);
        if (!$suffres||!$preres&&!$newres){
            return false;
        } else {
            return true;
        }
    }

    function passport($pp){
        $preg = "/[0-9]{9}/";
        $match = (preg_match($preg, $pp))? true : false ;
        return $match;
    }

    function drive($dl){
        $preg = "[A-Z]{5}[0-9]{6}[A-Z0-9]{5}";
        $match = (preg_match($preg, $dl))? true : false;
        return $match;
    }
}

?>
