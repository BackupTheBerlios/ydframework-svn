<?php

require_once('HTML/QuickForm/select.php');

class HTML_QuickForm_hiddenselect extends HTML_QuickForm_select
{

    function HTML_QuickForm_hiddenselect($elementName=null, $elementLabel=null, $options=null, $attributes=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'hiddenselect';
        if (isset($options)) {
            $this->load($options);
        }
    }

    function toHtml()
    {
        $tabs    = $this->_getTabs();
        $name    = $this->getPrivateName();
        $strHtml = '';

        foreach ($this->_values as $key => $val) {
            for ($i = 0, $optCount = count($this->_options); $i < $optCount; $i++) {
                if ($val == $this->_options[$i]['attr']['value']) {
                    $strHtml .= $tabs . '<input type="hidden" name="' . $name . '" value="' . $val . '" />' . "\n";
                }
            }
        }

        return $strHtml;
    } 

    function accept(&$renderer)
    {
        $renderer->renderHidden($this);
    }

}
?>
