<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_reset extends HTML_QuickForm_input
{

    function HTML_QuickForm_reset($elementName=null, $value=null, $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, null, $attributes);
        $this->setValue($value);
        $this->setType('reset');
    }

    function freeze()
    {
        return false;
    }

}
?>
