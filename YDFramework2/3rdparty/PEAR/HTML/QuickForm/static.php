<?php

require_once("HTML/QuickForm/element.php");

class HTML_QuickForm_static extends HTML_QuickForm_element {
    
    var $_text = null;

    function HTML_QuickForm_static($elementName=null, $elementLabel=null, $text=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel);
        $this->_persistantFreeze = false;
        $this->_type = 'static';
        $this->_text = $text;
    }

    function setName($name)
    {
        $this->updateAttributes(array('name'=>$name));
    }

    function getName()
    {
        return $this->getAttribute('name');
    }

    function setText($text)
    {
        $this->_text = $text;
    }

    function setValue($text)
    {
        $this->setText($text);
    }

    function toHtml()
    {
        return $this->_getTabs() . $this->_text;
    }

    function getFrozenHtml()
    {
        return $this->toHtml();
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_defaultValues);
                }
                if (null !== $value) {
                    $this->setValue($value);
                }
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    }

    function exportValue(&$submitValues, $assoc = false)
    {
        return null;
    }
    
}
?>
