<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_submit extends HTML_QuickForm_input
{

    function HTML_QuickForm_submit($elementName=null, $value=null, $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, null, $attributes);
        $this->setValue($value);
        $this->setType('submit');
    }

    function freeze()
    {
        return false;
    }

    function exportValue(&$submitValues, $assoc = false)
    {
        return $this->_prepareValue($this->_findValue($submitValues), $assoc);
    }

}
?>
