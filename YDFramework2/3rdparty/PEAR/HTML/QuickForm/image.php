<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_image extends HTML_QuickForm_input
{

    function HTML_QuickForm_image($elementName=null, $src='', $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, null, $attributes);
        $this->setType('image');
        $this->setSource($src);
    }

    function setSource($src)
    {
        $this->updateAttributes(array('src' => $src));
    }

    function setBorder($border)
    {
        $this->updateAttributes(array('border' => $border));
    }

    function setAlign($align)
    {
        $this->updateAttributes(array('align' => $align));
    }

    function freeze()
    {
        return false;
    }

}
?>
