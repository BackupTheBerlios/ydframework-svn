<?php

require_once("HTML/QuickForm/input.php");

class HTML_QuickForm_checkbox extends HTML_QuickForm_input
{

    var $_text = '';

    function HTML_QuickForm_checkbox($elementName=null, $elementLabel=null, $text='', $attributes=null)
    {
        HTML_QuickForm_input::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_text = $text;
        $this->setType('checkbox');
        $this->updateAttributes(array('value'=>1));
        $this->_generateId();
    }

    function setChecked($checked)
    {
        if (!$checked) {
            $this->removeAttribute('checked');
        } else {
            $this->updateAttributes(array('checked'=>'checked'));
        }
    }

    function getChecked()
    {
        return (bool)$this->getAttribute('checked');
    }

    function toHtml()
    {
        if (0 == strlen($this->_text)) {
            $label = '';
        } elseif ($this->_flagFrozen) {
            $label = $this->_text;
        } else {
            $label = '<label for="' . $this->getAttribute('id') . '">' . $this->_text . '</label>';
        }
        return HTML_QuickForm_input::toHtml() . $label;
    }

    function getFrozenHtml()
    {
        if ($this->getChecked()) {
            return '<tt>[x]</tt>' .
                   $this->_getPersistantData();
        } else {
            return '<tt>[ ]</tt>';
        }
    }

    function setText($text)
    {
        $this->_text = $text;
    }

    function getText()
    {
        return $this->_text;
    }

    function setValue($value)
    {
        return $this->setChecked($value);
    }

    function getValue()
    {
        return $this->getChecked();
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if (isset($caller->_submitValues) && 0 < count($caller->_submitValues)) {
                        $value = $this->_findValue($caller->_submitValues);
                    } else {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (null !== $value) {
                    $this->setChecked($value);
                }
                break;
            case 'setGroupValue':
                $this->setChecked($arg);
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    }

    function exportValue(&$submitValues, $assoc = false)
    {
        $value = $this->_findValue($submitValues);
        if (null === $value) {
            $value = $this->getChecked()? true: null;
        }
        return $this->_prepareValue($value, $assoc);
    }
    
}
?>
