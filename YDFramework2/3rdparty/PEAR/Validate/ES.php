<?php

require_once('Validate.php');

class Validate_ES
{
    function dni($dni)
    {
        $letra  = substr($dni, -1);
        $number = substr($dni, 0, -1);
        if (!Validate::string($number, VALIDATE_NUM, 8, 8)) {
            return false;
        }
        if (!Validate::string($letra, VALIDATE_ALPHA)) {
            return false;
        }
        $string = 'TRWAGMYFPDXBNJZSQVHLCKET';
        if ($letra == $string{$number % 23}) {
            return true;
        }
        return false;
    }
}
?>