<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_text extends HTML_QuickForm_input
{
                
    function HTML_QuickForm_text($elementName=null, $elementLabel=null, $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->setType('text');
    }

    function setSize($size)
    {
        $this->updateAttributes(array('size'=>$size));
    }

    function setMaxlength($maxlength)
    {
        $this->updateAttributes(array('maxlength'=>$maxlength));
    }
    
}
?>
