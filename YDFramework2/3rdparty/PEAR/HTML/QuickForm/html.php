<?php

require_once 'HTML/QuickForm/static.php';

class HTML_QuickForm_html extends HTML_QuickForm_static
{

    function HTML_QuickForm_html($text = null)
    {
        $this->HTML_QuickForm_static(null, null, $text);
        $this->_type = 'html';
    }

    function accept(&$renderer)
    {
        $renderer->renderHtml($this);
    }

}
?>
