<?php

require_once('Validate.php');

class Validate_DE
{
    function postcode($postcode, $strong=false)
    {
        return (ereg('^[0-9]{5}$', $postcode));
    }

    function bankcode($postcode)
    {
        return (ereg('^[0-9]{8}$', $postcode));
    }
}
?>
