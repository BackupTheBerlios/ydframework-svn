<?php

require_once('HTML/Common.php');

class HTML_QuickForm_element extends HTML_Common
{

    var $_label = '';
    var $_type = '';
    var $_flagFrozen = false;
    var $_persistantFreeze = false;

    function HTML_QuickForm_element($elementName=null, $elementLabel=null, $attributes=null)
    {
        HTML_Common::HTML_Common($attributes);
        if (isset($elementName)) {
            $this->setName($elementName);
        }
        if (isset($elementLabel)) {
            $this->setLabel($elementLabel);
        }
    }

    function apiVersion()
    {
        return 2.0;
    }

    function getType()
    {
        return $this->_type;
    }

    function setName($name)
    {
    }

    function getName()
    {
    }

    function setValue($value)
    {
    }

    function getValue()
    {
        return null;
    }

    function freeze()
    {
        $this->_flagFrozen = true;
    }

    function getFrozenHtml()
    {
        $value = $this->getValue();
        return ('' != $value? htmlspecialchars($value): '&nbsp;') .
               $this->_getPersistantData();
    }

    function _getPersistantData()
    {
        if (!$this->_persistantFreeze) {
            return '';
        } else {
            $id = $this->getAttribute('id');
            return '<input type="hidden"' .
                   (isset($id)? ' id="' . $id . '"': '') .
                   ' name="' . $this->getName() . '"' .
                   ' value="' . htmlspecialchars($this->getValue()) . '" />';
        }
    }

    function isFrozen()
    {
        return $this->_flagFrozen;
    }

    function setPersistantFreeze($persistant=false)
    {
        $this->_persistantFreeze = $persistant;
    }

    function setLabel($label)
    {
        $this->_label = $label;
    }

    function getLabel()
    {
        return $this->_label;
    }

    function _findValue(&$values)
    {
        if (empty($values)) {
            return null;
        }
        $elementName = $this->getName();
        if (isset($values[$elementName])) {
            return $values[$elementName];
        } elseif (strpos($elementName, '[')) {
            $myVar = "['" . str_replace(array(']', '['), array('', "']['"), $elementName) . "']";
            return eval("return (isset(\$values$myVar)) ? \$values$myVar : null;");
        } else {
            return null;
        }
    }

    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $className = get_class($this);
                $this->$className($arg[0], $arg[1], $arg[2], $arg[3], $arg[4]);
                break;
            case 'addElement':
                $this->onQuickFormEvent('createElement', $arg, $caller);
                $this->onQuickFormEvent('updateValue', null, $caller);
                break;
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    $value = $this->_findValue($caller->_submitValues);
                    if (null === $value) {
                        $value = $this->_findValue($caller->_defaultValues);
                    }
                }
                if (null !== $value) {
                    $this->setValue($value);
                }
                break;
            case 'setGroupValue':
                $this->setValue($arg);
        }
        return true;
    }

    function accept(&$renderer, $required=false, $error=null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    function _generateId()
    {
        static $idx = 1;

        if (!$this->getAttribute('id')) {
            $this->updateAttributes(array('id' => 'qf_' . substr(md5(microtime() . $idx++), 0, 6)));
        }
    }

    function exportValue(&$submitValues, $assoc = false)
    {
        $value = $this->_findValue($submitValues);
        if (null === $value) {
            $value = $this->getValue();
        }
        return $this->_prepareValue($value, $assoc);
    }

    function _prepareValue($value, $assoc)
    {
        if (null === $value) {
            return null;
        } elseif (!$assoc) {
            return $value;
        } else {
            $name = $this->getName();
            if (!strpos($name, '[')) {
                return array($name => $value);
            } else {
                $valueAry = array();
                $myIndex  = "['" . str_replace(array(']', '['), array('', "']['"), $name) . "']";
                eval("\$valueAry$myIndex = \$value;");
                return $valueAry;
            }
        }
    }
    
}
?>