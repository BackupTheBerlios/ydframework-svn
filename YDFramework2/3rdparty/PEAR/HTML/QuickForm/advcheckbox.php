<?php

require_once('HTML/QuickForm/checkbox.php');

class HTML_QuickForm_advcheckbox extends HTML_QuickForm_checkbox
{

    var $_values = null;
    var $_currentValue = null;

    function HTML_QuickForm_advcheckbox($elementName=null, $elementLabel=null, $text=null, $attributes=null, $values=null)
    {
        $this->HTML_QuickForm_checkbox($elementName, $elementLabel, $text, $attributes);
        $this->setValues($values);
    }

    function getPrivateName($elementName)
    {
        return '__'.$elementName;
    }

    function getOnclickJs($elementName)
    {
        $onclickJs = 'if (this.checked) { this.form[\''.$elementName.'\'].value=\''.addcslashes($this->_values[1], '\'').'\'; }';
        $onclickJs .= 'else { this.form[\''.$elementName.'\'].value=\''.addcslashes($this->_values[0], '\'').'\'; }';
        return $onclickJs;
    }

    function setValues($values)
    {
        if (empty($values)) {
            $this->_values = array('', 1);
        } elseif (is_scalar($values)) {
            $this->_values = array('', $values);
        } else {
            $this->_values = $values;
        }
        $this->setChecked($this->_currentValue == $this->_values[1]);
    }

    function setValue($value)
    {
        $this->setChecked(isset($this->_values[1]) && $value == $this->_values[1]);
        $this->_currentValue = $value;
    }

    function getValue()
    {
        if (is_array($this->_values)) {
            return $this->_values[$this->getChecked()? 1: 0];
        } else {
            return null;
        }
    }

    function toHtml()
    {
        if ($this->_flagFrozen) {
            return parent::toHtml();
        } else {
            $oldName = $this->getName();
            $oldJs   = $this->getAttribute('onclick');
            $this->updateAttributes(array(
                'name'    => $this->getPrivateName($oldName),
                'onclick' => $this->getOnclickJs($oldName) . ' ' . $oldJs
            ));
            $html = parent::toHtml() . '<input type="hidden" name="' . $oldName . 
                    '" value="' . $this->getValue() . '" />';
            $this->updateAttributes(array(
                'name'    => $oldName, 
                'onclick' => $oldJs
            ));
            return $html;
        }
    }

    function getFrozenHtml()
    {
        return ($this->getChecked()? '<tt>[x]</tt>': '<tt>[ ]</tt>') .
               $this->_getPersistantData();
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_submitValues);
                    if (null === $value) {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                $this->setValue($value);
                break;
            default:
                parent::onQuickFormEvent($event, $arg, $caller);
        }
        return true;
    }

    function exportValue(&$submitValues, $assoc)
    {
        $value = $this->_findValue($submitValues);
        if (null === $value) {
            $value = $this->getValue();
        } elseif (is_array($this->_values) && ($value != $this->_values[0]) && ($value != $this->_values[1])) {
            $value = null;
        }
        return $this->_prepareValue($value, $assoc);
    }

}
?>
