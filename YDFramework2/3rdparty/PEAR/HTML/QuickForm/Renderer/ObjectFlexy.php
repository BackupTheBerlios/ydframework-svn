<?php

require_once("HTML/QuickForm/Renderer/Object.php");

class HTML_QuickForm_Renderer_ObjectFlexy extends HTML_QuickForm_Renderer_Object {

    var $_flexy;
    var $_elementIdx;
    var $_groupElementIdx = 0;
    var $_html = '';
    var $label = '';
    var $_elementType = 'QuickformFlexyElement';

    function HTML_QuickForm_Renderer_ObjectFlexy($flexy)
    {
        $this->HTML_QuickForm_Renderer_Object(true);
        $this->_obj = new QuickformFlexyForm();
        $this->_flexy = $flexy;
    }

    function renderHeader(&$header)
    {
        if($name = $header->getName()) {
            $this->_obj->header->$name = $header->toHtml();
        } else {
            $this->_obj->header[$this->_sectionCount] = $header->toHtml();
        }
        $this->_currentSection = $this->_sectionCount++;
    }

    function startGroup(&$group, $required, $error)
    {
        parent::startGroup($group, $required, $error);
        $this->_groupElementIdx = 1;
    }

     function _elementToObject(&$element, $required, $error)
     {
        $ret = parent::_elementToObject($element, $required, $error);
        if($ret->type == 'group') {
            $ret->html = $element->toHtml();
            unset($ret->elements);
        }
        if(!empty($this->_label)) {
            $this->_renderLabel($ret);
        }

        if(!empty($this->_html)) {
            $this->_renderHtml($ret);
            $ret->error = $error;
        }

        if (false !== ($pos = strpos($ret->name, '[')) || is_object($this->_currentGroup)) {
            if (!$pos) {
                $keys = '->{\'' . $ret->name . '\'}';
            } else {
                $keys = '->{\'' . str_replace(array('[', ']'), array('\'}->{\'', ''), $ret->name) . '\'}';
            }
            if (is_object($this->_currentGroup)) {
                if ($this->_currentGroup->keys == $keys && 'radio' != $ret->type) {
                    return false;
                }
                if (0 === strpos($keys, $this->_currentGroup->keys)) {
                    $keys = substr_replace($keys, '', 0, strlen($this->_currentGroup->keys));
                }
            }
        } elseif (0 == strlen($ret->name)) {
            $keys = '->{\'element_' . $this->_elementIdx . '\'}';
        } else {
            $keys = '->{\'' . $ret->name . '\'}';
        }
        if ('radio' == $ret->type && '[]' != substr($keys, -2)) {
            $keys .= '->{\'' . $ret->value . '\'}';
        }
        $ret->keys = $keys;
        $this->_elementIdx++;
        return $ret;
    }

    function _storeObject($elObj) 
    {
        if ($elObj) {
            $keys = $elObj->keys;
            unset($elObj->keys);
            if(is_object($this->_currentGroup) && ('group' != $elObj->type)) {
                $code = '$this->_currentGroup' . $keys . ' = $elObj;';
            } else {
                $code = '$this->_obj' . $keys . ' = $elObj;';
            }
            eval($code);
        }
    }

    function setHtmlTemplate($template)
    {
        $this->_html = $template;
    } 

    function setLabelTemplate($template) 
    {
        $this->_label = $template;
    }

    function _renderLabel(&$ret)
    {
        $this->_flexy->compile($this->_label);
        $ret->label = $this->_flexy->bufferedOutputObject($ret);
    }

    function _renderHtml(&$ret)
    {
        $this->_flexy->compile($this->_html);
        $ret->html = $this->_flexy->bufferedOutputObject($ret);
    }

}

class QuickformFlexyForm {

    var $frozen;        
    var $javascript;
     var $attributes;
     var $requirednote;
     var $hidden;
     var $errors;
     var $elements;
     var $sections;

     function outputHeader()
     {
        $hdr = "<form " . $this->attributes . ">\n";
        return $hdr;
     }

     function outputJavaScript()
     {
        return $this->javascript;
     }
}

class QuickformFlexyElement {
    
    var $name;
    var $value;
    var $type;
    var $frozen;
    var $label;
    var $required;
    var $error;
    var $style;
    var $html;
    var $separator;
    var $elements;

}
?>