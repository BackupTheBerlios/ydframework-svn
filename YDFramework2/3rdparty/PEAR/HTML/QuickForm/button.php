<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_button extends HTML_QuickForm_input
{

    function HTML_QuickForm_button($elementName=null, $value=null, $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, null, $attributes);
        $this->_persistantFreeze = false;
        $this->setValue($value);
        $this->setType('button');
    }

    function freeze()
    {
        return false;
    } 
 
}
?>
