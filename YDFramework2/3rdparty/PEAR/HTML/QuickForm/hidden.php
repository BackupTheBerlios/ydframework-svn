<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_hidden extends HTML_QuickForm_input
{

    function HTML_QuickForm_hidden($elementName=null, $value='', $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, null, $attributes);
        $this->setType('hidden');
        $this->setValue($value);
    }

    function freeze()
    {
        return false;
    }

    function accept(&$renderer)
    {
        $renderer->renderHidden($this);
    }

}
?>
