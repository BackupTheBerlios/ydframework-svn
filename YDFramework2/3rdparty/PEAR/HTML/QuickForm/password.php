<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_password extends HTML_QuickForm_input
{

    function HTML_QuickForm_password($elementName=null, $elementLabel=null, $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->setType('password');
    }

    function setSize($size)
    {
        $this->updateAttributes(array('size'=>$size));
    }

    function setMaxlength($maxlength)
    {
        $this->updateAttributes(array('maxlength'=>$maxlength));
    }

    function getFrozenHtml()
    {
        $value = $this->getValue();
        return ('' != $value? '**********': '&nbsp;') .
               $this->_getPersistantData();
    }

}
?>
