<?php

require_once 'HTML/QuickForm/static.php';

class HTML_QuickForm_header extends HTML_QuickForm_static
{

    function HTML_QuickForm_header($elementName = null, $text = null)
    {
        $this->HTML_QuickForm_static($elementName, null, $text);
        $this->_type = 'header';
    }

    function accept(&$renderer)
    {
        $renderer->renderHeader($this);
    } 

}
?>
